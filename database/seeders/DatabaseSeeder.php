<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Definisikan semua Role yang dibutuhkan
        $roles = ['superadmin', 'admin', 'dm'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Buat User Super Admin
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@wonosarigk.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('wildundan'),
            ]
        );
        $superadmin->assignRole('superadmin');

        // 3. Buat User Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@wonosarigk.id'],
            [
                'name' => 'Admin Keputusan',
                'password' => Hash::make('wildundan'),
            ]
        );
        $admin->assignRole('admin');

        // 4. Buat User DM (Decision Maker)
        $dm = User::firstOrCreate(
            ['email' => 'dm@wonosarigk.id'],
            [
                'name' => 'Decision Maker',
                'password' => Hash::make('wildundan'),
            ]
        );
        $dm->assignRole('dm');
    }
}
