@php
    $score = $score ?? 0;

    if ($score >= 80) {
        $class = 'bg-emerald-500/10 text-emerald-600';
        $label = 'Excellent';
    } elseif ($score >= 68) {
        $class = 'bg-primary/10 text-primary';
        $label = 'Good';
    } elseif ($score >= 50) {
        $class = 'bg-amber-500/10 text-amber-600';
        $label = 'OK';
    } else {
        $class = 'bg-rose-500/10 text-rose-600';
        $label = 'Poor';
    }
@endphp

<span
    class="inline-flex items-center gap-2 rounded-lg px-3 py-1 text-[10px] font-black uppercase tracking-wider {{ $class }}">
    {{ number_format($score, 2) }}
    <span class="opacity-60">({{ $label }})</span>
</span>
