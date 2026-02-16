@extends('layouts.dashboard')

@section('title', 'Bobot Individu')

@section('content')
    @include('dms.partials.nav')

    <div class="bg-white border border-gray-200 rounded-xl p-6 mt-6">
        <h2 class="text-xl font-black text-app mb-2">Bobot Individu</h2>
        <p class="text-sm text-gray-600 mb-4">
            Tentukan tingkat kepentingan antar kriteria menggunakan perbandingan berpasangan (AHP).
        </p>

        <div class="space-y-4">
            @if ($decisionSession->status === 'active')
                {{-- Form Mode --}}
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    @include('dms.partials.pairwise')
                </div>
            @else
                {{-- Explanation --}}
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-600">
                    Bobot individu di bawah ini merupakan hasil perhitungan perbandingan berpasangan yang telah Anda
                    masukkan pada sesi aktif.
                </div>

                {{-- Individual Weights (Ranking) --}}
                @if ($existingResult && !empty($existingResult->weights))
                    @php
                        $sortedWeights = collect($existingResult->weights)->sortDesc();
                    @endphp

                    <div class="overflow-hidden border border-gray-200 rounded-xl">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-bold text-gray-600">
                                        Kriteria
                                    </th>
                                    <th class="px-4 py-3 text-right font-bold text-gray-600">
                                        Bobot Individu
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($sortedWeights as $criteriaId => $weight)
                                    @php
                                        $criteria = $decisionSession->criteria->firstWhere('id', $criteriaId);
                                    @endphp
                                    <tr>
                                        <td
                                            class="px-4 py-3 text-gray-700 font-medium break-words whitespace-normal w-full max-w-none">
                                            {{ $criteria?->name ?? 'Kriteria #' . $criteriaId }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono font-bold text-gray-700">
                                            {{ number_format($weight, 4) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Consistency Ratio (CR) --}}
                    <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-sm text-emerald-700">
                        Consistency Ratio (CR):
                        <span class="font-mono font-bold">{{ number_format($existingResult->cr, 4) }}</span>
                    </div>
                @else
                    <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-600">
                        Data perbandingan berpasangan belum tersedia.
                    </div>
                @endif

                {{-- Locked Info --}}
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-600 italic">
                    Halaman ini bersifat <span class="font-semibold uppercase tracking-tighter">Read-Only</span>. Data telah
                    dikunci oleh sistem.
                </div>
            @endif
        </div>
    </div>
@endsection
