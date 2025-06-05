<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    private $apiKey;
    private $baseUrl = 'https://maps.googleapis.com/maps/api';

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key');
    }

    /**
     * Geocoding: Konversi alamat ke koordinat lat/lng
     */
    public function geocodeAddress($address)
    {
        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/geocode/json', [
                'address' => $address,
                'key' => $this->apiKey,
                'language' => 'id',
                'region' => 'id',
                'components' => 'country:ID'  // Membatasi hasil ke Indonesia saja
            ]);

            if (!$response->successful()) {
                Log::error('Google Maps API HTTP Error: ' . $response->status());
                return null;
            }

            $data = $response->json();
            
            if ($data['status'] === 'OK' && !empty($data['results'])) {
                return $this->parseGeocodeResult($data['results'][0]);
            }

            Log::warning('Google Maps Geocoding: ' . ($data['status'] ?? 'Unknown status'));
            return null;
        } catch (\Exception $e) {
            Log::error('Google Maps Geocoding Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Reverse Geocoding: Konversi koordinat ke alamat
     */
    public function reverseGeocode($latitude, $longitude)
    {
        try {
            // Validasi koordinat
            if (!is_numeric($latitude) || !is_numeric($longitude)) {
                Log::error('Invalid coordinates provided for reverse geocoding');
                return null;
            }

            $response = Http::timeout(10)->get($this->baseUrl . '/geocode/json', [
                'latlng' => $latitude . ',' . $longitude,
                'key' => $this->apiKey,
                'language' => 'id',
                'region' => 'id'
            ]);

            if (!$response->successful()) {
                Log::error('Google Maps API HTTP Error: ' . $response->status());
                return null;
            }

            $data = $response->json();
            
            if ($data['status'] === 'OK' && !empty($data['results'])) {
                return $this->parseGeocodeResult($data['results'][0]);
            }

            Log::warning('Google Maps Reverse Geocoding: ' . ($data['status'] ?? 'Unknown status'));
            return null;
        } catch (\Exception $e) {
            Log::error('Google Maps Reverse Geocoding Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse hasil geocoding
     */
    private function parseGeocodeResult($result)
    {
        $location = $result['geometry']['location'];
        $addressComponents = $result['address_components'] ?? [];

        $parsedData = [
            'latitude' => $location['lat'],
            'longitude' => $location['lng'],
            'formatted_address' => $result['formatted_address'] ?? '',
            'street_number' => '',
            'route' => '',
            'sublocality' => '',
            'city' => '',
            'province' => '',
            'postal_code' => '',
            'country' => ''
        ];

        // Parse komponen alamat dengan prioritas
        foreach ($addressComponents as $component) {
            $types = $component['types'] ?? [];
            $longName = $component['long_name'] ?? '';
            $shortName = $component['short_name'] ?? '';

            if (in_array('street_number', $types)) {
                $parsedData['street_number'] = $longName;
            }
            if (in_array('route', $types)) {
                $parsedData['route'] = $longName;
            }
            if (in_array('sublocality_level_1', $types) || in_array('sublocality', $types)) {
                $parsedData['sublocality'] = $longName;
            }
            
            // Prioritas untuk kota: administrative_area_level_2 > locality > sublocality_level_1
            if (in_array('administrative_area_level_2', $types) && empty($parsedData['city'])) {
                $parsedData['city'] = $longName;
            } elseif (in_array('locality', $types) && empty($parsedData['city'])) {
                $parsedData['city'] = $longName;
            }
            
            if (in_array('administrative_area_level_1', $types)) {
                $parsedData['province'] = $longName;
            }
            if (in_array('postal_code', $types)) {
                $parsedData['postal_code'] = $longName;
            }
            if (in_array('country', $types)) {
                $parsedData['country'] = $longName;
            }
        }

        return $parsedData;
    }

    /**
     * Cari tempat berdasarkan query
     */
    public function searchPlaces($query, $location = null, $radius = 5000)
    {
        try {
            $params = [
                'query' => $query,
                'key' => $this->apiKey,
                'language' => 'id',
                'region' => 'id'
            ];

            if ($location) {
                $params['location'] = $location;
                $params['radius'] = $radius;
            }

            $response = Http::timeout(10)->get($this->baseUrl . '/place/textsearch/json', $params);

            if (!$response->successful()) {
                Log::error('Google Maps Places API HTTP Error: ' . $response->status());
                return [];
            }

            $data = $response->json();
            
            if ($data['status'] === 'OK') {
                return $data['results'];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Google Maps Places Search Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Validasi API Key
     */
    public function validateApiKey()
    {
        if (empty($this->apiKey)) {
            Log::error('Google Maps API Key is not configured');
            return false;
        }
        return true;
    }
}