<header
    class="bg-card border-b border-app px-6 py-3 flex items-center justify-between sticky top-0 z-40 backdrop-blur-md bg-card/80">

    {{-- LEFT: Branding & Mobile Toggle --}}
    <div class="flex items-center gap-4">
        {{-- Mobile menu button --}}
        <button onclick="toggleSidebar()"
            class="lg:hidden p-2 rounded-xl border border-app text-app hover:bg-primary/10 hover:text-primary transition-all active:scale-90">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2.5">
                <path class="menu-open-icon" stroke-linecap="round" stroke-linejoin="round"
                    d="M4 6h16M4 12h16M4 18h16" />
                <path class="menu-close-icon hidden" stroke-linecap="round" stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h1 class="font-black text-sm text-app tracking-tight uppercase opacity-80 hidden sm:block">
            Dashboard Sesi Keputusan
        </h1>
    </div>

    {{-- RIGHT: Controls --}}
    <div class="flex items-center gap-3">

        {{-- Theme palette toggle (mobile trigger) --}}
        <button onclick="document.getElementById('theme-palette').classList.toggle('hidden')"
            class="md:hidden p-2 rounded-xl border border-app text-app hover:bg-primary/10 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 3c5 0 9 3.6 9 8 0 2.8-2.2 4-4 4h-1a2 2 0 00-2 2v1a3 3 0 01-6 0v-1a2 2 0 00-2-2H7c-1.8 0-4-1.2-4-4 0-4.4 4-8 9-8z" />
            </svg>
        </button>

        {{-- Preset Colors Palette --}}
        <div id="theme-palette"
            class="hidden absolute top-full right-6 mt-2 bg-card border border-app rounded-2xl shadow-2xl p-4
                    md:static md:mt-0 md:shadow-none md:border-0 md:bg-transparent
                    md:flex items-center gap-3 z-50 animate-in fade-in zoom-in-95 duration-200">

            <p class="text-[10px] font-black uppercase tracking-widest opacity-40 md:hidden mb-2">Pilih Warna Preset</p>

            <div class="flex items-center gap-2.5">
                <button onclick="setThemePreset('blue')" title="Blue Preset"
                    class="w-5 h-5 rounded-full bg-blue-600 border-2 border-white dark:border-slate-800 shadow-sm hover:scale-125 transition-transform active:scale-95"></button>
                <button onclick="setThemePreset('green')" title="Green Preset"
                    class="w-5 h-5 rounded-full bg-emerald-700 border-2 border-white dark:border-slate-800 shadow-sm hover:scale-125 transition-transform active:scale-95"></button>
                <button onclick="setThemePreset('brown')" title="Brown Preset"
                    class="w-5 h-5 rounded-full bg-amber-900 border-2 border-white dark:border-slate-800 shadow-sm hover:scale-125 transition-transform active:scale-95"></button>
                <button onclick="setThemePreset('gray')" title="Gray Preset"
                    class="w-5 h-5 rounded-full bg-slate-600 border-2 border-white dark:border-slate-800 shadow-sm hover:scale-125 transition-transform active:scale-95"></button>
            </div>
        </div>

        <div class="h-6 w-px bg-app mx-1 hidden md:block"></div>

        {{-- Dark / Light Mode Toggle --}}
        <button onclick="toggleThemeMode()"
            class="p-2 rounded-xl border border-app text-app hover:bg-primary/10 hover:text-primary transition-all group"
            title="Toggle Theme">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:rotate-12 transition-transform"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        {{-- User Info --}}
        <div class="hidden lg:flex flex-col items-end leading-none ml-2">
            <span
                class="text-[10px] font-black text-app uppercase tracking-widest">{{ auth()->user()->name ?? 'Administrator' }}</span>
            <span class="text-[9px] text-primary font-bold uppercase tracking-tighter">Online</span>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="ml-2">
            @csrf
            <button type="submit" class="p-2 text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all group"
                title="Logout">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </button>
        </form>
    </div>
</header>
