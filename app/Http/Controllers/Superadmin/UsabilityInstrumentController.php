<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\UsabilityInstrument;
use App\Models\UsabilityQuestion;
use Illuminate\Http\Request;

class UsabilityInstrumentController extends Controller
{
    /**
     * Tampilkan instrumen SUS beserta pertanyaannya.
     */
    public function index()
    {
        $instrument = UsabilityInstrument::with(['questions'])
            ->where('is_active', true)
            ->first();

        return view('usability.instruments.index', compact('instrument'));
    }

    /**
     * Form edit instrumen SUS.
     */
    public function edit()
    {
        $instrument = UsabilityInstrument::with('questions')->firstOrFail();

        return view('usability.instruments.edit', compact('instrument'));
    }

    /**
     * Update instrumen SUS (nama, deskripsi, status).
     */
    public function update(Request $request)
    {
        $instrument = UsabilityInstrument::firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $instrument->update($validated);

        return redirect()
            ->route('superadmin.usability.instruments.index')
            ->with('success', 'Instrumen usability berhasil diperbarui.');
    }

    /**
     * Update pertanyaan SUS (teks dan status aktif).
     */
    public function updateQuestion(Request $request, UsabilityQuestion $question)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $question->update($validated);

        return back()->with('success', 'Pertanyaan usability berhasil diperbarui.');
    }
}
