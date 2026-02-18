@extends('layouts.dashboard')

@section('title', 'Edit Instrumen Usability')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Edit Instrumen Usability (SUS)
                </h1>
                <p class="adaptive-text-sub mt-2 max-w-xl text-sm leading-relaxed">
                    Perbarui informasi instrumen dan sesuaikan teks pertanyaan usability.
                </p>
            </div>

            <a href="{{ route('superadmin.usability.instruments.index') }}"
                class="adaptive-text-sub hover:text-primary hover:bg-primary/10 rounded-xl px-4 py-2 text-sm font-black transition">
                ← Kembali
            </a>
        </div>

        {{-- FORM INSTRUMENT --}}
        <form method="POST" action="{{ route('superadmin.usability.instruments.update') }}"
            class="adaptive-card space-y-6 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label class="adaptive-text-sub mb-1 block text-xs font-black uppercase tracking-wider">
                        Nama Instrumen
                    </label>
                    <input type="text" name="name" value="{{ old('name', $instrument->name) }}"
                        class="border-app bg-app w-full rounded-xl px-4 py-2 text-sm font-bold">
                </div>

                <div>
                    <label class="adaptive-text-sub mb-1 block text-xs font-black uppercase tracking-wider">
                        Status
                    </label>
                    <select name="is_active" class="border-app bg-app w-full rounded-xl px-4 py-2 text-sm font-bold">
                        <option value="1" @selected($instrument->is_active)>Aktif</option>
                        <option value="0" @selected(!$instrument->is_active)>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="adaptive-text-sub mb-1 block text-xs font-black uppercase tracking-wider">
                    Deskripsi
                </label>
                <textarea name="description" rows="3" class="border-app bg-app w-full rounded-xl px-4 py-2 text-sm font-bold">{{ old('description', $instrument->description) }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-primary shadow-primary/20 rounded-xl px-6 py-3 text-sm font-black text-white shadow-lg transition hover:brightness-110">
                    Simpan Instrumen
                </button>
            </div>
        </form>

        {{-- QUESTIONS --}}
        <div class="adaptive-card p-0 overflow-hidden">
            <div class="border-b px-6 py-4">
                <h3 class="adaptive-text-main text-sm font-black uppercase tracking-wider">
                    Pertanyaan SUS
                </h3>
            </div>

            <div class="divide-y">
                @foreach ($instrument->questions as $question)
                    <form method="POST" action="{{ route('superadmin.usability.questions.update', $question->id) }}"
                        class="flex items-start gap-4 px-6 py-4">
                        @csrf
                        @method('PUT')

                        <div
                            class="text-primary bg-primary/10 flex h-8 w-8 items-center justify-center rounded-lg text-xs font-black">
                            {{ $question->number }}
                        </div>

                        <div class="flex-1 space-y-2">
                            <textarea name="question" rows="2" class="border-app bg-app w-full rounded-xl px-3 py-2 text-sm font-bold">{{ old('question', $question->question) }}</textarea>

                            <div class="flex items-center gap-4 text-[11px] font-bold opacity-70">
                                <span>{{ ucfirst($question->polarity) }}</span>

                                <label class="flex items-center gap-1">
                                    <input type="checkbox" name="is_active" value="1" @checked($question->is_active)>
                                    Aktif
                                </label>
                            </div>
                        </div>

                        <div class="shrink-0">
                            <button type="submit"
                                class="adaptive-text-sub hover:text-primary hover:bg-primary/10 rounded-lg px-3 py-1.5 text-[10px] font-black uppercase tracking-wider transition">
                                Simpan
                            </button>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>

    </div>
@endsection
