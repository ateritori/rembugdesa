@extends('layouts.dashboard')

@section('title', 'Edit Sesi Keputusan')

@section('content')
    <div class="animate-in fade-in slide-in-from-bottom-4 w-full px-4 py-6 duration-700">

        <div class="mx-auto max-w-5xl">
            {{-- HEADER --}}
            <div class="mb-8 flex items-center gap-4">
                <div
                    class="bg-primary shadow-primary/20 flex h-12 w-12 items-center justify-center rounded-xl text-white shadow-lg">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 114 4L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black uppercase tracking-tight text-slate-800">Modifikasi Sesi</h1>
                    <p class="text-primary text-[10px] font-black uppercase tracking-[0.2em] opacity-80">Update Decision
                        Parameters</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

                {{-- KOLOM FORM (7/12) --}}
                <div class="lg:col-span-7">
                    <div
                        class="hover:border-primary/30 relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all sm:p-8">

                        {{-- Accent Line --}}
                        <div class="bg-primary absolute left-0 top-0 h-1 w-full opacity-20"></div>

                        {{-- Error Handling --}}
                        @if ($errors->any())
                            <div class="mb-6 rounded-xl border border-red-100 bg-red-50 p-4">
                                <div class="flex items-center gap-2 mb-2 text-red-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Terjadi Kesalahan</span>
                                </div>
                                <ul class="list-inside list-disc text-xs font-bold text-red-500 opacity-80">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('decision-sessions.update', $decisionSession->id) }}"
                            class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="space-y-2">
                                <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    Judul Sesi Keputusan
                                </label>
                                <div class="group relative">
                                    <div
                                        class="group-focus-within:text-primary absolute inset-y-0 left-0 flex items-center pl-4 text-slate-300 transition-colors">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 114 4L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name', $decisionSession->name) }}"
                                        class="focus:border-primary focus:ring-primary/5 w-full rounded-xl border-slate-200 bg-slate-50/50 py-3.5 pl-11 pr-4 text-sm font-bold text-slate-700 transition-all focus:bg-white focus:ring-4"
                                        required>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    Tahun
                                </label>
                                <div class="group relative max-w-[200px]">
                                    <div
                                        class="group-focus-within:text-primary absolute inset-y-0 left-0 flex items-center pl-4 text-slate-300 transition-colors">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="number" name="year" value="{{ old('year', $decisionSession->year) }}"
                                        class="focus:border-primary focus:ring-primary/5 w-full rounded-xl border-slate-200 bg-slate-50/50 py-3.5 pl-11 pr-4 text-sm font-bold text-slate-700 transition-all focus:bg-white focus:ring-4"
                                        required>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 pt-4">
                                <button type="submit"
                                    class="rounded-xl bg-slate-800 px-8 py-3.5 text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-slate-200 transition-all hover:bg-black active:scale-95">
                                    Update Perubahan
                                </button>

                                <a href="{{ route('decision-sessions.index') }}"
                                    class="rounded-xl border border-slate-200 bg-white px-8 py-3.5 text-[11px] font-black uppercase tracking-widest text-slate-400 transition-all hover:bg-slate-50 hover:text-slate-600">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- KOLOM INFO (5/12) --}}
                <div class="flex flex-col gap-4 lg:col-span-5">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3
                            class="mb-4 flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-800">
                            <span class="bg-primary h-1 w-3 rounded-full"></span>
                            Informasi Edit
                        </h3>
                        <div class="space-y-4">
                            <div class="flex gap-3">
                                <div class="text-primary text-xs font-black">TIP</div>
                                <p class="text-xs font-bold leading-relaxed text-slate-500">Perubahan judul sesi akan
                                    langsung tercermin pada laporan akhir dan dashboard utama.</p>
                            </div>
                        </div>
                    </div>

                    <div class="group relative overflow-hidden rounded-2xl bg-slate-900 p-6 text-white shadow-xl">
                        <svg class="absolute -bottom-4 -right-4 h-24 w-24 text-white opacity-5 transition-transform group-hover:scale-110"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-primary mb-1 text-[10px] font-black uppercase tracking-widest">Catatan Sistem</p>
                        <p class="text-xs font-bold leading-relaxed opacity-80">Pastikan tahun yang dimasukkan sesuai dengan
                            rentang data yang akan dianalisis.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
