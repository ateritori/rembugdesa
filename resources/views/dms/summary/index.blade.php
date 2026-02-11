@extends('layouts.dashboard')

@section('title', 'Ringkasan Keputusan')

@section('content')
    @include('dms.partials.nav')

    <div class="space-y-6 rounded-xl border border-gray-200 bg-white p-6">
        <div>
            <h2 class="text-app text-xl font-black">Ringkasan Keputusan</h2>
            <p class="text-sm text-gray-600">
                Ringkasan ini menampilkan status proses dan hasil penilaian sementara
                berdasarkan bobot kelompok.
            </p>
        </div>

        {{-- Status Bar: Responsive Stacked on Mobile, Balanced Row on Desktop --}}
        <div
            class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-6">
                {{-- 1. Status Utama --}}
                <div class="flex items-center gap-3 md:px-4">
                    <div
                        class="bg-{{ $decisionSession->status === 'closed' ? 'gray' : 'blue' }}-100 relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full">
                        <div
                            class="bg-{{ $decisionSession->status === 'closed' ? 'gray' : 'blue' }}-600 {{ $decisionSession->status === 'closed' ? '' : 'animate-pulse' }} h-2.5 w-2.5 rounded-full">
                        </div>
                    </div>
                    <div>
                        <p class="mb-1 text-[10px] font-bold uppercase leading-none tracking-wider text-gray-400">Status Sesi
                        </p>
                        <p class="text-sm font-black text-gray-800">{{ ucfirst($decisionSession->status) }}</p>
                    </div>
                </div>

                {{-- 2. Kolom Tengah: Selalu 1 Baris Melebar (Mobile & Desktop) --}}
                <div class="flex justify-between gap-4 sm:items-center sm:gap-6">
                    <div class="flex flex-col items-center text-center sm:items-start sm:text-left">
                        <p class="text-[10px] font-bold uppercase tracking-tighter text-gray-400">Individu</p>
                        <div class="flex items-center gap-1 text-[11px] font-bold text-green-600">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Selesai</span>
                        </div>
                    </div>
                    <div
                        class="flex flex-col items-center border-x border-gray-50 px-2 sm:items-start sm:text-left sm:border-none">
                        <p class="text-[10px] font-bold uppercase tracking-tighter text-gray-400">Kelompok</p>
                        <div class="flex items-center gap-1 text-[11px] font-bold text-blue-600">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Tersedia</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-center text-center sm:items-start sm:text-left">
                        <p class="text-[10px] font-bold uppercase tracking-tighter text-gray-400">Penilaian</p>
                        <div class="flex items-center gap-1 text-[11px] font-bold text-green-600">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Selesai</span>
                        </div>
                    </div>
                </div>
            </div>

            @if (auth()->check() && auth()->user()->hasRole('dm'))
                <div class="flex sm:shrink-0 sm:justify-end">
                    <a href="{{ route('alternative-evaluations.index', $decisionSession->id) }}"
                        class="rounded-lg bg-gray-900 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-600">
                        Edit Penilaian
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabel hasil SMART --}}
    @if (!empty($rows))
        <div>
            <h3 class="mb-3 text-sm font-bold text-gray-700">
                Skor Alternatif (SMART)
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full rounded-lg border border-gray-200 text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 text-left">Peringkat</th>
                            <th class="px-3 py-2 text-left">Alternatif</th>
                            <th class="px-3 py-2 text-right">Skor SMART</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $index => $row)
                            <tr class="border-t">
                                <td class="px-3 py-2 font-semibold">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $row['alternative'] }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    {{ number_format($row['smart'], 6) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="rounded-lg border border-dashed p-4 text-center text-sm text-gray-500">
            Belum ada data penilaian yang dapat ditampilkan.
        </div>
    @endif

    <div class="rounded-lg border border-dashed p-4 text-center text-sm text-gray-500">
        Halaman ini bersifat <span class="font-bold">read-only</span> untuk Decision Maker.
    </div>
    </div>
@endsection
