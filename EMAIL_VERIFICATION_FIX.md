# Email Verification AJAX Fix

## Issue
When residents with unverified email addresses tried to create a new request, they were redirected to the email verification page, causing the AJAX form submission to fail.

## Root Cause
Laravel's built-in `Illuminate\Auth\Middleware\EnsureEmailIsVerified` middleware (alias: `verified`) does not properly handle AJAX requests. It always returns a redirect response (HTTP 302), even for AJAX/fetch requests.

### The Problem Flow
```
User submits request form (AJAX)
  ↓
Route: POST /requests
  ↓
Middleware: auth, verified, CheckResidentApproved
  ↓
Laravel's EnsureEmailIsVerified checks email
  ↓
Email not verified → Returns redirect to verification.notice
  ↓
JavaScript receives HTML redirect instead of JSON
  ↓
Form submission fails
```

## Solution
Created a **custom** `EnsureEmailIsVerified` middleware that:
1. Detects AJAX/JSON requests using `$request->ajax()` and `$request->wantsJson()`
2. Returns proper JSON responses (403) for AJAX requests
3. Includes redirect URL in JSON payload
4. Falls back to normal redirects for regular page requests

## Files Created

### 1. Custom Email Verification Middleware
**File:** `app/Http/Middleware/EnsureEmailIsVerified.php`

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EnsureEmailIsVerified
{
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail())) {
            
            // Handle AJAX/JSON requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your email address is not verified. Please verify your email to continue.',
                    'redirect' => route('verification.notice')
                ], 403);
            }
            
            // Handle regular requests
            return Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'));
        }

        return $next($request);
    }
}
```

### Key Features:
- ✅ Detects AJAX requests
- ✅ Returns JSON with proper status code (403)
- ✅ Includes user-friendly error message
- ✅ Provides redirect URL for client-side handling
- ✅ Maintains backward compatibility for regular requests

## Files Modified

### 1. bootstrap/app.php
**Change:** Registered custom middleware alias

```php
$middleware->alias([
    // ... other aliases
    'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
]);
```

This overrides Laravel's default `verified` middleware with our custom one.

## How It Works Now

### Scenario 1: Verified Email ✅
1. User submits request form (AJAX)
2. Middleware checks email → Verified
3. Request proceeds to controller
4. Request created successfully
5. User redirected to dashboard

### Scenario 2: Unverified Email (AJAX) ✅
1. User submits request form (AJAX)
2. Custom middleware detects AJAX request
3. Returns JSON 403 with verification message
4. JavaScript displays error: "Your email address is not verified..."
5. User automatically redirected to verification page
6. User can verify email and try again

### Scenario 3: Unverified Email (Regular Page Load) ✅
1. User navigates to `/requests/create`
2. Middleware checks email → Not verified
3. User redirected to verification notice page
4. Standard Laravel behavior maintained

## Error Handling

The form's JavaScript already handles this properly:
```javascript
} else if (response.status === 403 || response.status === 401) {
    const errorMessage = data.message || 'You are not authorized to perform this action';
    alert(errorMessage);
    if (data.redirect) {
        window.location.href = data.redirect;
    }
    throw new Error(errorMessage);
}
```

## Testing Checklist

### As Verified User ✅
- Submit request → Should work normally
- No verification issues

### As Unverified User (AJAX) ✅
- Submit request via form → See verification message
- Get redirected to verification page
- Proper error handling

### As Unverified User (Page Navigation) ✅
- Visit `/requests/create` → Redirected to verification page
- Standard Laravel behavior

## Related Routes
All routes with the `verified` middleware now use the custom middleware:
- `GET /requests` - View requests
- `GET /requests/create` - Create request form
- `POST /requests` - Submit request ← This was failing
- `GET /requests/{id}/edit` - Edit request
- `PUT /requests/{id}` - Update request
- `DELETE /requests/{id}` - Delete request
- `GET /my-requests` - My requests page

## Benefits of This Solution

1. **Proper AJAX Support:** Returns JSON instead of HTML redirects
2. **User-Friendly:** Clear error messages
3. **Graceful Degradation:** Works for both AJAX and regular requests
4. **Consistent with Other Middleware:** Follows same pattern as CheckResidentApproved
5. **No Route Changes:** No need to modify existing routes
6. **Laravel Compatibility:** Works seamlessly with Laravel's email verification system

## Verification

After clearing caches:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

The middleware is now active and handling both regular and AJAX requests properly.

## Status
✅ **FIXED** - Email verification now works correctly for AJAX requests
