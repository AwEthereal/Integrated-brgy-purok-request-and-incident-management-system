# 🧪 Quick Testing Guide

## **Reset & Generate Test Data**

### **Quick Reset (Anytime):**
```bash
php artisan db:seed --class=TestDataSeeder
```

This will:
1. ✅ Clear all requests and incidents
2. ✅ Keep all your users intact
3. ✅ Create fresh test data
4. ✅ Generate data to test yellow dot system

---

## **What Test Data Gets Created:**

### **📄 Clearance Requests:**
- **Pending** → Yellow dot for Purok Leader
- **Purok Approved** → Yellow dot for Barangay Official
- **Rejected** → Yellow dot for Resident (action required)
- **Completed** → Yellow dot for Resident (pickup required)
- **Old Approval** → No dots (informational, >2h)
- **Barangay Approved** → Brief dot for Resident

### **🚨 Incident Reports:**
- **Pending** → Yellow dot for Barangay Officials
- **In Progress** → Brief dot for Resident
- **Resolved** → Brief dot for Resident
- **Old Resolved** → No dots

---

## **Testing Yellow Dots:**

### **1. Test as Purok Leader:**
```
Log in as purok leader
Expected: 🟡 Yellow dot on "Purok Dashboard" (pending request)
```

### **2. Test as Barangay Official:**
```
Log in as barangay official
Expected: 
  🟡 Dashboard dot (pending incident + purok approved request)
  🟡 Pending request card
  🟡 Active incident card
```

### **3. Test as Resident:**
```
Log in as resident
Expected:
  🟡 Dashboard dot (rejected/completed requests)
  🟡 My Requests link (rejected/completed only)
  🟡 Table rows (rejected/completed only)
  🟡 Recent Activity cards (brief for approvals, persistent for action items)
```

---

## **Quick Commands:**

### **Reset Everything:**
```bash
php artisan db:seed --class=TestDataSeeder
```

### **Check What Was Created:**
After running seeder, it shows a summary table automatically!

### **Manual Clear (if needed):**
```bash
php artisan tinker
\App\Models\Request::truncate();
\App\Models\IncidentReport::truncate();
exit
```

---

## **Development Workflow:**

1. **Test a feature** → Make changes
2. **Need fresh data?** → `php artisan db:seed --class=TestDataSeeder`
3. **Test again** → Repeat!

No need to recreate users every time! 🎉

---

## **Tip:**

Create an alias in your terminal for faster testing:

**PowerShell:**
```powershell
function Reset-TestData { php artisan db:seed --class=TestDataSeeder }
Set-Alias -Name reset -Value Reset-TestData
```

Then just run:
```bash
reset
```

---

**Happy Testing!** 🚀
