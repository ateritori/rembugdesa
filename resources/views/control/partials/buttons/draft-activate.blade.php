<form method="POST" action="{{ route('decision-sessions.activate', $decisionSession->id) }}"
  onsubmit="return confirm('Aktifkan sesi?')">
  @csrf
  @method('PATCH')

  <button type="submit" {{ $canActivate ?? false ? '' : 'disabled' }}
    class="rounded-xl bg-blue-600 px-10 py-5 text-xs font-black uppercase tracking-widest text-white shadow-lg transition-all hover:scale-105 disabled:cursor-not-allowed disabled:opacity-30 disabled:grayscale">
    {{ $canActivate ?? false ? 'Buka Sesi →' : 'Data Belum Lengkap' }}
  </button>
</form>
