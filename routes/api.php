<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeocodingController;
use App\Http\Controllers\Api\PurokApprovalController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes
Route::get('/reverse-geocode', [GeocodingController::class, 'reverseGeocode'])->name('api.reverse-geocode');
Route::get('/ip-location', [GeocodingController::class, 'getIpLocation'])->name('api.ip-location');

// Pending purok approvals count
Route::middleware('auth:sanctum')->get('/pending-purok-count', [PurokApprovalController::class, 'getPendingCount'])->name('api.pending-purok-count');
