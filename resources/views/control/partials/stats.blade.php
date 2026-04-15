{{-- SECTION 1: DASHBOARD RINGKASAN --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @php
        $stats = [
            [
                'label' => 'Status Sesi',
                'value' => $decisionSession->status,
                'icon' =>
                    'M11.241 4.817c.121-.696.927-1.023 1.511-.524l9.464 8.112a.75.75 0 0 1-.428 1.328H18v6.25a.75.75 0 0 1-.75.75H14.5a.75.75 0 0 1-.75-.75v-4.5a.25.25 0 0 0-.25-.25h-3a.25.25 0 0 0-.25.25v4.5a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V13.733H3.213a.75.75 0 0 1-.428-1.328l9.464-8.112c.125-.107.292-.158.459-.15z',
                'color' => 'from-blue-600 to-indigo-600',
                'shadow' => 'shadow-blue-500/20',
            ],
            [
                'label' => 'Kriteria Aktif',
                'value' => $activeCriteriaCount,
                'icon' =>
                    'M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6z',
                'color' => 'from-indigo-600 to-violet-600',
                'shadow' => 'shadow-indigo-500/20',
            ],
            [
                'label' => 'Alternatif Aktif',
                'value' => $activeAlternativesCount,
                'icon' =>
                    'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
                'color' => 'from-purple-600 to-fuchsia-600',
                'shadow' => 'shadow-purple-500/20',
            ],
            [
                'label' => 'Decision Maker',
                'value' => $assignedDmCount . ' DM',
                'icon' =>
                    'M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18.75a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-1.5a.75.75 0 0 1 .75-.75ZM5.106 17.834a.75.75 0 0 0 1.06 1.06l1.591-1.59a.75.75 0 1 0-1.06-1.061l-1.591 1.59ZM3 12a.75.75 0 0 1 .75-.75h2.25a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm3.166-5.894a.75.75 0 0 0 1.06 1.06l1.59-1.591a.75.75 0 1 0-1.06-1.061l-1.591 1.59Z',
                'color' => 'from-emerald-600 to-teal-600',
                'shadow' => 'shadow-emerald-500/20',
            ],
        ];
    @endphp

    @foreach ($stats as $stat)
        <div
            class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-500 hover:-translate-y-1 hover:shadow-xl dark:border-slate-700 dark:bg-slate-800">
            <div class="relative flex items-center gap-4">
                <div
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $stat['color'] }} {{ $stat['shadow'] }} shadow-lg transition-all duration-500 group-hover:rotate-6 group-hover:scale-110">
                    <svg class="h-8 w-8 text-white fill-current" viewBox="0 0 24 24">
                        <path d="{{ $stat['icon'] }}" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <p class="text-[9px] font-black uppercase tracking-[.2em] text-slate-400">
                        {{ $stat['label'] }}
                    </p>
                    <p class="text-lg font-black uppercase tracking-tight text-slate-800 dark:text-slate-100">
                        {{ $stat['value'] }}
                    </p>

                    @if ($stat['label'] === 'Decision Maker')
                        <p class="text-[9px] font-bold text-slate-400 mt-1">
                            {{ $totalExpectedActions ?? 0 }} Total Actions
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
