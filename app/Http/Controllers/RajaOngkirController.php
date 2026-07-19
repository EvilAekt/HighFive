<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('RAJAONGKIR_API_KEY');
        $this->baseUrl = env('RAJAONGKIR_BASE_URL', 'https://api.rajaongkir.com/starter');
    }

    public function getProvinces()
    {
        try {
            $response = Http::timeout(5)->withHeaders([
                'key' => $this->apiKey
            ])->get("{$this->baseUrl}/province");

            if ($response->successful()) {
                return response()->json($response->json()['rajaongkir']['results'] ?? []);
            }
            throw new \Exception('API Error');
        } catch (\Exception $e) {
            // Mock Fallback Data
            return response()->json([
                ['province_id' => '5', 'province' => 'DI Yogyakarta'],
                ['province_id' => '6', 'province' => 'DKI Jakarta'],
                ['province_id' => '9', 'province' => 'Jawa Barat'],
                ['province_id' => '10', 'province' => 'Jawa Tengah'],
                ['province_id' => '11', 'province' => 'Jawa Timur'],
            ]);
        }
    }

    public function getCities($provinceId)
    {
        try {
            $response = Http::timeout(5)->withHeaders([
                'key' => $this->apiKey
            ])->get("{$this->baseUrl}/city", [
                'province' => $provinceId
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['rajaongkir']['results'] ?? []);
            }
            throw new \Exception('API Error');
        } catch (\Exception $e) {
            // Mock Fallback Data
            return response()->json([
                ['city_id' => '501', 'province_id' => '5', 'type' => 'Kota', 'city_name' => 'Yogyakarta'],
                ['city_id' => '152', 'province_id' => '6', 'type' => 'Kota', 'city_name' => 'Jakarta Pusat'],
                ['city_id' => '153', 'province_id' => '6', 'type' => 'Kota', 'city_name' => 'Jakarta Selatan'],
                ['city_id' => '22', 'province_id' => '9', 'type' => 'Kota', 'city_name' => 'Bandung'],
                ['city_id' => '398', 'province_id' => '10', 'type' => 'Kota', 'city_name' => 'Semarang'],
                ['city_id' => '444', 'province_id' => '11', 'type' => 'Kota', 'city_name' => 'Surabaya'],
            ]);
        }
    }

    public function getCost(Request $request)
    {
        $request->validate([
            'destination' => 'required',
            'courier' => 'required',
        ]);

        try {
            $response = Http::timeout(5)->withHeaders([
                'key' => $this->apiKey
            ])->post("{$this->baseUrl}/cost", [
                'origin' => env('RAJAONGKIR_ORIGIN_CITY', 501),
                'destination' => $request->destination,
                'weight' => 1000,
                'courier' => strtolower($request->courier)
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['rajaongkir']['results'][0]['costs'] ?? []);
            }
            throw new \Exception('API Error');
        } catch (\Exception $e) {
            // Mock Fallback Data
            return response()->json([
                [
                    'service' => 'REG',
                    'description' => 'Layanan Reguler Mock',
                    'cost' => [
                        ['value' => 25000, 'etd' => '2-3', 'note' => '']
                    ]
                ],
                [
                    'service' => 'YES',
                    'description' => 'Yakin Esok Sampai Mock',
                    'cost' => [
                        ['value' => 45000, 'etd' => '1', 'note' => '']
                    ]
                ]
            ]);
        }
    }
}
