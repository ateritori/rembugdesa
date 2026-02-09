@extends('layouts.dashboard')

@section('title', 'Decision Maker Panel')

@section('content')

    {{-- NOTIFICATION SYSTEM --}}
    <div class="fixed top-6 right-6 z-[100] space-y-3 w-full max-w-sm pointer-events-none">
        {{-- Success --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                class="pointer-events-auto bg-white border border-emerald-100 shadow-xl rounded-xl p-3 flex items-center gap-3">
                <div class="shrink-0 w-8 h-8 bg-emerald-50 text-emerald-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-xs font-bold text-slate-600 flex-1">{{ session('success') }}</p>
                <button @click="show = false" class="text-slate-300 hover:text-slate-500 transition-colors px-1">×</button>
            </div>
        @endif
        {{-- Error --}}
        @if (session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show" x-transition
                class="pointer-events-auto bg-white border border-rose-100 shadow-xl rounded-xl p-3 flex items-center gap-3">
                <div class="shrink-0 w-8 h-8 bg-rose-50 text-rose-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-xs font-bold text-slate-600 flex-1">{{ session('error') }}</p>
                <button @click="show = false" class="text-slate-300 hover:text-slate-500 transition-colors px-1">×</button>
            </div>
        @endif
    </div>

    <div class="space-y-5 animate-in fade-in slide-in-from-top-2 duration-500 pb-10" x-data="{ tab: '{{ session('tab') }}' || new URLSearchParams(window.location.search).get('tab') || 'pairwise' }">

        {{-- MODERN MINIMALIST TABS --}}
        <div class="flex items-center border-b border-slate-200 gap-8">
            <button @click="tab = 'pairwise'; window.history.replaceState(null, '', '?tab=pairwise')"
                class="pb-3 text-xs font-black uppercase tracking-widest transition-all relative"
                :class="tab === 'pairwise' ? 'text-primary' : 'text-slate-400 hover:text-slate-600'">
                Perbandingan
                <div x-show="tab === 'pairwise'" x-transition.duration.300ms
                    class="absolute bottom-0 left-0 w-full h-0.5 bg-primary rounded-full"></div>
            </button>

            <button @click="tab = 'weights'; window.history.replaceState(null, '', '?tab=weights')"
                class="pb-3 text-xs font-black uppercase tracking-widest transition-all relative"
                :class="tab === 'weights' ? 'text-primary' : 'text-slate-400 hover:text-slate-600'">
                Hasil Bobot
                <div x-show="tab === 'weights'" x-transition.duration.300ms
                    class="absolute bottom-0 left-0 w-full h-0.5 bg-primary rounded-full"></div>
            </button>
        </div>

        {{-- TAB CONTENT --}}
        <div class="mt-4">
            {{-- PAIRWISE --}}
            <div x-show="tab === 'pairwise'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2">
                @if ($decisionSession->status === 'active')
                    @include('dms.partials.pairwise')
                @else
                    <div class="bg-slate-50 p-12 text-center rounded-2xl border border-slate-200">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Akses Ditutup</p>
                    </div>
                @endif
            </div>

            {{-- WEIGHTS --}}
            <div x-show="tab === 'weights'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2">
                @if (in_array($decisionSession->status, ['active', 'alternatives', 'closed']))
                    @include('dms.partials.dm-weights')
                @else
                    <div class="bg-slate-50 p-12 text-center rounded-2xl border border-slate-200">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Data Belum Tersedia</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
