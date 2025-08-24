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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
