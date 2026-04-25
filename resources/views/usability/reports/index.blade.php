@extends('layouts.dashboard')

@section('title', 'Laporan Analisis Usability SUS')

@section('content')
    <div class="animate-in fade-in space-y-6 pb-12 duration-500">

        {{-- HEADER: High Contrast & Formal --}}
        <div
            class="flex flex-col items-start justify-between gap-4 border-b-2 border-slate-900 pb-6 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">
                    Laporan Usability (SUS)
                </h1>
                <p class="mt-1 text-sm font-bold text-slate-600">
                    Instrumen Evaluasi Sistem: System Usability Scale (Standar Brooke, 1996)
                </p>
            </div>

            {{-- SUMMARY: Box Putih Bersih dengan Border Hitam Tegas --}}
            <div
                class="rounded-xl border-2 border-slate-900 bg-white p-4 shadow-[4px_4px_0px_0px_rgba(15,23,42,1)] flex items-center gap-6">
                <div class="text-center">
                    <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500">Mean Score</span>
                    <span class="text-3xl font-black text-slate-900">
                        {{ number_format($averageScore ?? 0, 2) }}
                    </span>
                </div>
                <div class="h-10 w-[2px] bg-slate-900"></div>
                <div>
                    <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500">Adjective
                        Rating</span>
                    <span class="block text-sm font-black text-slate-900">
                        @php
                            $score = $averageScore ?? 0;
                            if ($score >= 80.3) {
                                $grade = 'Excellent (Grade A)';
                            } elseif ($score >= 68) {
                                $grade = 'Good (Grade B/C)';
                            } elseif ($score >= 51) {
                                $grade = 'Fair (Grade D)';
                            } else {
                                $grade = 'Poor (Grade F)';
                            }
                        @endphp
                        {{ strtoupper($grade) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- FILTER: Border Lebih Tebal --}}
        <form method="GET"
            class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end bg-white p-5 rounded-xl border-2 border-slate-200">
            <div class="space-y-1.5">
                <label class="text-[10px] font-black uppercase text-slate-700 ml-1">Filter Peran</label>
                <input type="text" name="role" value="{{ request('role') }}" placeholder="Admin/User"
                    class="w-full rounded-lg border-2 border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-900 focus:border-slate-900 focus:ring-0">
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-black uppercase text-slate-700 ml-1">ID Sesi</label>
                <input type="number" name="decision_session_id" value="{{ request('decision_session_id') }}"
                    placeholder="Sesi ID"
                    class="w-full rounded-lg border-2 border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-900 focus:border-slate-900 focus:ring-0">
            </div>

            <div class="md:col-span-2 flex gap-2">
                <button type="submit"
                    class="flex-1 bg-slate-900 border-2 border-slate-900 rounded-lg px-5 py-2 text-xs font-black text-white hover:bg-white hover:text-slate-900 transition-all">
                    FILTER DATA
                </button>
                <a href="{{ route('superadmin.usability.reports.index') }}"
                    class="rounded-lg border-2 border-slate-300 px-5 py-2 text-xs font-black text-slate-600 hover:bg-slate-100 transition">
                    RESET
                </a>
            </div>
        </form>

        {{-- TABLE: Border-Collapse & Black Text --}}
        <div class="overflow-hidden rounded-xl border-2 border-slate-900 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="bg-slate-900">
                            <th
                                class="border-b border-slate-900 px-6 py-4 text-[11px] font-black uppercase tracking-widest text-white">
                                Responden</th>
                            <th
                                class="border-b border-slate-900 px-6 py-4 text-center text-[11px] font-black uppercase tracking-widest text-white">
                                Role/Peran</th>
                            <th
                                class="border-b border-slate-900 px-6 py-4 text-center text-[11px] font-black uppercase tracking-widest text-white">
                                Sesi Pengujian</th>
                            <th
                                class="border-b border-slate-900 px-6 py-4 text-center text-[11px] font-black uppercase tracking-widest text-white">
                                Skor Individual</th>
                            <th
                                class="border-b border-slate-900 px-6 py-4 text-right text-[11px] font-black uppercase tracking-widest text-white">
                                Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-slate-200">
                        @forelse ($responses as $response)
                            <tr class="bg-white">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="font-black text-slate-900">{{ strtoupper($response->user->name ?? 'N/A') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex border-2 border-slate-900 px-2 py-0.5 text-[10px] font-black uppercase text-slate-900">
                                        {{ $response->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-xs font-bold text-slate-700">
                                    {{ $response->decisionSession->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-base font-black text-slate-900">
                                        {{ number_format($response->total_score, 1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-[10px] font-bold text-slate-500 uppercase">
                                    {{ $response->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center">
                                    <p class="text-sm font-black uppercase tracking-widest text-slate-300">Data Kosong</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($responses->hasPages())
                <div class="border-t-2 border-slate-900 bg-slate-50 px-6 py-4">
                    {{ $responses->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
