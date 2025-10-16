# Rate Limiting & Request Limits Implementation

## 📋 Overview
This document outlines the rate limiting and request limit system implemented to prevent abuse and ensure fair usage of the Barangay Management System.

## ✅ What Has Been Implemented

### 1. **Route-Level Rate Limiting** (Priority 1 - COMPLETED)

#### Clearance Requests
- **Rate Limit:** 5 requests per hour
- **Location:** `routes/web.php` line 215-217
- **Applied to:** `POST /requests` (Request creation)
- **Middleware:** `throttle:5,60`

```php
Route::post('/requests', [RequestController::class, 'store'])
    ->middleware('throttle:5,60') // 5 requests per hour
    ->name('requests.store');
```

#### Incident Reports
- **Rate Limit:** 10 reports per hour
- **Location:** `routes/web.php` line 236-238
- **Applied to:** `POST /incident-reports` (Report creation)
- **Middleware:** `throttle:10,60`

```php
Route::post('/', [IncidentReportController::class, 'store'])
    ->middleware('throttle:10,60') // 10 incident reports per hour
    ->name('store');
```

#### Feedback Submissions
- **Rate Limit:** 10 submissions per hour
- **Location:** `routes/web.php` line 225-227
- **Applied to:** `POST /feedback` (Feedback submission)
- **Middleware:** `throttle:10,60`

```php
Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])
    ->middleware('throttle:10,60') // 10 feedback submissions per hour
    ->name('feedback.store');
```

### 2. **Pending Request Limits** (Priority 2 - COMPLETED)

#### Clearance Requests
- **Pending Limit:** Maximum 5 pending requests
- **Location:** `app/Http/Controllers/RequestController.php` lines 119-136
- **Status Check:** Includes `pending` and `purok_approved` statuses
- **Error Response:** HTTP 429 (Too Many Requests) for AJAX, redirect with error for regular requests

```php
$pendingCount = RequestModel::where('user_id', $user->id)
    ->whereIn('status', ['pending', 'purok_approved'])
    ->count();

if ($pendingCount >= 5) {
    // Return error response
}
```

#### Incident Reports
- **Pending Limit:** Maximum 10 pending reports
- **Location:** `app/Http/Controllers/IncidentReportController.php` lines 20-37
- **Status Check:** Includes `pending` and `in_progress` statuses
- **Error Response:** HTTP 429 (Too Many Requests) for AJAX, redirect with error for regular requests

```php
$pendingCount = IncidentReport::where('user_id', $user->id)
    ->whereIn('status', ['pending', 'in_progress'])
    ->count();

if ($pendingCount >= 10) {
    // Return error response
}
```

### 3. **User-Friendly Notifications** (COMPLETED)

#### Request Creation Page
- **Location:** `resources/views/requests/create.blade.php` lines 15-26
- **Type:** Blue info alert with icon
- **Message:** Displays rate limits clearly

```html
<div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded">
    <p class="font-semibold">Request Limits</p>
    <p class="text-sm mt-1">You can submit up to <strong>5 requests per hour</strong> 
    and have a maximum of <strong>5 pending requests</strong> at any time.</p>
</div>
```

#### Incident Report Creation Page
- **Location:** `resources/views/incidents/create.blade.php` lines 118-129
- **Type:** Blue info alert with icon
- **Message:** Displays rate limits clearly

```html
<div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-lg shadow-sm">
    <p class="font-semibold">Report Limits</p>
    <p class="text-sm mt-1">You can submit up to <strong>10 incident reports per hour</strong> 
    and have a maximum of <strong>10 pending reports</strong> at any time.</p>
</div>
```

## 🎯 How It Works

### Rate Limiting Flow (Laravel Throttle)
1. User submits a request/report
2. Laravel checks the throttle cache
3. If limit exceeded:
   - Returns HTTP 429 error
   - Shows "Too Many Attempts" message
   - User must wait before retrying
4. If within limit:
   - Increments counter
   - Processes the request
   - Counter resets after 60 minutes

### Pending Limits Flow (Custom Validation)
1. User tries to create a new request/report
2. Controller counts user's pending items
3. If limit exceeded:
   - Returns error message
   - Redirects back with input
   - Shows helpful error message
4. If within limit:
   - Proceeds with creation
   - Saves to database

## 🔒 Security Benefits

### Prevents:
- ✅ Database flooding
- ✅ System abuse by malicious users
- ✅ Storage overflow (ID photos, incident photos)
- ✅ Server performance degradation
- ✅ Queue overwhelming for staff
- ✅ Spam submissions

### Ensures:
- ✅ Fair usage for all residents
- ✅ Manageable workload for admin/staff
- ✅ System stability and availability
- ✅ Better user experience overall

## 📊 Limits Summary Table

| Feature                | Rate Limit (Hourly) | Pending Limit | Status Check                    |
|------------------------|---------------------|---------------|---------------------------------|
| Clearance Requests     | 5 per hour         | 5 maximum     | pending, purok_approved         |
| Incident Reports       | 10 per hour        | 10 maximum    | pending, in_progress            |
| Feedback Submissions   | 10 per hour        | N/A           | N/A                             |

## 🔧 Configuration

### Modifying Rate Limits
To change rate limits, edit `routes/web.php`:

```php
// Example: Change requests to 3 per hour
->middleware('throttle:3,60')

// Example: Change to 10 per day (1440 minutes)
->middleware('throttle:10,1440')
```

### Modifying Pending Limits
To change pending limits, edit the controllers:

**RequestController.php:**
```php
if ($pendingCount >= 5) { // Change 5 to desired limit
```

**IncidentReportController.php:**
```php
if ($pendingCount >= 10) { // Change 10 to desired limit
```

## ⚠️ Error Messages

### Rate Limit Exceeded (429)
**Message:** "Too many attempts. Please slow down the request."
**Action:** User must wait before retrying

### Pending Limit Exceeded (Requests)
**Message:** "You have reached the maximum limit of 5 pending requests. Please wait for your existing requests to be processed before submitting a new one."
**Action:** User must wait for requests to be processed

### Pending Limit Exceeded (Reports)
**Message:** "You have reached the maximum limit of 10 pending incident reports. Please wait for your existing reports to be processed before submitting a new one."
**Action:** User must wait for reports to be processed

## 🧪 Testing

### Test Rate Limiting
1. Try submitting 6 requests within an hour
2. 6th request should be blocked with 429 error
3. Wait 60 minutes and try again - should work

### Test Pending Limits
1. Create 5 pending clearance requests
2. Try to create 6th request while others are pending
3. Should show error message
4. Admin approves/rejects one request
5. User can now create a new request

## 📝 Future Enhancements (Not Yet Implemented)

### Priority 3: Database Constraints
- Add max file size enforcement (2MB per upload)
- Add max photos per incident (5 photos)
- Enforce max purpose length (255 chars)

### Priority 4: Cleanup System
- Auto-delete files after 90 days for completed/rejected requests
- Archive old data yearly
- Monitor storage usage
- Scheduled cleanup jobs

### Priority 5: Email Notifications
- Configure SMTP settings
- Send request approved notifications
- Send request rejected notifications
- Send status change updates
- Weekly summary emails

### Additional Security
- Add honeypot fields for bot detection
- Implement IP-based rate limiting
- Enhanced file type validation on server side
- XSS prevention in user inputs

## 🎓 Developer Notes

### Laravel Throttle Middleware
- Uses cache to store request counts
- Automatically resets after specified time
- Key format: `throttle:{ip}:{route}`
- Can be customized per route

### Best Practices Followed
- ✅ Clear user communication about limits
- ✅ Helpful error messages
- ✅ Different limits for different features
- ✅ AJAX-friendly error responses
- ✅ No breaking changes to existing functionality

## 📞 Support

If you encounter issues with rate limiting:
1. Check if limit is appropriate for use case
2. Review logs for throttle events
3. Adjust limits in routes/web.php if needed
4. Consider IP-based exceptions for trusted users

---

**Implementation Date:** January 12, 2025
**Version:** 1.0
**Status:** ✅ Implemented and Active
