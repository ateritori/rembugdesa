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
          // Kita ganti warna BIRU/PURPLE statis menjadi warna PRIMARY agar selaras preset
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
              // Menggunakan warna Primary Preset untuk fase input
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
                      'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
              ],
              default => [
                  'label' => $s->status,
                  'css' => 'text-slate-500 bg-slate-500/10 border-slate-500/20',
                  'icon' =>
                      'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
              ],
          };
        @endphp

        <div class="adaptive-card hover:border-primary/40 group p-5 transition-all duration-300">
          <div class="flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">

            <div class="flex w-full items-start gap-5">
              {{-- Icon Box Dinamis --}}
              <div
                class="bg-app border-app {{ $statusConfig['css'] }} flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusConfig['icon'] }}" />
                </svg>
              </div>

              <div class="min-w-0 flex-1">
                <div class="mb-1.5 flex items-center gap-3">
                  <h3 class="adaptive-text-main group-hover:text-primary truncate text-lg font-black transition-colors">
                    {{ $s->name }}
                  </h3>
                  {{-- Tag Status Vibrant --}}
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
                  <span class="adaptive-text-sub flex items-center gap-1 text-[11px] font-bold opacity-60">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $s->created_at->translatedFormat('d M Y') }}
                  </span>
                </div>
              </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex w-full items-center justify-end gap-3 md:w-auto">
              <a href="{{ $s->status === 'draft' ? route('criteria.index', $s->id) : route('control.index', $s->id) }}"
                class="bg-primary shadow-primary/20 inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-sm transition-all hover:brightness-110">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
                <span>Kelola</span>
              </a>

              @if ($s->status === 'draft')
                <div class="ml-2 flex items-center gap-1">
                  <a href="{{ route('decision-sessions.edit', $s->id) }}"
                    class="adaptive-text-sub hover:text-primary hover:bg-primary/10 rounded-lg p-2 transition-all">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </a>

                  <form method="POST" action="{{ route('decision-sessions.destroy', $s->id) }}"
                    onsubmit="return confirm('Hapus sesi ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                      class="adaptive-text-sub rounded-lg p-2 transition-all hover:bg-rose-500/10 hover:text-rose-500">
                      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </form>
                </div>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="adaptive-card flex flex-col items-center justify-center border-2 border-dashed bg-transparent py-24">
          <p class="adaptive-text-sub text-[10px] font-black uppercase tracking-[0.3em] opacity-30">Belum Ada Sesi
            Tersedia</p>
        </div>
      @endforelse
    </div>
  </div>
@endsection
