<aside class="w-64 bg-primary text-white min-h-screen">
    <div class="p-4 font-semibold border-b border-white/20">
        SPK Desa
    </div>

    <nav class="p-4 space-y-2 text-sm">
        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded bg-white/20">
            Dashboard
        </a>

        @role('admin')
            <a href="{{ route('decision-sessions.index') }}" class="block px-3 py-2 rounded hover:bg-white/10">
                Sesi Keputusan
            </a>
        @endrole
    </nav>
</aside>
