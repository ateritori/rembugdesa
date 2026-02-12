@extends('layouts.dashboard')

@section('title', 'Buat Sesi Keputusan')

@section('content')
  <div class="animate-in fade-in slide-in-from-bottom-4 w-full px-4 py-6 duration-700">

    <div class="mx-auto max-w-5xl">
      {{-- HEADER --}}
      <div class="mb-8 flex items-center gap-4">
        <div
          class="bg-primary shadow-primary/20 flex h-12 w-12 items-center justify-center rounded-xl text-white shadow-lg">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
        </div>
        <div>
          <h1 class="text-xl font-black uppercase tracking-tight text-slate-800">Inisialisasi Sesi</h1>
          <p class="text-primary text-[10px] font-black uppercase tracking-[0.2em] opacity-80">Decision Support Workspace
          </p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

        {{-- KOLOM FORM (7/12) --}}
        <div class="lg:col-span-7">
          <div
            class="hover:border-primary/30 relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all sm:p-8">

            {{-- Accent Line Dinamis --}}
            <div class="bg-primary absolute left-0 top-0 h-1 w-full opacity-20"></div>

            <form method="POST" action="{{ route('decision-sessions.store') }}" class="space-y-6">
              @csrf

              <div class="space-y-2">
                <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-slate-400">
                  Judul Sesi Keputusan
                </label>
                <div class="group relative">
                  <div
                    class="group-focus-within:text-primary absolute inset-y-0 left-0 flex items-center pl-4 text-slate-300 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 114 4L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </div>
                  <input type="text" name="name"
                    class="focus:border-primary focus:ring-primary/5 w-full rounded-xl border-slate-200 bg-slate-50/50 py-3.5 pl-11 pr-4 text-sm font-bold text-slate-700 transition-all placeholder:font-medium placeholder:text-slate-300 focus:bg-white focus:ring-4"
                    placeholder="Contoh: Pemilihan Vendor IT 2026" required autofocus>
                </div>
              </div>

              <div class="space-y-2">
                <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-slate-400">
                  Tahun
                </label>
                <div class="group relative max-w-[200px]">
                  <div
                    class="group-focus-within:text-primary absolute inset-y-0 left-0 flex items-center pl-4 text-slate-300 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <input type="number" name="year" value="{{ date('Y') }}"
                    class="focus:border-primary focus:ring-primary/5 w-full rounded-xl border-slate-200 bg-slate-50/50 py-3.5 pl-11 pr-4 text-sm font-bold text-slate-700 transition-all focus:bg-white focus:ring-4"
                    required>
                </div>
              </div>

              <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                  class="rounded-xl bg-slate-800 px-8 py-3.5 text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-slate-200 transition-all hover:bg-black active:scale-95">
                  Simpan Sesi
                </button>

                <a href="{{ route('decision-sessions.index') }}"
                  class="rounded-xl border border-slate-200 bg-white px-8 py-3.5 text-[11px] font-black uppercase tracking-widest text-slate-400 transition-all hover:bg-slate-50 hover:text-slate-600">
                  Batal
                </a>
              </div>
            </form>
          </div>
        </div>

        {{-- KOLOM INFO (5/12) --}}
        <div class="flex flex-col gap-4 lg:col-span-5">
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-800">
              <span class="bg-primary h-1 w-3 rounded-full"></span>
              Petunjuk Cepat
            </h3>
            <div class="space-y-4">
              <div class="flex gap-3">
                <div class="text-primary text-xs font-black">01.</div>
                <p class="text-xs font-bold leading-relaxed text-slate-500">Sesi ini akan menjadi wadah utama untuk
                  seluruh kriteria dan alternatif.</p>
              </div>
              <div class="flex gap-3">
                <div class="text-primary text-xs font-black">02.</div>
                <p class="text-xs font-bold leading-relaxed text-slate-500">Gunakan nama yang deskriptif untuk memudahkan
                  pencarian di kemudian hari.</p>
              </div>
            </div>
          </div>

          <div class="group relative overflow-hidden rounded-2xl bg-slate-900 p-6 text-white shadow-xl">
            {{-- Decorative Icon Background --}}
            <svg
              class="absolute -bottom-4 -right-4 h-24 w-24 text-white opacity-5 transition-transform group-hover:scale-110"
              fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>

            <p class="text-primary mb-1 text-[10px] font-black uppercase tracking-widest">Status Default</p>
            <p class="text-xs font-bold leading-relaxed opacity-80">Sesi baru akan otomatis diset sebagai <span
                class="decoration-primary text-white underline underline-offset-4">Draft</span> hingga parameter penilaian
              lengkap.</p>
          </div>
        </div>

      </div>
    </div>
  </div>
@endsection
