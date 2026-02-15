@extends('layouts.dashboard')

@section('content')
    <div class="animate-in fade-in slide-in-from-bottom-4 space-y-8 pb-10 duration-700">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-app text-2xl font-black tracking-tight">Dashboard Decision Maker</h1>
                <p class="text-app text-sm opacity-60">Ringkasan sesi keputusan yang ditugaskan kepada Anda.</p>
            </div>

            <div class="bg-primary/10 border-primary/20 rounded-2xl border px-4 py-2">
                <span class="text-primary text-[10px] font-black uppercase tracking-[0.2em]">Role: Decision Maker</span>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $cards = [
                    [
                        'label' => 'Total Sesi',
                        'value' => $assignedCount,
                        'icon' =>
                            'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                        'bg' => 'bg-blue-500',
                    ],
                    [
                        'label' => 'Sesi Aktif',
                        'value' => $activeCount,
                        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                        'bg' => 'bg-emerald-500',
                    ],
                    [
                        'label' => 'Tugas Pending',
                        'value' => $pendingTaskCount,
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        'bg' => $pendingTaskCount > 0 ? 'bg-amber-500' : 'bg-primary',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div
                    class="adaptive-card hover:border-primary/50 group relative overflow-hidden p-6 transition-all duration-500">
                    <div class="relative z-10 flex flex-col">
                        <span
                            class="text-app mb-1 text-[11px] font-black uppercase tracking-widest opacity-40">{{ $card['label'] }}</span>
                        <span
                            class="text-app group-hover:text-primary text-4xl font-black transition-colors">{{ $card['value'] }}</span>
                    </div>
                    <div
                        class="absolute -bottom-4 -right-4 opacity-[0.03] transition-all duration-700 group-hover:scale-110 group-hover:opacity-10">
                        <svg class="text-app h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}">
                            </path>
                        </svg>
                    </div>
                    <div class="{{ $card['bg'] }} absolute left-0 top-0 h-full w-1 opacity-50"></div>
                </div>
            @endforeach
        </div>

        {{-- DAFTAR TUGAS HEADER --}}
        <div class="flex items-center gap-4">
            <h2 class="text-app text-base font-black uppercase tracking-widest">Daftar Penilaian</h2>
            <div class="bg-app h-px flex-1 opacity-10"></div>
        </div>

        {{-- SESSION CARDS --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($assignedSessions as $session)
                @php
                    // Ambil CR untuk DM saat ini
                    $dmWeight = $session->criteriaWeights->where('dm_id', auth()->id())->first();
                    $crValue = $dmWeight ? $dmWeight->cr : null;
                    $isConsistent = $crValue !== null && $crValue <= 0.1;

                    // RESET VARIABEL UNTUK TIAP LOOP
                    $url = '#';
                    $btnLabel = 'Buka Workspace';
                    $statusMessage = '';
                    $statusColor = 'text-gray-500';
                    $isLocked = false;

                    /** * LOGIKA FIX: CEK STATUS GLOBAL DULU, BARU CEK PROGRESS DM
                     */

                    // 1. JIKA CLOSED (PRIORITAS UTAMA)
                    if ($session->status === 'closed') {
                        $url = route('dms.index', [$session->id, 'tab' => 'hasil-akhir']);
                        $btnLabel = 'Lihat Hasil Akhir';
                        $statusMessage = 'Keputusan Selesai';
                        $statusColor = 'text-emerald-500 font-bold';
                    }

                    // 2. JIKA DRAFT
                    elseif ($session->status === 'draft') {
                        $isLocked = true;
                        $statusMessage = 'Menunggu Admin';
                        $statusColor = 'text-gray-400';
                    }

                    // 3. JIKA BELUM ISI KRITERIA (PAIRWISE)
                    elseif (!$session->dmHasCompleted) {
                        $url = route('decision-sessions.pairwise.index', $session->id);
                        $btnLabel = 'Mulai Penilaian Kriteria';
                        $statusMessage = 'Perlu Perbandingan Kriteria';
                        $statusColor = 'text-amber-500 animate-pulse';
                    }

                    // 4. JIKA SUDAH KRITERIA, TINGGAL ALTERNATIF (SCORING)
                    elseif ($session->status === 'scoring') {
                        if (!($session->dmEvaluationFinished ?? false)) {
                            $url = route('alternative-evaluations.index', $session->id);
                            $btnLabel = 'Mulai Penilaian Alternatif';
                            $statusMessage = 'Lanjut Penilaian Alternatif';
                            $statusColor = 'text-blue-500 animate-pulse';
                        } else {
                            $url = route('dms.index', $session->id);
                            $btnLabel = 'Lihat Progress Saya';
                            $statusMessage = 'Menunggu Hasil Final';
                            $statusColor = 'text-emerald-500';
                        }
                    }
                @endphp

                <div
                    class="adaptive-card hover:shadow-primary/5 group flex flex-col justify-between p-6 transition-all hover:shadow-2xl">
                    <div class="space-y-5">
                        <div class="flex items-start justify-between">
                            <div class="min-w-0">
                                <h3 class="text-app group-hover:text-primary truncate text-lg font-black transition-colors">
                                    {{ $session->name }}
                                </h3>
                                <p class="text-app text-xs font-bold italic opacity-40">Periode {{ $session->year }}</p>
                            </div>
                        </div>

                        <div class="border-app/50 grid grid-cols-2 gap-4 border-y py-4">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black uppercase tracking-widest opacity-40">Status Sesi</span>
                                <div class="mt-1">
                                    @php
                                        $statusBadge = [
                                            'draft' => ['label' => 'Draft', 'class' => 'bg-gray-500/10 text-gray-500'],
                                            'configured' => [
                                                'label' => 'Ready',
                                                'class' => 'bg-blue-500/10 text-blue-500',
                                            ],
                                            'scoring' => [
                                                'label' => 'Scoring',
                                                'class' => 'bg-amber-500/10 text-amber-500',
                                            ],
                                            'closed' => [
                                                'label' => 'Closed',
                                                'class' => 'bg-emerald-500/10 text-emerald-500',
                                            ],
                                        ];
                                        $currentBadge = $statusBadge[$session->status] ?? [
                                            'label' => $session->status,
                                            'class' => 'bg-gray-500/10 text-gray-500',
                                        ];
                                    @endphp
                                    <span
                                        class="{{ $currentBadge['class'] }} rounded-md px-2 py-0.5 text-[10px] font-black uppercase">
                                        {{ $currentBadge['label'] }}
                                    </span>
                                </div>
                            </div>

                            {{-- Hanya tampilkan CR jika sudah isi kriteria --}}
                            @if ($session->dmHasCompleted)
                                <div class="border-app/50 flex flex-col border-l pl-4">
                                    <span class="text-[9px] font-black uppercase tracking-widest opacity-40">Konsistensi
                                        (CR)
                                    </span>
                                    <span
                                        class="mt-1 text-xs font-black {{ $isConsistent ? 'text-primary' : 'text-red-500' }}">
                                        {{ number_format($crValue ?? 0, 4) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-current {{ $statusColor }}"></div>
                            <p class="text-[11px] font-black uppercase tracking-tighter {{ $statusColor }}">
                                {{ $statusMessage }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8">
                        @if (!$isLocked)
                            <a href="{{ $url }}"
                                class="bg-primary shadow-primary/20 inline-flex w-full items-center justify-center rounded-xl px-4 py-3.5 text-[11px] font-black uppercase tracking-widest text-white shadow-lg transition-all hover:scale-[1.02] active:scale-95">
                                {{ $btnLabel }}
                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        @else
                            <div
                                class="bg-app/10 border-app/20 w-full rounded-xl border px-4 py-3.5 text-center text-[10px] font-black uppercase tracking-widest opacity-50">
                                Akses Terkunci
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="adaptive-card col-span-full rounded-3xl border-dashed py-24 text-center">
                    <p class="text-app text-sm font-black uppercase tracking-[0.2em] opacity-30">Belum ada sesi ditugaskan
                    </p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
