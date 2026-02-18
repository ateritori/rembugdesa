@extends('layouts.dashboard')

@section('title', 'Penilaian Usability')

@section('content')
    @php
        $isViewMode = isset($existingResponse) && $existingResponse;
    @endphp
    <div class="animate-in fade-in slide-in-from-bottom-4 w-full px-4 py-6 md:px-8 md:py-8 duration-700 dark:bg-slate-900">

        {{-- HEADER SECTION --}}
        <div
            class="mb-8 flex flex-col justify-between gap-6 border-b border-slate-200/60 pb-8 md:flex-row md:items-end dark:border-slate-800">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-8 rounded-full bg-indigo-600 dark:bg-indigo-500"></span>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-600 dark:text-indigo-400">
                        System Evaluation
                    </p>
                </div>
                <h1 class="text-3xl font-black uppercase tracking-tight text-slate-800 md:text-4xl dark:text-white">
                    Penilaian <span class="text-indigo-600 dark:text-indigo-400">Usability</span>
                </h1>
                <p class="max-w-xl text-sm leading-relaxed text-slate-500 dark:text-slate-400 font-medium">
                    Berikan umpan balik sejujurnya untuk membantu kami menciptakan pengalaman sistem yang lebih baik.
                </p>
            </div>

            @if ($decisionSession)
                <div
                    class="flex items-center gap-4 rounded-3xl border border-slate-200 bg-white p-2 pr-6 shadow-sm dark:border-slate-800 dark:bg-slate-800/50">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sesi Aktif</p>
                        <p class="text-sm font-black text-slate-700 dark:text-slate-200">{{ $decisionSession->name }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- SIMPLE INSTRUCTIONS --}}
        <div
            class="mb-8 flex flex-col gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-xs font-bold text-slate-700 md:flex-row md:items-center md:justify-between dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200">
            <div class="flex flex-wrap items-center gap-2">
                Attachment Code Usage

                <span
                    class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-[10px] font-black text-red-600 dark:bg-red-500/10 dark:text-red-400">
                    STS
                    <span class="font-bold opacity-70">Sangat Tidak Setuju</span>
                </span>

                <span
                    class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-[10px] font-black text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
                    TS
                    <span class="font-bold opacity-70">Tidak Setuju</span>
                </span>

                <span
                    class="inline-flex items-center gap-1 rounded-full bg-slate-200 px-3 py-1 text-[10px] font-black text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                    N
                    <span class="font-bold opacity-70">Netral</span>
                </span>

                <span
                    class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-[10px] font-black text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    S
                    <span class="font-bold opacity-70">Setuju</span>
                </span>

                <span
                    class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    SS
                    <span class="font-bold opacity-70">Sangat Setuju</span>
                </span>
            </div>

            <div class="text-[10px] font-black uppercase tracking-wider opacity-70">
                Semua pertanyaan wajib diisi
            </div>
        </div>

        {{-- STATUS BANNER --}}
        @if ($isViewMode)
            <div
                class="mb-8 flex items-center gap-4 rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4 dark:border-emerald-900/30 dark:bg-emerald-950/20">
                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-800 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="text-sm font-bold text-emerald-800 dark:text-emerald-400">
                    Selesai! Penilaian Anda telah tersimpan dan saat ini dalam mode baca.
                </p>
            </div>
        @endif

        {{-- MAIN FORM --}}
        <form method="POST" action="{{ $isViewMode ? '#' : route('usability.responses.store') }}"
            class="max-w-5xl mx-auto space-y-6">
            @csrf
            @if ($isViewMode)
                @method('PUT')
            @endif
            @if ($decisionSession)
                <input type="hidden" name="decision_session_id" value="{{ $decisionSession->id }}">
            @endif

            <div class="grid grid-cols-1 gap-6">
                @foreach ($instrument->questions as $question)
                    <div
                        class="group relative rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300 hover:border-indigo-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-800/80">
                        <div class="mb-6 flex items-start gap-4">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xs font-black text-slate-500 group-hover:bg-indigo-600 group-hover:text-white transition-colors dark:bg-slate-700 dark:text-slate-400">
                                {{ str_pad($question->number, 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <h3 class="text-base font-bold leading-tight text-slate-800 dark:text-slate-100">
                                {{ $question->question }}
                            </h3>
                        </div>

                        @php
                            $options = [
                                1 => ['STS', 'bg-red-50', 'text-red-600', 'border-red-200'],
                                2 => ['TS', 'bg-orange-50', 'text-orange-600', 'border-orange-200'],
                                3 => ['N', 'bg-slate-50', 'text-slate-600', 'border-slate-200'],
                                4 => ['S', 'bg-blue-50', 'text-blue-600', 'border-blue-200'],
                                5 => ['SS', 'bg-emerald-50', 'text-emerald-600', 'border-emerald-200'],
                            ];
                        @endphp

                        <div class="grid grid-cols-5 gap-2 md:gap-4">
                            @foreach ($options as $val => $style)
                                <label class="relative group/option cursor-pointer">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $val }}"
                                        class="peer sr-only" @checked($isViewMode && ($existingAnswers[$question->id] ?? null) == $val) @disabled($isViewMode)>

                                    <div
                                        class="flex flex-col items-center justify-center rounded-2xl border-2 border-slate-50 bg-slate-50/50 py-4 transition-all
                                        peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:ring-4 peer-checked:ring-indigo-100
                                        dark:border-slate-700/50 dark:bg-slate-900/50 dark:peer-checked:border-indigo-500 dark:peer-checked:bg-indigo-500/10 dark:peer-checked:ring-indigo-500/10
                                        @if (!$isViewMode) hover:border-slate-300 dark:hover:border-slate-600 @endif">

                                        <span
                                            class="text-lg font-black peer-checked:text-indigo-600 dark:text-slate-300 dark:peer-checked:text-indigo-400">
                                            {{ $val }}
                                        </span>
                                        <span
                                            class="text-[9px] font-black uppercase tracking-tighter opacity-60 md:text-[10px]">
                                            {{ $style[0] }}
                                        </span>
                                    </div>

                                    {{-- Tooltip-like label on hover (Desktop only) --}}
                                    <div
                                        class="absolute -bottom-8 left-1/2 -translate-x-1/2 scale-0 opacity-0 group-hover/option:scale-100 group-hover/option:opacity-100 transition-all z-10 whitespace-nowrap hidden md:block">
                                        <div class="bg-slate-800 text-white text-[9px] px-2 py-1 rounded-md font-bold">
                                            {{ $val == 1 ? 'Sangat Tidak Setuju' : ($val == 2 ? 'Tidak Setuju' : ($val == 3 ? 'Netral' : ($val == 4 ? 'Setuju' : 'Sangat Setuju'))) }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- FOOTER ACTION --}}
            <div
                class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 mt-12 border-t border-slate-200 pt-8 dark:border-slate-800">
                <a href="{{ url()->previous() }}"
                    class="w-full sm:w-auto text-center px-8 py-4 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-800 transition-colors dark:hover:text-white">
                    Kembali Ke Dashboard
                </a>

                @if (!$isViewMode)
                    <button type="submit"
                        class="w-full sm:w-auto flex items-center justify-center gap-3 rounded-2xl bg-indigo-600 px-10 py-4 text-[11px] font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-indigo-200 transition-all hover:bg-indigo-700 hover:-translate-y-1 active:scale-95 dark:shadow-none">
                        Kirim Jawaban
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                @endif
            </div>
        </form>
    </div>
@endsection
