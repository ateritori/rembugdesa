@extends('layouts.dashboard')

@section('title', 'Penugasan DM')

@section('content')

    {{-- TAB NAVIGASI SESI --}}
    @include('admin.partials.session-nav')

    <div class="animate-in fade-in slide-in-from-bottom-2 w-full px-4 py-4 md:px-6 md:py-6 duration-500 dark:bg-slate-900">

        <div class="w-full space-y-4 md:space-y-6">

            {{-- HEADER SECTION: Disinkronkan dengan Kriteria/Alternatif --}}
            <div
                class="flex flex-col justify-between gap-4 border-b border-slate-100 px-1 pb-4 md:flex-row md:items-end dark:border-slate-800">
                <div>
                    <div class="mb-1 flex items-center gap-2">
                        <span class="bg-indigo-500 h-1.5 w-6 rounded-full"></span>
                        <p class="text-indigo-500 text-[9px] font-black uppercase tracking-[0.2em]">System Authorization</p>
                    </div>
                    <h1
                        class="adaptive-text-main text-xl md:text-2xl font-black uppercase tracking-tight transition-all duration-300">
                        Penugasan
                    </h1>
                </div>

                @if ($decisionSession->status !== 'draft')
                    <div
                        class="flex items-center self-start md:self-auto gap-2.5 rounded-xl bg-slate-900 px-4 py-2 text-white shadow-lg dark:bg-slate-800">
                        <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Akses Terkunci</span>
                    </div>
                @endif
            </div>

            {{-- SEARCH: Slim Style --}}
            <div class="relative w-full max-w-sm">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                </div>
                <input type="text" onkeyup="filterDM(this.value)" placeholder="Cari DM..."
                    class="w-full rounded-xl border-none bg-white py-2.5 pl-11 pr-4 text-xs font-bold ring-1 ring-slate-200 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-800 dark:ring-slate-700 dark:text-white">
            </div>

            <form method="POST" onsubmit="prepareDmIds()"
                action="{{ route('decision-sessions.assign-dms.store', $decisionSession->id) }}">
                @csrf

                <div class="space-y-4">

                    {{-- ARAH PEMBANGUNAN --}}
                    <div
                        class="rounded-2xl border border-slate-200 bg-white dark:bg-slate-800 dark:border-slate-700 p-4 shadow-sm">
                        <h3 class="text-[11px] font-black uppercase tracking-widest text-indigo-500 mb-3">
                            Arah Pembangunan
                        </h3>
                        <div class="flex gap-2 mb-2">
                            <button type="button" onclick="checkAllPairwise(true)"
                                {{ $decisionSession->status !== 'draft' ? 'disabled' : '' }}
                                class="text-[9px] font-bold text-indigo-500">All</button>
                            <button type="button" onclick="checkAllPairwise(false)"
                                {{ $decisionSession->status !== 'draft' ? 'disabled' : '' }}
                                class="text-[9px] font-bold text-slate-400">Clear</button>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-80 overflow-y-auto pr-2">
                            @foreach ($dms as $dm)
                                <div data-name="{{ strtolower($dm->name) }}">
                                    <label class="flex items-center gap-2 text-[10px] font-bold text-slate-500">
                                        <input type="checkbox" name="pairwise[]" value="{{ $dm->id }}"
                                            {{ $decisionSession->status !== 'draft' ? 'disabled' : '' }}
                                            {{ in_array($dm->id, old('pairwise', $assignedDmIds ?? [])) ? 'checked' : '' }}
                                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        {{ $dm->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="pt-2">
                    <div class="h-[1px] w-full bg-slate-200 dark:bg-slate-700"></div>
                </div>

                {{-- PARAMETER EVALUATION ASSIGNMENT --}}
                <div class="mt-6 rounded-2xl border border-slate-200 bg-white dark:bg-slate-800 dark:border-slate-700 p-4">
                    <h3 class="text-[11px] font-black uppercase tracking-widest text-emerald-500 mb-4">
                        Penugasan Evaluator Parameter
                    </h3>

                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        @foreach ($criteria->where('level', 2)->where('evaluator_type', 'human') as $param)
                            <div
                                class="rounded-xl border border-slate-100 dark:border-slate-700 p-3 bg-slate-50 dark:bg-slate-900/40">
                                <p class="text-[11px] font-black text-slate-700 dark:text-slate-200 mb-2">
                                    {{ $param->name }}
                                </p>
                                <div class="flex gap-2 mb-2">
                                    <button type="button" onclick="checkAllParam({{ $param->id }}, true)"
                                        {{ $decisionSession->status !== 'draft' ? 'disabled' : '' }}
                                        class="text-[9px] font-bold text-emerald-500">All</button>
                                    <button type="button" onclick="checkAllParam({{ $param->id }}, false)"
                                        {{ $decisionSession->status !== 'draft' ? 'disabled' : '' }}
                                        class="text-[9px] font-bold text-slate-400">Clear</button>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                    @foreach ($dms as $dm)
                                        <div data-name="{{ strtolower($dm->name) }}">
                                            <label class="flex items-center gap-2 text-[10px] font-bold text-slate-500">
                                                <input type="checkbox" name="param_assign[{{ $param->id }}][]"
                                                    value="{{ $dm->id }}"
                                                    {{ $decisionSession->status !== 'draft' ? 'disabled' : '' }}
                                                    {{ in_array($dm->id, old('param_assign.' . $param->id, $assignedParam[$param->id] ?? [])) ? 'checked' : '' }}
                                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                {{ $dm->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- hidden dm_ids (union) --}}
                <div id="dm_ids_container"></div>

                {{-- ACTION FOOTER: Sticky & Slim --}}
                <div
                    class="sticky bottom-6 z-10 mt-12 flex items-center justify-between rounded-2xl border border-slate-200 bg-white/90 p-3 shadow-2xl backdrop-blur-md dark:bg-slate-800/95 dark:border-slate-700">
                    <div class="flex items-center gap-4 pl-2">
                        <div class="flex flex-col">
                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Selected</p>
                            <p id="counter" class="text-xl font-black leading-none text-indigo-600 dark:text-indigo-400">0
                            </p>
                        </div>
                        <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-700"></div>
                        <p class="hidden text-[9px] font-bold uppercase text-slate-400 lg:block">
                            Pilih DM berdasarkan tugas. Gunakan search & tombol cepat untuk efisiensi.
                        </p>
                    </div>

                    @if ($decisionSession->status === 'draft')
                        <div class="flex gap-2">
                            <button type="submit"
                                class="rounded-xl bg-slate-900 px-8 py-2.5 text-[10px] font-black uppercase tracking-widest text-white hover:bg-black active:scale-95 transition-all dark:bg-indigo-600">
                                Confirm Assignment
                            </button>
                        </div>
                    @endif
                </div>
            </form>

        </div>
    </div>

    <script>
        function filterDM(q) {
            q = q.toLowerCase();
            document.querySelectorAll('[data-name]').forEach(el => {
                el.style.display = el.dataset.name.includes(q) ? '' : 'none';
            });
        }

        function checkAllPairwise(val) {
            document.querySelectorAll('input[name="pairwise[]"]').forEach(el => el.checked = val);
            updateCounter();
        }

        function checkAllParam(paramId, val) {
            document.querySelectorAll(`input[name="param_assign[${paramId}][]"]`).forEach(el => el.checked = val);
            updateCounter();
        }

        function updateCounter() {
            const pairwise = document.querySelectorAll('input[name="pairwise[]"]:checked').length;
            const param = document.querySelectorAll('input[name^="param_assign"]:checked').length;
            document.getElementById('counter').innerText = pairwise + param;
        }

        document.addEventListener('change', function(e) {
            if (e.target.matches('input[type="checkbox"]')) updateCounter();
        });

        document.addEventListener('DOMContentLoaded', function() {
            updateCounter();
        });

        function prepareDmIds() {
            const ids = new Set();
            document.querySelectorAll('input[name="pairwise[]"]:checked').forEach(el => ids.add(el.value));
            document.querySelectorAll('input[name^="param_assign"]:checked').forEach(el => ids.add(el.value));
            const container = document.getElementById('dm_ids_container');
            container.innerHTML = '';
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'dm_ids[]';
                input.value = id;
                container.appendChild(input);
            });
        }
    </script>

@endsection
