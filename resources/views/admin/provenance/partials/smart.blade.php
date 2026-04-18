@extends('layouts.dashboard')

@section('title', 'SMART Debug')

@section('content')
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">SMART Provenance (Raw Debug)</h1>

        <div class="bg-black text-green-400 text-xs p-4 rounded-xl overflow-auto">
            <pre>{{ print_r($data['pipeline']['smart']['trace'] ?? [], true) }}</pre>
        </div>
    </div>
@endsection
