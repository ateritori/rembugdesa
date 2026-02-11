@extends('layouts.dashboard')

@section('title', 'Penilaian Alternatif')

@section('content')

  @include('decision-sessions.partials.nav')

  {{-- NOTIFICATION --}}
  @if (session('success'))
    <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
      <p class="text-sm font-bold">{{ session('success') }}</p>
    </div>
  @endif

  @if ($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
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

    <div class="space-y-6">

      {{-- HEADER --}}
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <h2 class="text-app text-2xl font-black">Penilaian Alternatif</h2>
        @if ($evaluations->isNotEmpty())
          <span class="text-xs font-bold uppercase text-yellow-600">
            Mode Edit Penilaian
          </span>
        @else
          <span class="text-xs font-bold uppercase text-green-600">
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

        <div class="space-y-4 rounded-2xl border border-gray-200 bg-white p-5">

          {{-- KRITERIA HEADER --}}
          <div class="border-b pb-2">
            <h3 class="text-sm font-black uppercase tracking-widest text-gray-700">
              {{ $c->name }}
            </h3>
          </div>

          {{-- TABEL ALTERNATIF --}}
          <div class="overflow-x-auto">
            <table class="w-full overflow-hidden rounded-lg border border-gray-200 text-sm">
              <thead class="bg-gray-50 text-gray-600">
                <tr>
                  <th class="px-4 py-3 text-left font-bold">Alternatif</th>
                  <th class="px-4 py-3 text-left font-bold">Penilaian</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                @foreach ($alternatives as $a)
                  @php
                    $evaluation = $evaluations[$a->id][$c->id] ?? null;
                  @endphp
                  <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold text-gray-700">
                      {{ $a->name }}
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex flex-wrap gap-4">
                        @foreach ($semantics as $value => $label)
                          <label class="hover:text-app flex cursor-pointer items-center gap-2 text-xs text-gray-600">
                            <input type="radio" name="evaluations[{{ $a->id }}][{{ $c->id }}]"
                              value="{{ $value }}" required @checked(optional($evaluation)->raw_value == $value)
                              class="text-app h-4 w-4 border-gray-300">
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
      <div class="flex justify-end pt-4">
        <button type="submit"
          class="rounded-xl bg-green-600 px-10 py-3 font-black uppercase text-white shadow-lg hover:bg-green-700">
          Simpan Penilaian
        </button>
      </div>

    </div>
  </form>

@endsection
