@extends('layouts.dashboard')

@section('title', 'Decision Maker Panel')

@section('content')

    <div class="space-y-6 animate-in fade-in slide-in-from-top-4 duration-500 pb-10" x-data="{ tab: new URLSearchParams(window.location.search).get('tab') || 'pairwise' }">

        {{-- NAVIGATION TAB SYSTEM --}}
        <div class="adaptive-card p-1.5 rounded-2xl flex items-center gap-1 inline-flex mb-2">
            <button @click="tab = 'pairwise'; window.history.replaceState(null, '', '?tab=pairwise')"
                :class="tab === 'pairwise'
                    ?
                    'bg-primary text-white shadow-lg shadow-primary/20' :
                    'text-app opacity-50 hover:opacity-100 hover:bg-app/10'"
                class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Perbandingan Kriteria
            </button>

            <button @click="tab = 'weights'; window.history.replaceState(null, '', '?tab=weights')"
                :class="tab === 'weights'
                    ?
                    'bg-primary text-white shadow-lg shadow-primary/20' :
                    'text-app opacity-50 hover:opacity-100 hover:bg-app/10'"
                class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                Hasil Bobot
            </button>
        </div>

        {{-- TAB CONTENT: PAIRWISE --}}
        <div x-show="tab === 'pairwise'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="space-y-4">

            @if ($decisionSession->status === 'active')
                @include('dms.partials.pairwise')
            @else
                <div class="adaptive-card p-12 text-center border-dashed">
                    <div
                        class="w-16 h-16 bg-rose-500/10 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-app font-black uppercase tracking-widest mb-1">Akses Ditutup</h3>
                    <p class="text-sm text-app opacity-50 italic">Sesi ini sudah tidak dalam tahap perbandingan kriteria.
                    </p>
                </div>
            @endif
        </div>

        {{-- TAB CONTENT: WEIGHTS --}}
        <div x-show="tab === 'weights'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="space-y-4">

            @if (
                $decisionSession->status === 'active' ||
                    $decisionSession->status === 'alternatives' ||
                    $decisionSession->status === 'closed')
                @include('dms.partials.dm-weights')
            @else
                <div class="adaptive-card p-12 text-center border-dashed">
                    <div
                        class="w-16 h-16 bg-amber-500/10 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-app font-black uppercase tracking-widest mb-1">Bobot Belum Tersedia</h3>
                    <p class="text-sm text-app opacity-50 italic">Selesaikan perbandingan kriteria terlebih dahulu.</p>
                </div>
            @endif
        </div>

    </div>
@endsection
