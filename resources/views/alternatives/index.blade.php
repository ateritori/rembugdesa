@extends('layouts.dashboard')

@section('title', 'Alternatif')

@section('content')

    {{-- TAB NAVIGASI SESI --}}
    @include('decision-sessions.partials.nav')

    <div class="space-y-6 animate-in fade-in duration-500">

        {{-- ================= NOTIFIKASI ================= --}}
        @if (session('success'))
            <div
                class="flex items-center gap-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 px-4 py-3 text-sm font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 px-4 py-3 text-sm font-bold">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="flex items-center gap-2">
                            <span class="w-1 h-1 rounded-full bg-current"></span>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="adaptive-card p-6 shadow-sm">
            {{-- Header --}}
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-black text-app">Manajemen Alternatif</h2>
                    <p class="text-sm adaptive-text-sub">
                        Daftar entitas atau pilihan yang akan dianalisis dalam sesi ini.
                    </p>
                </div>

                @if ($decisionSession->status !== 'draft')
                    <span
                        class="px-3 py-1 bg-amber-500/10 text-amber-600 rounded-lg text-xs font-bold border border-amber-500/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Terkunci (Hanya Baca)
                    </span>
                @endif
            </div>

            {{-- Form tambah alternatif --}}
            <form method="POST" action="{{ route('alternatives.store', $decisionSession->id) }}"
                class="flex flex-col md:flex-row gap-3 mb-8 p-4 bg-app/50 border border-app rounded-2xl transition-all
                     {{ $decisionSession->status !== 'draft' ? 'opacity-50 pointer-events-none' : 'focus-within:border-primary/50 shadow-inner' }}">
                @csrf

                <div class="relative flex-1 group">
                    <input type="text" name="name" placeholder="Masukkan nama alternatif baru..."
                        class="w-full pl-4 pr-4 py-2.5 bg-card border border-app rounded-xl text-app text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:opacity-50"
                        required>
                </div>

                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-primary/20"
                    {{ $decisionSession->status !== 'draft' ? 'disabled' : '' }}>
                    Tambah Alternatif
                </button>
            </form>

            {{-- List alternatif --}}
            <div class="grid grid-cols-1 gap-3">
                @forelse ($alternatives as $a)
                    <div x-data="{ open: false }" class="group">
                        <div
                            class="flex justify-between items-center bg-card border border-app rounded-2xl px-5 py-3 transition-all group-hover:border-primary/30 group-hover:shadow-md {{ !$a->is_active ? 'opacity-60 bg-app' : '' }}">

                            <div class="flex items-center gap-4 min-w-0">
                                <div
                                    class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-mono font-bold text-xs shrink-0">
                                    {{ $a->code }}
                                </div>

                                <div class="truncate">
                                    <span
                                        class="font-bold text-app block truncate {{ !$a->is_active ? 'line-through opacity-50' : '' }}">
                                        {{ $a->name }}
                                    </span>
                                    @if (!$a->is_active)
                                        <span class="text-[10px] font-black uppercase tracking-tighter text-rose-500">
                                            Tidak Digunakan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div
                                class="flex items-center gap-1 {{ $decisionSession->status !== 'draft' ? 'opacity-40 pointer-events-none' : '' }}">
                                {{-- Edit --}}
                                <button type="button" @click="open = !open"
                                    class="p-2 text-app hover:text-blue-500 hover:bg-blue-500/10 rounded-lg transition-all"
                                    title="Edit Nama">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                {{-- Toggle active --}}
                                <form method="POST" action="{{ route('alternatives.toggle', $a->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="p-2 text-app {{ $a->is_active ? 'hover:text-amber-500 hover:bg-amber-500/10' : 'text-emerald-500 bg-emerald-500/10' }} rounded-lg transition-all"
                                        title="{{ $a->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        @if ($a->is_active)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('alternatives.destroy', $a->id) }}"
                                    onsubmit="return confirm('Hapus alternatif ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-2 text-app hover:text-rose-500 hover:bg-rose-500/10 rounded-lg transition-all"
                                        title="Hapus Permanen">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Inline edit form --}}
                        <form x-show="open" x-transition.origin.top @click.outside="open = false" method="POST"
                            action="{{ route('alternatives.update', $a->id) }}"
                            class="mt-2 flex flex-col md:flex-row gap-3 bg-app/30 border border-app rounded-2xl p-4 shadow-inner">
                            @csrf @method('PUT')

                            <input type="text" name="name" value="{{ $a->name }}"
                                class="flex-1 px-4 py-2 bg-card border border-app rounded-xl text-app text-sm focus:ring-2 focus:ring-primary/20 outline-none"
                                required>

                            <div class="flex gap-2">
                                <button type="submit"
                                    class="flex-1 md:flex-none px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold">
                                    Update Nama
                                </button>
                                <button type="button" @click="open = false"
                                    class="px-5 py-2 rounded-xl bg-app border border-app adaptive-text-sub text-xs font-bold">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center opacity-40">
                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 8-8-8">
                            </path>
                        </svg>
                        <p class="text-sm font-bold uppercase tracking-widest">Belum ada alternatif</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection
