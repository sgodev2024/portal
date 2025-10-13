<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Bỏ qua hàng trống
        if (empty($row['email']) && empty($row['name'])) {
            return null;
        }

        $rawPassword = $row['password'] ?? '123456';

        return new User([
            'name' => $row['name'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'identity_number' => $row['identity_number'] ?? null,
            'password' => Hash::make($rawPassword),
            'role' => 3,
            'must_update_profile' => true,
            'is_active' => true,
            'tax_code' => $row['tax_code'] ?? null,
            'gender' => $row['gender'] ?? null,
            'birthday' => isset($row['birthday']) ? date('Y-m-d', strtotime($row['birthday'])) : null,
        ]);
    }
}
