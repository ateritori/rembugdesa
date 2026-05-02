@extends('layouts.dashboard')

@section('title', 'Decision Provenance')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span
                        class="bg-primary/10 text-primary text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-md">
                        Decision Provenance
                    </span>
                </div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Jejak Proses Keputusan
                </h1>
                <p class="adaptive-text-sub mt-1 max-w-xl text-sm leading-relaxed">
                    Menampilkan alur proses perhitungan dan keterlacakan keputusan dari setiap metode.
                </p>
            </div>

            <div class="flex items-center gap-2">

                <a href="{{ route('decision-sessions.index') }}"
                    class="group flex items-center gap-2 rounded-xl border-2 border-slate-200 dark:border-slate-700 px-4 py-2 text-xs font-black text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Kembali
                </a>
            </div>
        </div>



        <div class="adaptive-card p-6 border rounded-2xl bg-white dark:bg-slate-900/50">
            <h3 class="text-xs font-black uppercase tracking-widest mb-4">
                Pilih Metode
            </h3>

            <div class="flex border-b mb-4">
                <a href="#" onclick="setTab('smart')" data-tab="smart"
                    class="px-4 py-2 text-xs font-black border-b-2 {{ request()->routeIs('provenance.smart') ? 'border-primary text-primary' : 'border-transparent text-slate-500' }}">
                    SMART Trace
                </a>

                <a href="#" onclick="setTab('borda')" data-tab="borda"
                    class="px-4 py-2 text-xs font-black border-b-2 {{ request()->routeIs('provenance.borda') ? 'border-primary text-primary' : 'border-transparent text-slate-500' }}">
                    SMART Borda
                </a>

                <a href="#" onclick="setTab('saw')" data-tab="saw"
                    class="px-4 py-2 text-xs font-black border-b-2 border-transparent text-slate-500">
                    SAW Trace
                </a>
                <a href="#" onclick="setTab('sawborda')" data-tab="sawborda"
                    class="px-4 py-2 text-xs font-black border-b-2 border-transparent text-slate-500">
                    SAW Borda
                </a>
                <a href="#" onclick="setTab('compare')" data-tab="compare"
                    class="px-4 py-2 text-xs font-black border-b-2 border-transparent text-slate-500">
                    Perbandingan
                </a>
            </div>

            <div id="tab-smart" class="tab-content">
                @include('admin.provenance.partials.smart')
            </div>

            <div id="tab-borda" class="tab-content hidden">
                @include('admin.provenance.partials.smartborda')
            </div>

            <div id="tab-saw" class="tab-content hidden">
                @include('admin.provenance.partials.saw')
            </div>

            <div id="tab-sawborda" class="tab-content hidden">
                @include('admin.provenance.partials.sawborda')
            </div>

            <div id="tab-compare" class="tab-content hidden">
                @include('admin.provenance.partials.compare')
            </div>

            <div>
                <p class="text-xs text-slate-500">
                    Pilih tab untuk melihat detail proses perhitungan.
                </p>
            </div>
        </div>

        {{-- EMPTY --}}
        @if (isset($results) && $results->isEmpty())
            <div class="adaptive-card p-20 text-center border-dashed border-2 rounded-3xl opacity-50">
                <p class="text-slate-500 font-black uppercase tracking-widest text-xs">
                    Tidak ada data SMART.
                </p>
            </div>
        @endif

    </div>

    <script>
        function setTab(tab) {
            document.getElementById('tab-smart').classList.add('hidden');
            document.getElementById('tab-borda').classList.add('hidden');
            document.getElementById('tab-saw').classList.add('hidden');
            document.getElementById('tab-sawborda').classList.add('hidden');
            document.getElementById('tab-compare').classList.add('hidden');

            document.querySelectorAll('[data-tab]').forEach(el => {
                el.classList.remove('border-primary', 'text-primary');
                el.classList.add('border-transparent', 'text-slate-500');
            });

            document.getElementById('tab-' + tab).classList.remove('hidden');
        }
    </script>

@endsection
