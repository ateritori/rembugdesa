{{-- Tab: Hasil Akhir --}}
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h2 class="text-lg font-bold text-slate-800">
            Hasil Akhir Keputusan
        </h2>
        <p class="text-sm text-slate-500">
            Kontribusi penilaian Anda terhadap keputusan akhir kelompok.
        </p>
    </div>

    {{-- Guard --}}
    @if ($decisionSession->status !== 'closed')
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
            Hasil akhir belum tersedia. Keputusan belum ditutup.
        </div>
    @else
        {{-- =========================
             TABEL KONTRIBUSI DM
             ========================= --}}
        <div class="rounded-lg border bg-white">
            <div class="border-b px-4 py-3 font-semibold text-slate-700">
                Kontribusi Penilaian Anda
            </div>

            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 text-left">Alternatif</th>
                        <th class="px-4 py-2 text-right">Skor Anda (SMART)</th>
                        <th class="px-4 py-2 text-right">Peringkat Anda</th>
                        <th class="px-4 py-2 text-right">Skor Kelompok (Borda)</th>
                        <th class="px-4 py-2 text-right">Peringkat Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($resultContribution as $row)
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                {{ $row['alternative']->name ?? '-' }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                {{ number_format($row['smart_score'], 4) }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                {{ $row['smart_rank'] ?? '-' }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                {{ $row['borda_score'] }}
                            </td>
                            <td class="px-4 py-2 text-right font-semibold">
                                {{ $row['final_rank'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">
                                Data hasil belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Catatan --}}
        <div class="rounded-lg bg-slate-50 p-4 text-xs text-slate-600">
            <p>
                Skor SMART menunjukkan preferensi Anda sebagai Decision Maker.
                Skor Borda merupakan agregasi preferensi seluruh Decision Maker
                dan digunakan sebagai keputusan akhir kelompok.
            </p>
        </div>

    @endif
</div>
