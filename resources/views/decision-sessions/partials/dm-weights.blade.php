@if (!$criteriaWeights)
    <div class="text-sm text-red-600">
        Bobot belum tersedia. Silakan simpan penilaian perbandingan kriteria terlebih dahulu.
    </div>
@else
    <div class="mb-4">
        <h2 class="text-base font-bold adaptive-text-main">
            Bobot Kriteria (Individu)
        </h2>
        <p class="text-sm adaptive-text-sub mt-1">
            Consistency Ratio (CR):
            <strong>{{ number_format($criteriaWeights->cr, 4) }}</strong>
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border border-white/10 rounded-lg overflow-hidden">
            <thead class="bg-gray-50/70">
                <tr>
                    <th class="p-2 text-center w-16">Urutan</th>
                    <th class="p-2 text-left">Kriteria</th>
                    <th class="p-2 text-right">Bobot</th>
                </tr>
            </thead>
            <tbody>
                @foreach (collect($criteriaWeights->weights)->sortDesc() as $criteriaId => $weight)
                    <tr class="odd:bg-white/5 even:bg-transparent hover:bg-primary/5 transition-colors">
                        <td class="p-2 text-center font-semibold">
                            {{ $loop->iteration }}
                        </td>
                        <td class="p-2">
                            {{ $criterias->firstWhere('id', $criteriaId)->name ?? '-' }}
                        </td>
                        <td class="p-2 text-right font-medium">
                            {{ number_format($weight, 4) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
