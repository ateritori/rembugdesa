@php
    // Normalisasi input (partial dumb)
    $border = $border ?? 'border-slate-200';
    $badgeBg = $badgeBg ?? 'bg-slate-100';
    $badgeText = $badgeText ?? 'text-slate-600';

    $phase = $phase ?? '';
    $title = $title ?? '';
    $description = $description ?? '';

    // Status & URL dikirim dari luar; fallback aman bila tidak ada
    $isClosed = $isClosed ?? ($decisionSession->status ?? null) === 'closed';
    $analysisUrl =
        $analysisUrl ??
        (isset($decisionSession) ? route('control.index', [$decisionSession->id, 'tab' => 'analisis']) : '#');

    $currentStatus = $decisionSession->status ?? null;

    $actionConfig = [
        'draft' => [
            'phase' => '01',
            'label' => 'Preparation',
            'title' => 'Aktifkan Sesi',
            'ready_msg' => 'Parameter siap. Klik untuk membuka akses penilaian bagi DM.',
            'wait_msg' => 'Lengkapi minimal 2 Kriteria, 2 Alternatif, dan 1 DM untuk mengaktifkan.',
            'path' => 'control.partials.buttons.draft-activate',
            'check' => $canActivate ?? false,
        ],
        'configured' => [
            'phase' => '02',
            'label' => 'Configured',
            'title' => 'Buka Penilaian Alternatif',
            'ready_msg' => 'Pairwise selesai. Klik untuk memulai tahap evaluasi alternatif.',
            'wait_msg' =>
                'Menunggu ' .
                (($assignedDmCount ?? 0) - ($dmPairwiseDone ?? 0)) .
                ' DM lagi untuk menyelesaikan Pairwise.',
            'path' => 'control.partials.buttons.start-alternative',
            'check' => ($dmPairwiseDone ?? 0) >= ($assignedDmCount ?? 0),
        ],
        'scoring' => [
            'phase' => '03',
            'label' => 'Scoring',
            'title' => 'Finalisasi Sesi',
            'ready_msg' => 'Seluruh evaluasi selesai. Klik untuk mengunci dan hitung hasil akhir.',
            'wait_msg' =>
                'Menunggu ' .
                (($assignedDmCount ?? 0) - ($dmAltDone ?? 0)) .
                ' DM lagi menyelesaikan penilaian alternatif.',
            'path' => 'control.partials.buttons.close-scoring',
            'check' => ($dmAltDone ?? 0) >= ($assignedDmCount ?? 0),
        ],
        'closed' => [
            'phase' => '04',
            'label' => 'Closed',
            'title' => 'Keputusan Final',
            'ready_msg' => 'Sesi telah dikunci. Hasil akhir dapat dilihat di tab analisis.',
            'wait_msg' => '',
            'path' => 'control.partials.buttons.view-result',
            'check' => true,
        ],
    ];

    $act = $currentStatus ? $actionConfig[$currentStatus] ?? null : null;
@endphp
@if ($act)
    <div
        class="{{ $border }} flex flex-col items-center justify-between gap-8 rounded-2xl border-2 bg-white p-8 shadow-lg lg:flex-row lg:p-10">

        {{-- KIRI --}}
        <div class="flex-1 text-center lg:text-left">
            <span
                class="{{ $badgeBg }} {{ $badgeText }} rounded-full px-4 py-1.5 text-[10px] font-black uppercase tracking-widest">
                {{ $act['phase'] }}
            </span>

            <h3 class="mt-4 text-2xl font-black uppercase tracking-tight text-slate-800 lg:text-3xl">
                {{ $act['title'] }}
            </h3>

            <p class="mt-3 max-w-2xl text-sm font-medium leading-relaxed text-slate-500 lg:text-base">
                {{ $act['check'] ? $act['ready_msg'] : $act['wait_msg'] }}
            </p>

            {{-- Jika ada partial status tambahan (warning/info) --}}
            @if (isset($left_path))
                <div class="mt-4">
                    @include($left_path)
                </div>
            @endif
        </div>

        {{-- KANAN --}}
        @if ($isClosed)
            <a href="{{ $analysisUrl }}"
                class="inline-flex items-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow hover:bg-emerald-700">
                Lihat Hasil Akhir
            </a>
        @else
            <div
                class="w-full transition-all duration-300 @if ($act['check']) hover:scale-105 @else opacity-50 grayscale @endif md:w-auto">
                @include($act['path'], ['canActivate' => $act['check']])
            </div>
        @endif

    </div>
@endif
