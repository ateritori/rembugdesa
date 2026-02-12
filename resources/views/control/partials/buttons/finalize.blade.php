<form method="POST" action="{{ route('decision-sessions.finalize', $decisionSession->id) }}"
    onsubmit="return confirm('Finalisasi keputusan?')">
    @csrf
    @method('PATCH')

    <button type="submit"
        class="rounded-xl bg-emerald-600 px-10 py-5 text-xs font-black uppercase tracking-widest text-white shadow-lg transition-all hover:scale-105">
        Finalisasi Keputusan →
    </button>
</form>
