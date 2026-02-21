@php
    // Definisikan status untuk mempermudah pembacaan
    $status = $decisionSession->status ?? 'draft';
    $isClosed = $status === 'closed';

    // Syarat Edit: Tampilkan jika status adalah 'configured' atau 'scoring'
    $canEdit = in_array($status, ['configured', 'scoring']);

    // Data Weights
    $criteriaWeights = $criteriaWeights ?? null;
    $criterias = $criterias ?? collect();
@endphp

<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">

    {{-- 1. HEADER STATUS & CONSISTENCY RATIO (Locked SMART Style) --}}
    @if ($criteriaWeights)
        <div class="px-2 flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Prioritas Kriteria Anda</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $criteriaWeights->cr <= 0.1 ? 'bg-primary' : 'bg-rose-500' }} opacity-75"></span>
                        <span
                            class="relative inline-flex rounded-full h-2 w-2 {{ $criteriaWeights->cr <= 0.1 ? 'bg-primary' : 'bg-rose-500' }}"></span>
                    </span>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        {{ $criteriaWeights->cr <= 0.1 ? 'Matriks Konsisten & Terverifikasi' : 'Matriks Perlu Revisi (CR > 0.1)' }}
                    </p>
                </div>
            </div>

            {{-- Consistency Ratio Badge --}}
            <div
                class="bg-white border border-slate-200 rounded-2xl px-5 py-3 shadow-sm flex items-center gap-4 group hover:border-primary/50 transition-colors">
                <div class="text-right">
                    <span
                        class="block text-[9px] font-black uppercase tracking-widest text-slate-400 leading-none">Consistency
                        Ratio (CR)</span>
                    <span
                        class="font-mono text-xl font-black {{ $criteriaWeights->cr <= 0.1 ? 'text-primary' : 'text-rose-500' }}">
                        {{ number_format($criteriaWeights->cr, 4) }}
                    </span>
                </div>
                <div
                    class="p-2 rounded-xl {{ $criteriaWeights->cr <= 0.1 ? 'bg-emerald-50 text-emerald-500' : 'bg-rose-50 text-rose-500' }}">
                    @if ($criteriaWeights->cr <= 0.1)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
            </div>
        </div>

        {{-- 2. GRID LIST BOBOT (Masonry Flow Style) --}}
        @php
            $weightsData = is_array($criteriaWeights->weights)
                ? $criteriaWeights->weights
                : json_decode($criteriaWeights->weights, true);
            $sortedWeights = collect($weightsData ?? [])->sortDesc();
            $maxWeight = $sortedWeights->first() ?: 1;
            $rank = 1;
        @endphp

        <div class="columns-1 lg:columns-2 gap-6 space-y-6">
            @foreach ($sortedWeights as $criteriaId => $weight)
                @php
                    $criteria = $criterias->firstWhere('id', $criteriaId);
                    $percentage = $weight * 100;
                    $visualWidth = ($weight / $maxWeight) * 100;

                    $rankStyle = match ($rank) {
                        1 => 'bg-amber-500 text-white shadow-amber-200',
                        2 => 'bg-slate-400 text-white shadow-slate-100',
                        3 => 'bg-orange-400 text-white shadow-orange-100',
                        default => 'bg-slate-100 text-slate-500 border border-slate-200',
                    };
                @endphp

                <div
                    class="break-inside-avoid relative rounded-3xl border border-slate-200 bg-white p-5 transition-all duration-300 hover:shadow-lg mb-6 group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl font-black text-xs shadow-sm {{ $rankStyle }}">
                                {{ $rank++ }}
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">
                                    Prioritas Kriteria</h3>
                                <p
                                    class="text-sm font-black text-slate-800 uppercase tracking-tight leading-tight break-words">
                                    {{ $criteria->name ?? 'Kriteria #' . $criteriaId }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-primary font-mono text-xl font-black">
                                {{ number_format($percentage, 1) }}<span class="text-xs ml-0.5">%</span>
                            </span>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="relative h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="absolute left-0 top-0 h-full rounded-full bg-primary transition-all duration-[1s] ease-out shadow-[0_0_8px_rgba(var(--color-primary),0.3)]"
                            style="width: {{ $visualWidth }}%">
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between border-t border-slate-50 pt-3">
                        <span
                            class="text-[9px] font-bold uppercase tracking-widest text-slate-400 italic">Eigenvector</span>
                        <span class="rounded-md bg-slate-50 px-2 py-0.5 font-mono text-[10px] font-bold text-slate-600">
                            {{ number_format($weight, 4) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- 3. NAVIGASI & ACTION (Clean Locked Style) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 pt-4">

        @if ($canEdit)
            <a href="{{ route('decision-sessions.pairwise.index', [
                'decisionSession' => $decisionSession->id,
                'tab' => 'penilaian-kriteria',
                'edit' => 1,
            ]) }}"
                class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 transition-all hover:border-primary/50 hover:bg-primary/[0.02] shadow-sm">

                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-primary group-hover:text-white transition-all">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>

                <div class="flex-1 min-w-0">
                    <span
                        class="block text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-primary transition-colors">Revisi
                        Tersedia</span>
                    <span class="text-xs font-black text-slate-700 uppercase tracking-tight block">Buka Form
                        Perbandingan</span>
                </div>

                <svg class="w-4 h-4 text-slate-300 group-hover:text-primary group-hover:translate-x-1 transition-all"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @endif

        {{-- STATUS INFO BOX --}}
        <div
            class="flex items-center gap-4 rounded-2xl border {{ $canEdit ? 'border-amber-100 bg-amber-50/50' : 'border-emerald-100 bg-emerald-50/50' }} p-4 {{ !$canEdit ? 'col-span-full' : '' }}">
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $canEdit ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600' }}">
                @if ($canEdit)
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                @endif
            </div>
            <div>
                <h4
                    class="text-[10px] font-black uppercase tracking-widest {{ $canEdit ? 'text-amber-800' : 'text-emerald-800' }}">
                    {{ $canEdit ? 'Konfigurasi Terbuka' : 'Sesi Terkunci' }}
                </h4>
                <p
                    class="text-[10px] font-bold {{ $canEdit ? 'text-amber-600/80' : 'text-emerald-600/80' }} leading-tight uppercase tracking-tighter italic">
                    {{ $canEdit ? 'Input penilaian masih dapat disesuaikan ulang.' : 'Data sudah bersifat final dan sedang diproses.' }}
                </p>
            </div>
        </div>
    </div>
</div>
