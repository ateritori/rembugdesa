<div class="animate-in fade-in zoom-in-95 space-y-8 duration-500">

    {{-- 1. HERO HEADER --}}
    <div
        class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-1 shadow-2xl shadow-slate-200/50 md:p-2">
        <div class="relative overflow-hidden rounded-[1.7rem] bg-slate-900 px-8 py-10 text-white">
            <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-10 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"></div>

            <div class="relative z-10 flex flex-col justify-between gap-8 md:flex-row md:items-center">
                <div class="max-w-2xl">
                    <div class="mb-4 flex items-center gap-3">
                        <span class="flex h-2 w-2 animate-pulse rounded-full bg-emerald-400"></span>
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-400">Decision
                            Workspace</span>
                    </div>
                    <h2 class="mb-1 text-3xl font-black tracking-tight md:text-4xl">
                        {{ $decisionSession->name }}
                    </h2>
                    <p class="text-sm font-bold uppercase tracking-wider text-blue-400">
                        Periode {{ $decisionSession->year }}
                    </p>
                    <p class="font-medium leading-relaxed text-slate-400">
                        Selamat datang kembali. Sistem telah siap untuk memproses penilaian Anda secara objektif.
                    </p>
                </div>

                <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/20 text-blue-400">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-500">Status Sesi</p>
                        <p class="text-sm font-bold uppercase text-white">{{ $decisionSession->status }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. INTERACTIVE STEP CARDS --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $steps = [
                [
                    'label' => 'TAHAP 1',
                    'title' => 'Bobot Kriteria',
                    // Menggunakan tag SVG utuh untuk menghindari error render
                    'svg' =>
                        '<svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zM17 19v-2a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2zM9 9V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>',
                    'styles' => [
                        'bg_light' => 'bg-blue-50',
                        'text_color' => 'text-blue-500',
                        'bg_hover' => 'group-hover:bg-blue-500',
                        'border_hover' => 'group-hover:border-blue-200',
                        'glow' => 'bg-blue-500/5',
                        'text_hover' => 'group-hover:text-blue-500',
                    ],
                    'done' => $dmHasCompleted,
                    'link' => route('dms.index', [
                        'decisionSession' => $decisionSession->id,
                        'tab' => 'penilaian-kriteria',
                    ]),
                ],
                [
                    'label' => 'TAHAP 2',
                    'title' => 'Bobot Kelompok',
                    'svg' =>
                        '<svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>',
                    'styles' => [
                        'bg_light' => 'bg-indigo-50',
                        'text_color' => 'text-indigo-500',
                        'bg_hover' => 'group-hover:bg-indigo-500',
                        'border_hover' => 'group-hover:border-indigo-200',
                        'glow' => 'bg-indigo-500/5',
                        'text_hover' => 'group-hover:text-indigo-500',
                    ],
                    'done' => in_array($decisionSession->status, ['scoring', 'closed']),
                    'link' => '#',
                ],
                [
                    'label' => 'TAHAP 3',
                    'title' => 'Evaluasi Alternatif',
                    'svg' =>
                        '<svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>',
                    'styles' => [
                        'bg_light' => 'bg-amber-50',
                        'text_color' => 'text-amber-500',
                        'bg_hover' => 'group-hover:bg-amber-500',
                        'border_hover' => 'group-hover:border-amber-200',
                        'glow' => 'bg-amber-500/5',
                        'text_hover' => 'group-hover:text-amber-500',
                    ],
                    'done' => $decisionSession->dmEvaluationFinished ?? false,
                    'link' => route('dms.index', [
                        'decisionSession' => $decisionSession->id,
                        'tab' => 'evaluasi-alternatif',
                    ]),
                ],
                [
                    'label' => 'TAHAP 4',
                    'title' => 'Ranking Akhir',
                    'svg' =>
                        '<svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                    'styles' => [
                        'bg_light' => 'bg-emerald-50',
                        'text_color' => 'text-emerald-500',
                        'bg_hover' => 'group-hover:bg-emerald-500',
                        'border_hover' => 'group-hover:border-emerald-200',
                        'glow' => 'bg-emerald-500/5',
                        'text_hover' => 'group-hover:text-emerald-500',
                    ],
                    'done' => $decisionSession->status === 'closed',
                    'link' => route('dms.index', ['decisionSession' => $decisionSession->id, 'tab' => 'hasil-akhir']),
                ],
            ];
        @endphp

        @foreach ($steps as $index => $step)
            @php
                $isPreviousDone = $index === 0 || $steps[$index - 1]['done'];
                $isDisabled = !$isPreviousDone;
                if ($step['label'] === 'TAHAP 4' && $decisionSession->status !== 'closed') {
                    $isDisabled = true;
                }
            @endphp

            <a href="{{ $isDisabled ? 'javascript:void(0)' : $step['link'] }}"
                class="{{ $isDisabled ? 'cursor-not-allowed' : '' }} group relative block transition-all duration-300">
                <div
                    class="{{ !$isDisabled ? 'group-hover:-translate-y-2 group-hover:shadow-xl ' . $step['styles']['border_hover'] : 'opacity-60' }} relative h-full overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm transition-all duration-300">

                    {{-- Step Circle Icon - Menggunakan {!! !!} agar HTML SVG dirender --}}
                    <div
                        class="{{ $isDisabled ? 'bg-slate-100 text-slate-400' : $step['styles']['bg_light'] . ' ' . $step['styles']['text_color'] . ' ' . $step['styles']['bg_hover'] . ' group-hover:text-white' }} mb-5 flex h-14 w-14 items-center justify-center rounded-2xl transition-colors">
                        {!! $step['svg'] !!}
                    </div>

                    <div>
                        <p class="mb-1 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            {{ $step['label'] }}</p>
                        <h4 class="{{ $isDisabled ? 'text-slate-400' : 'text-slate-800' }} text-xl font-black">
                            {{ $step['title'] }}
                        </h4>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        @if ($step['done'])
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Selesai
                            </span>
                        @elseif($isDisabled)
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Menunggu
                            </span>
                        @else
                            <span
                                class="{{ $step['styles']['text_hover'] }} inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 transition-colors">
                                Mulai Tugas
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </span>
                        @endif
                    </div>

                    @if (!$isDisabled)
                        <div
                            class="{{ $step['styles']['glow'] }} absolute -right-4 -top-4 h-24 w-24 rounded-full opacity-0 transition-opacity group-hover:opacity-100">
                        </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>

    {{-- 3. FOOTER INFO --}}
    <div class="rounded-[2rem] border border-dashed border-slate-300 p-8 text-center">
        <p class="text-sm font-medium text-slate-500">
            Butuh bantuan? <a href="#" class="font-bold text-blue-600 hover:underline">Panduan</a> atau <a
                href="#" class="font-bold text-blue-600 hover:underline">Admin</a>
        </p>
    </div>
</div>
