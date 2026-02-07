@extends('layouts.dashboard')

@section('title', 'Sesi Keputusan')

@section('content')
    <style>
        /* Mengambil warna primary dari sistem Anda secara dinamis */
        :root {
            /* Fallback jika variabel CSS tidak ditemukan, tapi class Tailwind akan tetap mendominasi */
            --primary-smart: var(--primary, #3b82f6);
        }

        .theme-adaptive-text {
            color: inherit;
        }

        .theme-adaptive-subtext {
            color: inherit;
            opacity: 0.6;
        }

        .smart-card {
            background-color: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(156, 163, 175, 0.15);
        }

        /* Tombol Workspace: Border & Text mengikuti warna Picked User */
        .btn-workspace-user {
            border: 2px solid currentColor;
            /* Mengikuti warna teks primary */
            background: transparent;
            transition: all 0.2s ease-in-out;
        }

        .btn-workspace-user:hover {
            background-color: currentColor;
            /* Menjadi solid warna picked user */
        }

        .btn-workspace-user:hover span,
        .btn-workspace-user:hover svg {
            filter: brightness(0) invert(1);
            /* Memastikan icon/teks jadi putih saat hover */
        }

        .smart-list-item:hover {
            background-color: rgba(156, 163, 175, 0.08);
        }
    </style>

    <div class="space-y-6 animate-in fade-in duration-500">

        {{-- HEADER SECTION --}}
        <div class="flex justify-between items-center">
            <div class="theme-adaptive-text">
                <h1 class="text-2xl font-black tracking-tight">
                    Daftar Sesi Keputusan
                </h1>
                <p class="text-sm theme-adaptive-subtext">
                    Kelola sesi pengambilan keputusan dengan tema pilihan Anda.
                </p>
            </div>

            {{-- Menggunakan bg-primary (Warna Picked User) --}}
            <a href="{{ route('decision-sessions.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-sm shadow-lg hover:brightness-110 active:scale-95 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Sesi Baru</span>
            </a>
        </div>

        {{-- LIST SECTION --}}
        <div class="smart-card rounded-2xl overflow-hidden p-2">
            @forelse ($sessions as $s)
                <div
                    class="smart-list-item group flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 mb-2 last:mb-0 rounded-xl border border-transparent transition-all">

                    {{-- INFO --}}
                    <div class="flex items-center gap-4">
                        <div
                            class="hidden sm:flex w-12 h-12 rounded-lg bg-gray-500/10 items-center justify-center text-gray-400 group-hover:text-primary transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>

                        <div class="theme-adaptive-text">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold group-hover:text-primary transition-colors">
                                    {{ $s->name }}
                                </h3>
                                <span
                                    class="text-[10px] font-black px-2 py-0.5 rounded bg-gray-500/20 text-gray-400 uppercase tracking-widest">
                                    {{ $s->year }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                @php
                                    $statusDot =
                                        [
                                            'draft' => 'bg-gray-400',
                                            'active' => 'bg-emerald-500 animate-pulse',
                                            'closed' => 'bg-rose-500',
                                        ][$s->status] ?? 'bg-gray-400';
                                @endphp
                                <span class="w-2 h-2 rounded-full {{ $statusDot }}"></span>
                                <span
                                    class="text-xs font-semibold theme-adaptive-subtext capitalize">{{ $s->status }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex items-center gap-3 mt-4 sm:mt-0 w-full sm:w-auto justify-end">

                        {{-- Workspace: Menggunakan text-primary (Picked User) untuk border dan teks --}}
                        <a href="{{ route('decision-sessions.show', $s->id) }}"
                            class="btn-workspace-user text-primary inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-black uppercase tracking-wide">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h10" />
                            </svg>
                            <span>Workspace</span>
                        </a>

                        @if ($s->status === 'draft')
                            <a href="{{ route('decision-sessions.edit', $s->id) }}"
                                class="p-2 rounded-lg text-gray-400 hover:text-blue-500 hover:bg-blue-500/10 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.862 3.487a2.1 2.1 0 013.02 3.02L7.5 18.889l-4.5 1.125 1.125-4.5L16.862 3.487z" />
                                </svg>
                            </a>

                            <form method="POST" action="{{ route('decision-sessions.destroy', $s->id) }}"
                                onsubmit="return confirm('Hapus sesi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-2 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-500/10 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-20 text-center theme-adaptive-subtext">
                    <p class="text-sm font-bold uppercase tracking-widest">Belum ada sesi keputusan.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
