<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StaffProfileRequest extends FormRequest
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
            'phone' => ['required', 'string', 'max:15', 'unique:users,phone,' . Auth::id()],
            'department' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
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
            'name.required' => 'Họ tên là bắt buộc',
            'name.max' => 'Họ tên không được vượt quá 255 ký tự',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email này đã được sử dụng',
            'phone.required' => 'Số điện thoại là bắt buộc',
            'phone.max' => 'Số điện thoại không được vượt quá 15 ký tự',
            'phone.unique' => 'Số điện thoại này đã được sử dụng',
            // 'department.required' => 'Phòng ban là bắt buộc',
            // 'department.max' => 'Phòng ban không được vượt quá 255 ký tự',
            'position.required' => 'Chức vụ là bắt buộc',
            'position.max' => 'Chức vụ không được vượt quá 255 ký tự',
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
            'name' => 'họ tên',
            'email' => 'email',
            'phone' => 'số điện thoại',
            'department' => 'phòng ban',
            'position' => 'chức vụ',
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

        if ($this->has('department')) {
            $this->merge(['department' => trim($this->department)]);
        }

        if ($this->has('position')) {
            $this->merge(['position' => trim($this->position)]);
        }
    }
}
