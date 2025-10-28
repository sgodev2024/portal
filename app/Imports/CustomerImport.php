<?php

namespace App\Imports;

use App\Models\User;
use App\Models\CustomerGroup;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class CustomerImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;
    public function model(array $row)
    {
        // Bỏ qua hàng trống
        if (empty($row['email']) && empty($row['name'])) {
            return null;
        }

        // Kiểm tra email đã tồn tại
        if (User::where('email', $row['email'])->exists()) {
            Log::warning("Email đã tồn tại: {$row['email']}");
            return null;
        }

        // Tạo account_id từ phone nếu không có
        $accountId = $row['account_id'] ?? null;
        if (empty($accountId) && !empty($row['phone'])) {
            $accountId = $this->generateAccountId($row['phone']);
        }

        // Mật khẩu mặc định
        $rawPassword = $row['password'] ?? '123456';

        // Tạo user
        $user = new User([
            'name'                => $row['name'] ?? null,
            'email'               => $row['email'] ?? null,
            'phone'               => $row['phone'] ?? null,
            'account_id'          => $accountId,
            'company'             => $row['company'] ?? null,
            'address'             => $row['address'] ?? null,
            'password'            => Hash::make($rawPassword),
            'role'                => 3,
          'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : false,
            'must_update_profile' => true,
        ]);

        $user->save();

        // Gán nhóm khách hàng nếu có
        if (!empty($row['group_id'])) {
            $groupIds = is_array($row['group_id'])
                ? $row['group_id']
                : array_map('trim', explode(',', $row['group_id']));

            // Lọc các group_id hợp lệ
            $validGroupIds = CustomerGroup::whereIn('id', $groupIds)->pluck('id')->toArray();

            if (!empty($validGroupIds)) {
                $user->groups()->attach($validGroupIds);
            }
        }

        return $user;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'company'  => 'nullable|string|max:255',
            'address'  => 'nullable|string|max:500',
            'group_id' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required'  => 'Họ tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.email'    => 'Email không đúng định dạng.',
            'email.unique'   => 'Email đã được sử dụng.',
        ];
    }

    private function generateAccountId($phone)
    {
        $last3Digits = substr(preg_replace('/\D/', '', $phone), -3);
        $randomNumber = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        return 'KH' . $last3Digits . $randomNumber;
    }
}
