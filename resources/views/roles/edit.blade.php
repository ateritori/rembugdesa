@extends('layouts.dashboard')

@section('title', 'Edit Role')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Edit Role
                </h1>
                <p class="adaptive-text-sub mt-2 max-w-xl text-sm leading-relaxed">
                    Ubah nama role dan kelola pengguna yang terkait.
                </p>
            </div>
        </div>

        {{-- FORM --}}
        <div class="adaptive-card max-w-xl p-6">
            <form method="POST" action="{{ route('superadmin.roles.update', $role) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="adaptive-text-sub mb-1 block text-xs font-black uppercase tracking-wider opacity-60">
                        Nama Role
                    </label>
                    <input type="text" name="name" value="{{ old('name', $role->name) }}"
                        class="border-app bg-app w-full rounded-xl px-4 py-3 text-sm font-bold" required>

                    @error('name')
                        <p class="mt-1 text-xs font-bold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- PERMISSION --}}
                <div>
                    <label class="adaptive-text-sub mb-3 block text-xs font-black uppercase tracking-wider opacity-60">
                        Permission
                    </label>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @forelse ($permissions as $permission)
                            <label
                                class="flex items-center gap-3 rounded-xl border border-app bg-app px-4 py-3 text-xs font-bold">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                    @checked(in_array($permission->name, $rolePermissions))
                                    class="rounded border-gray-300 text-primary focus:ring-primary">
                                {{ $permission->name }}
                            </label>
                        @empty
                            <p class="text-xs opacity-50">
                                Belum ada permission
                            </p>
                        @endforelse
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('superadmin.roles.index') }}"
                        class="bg-app border-app rounded-xl px-5 py-2.5 text-xs font-black uppercase tracking-wider transition hover:opacity-80">
                        Batal
                    </a>

                    <button type="submit"
                        class="bg-primary shadow-primary/20 rounded-xl px-6 py-2.5 text-xs font-black uppercase tracking-wider text-white shadow-lg transition hover:scale-105 active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
