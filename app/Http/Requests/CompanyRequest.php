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
            'company_name'         => 'required|string|max:255',
            'company_address'      => 'required|string|max:255',
            'company_phone'        => 'required|numeric|regex:/^0[0-9]{9,10}$/', // Kiểm tra số điện thoại
            'company_email'        => 'nullable|email|max:255',
            'footer'               => 'nullable|string|max:250',
            'company_website'      => 'nullable|url',
            'tax_id'               => 'required|string|max:20',
            'vat_rate'             => 'required|numeric|min:0|max:100',
            'representative_name'  => 'required|string|max:255',
            'representative_position' => 'nullable|string|max:255',
            'representative_phone' => 'nullable|numeric|regex:/^0[0-9]{9,10}$/',
            'representative_email' => 'nullable|email',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }
}
