@extends('layouts.dashboard')

@section('title', 'Alternatif')

@section('content')

    {{-- TAB NAVIGASI SESI --}}
    @include('decision-sessions.partials.nav')

    <div class="animate-in fade-in slide-in-from-bottom-2 w-full px-4 py-4 md:px-6 md:py-6 duration-500 dark:bg-slate-900">

        @if ($errors->any())
            <div
                class="mb-4 rounded-xl border border-rose-500/20 bg-rose-50 px-4 py-3 text-[11px] font-black uppercase tracking-wide text-rose-600 shadow-sm">
                <div class="mb-1 flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Input Error:</span>
                </div>
                <ul class="list-inside list-disc opacity-80">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="w-full space-y-4 md:space-y-6">

            {{-- HEADER SECTION --}}
            <div
                class="flex flex-col justify-between gap-4 border-b border-slate-100 px-1 pb-4 md:flex-row md:items-end dark:border-slate-800">
                <div>
                    <div class="mb-1 flex items-center gap-2">
                        <span class="bg-primary h-1.5 w-6 rounded-full"></span>
                        <p class="text-primary text-[9px] font-black uppercase tracking-[0.2em]">
                            Target Entities
                        </p>
                    </div>
                    <h1 class="adaptive-text-main text-xl md:text-2xl font-black uppercase tracking-tight">
                        Manajemen Alternatif
                    </h1>
                </div>

                @if ($decisionSession->status !== 'draft')
                    <div
                        class="flex items-center self-start md:self-auto gap-2.5 rounded-xl bg-slate-900 px-4 py-2 text-white shadow-lg dark:bg-slate-800">
                        <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Akses Terkunci</span>
                    </div>
                @endif
            </div>

            {{-- FORM TAMBAH --}}
            <div
                class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm focus-within:border-primary/30 dark:border-slate-700 dark:bg-slate-800 {{ $decisionSession->status !== 'draft' ? 'opacity-50 grayscale pointer-events-none' : '' }}">
                <form method="POST" action="{{ route('alternatives.store', $decisionSession->id) }}"
                    class="flex flex-col gap-2 lg:flex-row">
                    @csrf
                    <div class="group relative w-full flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <input type="text" name="name" required
                            class="w-full rounded-xl border-none bg-slate-50 py-3 pl-11 pr-4 text-sm font-bold text-slate-700 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary/10 dark:bg-slate-900 dark:text-slate-200"
                            placeholder="Nama alternatif baru...">
                    </div>

                    <button type="submit"
                        class="w-full lg:w-auto flex-none rounded-xl bg-slate-800 px-8 py-3 text-[10px] font-black uppercase tracking-widest text-white hover:bg-black active:scale-95 dark:bg-primary-600 dark:hover:bg-primary-700">
                        + Tambah
                    </button>
                </form>
            </div>

            {{-- LIST DATA: GRID 2 KOLOM --}}
            <div class="grid grid-cols-1 gap-3 xl:grid-cols-2">
                @forelse ($alternatives as $a)
                    <div x-data="{ openEdit: false }"
                        class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm hover:border-primary/30 dark:border-slate-700 dark:bg-slate-800">

                        <div class="flex items-start justify-between px-4 py-3">
                            <div class="flex items-start gap-3 min-w-0">
                                {{-- KODE: h-9 tetap konsisten --}}
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-800 text-[10px] font-black italic text-white dark:bg-slate-700 mt-0.5">
                                    {{ $a->code }}
                                </div>

                                {{-- TEXT WRAPPING: Tidak dipenggal --}}
                                <div class="flex-1">
                                    <h3
                                        class="text-[14px] font-black uppercase tracking-tight text-slate-800 dark:text-slate-100 break-words leading-tight {{ !$a->is_active ? 'line-through opacity-40' : '' }}">
                                        {{ $a->name }}
                                    </h3>

                                    <span
                                        class="mt-1 flex items-center gap-1 text-[8px] font-bold uppercase tracking-wider {{ $a->is_active ? 'text-slate-400' : 'text-rose-500' }}">
                                        <span
                                            class="h-1 w-1 rounded-full {{ $a->is_active ? 'bg-primary/50' : 'bg-rose-500' }}"></span>
                                        {{ $a->is_active ? 'Aktif' : 'Off' }}
                                    </span>
                                </div>
                            </div>

                            {{-- ACTIONS: Tetap di tempatnya --}}
                            <div class="flex items-center gap-1 shrink-0 ml-3">
                                <button @click="openEdit = !openEdit"
                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-primary dark:hover:bg-slate-700 {{ $decisionSession->status !== 'draft' ? 'hidden' : '' }}">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 114 4L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                <form method="POST" action="{{ route('alternatives.toggle', $a->id) }}"
                                    class="{{ $decisionSession->status !== 'draft' ? 'hidden' : '' }}">
                                    @csrf @method('PATCH')
                                    <button
                                        class="rounded-lg p-1.5 {{ $a->is_active ? 'text-slate-400 hover:bg-amber-50' : 'text-emerald-500 hover:bg-emerald-50' }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if ($a->is_active)
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            @endif
                                        </svg>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('alternatives.destroy', $a->id) }}"
                                    onsubmit="return confirm('Hapus?')"
                                    class="{{ $decisionSession->status !== 'draft' ? 'hidden' : '' }}">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- EDIT FORM --}}
                        <div x-show="openEdit" x-collapse
                            class="border-t border-slate-100 bg-slate-50/50 p-3 dark:border-slate-700 dark:bg-slate-900/30">
                            <form method="POST" action="{{ route('alternatives.update', $a->id) }}" class="flex gap-2">
                                @csrf @method('PUT')
                                <input type="text" name="name" value="{{ $a->name }}" required
                                    class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold dark:bg-slate-800 dark:border-slate-700">
                                <button type="submit"
                                    class="rounded-lg bg-primary px-4 py-1.5 text-[9px] font-black uppercase text-white">Save</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full py-20 flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-100 dark:border-slate-800">
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-300">Belum ada alternatif</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
