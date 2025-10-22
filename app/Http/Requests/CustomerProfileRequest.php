<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class CustomerProfileRequest extends FormRequest
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
            'account_id' => ['nullable', 'string', 'max:15'],
            'address' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ];

        // Nếu bắt buộc cập nhật profile (must_update_profile = true)
        if ($mustUpdateProfile) {
            $rules['password'] = ['required', 'string', Password::min(8), 'confirmed'];
        } else {
            // Nếu không bắt buộc, kiểm tra điều kiện đổi mật khẩu
            // Nếu nhập current_password hoặc password thì validate đầy đủ
            if ($this->filled('current_password') || $this->filled('password')) {
                $rules['current_password'] = ['required', 'string', 'current_password'];
                $rules['password'] = ['required', 'string', Password::min(8), 'confirmed'];
            }
        }

        return $rules;
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
            'account_id.max' => 'Số điện thoại không được vượt quá 15 ký tự',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự',
            'avatar.image' => 'File phải là hình ảnh',
            'avatar.mimes' => 'Ảnh phải có định dạng: jpg, jpeg, png, gif',
            'avatar.max' => 'Kích thước ảnh không được vượt quá 2MB',
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'current_password.current_password' => 'Mật khẩu hiện tại không đúng',
            'password.required' => 'Mật khẩu mới là bắt buộc',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
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
            'account_id' => 'số điện thoại',
            'address' => 'địa chỉ',
            'avatar' => 'ảnh đại diện',
            'current_password' => 'mật khẩu hiện tại',
            'password' => 'mật khẩu mới',
        ];
    }
}
