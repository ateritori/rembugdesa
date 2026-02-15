@role('admin')
    <div class="border-b border-app mb-8 overflow-x-auto no-scrollbar">
        <div class="flex items-center min-w-max gap-1">
            @php
                $navItems = [
                    [
                        'route' => 'criteria.*',
                        'label' => 'Kriteria',
                        'url' => route('criteria.index', $decisionSession->id),
                    ],
                    [
                        'route' => 'alternatives.*',
                        'label' => 'Alternatif',
                        'url' => route('alternatives.index', $decisionSession->id),
                    ],
                    [
                        'route' => 'decision-sessions.assign-dms*',
                        'label' => 'Decision Maker',
                        'url' => route('decision-sessions.assign-dms', $decisionSession->id),
                    ],
                    [
                        'route' => 'control.*',
                        'label' => 'Kontrol Sesi',
                        'url' => route('control.index', $decisionSession->id),
                    ],
                ];
            @endphp

            @foreach ($navItems as $item)
                @if (isset($item['condition']) && !$item['condition'])
                    @continue
                @endif
                @php $isActive = request()->routeIs($item['route']); @endphp

                <a href="{{ $item['url'] }}"
                    class="relative px-6 py-4 text-sm font-black transition-all duration-300 group
                {{ $isActive ? 'nav-tab-active adaptive-text-main' : 'adaptive-text-sub hover:adaptive-text-main opacity-60 hover:opacity-100' }}">

                    <span class="relative z-10 tracking-tight adaptive-text-main">{{ $item['label'] }}</span>

                    {{-- Efek Hover Halus untuk Tab Non-Aktif --}}
                    @if (!$isActive)
                        <div
                            class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-1 bg-primary/20 group-hover:w-full transition-all duration-300 rounded-t-full">
                        </div>
                    @endif

                    {{-- Background Glow tipis saat Aktif --}}
                    @if ($isActive)
                        <div
                            class="absolute inset-x-0 bottom-0 top-2 bg-gradient-to-t from-primary/10 to-transparent rounded-t-xl -z-0">
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endrole
