<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = Auth::user();
        $mustUpdateProfile = $user && $user->must_update_profile;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone,' . Auth::id()],
            'company' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ];

        // Nếu bắt buộc cập nhật profile (must_update_profile = true)
        if ($mustUpdateProfile) {
            $rules['password'] = ['required', 'string', Password::min(6), 'confirmed'];
            $rules['password_confirmation'] = ['required'];
        } else {
            // Nếu không bắt buộc, kiểm tra điều kiện đổi mật khẩu
            // Nếu nhập current_password hoặc password thì validate đầy đủ
            if ($this->filled('current_password') || $this->filled('password')) {
                $rules['current_password'] = ['required', 'string'];
                $rules['password'] = ['required', 'string', Password::min(6), 'confirmed'];
                $rules['password_confirmation'] = ['required'];
            }
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        // Kiểm tra mật khẩu hiện tại nếu người dùng muốn đổi mật khẩu
        $validator->after(function ($validator) {
            $user = Auth::user();

            // Nếu không bắt buộc update profile và có nhập current_password
            if (!$user->must_update_profile && $this->filled('current_password')) {
                if (!Hash::check($this->current_password, $user->password)) {
                    $validator->errors()->add('current_password', 'Mật khẩu hiện tại không đúng');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên hiển thị là bắt buộc',
            'name.max' => 'Tên hiển thị không được vượt quá 255 ký tự',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email này đã được sử dụng',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự',
            'phone.unique' => 'Số điện thoại này đã được sử dụng',
            'company.max' => 'Tên công ty không được vượt quá 255 ký tự',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự',
            'avatar.image' => 'File phải là hình ảnh',
            'avatar.mimes' => 'Ảnh phải có định dạng: jpg, jpeg, png, gif',
            'avatar.max' => 'Kích thước ảnh không được vượt quá 2MB',
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'password.required' => 'Mật khẩu mới là bắt buộc',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu mới',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'tên hiển thị',
            'email' => 'email',
            'phone' => 'số điện thoại',
            'company' => 'công ty',
            'address' => 'địa chỉ',
            'avatar' => 'ảnh đại diện',
            'current_password' => 'mật khẩu hiện tại',
            'password' => 'mật khẩu mới',
            'password_confirmation' => 'xác nhận mật khẩu',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Loại bỏ khoảng trắng thừa
        if ($this->has('name')) {
            $this->merge(['name' => trim($this->name)]);
        }

        if ($this->has('email')) {
            $this->merge(['email' => trim(strtolower($this->email))]);
        }

        if ($this->has('phone')) {
            $this->merge(['phone' => trim($this->phone)]);
        }
    }
}
