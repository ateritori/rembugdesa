@php
    $decisionSession = $decisionSession ?? null;
    $status = $decisionSession?->status;
    $currentTab = request('tab', 'workspace');
    $dmHasCompleted = $dmHasCompleted ?? false;
    $hasCompletedEvaluation = $hasCompletedEvaluation ?? false;

    // Helper class untuk logic active/inactive
    $activeClass = 'bg-white text-primary shadow-sm ring-1 ring-black/5';
    $inactiveClass = 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50';
    $baseClass =
        'flex items-center justify-center rounded-xl px-5 py-2 text-sm font-black transition-all whitespace-nowrap';
@endphp

<style>
    /* Utility untuk sembunyikan scrollbar tapi tetap bisa di-scroll */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<nav
    class="mb-8 flex w-full sm:w-fit items-center gap-2 overflow-x-auto no-scrollbar rounded-2xl border border-slate-200 bg-slate-100/50 p-1.5 font-sans shadow-sm">

    {{-- TAB 1: WORKSPACE --}}
    <a href="{{ route('dms.index', [$decisionSession->id, 'tab' => 'workspace']) }}"
        class="{{ $currentTab === 'workspace' ? $activeClass : $inactiveClass }} {{ $baseClass }}">
        Workspace
    </a>

    {{-- TAB 2: PENILAIAN KRITERIA --}}
    <a href="{{ route('dms.index', [$decisionSession->id, 'tab' => 'penilaian-kriteria']) }}"
        class="{{ $currentTab === 'penilaian-kriteria' ? $activeClass : $inactiveClass }} {{ $baseClass }}">
        @if ($status === 'scoring' || $status === 'closed')
            Bobot Kriteria
        @else
            {{ $dmHasCompleted ? 'Bobot Individu' : 'Penilaian Pairwise' }}
        @endif
    </a>

    {{-- TAB 3: PENILAIAN ALTERNATIF (Kondisional Status) --}}
    @if (in_array($status, ['scoring', 'closed']))
        <a href="{{ route('dms.index', [$decisionSession->id, 'tab' => 'evaluasi-alternatif']) }}"
            class="{{ $currentTab === 'evaluasi-alternatif' ? $activeClass : $inactiveClass }} {{ $baseClass }}">
            {{ $hasCompletedEvaluation ? 'Hasil Penilaian' : 'Penilaian Alternatif' }}
        </a>
    @endif

    {{-- TAB 4: HASIL AKHIR (Kondisional Selesai) --}}
    @if ($status === 'closed')
        <a href="{{ route('dms.index', [$decisionSession->id, 'tab' => 'hasil-akhir']) }}"
            class="{{ $currentTab === 'hasil-akhir' ? $activeClass : $inactiveClass }} {{ $baseClass }}">
            Hasil Akhir
        </a>
    @endif

</nav>
