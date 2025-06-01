<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePickupRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Recipient data
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'recipient_city' => 'required|string|max:100',
            'recipient_province' => 'required|string|max:100',
            'recipient_postal_code' => 'required|string|max:10',
            'recipient_address' => 'required|string|max:500',
            'recipient_latitude' => 'nullable|numeric|between:-90,90',
            'recipient_longitude' => 'nullable|numeric|between:-180,180',

            // Pickup data
            'pickup_name' => 'required|string|max:255',
            'pickup_phone' => 'required|string|max:20',
            'pickup_city' => 'required|string|max:100',
            'pickup_province' => 'required|string|max:100',
            'pickup_postal_code' => 'required|string|max:10',
            'pickup_address' => 'required|string|max:500',
            'pickup_latitude' => 'nullable|numeric|between:-90,90',
            'pickup_longitude' => 'nullable|numeric|between:-180,180',
            'pickup_scheduled_at' => 'required|date|after:now',

            // Payment and costs
            'payment_method' => 'required|in:cod,prepaid',
            'shipping_cost' => 'required|numeric|min:0',
            'service_fee' => 'required|numeric|min:0',
            'product_total' => 'required|numeric|min:0',
            'cod_amount' => 'required_if:payment_method,cod|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',

            // Service details
            'courier_service' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',

            // Items
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.weight_per_pcs' => 'required|numeric|min:0',
            'items.*.price_per_pcs' => 'required|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            // Recipient messages
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'recipient_name.max' => 'Nama penerima maksimal 255 karakter.',
            'recipient_phone.required' => 'Nomor telepon penerima wajib diisi.',
            'recipient_phone.max' => 'Nomor telepon penerima maksimal 20 karakter.',
            'recipient_city.required' => 'Kota penerima wajib diisi.',
            'recipient_city.max' => 'Kota penerima maksimal 100 karakter.',
            'recipient_province.required' => 'Provinsi penerima wajib diisi.',
            'recipient_province.max' => 'Provinsi penerima maksimal 100 karakter.',
            'recipient_postal_code.required' => 'Kode pos penerima wajib diisi.',
            'recipient_postal_code.max' => 'Kode pos penerima maksimal 10 karakter.',
            'recipient_address.required' => 'Alamat penerima wajib diisi.',
            'recipient_address.max' => 'Alamat penerima maksimal 500 karakter.',
            'recipient_latitude.numeric' => 'Latitude penerima harus berupa angka.',
            'recipient_latitude.between' => 'Latitude penerima harus antara -90 sampai 90.',
            'recipient_longitude.numeric' => 'Longitude penerima harus berupa angka.',
            'recipient_longitude.between' => 'Longitude penerima harus antara -180 sampai 180.',

            // Pickup messages
            'pickup_name.required' => 'Nama pengirim wajib diisi.',
            'pickup_name.max' => 'Nama pengirim maksimal 255 karakter.',
            'pickup_phone.required' => 'Nomor telepon pengirim wajib diisi.',
            'pickup_phone.max' => 'Nomor telepon pengirim maksimal 20 karakter.',
            'pickup_city.required' => 'Kota pickup wajib diisi.',
            'pickup_city.max' => 'Kota pickup maksimal 100 karakter.',
            'pickup_province.required' => 'Provinsi pickup wajib diisi.',
            'pickup_province.max' => 'Provinsi pickup maksimal 100 karakter.',
            'pickup_postal_code.required' => 'Kode pos pickup wajib diisi.',
            'pickup_postal_code.max' => 'Kode pos pickup maksimal 10 karakter.',
            'pickup_address.required' => 'Alamat pickup wajib diisi.',
            'pickup_address.max' => 'Alamat pickup maksimal 500 karakter.',
            'pickup_latitude.numeric' => 'Latitude pickup harus berupa angka.',
            'pickup_latitude.between' => 'Latitude pickup harus antara -90 sampai 90.',
            'pickup_longitude.numeric' => 'Longitude pickup harus berupa angka.',
            'pickup_longitude.between' => 'Longitude pickup harus antara -180 sampai 180.',
            'pickup_scheduled_at.required' => 'Jadwal pickup wajib diisi.',
            'pickup_scheduled_at.date' => 'Jadwal pickup harus berupa tanggal yang valid.',
            'pickup_scheduled_at.after' => 'Jadwal pickup harus setelah waktu sekarang.',

            // Payment messages
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran harus COD atau Prabayar.',
            'shipping_cost.required' => 'Biaya pengiriman wajib diisi.',
            'shipping_cost.numeric' => 'Biaya pengiriman harus berupa angka.',
            'shipping_cost.min' => 'Biaya pengiriman tidak boleh kurang dari 0.',
            'service_fee.required' => 'Biaya layanan wajib diisi.',
            'service_fee.numeric' => 'Biaya layanan harus berupa angka.',
            'service_fee.min' => 'Biaya layanan tidak boleh kurang dari 0.',
            'product_total.required' => 'Total produk wajib diisi.',
            'product_total.numeric' => 'Total produk harus berupa angka.',
            'product_total.min' => 'Total produk tidak boleh kurang dari 0.',
            'cod_amount.required_if' => 'Jumlah COD wajib diisi untuk metode COD.',
            'cod_amount.numeric' => 'Jumlah COD harus berupa angka.',
            'cod_amount.min' => 'Jumlah COD tidak boleh kurang dari 0.',
            'total_amount.required' => 'Total amount wajib diisi.',
            'total_amount.numeric' => 'Total amount harus berupa angka.',
            'total_amount.min' => 'Total amount tidak boleh kurang dari 0.',

            // Service messages
            'courier_service.required' => 'Layanan kurir wajib dipilih.',
            'courier_service.max' => 'Layanan kurir maksimal 100 karakter.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',

            // Items messages
            'items.required' => 'Item produk wajib diisi.',
            'items.array' => 'Item produk harus berupa array.',
            'items.min' => 'Minimal 1 item produk.',
            'items.*.product_id.required' => 'ID produk wajib diisi.',
            'items.*.product_id.exists' => 'Produk tidak ditemukan.',
            'items.*.quantity.required' => 'Jumlah produk wajib diisi.',
            'items.*.quantity.integer' => 'Jumlah produk harus berupa angka.',
            'items.*.quantity.min' => 'Jumlah produk minimal 1.',
            'items.*.weight_per_pcs.required' => 'Berat per pcs wajib diisi.',
            'items.*.weight_per_pcs.numeric' => 'Berat per pcs harus berupa angka.',
            'items.*.weight_per_pcs.min' => 'Berat per pcs tidak boleh kurang dari 0.',
            'items.*.price_per_pcs.required' => 'Harga per pcs wajib diisi.',
            'items.*.price_per_pcs.numeric' => 'Harga per pcs harus berupa angka.',
            'items.*.price_per_pcs.min' => 'Harga per pcs tidak boleh kurang dari 0.'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->id()
        ]);
    }
}
