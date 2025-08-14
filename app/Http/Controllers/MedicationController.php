<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;

use function Ramsey\Uuid\v1;

class MedicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('medications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'medication_name' => ['required','string','max:255'],
            'dosage'          => ['required','string','max:255'],
            'effects'         => ['required','string'],
            'side_effects'    => ['required','string'],
            'notes'           => ['required','string'],
        ]);

        $medication = Medication::create($data);
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
