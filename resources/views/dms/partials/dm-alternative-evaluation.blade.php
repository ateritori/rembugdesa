@php
    $alternatives = $alternatives ?? collect();
    $criteria = $criteria ?? collect();
    $scoringRules = $scoringRules ?? collect();
    $existingEvaluations = $existingEvaluations ?? collect();
@endphp

<div class="space-y-6">

    <h2 class="text-lg font-bold adaptive-text-main">
        Penilaian Alternatif
    </h2>

    @if ($alternatives->isEmpty())
        <p class="text-sm adaptive-text-sub italic">
            Belum ada alternatif yang dapat dinilai.
        </p>
        @return
    @endif

    @foreach ($alternatives as $alternative)
        <div class="adaptive-card p-4 space-y-4">

            <h3 class="font-semibold adaptive-text-main">
                {{ $alternative->name }}
            </h3>

            @foreach ($criteria as $criteria)
                @php
                    $rule = $scoringRules[$criteria->id] ?? null;
                    if (!$rule) {
                        continue;
                    }

                    $key = $alternative->id . '-' . $criteria->id;
                    $existing = $existingEvaluations[$key] ?? null;
                @endphp

                <form method="POST" action="{{ route('alternative-evaluations.store', $decisionSession->id) }}"
                    class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                    @csrf

                    <input type="hidden" name="alternative_id" value="{{ $alternative->id }}">
                    <input type="hidden" name="criteria_id" value="{{ $criteria->id }}">

                    <label class="text-sm font-medium adaptive-text-main w-full sm:w-1/4">
                        {{ $criteria->name }}
                    </label>

                    {{-- ORDINAL --}}
                    @if ($rule->input_type === 'scale')
                        @php
                            $min = (int) $rule->getParameter('scale_min');
                            $max = (int) $rule->getParameter('scale_max');
                            $labels = (array) ($rule->getParameter('scale_semantics') ?? []);
                        @endphp

                        <select name="raw_value" class="w-full sm:w-1/3 border rounded px-3 py-2 text-sm" required>
                            <option value="">Pilih</option>
                            @for ($i = $min; $i <= $max; $i++)
                                <option value="{{ $i }}" @selected($existing && (int) $existing->raw_value === $i)>
                                    {{ $i }}
                                    {{ isset($labels[$i]) ? '– ' . $labels[$i] : '' }}
                                </option>
                            @endfor
                        </select>
                    @endif

                    {{-- NUMERIC --}}
                    @if ($rule->input_type === 'numeric')
                        @php
                            $unit = $rule->getParameter('unit');
                            $minVal = $rule->getParameter('value_min');
                            $maxVal = $rule->getParameter('value_max');
                        @endphp

                        <div class="flex items-center gap-2 w-full sm:w-1/3">
                            <input type="number" name="raw_value" step="any" min="{{ $minVal }}"
                                max="{{ $maxVal }}" value="{{ $existing?->raw_value }}"
                                class="flex-1 border rounded px-3 py-2 text-sm" required>
                            @if ($unit)
                                <span class="text-xs adaptive-text-sub">{{ $unit }}</span>
                            @endif
                        </div>
                    @endif

                    <button type="submit"
                        class="px-4 py-2 text-xs font-bold rounded bg-primary text-white hover:opacity-90">
                        Simpan
                    </button>
                </form>
            @endforeach
        </div>
    @endforeach
</div>
