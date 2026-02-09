@if ($decisionSession->status === 'draft')
    @php
        $isEdit = $rule !== null;
        // Data lama dari database untuk mode Edit
        $oldSemantics = $rule?->getParameter('scale_semantics') ?? [];
        $oldUtilities = $rule?->getParameter('scale_utilities') ?? [];
    @endphp

    <div x-data="{ openScoring: {{ $isEdit ? 'true' : 'false' }} }">
        <form id="scoringForm" x-show="openScoring" x-cloak x-transition method="POST"
            action="{{ $isEdit ? route('criteria.scoring.update', [$c->id, $rule->id]) : route('criteria.scoring.store', $c->id) }}"
            class="mt-6 p-8 rounded-[2rem] border border-[#f5f5f4] bg-white shadow-xl space-y-8 relative overflow-hidden">

            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="flex items-center justify-between border-b border-[#f5f5f4] pb-5">
                <h4 class="text-sm font-[900] text-[#2d1a12] uppercase tracking-tighter">Aturan Penilaian:
                    {{ $c->name }}</h4>
            </div>

            {{-- Konfigurasi Tipe --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-[900] uppercase opacity-50">Mekanisme Input</label>
                    <select id="inputType" name="input_type" onchange="toggleInputView()"
                        class="w-full bg-[#fafaf9] border-[#f5f5f4] rounded-xl text-sm font-bold py-3.5">
                        <option value="">Pilih Jenis</option>
                        <option value="scale" {{ ($rule->input_type ?? '') == 'scale' ? 'selected' : '' }}>Skala
                            (Terukur)</option>
                        <option value="numeric" {{ ($rule->input_type ?? '') == 'numeric' ? 'selected' : '' }}>Angka
                            Bebas (Raw)</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-[900] uppercase opacity-50">Fungsi Utilitas</label>
                    <select id="utilityType" name="preference_type" onchange="renderUtilityItems()"
                        class="w-full bg-[#fafaf9] border-[#f5f5f4] rounded-xl text-sm font-bold py-3.5">
                        <option value="linear" {{ ($rule->preference_type ?? '') == 'linear' ? 'selected' : '' }}>Linear
                            (Stabil)</option>
                        <option value="concave" {{ ($rule->preference_type ?? '') == 'concave' ? 'selected' : '' }}>
                            Konkaf (Sensitif Awal)</option>
                        <option value="convex" {{ ($rule->preference_type ?? '') == 'convex' ? 'selected' : '' }}>
                            Konveks (Sensitif Akhir)</option>
                    </select>
                </div>
            </div>

            {{-- Range Control --}}
            <div id="rangeControl"
                class="hidden p-6 bg-[#fafaf9] rounded-2xl border border-[#f5f5f4] grid grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[9px] font-[900] uppercase opacity-50">Min Skala</label>
                    <input type="number" id="scaleMin" name="scale_min" value="{{ $dbRange['min'] ?? 1 }}"
                        oninput="renderUtilityItems()" class="w-full border-[#f5f5f4] rounded-lg text-sm font-black">
                </div>
                <div class="space-y-2">
                    <label class="text-[9px] font-[900] uppercase opacity-50">Max Skala</label>
                    <input type="number" id="scaleMax" name="scale_max" value="{{ $dbRange['max'] ?? 5 }}"
                        oninput="renderUtilityItems()" class="w-full border-[#f5f5f4] rounded-lg text-sm font-black">
                </div>
            </div>

            {{-- Container untuk List Dinamis --}}
            <div id="utilityContainer" class="space-y-4"></div>

            <div class="flex items-center justify-end gap-4 pt-8 border-t border-[#f5f5f4]">
                <button type="button" @click="openScoring = false"
                    class="px-6 py-3 text-[10px] font-black uppercase text-[#a8a29e]">Batal</button>
                <button type="submit"
                    class="px-10 py-4 bg-[#2d1a12] hover:bg-[#7f1d1d] text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Tetapkan Aturan' }}
                </button>
            </div>
        </form>
    </div>

    <script>
        // Data awal dari PHP ke JS
        const initialSemantics = @json($oldSemantics);
        const initialUtilities = @json($oldUtilities);
        let firstLoad = true;

        function toggleInputView() {
            const type = document.getElementById('inputType').value;
            const rangeControl = document.getElementById('rangeControl');
            const container = document.getElementById('utilityContainer');

            if (type === 'scale') {
                rangeControl.classList.remove('hidden');
                renderUtilityItems();
            } else {
                rangeControl.classList.add('hidden');
                container.innerHTML = '';
            }
        }

        function renderUtilityItems() {
            const min = parseInt(document.getElementById('scaleMin').value);
            const max = parseInt(document.getElementById('scaleMax').value);
            const type = document.getElementById('utilityType').value;
            const container = document.getElementById('utilityContainer');

            if (isNaN(min) || isNaN(max) || min > max) {
                container.innerHTML = '<p class="text-xs text-center opacity-50 font-bold">Range tidak valid</p>';
                return;
            }

            let html = '';
            const range = max - min;

            for (let i = min; i <= max; i++) {
                // Ambil data lama jika tersedia dan ini adalah loading pertama
                let label = (firstLoad && initialSemantics[i]) ? initialSemantics[i] : '';
                let utilVal = (firstLoad && initialUtilities[i]) ? initialUtilities[i] : null;

                // Jika utilVal kosong (bukan 0), hitung otomatis
                if (utilVal === null) {
                    const x = range === 0 ? 1 : (i - min) / range;
                    let calc = 0;
                    if (type === 'concave') calc = Math.sqrt(x);
                    else if (type === 'convex') calc = Math.pow(x, 2);
                    else calc = x; // linear
                    utilVal = calc.toFixed(2);
                }

                html += `
                    <div class="flex items-center gap-4 bg-white p-3 rounded-2xl border border-[#f5f5f4] shadow-sm animate-in fade-in duration-300">
                        <div class="w-10 h-10 rounded-xl bg-[#2d1a12] text-white flex items-center justify-center font-black text-xs">${i}</div>
                        <div class="flex-1">
                            <input type="text" name="semantics[${i}]" value="${label}" placeholder="Label..."
                                class="w-full border-none bg-transparent text-sm font-bold text-[#2d1a12] focus:ring-0">
                        </div>
                        <div class="w-24">
                            <input type="number" step="0.01" name="utilities[${i}]" value="${utilVal}"
                                class="w-full border-none bg-[#fafaf9] rounded-lg text-center font-black text-xs text-[#7f1d1d] py-2 focus:ring-1 focus:ring-[#7f1d1d]">
                        </div>
                    </div>
                `;
            }
            container.innerHTML = html;

            // Setelah render pertama selesai, set firstLoad ke false
            // agar perubahan input type/range berikutnya menghitung ulang nilai otomatis
            if (firstLoad) {
                // Cek apakah data benar-benar ada, jika ya biarkan firstLoad tetap true sejenak
                // atau matikan setelah render inisial berhasil
                setTimeout(() => {
                    firstLoad = false;
                }, 100);
            }
        }

        // Jalankan saat dokumen siap
        document.addEventListener('DOMContentLoaded', () => {
            toggleInputView();
        });
    </script>
@endif
