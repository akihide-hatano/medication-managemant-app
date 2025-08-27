<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\RecordMedication;
use App\Models\Medication;
use App\Models\TimingTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecordController extends Controller
{
    /**
     * 一覧
     */
    public function index()
    {
        $records = Record::with(['recordMedications.medication','timingTag'])
            ->where('user_id', Auth::id())
            ->orderByDesc('taken_at')
            ->paginate(20);

        return view('records.index', compact('records'));
    }

    /**
     * 作成フォーム
     * （タイミングと薬の候補を渡す）
     */
    public function create()
    {
        $timingTags  = TimingTag::orderBy('timing_name')->get(['timing_tag_id','timing_name','base_time']);
        $medications = Medication::orderBy('medication_name')->get(['medication_id','medication_name']);

        return view('records.create', compact('timingTags','medications'));
    }

    /**
     * 親（Record）＋ 子（RecordMedication[]）を一括保存
     * フロントはネスト配列：
     * medications[0][medication_id], [taken_dosage], [is_completed], [reason_not_taken]
     */
public function store(Request $request)
{
    // まずは何が飛んできてるかを見たい時はこれ
    // dd($request->all());

    // フォーム実態に合わせた検証（medications は数値IDの配列）
    $data = $request->validate(
        [
            'taken_date'    => ['required','date_format:Y-m-d','before_or_equal:today'],
            'timing_tag_id' => ['required','integer','exists:timing_tags,timing_tag_id'],

            'medications'   => ['required','array','min:1'],
            'medications.*' => ['integer','exists:medications,medication_id'],

            'dosages'       => ['nullable','array'],
            'dosages.*'     => ['nullable','string','max:255'],

            'done'          => ['nullable','array'],
            'done.*'        => ['nullable','in:1'], // チェックされてたら "1"
        ],
        [],
        ['taken_date'=>'日付','timing_tag_id'=>'タイミング','medications'=>'内服薬']
    );

    $tag      = TimingTag::findOrFail($data['timing_tag_id']);
    $baseTime = $tag->base_time ?: '09:00:00';
    $takenAt  = Carbon::parse($data['taken_date'].' '.$baseTime);

    return DB::transaction(function () use ($data, $takenAt) {
        // 親を作成 or 取得
        $record = Record::firstOrCreate([
            'user_id'       => Auth::id(),
            'timing_tag_id' => (int)$data['timing_tag_id'],
            'taken_at'      => $takenAt,
        ]);

        // 子を upsert（同じ薬は上書き）
        $dosages = $data['dosages'] ?? [];
        $done    = $data['done'] ?? [];

        foreach ($data['medications'] as $mid) {
            $mid = (int)$mid;

            RecordMedication::updateOrCreate(
                [
                    'record_id'     => $record->record_id,
                    'medication_id' => $mid,
                ],
                [
                    'taken_dosage'     => $dosages[$mid] ?? null,
                    'is_completed'     => isset($done[$mid]) && $done[$mid] === '1',
                    'reason_not_taken' => null,
                ]
            );
        }

        return redirect()
            ->route('records.show', $record)
            ->with('ok','記録を作成し、選択した内服薬を登録しました。');
    });
}

    /**
     * 詳細
     */
    public function show(Record $record)
    {
        // 認可
        abort_unless($record->user_id === Auth::id(), 403, 'この記録にはアクセスできません');

        $record->load(['timingTag','recordMedications.medication']);

        // 集計（全部○か？）
        $total     = $record->recordMedications->count();
        $completed = $record->recordMedications->where('is_completed', true)->count();
        $allDone   = $total > 0 && $total === $completed;

        return view('records.show', compact('record','total','completed','allDone'));
    }

    /**
     * 編集フォーム（親だけ変更したい場合）
     */
    public function edit(Record $record)
    {
        abort_unless($record->user_id === Auth::id(), 403);

        $record->load(['timingTag','recordMedications.medication']);

        $timingTags = TimingTag::orderBy('timing_name')->get(['timing_tag_id','timing_name','base_time']);

        return view('records.edit', compact('record','timingTags'));
    }

    /**
     * 親の更新（必要なら）
     */
    public function update(Request $request, Record $record)
    {
        abort_unless($record->user_id === Auth::id(), 403);

        $data = $request->validate(
            [
                'taken_date'    => ['required','date_format:Y-m-d','before_or_equal:today'],
                'timing_tag_id' => ['required','integer','exists:timing_tags,timing_tag_id'],
            ],
            [],
            ['taken_date'=>'日付','timing_tag_id'=>'タイミング']
        );

        $tag      = TimingTag::findOrFail($data['timing_tag_id']);
        $baseTime = $tag->base_time ?: '09:00:00';
        $record->update([
            'timing_tag_id' => (int)$data['timing_tag_id'],
            'taken_at'      => Carbon::parse($data['taken_date'].' '.$baseTime),
        ]);

        return redirect()->route('records.show', $record)->with('ok','記録を更新しました。');
    }

    /**
     * 削除
     */
    public function destroy(Record $record)
    {
        abort_unless($record->user_id === Auth::id(), 403);

        $record->delete();

        return redirect()->route('records.index')->with('ok','記録を削除しました。');
    }
}
