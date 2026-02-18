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
        // Instrumen wajib
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

        // Decision session OPSIONAL
        $decisionSession = null;
        if ($request->filled('decision_session_id')) {
            $decisionSession = DecisionSession::find(
                $request->decision_session_id
            );
        }

        // Ambil response lama TANPA tergantung session
        $existingResponse = UsabilityResponse::where('user_id', Auth::id())
            ->where('usability_instrument_id', $instrument->id)
            ->latest()
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
     * Simpan jawaban SUS dan hitung skor.
     */
    public function store(Request $request)
    {
        $instrument = UsabilityInstrument::where('is_active', true)->first();

        if (! $instrument) {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Instrumen SUS belum tersedia.');
        }

        $questions = UsabilityQuestion::where(
            'usability_instrument_id',
            $instrument->id
        )
            ->where('is_active', true)
            ->orderBy('number')
            ->get();

        $request->validate([
            'answers' => 'required|array',
        ]);

        $user = Auth::user();

        // Cegah submit ganda (1x per user per instrumen)
        $alreadyExists = UsabilityResponse::where('user_id', $user->id)
            ->where('usability_instrument_id', $instrument->id)
            ->exists();

        if ($alreadyExists) {
            return redirect()
                ->route('usability.responses.create')
                ->with(
                    'info',
                    'Anda sudah pernah mengisi SUS. Jawaban sebelumnya ditampilkan.'
                );
        }

        DB::transaction(function () use (
            $request,
            $instrument,
            $questions,
            $user
        ) {
            $response = UsabilityResponse::create([
                'usability_instrument_id' => $instrument->id,
                'user_id'                 => $user->id,
                'role'                    => $user->getRoleNames()->first(),
                'decision_session_id'     => $request->decision_session_id,
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
            ->with(
                'success',
                'Terima kasih. Penilaian usability berhasil dikirim.'
            );
    }
}
