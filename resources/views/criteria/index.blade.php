@extends('layouts.dashboard')

@section('title', 'Manajemen Kriteria')

@section('content')

    @include('admin.partials.session-nav')

    {{-- Logika Pengunci: Aksi hanya boleh dilakukan jika status MASIH draft --}}
    @php
        $isLocked = $decisionSession->status !== 'draft';
    @endphp

    <div class="animate-in fade-in slide-in-from-bottom-2 w-full px-4 py-4 md:px-6 md:py-6 duration-500 dark:bg-slate-900">

        <div class="w-full space-y-4 md:space-y-6">

            {{-- HEADER SECTION --}}
            <div
                class="flex flex-col justify-between gap-4 border-b border-slate-100 px-1 pb-4 md:flex-row md:items-end dark:border-slate-800">
                <div>
                    <div class="mb-1 flex items-center gap-2">
                        <span class="bg-primary h-1.5 w-6 rounded-full"></span>
                        <p class="text-primary text-[9px] font-black uppercase tracking-[0.2em]">
                            Parameter Engine
                        </p>
                    </div>
                    <h1
                        class="adaptive-text-main text-xl md:text-2xl font-black uppercase tracking-tight transition-colors duration-300">
                        Manajemen Kriteria
                    </h1>
                </div>

                {{-- Status Indicator --}}
                @if ($isLocked)
                    <div
                        class="flex items-center self-start md:self-auto gap-2.5 rounded-xl bg-slate-900 px-4 py-2 text-white shadow-lg dark:bg-slate-800">
                        <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Akses Terkunci</span>
                    </div>
                @endif
            </div>

            {{-- FORM TAMBAH --}}
            <div
                class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm focus-within:border-primary/30 dark:border-slate-700 dark:bg-slate-800 {{ $isLocked ? 'opacity-50 grayscale pointer-events-none' : '' }}">
                <form method="POST" action="{{ route('criteria.store', $decisionSession->id) }}"
                    class="flex flex-col gap-2 lg:flex-row">
                    @csrf
                    <div class="group relative w-full flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />
                            </svg>
                        </div>
                        <input name="name" required {{ $isLocked ? 'disabled' : '' }}
                            class="w-full rounded-xl border-none bg-slate-50 py-3 pl-11 pr-4 text-sm font-bold text-slate-700 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary/10 dark:bg-slate-900 dark:text-slate-200"
                            placeholder="Nama kriteria baru...">
                    </div>

                    <div class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">
                        <select name="type" required {{ $isLocked ? 'disabled' : '' }}
                            class="w-full rounded-xl border-none bg-slate-50 px-4 py-3 text-[11px] font-black uppercase tracking-wider text-slate-500 lg:w-44 dark:bg-slate-900">
                            <option value="" disabled selected>TIPE</option>
                            <option value="benefit">BENEFIT</option>
                            <option value="cost">COST</option>
                        </select>

                        <button type="submit" {{ $isLocked ? 'disabled' : '' }}
                            class="w-full lg:w-auto flex-none rounded-xl bg-slate-800 px-8 py-3 text-[10px] font-black uppercase tracking-widest text-white hover:bg-black active:scale-95 dark:bg-primary-600">
                            + Tambah
                        </button>
                    </div>
                </form>
            </div>

            {{-- LIST DATA --}}
            <div class="grid grid-cols-1 gap-3 xl:grid-cols-2">
                @forelse ($criteria as $index => $c)
                    @php
                        $rule = $scoringRules->get($c->id);
                        $isBenefit = $c->type === 'benefit';
                    @endphp

                    <div x-data="{ openEdit: false, openScoring: false }"
                        class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition-all {{ !$isLocked ? 'hover:border-primary/30' : '' }} dark:border-slate-700 dark:bg-slate-800">

                        {{-- BODY --}}
                        <div class="flex items-start px-4 py-3 md:px-5">
                            <div class="flex items-start gap-3 min-w-0">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-800 text-[10px] font-black italic text-white dark:bg-slate-700 mt-0.5">
                                    C{{ $index + 1 }}
                                </div>

                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3
                                            class="text-[14px] font-black uppercase tracking-tight text-slate-800 dark:text-slate-100 break-words leading-tight">
                                            {{ $c->name }}
                                        </h3>
                                        <span
                                            class="rounded-md px-1.5 py-0.5 text-[7px] font-black uppercase tracking-wider {{ $isBenefit ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30' : 'bg-orange-100 text-orange-600 dark:bg-orange-900/30' }}">
                                            {{ $c->type }}
                                        </span>
                                    </div>

                                    <div class="mt-1 flex items-center gap-3">
                                        <span
                                            class="flex items-center gap-1 text-[8px] font-bold uppercase tracking-wider {{ $c->is_active ? 'text-slate-400' : 'text-rose-500' }}">
                                            <span
                                                class="h-1 w-1 rounded-full {{ $c->is_active ? 'bg-primary/50' : 'bg-rose-500 animate-pulse' }}"></span>
                                            {{ $c->is_active ? 'Aktif' : 'Off' }}
                                        </span>
                                        <span class="h-2 w-[1px] bg-slate-200 dark:bg-slate-700"></span>
                                        @if ($rule)
                                            <span
                                                class="flex items-center gap-1 text-[8px] font-bold uppercase tracking-wider text-emerald-500">
                                                <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                                        d="M5 13l4 4L19 7" />
                                                </svg> Ready
                                            </span>
                                        @else
                                            <span
                                                class="flex items-center gap-1 text-[8px] font-bold uppercase tracking-wider text-amber-500">
                                                <svg class="h-2.5 w-2.5 animate-bounce" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg> Set
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- FOOTER --}}
                        <div
                            class="flex items-center justify-between border-t border-slate-50 bg-slate-50/30 px-4 py-2 dark:border-slate-700/50 dark:bg-slate-900/20">
                            <div class="flex items-center gap-1">
                                {{-- Edit Button --}}
                                <button @click="openEdit = !openEdit" {{ $isLocked ? 'disabled' : '' }}
                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-primary transition-all disabled:opacity-20 disabled:cursor-not-allowed">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 114 4L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                <div class="h-4 w-[1px] bg-slate-200 dark:bg-slate-700 mx-1"></div>

                                {{-- Active Toggle --}}
                                <form method="POST" action="{{ route('criteria.toggle', $c->id) }}">
                                    @csrf @method('PATCH')
                                    <button
                                        class="rounded-lg p-1.5 transition-all {{ $c->is_active ? 'text-slate-400' : 'text-emerald-500' }} disabled:opacity-20 disabled:cursor-not-allowed"
                                        {{ $isLocked ? 'disabled' : '' }}>
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $c->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}" />
                                        </svg>
                                    </button>
                                </form>

                                {{-- Hapus --}}
                                <form method="POST" action="{{ route('criteria.destroy', $c->id) }}"
                                    onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button
                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all disabled:opacity-20 disabled:cursor-not-allowed"
                                        {{ $isLocked ? 'disabled' : '' }}>
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            {{-- Parameter Button: DISABLED TOTAL JIKA BUKAN DRAFT --}}
                            <button type="button" @click="{{ $isLocked ? '' : 'openScoring = !openScoring' }}"
                                {{ $isLocked ? 'disabled' : '' }}
                                :class="openScoring ? 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200 shadow-none' : ''"
                                class="rounded-lg px-4 py-1.5 text-[9px] font-black uppercase tracking-widest transition-all shadow-sm
                                {{ $rule ? 'bg-white border border-slate-200 text-slate-600' : 'bg-slate-900 text-white' }}
                                dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200
                                disabled:opacity-40 disabled:grayscale disabled:cursor-not-allowed">

                                <div class="flex items-center gap-2">
                                    <span
                                        x-text="openScoring ? 'Tutup' : '{{ $rule ? 'Edit Parameter' : 'Set Parameter' }}'"></span>
                                    @if (!$isLocked)
                                        <svg class="h-3 w-3 transition-transform duration-300"
                                            :class="openScoring ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @else
                                        <svg class="h-3 w-3 text-amber-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    @endif
                                </div>
                            </button>
                        </div>

                        {{-- EDIT FORM INLINE --}}
                        <div x-show="openEdit" x-collapse
                            class="border-t border-slate-100 bg-slate-50/50 p-3 dark:border-slate-700 dark:bg-slate-900/30">
                            <form method="POST" action="{{ route('criteria.update', $c->id) }}"
                                class="flex flex-col sm:flex-row gap-2">
                                @csrf
                                @method('PUT')

                                <input name="name" value="{{ $c->name }}" required
                                    class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold dark:bg-slate-800 dark:border-slate-700 outline-none focus:ring-2 focus:ring-primary/10">

                                <select name="type" required
                                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200">
                                    <option value="benefit" {{ $c->type === 'benefit' ? 'selected' : '' }}>
                                        BENEFIT
                                    </option>
                                    <option value="cost" {{ $c->type === 'cost' ? 'selected' : '' }}>
                                        COST
                                    </option>
                                </select>

                                <button
                                    class="rounded-lg bg-primary px-4 py-1.5 text-[9px] font-black uppercase text-white hover:brightness-110">
                                    Save
                                </button>
                            </form>
                        </div>

                        {{-- SCORING RULE PANEL --}}
                        <div x-show="openScoring" x-collapse
                            class="border-t border-slate-100 bg-white dark:border-slate-700 dark:bg-slate-800">
                            <div class="p-3 md:p-4 overflow-x-auto">
                                @include('criteria.partials.scoring-rule', [
                                    'c' => $c,
                                    'rule' => $rule,
                                    'isLocked' => $isLocked,
                                ])
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full py-20 flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-100 dark:border-slate-800">
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-300">Belum ada data kriteria
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
