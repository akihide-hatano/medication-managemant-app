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
    public function index(Request $request)
    {
        $q = Medication::query();

        if($request->filled('q')){
            $keyword = $request->string('q')->toString();
             $q->where('medication_name', 'like', "%{$keyword}%");
        }

                $medications = $q->orderBy('medication_name')
                        ->paginate(20)
                        ->withQueryString();

        return view('medications.index', compact('medications'));
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

        return redirect()
            ->route('medications.show', $medication)
            ->with('ok', '薬を登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Medication $medication)
    {
        return view('medications.show',compact('medication'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Medication $medication)
    {
        return view('medications.edit',compact('medication'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medication $medication)
    {
        $data = $request->validate([
            'medication_name' => ['required','string','max:255'],
            'dosage'          => ['nullable','string','max:255'],
            'effects'         => ['nullable','string'],
            'side_effects'    => ['nullable','string'],
            'notes'           => ['nullable','string'],
        ]);

        $medication->update($data);

        return redirect()
            ->route('medications.show', $medication)
            ->with('ok', '薬を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medication $medication)
    {
        $medication->delete();
        return redirect()
            ->route('medications.index')
            ->with('ok', '薬を削除しました。');
    }
}
