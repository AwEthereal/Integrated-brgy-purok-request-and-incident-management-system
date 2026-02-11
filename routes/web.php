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
use App\Http\Controllers\PublicSubmission\PublicClearanceController;
use App\Http\Controllers\PublicSubmission\PublicIncidentController;
use App\Models\Announcement;
use App\Http\Controllers\Secretary\PurokLeaderAccountController;
use App\Http\Controllers\Captain\SecretaryAccountController;

use App\Http\Controllers\FeedbackController;

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

// Secretary/Barangay Official - View Purok Clearance Document (read-only view page)
Route::middleware(['auth','checkrole:secretary,barangay_captain,barangay_kagawad,admin'])->group(function () {
    Route::get('/official/requests/{request}/document', [\App\Http\Controllers\Pdf\PurokClearanceDocumentController::class, 'officialView'])
        ->name('official.clearance.view');

    Route::get('/official/requests/{request}/document/preview', [\App\Http\Controllers\Pdf\PurokClearanceDocumentController::class, 'officialPreview'])
        ->name('official.clearance.preview');
    Route::post('/official/requests/{request}/document/draft', [\App\Http\Controllers\Pdf\PurokClearanceDocumentController::class, 'officialUpdateDraft'])
        ->name('official.clearance.draft');
    Route::post('/official/requests/{request}/document/finalize', [\App\Http\Controllers\Pdf\PurokClearanceDocumentController::class, 'officialFinalize'])
        ->name('official.clearance.finalize');
});

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Route to submit feedback via AJAX
Route::post('/feedback/submit', [\App\Http\Controllers\FeedbackController::class, 'submit'])
    ->name('feedback.submit')
    ->middleware('auth');

// CSM / General feedback form
Route::get('/feedback/general', [FeedbackController::class, 'general'])
    ->middleware('auth')
    ->name('feedback.general');

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
    Route::get('/requests/{request}', [RequestController::class, 'show'])
        ->whereNumber('request')
        ->name('requests.show');
});

// Routes that require an approved resident account and verified email
Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckResidentApproved::class])->group(function () {
    // Requests - with rate limiting on creation
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])
        ->name('requests.store');
    Route::get('/requests/{request}/edit', [RequestController::class, 'edit'])->name('requests.edit');
    Route::put('/requests/{request}', [RequestController::class, 'update'])->name('requests.update');
    Route::delete('/requests/{request}', [RequestController::class, 'destroy'])->name('requests.destroy');
    Route::get('/my-requests', [RequestController::class, 'myRequests'])->name('requests.my_requests');
});

// Incident Reports - Accessible to all authenticated users
Route::middleware('auth')->group(function () {
    Route::prefix('incident-reports')->name('incident_reports.')->group(function() {
        Route::get('/', [IncidentReportController::class, 'index'])->name('index');
        Route::get('/my_reports', [IncidentReportController::class, 'myReports'])->name('my_reports');
        Route::get('/create', [IncidentReportController::class, 'create'])->name('create');
        Route::post('/', [IncidentReportController::class, 'store'])
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
});

// Analytics endpoints for dashboards
Route::middleware('auth')->prefix('analytics')->group(function () {
    Route::get('/clearances', [\App\Http\Controllers\DashboardAnalyticsController::class, 'clearances'])->name('analytics.clearances');
    Route::get('/incidents', [\App\Http\Controllers\DashboardAnalyticsController::class, 'incidents'])->name('analytics.incidents');
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

    // Purok Clearance Document (View/Preview/Edit/Finalize)
    Route::get('/requests/{request}/document', [\App\Http\Controllers\Pdf\PurokClearanceDocumentController::class, 'view'])
        ->name('purok_leader.clearance.view');
    Route::get('/requests/{request}/document/preview', [\App\Http\Controllers\Pdf\PurokClearanceDocumentController::class, 'preview'])
        ->name('purok_leader.clearance.preview');
    Route::post('/requests/{request}/document/draft', [\App\Http\Controllers\Pdf\PurokClearanceDocumentController::class, 'updateDraft'])
        ->name('purok_leader.clearance.draft');
    Route::post('/requests/{request}/document/finalize', [\App\Http\Controllers\Pdf\PurokClearanceDocumentController::class, 'finalize'])
        ->name('purok_leader.clearance.finalize');

    // Resident Records (RBI Form B) CRUD moved to a broader access group below
});

// Resident Records (RBI Form B) CRUD - accessible to leaders, secretary, barangay officials, and admin
Route::prefix('purok-leader')->middleware(['auth','checkrole:purok_leader,secretary,barangay_captain,barangay_kagawad,admin'])->group(function () {
    Route::prefix('resident-records')->name('purok_leader.resident_records.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PurokLeader\ResidentRecordController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\PurokLeader\ResidentRecordController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\PurokLeader\ResidentRecordController::class, 'store'])->name('store');
        Route::get('/{record}/pdf', [\App\Http\Controllers\PurokLeader\ResidentRecordController::class, 'pdf'])->name('pdf');
        Route::get('/{record}/edit', [\App\Http\Controllers\PurokLeader\ResidentRecordController::class, 'edit'])->name('edit');
        Route::put('/{record}', [\App\Http\Controllers\PurokLeader\ResidentRecordController::class, 'update'])->name('update');
        Route::delete('/{record}', [\App\Http\Controllers\PurokLeader\ResidentRecordController::class, 'destroy'])->name('destroy');
        Route::get('/{record}', [\App\Http\Controllers\PurokLeader\ResidentRecordController::class, 'show'])->name('show');
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
    // HTML preview for residents (all or selected via ?ids=1,2,3)
    Route::get('/preview/residents', [\App\Http\Controllers\ReportController::class, 'previewResidents'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.preview.residents');

    // RBI Residents PDF preview (all or selected via ?ids=1,2,3)
    Route::get('/preview/residents-rbi', [\App\Http\Controllers\ReportController::class, 'previewResidentsRbi'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.preview.residents-rbi');
    
    // View resident profile from reports
    Route::get('/residents/{user}', [\App\Http\Controllers\ReportController::class, 'showResident'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.residents.show');
        
    Route::get('/purok-leaders', [\App\Http\Controllers\ReportController::class, 'purokLeaders'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.purok-leaders');
    // HTML preview for purok leaders (all or selected via ?ids=1,2,3)
    Route::get('/preview/purok-leaders', [\App\Http\Controllers\ReportController::class, 'previewPurokLeaders'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.preview.purok-leaders');
    
    // View purok leader profile from reports
    Route::get('/purok-leaders/{user}', [\App\Http\Controllers\ReportController::class, 'showLeader'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.purok-leaders.show');
        
    Route::get('/purok-clearance', [\App\Http\Controllers\ReportController::class, 'purokClearance'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,purok_leader,admin')
        ->name('reports.purok-clearance');
    // HTML preview for purok clearance report (all or selected via ?ids=1,2,3)
    Route::get('/preview/purok-clearance', [\App\Http\Controllers\ReportController::class, 'previewPurokClearance'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,purok_leader,admin')
        ->name('reports.preview.purok-clearance');
        
    Route::get('/incident-reports', [\App\Http\Controllers\ReportController::class, 'incidentReports'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.incident-reports');
    // HTML preview for incident reports (all or selected via ?ids=1,2,3)
    Route::get('/preview/incident-reports', [\App\Http\Controllers\ReportController::class, 'previewIncidentReports'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.preview.incident-reports');
    
    // Download reports
    Route::post('/download/residents', [\App\Http\Controllers\ReportController::class, 'downloadResidents'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.download.residents');

    Route::get('/pdf/residents', [\App\Http\Controllers\ReportController::class, 'pdfResidents'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.pdf.residents');
        
    Route::post('/download/purok-leaders', [\App\Http\Controllers\ReportController::class, 'downloadPurokLeaders'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.download.purok-leaders');

    Route::get('/pdf/purok-leaders', [\App\Http\Controllers\ReportController::class, 'pdfPurokLeaders'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.pdf.purok-leaders');
        
    Route::post('/download/purok-clearance', [\App\Http\Controllers\ReportController::class, 'downloadPurokClearance'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,purok_leader,admin')
        ->name('reports.download.purok-clearance');

    Route::get('/pdf/purok-clearance', [\App\Http\Controllers\ReportController::class, 'pdfPurokClearance'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,purok_leader,admin')
        ->name('reports.pdf.purok-clearance');
        
    Route::post('/download/incident-reports', [\App\Http\Controllers\ReportController::class, 'downloadIncidentReports'])
        ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
        ->name('reports.download.incident-reports');
});

// Consolidated auth routes
Route::middleware('auth')->group(function () {
    // Purok Leader Routes
    Route::middleware(['can:viewPendingPurok,App\Models\Request', PurokLeaderMiddleware::class])->group(function () {
        Route::get('/requests/pending/purok', [RequestController::class, 'pendingPurok'])->name('requests.pending-purok');
        Route::put('/requests/{request}/update-private-notes', [RequestController::class, 'updatePrivateNotes'])->name('requests.update-private-notes');
    });

    Route::post('/requests/{request}/approve-purok', [RequestController::class, 'approvePurok'])
        ->middleware('can:approvePurok,request')
        ->name('requests.approve-purok');

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
    Route::get('/reverse-geocode', [GeocodingController::class, 'reverseGeocode']);
});

// ============================================
// Admin Routes
// ============================================
Route::middleware(['auth', 'checkrole:barangay_captain,admin,barangay_kagawad'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('purge-data', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'purgeData'])
        ->middleware('checkrole:barangay_captain,admin')
        ->name('purge-data');
    Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
});

// Secretary: Manage Purok Leader accounts
Route::middleware(['auth', 'checkrole:secretary,barangay_captain,admin'])
    ->prefix('secretary/purok-leaders')
    ->name('secretary.purok-leaders.')
    ->group(function () {
        Route::get('/', [PurokLeaderAccountController::class, 'index'])->name('index');
        Route::get('/create', [PurokLeaderAccountController::class, 'create'])->name('create');
        Route::post('/', [PurokLeaderAccountController::class, 'store'])->name('store');
        Route::get('/{purok_leader}/edit', [PurokLeaderAccountController::class, 'edit'])->name('edit');
        Route::get('/{purok_leader}/personal-info', [PurokLeaderAccountController::class, 'editPersonalInfo'])->name('personal-info.edit');
        Route::put('/{purok_leader}/personal-info', [PurokLeaderAccountController::class, 'updatePersonalInfo'])->name('personal-info.update');
        Route::put('/{purok_leader}', [PurokLeaderAccountController::class, 'update'])->name('update');
        Route::delete('/{purok_leader}', [PurokLeaderAccountController::class, 'destroy'])->name('destroy');
    });

// Barangay Captain: Manage Secretary and similar official accounts
Route::middleware(['auth', 'checkrole:barangay_captain,admin'])
    ->prefix('captain/secretaries')
    ->name('captain.secretaries.')
    ->group(function () {
        Route::get('/', [SecretaryAccountController::class, 'index'])->name('index');
        Route::get('/create', [SecretaryAccountController::class, 'create'])->name('create');
        Route::post('/', [SecretaryAccountController::class, 'store'])->name('store');
        Route::get('/{secretary}/edit', [SecretaryAccountController::class, 'edit'])->name('edit');
        Route::put('/{secretary}', [SecretaryAccountController::class, 'update'])->name('update');
        Route::delete('/{secretary}', [SecretaryAccountController::class, 'destroy'])->name('destroy');
    });

// Legacy aliases for old superadmin URL
Route::middleware(['auth'])->group(function () {
    Route::get('/superadmin', function () {
        return redirect()->route('admin.dashboard');
    })->name('superadmin.redirect');
    Route::get('/superadmin/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('superadmin.dashboard');
});

// ============================================
// Public Submission Routes (feature-flagged)
// ============================================
if (config('features.public_forms')) {
    Route::prefix('public')->name('public.')->group(function () {
        // Landing page for public services
        Route::get('/', function () {
            $announcements = Announcement::active()
                ->published()
                ->with('creator')
                ->orderBy('is_featured', 'desc')
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
            return view('public.landing', compact('announcements'));
        })->name('landing');

        // Public Announcements (dedicated pages; no kiosk redirect)
        Route::get('/announcements', [\App\Http\Controllers\AnnouncementPublicController::class, 'publicIndex'])
            ->name('announcements.index');
        Route::get('/announcements/{announcement}', [\App\Http\Controllers\AnnouncementPublicController::class, 'publicShow'])
            ->name('announcements.show');

        // Public Clearance
        Route::get('/clearance', [PublicClearanceController::class, 'create'])->name('clearance.create');
        Route::post('/clearance', [PublicClearanceController::class, 'store'])
            ->name('clearance.store');

        // Public Incident
        Route::get('/incident', [PublicIncidentController::class, 'create'])->name('incident.create');
        Route::post('/incident', [PublicIncidentController::class, 'store'])
            ->name('incident.store');

        // Public Feedback
        Route::get('/feedback', [\App\Http\Controllers\FeedbackController::class, 'publicGeneral'])
            ->name('feedback.general');
        Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])
            ->name('feedback.store');

        // Thank You page
        Route::get('/thanks', function () {
            return view('public.thanks');
        })->name('thanks');
    });
}

require __DIR__ . '/auth.php';
