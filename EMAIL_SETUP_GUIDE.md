# 📧 Email Notification Setup Guide

## Overview
This guide will help you set up email notifications for purok clearance requests using **FREE** SMTP services.

---

## 🎯 Best FREE SMTP Options

### **Option 1: Gmail (Recommended for Production)** ⭐
- ✅ **100% FREE**
- ✅ 500 emails/day limit
- ✅ Most reliable
- ✅ Real email delivery
- ✅ Easy setup

### **Option 2: Mailtrap (Recommended for Testing)** 🧪
- ✅ **100% FREE**
- ✅ 500 emails/month
- ✅ Perfect for development
- ✅ Email testing inbox
- ✅ No real emails sent

### **Option 3: Brevo (Sendinblue)**
- ✅ **FREE tier available**
- ✅ 300 emails/day
- ✅ Good for production
- ✅ Professional features

---

## 🚀 Setup Instructions

### **OPTION 1: Gmail SMTP (Production)**

#### **Step 1: Enable 2-Step Verification**
1. Go to [Google Account Security](https://myaccount.google.com/security)
2. Click **2-Step Verification**
3. Follow the setup process
4. Enable 2-Step Verification

#### **Step 2: Generate App Password**
1. Go to [App Passwords](https://myaccount.google.com/apppasswords)
2. Select **Mail** as the app
3. Select **Windows Computer** as the device
4. Click **Generate**
5. **Copy the 16-character password** (you'll need this!)

#### **Step 3: Configure .env File**
Open your `.env` file and update these lines:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Barangay Kalawag II"
```

**Example:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=kalawag.barangay@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=kalawag.barangay@gmail.com
MAIL_FROM_NAME="Barangay Kalawag II"
```

---

### **OPTION 2: Mailtrap (Testing/Development)**

#### **Step 1: Create Mailtrap Account**
1. Go to [Mailtrap.io](https://mailtrap.io/)
2. Sign up for FREE account
3. Verify your email

#### **Step 2: Get SMTP Credentials**
1. Go to **Email Testing** → **Inboxes**
2. Click on your inbox
3. Go to **SMTP Settings**
4. Select **Laravel 9+** from dropdown
5. Copy the credentials

#### **Step 3: Configure .env File**
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@barangay-kalawag.test
MAIL_FROM_NAME="Barangay Kalawag II"
```

---

### **OPTION 3: Brevo (Sendinblue)**

#### **Step 1: Create Brevo Account**
1. Go to [Brevo.com](https://www.brevo.com/)
2. Sign up for FREE account
3. Verify your email

#### **Step 2: Get SMTP Credentials**
1. Go to **SMTP & API**
2. Click **SMTP**
3. Copy your SMTP key

#### **Step 3: Configure .env File**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-brevo-email@gmail.com
MAIL_PASSWORD=your-smtp-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-brevo-email@gmail.com
MAIL_FROM_NAME="Barangay Kalawag II"
```

---

## ⚙️ After Configuration

### **Step 1: Clear Config Cache**
```bash
php artisan config:clear
php artisan cache:clear
```

### **Step 2: Test Email**
```bash
php artisan tinker
```

Then run:
```php
Mail::raw('Test email from Barangay System', function ($message) {
    $message->to('your-test-email@gmail.com')
            ->subject('Test Email');
});
```

If you see `null` response, email was sent successfully!

---

## 📋 When Emails Are Sent

Emails are automatically sent when:

1. **✅ Purok Leader Approves Request**
   - Email: "Clearance Request Approved"
   - Status: `purok_approved`
   - Next: Forwarded to Barangay Office

2. **✅ Barangay Official Approves Request**
   - Email: "Clearance Request Approved - Ready for Pickup"
   - Status: `approved`
   - Action: Resident can claim document

3. **❌ Request is Rejected**
   - Email: "Clearance Request Update - Action Required"
   - Includes: Rejection reason
   - Action: Resident needs to visit office

4. **📋 Status Changes**
   - Email: "Status Update"
   - Shows: Old status → New status
   - Info: Current request status

---

## 🧪 Testing Email Notifications

### **Test with Dummy Users**
1. Login as a dummy resident
2. Submit a clearance request
3. Login as purok leader
4. Approve the request
5. Check resident's email inbox

### **Check Mailtrap Inbox**
If using Mailtrap:
1. Go to Mailtrap dashboard
2. Click your inbox
3. See all test emails
4. Preview email design

---

## 🔧 Troubleshooting

### **Issue: Emails not sending**
**Solution:**
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Check .env file
# Make sure no extra spaces in credentials
```

### **Issue: Gmail "Less secure app" error**
**Solution:**
- Use App Password (not regular password)
- Enable 2-Step Verification first
- Generate new App Password

### **Issue: Connection timeout**
**Solution:**
```env
# Try port 465 with SSL
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

### **Issue: "Username and Password not accepted"**
**Solution:**
- Double-check credentials
- Remove any spaces from password
- Regenerate App Password
- Try different email account

---

## 📊 Email Limits

| Service | Free Limit | Best For |
|---------|-----------|----------|
| **Gmail** | 500/day | Production |
| **Mailtrap** | 500/month | Testing |
| **Brevo** | 300/day | Production |

---

## 🎨 Email Features

### **Included in Templates:**
- ✅ Professional design
- ✅ Mobile responsive
- ✅ Color-coded by status
- ✅ Request details
- ✅ Action buttons
- ✅ Office hours info
- ✅ Barangay branding

### **Email Types:**
1. **Approval Email** (Green theme)
   - Success icon
   - Approval details
   - Next steps
   - Pickup instructions

2. **Rejection Email** (Red theme)
   - Warning icon
   - Rejection reason
   - What to do next
   - Office visit info

3. **Status Update** (Blue theme)
   - Update icon
   - Status change
   - Current status
   - Timeline info

---

## 💡 Best Practices

### **For Development:**
1. Use **Mailtrap** for testing
2. Test all email scenarios
3. Check email design
4. Verify all links work

### **For Production:**
1. Use **Gmail** or **Brevo**
2. Use dedicated email account
3. Monitor email quota
4. Keep App Password secure

### **Security Tips:**
- ✅ Never commit `.env` file
- ✅ Use App Passwords (not regular passwords)
- ✅ Rotate passwords regularly
- ✅ Monitor email logs
- ✅ Use dedicated email for system

---

## 📝 Quick Setup Checklist

- [ ] Choose SMTP service (Gmail/Mailtrap/Brevo)
- [ ] Create/configure email account
- [ ] Generate App Password (if Gmail)
- [ ] Update `.env` file with credentials
- [ ] Clear config cache
- [ ] Test email sending
- [ ] Submit test clearance request
- [ ] Verify email received
- [ ] Check email design
- [ ] Test all email types

---

## 🎯 Recommended Setup

### **For Thesis Testing:**
```
Use: Mailtrap
Why: Free, safe, see all emails in inbox
Setup Time: 5 minutes
```

### **For Thesis Defense/Demo:**
```
Use: Gmail
Why: Real emails, professional, reliable
Setup Time: 10 minutes
```

### **For Production Deployment:**
```
Use: Gmail or Brevo
Why: Free, reliable, sufficient limits
Setup Time: 10 minutes
```

---

## 📞 Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify `.env` configuration
3. Test with `php artisan tinker`
4. Check SMTP credentials
5. Try different SMTP service

---

## ✅ Verification

After setup, verify:
- ✅ Config cache cleared
- ✅ Credentials correct in `.env`
- ✅ Test email sent successfully
- ✅ Email received in inbox
- ✅ Email design looks good
- ✅ All links work
- ✅ Approval emails work
- ✅ Rejection emails work

---

**Your email notification system is now ready!** 📧✨

For Gmail setup, it takes about 10 minutes and is 100% FREE!
