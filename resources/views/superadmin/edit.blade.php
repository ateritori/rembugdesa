@extends('layouts.dashboard')

@section('title', 'Edit Pengguna')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Edit Pengguna
                </h1>
                <p class="adaptive-text-sub mt-2 max-w-xl text-sm leading-relaxed">
                    Perbarui peran akses pengguna secara terkendali.
                </p>
            </div>
        </div>

        {{-- CONTENT SECTION --}}
        <div class="adaptive-card max-w-xl p-6">
            <form method="POST" action="{{ route('superadmin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="adaptive-text-sub mb-1 block text-xs font-black uppercase tracking-wider opacity-60">
                        Nama
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="border-app bg-app w-full rounded-xl px-4 py-3 text-sm font-bold">
                </div>

                <div>
                    <label class="adaptive-text-sub mb-1 block text-xs font-black uppercase tracking-wider opacity-60">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="border-app bg-app w-full rounded-xl px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="adaptive-text-sub mb-1 block text-xs font-black uppercase tracking-wider opacity-60">
                        Password Baru
                    </label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diganti"
                        class="border-app bg-app w-full rounded-xl px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="adaptive-text-sub mb-1 block text-xs font-black uppercase tracking-wider opacity-60">
                        Konfirmasi Password
                    </label>
                    <input type="password" name="password_confirmation"
                        class="border-app bg-app w-full rounded-xl px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="adaptive-text-sub mb-1 block text-xs font-black uppercase tracking-wider opacity-60">
                        Role
                    </label>
                    <select name="role" class="border-app bg-app w-full rounded-xl px-4 py-3 text-sm font-bold">
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>
                                {{ strtoupper($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('superadmin.users.index') }}"
                        class="bg-app border-app rounded-xl px-5 py-2.5 text-xs font-black uppercase tracking-wider transition hover:opacity-80">
                        Batal
                    </a>

                    <button type="submit"
                        class="bg-primary shadow-primary/20 rounded-xl px-6 py-2.5 text-xs font-black uppercase tracking-wider text-white shadow-lg transition hover:scale-105 active:scale-95">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
