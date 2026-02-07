@extends('layouts.dashboard')

@section('title', 'Buat Sesi Keputusan')

@section('content')
    <div class="bg-card p-6 rounded shadow max-w-xl">

        <h1 class="text-lg font-semibold mb-4">
            Buat Sesi Keputusan
        </h1>

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('decision-sessions.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium">
                    Judul Sesi
                </label>
                <input type="text" name="name" class="w-full border border-app rounded px-3 py-2 bg-transparent"
                    required>
            </div>

            <div class="mb-6">
                <label class="block mb-1 text-sm font-medium">
                    Tahun
                </label>
                <input type="number" name="year" class="w-full border border-app rounded px-3 py-2 bg-transparent"
                    required>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 text-sm rounded bg-primary text-white">
                    Simpan
                </button>

                <a href="{{ route('decision-sessions.index') }}" class="px-4 py-2 text-sm rounded border border-app">
                    Batal
                </a>
            </div>
        </form>

    </div>
@endsection
