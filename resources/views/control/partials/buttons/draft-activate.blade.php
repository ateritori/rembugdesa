<form method="POST" action="{{ route('decision-sessions.activate', $decisionSession->id) }}"
    onsubmit="return confirm('Aktifkan sesi?')">
    @csrf
    @method('PATCH')

    <button type="submit" {{ $canActivate ? '' : 'disabled' }}
        class="rounded-xl bg-blue-600 px-10 py-5 text-xs font-black uppercase tracking-widest text-white shadow-lg transition-all hover:scale-105 disabled:opacity-20 disabled:grayscale">
        Buka Sesi →
    </button>
</form>
