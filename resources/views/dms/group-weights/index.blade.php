@extends('layouts.dashboard')

@section('title', 'Bobot Kelompok')

@section('content')
    @include('dms.partials.nav')

    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="text-xl font-black text-app mb-2">Bobot Kelompok</h2>
        <p class="text-sm text-gray-600 mb-4">
            Bobot ini merupakan hasil agregasi dari seluruh Decision Maker.
        </p>

        <div class="space-y-4">

            {{-- Explanation --}}
            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-600">
                Bobot kelompok merupakan hasil agregasi bobot individu seluruh Decision Maker
                menggunakan metode yang telah ditetapkan pada sistem.
            </div>

            {{-- Aggregated Weights (Ranking) --}}
            @if ($groupResult && !empty($groupResult->weights))
                @php
                    $sortedWeights = collect($groupResult->weights)->sortDesc();
                @endphp

                <div class="overflow-hidden border border-gray-200 rounded-xl">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold text-gray-600">
                                    Kriteria
                                </th>
                                <th class="px-4 py-3 text-right font-bold text-gray-600">
                                    Bobot Kelompok
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($sortedWeights as $criteriaId => $weight)
                                @php
                                    $criteria = $decisionSession->criteria->firstWhere('id', $criteriaId);
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ $criteria?->name ?? 'Kriteria #' . $criteriaId }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold">
                                        {{ number_format($weight, 4) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-600">
                    Bobot kelompok belum tersedia atau belum dikunci oleh sistem.
                </div>
            @endif

            {{-- Locked Info --}}
            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-600">
                Halaman ini bersifat <span class="font-semibold">read-only</span> bagi Decision Maker.
                Proses perhitungan dan penguncian bobot kelompok dilakukan oleh sistem.
            </div>

        </div>
    </div>
@endsection
