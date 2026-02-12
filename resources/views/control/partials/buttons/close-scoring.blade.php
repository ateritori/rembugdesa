<form method="POST" action="{{ route('decision-sessions.aggregate', $decisionSession->id) }}"
    onsubmit="return confirm('Akhiri penilaian?')">
    @csrf
    @method('PATCH')

    <button type="submit"
        class="rounded-xl bg-amber-600 px-10 py-5 text-xs font-black uppercase tracking-widest text-white shadow-lg transition-all hover:scale-105">
        Tutup Penilaian →
    </button>
</form>
