@extends('layouts.dashboard')

@section('title', 'Decision Maker Workspace')

@section('content')

    {{-- NOTIFICATION SYSTEM --}}
    <div class="fixed top-6 right-6 z-[100] space-y-3 w-full max-w-sm pointer-events-none">
        {{-- Success --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                class="pointer-events-auto bg-white border border-emerald-100 shadow-2xl rounded-2xl p-4 flex items-center gap-4">
                <div class="shrink-0 w-10 h-10 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-black uppercase tracking-wider text-emerald-600">Berhasil</p>
                    <p class="text-sm font-medium text-slate-600">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-slate-300 hover:text-slate-500 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Error --}}
        @if (session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show" x-transition
                class="pointer-events-auto bg-white border border-rose-100 shadow-2xl rounded-2xl p-4 flex items-center gap-4">
                <div class="shrink-0 w-10 h-10 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-black uppercase tracking-wider text-rose-600">Terjadi Kesalahan</p>
                    <p class="text-sm font-medium text-slate-600">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-slate-300 hover:text-slate-500 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        @endif
    </div>

    @include('dms.partials.nav')

    <div class="space-y-6 pb-10 mt-6">
        {{-- Welcome Header --}}
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-8 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">
                    Sesi Keputusan Aktif
                </p>

                <h2 class="text-2xl font-black text-app mb-1">
                    {{ $decisionSession->name }}
                </h2>

                <div class="flex flex-wrap items-center gap-3 mt-2 mb-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                        Tahun {{ $decisionSession->year }}
                    </span>

                    <span
                        class="px-3 py-1 rounded-full text-xs font-bold
                        @if ($decisionSession->status === 'active') bg-blue-100 text-blue-700
                        @elseif ($decisionSession->status === 'criteria') bg-indigo-100 text-indigo-700
                        @elseif ($decisionSession->status === 'alternatives') bg-amber-100 text-amber-700
                        @elseif ($decisionSession->status === 'closed') bg-emerald-100 text-emerald-700
                        @else bg-slate-100 text-slate-600 @endif
                    ">
                        Status: {{ ucfirst($decisionSession->status) }}
                    </span>
                </div>

                <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">
                    Workspace ini digunakan untuk mengisi bobot individu, melakukan penilaian alternatif,
                    serta meninjau hasil keputusan pada sesi ini.
                </p>
            </div>
            {{-- Subtle Decorative Element --}}
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-slate-50 rounded-full opacity-50"></div>
        </div>

        {{-- Info Cards Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="group border border-slate-200 rounded-2xl p-5 bg-white hover:border-app transition-all shadow-sm">
                <div
                    class="w-8 h-8 rounded-lg bg-blue-50 text-app flex items-center justify-center mb-4 group-hover:bg-app group-hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">
                    Bobot Individu
                </p>
                <p class="text-sm text-slate-600 leading-snug">
                    Input perbandingan berpasangan kriteria (AHP).
                </p>
            </div>

            <div class="group border border-slate-200 rounded-2xl p-5 bg-white hover:border-app transition-all shadow-sm">
                <div
                    class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">
                    Bobot Kelompok
                </p>
                <p class="text-sm text-slate-600 leading-snug">
                    Agregasi nilai dari seluruh pengambil keputusan.
                </p>
            </div>

            <div class="group border border-slate-200 rounded-2xl p-5 bg-white hover:border-app transition-all shadow-sm">
                <div
                    class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">
                    Penilaian Alternatif
                </p>
                <p class="text-sm text-slate-600 leading-snug">
                    Evaluasi alternatif berdasarkan kriteria yang ada.
                </p>
            </div>

            <div class="group border border-slate-200 rounded-2xl p-5 bg-white hover:border-app transition-all shadow-sm">
                <div
                    class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">
                    Ringkasan
                </p>
                <p class="text-sm text-slate-600 leading-snug">
                    Pantau peringkat dan hasil akhir keputusan.
                </p>
            </div>
        </div>
    </div>

@endsection
