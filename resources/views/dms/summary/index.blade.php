@extends('layouts.dashboard')

@section('title', 'Ringkasan Keputusan')

@section('content')
    @include('dms.partials.nav')

    <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
        <div>
            <h2 class="text-xl font-black text-app">Ringkasan Keputusan</h2>
            <p class="text-sm text-gray-600">
                Ringkasan ini menampilkan status proses dan hasil akhir keputusan.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border rounded-lg p-4 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-700 mb-1">Status Proses</h3>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>Bobot Individu: <span class="font-semibold">Selesai</span></li>
                    <li>Bobot Kelompok: <span class="font-semibold">Tersedia</span></li>
                    <li>Penilaian Alternatif: <span class="font-semibold">Selesai</span></li>
                </ul>
            </div>

            <div class="border rounded-lg p-4 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-700 mb-1">Hasil</h3>
                <p class="text-sm text-gray-600">
                    Hasil peringkat alternatif dapat dilihat setelah sesi keputusan ditutup.
                </p>
            </div>
        </div>

        <div class="border border-dashed rounded-lg p-4 text-center text-sm text-gray-500">
            Halaman ini bersifat <span class="font-bold">read-only</span> untuk Decision Maker.
        </div>
    </div>
@endsection
