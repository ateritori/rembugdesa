@extends('layouts.dashboard')

@section('title', 'Workspace Sesi Keputusan')

@section('content')
    {{-- CSS tambahan untuk mencegah elemen berkedip (bisa diletakkan di layout utama) --}}
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div x-data="{
        {{-- Ambil tab dari URL, jika tidak ada sesuaikan dengan Role --}}
        tab: new URLSearchParams(window.location.search).get('tab') ||
            (@json(auth()->user()->hasRole('dm')) ? 'pairwise' : 'criteria'),
    
            {{-- Fungsi untuk update URL tanpa reload halaman --}}
        updateUrl(val) {
            const url = new URL(window.location);
            url.searchParams.set('tab', val);
            window.history.pushState({}, '', url);
        }
    }" x-init="$watch('tab', val => updateUrl(val))" class="bg-card p-6 rounded shadow">

        {{-- ================= HEADER SESI ================= --}}
        <div class="flex justify-between items-start mb-6 gap-4">
            <div>
                <h1 class="text-lg font-semibold">{{ $decisionSession->name }}</h1>
                <p class="text-sm opacity-70">Status sesi: <span class="capitalize">{{ $decisionSession->status }}</span></p>
            </div>

            <a href="{{ auth()->user()->hasRole('admin') ? route('decision-sessions.index') : route('dashboard') }}"
                class="text-sm px-4 py-2 rounded border border-app hover:bg-gray-100 transition">
                ← Kembali
            </a>
        </div>

        {{-- ================= TAB HEADER ================= --}}
        <div class="border-b border-app mb-6 overflow-x-auto">
            <div class="flex gap-2 min-w-max">
                @role('admin')
                    <button @click="tab='criteria'"
                        :class="tab === 'criteria' ? 'border-b-2 border-primary text-primary font-bold' : 'opacity-60'"
                        class="px-4 py-2 text-sm font-medium transition-all">
                        Kriteria
                    </button>

                    <button @click="tab='alternatives'"
                        :class="tab === 'alternatives' ? 'border-b-2 border-primary text-primary font-bold' : 'opacity-60'"
                        class="px-4 py-2 text-sm font-medium transition-all">
                        Alternatif
                    </button>

                    <button @click="tab='dm'"
                        :class="tab === 'dm' ? 'border-b-2 border-primary text-primary font-bold' : 'opacity-60'"
                        class="px-4 py-2 text-sm font-medium transition-all">
                        Decision Maker
                    </button>

                    <button @click="tab='control'"
                        :class="tab === 'control' ? 'border-b-2 border-primary text-primary font-bold' : 'opacity-60'"
                        class="px-4 py-2 text-sm font-medium transition-all">
                        Kontrol
                    </button>
                @endrole

                @role('dm')
                    <button @click="tab='pairwise'"
                        :class="tab === 'pairwise' ? 'border-b-2 border-primary text-primary font-bold' : 'opacity-60'"
                        class="px-4 py-2 text-sm font-medium transition-all">
                        Pairwise Kriteria
                    </button>
                @endrole
            </div>
        </div>

        {{-- ================= TAB CONTENT ================= --}}
        {{-- Kita biarkan semua partial dirender oleh Blade, Alpine yang mengatur tampilannya --}}

        @role('admin')
            <div x-show="tab === 'criteria'" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2">
                @include('decision-sessions.partials.criteria')
            </div>

            <div x-show="tab === 'alternatives'" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2">
                @include('decision-sessions.partials.alternatives')
            </div>

            <div x-show="tab === 'dm'" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2">
                @include('decision-sessions.partials.assign-dm')
            </div>

            <div x-show="tab === 'control'" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2">
                @include('decision-sessions.partials.controls')
            </div>
        @endrole

        @role('dm')
            <div x-show="tab === 'pairwise'" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2">
                @include('decision-sessions.partials.pairwise')
            </div>
        @endrole

    </div>
@endsection
