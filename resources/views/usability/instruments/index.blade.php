@extends('layouts.dashboard')

@section('title', 'Instrumen Usability')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Instrumen Usability (SUS)
                </h1>
                <p class="adaptive-text-sub mt-2 max-w-xl text-sm leading-relaxed">
                    Kelola instrumen System Usability Scale (SUS) dan pertanyaan evaluasi usability sistem.
                </p>
            </div>

            <a href="{{ route('superadmin.usability.instruments.edit') }}"
                class="bg-primary shadow-primary/20 inline-flex items-center gap-2 rounded-2xl px-6 py-3 text-sm font-black text-white shadow-lg transition-all hover:scale-105 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Instrumen</span>
            </a>
        </div>

        {{-- INSTRUMENT INFO --}}
        @if ($instrument)
            <div class="adaptive-card p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="adaptive-text-main text-lg font-black">
                            {{ $instrument->name }}
                        </h2>
                        <p class="adaptive-text-sub mt-1 text-sm">
                            {{ $instrument->description }}
                        </p>
                    </div>

                    <span
                        class="inline-flex items-center rounded-md px-2.5 py-1 text-[10px] font-black uppercase tracking-wider
                    {{ $instrument->is_active ? 'bg-emerald-500/10 text-emerald-600' : 'bg-rose-500/10 text-rose-600' }}">
                        {{ $instrument->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            {{-- QUESTIONS LIST --}}
            <div class="adaptive-card p-0 overflow-hidden">
                <div class="border-b px-6 py-4">
                    <h3 class="adaptive-text-main text-sm font-black uppercase tracking-wider">
                        Pertanyaan SUS
                    </h3>
                </div>

                <div class="divide-y">
                    @foreach ($instrument->questions as $question)
                        <div class="flex items-start gap-4 px-6 py-4">
                            <div
                                class="text-primary bg-primary/10 flex h-8 w-8 items-center justify-center rounded-lg text-xs font-black">
                                {{ $question->number }}
                            </div>

                            <div class="flex-1">
                                <p class="adaptive-text-main text-sm font-bold">
                                    {{ $question->question }}
                                </p>

                                <div class="mt-1 flex items-center gap-3 text-[11px] font-bold opacity-70">
                                    <span>
                                        {{ ucfirst($question->polarity) }}
                                    </span>
                                    <span>•</span>
                                    <span>
                                        {{ $question->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>

                            <form method="POST"
                                action="{{ route('superadmin.usability.questions.update', $question->id) }}"
                                class="shrink-0">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="question" value="{{ $question->question }}">
                                <input type="hidden" name="is_active" value="{{ $question->is_active ? 0 : 1 }}">

                                <button type="submit"
                                    class="adaptive-text-sub rounded-lg px-3 py-1.5 text-[10px] font-black uppercase tracking-wider transition
                                {{ $question->is_active
                                    ? 'hover:bg-rose-500/10 hover:text-rose-600'
                                    : 'hover:bg-emerald-500/10 hover:text-emerald-600' }}">
                                    {{ $question->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="adaptive-card flex items-center justify-center py-20">
                <p class="adaptive-text-sub text-sm font-bold opacity-60">
                    Instrumen usability belum tersedia.
                </p>
            </div>
        @endif

    </div>
@endsection
