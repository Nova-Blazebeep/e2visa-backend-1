<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'Admin']);

        $user = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Adnan Hussain',
                'email_verified_at' => now(),
                'role' => 'Admin',
                'role_id' => 1,
                'password' => Hash::make('Hello123'),
            ]
        );

        $user->assignRole($superAdminRole);
    }
}