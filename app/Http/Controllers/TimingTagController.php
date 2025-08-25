<?php

namespace App\Http\Controllers;

use App\Models\TimingTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TimingTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $timing_tags = TimingTag::orderBy('timing_tag_id')->get();
        return view('timing-tags.index',compact('timing_tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('timing-tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'timing_name' => ['required','string','max:50','unique:timing_tags,timing_name'],
            'base_time' => ['required','bate_format:H:i']
        ],[
        'timing_name.unique'    => '同名のタグは既に登録されています。',
        'base_time.date_format' => '時間は HH:MM 形式で入力してください（例: 09:00）。',
        ]);

        try{
            $tag = TimingTag::create($data);
            return redirect()
                    ->route('timing-tags.show',$tag)
                    ->with('ok','時間を追加しました');
        }
        catch(\Throwable $e){
            Log::error($e);
            return back()
            ->withInput()
            ->with('error','登録できませんでした。');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TimingTag $timing_tag)
    {
        return view('timing-tags.show',compact('timing_tag'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
