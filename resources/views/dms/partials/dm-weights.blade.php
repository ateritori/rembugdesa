@php
    $criteriaWeights = $criteriaWeights ?? null;
    $criterias = $criterias ?? collect();
@endphp

@if (!$criteriaWeights)
    <div class="flex flex-col items-center justify-center p-10 border-2 border-dashed border-app rounded-3xl opacity-50">
        <svg class="w-12 h-12 mb-3 text-app" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <p class="text-xs font-black uppercase tracking-widest text-app">Bobot belum tersedia</p>
        <p class="text-[10px] text-app opacity-60">Silakan simpan penilaian kriteria terlebih dahulu.</p>
    </div>
@else
    <div class="space-y-6">
        {{-- Header Status --}}
        <div class="flex items-center justify-between bg-app/20 p-4 rounded-2xl border border-app">
            <div>
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-primary">
                    Prioritas Kriteria
                </h2>
                <p class="text-[10px] font-bold text-app opacity-50 mt-1 uppercase">
                    Hasil kalkulasi Eigenvector
                </p>
            </div>
            <div class="text-right">
                <span class="text-[9px] font-black uppercase opacity-40 block tracking-tighter">Consistency Ratio</span>
                <span
                    class="text-sm font-black {{ $criteriaWeights->cr <= 0.1 ? 'text-emerald-500' : 'text-rose-500' }}">
                    {{ number_format($criteriaWeights->cr, 4) }}
                </span>
            </div>
        </div>

        {{-- Visual List --}}
        <div class="space-y-3">
            @php
                $sortedWeights = collect($criteriaWeights->weights)->sortDesc();
                $maxWeight = $sortedWeights->first() ?: 1;
            @endphp

            @foreach ($sortedWeights as $criteriaId => $weight)
                @php
                    $criteria = $criterias->firstWhere('id', $criteriaId);
                    $percentage = $weight * 100;
                    // Skala relatif untuk progress bar agar kriteria tertinggi selalu penuh secara visual
                    $visualWidth = ($weight / $maxWeight) * 100;
                @endphp

                <div
                    class="relative group p-4 rounded-2xl border-2 border-app bg-app/10 hover:border-primary/30 transition-all duration-300">
                    <div class="relative z-10 flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-xl bg-primary flex items-center justify-center shadow-lg shadow-primary/20 rotate-3 group-hover:rotate-0 transition-transform">
                                <span class="text-xs font-black text-white italic">#{{ $loop->iteration }}</span>
                            </div>
                            <span class="text-sm font-black text-app uppercase tracking-tight">
                                {{ $criteria->name ?? 'Unknown' }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-base font-black text-primary">
                                {{ number_format($percentage, 1) }}%
                            </span>
                        </div>
                    </div>

                    {{-- Progress Track --}}
                    <div class="h-2 w-full bg-app/40 rounded-full overflow-hidden border border-white/5">
                        {{-- Progress Fill --}}
                        <div class="h-full bg-gradient-to-r from-primary to-primary/60 rounded-full transition-all duration-1000"
                            style="width: {{ $visualWidth }}%">
                        </div>
                    </div>

                    {{-- Detail angka desimal kecil --}}
                    <div class="mt-1 text-[9px] font-bold text-app opacity-30 text-right uppercase tracking-tighter">
                        Absolute Value: {{ number_format($weight, 4) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
