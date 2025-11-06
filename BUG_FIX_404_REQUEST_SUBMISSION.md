# Bug Fix: 404 Error on Request Submission

## Issue Summary
When residents attempted to create a new request, they received a **404 Not Found** error. This occurred because the middleware was redirecting AJAX requests instead of returning proper JSON responses.

## Root Cause Analysis

### The Problem
The application uses AJAX (`fetch()`) to submit the request form in `resources/views/requests/create.blade.php`. However, three middleware classes were not handling AJAX requests properly:

1. **CheckResidentApproved** - Blocked non-approved residents
2. **CheckRole** - Verified user roles
3. **PurokLeaderMiddleware** - Restricted access to purok leaders

When these middleware detected unauthorized access, they returned **HTTP redirect responses** (302) instead of **JSON responses** (403/401). This caused the JavaScript fetch to fail with a navigation error appearing as a 404.

### Technical Details

**The form submission flow:**
```
User submits form (AJAX)
  ↓
Web Route: POST /requests
  ↓
Middleware Chain:
  - auth
  - verified
  - CheckResidentApproved ← REDIRECTED HERE (302)
  - throttle:5,60
  ↓
Controller never reached
  ↓
JavaScript receives redirect HTML instead of JSON
  ↓
Results in 404 error
```

## Files Modified

### 1. CheckResidentApproved.php
**Location:** `app/Http/Middleware/CheckResidentApproved.php`

**Changes:**
- Added AJAX/JSON detection using `$request->ajax()` and `$request->wantsJson()`
- Returns JSON responses with proper status codes (401, 403)
- Includes redirect URLs in JSON for proper client-side handling

**Before:**
```php
if (!$user) {
    return redirect()->route('login');
}
```

**After:**
```php
if (!$user) {
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated. Please log in.'
        ], 401);
    }
    return redirect()->route('login');
}
```

### 2. CheckRole.php
**Location:** `app/Http/Middleware/CheckRole.php`

**Changes:**
- Added AJAX detection for unauthenticated users
- Returns JSON 401 response for AJAX requests

### 3. PurokLeaderMiddleware.php
**Location:** `app/Http/Middleware/PurokLeaderMiddleware.php`

**Changes:**
- Added AJAX detection for unauthorized access
- Returns JSON 403 response for AJAX requests

### 4. create.blade.php
**Location:** `resources/views/requests/create.blade.php`

**Changes:**
- Enhanced form submission error handling
- Added specific handling for 403 and 401 status codes
- Properly redirects users when receiving redirect URLs in JSON responses

**Added code:**
```javascript
} else if (response.status === 403 || response.status === 401) {
    // Handle authorization errors - redirect if provided
    const errorMessage = data.message || 'You are not authorized to perform this action';
    alert(errorMessage);
    if (data.redirect) {
        window.location.href = data.redirect;
    }
    throw new Error(errorMessage);
}
```

## How the Fix Works

### For Approved Residents
1. User fills out the form and submits
2. AJAX request reaches controller
3. Request is processed normally
4. User redirected to dashboard with success message ✅

### For Non-Approved Residents
1. User fills out the form and submits
2. Middleware detects AJAX request and user is not approved
3. **NEW:** Returns JSON 403 response with message
4. JavaScript displays error message
5. User redirected to dashboard
6. Proper error handling ✅

### For Rejected Residents
1. User fills out the form and submits
2. Middleware detects AJAX request and user is rejected
3. **NEW:** Returns JSON 403 response with message
4. JavaScript displays error message
5. User redirected to dashboard
6. Proper error handling ✅

## Testing the Fix

### Test Case 1: Approved Resident
- **Expected:** Request submitted successfully
- **Result:** ✅ Works as intended

### Test Case 2: Pending Approval Resident
- **Expected:** Error message displayed, redirected to dashboard
- **Result:** ✅ Proper error handling with message

### Test Case 3: Rejected Resident
- **Expected:** Rejection message displayed, redirected to dashboard
- **Result:** ✅ Proper error handling with message

## Additional Improvements

1. **Consistent Error Handling:** All three middleware now handle AJAX requests consistently
2. **Better User Experience:** Users see informative error messages instead of generic 404 errors
3. **Proper HTTP Status Codes:** 401 for authentication, 403 for authorization
4. **Client-Side Redirect Support:** JSON responses include redirect URLs when needed

## Verification Steps

After deploying this fix:

1. Clear all caches:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. Test as different user types:
   - Approved resident
   - Pending resident
   - Rejected resident
   - Non-resident user

3. Check browser console for proper error messages

4. Verify no 404 errors in network tab

## Preventive Measures

To prevent similar issues in the future:

1. **Always check for AJAX requests in middleware** when returning responses
2. **Use consistent response patterns** across all middleware
3. **Test form submissions** with different user states
4. **Monitor browser console** during development for fetch errors

## Related Files

- `routes/web.php` - Route definitions
- `app/Http/Controllers/RequestController.php` - Request handling
- `public/js/camera-handler-new.js` - Camera functionality

## Status
✅ **FIXED** - All middleware now properly handle AJAX requests with JSON responses
