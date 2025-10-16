# Email Notification Troubleshooting Guide

## ✅ Test Results

### SMTP Connection: ✓ WORKING
- Mailtrap connection successful
- Emails can be sent

### Queue System: ✓ WORKING  
- Jobs are being added to queue
- Notifications are being queued

---

## 🔍 Why You're Not Seeing Emails

### Most Likely Cause: **Queue Worker Not Running**

When you approve a request:
1. ✅ Notification is added to queue (database)
2. ❌ Queue worker is NOT running to process it
3. ❌ Email never gets sent

---

## ✅ SOLUTION: Start the Queue Worker

### **Option 1: Run Queue Worker Manually (For Testing)**

Open a **NEW terminal/command prompt** and run:

```bash
cd "C:\Users\PC\Documents\Capstone\Capstone System\kalawag_brgy_system"
php artisan queue:work
```

**Keep this terminal open!** It needs to run continuously.

You should see output like:
```
[2025-10-12 11:05:00][1] Processing: Illuminate\Notifications\SendQueuedNotifications
[2025-10-12 11:05:02][1] Processed:  Illuminate\Notifications\SendQueuedNotifications
```

---

### **Option 2: Process Queue Once (Quick Test)**

If you just want to test:

```bash
php artisan queue:work --once
```

This processes ONE job and stops.

---

### **Option 3: Run in Background (Windows)**

```powershell
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd 'C:\Users\PC\Documents\Capstone\Capstone System\kalawag_brgy_system'; php artisan queue:work" -WindowStyle Minimized
```

This opens a minimized window that runs the queue worker.

---

## 📋 Step-by-Step Testing Process

### **Step 1: Clear Any Stuck Jobs**
```bash
php artisan queue:clear
```

### **Step 2: Start Queue Worker**
```bash
php artisan queue:work
```
*Keep this terminal open!*

### **Step 3: Test the Flow**

1. **Open your browser**
2. **Log in as resident**
3. **Create a new clearance request**
4. **Log out and log in as purok president**
5. **Approve the request**
6. **Watch the queue worker terminal** - you should see:
   ```
   [timestamp] Processing: Illuminate\Notifications\SendQueuedNotifications
   [timestamp] Processed:  Illuminate\Notifications\SendQueuedNotifications
   ```

### **Step 4: Check Mailtrap**

1. Go to: https://mailtrap.io/inboxes
2. Log in with your credentials
3. Check the inbox
4. You should see the email!

---

## 🐛 Common Issues & Solutions

### **Issue 1: "No output in queue worker"**

**Cause:** No jobs in queue

**Solution:** 
```bash
# Check if jobs exist
php artisan tinker
>>> DB::table('jobs')->count()
```

If 0, approve another request.

---

### **Issue 2: "Queue worker stops after one job"**

**Cause:** Using `--once` flag

**Solution:** Use `queue:work` without `--once`:
```bash
php artisan queue:work
```

---

### **Issue 3: "Email sent but not in Mailtrap"**

**Cause:** Wrong Mailtrap inbox or credentials

**Solution:** Verify `.env` settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=47e9b6ac59ebac
MAIL_PASSWORD=79ecb1d5ae1f9f
```

Check your Mailtrap account for the correct inbox.

---

### **Issue 4: "Jobs failing silently"**

**Cause:** Errors in notification class

**Solution:** Check failed jobs:
```bash
php artisan queue:failed
```

Retry failed jobs:
```bash
php artisan queue:retry all
```

---

## 🧪 Quick Test Commands

### **Test 1: Send Test Email**
```bash
php test-smtp.php
```
Should show: "✓ Email sent successfully!"

### **Test 2: Queue a Notification**
```bash
php test-email.php
```
Should show: "✓ Job added to queue!"

### **Test 3: Process the Queue**
```bash
php artisan queue:work --once
```
Should process the job.

### **Test 4: Check Mailtrap**
Go to https://mailtrap.io/inboxes and check for emails.

---

## 📊 Monitoring Queue

### **Check Jobs in Queue:**
```bash
php artisan queue:monitor
```

### **View Queue Status:**
```bash
php artisan tinker
>>> DB::table('jobs')->count()
```

### **View Failed Jobs:**
```bash
php artisan queue:failed
```

---

## ✅ Production Setup (For Deployment)

### **Windows (Task Scheduler)**

Create a batch file `queue-worker.bat`:
```batch
@echo off
cd "C:\Users\PC\Documents\Capstone\Capstone System\kalawag_brgy_system"
php artisan queue:work --sleep=3 --tries=3
```

Schedule it to run at startup.

---

### **Linux (Supervisor)**

Create `/etc/supervisor/conf.d/kalawag-worker.conf`:
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

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start kalawag-worker:*
```

---

## 🎯 Summary

### **The Problem:**
- Emails are being queued ✅
- SMTP connection works ✅
- **Queue worker is NOT running** ❌

### **The Solution:**
```bash
# Open terminal and run:
php artisan queue:work

# Keep it running!
```

### **Expected Result:**
1. Approve a request
2. Queue worker processes it immediately
3. Email appears in Mailtrap within seconds
4. Success! 🎉

---

## 📞 Still Not Working?

### **Check These:**

1. **Queue worker running?**
   ```bash
   # Should see continuous output
   php artisan queue:work
   ```

2. **User has email address?**
   ```bash
   php artisan tinker
   >>> App\Models\User::find(1)->email
   ```

3. **Notification class exists?**
   ```bash
   ls app/Notifications/RequestApprovedNotification.php
   ```

4. **Jobs table exists?**
   ```bash
   php artisan migrate:status
   ```

5. **Mailtrap credentials correct?**
   - Check `.env` file
   - Verify at https://mailtrap.io

---

## 🚀 Quick Start (Right Now!)

**Open a new terminal and run:**
```bash
cd "C:\Users\PC\Documents\Capstone\Capstone System\kalawag_brgy_system"
php artisan queue:work
```

**Then approve a request and watch the magic happen!** ✨

---

**Created:** January 12, 2025  
**Status:** Email system is working, just needs queue worker running!
