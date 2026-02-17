@extends('layouts.dashboard')

@section('title', 'Manajemen Role')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Manajemen Role
                </h1>
                <p class="adaptive-text-sub mt-2 max-w-xl text-sm leading-relaxed">
                    Kelola role sistem dan keterkaitannya dengan pengguna secara terpusat.
                </p>
            </div>

            <a href="{{ route('superadmin.roles.create') }}"
                class="bg-primary shadow-primary/20 group flex items-center gap-2 rounded-2xl px-6 py-3 text-sm font-black text-white shadow-lg transition-all hover:scale-105 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Role Baru</span>
            </a>
        </div>

        {{-- CONTENT --}}
        <div class="adaptive-card overflow-hidden p-0">
            <table class="w-full text-sm">
                <thead class="bg-app border-app border-b">
                    <tr>
                        <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-wider opacity-60">
                            Nama Role
                        </th>
                        <th class="px-5 py-4 text-center text-[11px] font-black uppercase tracking-wider opacity-60">
                            Jumlah User
                        </th>
                        <th class="px-5 py-4 text-center text-[11px] font-black uppercase tracking-wider opacity-60">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($roles as $role)
                        <tr class="transition hover:bg-app/40">
                            <td class="px-5 py-4 font-bold">
                                {{ $role->name }}
                            </td>

                            <td class="px-5 py-4 text-center text-xs font-bold">
                                {{ $role->users_count }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('superadmin.roles.edit', $role) }}"
                                        class="adaptive-text-sub hover:text-primary hover:bg-primary/10 rounded-lg p-2 transition-all">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    @if ($role->name !== 'superadmin')
                                        <form method="POST" action="{{ route('superadmin.roles.destroy', $role) }}"
                                            onsubmit="return confirm('Hapus role ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="adaptive-text-sub hover:bg-rose-500/10 hover:text-rose-500 rounded-lg p-2 transition-all">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
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
                            <td colspan="3" class="py-24 text-center">
                                <p class="adaptive-text-sub text-[10px] font-black uppercase tracking-[0.3em] opacity-30">
                                    Belum Ada Role
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="border-app border-t px-5 py-4">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
@endsection
