@php
    $status = $decisionSession->status ?? null;
@endphp

<nav class="flex flex-wrap gap-2 p-1.5 bg-gray-100/50 border border-gray-200 rounded-2xl mb-8 w-fit">
    {{-- Workspace --}}
    <a href="{{ route('dms.index', $decisionSession->id) }}"
        class="flex items-center px-5 py-2 rounded-xl text-sm font-bold transition-all duration-200
              {{ request()->routeIs('dms.index')
                  ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
                  : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }}">
        Workspace
    </a>

    {{-- Bobot Individu (Pairwise DM) --}}
    <a href="{{ route('decision-sessions.pairwise.index', $decisionSession->id) }}"
        class="flex items-center px-5 py-2 rounded-xl text-sm font-bold transition-all duration-200
              {{ request()->routeIs('decision-sessions.pairwise.*')
                  ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
                  : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }}">
        Bobot Individu
    </a>

    {{-- Bobot Kelompok --}}
    @if (in_array($status, ['criteria', 'alternatives', 'closed'], true))
        <a href="{{ route('dms.group-weights.index', $decisionSession->id) }}"
            class="flex items-center px-5 py-2 rounded-xl text-sm font-bold transition-all duration-200
                  {{ request()->routeIs('dms.group-weights.*')
                      ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
                      : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }}">
            Bobot Kelompok
        </a>
    @endif

    {{-- Penilaian Alternatif --}}
    @if (in_array($status, ['alternatives', 'closed'], true))
        <a href="{{ route('alternative-evaluations.index', $decisionSession->id) }}"
            class="flex items-center px-5 py-2 rounded-xl text-sm font-bold transition-all duration-200
                  {{ request()->routeIs('alternative-evaluations.*')
                      ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
                      : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }}">
            Penilaian Alternatif
        </a>
    @endif

    {{-- Ringkasan / Hasil --}}
    @if ($status === 'closed')
        <a href="{{ route('decision-sessions.result', $decisionSession->id) }}"
            class="flex items-center px-5 py-2 rounded-xl text-sm font-bold transition-all duration-200
                  {{ request()->routeIs('decision-sessions.result')
                      ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
                      : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }}">
            Ringkasan
        </a>
    @endif
</nav>
