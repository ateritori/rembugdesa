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
        $role = Role::firstOrCreate([
            'name' => 'superadmin',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'superadmin@desa.id'],
            [
                'name' => 'superadmin',
                'password' => Hash::make('admin234'),
            ]
        );

        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }
    }
}
