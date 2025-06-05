<?php
namespace App\Requests\PickupRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePickupRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'recipient_city' => 'required|string|max:100',
            'recipient_province' => 'required|string|max:100',
            'recipient_postal_code' => 'required|string|max:10',
            'recipient_address' => 'required|string|max:500',
            'recipient_latitude' => 'nullable|numeric|between:-90,90',
            'recipient_longitude' => 'nullable|numeric|between:-180,180',
            'pickup_name' => 'required|string|max:255',
            'pickup_phone' => 'required|string|max:20',
            'pickup_city' => 'required|string|max:100',
            'pickup_province' => 'required|string|max:100',
            'pickup_postal_code' => 'required|string|max:10',
            'pickup_address' => 'required|string|max:500',
            'pickup_latitude' => 'nullable|numeric|between:-90,90',
            'pickup_longitude' => 'nullable|numeric|between:-180,180',
            'pickup_scheduled_at' => 'nullable|date|after:now',
            'payment_method' => ['required', Rule::in(['balance', 'wallet', 'cod'])],
            'shipping_cost' => 'required|numeric|min:0',
            'service_fee' => 'nullable|numeric|min:0',
            'courier_service' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'recipient_name.string' => 'Nama penerima harus berupa teks.',
            'recipient_name.max' => 'Nama penerima maksimal 255 karakter.',
            'recipient_phone.required' => 'Nomor telepon penerima wajib diisi.',
            'recipient_phone.string' => 'Nomor telepon penerima harus berupa teks.',
            'recipient_phone.max' => 'Nomor telepon penerima maksimal 20 karakter.',
            'recipient_city.required' => 'Kota penerima wajib diisi.',
            'recipient_city.string' => 'Kota penerima harus berupa teks.',
            'recipient_city.max' => 'Kota penerima maksimal 100 karakter.',
            'recipient_province.required' => 'Provinsi penerima wajib diisi.',
            'recipient_province.string' => 'Provinsi penerima harus berupa teks.',
            'recipient_province.max' => 'Provinsi penerima maksimal 100 karakter.',
            'recipient_postal_code.required' => 'Kode pos penerima wajib diisi.',
            'recipient_postal_code.string' => 'Kode pos penerima harus berupa teks.',
            'recipient_postal_code.max' => 'Kode pos penerima maksimal 10 karakter.',
            'recipient_address.required' => 'Alamat penerima wajib diisi.',
            'recipient_address.string' => 'Alamat penerima harus berupa teks.',
            'recipient_address.max' => 'Alamat penerima maksimal 500 karakter.',
            'pickup_name.required' => 'Nama pickup wajib diisi.',
            'pickup_name.string' => 'Nama pickup harus berupa teks.',
            'pickup_name.max' => 'Nama pickup maksimal 255 karakter.',
            'pickup_phone.required' => 'Nomor telepon pickup wajib diisi.',
            'pickup_phone.string' => 'Nomor telepon pickup harus berupa teks.',
            'pickup_phone.max' => 'Nomor telepon pickup maksimal 20 karakter.',
            'pickup_city.required' => 'Kota pickup wajib diisi.',
            'pickup_city.string' => 'Kota pickup harus berupa teks.',
            'pickup_city.max' => 'Kota pickup maksimal 100 karakter.',
            'pickup_province.required' => 'Provinsi pickup wajib diisi.',
            'pickup_province.string' => 'Provinsi pickup harus berupa teks.',
            'pickup_province.max' => 'Provinsi pickup maksimal 100 karakter.',
            'pickup_postal_code.required' => 'Kode pos pickup wajib diisi.',
            'pickup_postal_code.string' => 'Kode pos pickup harus berupa teks.',
            'pickup_postal_code.max' => 'Kode pos pickup maksimal 10 karakter.',
            'pickup_address.required' => 'Alamat pickup wajib diisi.',
            'pickup_address.string' => 'Alamat pickup harus berupa teks.',
            'pickup_address.max' => 'Alamat pickup maksimal 500 karakter.',
            'pickup_scheduled_at.date' => 'Jadwal pickup harus berupa tanggal yang valid.',
            'pickup_scheduled_at.after' => 'Jadwal pickup harus setelah waktu sekarang.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran harus salah satu dari: balance, wallet, atau cod.',
            'shipping_cost.required' => 'Biaya pengiriman wajib diisi.',
            'shipping_cost.numeric' => 'Biaya pengiriman harus berupa angka.',
            'shipping_cost.min' => 'Biaya pengiriman tidak boleh kurang dari 0.',
            'service_fee.numeric' => 'Biaya layanan harus berupa angka.',
            'service_fee.min' => 'Biaya layanan tidak boleh kurang dari 0.',
            'courier_service.string' => 'Layanan kurir harus berupa teks.',
            'courier_service.max' => 'Layanan kurir maksimal 100 karakter.',
            'notes.string' => 'Catatan harus berupa teks.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
            'items.required' => 'Item wajib diisi.',
            'items.array' => 'Item harus berupa array.',
            'items.min' => 'Minimal harus ada 1 item.',
            'items.*.product_id.required' => 'ID produk wajib diisi.',
            'items.*.product_id.exists' => 'Produk yang dipilih tidak valid.',
            'items.*.quantity.required' => 'Jumlah item wajib diisi.',
            'items.*.quantity.integer' => 'Jumlah item harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Jumlah item minimal 1.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        $validated['user_id'] = auth()->id();
        return $validated;
    }

}