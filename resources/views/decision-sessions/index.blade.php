@extends('layouts.dashboard')

@section('title', 'Sesi Keputusan')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Daftar Sesi Keputusan
                </h1>
                <p class="adaptive-text-sub mt-2 max-w-xl text-sm leading-relaxed">
                    Atur periode pengambilan keputusan, kelola kriteria, dan pantau status seleksi dalam satu tampilan
                    terpadu.
                </p>
            </div>

            <a href="{{ route('decision-sessions.create') }}"
                class="bg-primary shadow-primary/20 group flex items-center gap-2 rounded-2xl px-6 py-3 text-sm font-black text-white shadow-lg transition-all hover:scale-105 active:scale-95">
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
                                'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
                        ],
                        default => [
                            'label' => $s->status,
                            'css' => 'text-slate-500 bg-slate-500/10 border-slate-500/20',
                            'icon' =>
                                'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        ],
                    };
                @endphp

                <div class="adaptive-card hover:border-primary/40 p-5 transition-all duration-300">
                    <div class="flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">

                        {{-- INFO SECTION --}}
                        <div class="flex w-full items-start gap-5">
                            <div
                                class="bg-app border-app {{ $statusConfig['css'] }} flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border transition-transform">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $statusConfig['icon'] }}" />
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="mb-1.5 flex items-center gap-3">
                                    <h3 class="adaptive-text-main truncate text-lg font-black transition-colors">
                                        {{ $s->name }}
                                    </h3>
                                    <span
                                        class="{{ $statusConfig['css'] }} inline-flex items-center rounded-md border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider">
                                        @if ($s->status === 'active')
                                            <span class="relative mr-1.5 flex h-2 w-2">
                                                <span
                                                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-current opacity-75"></span>
                                                <span class="relative inline-flex h-2 w-2 rounded-full bg-current"></span>
                                            </span>
                                        @endif
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-4">
                                    <span
                                        class="text-primary bg-primary/10 flex items-center gap-1.5 rounded-lg px-2 py-0.5 text-xs font-black">
                                        Periode {{ $s->year }}
                                    </span>
                                    <span
                                        class="adaptive-text-sub flex items-center gap-1 text-[11px] font-bold opacity-60">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $s->created_at->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ACTIONS SECTION --}}
                        <div class="flex w-full items-center justify-end gap-1.5 md:w-auto">

                            @if ($s->status === 'closed')
                                <div
                                    class="flex items-center gap-1.5 pr-2 mr-0.5 border-r border-slate-200 dark:border-slate-700">
                                    {{-- AHP Log --}}
                                    <a href="{{ route('decision-sessions.ahp-log.index', $s->id) }}"
                                        class="group relative flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-primary/10 hover:text-primary transition-all active:scale-90">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <span
                                            class="pointer-events-none absolute -top-10 left-1/2 -translate-x-1/2 scale-0 rounded-lg bg-slate-900 px-2.5 py-1 text-[10px] font-bold text-white shadow-xl transition-all group-hover:scale-100 z-20 whitespace-nowrap">
                                            AHP Log
                                        </span>
                                    </a>

                                    {{-- SMART Log --}}
                                    <a href="{{ route('decision-sessions.smart-log.index', $s->id) }}"
                                        class="group relative flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all active:scale-90">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.628.283a2 2 0 01-1.186.127l-3.514-.451a2 2 0 01-1.817-1.93l-.149-2.276a2 2 0 011.219-1.903l2.364-1.048a2 2 0 001.089-1.2l1.32-3.446A2 2 0 0115.355 2.1l.392 2.413a2 2 0 001.314 1.519l2.412.827a2 2 0 011.334 2.112l-.473 2.448a2 2 0 00.12 1.344l1.183 2.708a2 2 0 01-.746 2.458l-1.961 1.307z" />
                                        </svg>
                                        <span
                                            class="pointer-events-none absolute -top-10 left-1/2 -translate-x-1/2 scale-0 rounded-lg bg-slate-900 px-2.5 py-1 text-[10px] font-bold text-white shadow-xl transition-all group-hover:scale-100 z-20 whitespace-nowrap">
                                            SMART Log
                                        </span>
                                    </a>

                                    {{-- BORDA Log --}}
                                    <a href="{{ route('decision-sessions.borda-log.index', $s->id) }}"
                                        class="group relative flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all active:scale-90">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 3v18h18M7 14l3-3 4 4 5-6" />
                                        </svg>
                                        <span
                                            class="pointer-events-none absolute -top-10 left-1/2 -translate-x-1/2 scale-0 rounded-lg bg-slate-900 px-2.5 py-1 text-[10px] font-bold text-white shadow-xl transition-all group-hover:scale-100 z-20 whitespace-nowrap">
                                            BORDA Log
                                        </span>
                                    </a>
                                </div>
                            @endif

                            {{-- Main Workspace Action --}}
                            <a href="{{ $s->status === 'draft' ? route('criteria.index', $s->id) : route('control.index', $s->id) }}"
                                class="group relative flex h-10 w-10 items-center justify-center rounded-xl transition-all active:scale-95 {{ $s->status === 'closed' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-slate-100 text-slate-600 hover:bg-slate-800 hover:text-white' }}">
                                @if ($s->status === 'closed')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                    </svg>
                                @endif
                                <span
                                    class="pointer-events-none absolute -top-10 left-1/2 -translate-x-1/2 scale-0 rounded-lg bg-slate-900 px-2.5 py-1 text-[10px] font-bold text-white shadow-xl transition-all group-hover:scale-100 z-20 whitespace-nowrap">
                                    {{ $s->status === 'closed' ? 'Lihat Hasil' : 'Kelola Workspace' }}
                                </span>
                            </a>

                            {{-- Edit/Delete (Draft Only) --}}
                            @if ($s->status === 'draft')
                                <div
                                    class="flex items-center gap-1 ml-1 border-l border-slate-200 dark:border-slate-700 pl-2">
                                    <a href="{{ route('decision-sessions.edit', $s->id) }}"
                                        class="group relative flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-800 transition-all active:scale-90">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span
                                            class="pointer-events-none absolute -top-10 left-1/2 -translate-x-1/2 scale-0 rounded-lg bg-slate-900 px-2.5 py-1 text-[10px] font-bold text-white shadow-xl transition-all group-hover:scale-100 z-20 whitespace-nowrap">Edit</span>
                                    </a>

                                    <form method="POST" action="{{ route('decision-sessions.destroy', $s->id) }}"
                                        onsubmit="return confirm('Hapus sesi ini?')" class="group relative">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all active:scale-90">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <span
                                            class="pointer-events-none absolute -top-10 left-1/2 -translate-x-1/2 scale-0 rounded-lg bg-rose-600 px-2.5 py-1 text-[10px] font-bold text-white shadow-xl transition-all group-hover:scale-100 z-20 whitespace-nowrap">Hapus</span>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="adaptive-card flex flex-col items-center justify-center border-2 border-dashed bg-transparent py-24">
                    <div class="bg-slate-100 dark:bg-slate-800 p-4 rounded-full mb-4">
                        <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <p class="adaptive-text-sub text-[10px] font-black uppercase tracking-[0.3em] opacity-30">Belum Ada
                        Sesi Tersedia</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
