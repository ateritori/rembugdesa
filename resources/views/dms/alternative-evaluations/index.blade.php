<form method="POST" action="{{ route('alternative-evaluations.store', $decisionSession->id) }}">
    @csrf

    <div class="w-full max-w-none py-6 px-3 lg:px-4">
        <div class="space-y-6">

            {{-- HEADER: Minimalis & Clean --}}
            <div class="px-2 lg:px-0">
                <h2 class="text-3xl font-light tracking-tight text-slate-900">Penilaian <span
                        class="font-bold">Alternatif</span></h2>
                <p class="mt-3 text-sm text-slate-500">
                    Berikan penilaian Anda terhadap <span class="font-semibold">setiap alternatif</span>
                    sesuai dengan <span class="font-semibold">kriteria yang dinilai</span>.
                    Gunakan skala yang tersedia atau masukkan nilai numerik sesuai kebutuhan.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start w-full">

                @foreach ($alternatives as $a)
                    <section
                        class="flex flex-col h-full rounded-3xl border border-slate-200 bg-white p-5 transition-all duration-300 hover:shadow-lg">

                        {{-- Header Alternatif --}}
                        <div class="mb-4">
                            <div class="flex items-start gap-3">
                                <span
                                    class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-md bg-slate-100 text-slate-700 text-[11px] font-semibold tracking-wide">
                                    {{ $a->code ?? 'A' . $loop->iteration }}
                                </span>
                                <div>
                                    <h3 class="text-base font-extrabold uppercase tracking-widest text-slate-900">
                                        {{ $a->name }}
                                    </h3>
                                    <div class="mt-2 flex flex-wrap gap-2 text-[11px]">
                                        <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 font-medium">
                                            RAB: Rp {{ number_format($a->rab ?? 0, 0, ',', '.') }}
                                        </span>
                                        <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                                            @php
                                                $coverageMap = [
                                                    0 => 'RT',
                                                    25 => 'Antar RT',
                                                    50 => 'Padukuhan',
                                                    75 => 'Antar Padukuhan',
                                                    100 => 'Kalurahan',
                                                ];
                                            @endphp
                                            Cakupan: {{ $coverageMap[$a->coverage] ?? ($a->coverage ?? '-') }}
                                        </span>
                                        <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                                            Penerima:
                                            {{ $a->beneficiaries ? number_format($a->beneficiaries, 0, ',', '.') . ' jiwa' : '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach ($criteria as $c)
                                @php
                                    $rule = $c->scoringRule;
                                    $semanticsParam = $rule?->getParameter('scale_semantics');
                                    $semantics = is_string($semanticsParam)
                                        ? json_decode($semanticsParam, true)
                                        : $semanticsParam ?? [];
                                    $evaluation = $evaluations[$a->id][$c->id] ?? null;
                                @endphp

                                <div class="flex flex-col gap-2">
                                    <div>
                                        <h4
                                            class="text-sm font-bold uppercase tracking-widest text-slate-700 text-center">
                                            {{ $c->name }}
                                        </h4>
                                        <div class="h-px w-10 mx-auto bg-slate-200 mt-1"></div>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-3 w-full">
                                        @if ($rule && $rule->input_type === 'scale')
                                            <div class="grid grid-cols-5 gap-2 w-full">
                                                @foreach ($semantics as $value => $label)
                                                    <label
                                                        class="flex flex-col items-center text-center cursor-pointer h-full">
                                                        <input type="radio"
                                                            name="evaluations[{{ $c->id }}][{{ $a->id }}]"
                                                            value="{{ $value }}" required
                                                            @checked(optional($evaluation)->raw_value == $value) class="peer sr-only">

                                                        <div
                                                            class="flex w-full h-10 items-center justify-center rounded-lg border border-slate-300 bg-white text-sm font-semibold text-slate-600 transition-all shrink-0
                                                            peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white hover:border-slate-400">
                                                            {{ $value }}
                                                        </div>

                                                        <span
                                                            class="mt-1 text-[11px] text-slate-500 text-center leading-tight min-h-[28px]">
                                                            {{ $label }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($rule && $rule->input_type === 'numeric')
                                            <input type="number" step="any" required
                                                name="evaluations[{{ $c->id }}][{{ $a->id }}]"
                                                value="{{ optional($evaluation)->raw_value }}"
                                                class="w-full max-w-xs rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900"
                                                placeholder="Masukkan nilai">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </div>

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
            const name = `evaluations[${criteriaId}][${alternativeId}]`;
            return !!document.querySelector(`input[name="${name}"]:checked`);
        }

        document.addEventListener('DOMContentLoaded', function() {

            // Function to populate inputs from saved draft
            function populateInputs(saved) {
                Object.keys(saved).forEach(cid => {
                    Object.keys(saved[cid]).forEach(aid => {
                        const val = saved[cid][aid];

                        // Update radio buttons
                        const input = document.querySelector(
                            `input[name="evaluations[${cid}][${aid}]"][value="${val}"]`
                        );
                        if (input) input.checked = true;

                        // Update numeric inputs
                        const numericInput = document.querySelector(
                            `input[type="number"][name="evaluations[${cid}][${aid}]"]`
                        );
                        if (numericInput) numericInput.value = val;
                    });
                });
            }

            // Initial load
            const storageKey = `evaluationsDraft_{{ $decisionSession->id }}_{{ auth()->id() }}`;
            let saved = JSON.parse(localStorage.getItem(storageKey) || '{}');
            populateInputs(saved);

            // Save on change
            document.querySelectorAll('input[type="radio"], input[type="number"]').forEach(input => {
                input.addEventListener('change', function() {
                    const nameMatch = this.name.match(/evaluations\[(\d+)\]\[(\d+)\]/);
                    if (!nameMatch) return;
                    const [_, cid, aid] = nameMatch;
                    const val = this.value;

                    saved[cid] = saved[cid] || {};
                    saved[cid][aid] = val;
                    localStorage.setItem(storageKey, JSON.stringify(saved));
                });
            });

            // Listen to storage events (for live update from other tabs)
            window.addEventListener('storage', (event) => {
                if (event.key === storageKey) {
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
