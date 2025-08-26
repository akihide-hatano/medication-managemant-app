<?php

namespace App\Http\Controllers;

use App\Models\Record;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $records = Record::with(['recordMedications.medication','timingTag'])
        ->where('user_id', Auth::id())   // ← ここがポイント
        ->orderByDesc('record_date')
        ->paginate(20);

    return view('records.index', compact('records'));
}

public function create()
{
    return view('records.create');
}

public function store(Request $request)
{
    $data = $request->validate(
        [
            'timing_id'   => ['required','integer','exists:timing_tags,timing_id'],
            'record_date' => ['required','date_format:Y-m-d','before_or_equal:today'],
        ],
        [
            'timing_id.required'          => 'タイミングは必須です。',
            'timing_id.integer'           => 'タイミングは数値で指定してください。',
            'timing_id.exists'            => '指定のタイミングが見つかりません。',
            'record_date.date_format'     => '日付は YYYY-MM-DD の形式で入力してください。',
            'record_date.before_or_equal' => '未来の日付は指定できません。',
        ],
        [
            'timing_id'   => 'タイミング',
            'record_date' => '日付',
        ]
    );

    $date = $data['record_date'] ?? now()->toDateString();

    Record::firstOrCreate([
        'user_id'     => Auth::id(),
        'record_date' => $date,
        'timing_id'   => (int)$data['timing_id'],
    ]);

    return back()->with('ok','内服を記録しました。');
}


    /**
     * Display the specified resource.
     */
    public function show(Request $request,Record $record)
    {
        //認証チェック
        if( $record->user_id !== Auth::id()){
            abort(403,'この記録にはアクセスできません');
        }

        //必要な関連をload
        $record->load(['timingTag']);

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
            ->where('record_data',$data['record_date'])
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
