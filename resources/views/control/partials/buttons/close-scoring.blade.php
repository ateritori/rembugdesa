<form method="POST" action="{{ route('decision-sessions.close', $decisionSession) }}"
    onsubmit="return confirm('Akhiri penilaian dan hitung hasil agregasi?')">
    @csrf

    @php
        // Logika: Tombol aktif jika jumlah DM yang ditugaskan > 0
        // DAN jumlah DM yang sudah selesai mengisi alternatif sama dengan total DM
        $isReadyToAggregate = $assignedDmCount > 0 && $dmEvaluationsDone === $assignedDmCount;
    @endphp

    <button type="submit" {{ $isReadyToAggregate ? '' : 'disabled' }}
        class="{{ $isReadyToAggregate
            ? 'bg-amber-600 text-white hover:scale-105'
            : 'bg-slate-200 text-slate-400 cursor-not-allowed opacity-70' }} rounded-xl px-10 py-5 text-xs font-black uppercase tracking-widest shadow-lg transition-all">
        Tutup Penilaian →
    </button>
</form>
