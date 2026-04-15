@extends('layouts.dashboard')

@section('title', 'Manajemen Kriteria')

@section('content')

    @include('admin.partials.session-nav')

    {{-- Logika Pengunci: Aksi hanya boleh dilakukan jika status MASIH draft --}}
    @php
        $isLocked = $decisionSession->status !== 'draft';
    @endphp

    <div class="animate-in fade-in slide-in-from-bottom-2 w-full px-4 py-4 md:px-6 md:py-6 duration-500 dark:bg-slate-900">

        <div class="w-full space-y-4 md:space-y-6">

            {{-- HEADER SECTION --}}
            <div
                class="flex flex-col justify-between gap-4 border-b border-slate-100 px-1 pb-4 md:flex-row md:items-end dark:border-slate-800">
                <div>
                    <div class="mb-1 flex items-center gap-2">
                        <span class="bg-primary h-1.5 w-6 rounded-full"></span>
                        <p class="text-primary text-[9px] font-black uppercase tracking-[0.2em]">
                            Parameter Engine
                        </p>
                    </div>
                    <h1
                        class="adaptive-text-main text-xl md:text-2xl font-black uppercase tracking-tight transition-colors duration-300">
                        Manajemen Kriteria
                    </h1>
                </div>

                {{-- Status Indicator --}}
                @if ($isLocked)
                    <div
                        class="flex items-center self-start md:self-auto gap-2.5 rounded-xl bg-slate-900 px-4 py-2 text-white shadow-lg dark:bg-slate-800">
                        <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Akses Terkunci</span>
                    </div>
                @endif
            </div>

            {{-- FORM TAMBAH --}}
            <div
                class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm focus-within:border-primary/30 dark:border-slate-700 dark:bg-slate-800 {{ $isLocked ? 'opacity-50 grayscale pointer-events-none' : '' }}">
                <form method="POST" action="{{ route('criteria.store', $decisionSession->id) }}"
                    class="flex flex-col gap-3 lg:flex-row lg:items-end">
                    @csrf
                    <div class="flex flex-col gap-1.5 w-full lg:flex-[2] pt-0.5">
                        <span class="block ml-1 mb-0.5 text-[10px] font-semibold text-slate-500">Nama Kriteria</span>
                        <div class="group relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />
                                </svg>
                            </div>
                            <input name="name" required {{ $isLocked ? 'disabled' : '' }}
                                class="w-full rounded-xl border-none bg-slate-50 py-2.5 pl-11 pr-4 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary/10 dark:bg-slate-900 dark:text-slate-200"
                                placeholder="Nama kriteria baru...">
                        </div>
                    </div>

                    <div class="flex w-full flex-col gap-3 sm:flex-row lg:flex-[3] lg:gap-2 items-end">
                        <div class="flex flex-col gap-1.5 pt-0.5 w-full lg:w-40">
                            <span class="block ml-1 mb-0.5 text-[10px] font-semibold text-slate-500">Tipe</span>
                            <select name="type" required {{ $isLocked ? 'disabled' : '' }}
                                class="w-full rounded-xl border-none bg-slate-50 px-4 py-2.5 text-xs font-semibold text-slate-500 focus:bg-white dark:bg-slate-900">
                                <option value="" disabled selected>Pilih tipe</option>
                                <option value="benefit">Benefit</option>
                                <option value="cost">Cost</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1.5 pt-0.5 w-full lg:w-40">
                            <span class="block ml-1 mb-0.5 text-[10px] font-semibold text-slate-500">Level</span>
                            <select name="level" required {{ $isLocked ? 'disabled' : '' }}
                                class="w-full rounded-xl border-none bg-slate-50 px-4 py-2.5 text-xs font-semibold text-slate-500 focus:bg-white dark:bg-slate-900">
                                <option value="" disabled selected>Pilih level</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="flex flex-col gap-1.5 pt-0.5 w-full lg:w-40">
                            <span class="block ml-1 mb-0.5 text-[10px] font-semibold text-slate-500">Evaluator</span>
                            <select name="evaluator_type" required {{ $isLocked ? 'disabled' : '' }}
                                class="w-full rounded-xl border-none bg-slate-50 px-4 py-2.5 text-xs font-semibold text-slate-500 focus:bg-white dark:bg-slate-900">
                                <option value="" disabled selected>Pilih evaluator</option>
                                <option value="human">Manusia</option>
                                <option value="system">Sistem</option>
                            </select>
                        </div>

                        <button type="submit" {{ $isLocked ? 'disabled' : '' }}
                            class="w-full sm:w-auto flex-none rounded-xl bg-slate-800 px-6 py-2.5 mt-1 lg:mt-0 text-[10px] font-black uppercase tracking-widest text-white hover:bg-black active:scale-95 dark:bg-primary-600">
                            + Tambah
                        </button>
                    </div>
                </form>
            </div>

            {{-- LIST DATA --}}
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                @php
                    $grouped = $criteria->groupBy('level');
                @endphp

                @forelse ($grouped as $level => $items)
                    @include('criteria.partials.level-group', [
                        'level' => $level,
                        'items' => $items,
                        'scoringRules' => $scoringRules,
                        'sessionLocked' => $isLocked,
                    ])
                @empty
                    <div class="col-span-full text-center text-slate-400 text-xs py-10">
                        Belum ada data kriteria
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
