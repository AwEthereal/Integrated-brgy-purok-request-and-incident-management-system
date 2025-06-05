<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeocodingController extends Controller
{
    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
        ]);

        $lat = $request->query('lat');
        $lon = $request->query('lon');
        
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'KalawagBrgySystem/1.0 (your@email.com)'
            ])->get("https://nominatim.openstreetmap.org/reverse", [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lon,
                'zoom' => 18,
                'addressdetails' => 1,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Failed to fetch location data',
                'details' => $response->body()
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Geocoding service error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function getIpLocation(Request $request)
    {
        try {
            // Get client IP (supports both local and production environments)
            $ip = $request->ip();
            
            // For local development, use a default IP or a test IP
            if ($ip === '127.0.0.1' || $ip === '::1') {
                $ip = ''; // Let ipapi.co use the server's IP
            }
            
            // Make request to ip-api.com (better free tier)
            $response = Http::get("http://ip-api.com/json/{$ip}?fields=status,message,lat,lon,query,country,city,regionName,zip");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'success') {
                    return response()->json([
                        'success' => true,
                        'latitude' => $data['lat'],
                        'longitude' => $data['lon'],
                        'ip' => $data['query'],
                        'city' => $data['city'],
                        'region' => $data['regionName'],
                        'country' => $data['country'],
                        'zip' => $data['zip'] ?? ''
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $data['message'] ?? 'Failed to get location from IP',
                ], 400);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch IP location',
            ], $response->status());
            
        } catch (\Exception $e) {
            \Log::error('IP geolocation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while getting location',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
