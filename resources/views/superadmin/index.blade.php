@extends('layouts.dashboard')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="animate-in fade-in space-y-6 pb-10 duration-500">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div class="flex-1">
                <h1 class="adaptive-text-main text-2xl sm:text-3xl font-black leading-tight tracking-tight">
                    Manajemen Pengguna
                </h1>
                <p class="adaptive-text-sub mt-1 sm:mt-2 max-w-full sm:max-w-xl text-xs sm:text-sm leading-relaxed">
                    Kelola akun pengguna dan peran akses sistem secara terpusat.
                </p>
            </div>
            <form method="GET" x-data="{ q: '{{ request('search') }}' }" x-init="$watch('q', value => {
                clearTimeout(window.__u);
                window.__u = setTimeout(() => $el.submit(), 400)
            })"
                class="mt-2 sm:mt-0 w-full sm:w-64 flex-shrink-0">
                <div class="relative w-full">
                    <input type="text" name="search" x-model="q" placeholder="Cari nama atau email"
                        class="border-app bg-app w-full rounded-xl px-4 py-2 pr-10 text-xs sm:text-sm font-bold placeholder:opacity-40">
                    <div class="absolute inset-y-0 right-3 flex items-center opacity-50">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.85-5.4a7.25 7.25 0 11-14.5 0 7.25 0 0114.5 0z" />
                        </svg>
                    </div>
                </div>
            </form>
        </div>

        {{-- CONTENT SECTION --}}
        <div class="adaptive-card overflow-x-auto p-2 sm:p-0 scrollbar-thin scrollbar-thumb-rounded scrollbar-thumb-app">
            <table class="w-full text-xs sm:text-sm min-w-[600px]">
                <thead class="bg-app border-app border-b">
                    <tr>
                        <th class="px-3 sm:px-5 py-3 sm:py-4 text-left font-black uppercase tracking-wider opacity-60">Nama
                        </th>
                        <th class="px-3 sm:px-5 py-3 sm:py-4 text-left font-black uppercase tracking-wider opacity-60">Email
                        </th>
                        <th class="px-3 sm:px-5 py-3 sm:py-4 text-left font-black uppercase tracking-wider opacity-60">Role
                        </th>
                        <th class="px-3 sm:px-5 py-3 sm:py-4 text-center font-black uppercase tracking-wider opacity-60">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($users as $user)
                        <tr class="transition hover:bg-app/40">
                            <td class="px-3 sm:px-5 py-2 sm:py-4 font-bold">
                                {{ $user->name }}
                            </td>
                            <td class="px-3 sm:px-5 py-2 sm:py-4 text-xs opacity-70">
                                {{ $user->email }}
                            </td>
                            <td class="px-3 sm:px-5 py-2 sm:py-4">
                                <span
                                    class="text-primary bg-primary/10 rounded-lg px-2 sm:px-3 py-0.5 sm:py-1 text-[8px] sm:text-[10px] font-black uppercase">
                                    {{ $user->roles->pluck('name')->join(', ') ?: '-' }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-5 py-2 sm:py-4">
                                <div class="flex flex-wrap items-center justify-center gap-1 sm:gap-2">
                                    <a href="{{ route('superadmin.users.edit', $user) }}"
                                        class="adaptive-text-sub hover:text-primary hover:bg-primary/10 rounded-lg p-1 sm:p-2 transition-all">
                                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    @if (!$user->hasRole('superadmin'))
                                        <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}"
                                            onsubmit="return confirm('Hapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="adaptive-text-sub hover:bg-rose-500/10 hover:text-rose-500 rounded-lg p-1 sm:p-2 transition-all">
                                                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 sm:py-24 text-center">
                                <p
                                    class="adaptive-text-sub text-[8px] sm:text-[10px] font-black uppercase tracking-[0.3em] opacity-30">
                                    Belum Ada Pengguna
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="border-app border-t px-3 sm:px-5 py-3 sm:py-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
