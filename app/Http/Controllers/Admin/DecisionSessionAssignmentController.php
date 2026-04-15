<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DecisionSessionAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Form Penugasan DM ke Sesi Keputusan
     */
    public function index(DecisionSession $decisionSession)
    {

        $decisionSession->load('criteria');

        $dms = User::role('dm')->get();
        $assignments = DB::table('decision_session_assignments')
            ->where('decision_session_id', $decisionSession->id)
            ->get();

        // DM yang ikut pairwise
        $assignedDmIds = $assignments
            ->where('can_pairwise', true)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        // Mapping parameter → DM
        $assignedParam = $assignments
            ->where('can_evaluate', true)
            ->groupBy('criteria_id')
            ->map(fn($rows) => collect($rows)->pluck('user_id')->unique()->values()->toArray())
            ->toArray();

        $criteria = $decisionSession->criteria;

        return view('assign-dms.index', compact(
            'decisionSession',
            'dms',
            'assignedDmIds',
            'assignedParam',
            'criteria'
        ));
    }

    /**
     * Simpan Penugasan DM
     */
    public function store(Request $request, DecisionSession $decisionSession)
    {
        // Ambil DM valid
        $validDmIds = User::role('dm')
            ->whereIn('id', $request->input('dm_ids', []))
            ->pluck('id')
            ->toArray();

        // Reset assignment lama
        DB::table('decision_session_assignments')
            ->where('decision_session_id', $decisionSession->id)
            ->delete();

        // 1. ARAH PEMBANGUNAN (pairwise)
        foreach ($request->input('pairwise', []) as $dmId) {
            if (in_array($dmId, $validDmIds)) {
                DB::table('decision_session_assignments')->insert([
                    'decision_session_id' => $decisionSession->id,
                    'user_id' => $dmId,
                    'can_pairwise' => true,
                    'can_evaluate' => false,
                    'criteria_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. EVALUASI PARAMETER
        foreach ($request->input('param_assign', []) as $criteriaId => $dmIds) {
            foreach ($dmIds as $dmId) {
                if (in_array($dmId, $validDmIds)) {
                    DB::table('decision_session_assignments')->insert([
                        'decision_session_id' => $decisionSession->id,
                        'user_id' => $dmId,
                        'can_pairwise' => false,
                        'can_evaluate' => true,
                        'criteria_id' => $criteriaId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return redirect()
            ->route('control.index', $decisionSession->id)
            ->with('success', 'Penugasan berhasil disimpan.');
    }
}
