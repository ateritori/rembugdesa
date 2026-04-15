@php
    $isBenefit = $c->type === 'benefit';

    $prefix = match ($level) {
        1 => 'K',
        2 => 'P',
        3 => 'I',
        4 => 'U',
        5 => 'V',
        default => 'L',
    };

    $color = match ($level) {
        1 => 'indigo',
        2 => 'rose',
        3 => 'amber',
        4 => 'blue',
        5 => 'violet',
        default => 'slate',
    };
@endphp

<div x-data="{ openEdit: false, openScoring: false }"
    class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition-all {{ !$isLocked ? 'hover:border-primary/30' : '' }} dark:border-slate-700 dark:bg-slate-800">

    {{-- BODY --}}
    <div class="flex items-start px-4 py-3 md:px-5">
        <div class="flex items-start gap-3 min-w-0">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-{{ $color }}-600 text-[10px] font-black italic text-white dark:bg-slate-700 mt-0.5">
                {{ $prefix }}{{ $loop->iteration }}
            </div>

            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h3
                        class="text-[14px] font-black uppercase tracking-tight text-slate-800 dark:text-slate-100 break-words leading-tight">
                        {{ $c->name }}
                    </h3>
                    <span
                        class="rounded-md px-1.5 py-0.5 text-[7px] font-black uppercase tracking-wider {{ $isBenefit ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30' : 'bg-orange-100 text-orange-600 dark:bg-orange-900/30' }}">
                        {{ $c->type }}
                    </span>
                    <span
                        class="rounded-md px-1.5 py-0.5 text-[7px] font-black uppercase tracking-wider bg-{{ $color }}-100 text-{{ $color }}-600 dark:bg-{{ $color }}-900/30">
                        Level {{ $c->level }}
                    </span>
                    <span
                        class="rounded-md px-1.5 py-0.5 text-[7px] font-black uppercase tracking-wider {{ $c->evaluator_type === 'system' ? 'bg-slate-200 text-slate-600 dark:bg-slate-700' : 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30' }}">
                        {{ $c->evaluator_type === 'system' ? 'Sistem' : 'Manusia' }}
                    </span>
                </div>

                <div class="mt-1 flex items-center gap-3">
                    <span
                        class="flex items-center gap-1 text-[8px] font-bold uppercase tracking-wider {{ $c->is_active ? 'text-slate-400' : 'text-rose-500' }}">
                        <span
                            class="h-1 w-1 rounded-full {{ $c->is_active ? 'bg-primary/50' : 'bg-rose-500 animate-pulse' }}"></span>
                        {{ $c->is_active ? 'Aktif' : 'Off' }}
                    </span>
                    <span class="h-2 w-[1px] bg-slate-200 dark:bg-slate-700"></span>
                    @if ($rule)
                        <span
                            class="flex items-center gap-1 text-[8px] font-bold uppercase tracking-wider text-emerald-500">
                            <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                    d="M5 13l4 4L19 7" />
                            </svg> Ready
                        </span>
                    @else
                        <span
                            class="flex items-center gap-1 text-[8px] font-bold uppercase tracking-wider text-amber-500">
                            <svg class="h-2.5 w-2.5 animate-bounce" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg> Set
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div
        class="flex items-center justify-between border-t border-slate-50 bg-slate-50/30 px-4 py-2 dark:border-slate-700/50 dark:bg-slate-900/20">
        <div class="flex items-center gap-1">

            <button @click="{{ $isLocked ? '' : 'openEdit = !openEdit' }}" {{ $isLocked ? 'disabled' : '' }}
                class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-primary transition-all disabled:opacity-20 disabled:cursor-not-allowed {{ $isLocked ? 'pointer-events-none' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 114 4L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>

            <div class="h-4 w-[1px] bg-slate-200 dark:bg-slate-700 mx-1"></div>

            <form method="POST" action="{{ route('criteria.toggle', $c->id) }}">
                @csrf @method('PATCH')
                <button
                    class="rounded-lg p-1.5 transition-all {{ $c->is_active ? 'text-slate-400' : 'text-emerald-500' }} disabled:opacity-20 disabled:cursor-not-allowed"
                    {{ $isLocked && $c->is_active ? 'disabled' : '' }}>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="{{ $c->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}" />
                    </svg>
                </button>
            </form>

            <form method="POST" action="{{ route('criteria.destroy', $c->id) }}" onsubmit="return confirm('Hapus?')">
                @csrf @method('DELETE')
                <button
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all disabled:opacity-20 disabled:cursor-not-allowed"
                    {{ $isLocked ? 'disabled' : '' }}>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>

        <button type="button" @click="{{ $isLocked ? '' : 'openScoring = !openScoring' }}"
            {{ $isLocked ? 'disabled' : '' }}
            :class="openScoring ? 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200 shadow-none' : ''"
            class="rounded-lg px-4 py-1.5 text-[9px] font-black uppercase tracking-widest transition-all shadow-sm
        {{ $rule ? 'bg-white border border-slate-200 text-slate-600' : 'bg-slate-900 text-white' }}
        dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200
        disabled:opacity-40 disabled:grayscale disabled:cursor-not-allowed {{ $isLocked ? 'pointer-events-none' : '' }}">

            <div class="flex items-center gap-2">
                <span x-text="openScoring ? 'Tutup' : '{{ $rule ? 'Edit Parameter' : 'Set Parameter' }}'"></span>
                @if (!$isLocked)
                    <svg class="h-3 w-3 transition-transform duration-300" :class="openScoring ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                @else
                    <svg class="h-3 w-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                @endif
            </div>
        </button>
    </div>

    {{-- EDIT --}}
    <div x-show="openEdit" x-collapse
        class="border-t border-slate-100 bg-slate-50/50 p-3 dark:border-slate-700 dark:bg-slate-900/30">
        <form method="POST" action="{{ route('criteria.update', $c->id) }}" class="flex flex-col sm:flex-row gap-2">
            @csrf
            @method('PUT')

            <input name="name" value="{{ $c->name }}" required
                class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold dark:bg-slate-800 dark:border-slate-700 outline-none focus:ring-2 focus:ring-primary/10">

            <select name="type" required
                class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200">
                <option value="benefit" {{ $c->type === 'benefit' ? 'selected' : '' }}>
                    BENEFIT
                </option>
                <option value="cost" {{ $c->type === 'cost' ? 'selected' : '' }}>
                    COST
                </option>
            </select>

            <select name="evaluator_type"
                class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200">
                <option value="human" {{ $c->evaluator_type === 'human' ? 'selected' : '' }}>MANUSIA</option>
                <option value="system" {{ $c->evaluator_type === 'system' ? 'selected' : '' }}>SISTEM</option>
            </select>

            <button
                class="rounded-lg bg-primary px-4 py-1.5 text-[9px] font-black uppercase text-white hover:brightness-110">
                Save
            </button>
        </form>
    </div>

    {{-- SCORING --}}
    <div x-show="openScoring" x-collapse
        class="border-t border-slate-100 bg-white dark:border-slate-700 dark:bg-slate-800">
        <div class="p-3 md:p-4 overflow-x-auto">
            @include('criteria.partials.scoring-rule', [
                'c' => $c,
                'rule' => $rule,
                'isLocked' => $isLocked,
            ])
        </div>
    </div>
</div>
