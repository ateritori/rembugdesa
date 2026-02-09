@extends('layouts.dashboard')

@section('title', 'Hasil Akhir')

@section('content')
    <div class="adaptive-card p-6 space-y-6">

        <div>
            <h1 class="text-xl font-bold adaptive-text-main">
                Hasil Akhir – {{ $decisionSession->name }} ({{ $decisionSession->year }})
            </h1>
            <p class="text-sm adaptive-text-sub">
                Metode SMART dan Borda
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-app text-sm">
                <thead class="bg-app/20">
                    <tr>
                        <th class="px-3 py-2 text-left">Peringkat</th>
                        <th class="px-3 py-2 text-left">Alternatif</th>
                        <th class="px-3 py-2 text-right">Skor SMART</th>
                        <th class="px-3 py-2 text-right">Skor Borda</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr class="border-t hover:bg-app/10">
                            <td class="px-3 py-2 font-bold">{{ $row['rank'] }}</td>
                            <td class="px-3 py-2">{{ $row['alternative'] }}</td>
                            <td class="px-3 py-2 text-right">{{ $row['smart'] }}</td>
                            <td class="px-3 py-2 text-right">{{ $row['borda'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('decision-sessions.index') }}" class="px-4 py-2 text-sm rounded border border-app">
                Kembali
            </a>
        </div>

    </div>
@endsection
