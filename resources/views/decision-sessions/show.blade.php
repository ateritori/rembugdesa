@extends('layouts.app')

@section('title', 'Detail Sesi Keputusan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">{{ $decisionSession->name }}</h1>
        <p class="text-gray-600 mt-2">ID: {{ $decisionSession->id }}</p>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Informasi Sesi</h2>
        </div>
        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                    <dd class="mt-1 text-gray-900">{{ $decisionSession->description ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            {{ $decisionSession->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($decisionSession->status ?? 'Unknown') }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                    <dd class="mt-1 text-gray-900">{{ $decisionSession->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Diperbarui</dt>
                    <dd class="mt-1 text-gray-900">{{ $decisionSession->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('decision-sessions.index') }}" class="inline-block px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
            Kembali ke Daftar
        </a>
        <a href="{{ route('decision-sessions.edit', $decisionSession->id) }}" class="inline-block ml-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Edit
        </a>
    </div>
</div>
@endsection
