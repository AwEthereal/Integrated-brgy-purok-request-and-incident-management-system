# 🟡 Resident Yellow Dot System - Complete Implementation

**Date:** October 12, 2025  
**Status:** ✅ COMPLETE  
**User Role:** Residents

---

## 📋 Overview

Complete action-based yellow dot system for residents. Dots appear on navigation links, dashboard cards, and table rows when there are status updates that need the resident's attention.

---

## 🎯 Where Residents See Dots

### **1. Navigation → Dashboard Link** 🟡

**Shows When:**
- Request status changed in last 48 hours **AND**
- Status is: `purok_approved`, `barangay_approved`, `rejected`, or `completed`

**Example:**
```
Dashboard 🟡
```

**Disappears When:**
- 48 hours pass (resident has had time to see it)
- User is currently on dashboard page

---

### **2. Navigation → My Purok Clearance Requests** 🟡

**Shows When:**
- Any request has status: `purok_approved`, `barangay_approved`, `rejected`, or `completed`
- Status changed within last 48 hours

**Example:**
```
My Purok Clearance Requests 🟡
```

**Disappears When:**
- User visits the requests page
- 48 hours pass since last update

**Code Location:**
- File: `resources/views/layouts/navigation.blade.php`
- Lines: 59-71

---

### **3. Navigation → My Incident Reports** 🟡

**Shows When:**
- Any incident has status: `in_progress` or `resolved`
- Status changed within last 48 hours

**Example:**
```
My Incident Reports 🟡
```

**Disappears When:**
- User visits the incident reports page
- 48 hours pass since last update

**Code Location:**
- File: `resources/views/layouts/navigation.blade.php`
- Lines: 211-223

---

### **4. Dashboard → Recent Activity Cards** 🟡

**Shows When:**
- Item status is actionable (approved, rejected, completed, in_progress, resolved)
- Status changed within last 24 hours

**Visual:**
```
┌─────────────────────────────────┐
│ Request: Business Permit    🟡  │
│ Purok Approved • 2 hours ago    │
└─────────────────────────────────┘
```

**Code Location:**
- File: `resources/views/dashboard.blade.php`
- Lines: 464-479

---

### **5. My Requests Table → Desktop View** 🟡

**Shows When:**
- Request status: `purok_approved`, `barangay_approved`, `rejected`, or `completed`
- Status changed within last 48 hours

**Visual (Desktop):**
```
┌──────────────────────────────────────────────┐
│ ID          │ Purpose        │ Status       │
├──────────────────────────────────────────────┤
│ 🟡 REQ-001  │ Business Cert  │ Approved     │
│    REQ-002  │ Clearance      │ Pending      │
└──────────────────────────────────────────────┘
```

**Code Location:**
- File: `resources/views/requests/my-requests.blade.php`
- Lines: 141-159 (Desktop table)
- Lines: 199-214 (Mobile cards)

---

### **6. My Incident Reports Table → Desktop View** 🟡

**Shows When:**
- Incident status: `in_progress` or `resolved`
- Status changed within last 48 hours

**Visual (Desktop):**
```
┌──────────────────────────────────────────────┐
│ Date        │ Type           │ Status       │
├──────────────────────────────────────────────┤
│ 🟡 Oct 12   │ Noise Issue    │ In Progress  │
│    Oct 11   │ Streetlight    │ Pending      │
└──────────────────────────────────────────────┘
```

**Code Location:**
- File: `resources/views/incident_reports/my-reports.blade.php`
- Lines: 147-165 (Desktop table)
- Lines: 204-219 (Mobile cards)

---

### **7. Mobile Card Views** 🟡

**Both requests and incidents have dots in mobile view:**

```
┌─────────────────────────────────┐
│ REQ-001                     🟡  │
│ Business Certificate            │
│ Purok Approved • Oct 12         │
└─────────────────────────────────┘
```

**Positioned:** Top-right corner of card

---

## 📊 Resident Dot Logic Summary

| Location | Trigger Statuses | Time Window | Position |
|----------|-----------------|-------------|----------|
| **Dashboard Link** | approved, rejected, completed | 48 hours | Navigation |
| **My Requests Link** | approved, rejected, completed | 48 hours | Navigation |
| **My Incidents Link** | in_progress, resolved | 48 hours | Navigation |
| **Recent Activity Cards** | approved, rejected, completed, in_progress, resolved | 24 hours | Top-right |
| **Requests Table (Desktop)** | approved, rejected, completed | 48 hours | Next to ID |
| **Requests Cards (Mobile)** | approved, rejected, completed | 48 hours | Top-right |
| **Incidents Table (Desktop)** | in_progress, resolved | 48 hours | Next to Date |
| **Incidents Cards (Mobile)** | in_progress, resolved | 48 hours | Top-right |

---

## 🔄 User Experience Flows

### **Flow 1: Request Approval Journey**

**Step 1: Resident Submits**
- Status: `pending`
- **No dots** (waiting for action)

**Step 2: Purok Approves**
- Status: `purok_approved`
- **Dots appear:**
  - 🟡 Navigation → Dashboard
  - 🟡 Navigation → My Purok Clearance Requests
  - 🟡 Dashboard → Recent Activity card
  - 🟡 My Requests table row
- **Message:** "Your request has been approved by your Purok Leader!"

**Step 3: Resident Views Dashboard**
- Navigation Dashboard dot disappears
- Other dots remain (until viewed)

**Step 4: Resident Views Requests Page**
- My Requests nav dot disappears
- Table row still shows dot (item still actionable)

**Step 5: Barangay Approves**
- Status: `barangay_approved`
- **Dots refresh:**
  - 🟡 All dots appear again
- **Message:** "Your request is ready for pickup!"

**Step 6: After 48 Hours**
- All dots disappear
- Items remain visible, just no dots

---

### **Flow 2: Request Rejection**

**Purok Rejects Request:**
- Status: `rejected`
- **Dots appear:**
  - 🟡 All navigation and table dots
- **Message:** "Your request was rejected. Review the reason and resubmit."

**Resident Takes Action:**
- Views rejection reason
- Can resubmit new request
- Dots remain for 48 hours

---

### **Flow 3: Incident Progress Updates**

**Barangay Marks In Progress:**
- Status: `in_progress`
- **Dots appear:**
  - 🟡 Navigation → Dashboard
  - 🟡 Navigation → My Incident Reports
  - 🟡 Recent Activity card
  - 🟡 My Incidents table row
- **Message:** "Your incident report is being addressed!"

**Barangay Resolves:**
- Status: `resolved`
- **Dots refresh:**
  - 🟡 All dots appear again
- **Message:** "Your incident has been resolved!"

---

## 💡 Why 48 Hours for Residents?

### **Reasoning:**

1. **Longer Window** - Residents don't check daily like officials
2. **Multiple Chances** - Weekend, weekdays, different times
3. **Important Updates** - Approvals, rejections need attention
4. **Action Items** - Completed = pickup, Rejected = resubmit

### **Dashboard Recent Activity = 24 Hours**

- Quick glance view
- Shows very recent changes
- Refreshes daily

---

## 🎨 Visual Design

### **Navigation Dots:**
- **Size:** 2.5 x 2.5 (10px)
- **Position:** Right of link text
- **Animation:** Pulsing ring

### **Table Row Dots (Desktop):**
- **Size:** 2.5 x 2.5 (10px)
- **Position:** Before ID/Date column
- **Animation:** Pulsing ring

### **Card Dots (Mobile):**
- **Size:** 3 x 3 (12px)
- **Position:** Top-right corner
- **Animation:** Pulsing ring
- **Z-index:** 10 (above content)

---

## 🧪 Testing Scenarios

### **Test 1: Navigation Dots**

**Setup:**
1. Log in as resident
2. Have purok leader approve a request

**Expected:**
- ✅ Dashboard link shows 🟡 dot
- ✅ My Requests link shows 🟡 dot
- ✅ Dots pulsing/animated

**Actions:**
- Click Dashboard
- ✅ Dashboard dot disappears
- ✅ My Requests dot remains

**Actions:**
- Click My Requests
- ✅ My Requests dot disappears

---

### **Test 2: Table Row Dots**

**Setup:**
1. Navigate to "My Purok Clearance Requests"
2. Have at least one approved request (within 48h)

**Expected:**
- ✅ Row with approved request shows 🟡 dot next to ID
- ✅ Pending requests have no dots
- ✅ Old approved requests (>48h) have no dots

---

### **Test 3: Multiple Updates**

**Setup:**
1. Have 3 requests at different statuses:
   - Request A: `pending` (no dot)
   - Request B: `purok_approved` (updated 1 hour ago) - 🟡 dot
   - Request C: `completed` (updated 50 hours ago) - no dot

**Expected:**
- ✅ Only Request B shows dot
- ✅ Request A: no dot (still pending)
- ✅ Request C: no dot (too old)

---

### **Test 4: Mobile View**

**Setup:**
1. Open on mobile device
2. Navigate to My Requests

**Expected:**
- ✅ Cards display instead of table
- ✅ Dots appear on top-right of cards
- ✅ Fully visible, not cut off

---

### **Test 5: Time-Based Removal**

**Setup:**
1. Request approved exactly 48 hours ago
2. Wait 1 minute

**Expected:**
- ✅ Dot disappears after 48 hours
- ✅ Request still visible
- ✅ Status badge still shows

---

## ⚙️ Configuration

### **Change Time Windows:**

**Navigation Dots (Current: 48 hours):**
```php
// In navigation.blade.php
->where('updated_at', '>=', now()->subHours(48))

// Change to 72 hours:
->where('updated_at', '>=', now()->subHours(72))

// Change to 7 days:
->where('updated_at', '>=', now()->subDays(7))
```

**Recent Activity (Current: 24 hours):**
```php
// In dashboard.blade.php
$isRecent = $activity->updated_at >= now()->subHours(24);

// Change to 48 hours:
$isRecent = $activity->updated_at >= now()->subHours(48);
```

---

### **Add More Actionable Statuses:**

**For Requests:**
```php
// Current statuses
$statusNeedsAttention = in_array($request->status, [
    'purok_approved', 
    'barangay_approved', 
    'rejected', 
    'completed'
]);

// Add more:
$statusNeedsAttention = in_array($request->status, [
    'purok_approved', 
    'barangay_approved', 
    'rejected', 
    'completed',
    'your_new_status' // Add here
]);
```

---

## 📁 Files Modified

### **1. Navigation**
- **File:** `resources/views/layouts/navigation.blade.php`
- **Changes:**
  - Lines 59-71: My Purok Clearance Requests dot
  - Lines 211-223: My Incident Reports dot

### **2. Dashboard**
- **File:** `resources/views/dashboard.blade.php`
- **Changes:**
  - Lines 464-479: Recent Activity cards dots

### **3. My Requests**
- **File:** `resources/views/requests/my-requests.blade.php`
- **Changes:**
  - Lines 141-159: Desktop table dots
  - Lines 199-214: Mobile card dots

### **4. My Incidents**
- **File:** `resources/views/incident_reports/my-reports.blade.php`
- **Changes:**
  - Lines 147-165: Desktop table dots
  - Lines 204-219: Mobile card dots

---

## ✅ Complete Resident Features

### **Navigation Indicators:**
- ✅ Dashboard link dot
- ✅ My Requests link dot
- ✅ My Incidents link dot

### **Dashboard Indicators:**
- ✅ Recent Activity card dots

### **Table Indicators:**
- ✅ My Requests table row dots
- ✅ My Incidents table row dots

### **Mobile Indicators:**
- ✅ Request card dots
- ✅ Incident card dots

### **Smart Logic:**
- ✅ Action-based (not just time)
- ✅ Status-aware
- ✅ Auto-cleanup after 48h
- ✅ Page-aware (disappear when viewing)

---

## 🎯 Benefits for Residents

1. **Never Miss Updates** - Dots show for 48 hours
2. **Clear Priorities** - Only actionable items have dots
3. **Easy Scanning** - Quick visual cues
4. **Mobile Friendly** - Works on all devices
5. **Automatic** - No manual marking as read
6. **Intuitive** - Dots disappear when viewed
7. **Persistent** - Stay until timeout or viewed

---

## ✅ Status: COMPLETE

All resident-side yellow dots are fully implemented!

**Summary:**
- 🟡 3 navigation link dots
- 🟡 Dashboard activity card dots
- 🟡 2 table types with dots (requests + incidents)
- 🟡 Mobile card dots
- 🟡 Action-based intelligent logic
- 🟡 48-hour attention window
- 🟡 Auto-cleanup

**Resident experience is now complete with comprehensive visual indicators!** 🎉

---

**Implementation Date:** October 12, 2025  
**Time Windows:** 24h (Recent Activity), 48h (Everything else)  
**Trigger:** Status-based + Time-based
