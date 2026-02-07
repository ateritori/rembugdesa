<header class="bg-card border-b border-app px-4 py-3 flex items-center justify-between">

    {{-- LEFT --}}
    <div class="flex items-center gap-3">
        {{-- Mobile menu --}}
        <button onclick="toggleSidebar()" class="md:hidden px-2 py-1 rounded border border-app text-app">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <h1 class="font-semibold text-sm text-app">
            Dashboard Sesi Keputusan
        </h1>
    </div>

    {{-- RIGHT --}}
    <div class="relative flex items-center gap-4">

        {{-- Theme palette toggle (mobile) --}}
        <button onclick="document.getElementById('theme-palette').classList.toggle('hidden')"
            class="md:hidden px-2 py-1 rounded border border-app text-app" title="Theme">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 3c5 0 9 3.6 9 8 0 2.8-2.2 4-4 4h-1a2 2 0 00-2 2v1a3 3 0 01-6 0v-1a2 2 0 00-2-2H7c-1.8 0-4-1.2-4-4 0-4.4 4-8 9-8z" />
            </svg>
        </button>

        {{-- Preset Colors --}}
        <div id="theme-palette"
            class="hidden absolute top-full right-0 mt-2 bg-card border border-app rounded shadow-md p-3
                    md:static md:mt-0 md:shadow-none md:border-0 md:bg-transparent
                    md:flex items-center gap-2">
            <button onclick="setThemePreset('gray')"
                class="w-4 h-4 rounded-full bg-gray-600 border border-app"></button>
            <button onclick="setThemePreset('green')"
                class="w-4 h-4 rounded-full bg-green-700 border border-app"></button>
            <button onclick="setThemePreset('blue')"
                class="w-4 h-4 rounded-full bg-blue-700 border border-app"></button>
            <button onclick="setThemePreset('brown')"
                class="w-4 h-4 rounded-full bg-amber-800 border border-app"></button>
        </div>

        {{-- Dark / Light --}}
        <button onclick="toggleThemeMode()" class="px-2 py-1 rounded border border-app text-app" title="Dark / Light">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z" />
            </svg>
        </button>

        {{-- User --}}
        <span class="hidden sm:block text-sm text-app">
            {{ auth()->user()->name ?? 'User' }}
        </span>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                Logout
            </button>
        </form>

    </div>
</header>
