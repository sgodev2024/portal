<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'phone' => '0900000001',
            'password' => Hash::make('123456'),
            'role' => 1,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Nhân viên',
            'email' => 'staff@gmail.com',
            'phone' => '0900000002',
            'password' => Hash::make('123456'),
            'role' => 2,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Người dùng',
            'email' => 'user@gmail.com',
            'phone' => '0900000003',
            'password' => Hash::make('123456'),
            'role' => 3,
            'is_active' => true,
        ]);
    }
}
