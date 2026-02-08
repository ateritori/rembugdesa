{{-- Kontrol Sesi --}}
<div class="space-y-8 animate-in fade-in duration-500">

    {{-- Section: Dashboard Ringkasan --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $stats = [
                ['label' => 'Status Sesi', 'value' => $decisionSession->status, 'color' => 'text-blue-500'],
                ['label' => 'Kriteria Aktif', 'value' => $activeCriteriaCount, 'color' => 'text-purple-500'],
                ['label' => 'Alternatif Aktif', 'value' => $activeAlternativesCount, 'color' => 'text-indigo-500'],
                ['label' => 'Total DM', 'value' => $assignedDmCount, 'color' => 'text-emerald-500'],
            ];
        @endphp

        @foreach ($stats as $stat)
            <div
                class="adaptive-card p-4 shadow-sm hover:shadow-md transition-all border-l-4 {{ str_replace('text', 'border', $stat['color']) }}">
                <p class="text-[10px] uppercase tracking-wider adaptive-text-sub font-bold mb-1">{{ $stat['label'] }}</p>
                <p class="text-lg font-bold capitalize {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Section: Statistik Progres Penilaian --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @php
            // Data Riil Pairwise
            $dmPairwiseDone = $assignedDms->where('has_submitted', true)->count();
            $pairwisePercent = $assignedDmCount > 0 ? ($dmPairwiseDone / $assignedDmCount) * 100 : 0;

            // Data Dummy Alternatif (Silakan sesuaikan variabelnya nanti)
            $dmAltDone = 0;
            $altPercent = $assignedDmCount > 0 ? ($dmAltDone / $assignedDmCount) * 100 : 0;
        @endphp

        {{-- Card: Progres Pairwise --}}
        <div class="adaptive-card p-5 group relative overflow-hidden transition-all">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[10px] uppercase font-black text-amber-500 tracking-widest mb-1">Pairwise Kriteria</p>
                    <h4 class="text-2xl font-black adaptive-text-main">
                        {{ $dmPairwiseDone }} <span class="text-xs font-medium opacity-40">/ {{ $assignedDmCount }}
                            DM</span>
                    </h4>
                </div>
                <div class="p-2 bg-amber-500/10 rounded-lg text-amber-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
            </div>
            <div class="w-full bg-app/20 rounded-full h-1.5 mb-1">
                <div class="bg-amber-500 h-1.5 rounded-full transition-all duration-1000"
                    style="width: {{ $pairwisePercent }}%"></div>
            </div>
            <p class="text-[9px] adaptive-text-sub font-bold text-right uppercase tracking-tighter">
                {{ round($pairwisePercent) }}% Partisipasi</p>
        </div>

        {{-- Card: Progres Alternatif --}}
        <div class="adaptive-card p-5 group relative overflow-hidden transition-all">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[10px] uppercase font-black text-emerald-500 tracking-widest mb-1">Penilaian
                        Alternatif</p>
                    <h4 class="text-2xl font-black adaptive-text-main">
                        {{ $dmAltDone }} <span class="text-xs font-medium opacity-40">/ {{ $assignedDmCount }}
                            DM</span>
                    </h4>
                </div>
                <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
            <div class="w-full bg-app/20 rounded-full h-1.5 mb-1">
                <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-1000"
                    style="width: {{ $altPercent }}%"></div>
            </div>
            <p class="text-[9px] adaptive-text-sub font-bold text-right uppercase tracking-tighter">
                {{ round($altPercent) }}% Partisipasi</p>
        </div>
    </div>

    {{-- Section: Manajemen Status (Action Cards) --}}
    <div class="grid grid-cols-1 gap-6">

        {{-- 1. DRAFT -> ACTIVE --}}
        @if ($decisionSession->status === 'draft')
            <div
                class="adaptive-card p-6 border-blue-500/20 bg-gradient-to-r from-transparent to-blue-500/5 relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-center md:text-left">
                        <h3 class="text-base font-bold adaptive-text-main">Aktifkan Sesi Keputusan</h3>
                        <p class="text-sm adaptive-text-sub max-w-md mt-1">Sesi akan dibuka untuk umum. Pastikan seluruh
                            Kriteria, Alternatif, dan DM sudah dikonfigurasi dengan benar.</p>

                        @php $canActivate = $activeCriteriaCount >= 2 && $activeAlternativesCount >= 2 && $assignedDmCount >= 1; @endphp
                        @if (!$canActivate)
                            <div
                                class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-500/10 text-red-500 border border-red-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                <span class="text-[11px] font-bold uppercase">Persyaratan belum terpenuhi</span>
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('decision-sessions.activate', $decisionSession->id) }}"
                        onsubmit="return confirm('Buka sesi sekarang?')">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="group relative inline-flex items-center gap-3 px-8 py-4 bg-blue-600 text-white rounded-xl font-bold text-sm transition-all hover:scale-105 hover:shadow-xl hover:shadow-blue-500/20 disabled:opacity-40 disabled:cursor-not-allowed"
                            {{ $canActivate ? '' : 'disabled' }}>
                            <span>Aktifkan Sesi Sekarang</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- 2. ACTIVE -> ALTERNATIVES --}}
        @if ($decisionSession->status === 'active')
            @php
                $completedDmCount = $assignedDms->where('has_submitted', true)->count();
                $canLockCriteria = $assignedDmCount > 0 && $completedDmCount === $assignedDmCount;
            @endphp
            <div
                class="adaptive-card p-6 border-amber-500/20 bg-gradient-to-r from-transparent to-amber-500/5 relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-center md:text-left">
                        <h3 class="text-base font-bold adaptive-text-main">Kunci Penilaian Kriteria</h3>
                        <p class="text-sm adaptive-text-sub max-w-md mt-1">Penilaian kriteria akan dikunci dan sistem
                            akan membentuk bobot kelompok. Tindakan ini tidak dapat dibatalkan.</p>
                    </div>

                    <form method="POST" action="{{ route('decision-sessions.lock-criteria', $decisionSession->id) }}"
                        onsubmit="return confirm('Kunci penilaian kriteria?')">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="group relative inline-flex items-center gap-3 px-8 py-4 bg-amber-500 text-white rounded-xl font-bold text-sm transition-all hover:scale-105 hover:shadow-xl hover:shadow-amber-500/20">
                            <span>Kunci Penilaian Kriteria</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- 3. ALTERNATIVES -> CLOSED --}}
        @if ($decisionSession->status === 'alternatives')
            <div
                class="adaptive-card p-6 border-red-500/20 bg-gradient-to-r from-transparent to-red-500/5 relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-center md:text-left">
                        <h3 class="text-base font-bold adaptive-text-main">Finalisasi & Tutup Sesi</h3>
                        <p class="text-sm adaptive-text-sub max-w-md mt-1">Seluruh penilaian alternatif akan dikunci dan
                            sistem akan menghitung hasil akhir secara permanen.</p>
                    </div>

                    <form method="POST" action="{{ route('decision-sessions.close', $decisionSession->id) }}"
                        onsubmit="return confirm('Tutup sesi dan finalisasi hasil?')">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="px-8 py-4 bg-red-600 text-white rounded-xl font-bold text-sm transition-all hover:bg-red-700 hover:scale-105 hover:shadow-xl hover:shadow-red-500/20">
                            Tutup & Finalisasi Sesi
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- 4. CLOSED STATE --}}
        @if ($decisionSession->status === 'closed')
            <div class="adaptive-card p-8 text-center border-emerald-500/30 bg-emerald-500/5 shadow-inner">
                <div
                    class="w-16 h-16 bg-emerald-500/20 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04 Pelaksanaan sesi telah selesai.">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-black adaptive-text-main">Sesi Telah Selesai</h3>
                <p class="text-sm adaptive-text-sub mt-2 max-w-sm mx-auto">Seluruh data penilaian telah dikunci. Anda
                    bisa melihat laporan ranking akhir di menu Laporan.</p>
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('reports.index') }}"
                        class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg font-bold text-xs hover:bg-emerald-700 transition-colors">
                        Lihat Laporan Ranking
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
