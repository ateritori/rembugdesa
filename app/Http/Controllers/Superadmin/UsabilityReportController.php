<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\UsabilityInstrument;
use App\Models\UsabilityResponse;
use Illuminate\Http\Request;

class UsabilityReportController extends Controller
{
    /**
     * Tampilkan ringkasan hasil usability (SUS).
     */
    public function index(Request $request)
    {
        $instrument = UsabilityInstrument::where('is_active', true)->firstOrFail();

        $query = UsabilityResponse::with(['user', 'decisionSession'])
            ->where('usability_instrument_id', $instrument->id);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('decision_session_id')) {
            $query->where('decision_session_id', $request->decision_session_id);
        }

        $responses = $query
            ->orderByDesc('created_at')
            ->paginate(20);

        $averageScore = $query->avg('total_score');

        return view('usability.reports.index', [
            'instrument' => $instrument,
            'responses' => $responses,
            'averageScore' => $averageScore,
        ]);
    }
}
