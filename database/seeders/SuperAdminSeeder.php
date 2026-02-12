<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Super Admin
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@wonosarigk.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('wildundan'),
            ]
        );

        if (! $superadmin->hasRole($superadminRole)) {
            $superadmin->assignRole($superadminRole);
        }

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@wonosarigk.id'],
            [
                'name' => 'Admin Keputusan',
                'password' => Hash::make('wildundan'),
            ]
        );

        if (! $admin->hasRole($adminRole)) {
            $admin->assignRole($adminRole);
        }
    }
}
