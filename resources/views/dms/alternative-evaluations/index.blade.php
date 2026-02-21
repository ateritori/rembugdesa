<form method="POST" action="{{ route('alternative-evaluations.store', $decisionSession->id) }}">
    @csrf

    <div class="mx-auto w-full space-y-16 py-8 px-4 lg:px-8">

        {{-- HEADER: Minimalis & Clean --}}
        <div class="px-4">
            <h2 class="text-3xl font-light tracking-tight text-slate-900">Penilaian <span
                    class="font-bold">Alternatif</span></h2>
            <p class="mt-3 text-sm text-slate-500">
                Berikan penilaian Anda terhadap <span class="font-semibold">setiap alternatif</span>
                sesuai dengan <span class="font-semibold">kriteria yang dinilai</span>.
                Gunakan skala yang tersedia atau masukkan nilai numerik sesuai kebutuhan.
            </p>
        </div>

        @foreach ($criteria as $c)
            @php
                $rule = $c->scoringRule;
                $semanticsParam = $rule?->getParameter('scale_semantics');
                $semantics = is_string($semanticsParam) ? json_decode($semanticsParam, true) : $semanticsParam ?? [];
            @endphp

            <section class="space-y-6">
                {{-- Nama Kriteria sebagai Divider --}}
                <div
                    class="sticky top-0 z-10 flex items-center gap-3 bg-white/90 py-5 backdrop-blur-md px-4 border-b border-slate-200">
                    <span
                        class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-900 text-[10px] font-bold text-white">
                        {{ $loop->iteration }}
                    </span>
                    <h3 class="text-base font-extrabold uppercase tracking-widest text-slate-900">
                        {{ $c->name }}
                    </h3>
                </div>
                <p class="mt-3 px-4 text-sm adaptive-text-sub">
                    Berikan penilaian untuk <span class="font-semibold adaptive-text-main">masing-masing
                        alternatif</span>
                    berdasarkan <span class="font-semibold adaptive-text-main">kriteria ini</span>.
                    Pilih skor pada skala atau isi nilai numerik sesuai tipe input.
                </p>

                <div class="divide-y divide-slate-100 px-4">
                    @foreach ($alternatives as $a)
                        @php $evaluation = $evaluations[$a->id][$c->id] ?? null; @endphp

                        <div x-data class="group flex flex-col py-6 lg:flex-row lg:items-center lg:justify-between">
                            {{-- Nama Alternatif --}}
                            <div class="mb-4 lg:mb-0 lg:w-1/3">
                                <span
                                    class="text-xs font-semibold uppercase tracking-wider text-slate-400">Alternatif</span>
                                <h4 class="text-lg font-medium adaptive-text-sub transition-colors"
                                    :class="{
                                        'adaptive-text-main font-semibold': hasSelected('{{ $a->id }}',
                                            '{{ $c->id }}')
                                    }">
                                    {{ $a->name }}</h4>
                            </div>

                            {{-- Pilihan Skor: Horizontal Scroll di Mobile, Flex di Desktop --}}
                            <div class="no-scrollbar -mx-4 flex overflow-x-auto px-4 lg:mx-0 lg:px-0">
                                <div class="flex gap-2">

                                    @if ($rule && $rule->input_type === 'scale')
                                        @foreach ($semantics as $value => $label)
                                            <label
                                                class="relative flex flex-col items-center group/item cursor-pointer">
                                                <input type="radio"
                                                    name="evaluations[{{ $a->id }}][{{ $c->id }}]"
                                                    value="{{ $value }}" required @checked(optional($evaluation)->raw_value == $value)
                                                    class="peer sr-only">

                                                <div
                                                    class="flex h-12 w-12 items-center justify-center rounded-full border-2 border-slate-100 bg-white text-sm font-bold text-slate-400 transition-all
                                                    peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white
                                                    peer-hover:border-slate-300 peer-focus:ring-2 peer-focus:ring-slate-900 peer-focus:ring-offset-2">
                                                    {{ $value }}
                                                </div>

                                                <span
                                                    class="mt-2 whitespace-nowrap text-[10px] font-medium uppercase tracking-tighter text-slate-400 transition-colors peer-checked:text-slate-900">
                                                    {{ $label }}
                                                </span>
                                            </label>
                                        @endforeach
                                    @endif

                                    @if ($rule && $rule->input_type === 'numeric')
                                        <input type="number" step="any" required
                                            name="evaluations[{{ $a->id }}][{{ $c->id }}]"
                                            value="{{ optional($evaluation)->raw_value }}"
                                            class="w-40 rounded-lg border border-slate-300 px-4 py-3 text-sm
                                                   focus:border-slate-900 focus:ring-slate-900"
                                            placeholder="Masukkan nilai numerik">
                                    @endif

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        {{-- FOOTER: Floating Action Button Style --}}
        <div class="fixed bottom-8 right-8 z-50">
            <button type="submit"
                class="flex items-center gap-3 rounded-full bg-slate-900 px-8 py-4 text-xs font-bold uppercase tracking-[0.2em] text-white shadow-2xl transition-all hover:scale-105 hover:bg-black active:scale-95">
                <span>Simpan Evaluasi</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </div>

    <script>
        function hasSelected(alternativeId, criteriaId) {
            const name = `evaluations[${alternativeId}][${criteriaId}]`;
            return !!document.querySelector(`input[name="${name}"]:checked`);
        }

        document.addEventListener('DOMContentLoaded', function() {

            // Function to populate inputs from saved draft
            function populateInputs(saved) {
                Object.keys(saved).forEach(aid => {
                    Object.keys(saved[aid]).forEach(cid => {
                        const val = saved[aid][cid];

                        // Update radio buttons
                        const input = document.querySelector(
                            `input[name="evaluations[${aid}][${cid}]"][value="${val}"]`
                        );
                        if (input) input.checked = true;

                        // Update numeric inputs
                        const numericInput = document.querySelector(
                            `input[type="number"][name="evaluations[${aid}][${cid}]"]`
                        );
                        if (numericInput) numericInput.value = val;
                    });
                });
            }

            // Initial load
            let saved = JSON.parse(localStorage.getItem('evaluationsDraft') || '{}');
            populateInputs(saved);

            // Save on change
            document.querySelectorAll('input[type="radio"], input[type="number"]').forEach(input => {
                input.addEventListener('change', function() {
                    const nameMatch = this.name.match(/evaluations\[(\d+)\]\[(\d+)\]/);
                    if (!nameMatch) return;
                    const [_, aid, cid] = nameMatch;
                    const val = this.value;

                    saved[aid] = saved[aid] || {};
                    saved[aid][cid] = val;
                    localStorage.setItem('evaluationsDraft', JSON.stringify(saved));
                });
            });

            // Listen to storage events (for live update from other tabs)
            window.addEventListener('storage', (event) => {
                if (event.key === 'evaluationsDraft') {
                    saved = JSON.parse(event.newValue || '{}');
                    populateInputs(saved);
                }
            });

        });
    </script>
</form>

<style>
    /* Menghilangkan scrollbar tapi tetap bisa di-scroll */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
