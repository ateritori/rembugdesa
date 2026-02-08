{{-- Kontrol Sesi --}}
<div class="space-y-8 animate-in fade-in duration-500">

    {{-- Section: Dashboard Ringkasan --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $stats = [
                ['label' => 'Status Sesi', 'value' => $decisionSession->status, 'color' => 'text-blue-500'],
                ['label' => 'Kriteria Aktif', 'value' => $activeCriteriaCount, 'color' => 'text-purple-500'],
                ['label' => 'Alternatif Aktif', 'value' => $activeAlternativesCount, 'color' => 'text-indigo-500'],
                ['label' => 'Decision Maker', 'value' => $assignedDmCount, 'color' => 'text-emerald-500'],
            ];
        @endphp

        @foreach ($stats as $stat)
            {{-- Menggunakan adaptive-card agar background & border otomatis berubah --}}
            <div class="adaptive-card p-4 shadow-sm hover:shadow-md transition-all">
                <p class="text-[10px] uppercase tracking-wider adaptive-text-sub font-bold mb-1">{{ $stat['label'] }}</p>
                <p class="text-lg font-bold capitalize {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Section: Monitoring DM --}}
    <div class="adaptive-card overflow-hidden shadow-sm">
        {{-- Header Monitoring --}}
        <div
            class="px-6 py-4 border-b border-app flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-app/20">
            <div>
                <h3 class="text-sm font-bold adaptive-text-main flex items-center gap-2">
                    <span class="w-2 h-5 bg-primary rounded-full"></span>
                    Monitoring Progress Penilaian
                </h3>
                <p class="text-xs adaptive-text-sub mt-0.5">Pantau partisipasi Decision Maker secara real-time.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:block text-right">
                    @php
                        $completedCount = $assignedDms->where('has_submitted', true)->count();
                        $percent = $assignedDmCount > 0 ? ($completedCount / $assignedDmCount) * 100 : 0;
                    @endphp
                    <p class="text-[10px] font-bold adaptive-text-sub uppercase leading-none mb-1">Total Partisipasi</p>
                    <p class="text-sm font-black adaptive-text-main">{{ $completedCount }} <span
                            class="opacity-30">/</span> {{ $assignedDmCount }}</p>
                </div>
                {{-- Progress Circle Adaptif --}}
                <div class="w-12 h-12 rounded-full border-4 border-app flex items-center justify-center relative">
                    <svg class="w-full h-full absolute -rotate-90">
                        <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4"
                            fill="transparent" class="opacity-10" />
                        <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4"
                            fill="transparent" class="text-primary" stroke-dasharray="125.6"
                            stroke-dashoffset="{{ 125.6 - (125.6 * $percent) / 100 }}" />
                    </svg>
                    <span class="text-[10px] font-bold adaptive-text-main">{{ round($percent) }}%</span>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($assignedDms as $dm)
                    <div
                        class="group flex items-center justify-between p-4 rounded-xl border transition-all
                        {{ $dm->has_submitted ? 'border-emerald-500/30 bg-emerald-500/5' : 'border-app bg-app/10' }} hover:border-primary/50">

                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="relative">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-xs uppercase shadow-lg
                                    {{ $dm->has_submitted ? 'bg-gradient-to-br from-emerald-400 to-emerald-600' : 'bg-slate-400' }}">
                                    {{ substr($dm->name, 0, 2) }}
                                </div>
                                @if ($dm->has_submitted)
                                    <div
                                        class="absolute -bottom-1 -right-1 bg-white dark:bg-slate-800 rounded-full p-0.5">
                                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="truncate">
                                <p class="text-xs font-bold adaptive-text-main truncate">{{ $dm->name }}</p>
                                <p class="text-[10px] adaptive-text-sub truncate">{{ $dm->email }}</p>
                            </div>
                        </div>

                        <div>
                            @if ($dm->has_submitted)
                                <span
                                    class="text-[9px] font-black uppercase tracking-widest text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded">Selesai</span>
                            @else
                                <span
                                    class="text-[9px] font-black uppercase tracking-widest text-amber-500 bg-amber-500/10 px-2 py-0.5 rounded animate-pulse">Pending</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
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
                            class="group relative inline-flex items-center gap-3 px-8 py-4 bg-primary text-white rounded-xl font-bold text-sm transition-all hover:scale-105 hover:shadow-xl hover:shadow-primary/20 disabled:opacity-30 disabled:scale-100"
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

        {{-- 2. ACTIVE -> CLOSED (BAGIAN YANG TADI HILANG) --}}
        @if ($decisionSession->status === 'active')
            <div
                class="adaptive-card p-6 border-red-500/20 bg-gradient-to-r from-transparent to-red-500/5 relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="text-center md:text-left">
                            <h3 class="text-base font-bold adaptive-text-main">Finalisasi & Tutup Sesi</h3>
                            <p class="text-sm adaptive-text-sub max-w-md mt-1">Data akan dikunci secara permanen dan
                                sistem akan mulai menghitung ranking final.</p>
                        </div>

                        <form method="POST" action="{{ route('decision-sessions.close', $decisionSession->id) }}"
                            onsubmit="return confirm('Tutup sesi? Tindakan ini permanen.')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="px-8 py-4 bg-red-600 text-white rounded-xl font-bold text-sm transition-all hover:bg-red-700 hover:scale-105 hover:shadow-xl hover:shadow-red-500/20">
                                Tutup & Kunci Sesi
                            </button>
                        </form>
                    </div>

                    {{-- Warning jika DM belum selesai --}}
                    @php $pendingDmCount = $assignedDms->where('has_submitted', false)->count(); @endphp
                    @if ($pendingDmCount > 0)
                        <div
                            class="mt-6 flex items-start gap-3 p-4 bg-amber-500/10 rounded-xl border border-amber-500/20">
                            <span class="text-amber-500 mt-0.5">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <div class="text-xs text-amber-500">
                                <p class="font-bold">Perhatian: Penilaian Belum Lengkap</p>
                                <p class="opacity-80">Masih ada <strong>{{ $pendingDmCount }} Decision Maker</strong>
                                    yang belum mengirimkan penilaian. Menutup sesi sekarang akan mengabaikan partisipasi
                                    mereka.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- 3. CLOSED STATE --}}
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
                <p class="text-sm adaptive-text-sub mt-2 max-w-sm mx-auto">Seluruh data penilaian telah dikunci dan
                    diproses. Anda bisa melihat laporan ranking akhir di menu Laporan.</p>
                <div class="mt-6 flex justify-center gap-4">
                    <a href="{{ route('reports.index') }}"
                        class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg font-bold text-xs hover:bg-emerald-700 transition-colors">
                        Lihat Laporan Ranking
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
