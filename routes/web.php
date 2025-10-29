<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Middleware\PurokLeaderMiddleware;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeocodingController;
use App\Http\Controllers\KioskController;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\EmailVerificationController;

// ============================================
// KIOSK ROUTES (Public - No Authentication)
// ============================================
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/', [KioskController::class, 'index'])->name('index');
    Route::get('/information', [KioskController::class, 'information'])->name('information');
    Route::get('/services', [KioskController::class, 'services'])->name('services');
    Route::get('/officials', [KioskController::class, 'officials'])->name('officials');
    Route::get('/announcements', [KioskController::class, 'announcements'])->name('announcements');
    Route::get('/contact', [KioskController::class, 'contact'])->name('contact');
    Route::get('/requirements', [KioskController::class, 'requirements'])->name('requirements');
    Route::get('/qr-code', [KioskController::class, 'qrCode'])->name('qr-code');
    Route::get('/reset', [KioskController::class, 'reset'])->name('reset');
});

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Route to submit feedback via AJAX
Route::post('/feedback/submit', [\App\Http\Controllers\FeedbackController::class, 'submit'])
    ->name('feedback.submit')
    ->middleware('auth');

// ============================================
// Email Verification Routes
// ============================================
// Note: Email verification routes are now handled in routes/auth.php
// The routes below are commented out to avoid duplicate route names

/*
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
*/

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

// Dashboard route for all authenticated users (no email verification required for officials)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Request show route - accessible to all authenticated users (barangay officials, residents, etc.)
Route::middleware(['auth'])->group(function () {
    Route::get('/requests/{request}', [RequestController::class, 'show'])->name('requests.show');
});

// Routes that require an approved resident account and verified email
Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckResidentApproved::class])->group(function () {
    // Requests - with rate limiting on creation
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])
        ->middleware('throttle:5,60') // 5 requests per hour
        ->name('requests.store');
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

// ============================================
// Report Routes - Accessible to authorized roles only
// ============================================
Route::prefix('reports')->middleware(['auth'])->group(function () {
    // Preview reports
    Route::get('/residents', [\App\Http\Controllers\ReportController::class, 'residents'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.residents');
    
    // View resident profile from reports
    Route::get('/residents/{user}', [\App\Http\Controllers\ReportController::class, 'showResident'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.residents.show');
        
    Route::get('/purok-leaders', [\App\Http\Controllers\ReportController::class, 'purokLeaders'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.purok-leaders');
    
    // View purok leader profile from reports
    Route::get('/purok-leaders/{user}', [\App\Http\Controllers\ReportController::class, 'showLeader'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.purok-leaders.show');
        
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

    // Public Announcements for Residents and Purok Leaders
    Route::get('/announcements', [\App\Http\Controllers\AnnouncementPublicController::class, 'index'])->name('announcements.public');

    // Announcement Management Routes for Barangay Officials
    Route::prefix('barangay/announcements')->name('barangay.announcements.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Barangay\AnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Barangay\AnnouncementController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Barangay\AnnouncementController::class, 'store'])->name('store');
        Route::get('/{announcement}', [\App\Http\Controllers\Barangay\AnnouncementController::class, 'show'])->name('show');
        Route::get('/{announcement}/edit', [\App\Http\Controllers\Barangay\AnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{announcement}', [\App\Http\Controllers\Barangay\AnnouncementController::class, 'update'])->name('update');
        Route::delete('/{announcement}', [\App\Http\Controllers\Barangay\AnnouncementController::class, 'destroy'])->name('destroy');
    });

    // This route is already using {id} which matches the controller method
    Route::get('/reverse-geocode', [GeocodingController::class, 'reverse']);
    Route::post('/feedback/skip', [\App\Http\Controllers\FeedbackController::class, 'skip'])
        ->name('feedback.skip');
});

// ============================================
// Admin Routes
// ============================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
});

require __DIR__ . '/auth.php';
