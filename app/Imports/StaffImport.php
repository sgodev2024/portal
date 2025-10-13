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
            'name'            => $row['name'] ?? null,
            'email'           => $row['email'] ?? null,
            'phone'           => $row['phone'] ?? null,
            'identity_number' => $row['identity_number'] ?? null,
            'password'        => Hash::make($rawPassword),
            'role'            => 2,
            'is_active'       => true,
            'gender'          => $row['gender'] ?? null,
            'birthday'        => isset($row['birthday']) ? date('Y-m-d', strtotime($row['birthday'])) : null,
        ]);
    }
}
