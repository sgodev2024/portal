<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StaffImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $rawPassword = $row['password'] ?? '123456';

        return new User([
            'name'       => $row['name'] ?? null,
            'account_id'      => $row['account_id'] ?? null,
            'email'      => $row['email'] ?? null,
            'department' => $row['department'] ?? null,
            'position'   => $row['position'] ?? null,
            'password'   => Hash::make($rawPassword),
            'role'       => 2,
            'is_active'  => true,
        ]);
    }
}
