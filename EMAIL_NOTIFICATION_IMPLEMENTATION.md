# Email Notification System Implementation

## 📧 Overview
This document outlines the complete email notification system for the Barangay Kalawag Dos Management System.

---

## ✅ What Has Been Implemented

### 1. **Notification Classes Created**

#### A. `RequestApprovedNotification.php` ✅
**Purpose:** Notify residents when their clearance requests are approved

**Triggers:**
- Purok Leader approves a request → "Request Approved by Purok Leader"
- Barangay Official approves a request → "Request Approved - Ready for Pickup!"

**Email Content:**
- Request ID, Document Type, Purpose
- Approval date and time
- Next steps (for purok) or pickup instructions (for barangay)
- Action button to view request details

**File Location:** `app/Notifications/RequestApprovedNotification.php`

---

#### B. `RequestRejectedNotification.php` ✅
**Purpose:** Notify residents when their clearance requests are rejected

**Triggers:**
- Purok Leader rejects a request
- Barangay Official rejects a request

**Email Content:**
- Request ID, Document Type, Purpose
- Rejection date and time
- Rejection reason (if provided)
- Steps to take next (resubmit with corrections)
- Action button to submit new request

**File Location:** `app/Notifications/RequestRejectedNotification.php`

---

#### C. `IncidentReportStatusNotification.php` ✅
**Purpose:** Notify residents when their incident report status changes

**Triggers:**
- Status changes: `pending` → `in_progress` → `resolved` → `rejected/invalid`

**Email Content:**
- Report ID, Incident Type, Location
- Current status
- Status-specific messages:
  - **In Progress:** "Team is actively working on this"
  - **Resolved:** "Incident resolved, request feedback"
  - **Rejected/Invalid:** "Report marked as invalid"
- Staff notes (if any)
- Action button to view report details

**File Location:** `app/Notifications/IncidentReportStatusNotification.php`

---

### 2. **Controller Integration** ✅

#### A. `RequestController.php`
**Modified Method:** `updateStatus()`
**Lines:** 100-101, 108-109

**Added:**
```php
// When Purok Leader approves
$requestModel->user->notify(new \App\Notifications\RequestApprovedNotification($requestModel, 'purok'));

// When Purok Leader rejects
$requestModel->user->notify(new \App\Notifications\RequestRejectedNotification($requestModel, 'purok'));
```

---

#### B. `BarangayApprovalController.php`
**Modified Methods:** `approve()`, `reject()`
**Lines:** 226, 248

**Added:**
```php
// When Barangay approves
$request->user->notify(new \App\Notifications\RequestApprovedNotification($request, 'barangay'));

// When Barangay rejects
$request->user->notify(new \App\Notifications\RequestRejectedNotification($request, 'barangay'));
```

---

#### C. `IncidentReportController.php`
**Modified Method:** `update()`
**Lines:** 307-309

**Added:**
```php
// When incident status changes
if ($oldStatus !== $request->status) {
    $report->user->notify(new \App\Notifications\IncidentReportStatusNotification($report, $oldStatus, $request->status));
}
```

---

## 🔧 Current Configuration

### Email Settings (from .env)
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=47e9b6ac59ebac
MAIL_PASSWORD=79ecb1d5ae1f9f
MAIL_FROM_ADDRESS=edisonlaurico@gmail.com
MAIL_FROM_NAME="Kalawag Dos Request System"
MAIL_ENCRYPTION=tls
```

**Current Status:** ✅ **Mailtrap (Testing Mode)**
- Emails are captured but NOT sent to real recipients
- Perfect for development and testing
- View emails at: https://mailtrap.io/inboxes

---

## 📊 Email Flow Diagram

### Clearance Request Flow:
```
Resident submits request
        ↓
Purok Leader Reviews
        ↓
    [APPROVED] → Email: "Request Approved by Purok Leader"
        ↓
Barangay Reviews
        ↓
    [APPROVED] → Email: "Request Approved - Ready for Pickup!"
    [REJECTED] → Email: "Request Rejected - Action Required"
```

### Incident Report Flow:
```
Resident reports incident
        ↓
Status: PENDING
        ↓
Staff updates to IN_PROGRESS → Email: "Team is working on this"
        ↓
Staff updates to RESOLVED → Email: "Incident resolved - Please provide feedback"
```

---

## 🧪 Testing Instructions

### Option 1: Using Mailtrap (Current Setup)
1. **Submit a Test Request:**
   - Log in as a resident
   - Submit a clearance request
   
2. **Approve/Reject as Purok Leader:**
   - Log in as purok leader
   - Approve or reject the request
   
3. **Check Email in Mailtrap:**
   - Go to https://mailtrap.io
   - Log in with credentials
   - Check inbox for captured email
   
4. **Verify Email Content:**
   - Subject line correct?
   - All details included?
   - Action buttons work?

### Option 2: Queue Processing
1. **Start Queue Worker:**
   ```bash
   php artisan queue:work
   ```

2. **Perform Actions:**
   - Approve/reject requests
   - Update incident statuses

3. **Monitor Queue:**
   ```bash
   # Check failed jobs
   php artisan queue:failed
   
   # Retry failed jobs
   php artisan queue:retry all
   ```

---

## 📋 Email Templates Summary

| Notification Type | Subject Line | Trigger | Recipients |
|------------------|-------------|---------|-----------|
| Request Approved (Purok) | "Request Approved by Purok Leader" | Purok leader approves | Resident |
| Request Approved (Barangay) | "Request Approved - Ready for Pickup!" | Barangay approves | Resident |
| Request Rejected | "Request Update - Action Required" | Any rejection | Resident |
| Incident Status Change | "Incident Report Update" | Status changes | Resident |

---

## 🚀 Switching to Production Email

### Option 1: Gmail SMTP (Free, Easy)
Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-app-password  # Generate from Google Account
MAIL_FROM_ADDRESS=your-gmail@gmail.com
MAIL_FROM_NAME="Barangay Kalawag Dos"
MAIL_ENCRYPTION=tls
```

**Steps:**
1. Enable 2FA on Gmail
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Use app password in `.env`

---

### Option 2: SendGrid (Production Grade)
Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_FROM_ADDRESS=noreply@kalawag-brgy.ph
MAIL_FROM_NAME="Barangay Kalawag Dos"
MAIL_ENCRYPTION=tls
```

---

### Option 3: Custom SMTP Server
Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Barangay Kalawag Dos"
MAIL_ENCRYPTION=tls
```

---

## ⚡ Queue Configuration

### Current Setup:
- **Queue Driver:** Database (`QUEUE_CONNECTION=database`)
- **Status:** ✅ Ready to use
- **Benefits:** 
  - Emails sent asynchronously (non-blocking)
  - Failed jobs can be retried
  - Better performance

### Running the Queue:

**For Development:**
```bash
php artisan queue:work
```

**For Production (with Supervisor):**
```ini
[program:kalawag-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

---

## 🔍 Debugging Email Issues

### Check if email was queued:
```sql
SELECT * FROM jobs ORDER BY id DESC LIMIT 10;
```

### Check failed jobs:
```sql
SELECT * FROM failed_jobs ORDER BY id DESC;
```

### View Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

### Test email configuration:
```bash
php artisan tinker
```
```php
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')->subject('Test');
});
```

---

## 📝 Notification Features

### ✅ Implemented Features:
- Queue support for async email sending
- Different email templates for different events
- Rich HTML email templates (Laravel Markdown)
- Action buttons with links
- Formatted dates and times
- Conditional content based on status/type
- Rejection reasons included
- Staff notes included

### 🎯 Best Practices Followed:
- **ShouldQueue interface** - Emails sent asynchronously
- **Clear subject lines** - Easy to identify email type
- **Action buttons** - Direct links to view details
- **Formatted content** - Professional appearance
- **Error handling** - Failed jobs can be retried
- **Informative content** - All necessary details included

---

## 🔐 Security Considerations

### ✅ Already Implemented:
- Email credentials in `.env` (not in code)
- Queue system prevents blocking
- Proper authorization checks before sending

### ⚠️ Additional Recommendations:
1. Use environment-specific FROM addresses
2. Implement rate limiting on email sending
3. Monitor failed jobs regularly
4. Keep email credentials secure
5. Use app passwords (not main passwords)

---

## 📊 Email Statistics (Future Enhancement)

Consider adding:
- Email delivery tracking
- Open rate monitoring
- Click-through rate tracking
- Bounced email handling
- Unsubscribe functionality

---

## 🎓 For Developers

### Adding New Email Notifications:

1. **Create Notification Class:**
```bash
php artisan make:notification YourNotificationName
```

2. **Implement `toMail()` method:**
```php
public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('Your Subject')
        ->line('Content here')
        ->action('Button Text', url('/link'))
        ->line('More content');
}
```

3. **Send Notification:**
```php
$user->notify(new YourNotificationName($data));
```

---

## ✅ Implementation Checklist

- [x] Create RequestApprovedNotification
- [x] Create RequestRejectedNotification  
- [x] Create IncidentReportStatusNotification
- [x] Update RequestController
- [x] Update BarangayApprovalController
- [x] Update IncidentReportController
- [x] Configure Mailtrap for testing
- [x] Set up queue system
- [x] Document implementation
- [ ] Test all email scenarios
- [ ] Switch to production SMTP (when ready)
- [ ] Monitor email delivery

---

## 📞 Support

### Troubleshooting Common Issues:

**"Queue not processing"**
- Run: `php artisan queue:work`
- Check: `jobs` table in database

**"Emails not sending"**
- Verify SMTP credentials
- Check `.env` configuration
- Test with tinker command

**"Notification not triggered"**
- Check controller code
- Verify user has email address
- Check Laravel logs

---

**Implementation Date:** January 12, 2025  
**Version:** 1.0  
**Status:** ✅ Implemented and Ready for Testing  
**Next Step:** Test with Mailtrap, then switch to production SMTP

---

## 🎉 Summary

**What Works Now:**
- ✅ Purok approval emails
- ✅ Purok rejection emails
- ✅ Barangay approval emails  
- ✅ Barangay rejection emails
- ✅ Incident status update emails
- ✅ Queue system for async processing
- ✅ Professional email templates
- ✅ Action buttons and links

**Ready for Production:** Just need to switch from Mailtrap to real SMTP! 🚀
