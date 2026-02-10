@extends('layouts.dashboard')

@section('title', 'Penilaian Alternatif')

@section('content')

    @include('decision-sessions.partials.nav')

    {{-- NOTIFICATION --}}
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('warning'))
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-xl">
            <p class="text-sm font-bold">{{ session('warning') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
            <p class="text-sm font-bold mb-1">Penilaian belum lengkap:</p>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('alternative-evaluations.store', $decisionSession->id) }}">
        @csrf

        <div x-data="{
            activeIdx: 0,
            total: {{ count($alternatives) }},
            currentDesktopPage: 0,
            perPage: 5,
            get totalDesktopPages() { return Math.ceil(this.total / this.perPage) }
        }" x-cloak class="space-y-6">

            {{-- HEADER SECTION --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h2 class="text-2xl font-black text-app">Penilaian Alternatif</h2>
                <div class="bg-app/10 px-4 py-2 rounded-full border border-app/20">
                    <span class="text-xs font-bold text-app uppercase">Simpan di akhir penilaian</span>
                </div>
            </div>

            {{-- 1. DESKTOP VIEW --}}
            <div class="hidden md:block space-y-4">
                @foreach ($alternatives->chunk(5) as $chunkIndex => $chunk)
                    <div x-show="currentDesktopPage === {{ $chunkIndex }}"
                        class="overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                        Alternatif</th>
                                    @foreach ($criteria as $c)
                                        <th
                                            class="px-4 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">
                                            {{ $c->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($chunk as $a)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 font-bold text-app bg-gray-50/20">{{ $a->name }}</td>
                                        @foreach ($criteria as $c)
                                            @php
                                                $evaluation = $evaluations[$a->id][$c->id] ?? null;
                                                $semanticsParam = $c->scoringRule->getParameter('scale_semantics');
                                                $semantics = is_string($semanticsParam)
                                                    ? json_decode($semanticsParam, true)
                                                    : (is_array($semanticsParam)
                                                        ? $semanticsParam
                                                        : []);
                                            @endphp
                                            <td class="px-4 py-4">
                                                <div class="flex flex-col gap-1 min-w-[140px]">
                                                    @foreach ($semantics as $value => $label)
                                                        <label
                                                            class="flex items-center gap-2 cursor-pointer group text-xs text-gray-600 hover:text-app">
                                                            <input type="radio"
                                                                name="evaluations[{{ $a->id }}][{{ $c->id }}]"
                                                                {{ $loop->first ? 'required' : '' }}
                                                                value="{{ $value }}" @checked(optional($evaluation)->raw_value == $value)
                                                                class="w-4 h-4 text-app border-gray-300">
                                                            {{ $label }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach

                <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-gray-200">
                    <div class="flex gap-2">
                        <button type="button" @click="currentDesktopPage--" :disabled="currentDesktopPage === 0"
                            class="px-4 py-2 border-2 rounded-lg font-bold disabled:opacity-30">Prev</button>
                        <button type="button" @click="currentDesktopPage++"
                            :disabled="currentDesktopPage >= totalDesktopPages - 1"
                            class="px-4 py-2 border-2 rounded-lg font-bold disabled:opacity-30">Next</button>
                    </div>
                    <button type="submit" x-show="currentDesktopPage === totalDesktopPages - 1"
                        class="bg-green-600 text-white px-10 py-3 rounded-xl font-black shadow-lg uppercase">Simpan
                        Semua</button>
                </div>
            </div>

            {{-- 2. MOBILE VIEW (Wizard) - BRUTE FORCE APPROACH --}}
            <div class="md:hidden">
                @foreach ($alternatives as $index => $a)
                    <div x-show="activeIdx === {{ $index }}" class="block">

                        {{-- CARD PUTIH --}}
                        <div class="bg-white border-2 border-gray-100 rounded-[2rem] p-6 shadow-sm">
                            <div class="border-b pb-3 mb-6">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Penilaian
                                    Alternatif</span>
                                <h3 class="font-black text-2xl text-app uppercase tracking-tight">{{ $a->name }}</h3>
                            </div>

                            <div class="space-y-6">
                                @foreach ($criteria as $c)
                                    <div class="space-y-4">
                                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">
                                            {{ $c->name }}</h4>

                                        @php
                                            $evaluation = $evaluations[$a->id][$c->id] ?? null;
                                            $semanticsParam = $c->scoringRule->getParameter('scale_semantics');
                                            $semantics = is_string($semanticsParam)
                                                ? json_decode($semanticsParam, true)
                                                : (is_array($semanticsParam)
                                                    ? $semanticsParam
                                                    : []);
                                        @endphp

                                        <div class="flex flex-col gap-3">
                                            @foreach ($semantics as $value => $label)
                                                {{-- STRUKTUR BARU: Input di depan, Label di belakang. Klasik & Pasti Jalan --}}
                                                <label
                                                    class="flex items-center gap-4 p-4 rounded-2xl border-2 border-gray-100 bg-gray-50 active:bg-app/5 transition-all">
                                                    {{-- Kita buat inputnya terlihat dan besar agar mudah diklik jempol --}}
                                                    <input type="radio"
                                                        name="evaluations[{{ $a->id }}][{{ $c->id }}]"
                                                        {{ $loop->first ? 'required' : '' }} value="{{ $value }}"
                                                        @checked(optional($evaluation)->raw_value == $value)
                                                        class="w-6 h-6 text-app border-gray-300 focus:ring-app">

                                                    <span
                                                        class="text-sm font-bold text-gray-600 uppercase">{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- NAVIGATION (MENEMPEL) --}}
                        <div class="mt-4 px-2 pb-10">
                            <div class="flex gap-3">
                                <button type="button" @click="activeIdx--; window.scrollTo({top: 0, behavior: 'smooth'})"
                                    x-show="activeIdx > 0"
                                    class="flex-1 bg-gray-200 py-4 rounded-2xl font-black text-gray-600 uppercase text-sm">
                                    PREV
                                </button>

                                <button type="button" @click="activeIdx++; window.scrollTo({top: 0, behavior: 'smooth'})"
                                    x-show="activeIdx < total - 1" :class="activeIdx === 0 ? 'w-full' : 'flex-[2]'"
                                    class="bg-gray-900 text-white py-4 rounded-2xl font-black uppercase text-sm tracking-widest">
                                    NEXT
                                </button>

                                <button type="submit" x-show="activeIdx === total - 1"
                                    class="flex-[2] bg-green-600 text-white py-4 rounded-2xl font-black uppercase text-sm tracking-widest border-b-4 border-green-800">
                                    SIMPAN
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
    </form>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

@endsection

<style>
    [x-cloak] {
        display: none !important;
    }

    @media (max-width: 768px) {
        .content-wrapper {
            padding-bottom: 40px !important;
        }
    }
</style>
