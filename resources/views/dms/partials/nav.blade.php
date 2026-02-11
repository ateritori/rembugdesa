@php
  $status = $decisionSession->status ?? null;
@endphp

<nav class="mb-8 flex w-fit flex-wrap gap-2 rounded-2xl border border-gray-200 bg-gray-100/50 p-1.5">
  {{-- Workspace --}}
  <a href="{{ route('dms.index', $decisionSession->id) }}"
    class="{{ request()->routeIs('dms.index')
        ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
        : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }} flex items-center rounded-xl px-5 py-2 text-sm font-bold transition-all duration-200">
    Workspace
  </a>

  {{-- Bobot Individu (Pairwise DM) --}}
  <a href="{{ route('decision-sessions.pairwise.index', $decisionSession->id) }}"
    class="{{ request()->routeIs('decision-sessions.pairwise.*')
        ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
        : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }} flex items-center rounded-xl px-5 py-2 text-sm font-bold transition-all duration-200">
    Bobot Individu
  </a>

  {{-- Bobot Kelompok --}}
  @if (in_array($status, ['criteria', 'alternatives', 'closed'], true))
    <a href="{{ route('dms.group-weights.index', $decisionSession->id) }}"
      class="{{ request()->routeIs('dms.group-weights.*')
          ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
          : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }} flex items-center rounded-xl px-5 py-2 text-sm font-bold transition-all duration-200">
      Bobot Kelompok
    </a>
  @endif

  {{-- Hasil Penilaian (SMART – DM × Bobot Kelompok) --}}
  @if (auth()->check() && auth()->user()->hasRole('dm'))
    <a href="{{ route('decision-sessions.summary', $decisionSession->id) }}"
      class="{{ request()->routeIs('decision-sessions.summary')
          ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
          : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }} flex items-center rounded-xl px-5 py-2 text-sm font-bold transition-all duration-200">
      Hasil Penilaian
    </a>
  @endif
</nav>
