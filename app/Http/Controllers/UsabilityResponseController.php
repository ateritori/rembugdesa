<?php

namespace App\Http\Controllers;

use App\Models\UsabilityInstrument;
use App\Models\UsabilityQuestion;
use App\Models\UsabilityResponse;
use App\Models\UsabilityAnswer;
use App\Models\DecisionSession;
use App\Models\AlternativeEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsabilityResponseController extends Controller
{
    /**
     * Tampilkan form SUS.
     * SUS hanya bisa diakses oleh DM
     * setelah menyelesaikan evaluasi alternatif
     * dan berlaku per decision session.
     */
    public function create(Request $request)
    {
        // decision_session_id wajib
        if (! $request->filled('decision_session_id')) {
            return redirect()
                ->route('dashboard')
                ->with('info', 'SUS hanya dapat diisi dalam konteks sesi keputusan.');
        }

        $decisionSession = DecisionSession::with(['alternatives', 'criteria'])
            ->find($request->decision_session_id);

        if (! $decisionSession) {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Sesi keputusan tidak ditemukan.');
        }

        $user = Auth::user();

        // Ambil instrumen SUS aktif
        $instrument = UsabilityInstrument::where('is_active', true)
            ->with(['questions' => function ($q) {
                $q->where('is_active', true)
                    ->orderBy('number');
            }])
            ->first();

        if (! $instrument) {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Instrumen SUS belum tersedia.');
        }

        /**
         * ===== CEK FASE SCORING SELESAI =====
         * DM dianggap selesai jika:
         * jumlah evaluasi alternatif >= (jumlah alternatif × jumlah kriteria)
         */
        $totalExpectation =
            $decisionSession->alternatives->count()
            * $decisionSession->criteria->count();

        $dmCompletedScoring = $decisionSession->dms()
            ->where('users.id', $user->id)
            ->whereHas('alternativeEvaluations', function ($query) use ($decisionSession) {
                $query->where('decision_session_id', $decisionSession->id);
            }, '>=', $totalExpectation)
            ->exists();

        if (! $dmCompletedScoring) {
            return redirect()
                ->route('decision-sessions.summary', [
                    'decisionSession' => $decisionSession->id,
                ])
                ->with(
                    'info',
                    'SUS dapat diisi setelah Anda menyelesaikan evaluasi alternatif.'
                );
        }

        // Cari response SUS existing (per DM per session)
        $existingResponse = UsabilityResponse::where('user_id', $user->id)
            ->where('usability_instrument_id', $instrument->id)
            ->where('decision_session_id', $decisionSession->id)
            ->first();

        $existingAnswers = [];

        if ($existingResponse) {
            $existingAnswers = UsabilityAnswer::where(
                'usability_response_id',
                $existingResponse->id
            )
                ->pluck('value', 'usability_question_id')
                ->toArray();
        }

        return view('usability.responses.index', [
            'instrument'       => $instrument,
            'decisionSession'  => $decisionSession,
            'existingResponse' => $existingResponse,
            'existingAnswers'  => $existingAnswers,
        ]);
    }

    /**
     * Simpan jawaban SUS (per DM per decision session).
     */
    public function store(Request $request)
    {
        $request->validate([
            'decision_session_id' => 'required|integer|exists:decision_sessions,id',
            'answers'             => 'required|array',
        ]);

        $user = Auth::user();

        $decisionSession = DecisionSession::findOrFail(
            $request->decision_session_id
        );

        $instrument = UsabilityInstrument::where('is_active', true)->firstOrFail();

        $questions = UsabilityQuestion::where(
            'usability_instrument_id',
            $instrument->id
        )
            ->where('is_active', true)
            ->orderBy('number')
            ->get();

        DB::transaction(function () use (
            $request,
            $instrument,
            $questions,
            $user,
            $decisionSession
        ) {
            $response = UsabilityResponse::create([
                'usability_instrument_id' => $instrument->id,
                'user_id'                 => $user->id,
                'role'                    => $user->getRoleNames()->first(),
                'decision_session_id'     => $decisionSession->id,
            ]);

            $total = 0;

            foreach ($questions as $question) {
                $value = (int) $request->answers[$question->id];

                UsabilityAnswer::create([
                    'usability_response_id' => $response->id,
                    'usability_question_id' => $question->id,
                    'value'                 => $value,
                ]);

                if ($question->polarity === 'positive') {
                    $total += ($value - 1);
                } else {
                    $total += (5 - $value);
                }
            }

            $response->update([
                'total_score' => $total * 2.5,
            ]);
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Terima kasih. Penilaian usability berhasil dikirim.');
    }
}
