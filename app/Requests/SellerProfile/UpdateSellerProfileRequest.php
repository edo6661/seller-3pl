<?php

namespace App\Requests\SellerProfile;use Illuminate\Foundation\Http\FormRequest;

class UpdateSellerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'business_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:1000',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    public function messages(): array
    {
        return [
            'address.required' => 'Alamat harus diisi.',
            'city.required' => 'Kota harus diisi.',
            'province.required' => 'Provinsi harus diisi.',
            'postal_code.required' => 'Kode pos harus diisi.',
            'latitude.between' => 'Latitude harus antara -90 dan 90.',
            'longitude.between' => 'Longitude harus antara -180 dan 180.',
        ];
    }
}