@if ($decisionSession->status === 'draft')
    @php
        $isEdit = $rule !== null;

        // Ambil parameter dengan fallback array kosong
        $semantics = $rule ? $rule->getParameter('scale_semantics') ?? [] : [];
        $utilities = $rule ? $rule->getParameter('scale_utilities') ?? [] : [];
        $rangeArr = $rule ? $rule->getParameter('scale_range') ?? ['min' => 1, 'max' => 5] : ['min' => 1, 'max' => 5];

        // Pastikan range adalah array (untuk menangani string JSON di DB)
        if (is_string($rangeArr)) {
            $rangeArr = json_decode($rangeArr, true);
        }
        $minVal = $rangeArr['min'] ?? 1;
        $maxVal = $rangeArr['max'] ?? 5;
    @endphp

    <div x-data="scoringRule({
        isEdit: {{ $isEdit ? 'true' : 'false' }},
        inputType: '{{ $rule->input_type ?? '' }}',
        preferenceType: '{{ $rule->preference_type ?? 'linear' }}',
        min: {{ $minVal }},
        max: {{ $maxVal }},
        semantics: @js($semantics),
        utilities: @js($utilities)
    })" :key="'rule-{{ $c->id }}'" class="w-full">

        <form x-show="open" x-transition method="POST"
            action="{{ $isEdit ? route('criteria.scoring.update', [$c->id, $rule->id]) : route('criteria.scoring.store', $c->id) }}"
            class="space-y-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-8">

            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            {{-- HEADER (Tetap Sama) --}}
            <div class="flex flex-col justify-between gap-3 border-b border-slate-100 pb-5 sm:flex-row sm:items-center">
                <div>
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Aturan Penilaian</h4>
                    <p class="text-lg font-bold tracking-tight text-slate-800">{{ $c->name }}</p>
                </div>
                @if ($isEdit)
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-indigo-600">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-indigo-500"></span> Mode Update
                    </span>
                @endif
            </div>

            {{-- CONFIG GRID --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-2">
                    <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-slate-500">Mekanisme
                        Input</label>
                    <select x-model="inputType" name="input_type" required
                        class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3.5 font-bold text-slate-700 transition-all focus:ring-4 focus:ring-indigo-500/10">
                        <option value="">Pilih Mekanisme</option>
                        <option value="scale">Skala (Pilihan/Likert)</option>
                        <option value="numeric">Input Angka Langsung</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-slate-500">Fungsi
                        Utilitas</label>
                    <select x-model="preferenceType" name="preference_type"
                        class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3.5 font-bold text-slate-700 transition-all focus:ring-4 focus:ring-indigo-500/10">
                        <option value="linear">Linear (Stabil)</option>
                        <option value="concave">Concave (Menurun)</option>
                        <option value="convex">Convex (Meningkat)</option>
                    </select>
                </div>
            </div>

            {{-- RANGE CONFIG --}}
            <div x-show="inputType === 'scale'" x-collapse
                class="grid grid-cols-1 gap-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-5 sm:grid-cols-2">
                <div class="space-y-1.5">
                    <label class="ml-1 text-xs font-bold text-slate-600">Nilai Minimum</label>
                    <input type="number" x-model.number="min" name="scale_min"
                        class="w-full rounded-lg border-slate-200 px-4 py-2.5 font-bold">
                </div>
                <div class="space-y-1.5">
                    <label class="ml-1 text-xs font-bold text-slate-600">Nilai Maksimum</label>
                    <input type="number" x-model.number="max" name="scale_max"
                        class="w-full rounded-lg border-slate-200 px-4 py-2.5 font-bold">
                </div>
            </div>

            {{-- DYNAMIC LIST --}}
            <div class="space-y-4" x-show="inputType === 'scale'" x-collapse>
                <div class="grid grid-cols-1 gap-3">
                    <template x-for="i in range()" :key="i">
                        <div
                            class="group flex flex-col items-stretch gap-4 rounded-xl border border-slate-200 p-3 hover:bg-indigo-50/30 sm:flex-row sm:items-center">
                            <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg bg-slate-800 text-sm font-black text-white"
                                x-text="i"></div>
                            <div class="min-w-0 flex-1">
                                <input type="text" :name="`semantics[${i}]`" x-model="semantics[i]"
                                    placeholder="Label..."
                                    class="w-full border-none bg-transparent py-1 text-sm font-bold text-slate-800 focus:ring-0">
                            </div>
                            <div
                                class="flex items-center gap-3 rounded-lg border border-slate-100 bg-white px-3 py-1.5">
                                <span class="text-[10px] font-bold uppercase text-slate-400">Utility:</span>
                                <input type="number" step="0.01" :name="`utilities[${i}]`" x-model="utilities[i]"
                                    class="w-20 rounded-md border-slate-200 bg-slate-50 py-1 text-center text-xs font-black text-indigo-600 focus:ring-0">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex flex-col items-center justify-end gap-3 border-t border-slate-100 pt-8 sm:flex-row">
                <button type="submit"
                    class="w-full rounded-xl bg-indigo-600 px-10 py-4 text-xs font-black uppercase tracking-widest text-white shadow-lg hover:bg-indigo-700 transition-all sm:w-auto">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Tetapkan Aturan' }}
                </button>
            </div>
        </form>
    </div>
@endif
