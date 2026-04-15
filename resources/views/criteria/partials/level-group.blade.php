<div class="space-y-3">
    <h2 class="text-xs font-black uppercase tracking-wider text-slate-500 mb-2">
        Level {{ $level }}
    </h2>

    @foreach ($items as $c)
        @include('criteria.partials.criteria-card', [
            'c' => $c,
            'level' => $level,
            'rule' => $scoringRules->get($c->id),
            'isLocked' => $c->ui_locked,
        ])
    @endforeach
</div>
