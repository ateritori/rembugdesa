{{-- =========================================================
 | TAB ANALISIS: Perbandingan Hasil Keputusan
 | ========================================================= --}}

@php
    $borda = $borda ?? collect();
    $sawBorda = $sawBorda ?? collect();
@endphp

<div class="space-y-8 animate-in fade-in duration-500">

    {{-- HEADER DENGAN AKSEN --}}
    <div class="border-l-4 border-indigo-500 pl-4">
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Analisis Perbandingan Metode</h1>
        <p class="mt-1 text-sm text-slate-500 max-w-2xl">
            Evaluasi sinkronisasi antara algoritma <span class="font-semibold text-indigo-600">AHP+SMART</span>
            dibandingkan dengan <span class="font-semibold text-slate-700">AHP+SAW</span> menggunakan agregasi Borda.
        </p>
    </div>

    {{-- GRID UNTUK TABEL UTAMA & BENCHMARK --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- CARD: SMART + BORDA --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="bg-slate-50 border-b px-5 py-3 flex justify-between items-center">
                <span class="font-bold text-slate-700 uppercase text-xs tracking-wider">
                    Metode Utama: AHP + SMART + Borda
                </span>
                <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded-full">CORE</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-slate-500 border-b">
                            <th class="px-5 py-3 text-left font-semibold">Rank</th>
                            <th class="px-5 py-3 text-left font-semibold">Alternatif</th>
                            <th class="px-5 py-3 text-right font-semibold">Skor Borda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($borda as $row)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3 text-center w-16">
                                    <span
                                        class="inline-flex items-center justify-center h-7 w-7 rounded-full {{ $loop->first ? 'bg-amber-100 text-amber-700 font-bold' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $row->final_rank }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-medium text-slate-700">{{ $row->alternative->name ?? '-' }}
                                </td>
                                <td class="px-5 py-3 text-right font-mono text-slate-600">{{ $row->borda_score }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-10 text-center text-slate-400 italic">Data belum
                                    tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- CARD: SAW + BORDA --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="bg-slate-50 border-b px-5 py-3">
                <span class="font-bold text-slate-700 uppercase text-xs tracking-wider">
                    Benchmark: AHP + SAW + Borda
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-slate-500 border-b">
                            <th class="px-5 py-3 text-left font-semibold">Rank</th>
                            <th class="px-5 py-3 text-left font-semibold">Alternatif</th>
                            <th class="px-5 py-3 text-right font-semibold">Skor Borda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($sawBorda as $row)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3 text-center w-16">
                                    <span
                                        class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-slate-100 text-slate-600">
                                        {{ $row->final_rank }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-slate-600">{{ $row->alternative->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-right font-mono text-slate-500">{{ $row->borda_score }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-10 text-center text-slate-400 italic">Data benchmark
                                    belum tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- =========================
         PERBANDINGAN ANALITIS
         ========================= --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-md overflow-hidden">
        <div class="border-b bg-white px-6 py-4">
            <h3 class="font-bold text-slate-800">Matriks Perbandingan Peringkat</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b">
                        <th class="px-6 py-4 text-center font-bold">#</th>
                        <th class="px-6 py-4 text-left font-bold">Alternatif Program</th>
                        <th class="px-6 py-4 text-center font-bold">Rank Core<br><span
                                class="text-[10px] font-semibold text-slate-400">AHP + SMART + Borda</span></th>
                        <th class="px-6 py-4 text-center font-bold">Rank Benchmark<br><span
                                class="text-[10px] font-semibold text-slate-400">AHP + SAW + Borda</span></th>
                        <th class="px-6 py-4 text-center font-bold">Selisih</th>
                        <th class="px-6 py-4 text-center font-bold">Status Keabsahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($borda as $row)
                        @php
                            $saw = $sawBorda->firstWhere('alternative_id', $row->alternative_id);
                            $rankSmart = $row->final_rank;
                            $rankSaw = $saw?->final_rank;
                            $diff = is_null($rankSaw) ? null : abs($rankSmart - $rankSaw);
                        @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-bold text-slate-400">A{{ $row->alternative_id }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700 uppercase tracking-tight">
                                {{ $row->alternative->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-indigo-600">{{ $rankSmart }}</td>
                            <td class="px-6 py-4 text-center font-medium text-slate-500">{{ $rankSaw ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if ($diff === 0)
                                    <span class="text-emerald-500 font-bold">0</span>
                                @else
                                    <span class="text-rose-500 font-bold">{{ $diff }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if (is_null($diff))
                                    <span class="text-slate-300">-</span>
                                @elseif ($diff === 0)
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Konsisten
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a1 1 0 100-2 1 1 0 000 2z"></path>
                                            <path fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Variasi
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <span class="mt-2 text-slate-400">Data analisis belum tersedia.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
