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
            'account_id' => '0900000001',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 1,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Nhân viên',
            'account_id' => '0900000002',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 2,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Người dùng',
            'account_id' => '0900000003',
            'email' => 'user@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 3,
            'is_active' => true,
        ]);
    }
}
