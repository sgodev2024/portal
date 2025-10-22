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
            'name'       => $row['name'] ?? null,
            'email'      => $row['email'] ?? null,
            'account_id' => $row['account_id'] ?? null,
            'company'    => $row['company'] ?? null,
            'address'    => $row['address'] ?? null,
            'password'   => Hash::make($rawPassword),
            'role'       => 3,
            'is_active'  => isset($row['is_active']) ? (bool)$row['is_active'] : false,
            'must_update_profile' => true,
        ]);
    }
}
