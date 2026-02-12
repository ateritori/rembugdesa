@extends('layouts.dashboard')

@section('title', 'Kriteria')

@section('content')

  @include('decision-sessions.partials.nav')

  <div class="w-full space-y-6 px-4 py-6">

    {{-- NOTIFIKASI MANUAL DIHAPUS - SEKARANG MENGGUNAKAN TOAST DARI LAYOUT --}}

    <div class="w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

      <div class="border-b border-slate-100 bg-slate-50/30 p-6">
        <h2 class="text-xl font-bold tracking-tight text-slate-800">Manajemen Kriteria</h2>
        <p class="mt-1 text-xs font-semibold uppercase tracking-widest text-slate-500">Pengaturan Parameter Penilaian</p>
      </div>

      <div class="p-4 sm:p-6">
        {{-- FORM TAMBAH --}}
        <form method="POST" action="{{ route('criteria.store', $decisionSession->id) }}"
          class="{{ $decisionSession->status !== 'draft' ? 'opacity-50 pointer-events-none' : '' }} mb-8">
          @csrf
          <div
            class="flex flex-col items-stretch gap-3 rounded-xl border border-slate-200 bg-slate-100/50 p-4 xl:flex-row">
            <input name="name" required
              class="flex-1 rounded-lg border-slate-300 bg-white px-4 py-2.5 text-sm transition-all focus:ring-2 focus:ring-indigo-500"
              placeholder="Nama Kriteria (Contoh: Harga Produk, Kecepatan Pengiriman)">

            <select name="type" required
              class="w-full rounded-lg border-slate-300 bg-white px-4 py-2.5 text-sm font-bold transition-all focus:ring-2 focus:ring-indigo-500 xl:w-48">
              <option value="" disabled selected>Pilih Tipe</option>
              <option value="benefit">BENEFIT</option>
              <option value="cost">COST</option>
            </select>

            <button
              class="rounded-lg bg-indigo-600 px-8 py-2.5 text-sm font-bold text-white shadow-md transition-all hover:bg-indigo-700 active:scale-95">
              + Tambah
            </button>
          </div>
        </form>

        {{-- LIST DATA --}}
        <div class="grid grid-cols-1 gap-4">
          @foreach ($criteria as $c)
            @php
              $rule = $scoringRules->get($c->id);
              $locked = $decisionSession->status !== 'draft';
              $isBenefit = $c->type === 'benefit';
            @endphp

            <div x-data="{ openEdit: false, openScoring: false }"
              class="{{ !$c->is_active ? 'opacity-60 bg-slate-50' : 'bg-white' }} overflow-hidden rounded-2xl border border-slate-200 shadow-sm transition-all hover:border-indigo-300">

              {{-- HEADER ITEM --}}
              <div class="flex flex-col justify-between gap-4 p-4 md:flex-row md:items-center">

                {{-- AREA TEKS --}}
                <div class="flex min-w-0 flex-1 items-start gap-4">
                  <div
                    class="{{ $isBenefit ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700' }} flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-sm font-black">
                    {{ $isBenefit ? 'B' : 'C' }}
                  </div>
                  <div class="min-w-0">
                    <h3 class="break-words text-lg font-bold uppercase leading-tight text-slate-800">{{ $c->name }}
                    </h3>
                    <div class="mt-1 flex flex-wrap gap-2">
                      <span
                        class="{{ $isBenefit ? 'text-emerald-600' : 'text-orange-600' }} rounded bg-slate-100 px-2 py-0.5 text-[10px] font-black tracking-widest">
                        TIPE: {{ strtoupper($c->type) }}
                      </span>
                      @if (!$c->is_active)
                        <span
                          class="rounded bg-rose-100 px-2 py-0.5 text-[10px] font-black tracking-widest text-rose-600">NON-AKTIF</span>
                      @endif
                    </div>
                  </div>
                </div>

                {{-- AREA ACTION --}}
                <div
                  class="flex w-full items-center justify-between gap-2 border-t border-slate-100 pt-3 md:w-auto md:justify-end md:border-0 md:pt-0">
                  <div class="flex items-center gap-1">
                    <button @click="openEdit = !openEdit"
                      class="rounded-lg p-2.5 text-slate-500 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                      {{ !$c->is_active || $locked ? 'disabled' : '' }}>
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 114 4L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>

                    <form method="POST" action="{{ route('criteria.toggle', $c->id) }}">
                      @csrf @method('PATCH')
                      <button class="rounded-lg p-2.5 text-slate-500 transition-colors hover:bg-slate-100"
                        {{ $locked ? 'disabled' : '' }}>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                      </button>
                    </form>

                    <form method="POST" action="{{ route('criteria.destroy', $c->id) }}"
                      onsubmit="return confirm('Hapus?')">
                      @csrf @method('DELETE')
                      <button
                        class="rounded-lg p-2.5 text-slate-500 transition-colors hover:bg-rose-50 hover:text-rose-600"
                        {{ !$c->is_active || $locked ? 'disabled' : '' }}>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </form>
                  </div>

                  <button type="button" @click="openScoring = !openScoring"
                    class="rounded-xl bg-slate-800 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-black active:scale-95"
                    {{ !$c->is_active || $locked ? 'disabled' : '' }}>
                    Atur Nilai
                  </button>
                </div>
              </div>

              {{-- EDIT FORM --}}
              <div x-show="openEdit" x-collapse class="border-t border-slate-200 bg-slate-50 p-4">
                <form method="POST" action="{{ route('criteria.update', $c->id) }}"
                  class="flex flex-col gap-3 sm:flex-row">
                  @csrf @method('PUT')
                  <input name="name" value="{{ $c->name }}"
                    class="flex-1 rounded-lg border-slate-300 px-4 py-2 text-sm shadow-sm">
                  <select name="type"
                    class="rounded-lg border-slate-300 px-4 py-2 text-sm font-bold shadow-sm sm:w-32">
                    <option value="benefit" {{ $c->type === 'benefit' ? 'selected' : '' }}>Benefit</option>
                    <option value="cost" {{ $c->type === 'cost' ? 'selected' : '' }}>Cost</option>
                  </select>
                  <button
                    class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-bold text-white shadow-md hover:bg-indigo-700">Simpan</button>
                </form>
              </div>

              {{-- SCORING AREA --}}
              <div x-show="openScoring" x-collapse class="border-t border-slate-200 bg-slate-50/80">
                <div class="overflow-x-auto p-4 sm:p-8">
                  @include('criteria.partials.scoring-rule', [
                      'c' => $c,
                      'rule' => $rule,
                      'decisionSession' => $decisionSession,
                  ])
                </div>
              </div>

            </div>
          @endforeach
        </div>

      </div>
    </div>
  </div>

@endsection
