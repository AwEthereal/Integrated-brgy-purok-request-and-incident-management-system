# Browser Tab Titles Update

## ✅ Changes Applied

### Main Layout Updated
**File:** `resources/views/layouts/app.blade.php`

**Before:**
```blade
<title>{{ config('app.name', 'BP Transaction & Report System') }}</title>
```

**After:**
```blade
<title>@yield('title', 'Dashboard') - {{ config('app.name', 'Barangay Kalawag II') }}</title>
```

Now all pages will show: `[Page Name] - Barangay Kalawag II`

---

## 📋 Pages Updated (31 Total)

### Dashboard Pages
- ✅ `dashboard.blade.php` - "Dashboard" (already had title)
- ✅ `admin/dashboard.blade.php` - "Admin Dashboard"
- ✅ `purok_leader/dashboard.blade.php` - "Purok Leader Dashboard"
- ✅ `barangay_official/dashboard.blade.php` - "Barangay Official Dashboard"
- ✅ `purok/dashboard.blade.php` - "Purok Dashboard"

### Clearance Request Pages
- ✅ `requests/index.blade.php` - "My Requests"
- ✅ `requests/create.blade.php` - "New Clearance Request"
- ✅ `requests/edit.blade.php` - "Edit Request"
- ✅ `requests/show.blade.php` - "Request Details"
- ✅ `requests/my-requests.blade.php` - "My Clearance Requests"
- ✅ `requests/pending-purok.blade.php` - "Pending Purok Requests"
- ✅ `requests/pending-barangay.blade.php` - "Pending Barangay Requests"
- ✅ `barangay_official/approvals.blade.php` - "Clearance Approvals"
- ✅ `barangay_official/show.blade.php` - "Request Details"

### Profile Pages
- ✅ `profile/edit.blade.php` - "Profile Settings"
- ✅ `profile/update-password.blade.php` - "Update Password"

### Incident Report Pages
- ✅ `incident_reports/my-reports.blade.php` - "My Incident Reports"
- ✅ `resident/incidents/my_reports.blade.php` - "My Incident Reports"
- ✅ `resident/incidents/show.blade.php` - "Incident Report Details"
- ✅ `admin/incidents/pending.blade.php` - "Pending Incident Reports"
- ✅ `admin/incidents/show.blade.php` - "Incident Report Details"

### Resident Management Pages
- ✅ `purok_leader/residents.blade.php` - "Manage Residents"
- ✅ `purok_leader/resident_show.blade.php` - "Resident Details"
- ✅ `purok_leader/residents/reject.blade.php` - "Reject Resident"
- ✅ `purok_leader/purok_change_requests.blade.php` - "Purok Change Requests"

### Admin Pages
- ✅ `admin/users/edit.blade.php` - "Edit User"

### Report Preview Pages
- ✅ `reports/preview/residents.blade.php` - "Residents Report Preview"
- ✅ `reports/preview/purok-leaders.blade.php` - "Purok Leaders Report Preview"
- ✅ `reports/preview/purok-clearance.blade.php` - "Clearance Requests Report Preview"
- ✅ `reports/preview/incident-reports.blade.php` - "Incident Reports Preview"

### Other Pages
- ✅ `feedback/form.blade.php` - "Submit Feedback"

---

## 📊 Tab Title Format

All pages now follow this format:
```
[Specific Page Title] - Barangay Kalawag II
```

### Examples:
- Dashboard page: `Dashboard - Barangay Kalawag II`
- New request: `New Clearance Request - Barangay Kalawag II`
- Profile: `Profile Settings - Barangay Kalawag II`
- Admin dashboard: `Admin Dashboard - Barangay Kalawag II`

---

## 🎯 How It Works

### In Layout File:
```blade
<title>@yield('title', 'Dashboard') - {{ config('app.name', 'Barangay Kalawag II') }}</title>
```

### In Each Page:
```blade
@extends('layouts.app')

@section('title', 'Page Name Here')

@section('content')
    <!-- Page content -->
@endsection
```

---

## ✨ Benefits

1. ✅ **Better SEO** - Descriptive page titles
2. ✅ **Improved UX** - Users know which page they're on
3. ✅ **Browser History** - Easier to find pages in history
4. ✅ **Multiple Tabs** - Easy to identify tabs
5. ✅ **Professional** - Consistent branding

---

## 🔍 Pages That Don't Use App Layout

These pages have their own titles (not changed):
- `welcome.blade.php` - "Barangay Kalawag II – AKSYON AGAD!"
- `incidents/create.blade.php` - "Report an Incident"
- `layouts/guest.blade.php` - Uses `@yield('title')` already
- PDF/Print reports - Have specific titles with dates

---

## 📝 Adding Titles to New Pages

When creating new pages, add this after `@extends('layouts.app')`:

```blade
@extends('layouts.app')

@section('title', 'Your Page Title')

@section('content')
    <!-- Your content here -->
@endsection
```

---

## ✅ Summary

**Total Pages Updated:** 31 pages
**Layout Files Modified:** 1 file
**Format:** `[Page Title] - Barangay Kalawag II`

All browser tabs now show proper, descriptive titles instead of just "Laravel"! 🎉
