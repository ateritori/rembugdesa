<aside class="sidebar-container w-64 bg-primary text-white min-h-screen flex-shrink-0">
    <div class="p-6 font-black tracking-widest border-b border-white/10 uppercase text-sm">
        SPK Desa
    </div>

    <nav class="p-4 space-y-2 text-sm">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Dashboard</span>
        </a>

        @role('admin')
            {{-- Sesi Keputusan --}}
            <a href="{{ route('decision-sessions.index') }}"
                class="sidebar-link {{ request()->routeIs('decision-sessions.*') ? 'sidebar-link-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Sesi Keputusan</span>
            </a>
        @endrole
    </nav>
</aside>
