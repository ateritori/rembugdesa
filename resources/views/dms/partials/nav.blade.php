@php
    $status = $decisionSession->status ?? null;
@endphp

<nav class="mb-8 flex w-fit flex-wrap gap-2 rounded-2xl border border-gray-200 bg-gray-100/50 p-1.5">
    {{-- 1. Workspace: selalu muncul --}}
    <a href="{{ route('dms.index', $decisionSession->id) }}"
        class="{{ request()->routeIs('dms.index')
            ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
            : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }} flex items-center rounded-xl px-5 py-2 text-sm font-bold transition-all duration-200">
        Workspace
    </a>

    {{-- 2. Perbandingan Kriteria: hanya jika status = configured --}}
    @if ($status === 'configured')
        <a href="{{ route('decision-sessions.pairwise.index', $decisionSession->id) }}"
            class="{{ ($activeTab ?? null) === 'pairwise'
                ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
                : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }} flex items-center rounded-xl px-5 py-2 text-sm font-bold transition-all duration-200">
            Perbandingan Kriteria
        </a>
    @endif

    {{-- 3. Bobot Individu: muncul jika DM sudah menyelesaikan pairwise (read only) --}}
    @if (($hasCompletedPairwise ?? false) === true)
        <a href="{{ route('dms.weights.index', $decisionSession->id) }}"
            class="{{ request()->routeIs('dms.weights.*')
                ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
                : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }} flex items-center rounded-xl px-5 py-2 text-sm font-bold transition-all duration-200">
            Bobot Individu
        </a>
    @endif

    {{-- 4. Bobot Kelompok: hanya jika status = scoring --}}
    @if ($status === 'scoring')
        <a href="{{ route('dms.group-weights.index', $decisionSession->id) }}"
            class="{{ request()->routeIs('dms.group-weights.*')
                ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
                : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }} flex items-center rounded-xl px-5 py-2 text-sm font-bold transition-all duration-200">
            Bobot Kelompok
        </a>
    @endif

    {{-- 5. Penilaian Alternatif: hanya jika status = scoring --}}
    @if ($status === 'scoring')
        <a href="{{ route('alternative-evaluations.index', $decisionSession->id) }}"
            class="{{ request()->routeIs('alternative-evaluations.*')
                ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
                : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }} flex items-center rounded-xl px-5 py-2 text-sm font-bold transition-all duration-200">
            Penilaian Alternatif
        </a>
    @endif

    {{-- 6. Ranking Alternatif: muncul jika penilaian alternatif DM selesai --}}
    @if (($decisionSession->dmEvaluationFinished ?? false) === true)
        <a href="{{ route('decision-sessions.summary', $decisionSession->id) }}"
            class="text-gray-500 hover:text-gray-700 hover:bg-white/50 flex items-center rounded-xl px-5 py-2 text-sm font-bold transition-all duration-200">
            Ranking Alternatif
        </a>
    @endif

    {{-- 7. Hasil: hanya jika status = closed --}}
    @if ($status === 'closed')
        <a href="{{ route('decision-sessions.summary', $decisionSession->id) }}"
            class="{{ request()->routeIs('decision-sessions.summary')
                ? 'bg-white text-app shadow-sm ring-1 ring-black/5'
                : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }} flex items-center rounded-xl px-5 py-2 text-sm font-bold transition-all duration-200">
            Hasil
        </a>
    @endif
</nav>
