<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Models\Record;
use App\Models\RecordMedication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Ramsey\Uuid\v1;

class RecordMedicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /** 親Record配下の明細一覧（/records/{record}/record-medications） */
    public function index(Record $record)
    {
        //認証チェック
        if( $record->user_id !== Auth::id()){
            abort(403,'この記録にはアクセスできません');
        }

        $items = $record->recordMedications()
                ->with('medication')
                ->orderBy('record_medication_id')
                ->paginate(20);

        return view('record_medications.index',compact('record','items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request,Record $record)
    {
        //認証チェック
        if( $record->user_id !== Auth::id()){
            abort(403,'この記録にはアクセスできません');
        }

        $medications = Medication::orderBy('medication_name')
                    ->get(['medication_id','medication_name']);

        return view('record_medications.create',compact('record','medications'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,Record $record)
    {
        //認証チェック
        if( $record->user_id !== Auth::id()){
            abort(403,'この記録にはアクセスできません');
        }

        $data = $request->validate([
            'medication_id' => ['required','integer','exists:medications,medication_id'],
            'taken_dosage'  => ['nullable','string','max:255'],
            'is_completed'  => ['required','boolean'],
            'reason_not_taken' => ['nullable','string'],
        ]);

        // 薬差し替え時の重複チェック
        $dup = RecordMedication::where('record_id', $record->record_id)
            ->where('medication_id',(int)$data['medication_id'])
            ->exists();

        if ($dup) {
            return back()->withInput()->withErrors([
                'medication_id' => 'この記録には同じ薬が既に登録されています。',
            ]);
        }

        RecordMedication::create([
            'record_id' => $record ->record_id,
            'medication_id' => (int)$data['medication_id'],
            'taken_dosage'  =>$data['taken_dosage'] ?? null,
            'is_completed'  => (bool)($data['is_completed'] ?? false),
            'reason_not_taken' => $data['reason_not_taken'] ?? null,
        ]);

        return redirect()->route('record_medication,show',$record)
                ->with('ok','内服詳細を追加しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(RecordMedication $recordMedication)
    {
        //認証チェック
        if( $recordMedication->record->user_id !== Auth::id()){
            abort(403,'この記録にはアクセスできません');
        }

        $recordMedication->load(['medication','record.timingTag']);
        return view('record_medications.show', compact('recordMedication'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RecordMedication $recordMedication)
    {
        //認証チェック
        if( $recordMedication->record->user_id !== Auth::id()){
            abort(403,'この記録にはアクセスできません');
        }

        //画像に必要な関連をロード
        $recordMedication->load(['record.timingTag','medication']);

        //セレクト用
        $medications = Medication::orderBy('medication_name')
                    ->get(['medication_id','medication_name']);

        return view('record_medications.edit', compact('recordMedication','medications'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RecordMedication $recordMedication)
    {
        //認証チェック
        if( $recordMedication->record->user_id !== Auth::id()){
            abort(403,'この記録にはアクセスできません');
        }

        //入力バリデーション
        $data = $request->validate([
            'medication_id' => ['required','integer','exists:medications,medication_id'],
            'taken_dosage'     => ['nullable','string','max:255'],
            'is_completed'     => ['required','boolean'],
            'reason_not_taken' => ['nullable','string'],
        ]);

        //同一record内での重複を禁止
        $dup = RecordMedication::where('record_id',$recordMedication->record_id)
                ->where('medication_id',(int)$data['medication_id'])
                ->where('record_medication_id','!=',$recordMedication->record_medication_id)
                ->exists();

        if($dup){
            return back()->withInput()->withErrors([
                'medication_id' => 'この記録には同じ'
            ]);
        }

        //更新
        $recordMedication->update([
        'medication_id'     => (int)$data['medication_id'],
        'taken_dosage'      => $data['taken_dosage'] ?? null,
        'is_completed'      => (bool)$data['is_completed'],
        'reason_not_taken'  => $data['reason_not_taken'] ?? null,
        ]);


        return redirect()
        ->route('records.show', $recordMedication->record)
        ->with('ok', '内服明細を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RecordMedication $recordMedication)
    {
        //認証チェック
        if( $recordMedication->record->user_id !== Auth::id()){
            abort(403,'この記録にはアクセスできません');
        }

        $record = $recordMedication->record;
        $recordMedication->delete();

        return redirect()
                ->route('record_medications',$record)
                ->with('ok','内服記録を削除しました');

    }
}
