@extends('layouts.dashboard')

@section('title', 'Laporan Usability')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Laporan Usability (SUS)
                </h1>
                <p class="adaptive-text-sub mt-2 max-w-xl text-sm leading-relaxed">
                    Ringkasan hasil penilaian usability sistem berdasarkan System Usability Scale (SUS).
                </p>
            </div>
        </div>

        {{-- SUMMARY --}}
        <div class="adaptive-card p-6 flex flex-col gap-2 max-w-sm">
            <span class="adaptive-text-sub text-xs font-black uppercase tracking-wider opacity-60">
                Rata-rata Skor SUS
            </span>
            <span class="adaptive-text-main text-4xl font-black">
                {{ number_format($averageScore ?? 0, 2) }}
            </span>
            <span class="adaptive-text-sub text-xs font-bold opacity-60">
                Skala 0 – 100
            </span>
        </div>

        {{-- FILTER --}}
        <form method="GET" class="adaptive-card p-6 flex flex-col gap-4 sm:flex-row sm:items-end">
            <div>
                <label class="adaptive-text-sub mb-1 block text-xs font-black uppercase tracking-wider">
                    Role
                </label>
                <input type="text" name="role" value="{{ request('role') }}" placeholder="admin / dm / user"
                    class="border-app bg-app w-full rounded-xl px-4 py-2 text-sm font-bold">
            </div>

            <div>
                <label class="adaptive-text-sub mb-1 block text-xs font-black uppercase tracking-wider">
                    ID Sesi Keputusan
                </label>
                <input type="number" name="decision_session_id" value="{{ request('decision_session_id') }}"
                    placeholder="Opsional" class="border-app bg-app w-full rounded-xl px-4 py-2 text-sm font-bold">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="bg-primary shadow-primary/20 rounded-xl px-6 py-2 text-sm font-black text-white shadow-lg transition hover:brightness-110">
                    Filter
                </button>

                <a href="{{ route('superadmin.usability.reports.index') }}"
                    class="adaptive-text-sub hover:text-primary hover:bg-primary/10 rounded-xl px-4 py-2 text-sm font-black transition">
                    Reset
                </a>
            </div>
        </form>

        {{-- TABLE --}}
        <div class="adaptive-card overflow-hidden p-0">
            <table class="w-full text-sm">
                <thead class="bg-app border-b">
                    <tr>
                        <th class="px-5 py-4 text-left text-[11px] font-black uppercase tracking-wider opacity-60">
                            Pengguna
                        </th>
                        <th class="px-5 py-4 text-center text-[11px] font-black uppercase tracking-wider opacity-60">
                            Role
                        </th>
                        <th class="px-5 py-4 text-center text-[11px] font-black uppercase tracking-wider opacity-60">
                            Sesi
                        </th>
                        <th class="px-5 py-4 text-center text-[11px] font-black uppercase tracking-wider opacity-60">
                            Skor SUS
                        </th>
                        <th class="px-5 py-4 text-center text-[11px] font-black uppercase tracking-wider opacity-60">
                            Waktu
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($responses as $response)
                        <tr class="transition hover:bg-app/40">
                            <td class="px-5 py-4 font-bold">
                                {{ $response->user->name ?? '-' }}
                            </td>

                            <td class="px-5 py-4 text-center text-xs font-bold">
                                {{ $response->role }}
                            </td>

                            <td class="px-5 py-4 text-center text-xs font-bold">
                                {{ $response->decisionSession->name ?? '-' }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span class="rounded-lg bg-primary/10 px-3 py-1 text-[10px] font-black text-primary">
                                    {{ number_format($response->total_score, 2) }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center text-xs font-bold opacity-60">
                                {{ $response->created_at->translatedFormat('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-24 text-center">
                                <p class="adaptive-text-sub text-[10px] font-black uppercase tracking-[0.3em] opacity-30">
                                    Belum ada data penilaian
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="border-t px-5 py-4">
                {{ $responses->withQueryString()->links() }}
            </div>
        </div>

    </div>
@endsection
