<?php

namespace App\Http\Controllers;

use App\Models\UsabilityInstrument;
use App\Models\UsabilityQuestion;
use App\Models\UsabilityResponse;
use App\Models\UsabilityAnswer;
use App\Models\DecisionSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsabilityResponseController extends Controller
{
    /**
     * Tampilkan form pengisian SUS.
     */
    public function create(Request $request)
    {
        $instrument = UsabilityInstrument::where('is_active', true)
            ->with(['questions' => function ($q) {
                $q->where('is_active', true)->orderBy('number');
            }])
            ->firstOrFail();

        $decisionSession = null;

        if ($request->filled('decision_session_id')) {
            $decisionSession = DecisionSession::findOrFail($request->decision_session_id);
        }

        $existingResponse = null;
        $existingAnswers = [];

        if ($decisionSession) {
            $existingResponse = UsabilityResponse::where('user_id', Auth::id())
                ->where('usability_instrument_id', $instrument->id)
                ->where('decision_session_id', $decisionSession->id)
                ->first();

            if ($existingResponse) {
                $existingAnswers = UsabilityAnswer::where('usability_response_id', $existingResponse->id)
                    ->pluck('value', 'usability_question_id')
                    ->toArray();
            }
        }

        return view('usability.responses.index', [
            'instrument' => $instrument,
            'decisionSession' => $decisionSession,
            'existingResponse' => $existingResponse,
            'existingAnswers' => $existingAnswers,
        ]);
    }

    /**
     * Simpan jawaban SUS dan hitung skor.
     */
    public function store(Request $request)
    {
        $instrument = UsabilityInstrument::where('is_active', true)->firstOrFail();

        $questions = UsabilityQuestion::where('usability_instrument_id', $instrument->id)
            ->where('is_active', true)
            ->orderBy('number')
            ->get();

        $request->validate([
            'answers' => 'required|array',
        ]);

        DB::transaction(function () use ($request, $instrument, $questions) {
            $user = Auth::user();

            $alreadyExists = UsabilityResponse::where('user_id', $user->id)
                ->where('usability_instrument_id', $instrument->id)
                ->where('decision_session_id', $request->decision_session_id)
                ->exists();

            if ($alreadyExists) {
                abort(403, 'Anda sudah mengisi penilaian usability.');
            }

            $response = UsabilityResponse::create([
                'usability_instrument_id' => $instrument->id,
                'user_id' => $user->id,
                'role' => $user->getRoleNames()->first(),
                'decision_session_id' => $request->decision_session_id,
            ]);

            $total = 0;

            foreach ($questions as $question) {
                $value = (int) ($request->answers[$question->id] ?? 0);

                UsabilityAnswer::create([
                    'usability_response_id' => $response->id,
                    'usability_question_id' => $question->id,
                    'value' => $value,
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
            ->with('success', 'Terima kasih, penilaian usability berhasil dikirim.');
    }
}
