@extends('layouts.dashboard')

@section('title', 'Penugasan DM')

@section('content')

    {{-- TAB NAVIGASI SESI --}}
    @include('decision-sessions.partials.nav')

    <div class="animate-in fade-in slide-in-from-bottom-4 w-full px-4 py-6 duration-700" x-data="{
        selected: {{ json_encode($assignedDmIds ?? []) }},
        toggleDm(id) {
            const index = this.selected.indexOf(id);
            if (index > -1) {
                this.selected.splice(index, 1);
            } else {
                this.selected.push(id);
            }
        }
    }">

        {{-- CONTAINER UTAMA FULL WIDTH --}}
        <div class="w-full space-y-6">

            {{-- HEADER: Full Width with Bottom Border --}}
            <div class="flex flex-col justify-between gap-4 border-b-2 border-slate-100 px-2 pb-8 md:flex-row md:items-end">
                <div>
                    <div class="mb-2 flex items-center gap-3">
                        <span class="bg-primary h-2 w-10 rounded-full"></span>
                        <p class="text-primary text-[10px] font-black uppercase tracking-[0.3em]">System Authorization</p>
                    </div>
                    <h1
                        class="text-3xl font-black uppercase tracking-tighter adaptive-text-main transition-colors duration-300">
                        Penugasan Decision Maker
                    </h1>
                </div>

                @if ($decisionSession->status !== 'draft')
                    <div
                        class="flex items-center gap-3 rounded-2xl bg-slate-900 px-6 py-3 text-white shadow-xl shadow-slate-200">
                        <svg class="text-primary h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-xs font-black uppercase tracking-widest">Akses Terkunci</span>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('decision-sessions.assign-dms.store', $decisionSession->id) }}"
                class="space-y-6">
                @csrf

                {{-- GRID DM: 4 Kolom di Desktop Besar supaya Padat --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                    @forelse ($dms as $dm)
                        <div @click="{{ $decisionSession->status === 'draft' ? "toggleDm($dm->id)" : '' }}"
                            :class="selected.includes({{ $dm->id }}) ?
                                'border-primary bg-primary/5 ring-4 ring-primary/5 shadow-md scale-[1.02]' :
                                'bg-white border-slate-200 hover:border-slate-400 shadow-sm'"
                            class="{{ $decisionSession->status !== 'draft' ? 'opacity-70 grayscale-[0.5] pointer-events-none' : '' }} group relative flex cursor-pointer select-none flex-col gap-4 rounded-3xl border-2 p-6 transition-all duration-300">

                            {{-- Hidden Checkbox --}}
                            <input type="checkbox" name="dm_ids[]" value="{{ $dm->id }}" class="hidden"
                                :checked="selected.includes({{ $dm->id }})">

                            {{-- Layout Atas: Avatar & Status --}}
                            <div class="flex w-full items-start justify-between">
                                <div :class="selected.includes({{ $dm->id }}) ? 'bg-primary text-white shadow-primary/30' :
                                    'bg-slate-100 text-slate-400'"
                                    class="flex h-14 w-14 items-center justify-center rounded-2xl shadow-lg transition-all">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>

                                <div class="flex h-6 w-6 items-center justify-center rounded-full border-2 transition-all"
                                    :class="selected.includes({{ $dm->id }}) ? 'bg-primary border-primary text-white' :
                                        'border-slate-200'">
                                    <template x-if="selected.includes({{ $dm->id }})">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>
                                </div>
                            </div>

                            {{-- Info User --}}
                            <div class="mt-2 min-w-0">
                                <h3 :class="selected.includes({{ $dm->id }}) ? 'text-primary' : 'adaptive-text-main'"
                                    class="truncate text-base font-black uppercase tracking-tighter transition-colors">
                                    {{ $dm->name }}
                                </h3>
                                <p class="mt-1 truncate text-[11px] font-bold uppercase tracking-widest adaptive-text-sub">
                                    {{ $dm->email }}
                                </p>
                            </div>

                            {{-- Decorative Bottom Line --}}
                            <div class="absolute bottom-0 left-0 h-1 transition-all"
                                :class="selected.includes({{ $dm->id }}) ? 'w-full bg-primary' :
                                    'w-0 bg-slate-200 group-hover:w-1/2'">
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full flex flex-col items-center justify-center rounded-[3rem] border-4 border-dashed border-slate-100 py-32">
                            <p class="text-xs font-black uppercase tracking-[0.4em] adaptive-text-sub">Data Source Empty</p>
                        </div>
                    @endforelse
                </div>

                {{-- ACTION FOOTER: Sticky di bawah jika list panjang --}}
                <div
                    class="sticky bottom-6 z-10 mt-12 flex items-center justify-between rounded-3xl border border-slate-200 bg-white/80 p-4 shadow-2xl backdrop-blur-md">
                    <div class="flex items-center gap-6 pl-4">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-widest adaptive-text-sub">Selected Count</p>
                            <p class="text-2xl font-black leading-none adaptive-text-main" x-text="selected.length"></p>
                        </div>
                        <div class="h-10 w-[2px] bg-slate-100"></div>
                        <p class="hidden max-w-[200px] text-[10px] font-bold uppercase text-slate-400 md:block">
                            Klik kartu user untuk memberikan hak akses penilaian pada sesi ini.
                        </p>
                    </div>

                    @if ($decisionSession->status === 'draft')
                        <div class="flex gap-4">
                            <button type="button" @click="selected = []"
                                class="rounded-2xl px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all hover:text-rose-600">
                                Clear All
                            </button>
                            <button type="submit"
                                class="rounded-2xl bg-slate-900 px-12 py-4 text-[10px] font-black uppercase tracking-widest text-white shadow-2xl shadow-slate-300 transition-all hover:bg-black active:scale-95">
                                Confirm Assignment
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

@endsection
