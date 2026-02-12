<form method="POST" action="{{ route('decision-sessions.activate', $decisionSession->id) }}"
    onsubmit="return confirm('Buka sesi penilaian alternatif?')">
    @csrf
    @method('PATCH')

    <button type="submit" {{ $assignedDmCount > 0 && $dmPairwiseDone === $assignedDmCount ? '' : 'disabled' }}
        class="rounded-xl px-10 py-5 text-xs font-black uppercase tracking-widest shadow-lg transition-all
            {{ $assignedDmCount > 0 && $dmPairwiseDone === $assignedDmCount
                ? 'bg-indigo-600 text-white hover:scale-105'
                : 'bg-slate-200 text-slate-400 cursor-not-allowed opacity-70' }}">
        Buka Penilaian Alternatif →
    </button>
</form>
