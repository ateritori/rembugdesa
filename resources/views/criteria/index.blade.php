@extends('layouts.dashboard')

@section('title', 'Manajemen Kriteria')

@section('content')

  @include('decision-sessions.partials.nav')

  <div class="animate-in fade-in slide-in-from-bottom-4 w-full px-4 py-8 duration-700">

    {{-- CONTAINER UTAMA: Full Width --}}
    <div class="w-full space-y-6">

      {{-- HEADER SECTION: Full Width --}}
      <div class="flex flex-col justify-between gap-4 border-b-2 border-slate-100 px-2 pb-8 md:flex-row md:items-end">
        <div>
          <div class="mb-2 flex items-center gap-3">
            <span class="bg-primary h-2 w-10 rounded-full"></span>
            <p class="text-primary text-[10px] font-black uppercase tracking-[0.3em]">Parameter Engine</p>
          </div>
          <h1 class="text-3xl font-black uppercase tracking-tighter text-slate-800">Manajemen Kriteria</h1>
        </div>

        @if ($decisionSession->status !== 'draft')
          <div class="flex items-center gap-3 rounded-2xl bg-slate-900 px-6 py-3 text-white shadow-xl shadow-slate-200">
            <svg class="text-primary h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m0 0v2m0-2h2m-2 0H10m11-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-xs font-black uppercase tracking-widest text-white">Sesi Terkunci</span>
          </div>
        @endif
      </div>

      {{-- FORM TAMBAH: Full Width --}}
      <div
        class="focus-within:border-primary/30 {{ $decisionSession->status !== 'draft' ? 'opacity-50 grayscale pointer-events-none' : '' }} relative overflow-hidden rounded-[2rem] border-2 border-slate-200 bg-white p-2 shadow-sm transition-all">
        <form method="POST" action="{{ route('criteria.store', $decisionSession->id) }}"
          class="flex flex-col items-center gap-3 p-1 lg:flex-row">
          @csrf
          <div class="group relative w-full flex-1">
            <div
              class="group-focus-within:text-primary absolute inset-y-0 left-0 flex items-center pl-5 text-slate-400 transition-colors">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />
              </svg>
            </div>
            <input name="name" required
              class="focus:ring-primary/5 w-full rounded-2xl border-none bg-slate-50 py-4 pl-14 pr-6 text-sm font-bold text-slate-700 transition-all placeholder:font-medium placeholder:text-slate-300 focus:bg-white focus:ring-4"
              placeholder="Nama Kriteria Baru (Contoh: Pengalaman Kerja, Skor Tes, dsb)">
          </div>

          <div class="flex w-full gap-3 lg:w-auto">
            <select name="type" required
              class="focus:ring-primary/5 flex-1 rounded-2xl border-none bg-slate-50 px-6 py-4 text-sm font-black uppercase tracking-widest text-slate-500 transition-all focus:bg-white focus:ring-4 lg:w-56">
              <option value="" disabled selected>TIPE KRITERIA</option>
              <option value="benefit">BENEFIT</option>
              <option value="cost">COST</option>
            </select>

            <button
              class="flex-none rounded-2xl bg-slate-800 px-10 py-4 text-[11px] font-black uppercase tracking-widest text-white shadow-xl shadow-slate-200 transition-all hover:bg-black active:scale-95">
              + Tambah
            </button>
          </div>
        </form>
      </div>

      {{-- LIST DATA: Tetap 1 Kolom tapi Melebar Gagah --}}
      <div class="grid grid-cols-1 gap-5">
        @forelse ($criteria as $index => $c)
          @php
            $rule = $scoringRules->get($c->id);
            $locked = $decisionSession->status !== 'draft';
            $isBenefit = $c->type === 'benefit';
          @endphp

          <div x-data="{ openEdit: false, openScoring: false }"
            class="{{ !$c->is_active ? 'opacity-60 bg-slate-50' : 'bg-white' }} hover:border-primary/40 group overflow-hidden rounded-[1.5rem] border-2 border-slate-200 shadow-sm transition-all hover:scale-[1.005] hover:shadow-xl">

            <div class="flex flex-col justify-between gap-6 p-6 md:flex-row md:items-center">

              {{-- AREA TEKS & KODE --}}
              <div class="flex min-w-0 flex-1 items-center gap-6">
                {{-- KODE KRITERIA --}}
                <div
                  class="group-hover:bg-primary flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-slate-800 text-white shadow-xl shadow-slate-200 transition-all group-hover:-rotate-3">
                  <span class="text-sm font-black italic tracking-tighter">C{{ $index + 1 }}</span>
                </div>

                <div class="min-w-0 flex-1">
                  <div class="flex flex-wrap items-center gap-3">
                    <h3
                      class="group-hover:text-primary break-words text-xl font-black uppercase leading-tight text-slate-800 transition-colors">
                      {{ $c->name }}
                    </h3>
                    <span
                      class="{{ $isBenefit ? 'bg-emerald-100 text-emerald-600' : 'bg-orange-100 text-orange-600' }} rounded-lg px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em]">
                      {{ $c->type }}
                    </span>
                  </div>

                  <div class="mt-2 flex items-center gap-4">
                    @if (!$c->is_active)
                      <span
                        class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-rose-500">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-rose-500"></span> Non-Aktif
                      </span>
                    @else
                      <span
                        class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <span class="bg-primary h-2 w-2 rounded-full opacity-50"></span> Parameter Aktif
                      </span>
                    @endif
                  </div>
                </div>
              </div>

              {{-- AREA ACTION --}}
              <div
                class="flex w-full items-center justify-between gap-4 border-t border-slate-50 pt-6 md:w-auto md:justify-end md:border-0 md:pt-0">
                <div class="flex items-center gap-2">
                  <button @click="openEdit = !openEdit"
                    class="hover:bg-primary/10 hover:text-primary rounded-xl p-3 text-slate-400 transition-colors"
                    {{ !$c->is_active || $locked ? 'disabled' : '' }}>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 114 4L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>

                  <form method="POST" action="{{ route('criteria.toggle', $c->id) }}">
                    @csrf @method('PATCH')
                    <button
                      class="rounded-xl p-3 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-800"
                      {{ $locked ? 'disabled' : '' }}>
                      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                      </svg>
                    </button>
                  </form>

                  <form method="POST" action="{{ route('criteria.destroy', $c->id) }}"
                    onsubmit="return confirm('Hapus Kriteria?')">
                    @csrf @method('DELETE')
                    <button class="rounded-xl p-3 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600"
                      {{ !$c->is_active || $locked ? 'disabled' : '' }}>
                      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </form>
                </div>

                <div class="hidden h-10 w-[2px] bg-slate-100 md:block"></div>

                <button type="button" @click="openScoring = !openScoring"
                  class="rounded-2xl bg-slate-900 px-8 py-4 text-[10px] font-black uppercase tracking-widest text-white shadow-xl shadow-slate-200 transition-all hover:bg-black active:scale-95"
                  {{ !$c->is_active || $locked ? 'disabled' : '' }}>
                  Atur Scoring
                </button>
              </div>
            </div>

            {{-- EDIT FORM DROPDOWN: Melebar --}}
            <div x-show="openEdit" x-collapse class="border-t-2 border-slate-100 bg-slate-50/50 p-8">
              <form method="POST" action="{{ route('criteria.update', $c->id) }}"
                class="flex w-full flex-col gap-4 sm:flex-row">
                @csrf @method('PUT')
                <input name="name" value="{{ $c->name }}"
                  class="focus:border-primary focus:ring-primary/5 flex-1 rounded-2xl border-2 border-slate-200 px-6 py-4 text-sm font-bold text-slate-700 shadow-sm focus:ring-4">
                <select name="type"
                  class="focus:border-primary focus:ring-primary/5 rounded-2xl border-2 border-slate-200 px-6 py-4 text-sm font-black uppercase tracking-widest text-slate-500 shadow-sm focus:ring-4 sm:w-56">
                  <option value="benefit" {{ $c->type === 'benefit' ? 'selected' : '' }}>Benefit</option>
                  <option value="cost" {{ $c->type === 'cost' ? 'selected' : '' }}>Cost</option>
                </select>
                <button
                  class="bg-primary shadow-primary/20 rounded-2xl px-10 py-4 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:brightness-110">
                  Update Kriteria
                </button>
              </form>
            </div>

            {{-- SCORING AREA DROPDOWN: Full Width --}}
            <div x-show="openScoring" x-collapse class="border-t-2 border-slate-100 bg-white">
              <div class="p-6 md:p-12">
                @include('criteria.partials.scoring-rule', [
                    'c' => $c,
                    'rule' => $rule,
                    'decisionSession' => $decisionSession,
                ])
              </div>
            </div>

          </div>
        @empty
          <div
            class="col-span-full flex flex-col items-center justify-center rounded-[3rem] border-4 border-dashed border-slate-100 py-32">
            <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-slate-50 text-slate-200">
              <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <p class="text-xs font-black uppercase tracking-[0.4em] text-slate-300">Belum ada kriteria yang dikonfigurasi
            </p>
          </div>
        @endforelse
      </div>
    </div>
  </div>

@endsection
