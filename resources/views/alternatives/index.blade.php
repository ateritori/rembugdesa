@extends('layouts.dashboard')

@section('title', 'Alternatif')

@section('content')

  {{-- TAB NAVIGASI SESI --}}
  @include('decision-sessions.partials.nav')

  <div class="animate-in fade-in slide-in-from-bottom-4 w-full px-4 py-8 duration-700">

    @if ($errors->any())
      <div
        class="mb-6 rounded-2xl border-2 border-rose-500/20 bg-rose-50 px-6 py-4 text-sm font-black uppercase tracking-wide text-rose-600 shadow-lg">
        <div class="mb-2 flex items-center gap-3">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Input Error Terdeteksi:</span>
        </div>
        <ul class="list-inside list-disc opacity-80">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="w-full space-y-8">

      {{-- HEADER SECTION --}}
      <div class="flex flex-col justify-between gap-6 border-b-2 border-slate-100 px-2 pb-8 md:flex-row md:items-end">
        <div>
          <div class="mb-2 flex items-center gap-3">
            <span class="bg-primary h-2 w-12 rounded-full"></span>
            <p class="text-primary text-[11px] font-black uppercase tracking-[0.3em]">Target Entities</p>
          </div>
          <h1 class="text-4xl font-black uppercase tracking-tighter text-slate-800">Manajemen Alternatif</h1>
        </div>

        @if ($decisionSession->status !== 'draft')
          <div class="flex items-center gap-3 rounded-2xl bg-slate-900 px-6 py-3 text-white shadow-xl">
            <svg class="text-primary h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span class="text-xs font-black uppercase tracking-widest text-white">Akses Terkunci</span>
          </div>
        @endif
      </div>

      {{-- FORM TAMBAH: Besar & Solid --}}
      <div
        class="focus-within:border-primary/30 {{ $decisionSession->status !== 'draft' ? 'opacity-50 grayscale pointer-events-none' : '' }} relative overflow-hidden rounded-[2.5rem] border-2 border-slate-200 bg-white p-2 shadow-sm">
        <form method="POST" action="{{ route('alternatives.store', $decisionSession->id) }}"
          class="flex flex-col items-center gap-4 p-2 md:flex-row">
          @csrf
          <div class="group relative w-full flex-1">
            <div
              class="group-focus-within:text-primary absolute inset-y-0 left-0 flex items-center pl-6 text-slate-400 transition-colors">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
            <input type="text" name="name"
              placeholder="Masukkan nama alternatif baru (Contoh: Kandidat A, Vendor X, dsb)..."
              class="focus:ring-primary/5 w-full rounded-[1.5rem] border-none bg-slate-50 py-6 pl-16 pr-8 text-lg font-bold text-slate-700 transition-all placeholder:text-slate-300 focus:bg-white focus:ring-4"
              required>
          </div>

          <button type="submit"
            class="w-full rounded-[1.5rem] bg-slate-800 px-12 py-6 text-xs font-black uppercase tracking-[0.2em] text-white shadow-2xl shadow-slate-200 transition-all hover:bg-black active:scale-95 md:w-auto">
            + Tambah Alternatif
          </button>
        </form>
      </div>

      {{-- LIST DATA: Melebar Gagah --}}
      <div class="grid grid-cols-1 gap-4">
        @forelse ($alternatives as $a)
          <div x-data="{ open: false }" class="group">
            <div
              class="{{ !$a->is_active ? 'opacity-60 bg-slate-50' : 'bg-white' }} group-hover:border-primary/40 flex items-center justify-between rounded-[2rem] border-2 border-slate-200 p-6 transition-all group-hover:scale-[1.005] group-hover:shadow-2xl">

              <div class="flex min-w-0 items-center gap-6">
                {{-- KODE ALTERNATIF --}}
                <div
                  class="group-hover:bg-primary flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-slate-800 text-white shadow-xl shadow-slate-200 transition-all group-hover:rotate-3">
                  <span class="text-sm font-black uppercase italic">{{ $a->code }}</span>
                </div>

                <div class="truncate">
                  <h3
                    class="group-hover:text-primary {{ !$a->is_active ? 'line-through opacity-40' : '' }} text-xl font-black uppercase tracking-tight text-slate-800 transition-colors">
                    {{ $a->name }}
                  </h3>
                  @if (!$a->is_active)
                    <span class="mt-1 block text-[10px] font-black uppercase tracking-[0.2em] text-rose-500">
                      Status: Dinonaktifkan
                    </span>
                  @else
                    <span class="mt-1 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                      Status: Entitas Aktif
                    </span>
                  @endif
                </div>
              </div>

              {{-- ACTIONS --}}
              <div class="flex items-center gap-3">
                <button type="button" @click="open = !open"
                  class="hover:bg-primary/10 hover:text-primary {{ $decisionSession->status !== 'draft' ? 'hidden' : '' }} rounded-xl p-4 text-slate-400 transition-all"
                  title="Edit Nama">
                  <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>

                <form method="POST" action="{{ route('alternatives.toggle', $a->id) }}"
                  class="{{ $decisionSession->status !== 'draft' ? 'hidden' : '' }}">
                  @csrf @method('PATCH')
                  <button type="submit"
                    class="{{ $a->is_active ? 'text-slate-400 hover:bg-amber-50 hover:text-amber-600' : 'text-emerald-500 bg-emerald-50 hover:bg-emerald-100' }} rounded-xl p-4 transition-all"
                    title="{{ $a->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      @if ($a->is_active)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                      @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      @endif
                    </svg>
                  </button>
                </form>

                <form method="POST" action="{{ route('alternatives.destroy', $a->id) }}"
                  onsubmit="return confirm('Hapus alternatif ini?')"
                  class="{{ $decisionSession->status !== 'draft' ? 'hidden' : '' }}">
                  @csrf @method('DELETE')
                  <button type="submit"
                    class="rounded-xl p-4 text-slate-400 transition-all hover:bg-rose-50 hover:text-rose-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </form>

                @if ($decisionSession->status !== 'draft')
                  <div class="flex h-10 w-10 items-center justify-center text-slate-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                  </div>
                @endif
              </div>
            </div>

            {{-- Inline edit form: Besar --}}
            <form x-show="open" x-collapse @click.outside="open = false" method="POST"
              action="{{ route('alternatives.update', $a->id) }}"
              class="mt-4 flex flex-col gap-4 rounded-[2rem] border-2 border-dashed border-slate-200 bg-slate-50 p-8 shadow-inner md:flex-row">
              @csrf @method('PUT')

              <input type="text" name="name" value="{{ $a->name }}"
                class="focus:border-primary focus:ring-primary/5 flex-1 rounded-2xl border-2 border-white bg-white px-6 py-4 text-lg font-bold text-slate-700 shadow-sm outline-none focus:ring-4"
                required>

              <div class="flex gap-3">
                <button type="submit"
                  class="bg-primary shadow-primary/20 rounded-2xl px-10 py-4 text-xs font-black uppercase tracking-widest text-white shadow-xl transition-all hover:brightness-110 active:scale-95">
                  Update Nama
                </button>
                <button type="button" @click="open = false"
                  class="rounded-2xl border-2 border-slate-200 bg-white px-10 py-4 text-xs font-black uppercase tracking-widest text-slate-400 transition-all hover:bg-slate-100">
                  Batal
                </button>
              </div>
            </form>
          </div>
        @empty
          <div
            class="flex flex-col items-center justify-center rounded-[4rem] border-4 border-dashed border-slate-100 py-40 text-center">
            <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-slate-50 text-slate-200">
              <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 8-8-8" />
              </svg>
            </div>
            <p class="text-sm font-black uppercase tracking-[0.5em] text-slate-300">Belum Ada Alternatif Terdaftar</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>

@endsection
