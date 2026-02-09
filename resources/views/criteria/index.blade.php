@extends('layouts.dashboard')

@section('title', 'Kriteria')

@section('content')

    {{-- TAB NAVIGASI SESI --}}
    @include('decision-sessions.partials.nav')

    <div class="space-y-6 animate-in fade-in duration-500 pb-10">

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
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-app pb-6">
                <div>
                    <h2 class="text-xl font-black text-app">Daftar Kriteria</h2>
                    <p class="text-sm adaptive-text-sub">Tentukan kriteria penilaian dan konfigurasikan aturan penilaian
                        masing-masing.</p>
                </div>
                @if ($decisionSession->status !== 'draft')
                    <span
                        class="px-3 py-1 bg-amber-500/10 text-amber-600 rounded-lg text-xs font-bold border border-amber-500/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Status Terkunci
                    </span>
                @endif
            </div>

            {{-- Form Tambah Kriteria --}}
            <form method="POST" action="{{ route('criteria.store', $decisionSession->id) }}"
                class="flex flex-col md:flex-row gap-3 mb-8 p-4 bg-app/30 border border-app rounded-2xl transition-all
                     {{ $decisionSession->status !== 'draft' ? 'opacity-50 pointer-events-none' : 'focus-within:border-primary/50' }}">
                @csrf
                <input type="text" name="name" placeholder="Nama kriteria (contoh: Harga, Kualitas)"
                    class="flex-1 px-4 py-2.5 bg-card border border-app rounded-xl text-app text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                    required>

                <select name="type"
                    class="px-4 py-2.5 bg-card border border-app rounded-xl text-app text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all cursor-pointer font-bold"
                    required>
                    <option value="" disabled selected>Pilih Jenis</option>
                    <option value="benefit" class="font-bold text-emerald-600">Benefit (+)</option>
                    <option value="cost" class="font-bold text-rose-600">Cost (-)</option>
                </select>

                <button type="submit"
                    class="px-8 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-primary/20"
                    {{ $decisionSession->status !== 'draft' ? 'disabled' : '' }}>
                    Tambah
                </button>
            </form>

            {{-- List Kriteria --}}
            <div class="grid grid-cols-1 gap-4">
                @forelse ($criteria as $c)
                    @php $rule = $scoringRules->get($c->id); @endphp
                    <div x-data="{ open: false, openScoring: false }" class="group">
                        <div
                            class="flex flex-col md:flex-row justify-between items-center bg-card border border-app rounded-2xl px-5 py-4 transition-all group-hover:border-primary/30 group-hover:shadow-md {{ !$c->is_active ? 'opacity-60 grayscale' : '' }}">

                            <div class="flex items-center gap-4 w-full md:w-auto">
                                <div
                                    class="w-8 h-8 rounded-lg bg-app border border-app flex items-center justify-center font-black text-xs adaptive-text-sub">
                                    {{ $loop->iteration }}
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-bold text-app">{{ $c->name }}</span>

                                    {{-- Type Badge --}}
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter border {{ $c->type === 'benefit' ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 border-rose-500/20' }}">
                                        {{ $c->type }}
                                    </span>

                                    {{-- Rule Badges --}}
                                    @if ($rule)
                                        <div class="flex items-center gap-1">
                                            <span
                                                class="text-[9px] px-1.5 py-0.5 rounded bg-blue-500/10 text-blue-600 font-bold border border-blue-500/10">
                                                {{ $rule->input_type === 'scale' ? 'Skala' : 'Numerik' }}
                                            </span>
                                            <span
                                                class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-500/10 text-indigo-600 font-bold border border-indigo-500/10">
                                                {{ ucfirst($rule->preference_type) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 mt-4 md:mt-0 w-full md:w-auto justify-end">
                                {{-- Edit Toggle --}}
                                <button @click="open = !open"
                                    class="p-2 text-app hover:text-blue-500 hover:bg-blue-500/10 rounded-lg transition-all"
                                    title="Edit Kriteria">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                {{-- Toggle Active --}}
                                <form method="POST" action="{{ route('criteria.toggle', $c->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="p-2 text-app hover:text-amber-500 hover:bg-amber-500/10 rounded-lg transition-all"
                                        title="Aktif/Nonaktif">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('criteria.destroy', $c->id) }}"
                                    onsubmit="return confirm('Hapus kriteria ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-2 text-app hover:text-rose-500 hover:bg-rose-500/10 rounded-lg transition-all"
                                        title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>

                                {{-- Action: Atur Penilaian --}}
                                @if ($decisionSession->status === 'draft')
                                    <button @click="openScoring = !openScoring"
                                        class="ml-2 px-4 py-2 rounded-xl bg-amber-500 text-white text-[10px] font-black uppercase tracking-widest hover:brightness-110 transition-all shadow-md shadow-amber-500/20 active:scale-95">
                                        Atur Penilaian
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Inline Form: Edit Identitas --}}
                        <form x-show="open" x-transition.origin.top method="POST"
                            action="{{ route('criteria.update', $c->id) }}"
                            class="mt-2 flex flex-col md:flex-row gap-3 bg-app/20 border border-app rounded-2xl p-4 shadow-inner"
                            @click.outside="open = false">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $c->name }}"
                                class="flex-1 px-4 py-2 bg-card border border-app rounded-xl text-app text-sm outline-none focus:ring-2 focus:ring-primary/20"
                                {{ $decisionSession->status !== 'draft' ? 'readonly' : 'required' }}>
                            <select name="type"
                                class="px-4 py-2 bg-card border border-app rounded-xl text-app text-sm font-bold"
                                {{ $decisionSession->status !== 'draft' ? 'disabled' : 'required' }}>
                                <option value="benefit" {{ $c->type === 'benefit' ? 'selected' : '' }}>Benefit</option>
                                <option value="cost" {{ $c->type === 'cost' ? 'selected' : '' }}>Cost</option>
                            </select>
                            @if ($decisionSession->status === 'draft')
                                <button type="submit"
                                    class="px-6 py-2 rounded-xl bg-primary text-white text-xs font-bold">Simpan</button>
                            @endif
                        </form>

                        {{-- Scoring Rules Section --}}
                        <div x-show="openScoring" x-transition.origin.top class="mt-2">
                            @include('criteria.partials.scoring-rule', [
                                'c' => $c,
                                'rule' => $rule,
                                'decisionSession' => $decisionSession,
                            ])
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center opacity-40">
                        <p class="text-sm font-bold uppercase tracking-widest italic">Belum ada kriteria yang ditambahkan.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection
