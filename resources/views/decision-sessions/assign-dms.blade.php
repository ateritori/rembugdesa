@extends('layouts.dashboard')

@section('title', 'Penugasan DM')

@section('content')

    {{-- TAB NAVIGASI SESI --}}
    @include('decision-sessions.partials.nav')

    <div class="space-y-6 animate-in fade-in duration-500 pb-10" x-data="{
        {{-- Inisialisasi selected dengan data dari server --}}
        selected: {{ json_encode($assignedDmIds) }},
            toggleDm(id) {
                const index = this.selected.indexOf(id);
                if (index > -1) {
                    this.selected.splice(index, 1);
                    {{-- Uncheck --}}
                } else {
                    this.selected.push(id);
                    {{-- Check --}}
                }
            }
    }">

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div
                class="flex items-center gap-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 px-4 py-3 text-sm font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="adaptive-card p-6 shadow-sm">
            {{-- Header --}}
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-app pb-6">
                <div>
                    <h2 class="text-xl font-black text-app">Decision Makers</h2>
                    <p class="text-sm adaptive-text-sub">Klik kartu untuk memilih atau membatalkan pilihan pembuat
                        keputusan.</p>
                </div>

                @if ($decisionSession->status !== 'draft')
                    <span
                        class="px-3 py-1 bg-amber-500/10 text-amber-600 rounded-lg text-xs font-bold border border-amber-500/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Terkunci
                    </span>
                @endif
            </div>

            {{-- Form Multiple Selection --}}
            <form method="POST" action="{{ route('decision-sessions.assign-dms.store', $decisionSession->id) }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                    @forelse ($dms as $dm)
                        <div @click="{{ $decisionSession->status === 'draft' ? "toggleDm($dm->id)" : '' }}"
                            :class="selected.includes({{ $dm->id }}) ?
                                'border-primary shadow-sm bg-primary/5 ring-1 ring-primary/20' :
                                'hover:border-primary/30 bg-card border-app'"
                            class="group relative flex items-center gap-4 p-5 border rounded-2xl transition-all cursor-pointer select-none
                                {{ $decisionSession->status !== 'draft' ? 'opacity-70 grayscale-[0.5] pointer-events-none' : '' }}">

                            {{-- Input Hidden yang akan dikirim ke Server --}}
                            <input type="checkbox" name="dm_ids[]" value="{{ $dm->id }}" class="hidden"
                                :checked="selected.includes({{ $dm->id }})">

                            {{-- Custom UI Checkbox (Animated) --}}
                            <div :class="selected.includes({{ $dm->id }}) ? 'bg-primary border-primary text-white scale-110' :
                                'border-app group-hover:border-primary/50'"
                                class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all duration-300">
                                <template x-if="selected.includes({{ $dm->id }})">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </template>
                            </div>

                            <div class="flex flex-col">
                                <span :class="selected.includes({{ $dm->id }}) ? 'text-primary' : 'text-app'"
                                    class="text-sm font-black transition-colors">
                                    {{ $dm->name }}
                                </span>
                                <span class="text-[10px] font-bold opacity-40 uppercase tracking-tighter">
                                    {{ $dm->email }}
                                </span>
                            </div>

                            {{-- Indicator Badge --}}
                            <template x-if="selected.includes({{ $dm->id }})">
                                <div class="absolute -top-2 -right-1 animate-in zoom-in duration-300">
                                    <span
                                        class="bg-primary text-white text-[8px] font-black px-2 py-0.5 rounded-full shadow-lg uppercase tracking-widest">
                                        Aktif
                                    </span>
                                </div>
                            </template>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center opacity-40">
                            <p class="text-sm font-bold uppercase tracking-widest italic">Belum ada user DM.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Action --}}
                <div class="flex items-center justify-between pt-6 border-t border-app">
                    <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest">
                        Total terpilih: <span class="text-primary font-black" x-text="selected.length"></span>
                    </p>

                    @if ($decisionSession->status === 'draft')
                        <div class="flex gap-3">
                            <button type="button" @click="selected = []"
                                class="px-4 py-3 rounded-xl border border-app text-app text-[10px] font-black uppercase tracking-widest hover:bg-rose-500/10 hover:text-rose-600 transition-all">
                                Reset
                            </button>
                            <button type="submit"
                                class="px-8 py-3 rounded-xl bg-primary text-white text-xs font-black uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-primary/20">
                                Simpan Perubahan
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

@endsection
