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
    public function index(Request $request)
    {
        $query = Record::query()->with('recordMedications','timingTag');

        // 週で絞り込み
        if ($request->filled('filter_date')) {
            [$year, $week] = explode('-W', $request->input('filter_date'));
            $startOfWeek = Carbon::now()->setISODate((int)$year, (int)$week)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            $query->whereBetween('taken_at', [$startOfWeek, $endOfWeek]);
        }

        // 完了状況で絞り込み
        if ($request->filled('filter_completion')) {
            if ($request->input('filter_completion') === 'completed') {
                // 未完了の薬が一つもないレコードを絞り込む
                $query->whereDoesntHave('recordMedications', function ($subQuery) {
                    $subQuery->where('is_completed', false);
                });
            } elseif ($request->input('filter_completion') === 'incomplete') {
                // 一つでも未完了の薬があるレコードを絞り込む
                $query->whereHas('recordMedications', function ($subQuery) {
                    $subQuery->where('is_completed', false);
                });
            }
        }

        $records = $query->latest('taken_at')->paginate(10);
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
     */
    public function store(Request $request)
    {
        // フォームからのネストされた配列に対応したバリデーション
        $data = $request->validate([
            'taken_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'timing_tag_id' => ['required', 'integer', 'exists:timing_tags,timing_tag_id'],

            'medications' => ['required', 'array', 'min:1'],
            'medications.*.medication_id' => ['required', 'integer', 'exists:medications,medication_id'],
            'medications.*.taken_dosage' => ['nullable', 'string', 'max:255'],
            'medications.*.is_completed' => ['nullable', 'in:1'],
        ], [], [
            'taken_date' => '日付',
            'timing_tag_id' => 'タイミング',
            'medications' => '内服薬',
            'medications.*.medication_id' => '内服薬の選択',
            'medications.*.taken_dosage' => '服用量',
            'medications.*.is_completed' => '完了フラグ',
        ]);

        $tag      = TimingTag::findOrFail($data['timing_tag_id']);
        $baseTime = $tag->base_time ?: '09:00:00';
        $takenAt  = Carbon::parse($data['taken_date'] . ' ' . $baseTime);
        
        return DB::transaction(function () use ($data, $takenAt) {
            // 親（Record）を作成
            $record = Record::firstOrCreate([
                'user_id'       => Auth::id(),
                'timing_tag_id' => (int)$data['timing_tag_id'],
                'taken_at'      => $takenAt,
            ]);

            // 子（RecordMedication）を upsert（同じ薬は上書き）
            foreach ($data['medications'] as $med) {
                RecordMedication::updateOrCreate(
                    [
                        'record_id'     => $record->record_id,
                        'medication_id' => (int)$med['medication_id'],
                    ],
                    [
                        'taken_dosage'     => $med['taken_dosage'] ?? null,
                        'is_completed'     => isset($med['is_completed']) && $med['is_completed'] === '1',
                        'reason_not_taken' => null,
                    ]
                );
            }

            return redirect()
                ->route('records.show', $record)
                ->with('ok', '記録を作成し、選択した内服薬を登録しました。');
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
        $medications = Medication::orderBy('medication_name')->get(['medication_id','medication_name']);


        return view('records.edit', compact('record','timingTags','medications'));
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
            'medications' => ['array', 'nullable'],
            'medications.*.medication_id' => ['required', 'integer', 'exists:medications,medication_id'],
            'medications.*.taken_dosage'  => ['nullable', 'string', 'max:255'],
            'medications.*.is_completed'  => ['nullable', 'boolean'],
            'medications.*.reason_not_taken' => ['nullable', 'string', 'max:255'],
        ],
        [],
        ['taken_date'=>'日付','timing_tag_id'=>'タイミング']
    );

    // 親モデルの更新
    $tag      = TimingTag::findOrFail($data['timing_tag_id']);
    $baseTime = $tag->base_time ?: '09:00:00';
    $record->update([
        'timing_tag_id' => (int)$data['timing_tag_id'],
        'taken_at'      => Carbon::parse($data['taken_date'].' '.$baseTime),
    ]);

    // 子モデル（内服薬）の更新
    // 1. 既存の関連データをすべて削除
    $record->recordMedications()->delete();

    // 2. バリデーション済みのデータから新しいデータを保存
    if (isset($data['medications'])) {
        foreach ($data['medications'] as $medicationData) {
            $record->recordMedications()->create($medicationData);
        }
    }
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
