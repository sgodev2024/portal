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
            'company_phone'           => 'required|numeric|regex:/^0[0-9]{9,10}$/',
            'company_email'           => 'nullable|email|max:255',
            'footer'                  => 'nullable|string|max:250',
            'company_website'         => 'nullable|url',
            'tax_id'                  => 'required|string|max:20',
            'vat_rate'                => 'required|numeric|min:0|max:100',
            'representative_name'     => 'required|string|max:255',
            'representative_position' => 'nullable|string|max:255',
            'representative_phone'    => 'nullable|numeric|regex:/^0[0-9]{9,10}$/',
            'representative_email'    => 'nullable|email',
            'company_logo'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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

            'company_phone.required' => 'Vui lòng nhập số điện thoại công ty.',
            'company_phone.numeric' => 'Số điện thoại công ty chỉ được chứa chữ số.',
            'company_phone.regex' => 'Số điện thoại công ty không hợp lệ (phải bắt đầu bằng 0 và có 10–11 số).',

            'company_email.email' => 'Email công ty không hợp lệ.',
            'company_email.max' => 'Email công ty không được vượt quá 255 ký tự.',

            'footer.max' => 'Nội dung footer không được vượt quá 250 ký tự.',

            'company_website.url' => 'Địa chỉ website không hợp lệ.',

            'tax_id.required' => 'Vui lòng nhập mã số thuế.',
            'tax_id.max' => 'Mã số thuế không được vượt quá 20 ký tự.',

            'vat_rate.required' => 'Vui lòng nhập thuế VAT.',
            'vat_rate.numeric' => 'Thuế VAT phải là số.',
            'vat_rate.min' => 'Thuế VAT không được nhỏ hơn 0%.',
            'vat_rate.max' => 'Thuế VAT không được lớn hơn 100%.',

            'representative_name.required' => 'Vui lòng nhập tên người đại diện.',
            'representative_name.max' => 'Tên người đại diện không được vượt quá 255 ký tự.',

            'representative_position.max' => 'Chức vụ người đại diện không được vượt quá 255 ký tự.',

            'representative_phone.numeric' => 'Số điện thoại người đại diện chỉ được chứa chữ số.',
            'representative_phone.regex' => 'Số điện thoại người đại diện không hợp lệ (phải bắt đầu bằng 0 và có 10–11 số).',

            'representative_email.email' => 'Email người đại diện không hợp lệ.',

            'company_logo.image' => 'Logo công ty phải là hình ảnh.',
            'company_logo.mimes' => 'Logo chỉ chấp nhận định dạng: jpeg, png, jpg, webp.',
            'company_logo.max' => 'Kích thước logo không được vượt quá 2MB.',
        ];
    }
}
