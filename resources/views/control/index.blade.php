@extends('layouts.dashboard')

@section('title', 'Kontrol Sesi')
@section('content')

    {{-- TAB NAVIGASI SESI --}}
    @include('decision-sessions.partials.nav')

    {{-- Kontrol Sesi --}}
    <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500 pb-10">

        {{-- SECTION: DASHBOARD RINGKASAN (Stat Cards) --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $stats = [
                    [
                        'label' => 'Status Sesi',
                        'value' => $decisionSession->status,
                        'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        'color' => 'blue',
                    ],
                    [
                        'label' => 'Kriteria Aktif',
                        'value' => $activeCriteriaCount,
                        'icon' =>
                            'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                        'color' => 'purple',
                    ],
                    [
                        'label' => 'Alternatif Aktif',
                        'value' => $activeAlternativesCount,
                        'icon' =>
                            'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                        'color' => 'indigo',
                    ],
                    [
                        'label' => 'Total DM',
                        'value' => $assignedDmCount,
                        'icon' =>
                            'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                        'color' => 'emerald',
                    ],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="adaptive-card p-5 group hover:scale-[1.02] transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-{{ $stat['color'] }}-500/10 text-{{ $stat['color'] }}-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $stat['icon'] }}" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] uppercase tracking-widest adaptive-text-sub font-black mb-0.5">
                                {{ $stat['label'] }}</p>
                            <p
                                class="text-xl font-black capitalize text-{{ $stat['color'] }}-600 tracking-tight leading-none">
                                {{ $stat['value'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- SECTION: STATISTIK PROGRES PENILAIAN --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $pairwisePercent = $assignedDmCount > 0 ? ($dmPairwiseDone / $assignedDmCount) * 100 : 0;

                $dmAltDone = 0; // TODO: hitung dari controller
                $altPercent = $assignedDmCount > 0 ? ($dmAltDone / $assignedDmCount) * 100 : 0;
            @endphp

            {{-- Progres Pairwise --}}
            <div class="adaptive-card p-6 border-t-4 border-t-amber-500">
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <h4 class="text-lg font-black adaptive-text-main leading-tight">Progres Pairwise Kriteria</h4>
                        <p class="text-xs adaptive-text-sub font-medium mt-1">Status pengumpulan bobot dari Decision Maker.
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-amber-500">{{ round($pairwisePercent) }}%</span>
                    </div>
                </div>
                <div class="relative w-full bg-app/20 rounded-full h-3 mb-2 overflow-hidden shadow-inner">
                    <div class="absolute top-0 left-0 h-full bg-amber-500 rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(245,158,11,0.5)]"
                        style="width: {{ $pairwisePercent }}%"></div>
                </div>
                <div class="flex justify-between text-[10px] font-black uppercase tracking-tighter adaptive-text-sub">
                    <span>{{ $dmPairwiseDone }} Terkumpul</span>
                    <span>Total {{ $assignedDmCount }} DM</span>
                </div>
            </div>

            {{-- Progres Alternatif --}}
            <div class="adaptive-card p-6 border-t-4 border-t-emerald-500">
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <h4 class="text-lg font-black adaptive-text-main leading-tight">Penilaian Alternatif</h4>
                        <p class="text-xs adaptive-text-sub font-medium mt-1">Status penilaian kandidat berdasarkan
                            kriteria.</p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-emerald-500">{{ round($altPercent) }}%</span>
                    </div>
                </div>
                <div class="relative w-full bg-app/20 rounded-full h-3 mb-2 overflow-hidden shadow-inner">
                    <div class="absolute top-0 left-0 h-full bg-emerald-500 rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(16,185,129,0.5)]"
                        style="width: {{ $altPercent }}%"></div>
                </div>
                <div class="flex justify-between text-[10px] font-black uppercase tracking-tighter adaptive-text-sub">
                    <span>{{ $dmAltDone }} Terkumpul</span>
                    <span>Total {{ $assignedDmCount }} DM</span>
                </div>
            </div>
        </div>

        {{-- SECTION: MANAJEMEN STATUS (Action Cards) --}}
        <div class="grid grid-cols-1 gap-6">

            {{-- 1. DRAFT -> ACTIVE --}}
            @if ($decisionSession->status === 'draft')
                <div
                    class="adaptive-card p-8 border-blue-500/30 bg-gradient-to-br from-blue-500/5 to-transparent relative overflow-hidden group">
                    <div
                        class="absolute -right-8 -top-8 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all">
                    </div>
                    <div
                        class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8 text-center md:text-left">
                        <div class="flex-1">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 text-blue-600 text-[10px] font-black uppercase tracking-widest mb-4">
                                Tahap Persiapan
                            </div>
                            <h3 class="text-2xl font-black adaptive-text-main leading-tight">Aktifkan Sesi Keputusan</h3>
                            <p class="text-sm adaptive-text-sub mt-2 max-w-xl">Dengan mengaktifkan sesi, Decision Maker
                                dapat mulai memberikan penilaian. Pastikan data kriteria dan alternatif sudah final.</p>

                            @php $canActivate = $activeCriteriaCount >= 2 && $activeAlternativesCount >= 2 && $assignedDmCount >= 1; @endphp
                            @if (!$canActivate)
                                <div
                                    class="mt-4 inline-flex items-center gap-2 text-rose-500 font-bold text-xs bg-rose-500/5 px-4 py-2 rounded-xl border border-rose-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Lengkapi Kriteria (min. 2), Alternatif (min. 2), dan DM (min. 1).
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('decision-sessions.activate', $decisionSession->id) }}"
                            onsubmit="return confirm('Buka sesi sekarang?')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="inline-flex items-center gap-3 px-10 py-4 bg-blue-600 text-white rounded-2xl font-black text-sm transition-all hover:scale-105 hover:shadow-xl hover:shadow-blue-500/30 active:scale-95 disabled:opacity-30 disabled:grayscale disabled:pointer-events-none"
                                {{ $canActivate ? '' : 'disabled' }}>
                                <span>BUKA SESI SEKARANG</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- 2. ACTIVE -> CRITERIA LOCK (ALTERNATIVES) --}}
            @if ($decisionSession->status === 'active')
                <div
                    class="adaptive-card p-8 border-amber-500/30 bg-gradient-to-br from-amber-500/5 to-transparent relative overflow-hidden group">
                    <div
                        class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8 text-center md:text-left">
                        <div class="flex-1">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-600 text-[10px] font-black uppercase tracking-widest mb-4">
                                Tahap Pairwise
                            </div>
                            <h3 class="text-2xl font-black adaptive-text-main leading-tight">Kunci Penilaian Kriteria</h3>
                            <p class="text-sm adaptive-text-sub mt-2 max-w-xl">Gunakan langkah ini jika seluruh Decision
                                Maker telah selesai mengisi perbandingan kriteria. Bobot kriteria akan segera dihitung
                                secara permanen.</p>
                        </div>

                        @php
                            $canLockCriteria = $assignedDmCount > 0 && $dmPairwiseDone === $assignedDmCount;
                        @endphp
                        @unless ($canLockCriteria)
                            <div
                                class="mt-4 inline-flex items-center gap-2 text-amber-600 font-bold text-xs bg-amber-500/10 px-4 py-2 rounded-xl border border-amber-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Menunggu seluruh Decision Maker menyelesaikan pairwise kriteria.
                            </div>
                        @endunless
                        <form method="POST" action="{{ route('decision-sessions.lock-criteria', $decisionSession->id) }}"
                            onsubmit="return confirm('Kunci kriteria?')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="px-10 py-4 bg-amber-500 text-white rounded-2xl font-black text-sm transition-all hover:scale-105 hover:shadow-xl hover:shadow-amber-500/30 active:scale-95 disabled:opacity-30 disabled:grayscale disabled:pointer-events-none"
                                {{ $canLockCriteria ? '' : 'disabled' }}>
                                KUNCI & LANJUTKAN
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- 3. ALTERNATIVES -> CLOSED --}}
            @if ($decisionSession->status === 'alternatives')
                <div
                    class="adaptive-card p-8 border-rose-500/30 bg-gradient-to-br from-rose-500/5 to-transparent relative overflow-hidden group">
                    <div
                        class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8 text-center md:text-left">
                        <div class="flex-1">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/10 text-rose-600 text-[10px] font-black uppercase tracking-widest mb-4">
                                Tahap Akhir
                            </div>
                            <h3 class="text-2xl font-black adaptive-text-main leading-tight">Finalisasi & Tutup Sesi</h3>
                            <p class="text-sm adaptive-text-sub mt-2 max-w-xl">Langkah ini akan menutup seluruh aktivitas
                                penilaian. Hasil ranking akhir akan dibentuk dan tidak dapat diubah lagi.</p>
                        </div>

                        @php
                            $canFinalize = $assignedDmCount > 0 && $dmAltDone === $assignedDmCount;
                        @endphp
                        @unless ($canFinalize)
                            <div
                                class="mt-4 inline-flex items-center gap-2 text-rose-600 font-bold text-xs bg-rose-500/10 px-4 py-2 rounded-xl border border-rose-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Menunggu seluruh Decision Maker menyelesaikan penilaian alternatif.
                            </div>
                        @endunless
                        <form method="POST" action="{{ route('decision-sessions.close', $decisionSession->id) }}"
                            onsubmit="return confirm('Tutup sesi dan finalisasi?')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="px-10 py-4 bg-rose-600 text-white rounded-2xl font-black text-sm transition-all hover:scale-105 hover:shadow-xl hover:shadow-rose-500/30 active:scale-95 disabled:opacity-30 disabled:grayscale disabled:pointer-events-none"
                                {{ $canFinalize ? '' : 'disabled' }}>
                                TUTUP & FINALISASI
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- 4. CLOSED STATE --}}
            @if ($decisionSession->status === 'closed')
                <div class="adaptive-card p-12 text-center border-emerald-500/30 bg-emerald-500/5 relative">
                    <div
                        class="w-20 h-20 bg-emerald-500 text-white rounded-3xl flex items-center justify-center mx-auto mb-6 rotate-3 shadow-lg shadow-emerald-500/20">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-3xl font-black adaptive-text-main tracking-tight">Pelaksanaan Sesi Selesai</h3>
                    <p class="text-base adaptive-text-sub mt-3 max-w-md mx-auto">Seluruh data penilaian telah dikunci dan
                        perhitungan ranking telah disimpan dalam database.</p>

                    <div class="mt-10 flex justify-center gap-4">
                        <a href="{{ route('reports.index') }}"
                            class="px-8 py-3 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:brightness-110 transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                            Lihat Laporan Ranking
                        </a>
                        <a href="{{ route('decision-sessions.index') }}"
                            class="px-8 py-3 bg-app border border-app adaptive-text-main rounded-xl font-bold text-sm hover:bg-card transition-all active:scale-95">
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
