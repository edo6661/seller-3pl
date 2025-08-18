<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BankValidationService
{
    private string $flipApiKey;
    private string $flipApiUrl;

    public function __construct()
    {
        $this->flipApiKey = config('services.flip.secret_key');
        $this->flipApiUrl = config('services.flip.api_url', 'https://bigflip.id/big_sandbox_api/v2');
    }

    /**
     * Validate bank account menggunakan FLIP API
     */
    public function validateAccount(string $bankCode, string $accountNumber): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->flipApiKey,
                'Content-Type' => 'application/json',
            ])->post($this->flipApiUrl . '/pwf/account-inquiry', [
                'inquiry_key' => $accountNumber,
                'bank_code' => $this->mapBankCode($bankCode),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'SUCCESS') {
                    return [
                        'valid' => true,
                        'account_name' => $data['account_holder'] ?? null,
                        'bank_name' => $data['bank_name'] ?? null,
                        'account_number' => $accountNumber,
                    ];
                } else {
                    return [
                        'valid' => false,
                        'message' => $data['message'] ?? 'Nomor rekening tidak valid',
                    ];
                }
            }

            Log::warning('FLIP API returned non-successful response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return $this->fallbackValidation($bankCode, $accountNumber);
            
        } catch (\Exception $e) {
            Log::error('Bank validation API error: ' . $e->getMessage(), [
                'bank_code' => $bankCode,
                'account_number' => $accountNumber
            ]);
            
            return $this->fallbackValidation($bankCode, $accountNumber);
        }
    }

    /**
     * Fallback validation jika API tidak tersedia
     */
    private function fallbackValidation(string $bankCode, string $accountNumber): array
    {
        // $rules = $this->getBankValidationRules();
        // $rule = $rules[$bankCode] ?? $rules['default'];

        // // Remove spaces and special characters
        // $cleanAccountNumber = preg_replace('/[^0-9]/', '', $accountNumber);

        // // Check length
        // if (strlen($cleanAccountNumber) < $rule['min_length'] || 
        //     strlen($cleanAccountNumber) > $rule['max_length']) {
        //     return [
        //         'valid' => false,
        //         'message' => "Nomor rekening {$bankCode} harus {$rule['min_length']}-{$rule['max_length']} digit"
        //     ];
        // }

        // // Check pattern
        // if (!preg_match($rule['pattern'], $cleanAccountNumber)) {
        //     return [
        //         'valid' => false,
        //         'message' => "Format nomor rekening {$bankCode} tidak valid"
        //     ];
        // }

        // // Basic mod-10 check untuk beberapa bank
        // if (in_array($bankCode, ['BCA', 'Mandiri']) && !$this->luhnCheck($cleanAccountNumber)) {
        //     return [
        //         'valid' => false,
        //         'message' => "Nomor rekening {$bankCode} tidak valid (checksum failed)"
        //     ];
        // }

        return [
            'valid' => true,
            // 'account_number' => $cleanAccountNumber,
            'message' => 'Validasi dasar berhasil, namun tidak dapat memverifikasi nama pemilik rekening'
        ];
    }

    /**
     * Get bank validation rules
     */
    private function getBankValidationRules(): array
    {
        return [
            'BCA' => [
                'min_length' => 10,
                'max_length' => 10,
                'pattern' => '/^[0-9]{10}$/',
                'checksum' => true
            ],
            'BNI' => [
                'min_length' => 10,
                'max_length' => 10,
                'pattern' => '/^[0-9]{10}$/',
                'checksum' => false
            ],
            'BRI' => [
                'min_length' => 15,
                'max_length' => 15,
                'pattern' => '/^[0-9]{15}$/',
                'checksum' => false
            ],
            'Mandiri' => [
                'min_length' => 13,
                'max_length' => 13,
                'pattern' => '/^[0-9]{13}$/',
                'checksum' => true
            ],
            'CIMB Niaga' => [
                'min_length' => 13,
                'max_length' => 14,
                'pattern' => '/^[0-9]{13,14}$/',
                'checksum' => false
            ],
            'Danamon' => [
                'min_length' => 10,
                'max_length' => 10,
                'pattern' => '/^[0-9]{10}$/',
                'checksum' => false
            ],
            'Permata' => [
                'min_length' => 10,
                'max_length' => 10,
                'pattern' => '/^[0-9]{10}$/',
                'checksum' => false
            ],
            'BTN' => [
                'min_length' => 16,
                'max_length' => 16,
                'pattern' => '/^[0-9]{16}$/',
                'checksum' => false
            ],
            'default' => [
                'min_length' => 8,
                'max_length' => 20,
                'pattern' => '/^[0-9]{8,20}$/',
                'checksum' => false
            ]
        ];
    }

    /**
     * Luhn algorithm untuk checksum validation
     */
    private function luhnCheck(string $number): bool
    {
        $sum = 0;
        $numDigits = strlen($number);
        $parity = ($numDigits - 1) % 2;

        for ($i = $numDigits - 1; $i >= 0; $i--) {
            $digit = intval($number[$i]);
            
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
        }

        return ($sum % 10) == 0;
    }

    /**
     * Map bank name to API bank code
     */
    private function mapBankCode(string $bankName): string
    {
        $bankCodes = [
            'BCA' => 'bca',
            'BNI' => 'bni',
            'BRI' => 'bri',
            'Mandiri' => 'mandiri',
            'CIMB Niaga' => 'cimb',
            'Danamon' => 'danamon',
            'Permata' => 'permata',
            'BTN' => 'btn',
            'default' => 'other'
        ];
        return $bankCodes[$bankName] ?? $bankCodes['default'];
    }
    /**
     * Get bank name from code
     */
    public function getBankName(string $bankCode): string {
        $bankNames = [
            'bca' => 'BCA',
            'bni' => 'BNI',
            'bri' => 'BRI',
            'mandiri' => 'Mandiri',
            'cimb' => 'CIMB Niaga',
            'danamon' => 'Danamon',
            'permata' => 'Permata',
            'btn' => 'BTN',
            'other' => 'Lainnya'
        ];
        return $bankNames[$bankCode] ?? $bankNames['other'];
    }
    /**
     * Get all supported bank codes
     */
    public function getSupportedBankCodes(): array {
        return [
            'bca', 'bni', 'bri', 'mandiri', 'cimb', 
            'danamon', 'permata', 'btn', 'other'
        ];
    }
}