<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionSession;
use App\Models\User;
use Illuminate\Http\Request;

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
        abort_if($decisionSession->status === 'closed', 403);

        $dms = User::role('dm')->get();
        $assignedDmIds = $decisionSession->dms()->pluck('users.id')->toArray();

        return view('assign-dms.index', compact(
            'decisionSession',
            'dms',
            'assignedDmIds'
        ));
    }

    /**
     * Simpan Penugasan DM
     */
    public function store(Request $request, DecisionSession $decisionSession)
    {
        abort_if($decisionSession->status === 'closed', 403);

        $validDmIds = User::role('dm')
            ->whereIn('id', $request->input('dm_ids', []))
            ->pluck('id');

        $decisionSession->dms()->sync($validDmIds);

        return redirect()
            ->route('control.index', $decisionSession->id)
            ->with('success', 'Daftar DM diperbarui.');
    }
}
