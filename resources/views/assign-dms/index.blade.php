@extends('layouts.dashboard')

@section('title', 'Penugasan DM')

@section('content')

    {{-- TAB NAVIGASI SESI --}}
    @include('admin.partials.session-nav')

    <div class="animate-in fade-in slide-in-from-bottom-2 w-full px-4 py-4 md:px-6 md:py-6 duration-500 dark:bg-slate-900"
        x-data="{
            selected: {{ json_encode($assignedDmIds ?? []) }},
            search: '',
            toggleDm(id) {
                const index = this.selected.indexOf(id);
                if (index > -1) {
                    this.selected.splice(index, 1);
                } else {
                    this.selected.push(id);
                }
            }
        }">

        <div class="w-full space-y-4 md:space-y-6">

            {{-- HEADER SECTION: Disinkronkan dengan Kriteria/Alternatif --}}
            <div
                class="flex flex-col justify-between gap-4 border-b border-slate-100 px-1 pb-4 md:flex-row md:items-end dark:border-slate-800">
                <div>
                    <div class="mb-1 flex items-center gap-2">
                        <span class="bg-indigo-500 h-1.5 w-6 rounded-full"></span>
                        <p class="text-indigo-500 text-[9px] font-black uppercase tracking-[0.2em]">System Authorization</p>
                    </div>
                    <h1
                        class="adaptive-text-main text-xl md:text-2xl font-black uppercase tracking-tight transition-all duration-300">
                        Penugasan Decision Maker
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

            {{-- SEARCH: Slim Style --}}
            <div class="relative w-full max-w-sm">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                </div>
                <input type="text" x-model.debounce.300ms="search" placeholder="Cari DM..."
                    class="w-full rounded-xl border-none bg-white py-2.5 pl-11 pr-4 text-xs font-bold ring-1 ring-slate-200 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-800 dark:ring-slate-700 dark:text-white">
            </div>

            <form method="POST" action="{{ route('decision-sessions.assign-dms.store', $decisionSession->id) }}">
                @csrf

                {{-- GRID DM: Slim Cards --}}
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                    @forelse ($dms as $dm)
                        <div x-show="search === '' || '{{ strtolower($dm->name) }}'.includes(search.toLowerCase())"
                            @click="{{ $decisionSession->status === 'draft' ? "toggleDm($dm->id)" : '' }}"
                            :class="selected.includes({{ $dm->id }}) ?
                                'border-indigo-500 bg-indigo-50/30 dark:bg-indigo-500/5 ring-1 ring-indigo-500/20 shadow-sm' :
                                'border-slate-200 bg-white dark:bg-slate-800 dark:border-slate-700'"
                            class="group relative flex cursor-pointer select-none flex-col gap-3 rounded-xl border p-4 transition-all duration-200 {{ $decisionSession->status !== 'draft' ? 'opacity-60 pointer-events-none' : 'hover:border-slate-400 hover:shadow-sm' }}">

                            <input type="checkbox" name="dm_ids[]" value="{{ $dm->id }}" class="hidden"
                                :checked="selected.includes({{ $dm->id }})">

                            <div class="flex items-center justify-between">
                                <div :class="selected.includes({{ $dm->id }}) ? 'bg-indigo-600 text-white' :
                                    'bg-slate-100 text-slate-400 dark:bg-slate-700'"
                                    class="flex h-9 w-9 items-center justify-center rounded-lg transition-colors shadow-sm">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>

                                <div class="flex h-5 w-5 items-center justify-center rounded-full border-2 transition-all"
                                    :class="selected.includes({{ $dm->id }}) ?
                                        'bg-indigo-500 border-indigo-500 text-white' :
                                        'border-slate-200 dark:border-slate-600'">
                                    <svg x-show="selected.includes({{ $dm->id }})" class="h-3 w-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>

                            <div class="min-w-0">
                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">
                                    D{{ $loop->iteration }}</p>
                                <h3 :class="selected.includes({{ $dm->id }}) ? 'text-indigo-600' : 'adaptive-text-main'"
                                    class="truncate text-[13px] font-black uppercase tracking-tight transition-colors">
                                    {{ $dm->name }}
                                </h3>
                                <p class="truncate text-[9px] font-bold text-slate-400 uppercase tracking-tighter">
                                    {{ $dm->email }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full py-20 flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-100 dark:border-slate-800">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-300">Data Source Empty
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- ACTION FOOTER: Sticky & Slim --}}
                <div
                    class="sticky bottom-6 z-10 mt-12 flex items-center justify-between rounded-2xl border border-slate-200 bg-white/90 p-3 shadow-2xl backdrop-blur-md dark:bg-slate-800/95 dark:border-slate-700">
                    <div class="flex items-center gap-4 pl-2">
                        <div class="flex flex-col">
                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Selected</p>
                            <p class="text-xl font-black leading-none text-indigo-600 dark:text-indigo-400"
                                x-text="selected.length"></p>
                        </div>
                        <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-700"></div>
                        <p class="hidden text-[9px] font-bold uppercase text-slate-400 lg:block">
                            Klik kartu user untuk memberikan hak akses penilaian.
                        </p>
                    </div>

                    @if ($decisionSession->status === 'draft')
                        <div class="flex gap-2">
                            <button type="button" @click="selected = []"
                                class="rounded-xl px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-rose-500 transition-all">
                                Clear
                            </button>
                            <button type="submit"
                                class="rounded-xl bg-slate-900 px-8 py-2.5 text-[10px] font-black uppercase tracking-widest text-white hover:bg-black active:scale-95 transition-all dark:bg-indigo-600">
                                Confirm Assignment
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

@endsection
