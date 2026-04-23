@extends('layouts.dashboard')

@section('title', 'Penilaian Usability')

@section('content')
    @php
        $isViewMode = isset($existingResponse) && $existingResponse;
    @endphp

    <style>
        .academic-box {
            border: 2px solid #000000 !important;
            border-radius: 0px !important;
        }

        .academic-btn {
            border: 2px solid #000000 !important;
            border-radius: 0px !important;
        }

        /* Radio Tile Logic */
        .peer:checked+.radio-tile {
            background-color: #000000 !important;
            color: #ffffff !important;
            border-color: #000000 !important;
        }

        /* Hover effect hanya jika bukan view mode */
        @if (!$isViewMode)
            .radio-tile:hover {
                background-color: #f8fafc;
            }
        @endif
    </style>

    <div class="w-full px-4 py-6 md:px-8 text-black bg-white">

        {{-- HEADER --}}
        <div class="mb-6 flex flex-col justify-between gap-4 border-b-4 border-black pb-6 md:flex-row md:items-end">
            <div class="space-y-1">
                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-black/50">Instrument Evaluation</p>
                <h1 class="text-2xl font-black uppercase tracking-tighter text-black md:text-3xl">
                    Penilaian <span class="underline decoration-black decoration-2">Usability</span>
                </h1>
            </div>

            @if ($decisionSession)
                <div class="flex items-center gap-3 border-2 border-black bg-white p-2 pr-4">
                    <div class="flex h-8 w-8 items-center justify-center bg-black text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-[10px] font-black uppercase">{{ $decisionSession->name }}</p>
                </div>
            @endif
        </div>

        {{-- LEGEND --}}
        <div class="mb-6 border-2 border-black bg-slate-50 p-3">
            <div class="flex flex-wrap items-center gap-4 text-[9px] font-black uppercase">
                <span class="text-black/40">Skala:</span>
                <span class="flex items-center gap-1"><span class="h-2 w-2 bg-red-600"></span> 1: STS</span>
                <span class="flex items-center gap-1"><span class="h-2 w-2 bg-orange-600"></span> 2: TS</span>
                <span class="flex items-center gap-1"><span class="h-2 w-2 bg-slate-600"></span> 3: N</span>
                <span class="flex items-center gap-1"><span class="h-2 w-2 bg-blue-600"></span> 4: S</span>
                <span class="flex items-center gap-1"><span class="h-2 w-2 bg-emerald-600"></span> 5: SS</span>
            </div>
        </div>

        {{-- MAIN FORM - 2 COLUMNS ON DESKTOP --}}
        <form method="POST" action="{{ $isViewMode ? '#' : route('usability.responses.store') }}">
            @csrf
            @if ($isViewMode)
                @method('PUT')
            @endif
            @if ($decisionSession)
                <input type="hidden" name="decision_session_id" value="{{ $decisionSession->id }}">
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach ($instrument->questions as $question)
                    <div class="academic-box bg-white p-4 transition-colors">
                        <div class="mb-4 flex items-start gap-3">
                            <span
                                class="flex h-6 w-6 shrink-0 items-center justify-center border-2 border-black bg-black text-[10px] font-black text-white">
                                {{ $question->number }}
                            </span>
                            <h3 class="text-[11px] font-black leading-tight text-black uppercase">
                                {{ $question->question }}
                            </h3>
                        </div>

                        <div class="grid grid-cols-5 gap-1.5">
                            @for ($val = 1; $val <= 5; $val++)
                                @php $labels = [1 => 'STS', 2 => 'TS', 3 => 'N', 4 => 'S', 5 => 'SS']; @endphp
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $val }}"
                                        class="peer sr-only" @checked($isViewMode && ($existingAnswers[$question->id] ?? null) == $val) @disabled($isViewMode)>

                                    <div
                                        class="radio-tile flex flex-col items-center justify-center border border-black bg-white py-2 transition-all">
                                        <span class="text-sm font-black">{{ $val }}</span>
                                        <span class="text-[8px] font-black uppercase">{{ $labels[$val] }}</span>
                                    </div>
                                </label>
                            @endfor
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- FOOTER --}}
            <div class="mt-8 pt-6 border-t-2 border-black flex flex-col md:flex-row items-center justify-between gap-4">
                <a href="{{ url()->previous() }}"
                    class="text-[9px] font-black uppercase tracking-widest text-black/40 hover:text-black underline underline-offset-4">
                    Back to Dashboard
                </a>

                @if (!$isViewMode)
                    <button type="submit"
                        class="academic-btn w-full md:w-auto bg-black px-10 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:shadow-none transition-all">
                        Submit Response
                    </button>
                @endif
            </div>
        </form>
    </div>
@endsection
