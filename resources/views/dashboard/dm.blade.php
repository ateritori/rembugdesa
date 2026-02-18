@extends('layouts.dashboard')

@section('content')
    <div class="animate-in fade-in slide-in-from-bottom-4 space-y-8 pb-10 duration-700">

        {{-- 1. HEADER --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-800 dark:text-white">Dashboard Decision Maker</h1>
                <p class="text-sm font-bold text-slate-400">Kelola dan pantau partisipasi Anda dalam pengambilan keputusan.
                </p>
            </div>

            <div class="rounded-2xl border border-primary/20 bg-primary/10 px-4 py-2">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">Role: Decision Maker</span>
            </div>
        </div>

        {{-- 2. SUMMARY CARDS --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $cards = [
                    [
                        'label' => 'Total Sesi',
                        'value' => $assignedCount,
                        'icon' =>
                            'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                        'color' => 'from-blue-600 to-blue-700',
                    ],
                    [
                        'label' => 'Sesi Aktif',
                        'value' => $activeCount,
                        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                        'color' => 'from-emerald-600 to-emerald-700',
                    ],
                    [
                        'label' => 'Tugas Pending',
                        'value' => $pendingTaskCount,
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        'color' =>
                            $pendingTaskCount > 0 ? 'from-amber-500 to-orange-600' : 'from-slate-700 to-slate-800',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div
                    class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-500 hover:-translate-y-1 hover:shadow-xl dark:border-slate-700 dark:bg-slate-800">
                    <div class="relative z-10 flex flex-col">
                        <span
                            class="mb-1 text-[11px] font-black uppercase tracking-widest text-slate-400">{{ $card['label'] }}</span>
                        <span
                            class="text-4xl font-black tracking-tighter text-slate-800 transition-colors group-hover:text-primary dark:text-white">{{ $card['value'] }}</span>
                    </div>
                    <div
                        class="absolute -bottom-4 -right-4 opacity-[0.05] transition-all duration-700 group-hover:scale-110 group-hover:opacity-10">
                        <svg class="h-20 w-20 text-slate-900 dark:text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}">
                            </path>
                        </svg>
                    </div>
                    <div class="absolute left-0 top-0 h-full w-1.5 bg-gradient-to-b {{ $card['color'] }}"></div>
                </div>
            @endforeach
        </div>

        {{-- 3. DAFTAR TUGAS HEADER --}}
        <div class="flex items-center gap-4 text-slate-400">
            <h2 class="text-xs font-black uppercase tracking-[0.3em]">Daftar Penilaian Aktif</h2>
            <div class="h-px flex-1 bg-slate-200 dark:bg-slate-700"></div>
        </div>

        {{-- 4. SESSION CARDS --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($assignedSessions as $session)
                @php
                    $url = '#';
                    $btnLabel = 'Buka Workspace';
                    $statusMessage = '';
                    $statusColor = 'text-slate-400';
                    $isLocked = true;

                    if ($session->status === 'closed') {
                        $url = route('dms.index', [$session->id, 'tab' => 'hasil-akhir']);
                        $btnLabel = 'Lihat Hasil Akhir';
                        $statusMessage = 'Sesi Selesai & Final';
                        $statusColor = 'text-emerald-500';
                        $isLocked = false;
                    } elseif ($session->status === 'scoring') {
                        if (!($session->dmEvaluationFinished ?? false)) {
                            $url = route('dms.index', [$session->id, 'tab' => 'evaluasi-alternatif']);
                            $btnLabel = 'Nilai Alternatif';
                            $statusMessage = 'Perlu Evaluasi Alternatif';
                            $statusColor = 'text-blue-500 animate-pulse';
                        } else {
                            $url = route('dms.index', [$session->id, 'tab' => 'status']);
                            $btnLabel = 'Lihat Progres';
                            $statusMessage = 'Menunggu Finalisasi';
                            $statusColor = 'text-emerald-500';
                        }
                        $isLocked = false;
                    } elseif ($session->status === 'configured') {
                        if (!$session->dmHasCompleted) {
                            $url = route('dms.index', [$session->id, 'tab' => 'penilaian-kriteria']);
                            $btnLabel = 'Bobot Kriteria';
                            $statusMessage = 'Perlu Input Pairwise';
                            $statusColor = 'text-amber-500 animate-pulse';
                        } else {
                            $url = route('dms.index', [$session->id, 'tab' => 'status']);
                            $btnLabel = 'Cek Status Bobot';
                            $statusMessage = 'Menunggu Tahap Berikutnya';
                            $statusColor = 'text-indigo-500';
                        }
                        $isLocked = false;
                    } elseif ($session->status === 'draft') {
                        $isLocked = true;
                        $statusMessage = 'Belum Dibuka Admin';
                        $statusColor = 'text-slate-400';
                    }
                @endphp

                <div
                    class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-500 hover:-translate-y-1 hover:shadow-xl dark:border-slate-700 dark:bg-slate-800">
                    <div class="space-y-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <h3 class="line-clamp-2 text-lg font-black uppercase leading-tight tracking-tight text-slate-800 transition-colors group-hover:text-primary dark:text-white"
                                    title="{{ $session->name }}">
                                    {{ $session->name }}
                                </h3>
                                <p class="mt-1 text-[10px] font-black uppercase tracking-widest text-primary">Periode
                                    {{ $session->year }}</p>
                            </div>
                            <span
                                class="shrink-0 rounded-lg bg-slate-100 px-2 py-1 text-[9px] font-black text-slate-500 dark:bg-slate-900">ID-{{ $session->id }}</span>
                        </div>

                        {{-- Info Grid: Tahap & Cakupan Data --}}
                        <div class="grid grid-cols-2 gap-4 border-y border-slate-50 py-4 dark:border-slate-700">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Tahap
                                    Sesi</span>
                                <div class="mt-1">
                                    @php
                                        $badge = match ($session->status) {
                                            'draft' => 'bg-slate-100 text-slate-500',
                                            'configured' => 'bg-blue-100 text-blue-700',
                                            'scoring' => 'bg-amber-100 text-amber-700',
                                            'closed' => 'bg-emerald-100 text-emerald-700',
                                            default => 'bg-slate-100 text-slate-500',
                                        };
                                    @endphp
                                    <span
                                        class="{{ $badge }} rounded px-2 py-0.5 text-[9px] font-black uppercase tracking-tighter">
                                        {{ $session->status }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-col border-l border-slate-50 pl-4 dark:border-slate-700">
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Cakupan
                                    Sesi</span>
                                <div class="mt-1 flex items-center gap-2">
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="text-xs font-black text-slate-700 dark:text-slate-200">{{ $session->criteria_count ?? $session->criteria->count() }}</span>
                                        <span class="text-[7px] uppercase font-bold text-slate-400">Kriteria</span>
                                    </div>
                                    <div class="h-4 w-px bg-slate-200 dark:bg-slate-600"></div>
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="text-xs font-black text-slate-700 dark:text-slate-200">{{ $session->alternatives_count ?? $session->alternatives->count() }}</span>
                                        <span class="text-[7px] uppercase font-bold text-slate-400">Alternatif</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status Indikator --}}
                        <div class="flex items-center gap-2 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-900/50">
                            <div class="h-2 w-2 shrink-0 rounded-full bg-current {{ $statusColor }}"></div>
                            <p class="text-[10px] font-black uppercase tracking-widest {{ $statusColor }}">
                                {{ $statusMessage }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8">
                        @if (!$isLocked)
                            <a href="{{ $url }}"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-4 text-[11px] font-black uppercase tracking-[0.2em] text-white shadow-lg shadow-slate-200 transition-all hover:bg-primary hover:shadow-primary/30 active:scale-95 dark:bg-primary dark:shadow-none">
                                {{ $btnLabel }}
                                <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                            @if ($session->status === 'scoring' && ($session->dmEvaluationFinished ?? false))
                                <a href="{{ route('usability.responses.create', ['decision_session_id' => $session->id]) }}"
                                    class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-black uppercase tracking-[0.2em] text-slate-700 transition hover:border-primary/30 hover:text-primary dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                    Isi SUS
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @endif
                        @else
                            <div
                                class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-300 dark:border-slate-700 dark:bg-slate-900">
                                <svg class="h-4 w-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Akses Terkunci
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full rounded-3xl border-2 border-dashed border-slate-200 py-24 text-center dark:border-slate-700">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300">Belum ada sesi yang
                        ditugaskan kepada Anda</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
