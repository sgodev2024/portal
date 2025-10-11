<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name'            => 'required|string|max:255',
            'username'        => 'required|string|max:255|unique:users,username,' . $userId,
            'email'           => 'required|email|unique:users,email,' . $userId,
            'phone'           => 'nullable|string|max:20',
            'tax_code'        => 'nullable|string|max:50',
            'identity_number' => 'nullable|string|max:50',
            'gender'          => 'nullable|in:male,female,other',
            'birthday'        => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'username.unique' => 'Tên đăng nhập đã tồn tại.',
            'username.max' => 'Tên đăng nhập không được vượt quá 255 ký tự.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã được sử dụng.',

            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',

            'tax_code.max' => 'Mã số thuế không được vượt quá 50 ký tự.',

            'identity_number.max' => 'Số CCCD/CMND không được vượt quá 50 ký tự.',

            'gender.in' => 'Giới tính không hợp lệ.',

            'birthday.date' => 'Ngày sinh không hợp lệ.',
        ];
    }
}
