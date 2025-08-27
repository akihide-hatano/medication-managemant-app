<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Models\Record;
use App\Models\TimingTag;
use App\Models\RecordMedication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = Record::with(['recordMedications.medication','timingTag'])
            ->where('user_id', Auth::id())   // ← ここがポイント
            ->orderByDesc('taken_at')
            ->paginate(20);
        return view('records.index', compact('records'));
    }

    public function create()
    {
        $timingTags = TimingTag::orderBy('timing_name')
                        ->get(['timing_tag_id','timing_name','base_time']);

        $medications = Medication::orderBy('medication_name')
                        ->get(['medication_id','medication_name']);

        return view('records.create',compact('timingTags','medications'));
    }

/** 保存：親＋子を一括作成 */
    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'taken_date'     => ['required','date_format:Y-m-d','before_or_equal:today'],
                'timing_tag_id'  => ['required','integer','exists:timing_tags,timing_tag_id'],

                // 子（薬）配列：最低1件は選択させたい場合は required|array に
                'medications'            => ['nullable','array'],
                'medications.*.id'       => ['required','integer','exists:medications,medication_id'],
                'medications.*.dosage'   => ['nullable','string','max:255'],
                'medications.*.done'     => ['nullable','boolean'], // チェック無しなら来ない
            ],
            [],
            ['taken_date'=>'日付','timing_tag_id'=>'タイミング','medications'=>'内服薬']
        );

        $tag = TimingTag::findOrFail($data['timing_tag_id']);
        $base = $tag->base_time ?: '09:00:00';
        $takenAt = Carbon::parse($data['taken_date'].' '.$base);

        return DB::transaction(function () use ($data, $takenAt) {

            // 同一ユーザ×同日×同タイミング の重複を避ける
            $record = Record::firstOrCreate(
                [
                    'user_id'       => Auth::id(),
                    'timing_tag_id' => (int)$data['timing_tag_id'],
                    'taken_at'      => $takenAt, // 日付＋基準時刻
                ],
                []
            );

            // 子明細（薬）を一括作成（重複を除外）
            $items = collect($data['medications'] ?? [])
                ->unique(fn ($m) => (int)$m['id']) // 同じ薬の重複選択を除外
                ->map(function ($m) use ($record) {
                    return [
                        'record_id'        => $record->record_id,
                        'medication_id'    => (int)$m['id'],
                        'taken_dosage'     => $m['dosage'] ?? null,
                        'is_completed'     => (bool)($m['done'] ?? false),
                        'reason_not_taken' => null,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                })
                ->values()
                ->all();

            if (!empty($items)) {
                // 既に同じ薬が入っていないかチェックしてから差分だけ入れる
                $existingIds = RecordMedication::where('record_id', $record->record_id)
                    ->pluck('medication_id')->all();

                $toInsert = array_values(array_filter($items, fn($row) =>
                    !in_array($row['medication_id'], $existingIds, true)
                ));

                if ($toInsert) {
                    RecordMedication::insert($toInsert);
                }
            }

            return redirect()
                ->route('records.show', $record)
                ->with('ok','記録を作成し、選択した内服薬を登録しました。');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Record $record)
    {
        //認証チェック
        if( $record->user_id !== Auth::id()){
            abort(403,'この記録にはアクセスできません');
        }

        //必要な関連をload
        $record->load(['timingTag',
                    'recordMedications.medication',
                ]);

        return view('records.show',compact('record'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Record $record)
    {
        //認証チェック
        if( $record->user_id !== Auth::id()){
            abort(403,'この記録にはアクセスできません');
        }
        return view('records.edit',compact('record'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Record $record)
    {
        //認証チェック
        if( $record->user_id !== Auth::id()){
            abort(403,'この記録にはアクセスできません');
        }

        $data = $request->validate(
            [
                'record_date' => ['required','date_format:Y-m-d','before_or_equal:today'],
                'timing_id'   => ['required','integer','exists:timing_tags,timing_id'],
            ],
            [],
            ['record_date'=>'日付','timing_id'=>'タイミング']
        );

        // 自分の他レコードで同一(日付×タイミング)が無いか
        $exist = Record::where('user_id',Auth::id())
            ->where('record_date',$data['record_date'])
            ->where('timing_id',$data['timing_id'])
            ->where('record_id','!=',$record->record_id)
            ->exists();

        if($exist){
            return back()
                ->withInput()
                ->withErrors([
                    'record_date'=>'同じ日付、タイミングの記録がすでにあります。'
                ]);
        }

        $record->update([
            'record_date' => $data['record_date'],
            'timing_id'   => $data['timing_id'],
        ]);

        return redirect()->route('records.show',$record)
                ->with('ok','内服記録を更新しました');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Record $record)
    {
        //認証チェック
        if( $record->user_id !== Auth::id()){
            abort(403,'この記録にはアクセスできません');
        }

        $record->delete();

        return redirect()
                ->route('records.index')
                ->with('ok','内服記録を削除しました');

    }
}
