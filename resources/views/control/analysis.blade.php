{{-- =========================================================
 | TAB ANALISIS
 | Perbandingan Hasil Keputusan:
 | - AHP + SMART + Borda (hasil utama)
 | - AHP + SAW + Borda (benchmark)
 | =========================================================
--}}

@php
    // Defensive defaults
    $borda = $borda ?? collect(); // AHP + SMART + Borda
    $sawBorda = $sawBorda ?? collect(); // AHP + SAW + Borda
@endphp

<div class="space-y-10">

    {{-- HEADER --}}
    <div>
        <h1 class="text-xl font-bold text-slate-800">Analisis Perbandingan Metode</h1>
        <p class="mt-1 text-sm text-slate-500">
            Analisis perbandingan hasil keputusan antara pendekatan
            <strong>AHP + SMART + Borda</strong> dan
            <strong>AHP + SAW + Borda</strong>.
        </p>
    </div>

    {{-- =========================
         AHP + SMART + BORDA
         ========================= --}}
    <div class="rounded-xl border bg-white">
        <div class="border-b px-4 py-3 font-semibold text-slate-700">
            Hasil AHP + SMART + Borda
        </div>

        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left">Rank</th>
                    <th class="px-4 py-2 text-left">Alternatif</th>
                    <th class="px-4 py-2 text-right">Skor Borda</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($borda as $row)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $row->final_rank }}</td>
                        <td class="px-4 py-2">
                            {{ $row->alternative->name ?? '-' }}
                        </td>
                        <td class="px-4 py-2 text-right">
                            {{ $row->borda_score }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-400">
                            Data belum tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- =========================
         AHP + SAW + BORDA
         ========================= --}}
    <div class="rounded-xl border bg-white">
        <div class="border-b px-4 py-3 font-semibold text-slate-700">
            Hasil AHP + SAW + Borda (Benchmark)
        </div>

        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left">Rank</th>
                    <th class="px-4 py-2 text-left">Alternatif</th>
                    <th class="px-4 py-2 text-right">Skor Borda</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sawBorda as $row)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $row->final_rank }}</td>
                        <td class="px-4 py-2">
                            {{ $row->alternative->name ?? '-' }}
                        </td>
                        <td class="px-4 py-2 text-right">
                            {{ $row->borda_score }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-400">
                            Data benchmark belum tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
