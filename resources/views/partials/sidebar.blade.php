{{-- 1. Hapus bg-primary, biarkan .sidebar-container di CSS yang menentukan warna gelapnya --}}
<aside class="sidebar-container w-64 text-white min-h-screen flex-shrink-0">

    <div class="p-6 font-black tracking-widest border-b border-white/10 uppercase text-sm flex items-center gap-3">
        {{-- 2. Tambahkan aksen warna preset di logo agar tetap ada identitas warna --}}
        <div class="w-2 h-6 bg-primary rounded-full shadow-[0_0_10px_var(--primary)]"></div>
        <span>SPK Desa</span>
    </div>

    <nav class="p-4 space-y-1.5 text-sm">
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
            {{-- 3. Perluas pengecekan route agar menu tetap 'active' saat mengelola kriteria/alternatif --}}
            <a href="{{ route('decision-sessions.index') }}"
                class="sidebar-link {{ request()->is('decision-sessions*', 'criteria*', 'alternatives*', 'dms*') ? 'sidebar-link-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Sesi Keputusan</span>
            </a>
        @endrole
    </nav>
</aside>
