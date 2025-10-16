<?php

use App\Http\Controllers\Auth\VerifyEmailController;

// Simple test route
Route::get('/test-page', function() {
    return 'Test page is working';
});

// Simple test route
Route::get('/test-route', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'Test route is working',
        'user' => auth()->check() ? auth()->user()->only('id', 'name', 'email', 'role', 'is_approved') : 'Not authenticated'
    ]);
});
use App\Http\Middleware\PurokLeaderMiddleware;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeocodingController;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;


// Test route to check file access
Route::get('/test-file-access', function () {
    $latestRequest = \App\Models\Request::latest()->first();
    
    if (!$latestRequest) {
        return 'No requests found';
    }

    $frontPath = $latestRequest->valid_id_front_path;
    $backPath = $latestRequest->valid_id_back_path;

    $frontExists = file_exists(public_path($frontPath));
    $backExists = file_exists(public_path($backPath));

    return [
        'request_id' => $latestRequest->id,
        'front_path' => $frontPath,
        'front_exists' => $frontExists ? 'Yes' : 'No',
        'front_public_url' => $frontPath ? asset($frontPath) : 'N/A',
        'back_path' => $backPath,
        'back_exists' => $backExists ? 'Yes' : 'No',
        'back_public_url' => $backPath ? asset($backPath) : 'N/A',
    ];
});

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
    session([
        'show_feedback_prompt' => true,
        'resolved_count' => 4
    ]);
    
    return redirect()->route('dashboard');
})->middleware('auth')->name('test.feedback.prompt');

// Test route for checking authentication status
Route::get('/check-auth', function () {
    return auth()->check() ? 'Logged in' : 'Not logged in';
});

// Test incident reports route
Route::get('/test-incident-reports', function () {
    return 'Test incident reports route is working';
})->name('test.incident_reports');

// Direct test for myReports method
Route::get('/test-my-reports', [IncidentReportController::class, 'myReports']);

// Debug route to check user role and permissions
Route::get('/debug/user', function () {
    if (!auth()->check()) {
        return 'No user is logged in';
    }
    
    $user = auth()->user();
    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'purok_id' => $user->purok_id,
        'is_president' => $user->role === 'purok_leader' ? 'Yes' : 'No',
    ];
})->middleware('auth');

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
        session()->flash('show_feedback_prompt', true);
        session()->flash('resolved_count', 3);
        
        return view('test-feedback');
    })->middleware(['auth']);
}

Route::view('/test', 'test');

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

// Barangay Official Approval Routes
Route::middleware('auth')
    ->prefix('barangay/approvals')
    ->name('barangay.approvals.')
    ->group(function () {
        // Index and show routes are accessible by both barangay officials and purok leaders
        Route::middleware(['can:viewAny,App\Models\Request'])->group(function () {
            Route::get('/', [\App\Http\Controllers\BarangayApprovalController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\BarangayApprovalController::class, 'show'])->name('show');
        });
        
        // Approve/Reject actions require barangay official permissions
        Route::middleware(['can:barangay-official-actions'])->group(function () {
            Route::post('/{id}/approve', [\App\Http\Controllers\BarangayApprovalController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\BarangayApprovalController::class, 'reject'])->name('reject');
        });
    });

// Dashboard route for all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Routes that require an approved resident account
Route::middleware(['auth', \App\Http\Middleware\CheckResidentApproved::class])->group(function () {
    // Requests - with rate limiting on creation
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])
        ->middleware('throttle:5,60') // 5 requests per hour
        ->name('requests.store');
    Route::get('/requests/{request}', [RequestController::class, 'show'])->name('requests.show');
    Route::get('/requests/{request}/edit', [RequestController::class, 'edit'])->name('requests.edit');
    Route::put('/requests/{request}', [RequestController::class, 'update'])->name('requests.update');
    Route::delete('/requests/{request}', [RequestController::class, 'destroy'])->name('requests.destroy');
    Route::get('/my-requests', [RequestController::class, 'myRequests'])->name('requests.my_requests');
    
    // Other routes that require an approved account
    Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])
        ->middleware('throttle:10,60') // 10 feedback submissions per hour
        ->name('feedback.store');
});

// Incident Reports - Accessible to all authenticated users
Route::middleware('auth')->group(function () {
    Route::prefix('incident-reports')->name('incident_reports.')->group(function() {
        Route::get('/', [IncidentReportController::class, 'index'])->name('index');
        Route::get('/my_reports', [IncidentReportController::class, 'myReports'])->name('my_reports');
        Route::get('/create', [IncidentReportController::class, 'create'])->name('create');
        Route::post('/', [IncidentReportController::class, 'store'])
            ->middleware('throttle:10,60') // 10 incident reports per hour
            ->name('store');
        Route::get('/{id}', [IncidentReportController::class, 'show'])->name('show');
    });
});

// Public routes that don't require approval but need authentication
Route::middleware('auth')->group(function () {
    // Account deletion for rejected users
    Route::delete('/account', [ProfileController::class, 'destroyAccount'])->name('account.destroy');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Password update routes
    Route::get('/profile/password', [ProfileController::class, 'edit'])->name('profile.password.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    // Feedback routes
    Route::get('/feedback/{type}/{id}', [\App\Http\Controllers\FeedbackController::class, 'showFeedbackForm'])
        ->name('feedback.show');
    Route::post('/feedback/skip', [\App\Http\Controllers\FeedbackController::class, 'skip'])
        ->name('feedback.skip');
});

// Purok Leader Dashboard
Route::get('/purok-leader/dashboard', [\App\Http\Controllers\PurokLeaderController::class, 'dashboard'])
    ->middleware(['auth', PurokLeaderMiddleware::class])
    ->name('purok_leader.dashboard');

// Purok Leader - Purok Change Requests
Route::prefix('purok-leader')->middleware(['auth', PurokLeaderMiddleware::class])->group(function () {
    // List purok change requests
    Route::get('/purok-change-requests', [\App\Http\Controllers\PurokLeaderController::class, 'purokChangeRequests'])
        ->name('purok_leader.purok_change_requests');
        
    // Approve purok change request
    Route::post('/purok-change-requests/{changeRequest}/approve', [\App\Http\Controllers\PurokLeaderController::class, 'approvePurokChange'])
        ->name('purok_leader.approve-purok-change');
        
    // Reject purok change request
    Route::post('/purok-change-requests/{changeRequest}/reject', [\App\Http\Controllers\PurokLeaderController::class, 'rejectPurokChange'])
        ->name('purok_leader.reject-purok-change');
});

// Purok Leader - View and Manage Residents
Route::prefix('purok-leader')->middleware(['auth', PurokLeaderMiddleware::class])->group(function () {
    // List residents
    Route::get('/residents', [\App\Http\Controllers\PurokLeaderController::class, 'residents'])
        ->name('purok_leader.residents');
        
    // View resident details
    Route::get('/residents/{id}', [\App\Http\Controllers\PurokLeaderController::class, 'showResident'])
        ->name('purok_leader.residents.show');
        
    // Approve/Reject resident
    Route::prefix('residents/{resident}')->group(function () {
        // Approve resident
        Route::patch('/approve', [\App\Http\Controllers\PurokLeader\PurokResidentController::class, 'approve'])
            ->name('purok_leader.residents.approve');
            
        // Show reject form
        Route::get('/reject', [\App\Http\Controllers\PurokLeader\PurokResidentController::class, 'showRejectForm'])
            ->name('purok_leader.residents.reject-form');
            
        // Process rejection
        Route::post('/reject', [\App\Http\Controllers\PurokLeader\PurokResidentController::class, 'reject'])
            ->name('purok_leader.residents.reject');
    });
});

// Update request status (approve/reject)
Route::patch('/requests/{request}/status', [RequestController::class, 'updateStatus'])
    ->middleware(['auth', PurokLeaderMiddleware::class])
    ->name('requests.updateStatus');

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

// Debug route to check user roles and permissions
Route::get('/debug/user-info', function () {
    $user = auth()->user();
    if (!$user) {
        return 'No user is logged in';
    }
    
    // Get user's actual role from database
    $dbUser = \App\Models\User::find($user->id);
    
    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'purok_id' => $user->purok_id,
        'db_role' => $dbUser->role, // Get role directly from database
        'is_president' => in_array($user->role, ['purok_leader', 'purok_president']) ? 'Yes' : 'No',
        'can_reject' => $user->can('reject', \App\Models\Request::first()),
        'middleware' => \Route::current()->middleware(),
    ];
})->middleware('auth');

Route::get('/debug/logs', function () {
    if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'purok_leader', 'purok_president'])) {
        abort(403, 'Unauthorized.');
    }
});

// Report routes - Accessible to authorized roles only
Route::prefix('reports')->middleware(['auth', 'verified'])->group(function () {
    // Preview reports
    Route::get('/residents', [\App\Http\Controllers\ReportController::class, 'residents'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.residents');
        
    Route::get('/purok-leaders', [\App\Http\Controllers\ReportController::class, 'purokLeaders'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.purok-leaders');
        
    Route::get('/purok-clearance', [\App\Http\Controllers\ReportController::class, 'purokClearance'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,purok_president,admin')
        ->name('reports.purok-clearance');
        
    Route::get('/incident-reports', [\App\Http\Controllers\ReportController::class, 'incidentReports'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.incident-reports');
    
    // Download reports
    Route::post('/download/residents', [\App\Http\Controllers\ReportController::class, 'downloadResidents'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.download.residents');
        
    Route::post('/download/purok-leaders', [\App\Http\Controllers\ReportController::class, 'downloadPurokLeaders'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.download.purok-leaders');
        
    Route::post('/download/purok-clearance', [\App\Http\Controllers\ReportController::class, 'downloadPurokClearance'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,purok_president,admin')
        ->name('reports.download.purok-clearance');
        
    Route::post('/download/incident-reports', [\App\Http\Controllers\ReportController::class, 'downloadIncidentReports'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.download.incident-reports');
});

// Consolidated auth routes
Route::middleware('auth')->group(function () {
    // Purok Leader Routes
    Route::middleware(['can:viewPendingPurok,App\Models\Request', PurokLeaderMiddleware::class])->group(function () {
        Route::get('/requests/pending/purok', [RequestController::class, 'pendingPurok'])->name('requests.pending-purok');
        Route::post('/requests/{request}/approve-purok', [RequestController::class, 'approvePurok'])->name('requests.approve-purok');
        Route::put('/requests/{request}/update-private-notes', [RequestController::class, 'updatePrivateNotes'])->name('requests.update-private-notes');
    });

    // Reject route for both purok leaders and barangay officials
    Route::post('/requests/{request}/reject', [RequestController::class, 'reject'])
        ->name('requests.reject');

    // Barangay Official Routes - Request Related
    Route::middleware('can:viewPendingBarangay,App\Models\Request')->group(function () {
        Route::get('/requests/pending/barangay', [RequestController::class, 'pendingBarangay'])->name('requests.pending-barangay');
        Route::post('/requests/{request}/approve-barangay', [RequestController::class, 'approveBarangay'])->name('requests.approve-barangay');
        Route::post('/requests/{request}/complete', [RequestController::class, 'complete'])->name('requests.complete');
    });

    // Incident Report Routes for Barangay Officials
    Route::prefix('barangay/incident-reports')->name('barangay.incident_reports.')->group(function () {
        Route::get('/', [IncidentReportController::class, 'pendingApproval'])->name('index');
        Route::post('/{incidentReport}/approve', [IncidentReportController::class, 'approve'])->name('approve');
        Route::post('/{incidentReport}/reject', [IncidentReportController::class, 'reject'])->name('reject');
        Route::post('/{incidentReport}/in-progress', [IncidentReportController::class, 'markInProgress'])->name('in_progress');
        Route::post('/{incidentReport}/resolve', [IncidentReportController::class, 'markResolved'])->name('resolve');
    });
    // This route is already using {id} which matches the controller method
    Route::get('/reverse-geocode', [GeocodingController::class, 'reverse']);
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

// Debug route to check user permissions
Route::get('/debug/permissions', function () {
    $user = auth()->user();
    
    if (!$user) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }
    
    // Test the Gate directly
    $gate = app('Illuminate\Contracts\Auth\Access\Gate');
    $canBarangayActions = $gate->forUser($user)->check('barangay-official-actions');
    
    // Get user's role from database directly
    $dbUser = \App\Models\User::find($user->id);
    
    // Check if user has any of the barangay official roles
    $isBarangayOfficial = in_array($user->role, [
        'barangay_captain',
        'barangay_kagawad',
        'secretary',
        'sk_chairman',
        'admin'
    ]);
    
    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'purok_id' => $user->purok_id ?? null,
            'email_verified_at' => $user->email_verified_at ? 'verified' : 'not verified'
        ],
        'database_check' => [
            'user_exists' => (bool)$dbUser,
            'db_role' => $dbUser ? $dbUser->role : 'not found'
        ],
        'permissions' => [
            'is_authenticated' => auth()->check(),
            'is_barangay_official' => $isBarangayOfficial,
            'can_barangay_actions' => $canBarangayActions,
            'gate_defined' => $gate->has('barangay-official-actions'),
            'gate_check' => $canBarangayActions
        ],
        'session' => [
            'id' => session()->getId(),
            'previous_url' => url()->previous()
        ],
        'auth' => [
            'driver' => config('auth.defaults.guard'),
            'user_class' => get_class(auth()->user())
        ]
    ]);
})->middleware('auth');

// Temporary debug route to view logs
Route::get('/debug/logs', function () {
    if (!in_array(auth()->user()->role, ['admin', 'barangay_captain', 'barangay_kagawad'])) {
        abort(403, 'Unauthorized');
    }
    
    $logFile = storage_path('logs/laravel.log');
    if (!file_exists($logFile)) {
        return 'Log file not found';
    }
    
    $logs = file_get_contents($logFile);
    return '<pre>' . htmlspecialchars($logs) . '</pre>';
})->middleware('auth');

// Temporary debug route - remove after use
Route::get('/debug/request/{id}', function ($id) {
    $request = \App\Models\Request::with(['user', 'purok'])->find($id);
    
    if (!$request) {
        return response()->json(['error' => 'Request not found'], 404);
    }
    
    // Use getAttribute to safely access properties
    $requestData = [
        'id' => $request->getAttribute('id'),
        'status' => $request->getAttribute('status'),
        'purok_id' => $request->getAttribute('purok_id'),
        'user_id' => $request->getAttribute('user_id'),
        'created_at' => $request->getAttribute('created_at'),
        'purok_approved_at' => $request->getAttribute('purok_approved_at'),
        'barangay_approved_at' => $request->getAttribute('barangay_approved_at'),
        'rejected_at' => $request->getAttribute('rejected_at'),
    ];
    
    // Add user data if available
    if ($request->relationLoaded('user') && $request->user) {
        $requestData['user'] = [
            'id' => $request->user->getAttribute('id'),
            'name' => $request->user->getAttribute('name'),
            'role' => $request->user->getAttribute('role')
        ];
    } else {
        $requestData['user'] = null;
    }
    
    // Add purok data if available
    if ($request->relationLoaded('purok') && $request->purok) {
        $requestData['purok'] = [
            'id' => $request->purok->getAttribute('id'),
            'name' => $request->purok->getAttribute('name')
        ];
    } else {
        $requestData['purok'] = null;
    }
    
    return response()->json($requestData);
})->middleware('auth');

require __DIR__ . '/auth.php';
