@extends('layouts.dashboard')

@section('content')
    <div class="space-y-6">

        {{-- HEADER --}}
        <div>
            <h1 class="text-lg font-semibold">Dashboard Superadmin</h1>
            <p class="text-sm opacity-70">
                Ringkasan kondisi sistem dan pengguna
            </p>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-card p-4 rounded shadow">
                <p class="text-xs opacity-70">Total Pengguna</p>
                <p class="text-2xl font-semibold">{{ $totalUsers }}</p>
            </div>

            <div class="bg-card p-4 rounded shadow">
                <p class="text-xs opacity-70">Admin</p>
                <p class="text-2xl font-semibold">
                    {{ $totalAdmins }}
                </p>
            </div>

            <div class="bg-card p-4 rounded shadow">
                <p class="text-xs opacity-70">Decision Maker</p>
                <p class="text-2xl font-semibold">
                    {{ $totalDms }}
                </p>
            </div>

            <div class="bg-card p-4 rounded shadow">
                <p class="text-xs opacity-70">Total Sesi Keputusan</p>
                <p class="text-2xl font-semibold">
                    {{ $totalSessions }}
                </p>
            </div>
        </div>

        {{-- SYSTEM INFO --}}
        <div class="bg-card p-4 rounded shadow">
            <h2 class="text-sm font-semibold mb-2">Status Sistem</h2>
            <ul class="text-sm space-y-1 opacity-80">
                <li>Framework: Laravel {{ app()->version() }}</li>
                <li>PHP: {{ phpversion() }}</li>
                <li>Environment: {{ app()->environment() }}</li>
                <li>Waktu Server: {{ now()->format('d M Y H:i') }}</li>
            </ul>
        </div>

    </div>
@endsection
