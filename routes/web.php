<?php

use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Log;



Route::get('/check-auth', function () {
    return auth()->check() ? 'Logged in' : 'Not logged in';
});

Route::view('/test', 'test');

Route::get('/incident-reports/create', function () {
    return view('incidents.create');
})->middleware('auth')->name('incident_reports.create');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('requests', RequestController::class);

    // Incident report routes
    Route::post('/incident-reports', [IncidentReportController::class, 'store'])->name('incident_reports.store');
    Route::get('/incident-reports', [IncidentReportController::class, 'index'])->name('incident_reports.index');
    Route::get('/incident-reports/{id}', [IncidentReportController::class, 'show'])->name('incident_reports.show');
    Route::put('/incident-reports/{id}', [IncidentReportController::class, 'update'])->name('incident_reports.update');
    // In routes/web.php inside 'auth' middleware group
    Route::get('/incident-reports/create', [IncidentReportController::class, 'create'])->name('incident_reports.create');

    // Resident’s own reports
    Route::get('/my-incident-reports', [IncidentReportController::class, 'myReports'])->name('incident_reports.my_reports');
});

// Admin routes, protected by 'auth' and 'admin' middleware
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
});

// Geolocation

Route::get('/reverse-geocode', function () {
    $lat = request('lat');
    $lon = request('lon');

    if (!$lat || !$lon) {
        return response()->json(['error' => 'Missing coordinates'], 400);
    }

    try {
        $response = Http::withHeaders([
            'User-Agent' => 'YourAppName/1.0 (youremail@example.com)' // replace with real email
        ])->get("https://nominatim.openstreetmap.org/reverse", [
            'format' => 'json',
            'lat' => $lat,
            'lon' => $lon
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Reverse geocode failed'], 500);
    } catch (\Exception $e) {
        Log::error('Reverse geocode error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
});

require __DIR__ . '/auth.php';
