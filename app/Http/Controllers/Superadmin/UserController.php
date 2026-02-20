<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('superadmin.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();

        return view('superadmin.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();

        return view('superadmin.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        $passwordUpdated = false;
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
            $passwordUpdated = true;
        }

        $user->save();

        $user->syncRoles([$validated['role']]);

        $message = 'Data pengguna berhasil diperbarui.';
        if ($passwordUpdated) {
            $message .= ' Password berhasil diperbarui.';
        }

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', $message);
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('superadmin')) {
            abort(403, 'Superadmin tidak dapat dihapus.');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
