<div class="space-y-8 animate-in fade-in zoom-in-95 duration-500">

    {{-- 1. HERO HEADER: Command Center Style --}}
    <div
        class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-1 md:p-2 shadow-2xl shadow-slate-200/50">
        <div class="relative overflow-hidden rounded-[1.7rem] bg-slate-900 px-8 py-10 text-white">
            {{-- Decorative Shapes --}}
            <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-10 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div class="max-w-2xl">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-400">Decision
                            Workspace</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black tracking-tight mb-1">
                        {{ $decisionSession->name }}
                    </h2>
                    <p class="text-sm font-bold text-blue-400 uppercase tracking-wider">
                        Tahun Sesi {{ $decisionSession->year }}
                    </p>
                    <p class="text-slate-400 font-medium leading-relaxed">
                        Selamat datang kembali. Sistem telah siap untuk memproses penilaian Anda.
                        Pastikan data yang Anda masukkan objektif untuk hasil terbaik.
                    </p>
                </div>

                <div class="flex items-center gap-4 rounded-2xl bg-white/5 p-4 backdrop-blur-md border border-white/10">
                    <div class="h-12 w-12 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-400">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Status Sesi
                        </p>
                        <p class="text-sm font-bold text-white uppercase">{{ $decisionSession->status }}</p>
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
                    'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z',
                    'color' => 'blue',
                    'done' => $dmHasCompleted,
                    'link' => route('dms.index', [
                        'decisionSession' => $decisionSession->id,
                        'tab' => 'penilaian-kriteria',
                    ]),
                ],
                [
                    'label' => 'TAHAP 2',
                    'title' => 'Bobot Kelompok',
                    'icon' =>
                        'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    'color' => 'indigo',
                    'done' => in_array($decisionSession->status, ['scoring', 'closed']),
                    'link' => '#',
                ],
                [
                    'label' => 'TAHAP 3',
                    'title' => 'Evaluasi Alternatif',
                    'icon' =>
                        'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                    'color' => 'amber',
                    'done' => $decisionSession->dmEvaluationFinished ?? false,
                    'link' => route('dms.index', [
                        'decisionSession' => $decisionSession->id,
                        'tab' => 'evaluasi-alternatif',
                    ]),
                ],
                [
                    'label' => 'TAHAP 4',
                    'title' => 'Ranking Akhir',
                    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'color' => 'emerald',
                    'done' => $decisionSession->status === 'closed',
                    'link' => route('dms.index', [
                        'decisionSession' => $decisionSession->id,
                        'tab' => 'hasil-akhir',
                    ]),
                ],
            ];
        @endphp

        @foreach ($steps as $step)
            <a href="{{ $step['link'] }}" class="group relative block">
                <div
                    class="relative h-full overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm transition-all duration-300 group-hover:-translate-y-2 group-hover:shadow-xl group-hover:border-{{ $step['color'] }}-200">

                    {{-- Step Circle --}}
                    <div
                        class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-{{ $step['color'] }}-50 text-{{ $step['color'] }}-500 transition-colors group-hover:bg-{{ $step['color'] }}-500 group-hover:text-white">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $step['icon'] }}" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">
                            {{ $step['label'] }}</p>
                        <h4 class="text-xl font-black text-slate-800">{{ $step['title'] }}</h4>
                    </div>

                    {{-- Status Indicator --}}
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
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 group-hover:text-{{ $step['color'] }}-500 transition-colors">
                                Mulai Tugas
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </span>
                        @endif
                    </div>

                    {{-- Hover Effect Glow --}}
                    <div
                        class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-{{ $step['color'] }}-500/5 opacity-0 transition-opacity group-hover:opacity-100">
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- 3. FOOTER INFO: Visual Shortcut --}}
    <div class="rounded-[2rem] border border-dashed border-slate-300 p-8 text-center">
        <p class="text-sm font-medium text-slate-500">
            Butuh bantuan dalam melakukan penilaian?
            <a href="#" class="text-blue-600 font-bold hover:underline">Buka Panduan Penggunaan</a> atau
            <a href="#" class="text-blue-600 font-bold hover:underline">Hubungi Administrator</a>
        </p>
    </div>
</div>
