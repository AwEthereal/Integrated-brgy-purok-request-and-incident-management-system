# Reports Page Enhancements

## ✅ Features Added

### 1. Purok Dropdown Filter
- Added to both Residents and Purok Leaders reports
- Filter by specific Purok or view all
- Dropdown populated from database

### 2. Enhanced Search Feature
- Server-side search functionality
- Searches across: First Name, Last Name, Middle Name, Email, Contact Number
- Combined with Purok filter for precise results
- Clear filters option

### 3. Clickable Rows
- Click any row to view full profile
- Hover effect shows interactivity
- Checkbox doesn't trigger row click

### 4. Profile View Pages
- Detailed profile pages for residents and purok leaders
- Organized sections with all information
- Back navigation to reports list

---

## 📁 Files Modified

### 1. Controller: `app/Http/Controllers/ReportController.php`
**Changes:**
- Added `Purok` model import
- Updated `residents()` method with filtering and search
- Updated `purokLeaders()` method with filtering and search
- Added `showResident()` method for resident profile view
- Added `showLeader()` method for purok leader profile view

**New Methods:**
```php
public function residents(Request $request)
{
    $query = User::where('role', 'resident')->with('purok');
    
    // Filter by purok
    if ($request->filled('purok_id')) {
        $query->where('purok_id', $request->purok_id);
    }
    
    // Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('middle_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('contact_number', 'like', "%{$search}%");
        });
    }
    
    $residents = $query->orderBy('last_name')->get();
    $puroks = Purok::orderBy('name')->get();
    
    return view('reports.preview.residents', compact('residents', 'puroks'));
}

public function showResident(User $user)
{
    $user->load('purok');
    return view('reports.show.resident', compact('user'));
}
```

---

### 2. Routes: `routes/web.php`
**Changes:**
- Added routes for viewing resident and leader profiles

**New Routes:**
```php
// View resident profile from reports
Route::get('/residents/{user}', [\App\Http\Controllers\ReportController::class, 'showResident'])
    ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
    ->name('reports.residents.show');

// View purok leader profile from reports
Route::get('/purok-leaders/{user}', [\App\Http\Controllers\ReportController::class, 'showLeader'])
    ->middleware('checkrole:barangay_captain,barangay_kagawad,secretary,admin')
    ->name('reports.purok-leaders.show');
```

---

### 3. View: `resources/views/reports/preview/residents.blade.php`
**Changes:**
- Replaced client-side search with server-side filtering
- Added Purok dropdown filter
- Added search input field
- Made table rows clickable
- Added "Clear Filters" button
- Updated info message

**Key Features:**
```blade
<!-- Filter Form -->
<form method="GET" action="{{ route('reports.residents') }}">
    <select name="purok_id">...</select>
    <input type="text" name="search">
    <button type="submit">Apply Filters</button>
</form>

<!-- Clickable Row -->
<tr onclick="window.location='{{ route('reports.residents.show', $resident->id) }}'">
    <td onclick="event.stopPropagation()">
        <input type="checkbox" name="resident_ids[]">
    </td>
    <!-- resident data -->
</tr>
```

---

### 4. View: `resources/views/reports/preview/purok-leaders.blade.php`
**Changes:**
- Same enhancements as residents report
- Added Purok dropdown filter
- Added search functionality
- Made rows clickable
- Added clear filters option

---

### 5. View: `resources/views/reports/show/resident.blade.php` (NEW)
**New File Created**

**Sections:**
1. **Header** - Avatar, name, status badge
2. **Personal Information** - Name, birth date, gender, civil status
3. **Contact Information** - Email, phone, purok, address
4. **Account Information** - User ID, status, verification, registration date

---

### 6. View: `resources/views/reports/show/leader.blade.php` (NEW)
**New File Created**

**Sections:**
1. **Header** - Avatar, name, purok, status badge
2. **Personal Information** - Name, birth date, gender
3. **Contact Information** - Email, phone, purok, address
4. **Leadership Information** - Position, status, verification, appointed date

---

## 🎨 UI/UX Improvements

### Before:
- Client-side search only
- No purok filtering
- No profile view
- Basic table layout

### After:
- Server-side search with purok filter
- Dropdown to filter by purok
- Clickable rows for quick access
- Detailed profile pages
- Clear filters option
- Better user experience

---

## 🔍 Search & Filter Features

### Purok Filter:
- Dropdown with all puroks
- "All Puroks" option
- Filters results instantly

### Search Feature:
- Searches across multiple fields:
  - First Name
  - Last Name
  - Middle Name
  - Email
  - Contact Number
- Can be combined with Purok filter
- Server-side for better performance

### Combined Filtering:
```
Example: Filter by "Purok 1" + Search "Juan"
Result: Shows only residents/leaders in Purok 1 with "Juan" in their name
```

---

## 📊 Features Summary

| Feature | Residents Report | Purok Leaders Report |
|---------|-----------------|---------------------|
| Purok Filter | ✅ | ✅ |
| Search | ✅ | ✅ |
| Clickable Rows | ✅ | ✅ |
| Profile View | ✅ | ✅ |
| Clear Filters | ✅ | ✅ |
| Print Selected | ✅ | ✅ |
| Print All | ✅ | ✅ |

---

## 🚀 How to Use

### For Barangay Officials:

1. **Access Reports:**
   - Navigate to Reports section
   - Click "Residents" or "Purok Leaders"

2. **Filter by Purok:**
   - Select a Purok from dropdown
   - Click "Apply Filters"
   - View only users from that Purok

3. **Search:**
   - Enter name, email, or contact
   - Click "Apply Filters"
   - View matching results

4. **Combined Filter:**
   - Select Purok + Enter search term
   - Click "Apply Filters"
   - View precise results

5. **View Profile:**
   - Click anywhere on a user's row
   - See detailed profile information
   - Click "Back to List" to return

6. **Clear Filters:**
   - Click "Clear" button
   - Returns to showing all users

---

## ✨ Benefits

1. **Faster Navigation** - Click rows to view profiles
2. **Better Filtering** - Find specific users by Purok
3. **Enhanced Search** - Server-side search across multiple fields
4. **More Information** - Detailed profile pages
5. **Better Performance** - Server-side filtering reduces load
6. **Improved UX** - Intuitive interactions

---

## 🔒 Security

- All routes protected by authentication and role middleware
- Only barangay officials can access reports
- Uses Laravel's route model binding
- CSRF protection on all forms

---

## 📝 Technical Details

### Server-Side Filtering:
```php
// Efficient database queries
$query = User::where('role', 'resident')->with('purok');

if ($request->filled('purok_id')) {
    $query->where('purok_id', $request->purok_id);
}

if ($request->filled('search')) {
    $query->where(function($q) use ($search) {
        $q->where('first_name', 'like', "%{$search}%")
          ->orWhere('last_name', 'like', "%{$search}%")
          // ... more fields
    });
}
```

### Clickable Rows:
```javascript
// Row click navigates to profile
onclick="window.location='{{ route('reports.residents.show', $resident->id) }}'"

// Checkbox prevents row click
onclick="event.stopPropagation()"
```

---

## 🎯 URLs

### Residents Report:
- List: `http://127.0.0.1:8000/reports/residents`
- With Filter: `http://127.0.0.1:8000/reports/residents?purok_id=1`
- With Search: `http://127.0.0.1:8000/reports/residents?search=Juan`
- Profile: `http://127.0.0.1:8000/reports/residents/{id}`

### Purok Leaders Report:
- List: `http://127.0.0.1:8000/reports/purok-leaders`
- With Filter: `http://127.0.0.1:8000/reports/purok-leaders?purok_id=1`
- With Search: `http://127.0.0.1:8000/reports/purok-leaders?search=Maria`
- Profile: `http://127.0.0.1:8000/reports/purok-leaders/{id}`

---

**All changes are complete and ready to use!** 🎉
