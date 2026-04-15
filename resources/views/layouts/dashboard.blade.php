<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    {{-- SCRIPT PENCEGAH FLASH (ANTI-KELIP) --}}
    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.removeAttribute('data-theme');
            }
            const preset = localStorage.getItem('theme-preset') || 'blue';
            document.documentElement.setAttribute('data-theme-preset', preset);
        })();
    </script>

    {{-- Vite: Vital untuk Asset Laravel Modern --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-app text-app min-h-screen font-sans antialiased">

    {{-- MODERN FLOATING TOAST SYSTEM --}}
    {{-- Posisi diturunkan sedikit (top-24) agar tidak menabrak header di mobile --}}
    <div class="pointer-events-none fixed right-6 top-24 z-[100] flex w-full max-w-sm flex-col gap-3 sm:top-6">

        {{-- Toast Error --}}
        @if (session('error'))
            <div x-data="{ show: true, progress: 100 }" x-init="let interval = setInterval(() => {
                progress -= 1;
                if (progress <= 0) {
                    show = false;
                    clearInterval(interval);
                }
            }, 30);" x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="group pointer-events-auto relative overflow-hidden rounded-2xl border border-rose-500/20 bg-white/90 p-4 shadow-2xl backdrop-blur-xl">

                <div class="flex items-start gap-4">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-500 text-white shadow-lg shadow-rose-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-rose-600">Terjadi Kesalahan
                        </h3>
                        <p class="mt-1 text-sm font-bold leading-tight text-slate-700">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-slate-400 transition-colors hover:text-rose-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="absolute bottom-0 left-0 h-1 bg-rose-500 transition-all ease-linear"
                    :style="`width: ${progress}%`
                    font - size: 0 px;"></div>
            </div>
        @endif

        {{-- Toast Success --}}
        @if (session('success'))
            <div x-data="{ show: true, progress: 100 }" x-init="let interval = setInterval(() => {
                progress -= 1;
                if (progress <= 0) {
                    show = false;
                    clearInterval(interval);
                }
            }, 30);" x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="group pointer-events-auto relative overflow-hidden rounded-2xl border border-emerald-500/20 bg-white/90 p-4 shadow-2xl backdrop-blur-xl">

                <div class="flex items-start gap-4">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-lg shadow-emerald-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600">Berhasil</h3>
                        <p class="mt-1 text-sm font-bold leading-tight text-slate-700">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-slate-400 transition-colors hover:text-emerald-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="absolute bottom-0 left-0 h-1 bg-emerald-500 transition-all ease-linear"
                    :style="`width: ${progress}%`
                    font - size: 0 px;"></div>
            </div>
        @endif

    </div>

    {{-- Overlay untuk Mobile --}}
    <div id="sidebar-overlay" class="sidebar-overlay fixed inset-0 z-40 hidden bg-black/50 backdrop-blur-sm lg:hidden"
        onclick="toggleSidebar()"></div>

    <div class="flex min-h-screen">
        @include('partials.sidebar')

        <div class="flex min-w-0 flex-1 flex-col">
            @include('partials.navbar')

            <main class="w-full min-w-0 flex-1 p-4 md:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Script Sidebar Mobile --}}
    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }
    </script>

    {{-- Alpine Logic --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('scoringRule', (cfg) => ({
                open: true,
                isEdit: Boolean(cfg.isEdit),
                inputType: cfg.inputType || '',
                preferenceType: cfg.preferenceType || 'linear',
                min: Number(cfg.min || 1),
                max: Number(cfg.max || 5),
                type: cfg.type || 'benefit',

                // FUNGSI PEMBERSIH DATA (Kunci Utama)
                parseData(data) {
                    if (typeof data === 'string') {
                        try {
                            return JSON.parse(data);
                        } catch (e) {
                            return {};
                        }
                    }
                    return data || {};
                },

                semantics: {},
                utilities: {},

                init() {
                    // Bersihkan data sebelum dipakai
                    this.semantics = this.parseData(cfg.semantics);
                    const rawUtils = this.parseData(cfg.utilities);
                    this.utilities = (Array.isArray(rawUtils) && rawUtils.length === 0) ? {} : rawUtils;

                    if (Object.keys(this.utilities).length === 0) {
                        this.syncUtilities();
                    }

                    this.$watch('preferenceType', () => this.syncUtilities());
                    this.$watch('min', () => this.syncUtilities());
                    this.$watch('max', () => this.syncUtilities());
                },

                range() {
                    let s = Math.min(this.min, this.max),
                        e = Math.max(this.min, this.max);
                    return Array.from({
                        length: (e - s) + 1
                    }, (_, i) => i + s);
                },

                calculateAutoValue(i) {
                    let min = Number(this.min),
                        max = Number(this.max),
                        d = max - min;

                    if (d <= 0) return "100.00";

                    let x;

                    // Benefit vs Cost handling
                    if (this.type === 'cost') {
                        x = (max - i) / d;
                    } else {
                        x = (i - min) / d;
                    }

                    x = Math.max(0, Math.min(1, x));

                    // Preference curve (α)
                    let v = (this.preferenceType === 'concave') ? Math.sqrt(x) :
                        (this.preferenceType === 'convex') ? Math.pow(x, 2) : x;

                    // Scale to 0–100
                    return Math.round(v * 100);
                },

                syncUtilities() {
                    let updatedUtils = {};
                    this.range().forEach(i => {
                        updatedUtils[i] = this.calculateAutoValue(i);
                    });
                    this.utilities = updatedUtils;
                }
            }));
        });
    </script>
</body>

</html>
