@extends('layouts.dashboard')

@section('title', 'Penilaian Alternatif')

@section('content')

  {{-- 1. PERBAIKAN: Path navigasi disamakan dengan file index utama --}}
  {{-- Kita kirim variabel yang dibutuhkan agar partial nav tidak error --}}
  @include('dms.partials.nav', [
      'activeTab' => 'evaluasi-alternatif',
      'decisionSession' => $decisionSession,
      'dmHasCompleted' => $dmHasCompleted ?? true,
  ])

  {{-- NOTIFICATION --}}
  @if (session('success'))
    <div class="animate-in fade-in mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
      <p class="text-sm font-bold">{{ session('success') }}</p>
    </div>
  @endif

  @if ($errors->any())
    <div class="animate-in shake-50 mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      <p class="mb-1 text-sm font-bold">Penilaian belum lengkap:</p>
      <ul class="list-inside list-disc space-y-1 text-xs">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('alternative-evaluations.store', $decisionSession->id) }}">
    @csrf

    <div class="space-y-8">

      {{-- HEADER --}}
      <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
          <h2 class="text-app text-2xl font-black italic">Penilaian Alternatif</h2>
          <p class="text-xs text-slate-500">Berikan nilai pada setiap alternatif berdasarkan kriteria yang ada.</p>
        </div>

        @if ($evaluations->isNotEmpty())
          <span
            class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-[10px] font-black uppercase text-amber-600 ring-1 ring-amber-600/20">
            <span class="mr-1.5 h-1.5 w-1.5 animate-pulse rounded-full bg-amber-600"></span>
            Mode Edit Penilaian
          </span>
        @else
          <span
            class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase text-emerald-600 ring-1 ring-emerald-600/20">
            Penilaian Baru
          </span>
        @endif
      </div>

      {{-- LOOP PER KRITERIA --}}
      @foreach ($criteria as $c)
        @php
          $semanticsParam = $c->scoringRule->getParameter('scale_semantics');
          $semantics = is_string($semanticsParam)
              ? json_decode($semanticsParam, true)
              : (is_array($semanticsParam)
                  ? $semanticsParam
                  : []);
        @endphp

        <div class="adaptive-card overflow-hidden border border-gray-200 bg-white">

          {{-- KRITERIA HEADER --}}
          <div class="border-b bg-slate-50/50 px-6 py-4">
            <h3 class="text-primary text-xs font-black uppercase tracking-[0.2em]">
              Kriteria: {{ $c->name }}
            </h3>
          </div>

          {{-- TABEL ALTERNATIF --}}
          <div class="overflow-x-auto p-2">
            <table class="w-full text-sm">
              <thead>
                <tr class="text-[10px] uppercase tracking-widest text-slate-400">
                  <th class="px-4 py-3 text-left font-black">Alternatif</th>
                  <th class="px-4 py-3 text-left font-black">Skala Penilaian</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                @foreach ($alternatives as $a)
                  @php
                    $evaluation = $evaluations[$a->id][$c->id] ?? null;
                  @endphp
                  <tr class="group transition-colors hover:bg-slate-50/80">
                    <td class="px-4 py-4 font-bold text-slate-700">
                      {{ $a->name }}
                    </td>
                    <td class="px-4 py-4">
                      <div class="flex flex-wrap gap-x-6 gap-y-3">
                        @foreach ($semantics as $value => $label)
                          <label
                            class="group/radio hover:text-primary flex cursor-pointer items-center gap-2 text-xs font-medium text-slate-600 transition-colors">
                            <input type="radio" name="evaluations[{{ $a->id }}][{{ $c->id }}]"
                              value="{{ $value }}" required @checked(optional($evaluation)->raw_value == $value)
                              class="text-primary focus:ring-primary/20 h-4 w-4 border-slate-300">
                            {{ $label }}
                          </label>
                        @endforeach
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endforeach

      {{-- SUBMIT --}}
      <div class="flex items-center justify-between border-t pb-10 pt-8">
        <p class="text-xs font-medium text-slate-400">Pastikan semua kolom radio sudah terisi sebelum menyimpan.</p>
        <button type="submit"
          class="bg-primary shadow-primary/20 inline-flex items-center rounded-xl px-10 py-4 text-xs font-black uppercase tracking-widest text-white shadow-xl transition-all hover:scale-[1.02] active:scale-95">
          <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
          Simpan Penilaian
        </button>
      </div>

    </div>
  </form>

@endsection
