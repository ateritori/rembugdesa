@extends('layouts.dashboard')

@section('title', 'Sesi Keputusan')

@section('content')
    <div class="space-y-8 animate-in fade-in duration-500 pb-10">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight leading-tight adaptive-text-main">
                    Daftar Sesi Keputusan
                </h1>
                <p class="adaptive-text-sub mt-2 max-w-xl leading-relaxed text-sm">
                    Atur periode pengambilan keputusan, kelola kriteria, dan pantau status seleksi dalam satu tampilan
                    terpadu.
                </p>
            </div>

            <a href="{{ route('decision-sessions.create') }}"
                class="group flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-2xl font-black text-sm shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Sesi Baru</span>
            </a>
        </div>

        {{-- CONTENT SECTION --}}
        <div class="grid grid-cols-1 gap-4">
            @forelse ($sessions as $s)
                @php
                    // Kita ganti warna BIRU/PURPLE statis menjadi warna PRIMARY agar selaras preset
                    $statusConfig = match ($s->status) {
                        'draft' => [
                            'label' => 'Draft',
                            'css' => 'text-slate-500 bg-slate-500/10 border-slate-500/20',
                            'icon' =>
                                'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                        ],
                        'active' => [
                            'label' => 'Sesi Aktif',
                            'css' => 'text-emerald-600 bg-emerald-500/10 border-emerald-500/20',
                            'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                        ],
                        // Menggunakan warna Primary Preset untuk fase input
                        'criteria', 'alternatives' => [
                            'label' => $s->status === 'criteria' ? 'Input Kriteria' : 'Input Alternatif',
                            'css' => 'text-primary bg-primary/10 border-primary/20',
                            'icon' =>
                                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                        ],
                        'closed' => [
                            'label' => 'Selesai',
                            'css' => 'text-rose-600 bg-rose-500/10 border-rose-500/20',
                            'icon' =>
                                'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
                        ],
                        default => [
                            'label' => $s->status,
                            'css' => 'text-slate-500 bg-slate-500/10 border-slate-500/20',
                            'icon' =>
                                'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        ],
                    };
                @endphp

                <div class="group adaptive-card p-5 hover:border-primary/40 transition-all duration-300">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">

                        <div class="flex items-start gap-5 w-full">
                            {{-- Icon Box Dinamis --}}
                            <div
                                class="shrink-0 w-14 h-14 rounded-2xl flex items-center justify-center bg-app border border-app {{ $statusConfig['css'] }}">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $statusConfig['icon'] }}" />
                                </svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1.5">
                                    <h3
                                        class="text-lg font-black adaptive-text-main truncate group-hover:text-primary transition-colors">
                                        {{ $s->name }}
                                    </h3>
                                    {{-- Tag Status Vibrant --}}
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $statusConfig['css'] }}">
                                        @if ($s->status === 'active')
                                            <span class="relative flex h-2 w-2 mr-1.5">
                                                <span
                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-current opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-current"></span>
                                            </span>
                                        @endif
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-4">
                                    <span
                                        class="flex items-center gap-1.5 text-xs font-black text-primary px-2 py-0.5 bg-primary/10 rounded-lg">
                                        Periode {{ $s->year }}
                                    </span>
                                    <span
                                        class="text-[11px] adaptive-text-sub opacity-60 font-bold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $s->created_at->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ACTIONS --}}
                        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                            <a href="{{ route('criteria.index', $s->id) }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:brightness-110 transition-all shadow-sm shadow-primary/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                <span>Kelola</span>
                            </a>

                            @if ($s->status === 'draft')
                                <div class="flex items-center gap-1 ml-2">
                                    <a href="{{ route('decision-sessions.edit', $s->id) }}"
                                        class="p-2 adaptive-text-sub hover:text-primary hover:bg-primary/10 rounded-lg transition-all">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('decision-sessions.destroy', $s->id) }}"
                                        onsubmit="return confirm('Hapus sesi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-2 adaptive-text-sub hover:text-rose-500 hover:bg-rose-500/10 rounded-lg transition-all">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="flex flex-col items-center justify-center py-24 adaptive-card border-dashed border-2 bg-transparent">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] adaptive-text-sub opacity-30">Belum Ada Sesi
                        Tersedia</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
