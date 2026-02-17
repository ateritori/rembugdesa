@extends('layouts.dashboard')

@section('title', 'Manajemen Sesi Keputusan')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500" x-data="{ search: '' }">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Manajemen Sesi Keputusan
                </h1>
                <p class="adaptive-text-sub mt-2 max-w-xl text-sm leading-relaxed">
                    Monitoring seluruh sesi keputusan lintas admin. Superadmin hanya mengelola status.
                </p>
            </div>

            <form method="GET" class="w-full max-w-sm">
                <input type="text" x-model="search" placeholder="Cari nama / tahun / status"
                    class="border-app bg-app w-full rounded-xl px-4 py-3 text-sm font-bold placeholder:opacity-40">
            </form>
        </div>

        {{-- CONTENT --}}
        <div class="adaptive-card overflow-hidden p-0">
            <table class="w-full text-sm">
                <thead class="bg-app border-app border-b">
                    <tr>
                        <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-wider opacity-60">
                            Nama Sesi
                        </th>
                        <th class="px-5 py-4 text-center text-[11px] font-black uppercase tracking-wider opacity-60">
                            Tahun
                        </th>
                        <th class="px-5 py-4 text-center text-[11px] font-black uppercase tracking-wider opacity-60">
                            Status
                        </th>
                        <th class="px-5 py-4 text-center text-[11px] font-black uppercase tracking-wider opacity-60">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($sessions as $session)
                        <tr class="transition hover:bg-app/40"
                            x-show="
                                '{{ strtolower($session->name) }}'.includes(search.toLowerCase()) ||
                                '{{ $session->year }}'.includes(search) ||
                                '{{ strtolower($session->status) }}'.includes(search.toLowerCase())
                            ">
                            <td class="px-5 py-4 font-bold">
                                {{ $session->name }}
                            </td>

                            <td class="px-5 py-4 text-center text-xs font-bold">
                                {{ $session->year }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span
                                    class="rounded-lg bg-primary/10 px-3 py-1 text-[10px] font-black uppercase text-primary">
                                    {{ $session->status }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('superadmin.decision-sessions.show', $session) }}"
                                        class="adaptive-text-sub hover:text-primary hover:bg-primary/10 rounded-lg p-2 transition-all">
                                        Lihat
                                    </a>

                                    <form method="POST"
                                        action="{{ route('superadmin.decision-sessions.update-status', $session) }}"
                                        onsubmit="return confirm('Ubah status sesi ini?')">
                                        @csrf
                                        @method('PATCH')

                                        @php
                                            $currentIndex = array_search(
                                                $session->status,
                                                \App\Models\DecisionSession::STATUS_ORDER,
                                                true,
                                            );
                                        @endphp

                                        <select name="status"
                                            class="border-app bg-app rounded-lg px-2 py-1 text-[10px] font-bold"
                                            onchange="this.form.submit()">
                                            @foreach (\App\Models\DecisionSession::STATUS_ORDER as $index => $status)
                                                <option value="{{ $status }}" @selected($session->status === $status)
                                                    @if ($index > $currentIndex) disabled @endif>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <span class="ml-2 text-[10px] font-bold uppercase tracking-wider opacity-50">
                                            Rollback only
                                        </span>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-24 text-center">
                                <p class="adaptive-text-sub text-[10px] font-black uppercase tracking-[0.3em] opacity-30">
                                    Belum Ada Sesi Keputusan
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="border-app border-t px-5 py-4">
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
@endsection
