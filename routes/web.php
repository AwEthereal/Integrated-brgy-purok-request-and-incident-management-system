<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeocodingController;
use App\Http\Middleware\PurokLeaderMiddleware;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;


// Debug route to check session data
Route::get('/debug-session', function (HttpRequest $request) {
    return [
        'session_data' => [
            'show_feedback_prompt' => session('show_feedback_prompt'),
            'resolved_count' => session('resolved_count'),
            'user_id' => auth()->id(),
            'session_id' => session()->getId()
        ],
        'cookies' => $request->cookies->all(),
    ];
})->middleware('auth');

// Test route for feedback prompt
Route::get('/test-feedback-prompt', function (HttpRequest $request) {
    if (!auth()->check()) {
        return 'Please log in first';
    }
    
    // Simulate having 4 resolved items (within the 3-5 range)
    $request->session()->flash('show_feedback_prompt', true);
    $request->session()->flash('resolved_count', 4);
    
    return redirect()->route('dashboard');
})->middleware('auth')->name('test.feedback.prompt');

// Test route for checking authentication status
Route::get('/check-auth', function () {
    return auth()->check() ? 'Logged in' : 'Not logged in';
});

// Route to submit feedback via AJAX
Route::post('/feedback/submit', [\App\Http\Controllers\FeedbackController::class, 'submit'])
    ->name('feedback.submit')
    ->middleware('auth');

// Debug route to force show feedback prompt
Route::get('/debug/feedback-prompt', function () {
    if (!auth()->check()) {
        return 'Please log in first';
    }
    
    // Force show the feedback prompt
    session([
        'show_feedback_prompt' => true,
        'pending_feedback' => [
            'type' => 'request',
            'id' => 1, // This should be a valid request ID
            'title' => 'Test Feedback Request',
            'item' => [
                'id' => 1,
                'title' => 'Test Request',
                'description' => 'This is a test request for feedback',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]
    ]);
    
    return redirect()->route('dashboard');
})->name('debug.feedback.prompt');

// Test route for feedback prompt (only in local environment)
if (app()->environment('local')) {
    Route::get('/test/feedback', function (HttpRequest $request) {
        // Simulate having 3 resolved items
        $request->session()->flash('show_feedback_prompt', true);
        $request->session()->flash('resolved_count', 3);
        
        return view('test-feedback');
    })->middleware(['auth']);
}

Route::view('/test', 'test');

Route::get('/incident-reports/create', function () {
    return view('incidents.create');
})->middleware('auth')->name('incident_reports.create');

Route::get('/', function () {
    return view('welcome');
});

// Test route - remove after testing

// Email Verification Routes
use App\Http\Controllers\EmailVerificationController;

Route::middleware('auth')->group(function () {
    // Show notice if email needs verification
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    // Handle email verification
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Resend verification email
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Update email and require verification
    Route::post('/email/update', [EmailVerificationController::class, 'update'])
        ->name('verification.update');
});

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// Purok Leader Dashboard
Route::get('/purok-leader/dashboard', [\App\Http\Controllers\PurokLeaderController::class, 'dashboard'])
    ->middleware(['auth', \App\Http\Middleware\PurokLeaderMiddleware::class])
    ->name('purok_leader.dashboard');

// Purok Leader - View Residents
Route::get('/purok-leader/residents', [\App\Http\Controllers\PurokLeaderController::class, 'residents'])
    ->middleware(['auth', \App\Http\Middleware\PurokLeaderMiddleware::class])
    ->name('purok_leader.residents');

// Update request status (approve/reject)
Route::patch('/requests/{request}/status', [RequestController::class, 'updateStatus'])
    ->middleware(['auth', PurokLeaderMiddleware::class])
    ->name('requests.update-status');

// Feedback routes
Route::prefix('feedback')->middleware('auth')->group(function () {
    // Show feedback form for a specific item
    Route::get('/{type}/{id}', [\App\Http\Controllers\FeedbackController::class, 'showFeedbackForm'])
        ->name('feedback.show');
        
    // Submit feedback
    Route::post('/', [\App\Http\Controllers\FeedbackController::class, 'store'])
        ->name('feedback.store');
        
    // Skip feedback for an item
    Route::post('/skip', [\App\Http\Controllers\FeedbackController::class, 'skip'])
        ->name('feedback.skip');
});

// Test route for debugging
Route::get('/test-dashboard', function () {
    return 'Test Dashboard - ' . (auth()->check() ? 'Logged in as: ' . auth()->user()->email : 'Not logged in');
})->middleware('auth');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Password update routes
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Purok Clearance Requests
    Route::resource('requests', RequestController::class);
    Route::get('/my-requests', [RequestController::class, 'myRequests'])->name('requests.my_requests');

    // Purok Leader Routes
    Route::middleware('can:viewPendingPurok,App\Models\Request')->group(function () {
        Route::get('/requests/pending/purok', [RequestController::class, 'pendingPurok'])->name('requests.pending-purok');
        Route::post('/requests/{request}/approve-purok', [RequestController::class, 'approvePurok'])->name('requests.approve-purok');
    });

    // Barangay Official Routes
    Route::middleware('can:viewPendingBarangay,App\Models\Request')->group(function () {
        Route::get('/requests/pending/barangay', [RequestController::class, 'pendingBarangay'])->name('requests.pending-barangay');
        Route::post('/requests/{request}/approve-barangay', [RequestController::class, 'approveBarangay'])->name('requests.approve-barangay');
        Route::post('/requests/{request}/reject', [RequestController::class, 'reject'])->name('requests.reject');
        Route::post('/requests/{request}/complete', [RequestController::class, 'complete'])->name('requests.complete');
    });

    // Incident report routes
    Route::post('/incident-reports', [IncidentReportController::class, 'store'])->name('incident_reports.store');
    Route::get('/incident-reports', [IncidentReportController::class, 'index'])->name('incident_reports.index');
    Route::get('/incident-reports/{id}', [IncidentReportController::class, 'show'])->name('incident_reports.show');
    Route::put('/incident-reports/{id}', [IncidentReportController::class, 'update'])->name('incident_reports.update');
    Route::get('/reverse-geocode', [GeocodingController::class, 'reverse']);
    // In routes/web.php inside 'auth' middleware group
    Route::get('/incident-reports/create', [IncidentReportController::class, 'create'])->name('incident_reports.create');

    // Resident’s own reports
    Route::get('/my-incident-reports', [IncidentReportController::class, 'myReports'])->name('incident_reports.my_reports');
    
    // Feedback routes
    Route::get('/feedback', [\App\Http\Controllers\FeedbackController::class, 'showFeedbackForm'])
        ->name('feedback.form');
        
    Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])
        ->name('feedback.store');
        
    Route::post('/feedback/skip', [\App\Http\Controllers\FeedbackController::class, 'skip'])
        ->name('feedback.skip');
});

// Admin routes, protected by 'auth' and 'admin' middleware
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
});

// Test email verification
Route::get('/test-email-verification', function () {
    $user = \App\Models\User::first();
    if (!$user) {
        return 'No user found. Please create a test user first.';
    }

    try {
        $user->sendEmailVerificationNotification();
        return 'Verification email sent to ' . $user->email;
    } catch (\Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
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

if (app()->environment('local')) {
    require __DIR__.'/test-feedback.php';
}

require __DIR__ . '/auth.php';
