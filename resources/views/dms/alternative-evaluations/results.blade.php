@php
    $alternatives = $alternatives ?? collect();
    $criteria = $criteria ?? collect();
    $scoringRules = $scoringRules ?? collect();
    $existingEvaluations = $existingEvaluations ?? collect();
@endphp

<div class="space-y-10">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between px-2">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Penilaian Alternatif</h2>
            <p class="text-sm text-slate-500 font-medium">Input nilai secara presisi berdasarkan kriteria pembobotan.</p>
        </div>
        {{-- Tombol Save Utama (Opsional: jika ingin batch save) --}}
        <div class="flex items-center gap-2">
            <span
                class="text-[10px] font-black uppercase tracking-widest text-slate-400 bg-slate-100 px-3 py-1 rounded-full">
                Auto-Save Enabled
            </span>
        </div>
    </div>

    @if ($alternatives->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-200 p-20">
            <div class="rounded-full bg-slate-50 p-4 text-slate-300">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <p class="mt-4 text-sm font-medium text-slate-400">Belum ada alternatif yang tersedia.</p>
        </div>
    @else
        <div class="grid gap-12">
            @foreach ($alternatives as $alternative)
                <div class="group relative">
                    {{-- Nama Alternatif dengan Ornamen --}}
                    <div class="mb-6 flex items-center gap-4 px-2">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-xl shadow-slate-200 font-bold text-sm">
                            {{ substr($alternative->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">
                                {{ $alternative->name }}</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Detail Penilaian
                                Unit</p>
                        </div>
                        <div class="h-[1px] flex-1 bg-gradient-to-r from-slate-200 to-transparent ml-4"></div>
                    </div>

                    {{-- Grid Input --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach ($criteria as $criterion)
                            @php
                                $rule = $scoringRules[$criterion->id] ?? null;
                                if (!$rule) {
                                    continue;
                                }
                                $key = $alternative->id . '-' . $criterion->id;
                                $existing = $existingEvaluations[$key] ?? null;
                                $weight = $criterion->weight ?? 0;
                            @endphp

                            <div
                                class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 transition-all duration-300 hover:border-primary hover:shadow-2xl hover:shadow-primary/5 group/card">

                                {{-- Visual Bobot (Progress Bar Mini) --}}
                                <div class="absolute top-0 left-0 h-1 bg-primary/10 w-full">
                                    <div class="h-full bg-primary" style="width: {{ $weight }}%"></div>
                                </div>

                                <form method="POST"
                                    action="{{ route('alternative-evaluations.store', $decisionSession->id) }}">
                                    @csrf
                                    <input type="hidden" name="alternative_id" value="{{ $alternative->id }}">
                                    <input type="hidden" name="criteria_id" value="{{ $criterion->id }}">

                                    <div class="mb-4 flex items-start justify-between">
                                        <div>
                                            <label
                                                class="text-[11px] font-black uppercase tracking-widest text-slate-400 group-hover/card:text-primary transition-colors">
                                                {{ $criterion->name }}
                                            </label>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-xs font-bold text-slate-700">Bobot
                                                    {{ $weight }}%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        {{-- INPUT: SCALE --}}
                                        @if ($rule->input_type === 'scale')
                                            @php
                                                $min = (int) $rule->getParameter('scale_min');
                                                $max = (int) $rule->getParameter('scale_max');
                                                $labels = (array) ($rule->getParameter('scale_semantics') ?? []);
                                            @endphp
                                            <select name="raw_value" required
                                                class="flex-1 rounded-xl border-slate-100 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-700 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all outline-none appearance-none">
                                                <option value="">Pilih Skor</option>
                                                @for ($i = $min; $i <= $max; $i++)
                                                    <option value="{{ $i }}" @selected($existing && (int) $existing->raw_value === $i)>
                                                        {{ $i }}
                                                        {{ isset($labels[$i]) ? ' — ' . $labels[$i] : '' }}
                                                    </option>
                                                @endfor
                                            </select>
                                        @endif

                                        {{-- INPUT: NUMERIC --}}
                                        @if ($rule->input_type === 'numeric')
                                            @php
                                                $unit = $rule->getParameter('unit');
                                                $minVal = $rule->getParameter('value_min');
                                                $maxVal = $rule->getParameter('value_max');
                                            @endphp
                                            <div class="relative flex-1">
                                                <input type="number" name="raw_value" step="any"
                                                    min="{{ $minVal }}" max="{{ $maxVal }}"
                                                    value="{{ $existing?->raw_value }}" required
                                                    class="w-full rounded-xl border-slate-100 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-700 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all outline-none"
                                                    placeholder="Input {{ $unit ?? 'nilai' }}...">
                                                @if ($unit)
                                                    <span
                                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300 uppercase">{{ $unit }}</span>
                                                @endif
                                            </div>
                                        @endif

                                        <button type="submit"
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-white transition-all hover:bg-primary active:scale-90 shadow-lg shadow-slate-200 hover:shadow-primary/20">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
