<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'company_name'            => 'required|string|max:255',
            'company_address'         => 'required|string|max:255',
            'company_email'           => 'nullable|email|max:255',
            'footer'                  => 'nullable|string|max:250',
            'company_logo'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'default_language'        => 'nullable|string|in:vi,de',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Vui lòng nhập tên công ty.',
            'company_name.max' => 'Tên công ty không được vượt quá 255 ký tự.',

            'company_address.required' => 'Vui lòng nhập địa chỉ công ty.',
            'company_address.max' => 'Địa chỉ công ty không được vượt quá 255 ký tự.',

            'company_email.email' => 'Email công ty không hợp lệ.',
            'company_email.max' => 'Email công ty không được vượt quá 255 ký tự.',

            'footer.max' => 'Nội dung footer không được vượt quá 250 ký tự.',

            'company_logo.image' => 'Logo công ty phải là hình ảnh.',
            'company_logo.mimes' => 'Logo chỉ chấp nhận định dạng: jpeg, png, jpg, webp.',
            'company_logo.max' => 'Kích thước logo không được vượt quá 2MB.',
        ];
    }
}
