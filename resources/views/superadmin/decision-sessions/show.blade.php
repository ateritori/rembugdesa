@extends('layouts.app')

@section('title', 'Detail Sesi Keputusan - Superadmin')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">{{ $session->name }}</h1>
            <p class="text-gray-600 mt-2">View Mode (Read-Only)</p>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Informasi Sesi Keputusan</h2>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ID Sesi</dt>
                        <dd class="mt-1 text-gray-900 font-mono">{{ $session->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama</dt>
                        <dd class="mt-1 text-gray-900">{{ $session->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                        <dd class="mt-1 text-gray-900">{{ $session->description ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span
                                class="px-3 py-1 rounded-full text-sm font-semibold
                            {{ $session->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($session->status ?? 'Unknown') }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dibuat pada</dt>
                        <dd class="mt-1 text-gray-900">{{ $session->created_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Terakhir diperbarui</dt>
                        <dd class="mt-1 text-gray-900">{{ $session->updated_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded">
            <p class="text-sm text-blue-800">
                <strong>Catatan:</strong> Sebagai Superadmin, Anda dapat melihat semua informasi sesi ini dalam mode
                read-only.
                Untuk perubahan status, gunakan fitur yang tersedia di dashboard.
            </p>
        </div>

        <div class="mt-6">
            <a href="{{ route('decision-sessions.index') }}"
                class="inline-block px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                Kembali ke Daftar
            </a>
        </div>
    </div>
@endsection
