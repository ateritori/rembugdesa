@php
    $status = $decisionSession->status ?? null;
    $currentTab = request('tab', 'workspace');
@endphp

<nav
    class="mb-8 flex w-fit flex-wrap gap-2 rounded-2xl border border-slate-200 bg-slate-100/50 p-1.5 font-sans shadow-sm">

    {{-- TAB 1: WORKSPACE --}}
    <a href="{{ route('dms.index', [$decisionSession->id, 'tab' => 'workspace']) }}"
        class="{{ $currentTab === 'workspace'
            ? 'bg-white text-primary shadow-sm ring-1 ring-black/5'
            : 'text-slate-500 hover:text-slate-700' }} flex items-center rounded-xl px-5 py-2 text-sm font-black transition-all">
        Workspace
    </a>

    {{-- TAB 2: PENILAIAN KRITERIA --}}
    <a href="{{ route('dms.index', [$decisionSession->id, 'tab' => 'penilaian-kriteria']) }}"
        class="{{ $currentTab === 'penilaian-kriteria'
            ? 'bg-white text-primary shadow-sm ring-1 ring-black/5'
            : 'text-slate-500 hover:text-slate-700' }} flex items-center rounded-xl px-5 py-2 text-sm font-black transition-all">
        @if ($status === 'scoring' || $status === 'closed')
            Bobot Kriteria
        @else
            {{ $dmHasCompleted ? 'Bobot Individu' : 'Penilaian Pairwise' }}
        @endif
    </a>

    {{-- TAB 3: PENILAIAN ALTERNATIF --}}
    @if (in_array($status, ['scoring', 'closed']))
        <a href="{{ route('dms.index', [$decisionSession->id, 'tab' => 'evaluasi-alternatif']) }}"
            class="{{ $currentTab === 'evaluasi-alternatif'
                ? 'bg-white text-primary shadow-sm ring-1 ring-black/5'
                : 'text-slate-500 hover:text-slate-700' }} flex items-center rounded-xl px-5 py-2 text-sm font-black transition-all">
            {{ $hasCompletedEvaluation ? 'Hasil Penilaian' : 'Penilaian Alternatif' }}
        </a>
    @endif

    {{-- TAB 4: HASIL AKHIR --}}
    @if ($status === 'closed')
        <a href="{{ route('dms.index', [$decisionSession->id, 'tab' => 'hasil-akhir']) }}"
            class="{{ $currentTab === 'hasil-akhir'
                ? 'bg-white text-primary shadow-sm ring-1 ring-black/5'
                : 'text-slate-500 hover:text-slate-700' }} flex items-center rounded-xl px-5 py-2 text-sm font-black transition-all">
            Hasil Akhir
        </a>
    @endif
</nav>
