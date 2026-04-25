@extends('layouts.app')

@section('title', 'Evaluasi Alternatif')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Evaluasi Alternatif</h1>
        <p class="text-gray-600 mt-2">Workspace Evaluasi untuk Decision Maker</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <p class="text-gray-500 text-sm">Sesi Keputusan</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ $decisionSession ?? 'N/A' }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <p class="text-gray-500 text-sm">Total Alternatif</p>
                <p class="text-2xl font-bold text-blue-600">
                    {{ count($alternatives ?? []) }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <p class="text-gray-500 text-sm">Total Kriteria</p>
                <p class="text-2xl font-bold text-green-600">
                    {{ count($criteria ?? []) }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <p class="text-gray-500 text-sm">Progres</p>
                <p class="text-2xl font-bold text-purple-600">
                    {{ $progress ?? 0 }}%
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Tabel Evaluasi</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Alternatif</th>
                        @foreach($criteria ?? [] as $c)
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">{{ $c }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alternatives ?? [] as $alt)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-800">{{ $alt }}</td>
                            @foreach($criteria ?? [] as $c)
                                <td class="px-4 py-3 text-gray-600">-</td>
                            @endforeach
                            <td class="px-4 py-3">
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($criteria ?? []) + 2 }}" class="px-4 py-8 text-center text-gray-500">
                                Belum ada data alternatif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Simpan Evaluasi
        </button>
        <a href="#" class="px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
            Batal
        </a>
    </div>
</div>
@endsection
