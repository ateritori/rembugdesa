@extends('layouts.dashboard')

@section('title', 'Bobot Kelompok')

@section('content')
    @include('dms.partials.nav')

    <div class="space-y-8 mt-4">
        {{-- Header Section --}}
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between px-2">
            <div class="space-y-1">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Bobot Kelompok
                </h2>
                <p class="text-slate-500 font-medium flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    Hasil agregasi bobot dari seluruh Decision Maker aktif.
                </p>
            </div>
        </div>

        {{-- Info Banner --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-5 rounded-3xl border border-slate-200 bg-slate-50/50 text-sm text-slate-600 leading-relaxed">
                <span class="block font-bold text-slate-900 mb-1">Metode Agregasi</span>
                Bobot kelompok dihitung menggunakan rata-rata preferensi seluruh individu secara otomatis oleh sistem.
            </div>
            <div class="p-5 rounded-3xl border border-slate-200 bg-amber-50/50 text-sm text-slate-600 leading-relaxed">
                <span class="block font-bold text-amber-900 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Akses Terkunci
                </span>
                Halaman ini bersifat <span class="font-bold">Read-Only</span>. Perubahan hanya dapat dilakukan melalui
                konsensus atau admin.
            </div>
        </div>

        {{-- Main Content Card --}}
        @if ($groupResult && !empty($groupResult->weights))
            @php
                $sortedWeights = collect($groupResult->weights)->sortDesc();
                $rank = 1;
            @endphp

            <div
                class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-md">
                {{-- Card Header --}}
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-[0.15em] text-slate-400">
                        Peringkat Kepentingan Kriteria
                    </h3>
                    <span
                        class="text-[10px] font-bold bg-white px-2 py-1 rounded-full border border-slate-200 text-slate-400">
                        {{ $sortedWeights->count() }} Kriteria
                    </span>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-white">
                                <th
                                    class="w-20 px-6 py-4 text-left font-bold uppercase tracking-wider text-slate-400 text-[11px]">
                                    Rank</th>
                                <th
                                    class="px-6 py-4 text-left font-bold uppercase tracking-wider text-slate-400 text-[11px]">
                                    Kriteria</th>
                                <th
                                    class="px-6 py-4 text-right font-bold uppercase tracking-wider text-slate-400 text-[11px]">
                                    Bobot Agregat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($sortedWeights as $criteriaId => $weight)
                                @php
                                    $criteria = $decisionSession->criteria->firstWhere('id', $criteriaId);

                                    // Style Rank (Baseline Match)
                                    $rankColor = match ($rank) {
                                        1 => 'bg-yellow-100 text-yellow-700 ring-yellow-200',
                                        2 => 'bg-slate-100 text-slate-600 ring-slate-200',
                                        3 => 'bg-orange-100 text-orange-700 ring-orange-200',
                                        default => 'bg-white text-slate-500 ring-slate-100',
                                    };
                                @endphp
                                <tr class="group transition-colors hover:bg-slate-50/80">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-black text-xs ring-1 shadow-sm {{ $rankColor }}">
                                            {{ $rank++ }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800 group-hover:text-primary transition-colors">
                                            {{ $criteria?->name ?? 'Kriteria #' . $criteriaId }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-mono font-black text-primary text-base">
                                            {{ number_format($weight, 4) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            {{-- Empty State (Baseline Match) --}}
            <div class="rounded-3xl border-2 border-dashed border-slate-200 p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4 text-slate-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-slate-900 font-bold text-lg">Belum Ada Hasil</h3>
                <p class="text-slate-500 max-w-sm mx-auto mt-1">Bobot kelompok belum dihitung oleh sistem.</p>
            </div>
        @endif
    </div>
@endsection
