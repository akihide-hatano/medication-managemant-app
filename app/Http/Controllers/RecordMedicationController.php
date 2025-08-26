<?php

namespace App\Http\Controllers;

use App\Models\Record;
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
    public function show(string $id)
    {
        //
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
