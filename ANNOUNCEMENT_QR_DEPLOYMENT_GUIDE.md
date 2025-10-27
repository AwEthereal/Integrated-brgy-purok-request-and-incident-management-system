# 🔔 Announcement System & QR Code Deployment Guide

## ✅ **What's New**

### **1. Announcement Bell Notifications** 🔔
- **For Residents:** Bell icon in navigation
- **For Purok Leaders:** Bell icon in navigation
- **Red dot indicator** when featured announcements exist
- **Direct link** to view all barangay announcements

### **2. Automated QR Code Generation** 📱
- QR code automatically generated from `APP_URL` in `.env`
- No manual updates needed when deploying
- Dynamic website link in kiosk

### **3. Environment-Based Configuration** ⚙️
- All URLs pulled from `.env` file
- Change once, updates everywhere
- Production-ready automation

---

## 🎯 **How It Works**

### **Announcement Notifications**

#### **For Residents & Purok Leaders:**
1. Bell icon appears in navigation menu
2. Red dot shows when featured announcements exist
3. Click to view all barangay announcements
4. See full content with priority and category
5. Red dot disappears after viewing page

#### **For Barangay Officials:**
- Manage announcements via `/barangay/announcements`
- Toggle "Featured" checkbox to control red dot
- All users see featured announcements

---

### **QR Code System**

#### **Automatic Generation:**
```php
// In KioskController.php
$websiteUrl = config('app.url');  // From .env
$qrCodeSvg = QrCodeHelper::generateWebsiteQr();  // Auto-generated
```

#### **Display Locations:**
1. **Kiosk Home:** QR code button
2. **QR Code Page:** Full QR code display
3. **Contact Page:** Website URL display

#### **What Happens:**
- QR code points to `APP_URL` from `.env`
- Scanning opens your website
- No hardcoded URLs anywhere

---

## 🚀 **Deployment Steps**

### **Step 1: Update .env File**

When deploying to production, update these values:

```env
# Local Development
APP_URL=http://127.0.0.1:8000
ASSET_URL=http://127.0.0.1:8000

# Production (Example)
APP_URL=https://kalawag-barangay.com
ASSET_URL=https://kalawag-barangay.com

# Or with ngrok for testing
APP_URL=https://your-subdomain.ngrok.io
ASSET_URL=https://your-subdomain.ngrok.io
```

### **Step 2: Clear Configuration Cache**

After updating `.env`, run:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### **Step 3: Test QR Code**

1. Visit `/kiosk/qr-code`
2. Verify QR code displays
3. Scan with phone
4. Should open your production URL

### **Step 4: Test Announcements**

1. Create a test announcement
2. Check "Featured" checkbox
3. Log in as resident/purok leader
4. Verify bell icon shows red dot
5. Click to view announcements

---

## 📝 **Configuration Reference**

### **.env Variables**

| Variable | Purpose | Example |
|----------|---------|---------|
| `APP_URL` | Main website URL | `https://kalawag-barangay.com` |
| `ASSET_URL` | Assets (CSS, JS, images) | `https://kalawag-barangay.com` |
| `APP_NAME` | Application name | `Kalawag Barangay System` |

### **Where URLs Are Used:**

1. **QR Code Generation**
   - File: `app/Helpers/QrCodeHelper.php`
   - Uses: `config('app.url')`

2. **Kiosk Contact Page**
   - File: `resources/views/kiosk/contact.blade.php`
   - Uses: `{{ config('app.url') }}`

3. **QR Code Page**
   - File: `resources/views/kiosk/qr-code.blade.php`
   - Uses: `$websiteUrl` (from controller)

---

## 🔧 **No Manual Updates Needed!**

### **Before (Manual):**
❌ Edit QR code image file
❌ Update hardcoded URLs in views
❌ Regenerate QR code manually
❌ Upload new QR image

### **After (Automated):**
✅ Update `APP_URL` in `.env`
✅ Clear cache
✅ Done! Everything updates automatically

---

## 🎨 **Features**

### **Announcement Bell**

**Visual Indicators:**
- 🔔 Bell icon in navigation
- 🔴 Red dot when featured announcements exist
- ✨ Animated pulsing effect
- 📱 Mobile-responsive

**User Experience:**
- Click bell → View announcements
- See full content
- Priority and category badges
- Posted by and timestamp

### **QR Code**

**Features:**
- 📱 SVG format (scalable)
- 🎨 Clean design
- 📏 320x320 pixels
- 🔄 Auto-generated
- 🌐 Points to production URL

**Display:**
- White background
- Rounded corners
- Shadow effect
- Instructions included

---

## 🧪 **Testing Checklist**

### **Local Testing:**
- [ ] QR code displays on `/kiosk/qr-code`
- [ ] QR code scans correctly
- [ ] Website URL shows on contact page
- [ ] Bell icon appears for residents
- [ ] Bell icon appears for purok leaders
- [ ] Red dot shows when featured announcement exists
- [ ] Red dot disappears after viewing
- [ ] Announcements page loads
- [ ] All announcement data displays correctly

### **Production Testing:**
- [ ] Update `APP_URL` to production domain
- [ ] Clear all caches
- [ ] QR code points to production URL
- [ ] Scan QR code with phone
- [ ] Opens correct website
- [ ] All links work
- [ ] Announcements accessible
- [ ] Bell notifications work

---

## 📱 **Mobile Testing**

### **QR Code Scanning:**
1. Open phone camera
2. Point at QR code
3. Tap notification
4. Should open website

### **Supported Apps:**
- ✅ iPhone Camera (iOS 11+)
- ✅ Android Camera
- ✅ QR Scanner apps
- ✅ WeChat
- ✅ WhatsApp

---

## 🔐 **Security Notes**

### **Environment Variables:**
- Never commit `.env` to Git
- Use `.env.example` as template
- Different values for dev/staging/prod
- Keep production `.env` secure

### **QR Code:**
- Points to HTTPS in production
- No sensitive data in QR
- Public URL only
- Safe to display publicly

---

## 🚨 **Troubleshooting**

### **QR Code Not Displaying:**

**Problem:** Blank space where QR should be

**Solution:**
```bash
# Check if package installed
composer show bacon/bacon-qr-code

# If not installed
composer require bacon/bacon-qr-code

# Clear cache
php artisan config:clear
php artisan view:clear
```

### **QR Code Points to Wrong URL:**

**Problem:** Scans to localhost or wrong domain

**Solution:**
1. Check `.env` file:
   ```env
   APP_URL=https://your-production-domain.com
   ```
2. Clear config cache:
   ```bash
   php artisan config:clear
   ```
3. Refresh page

### **Bell Icon Not Showing:**

**Problem:** No bell icon in navigation

**Solution:**
1. Check user role (resident or purok_leader)
2. Check if logged in
3. Clear browser cache
4. Hard refresh (Ctrl+F5)

### **Red Dot Not Appearing:**

**Problem:** No red dot even with featured announcements

**Solution:**
1. Verify announcement is:
   - ✅ Active (checkbox checked)
   - ✅ Featured (checkbox checked)
   - ✅ Published (date is past or null)
2. Clear cache:
   ```bash
   php artisan cache:clear
   ```
3. Refresh page

---

## 📊 **Database Schema**

### **Announcements Table:**

```sql
announcements
├── id
├── title
├── content
├── category (general, event, emergency, notice)
├── priority (low, normal, high, urgent)
├── created_by (foreign key to users)
├── is_active (boolean)
├── is_featured (boolean) ← Controls red dot
├── published_at (nullable timestamp)
├── expires_at (nullable timestamp)
├── created_at
└── updated_at
```

---

## 🔄 **Deployment Workflow**

### **Development → Production:**

```bash
# 1. On your local machine
git add .
git commit -m "Add announcement notifications and QR system"
git push origin main

# 2. On production server
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force

# 3. Update .env
nano .env
# Change APP_URL to production domain
# Change ASSET_URL to production domain

# 4. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 5. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Test
# Visit /kiosk/qr-code
# Scan QR code
# Test bell notifications
```

---

## 📋 **Quick Reference**

### **Routes:**

| Route | Purpose | Access |
|-------|---------|--------|
| `/announcements` | Public announcements | Residents, Purok Leaders |
| `/barangay/announcements` | Manage announcements | Barangay Officials |
| `/kiosk/qr-code` | QR code display | Public (Kiosk) |
| `/kiosk/contact` | Contact info with URL | Public (Kiosk) |

### **Files Modified:**

| File | Purpose |
|------|---------|
| `app/Helpers/QrCodeHelper.php` | QR code generation |
| `app/Http/Controllers/KioskController.php` | Pass QR to view |
| `app/Http/Controllers/AnnouncementPublicController.php` | Public announcements |
| `resources/views/kiosk/qr-code.blade.php` | Display QR code |
| `resources/views/kiosk/contact.blade.php` | Display website URL |
| `resources/views/layouts/navigation.blade.php` | Bell notifications |
| `resources/views/announcements/public.blade.php` | Public announcement view |
| `routes/web.php` | Public announcement route |

### **Commands:**

```bash
# Install QR package
composer require bacon/bacon-qr-code

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ✨ **Benefits**

### **For Administrators:**
- ✅ No manual QR code updates
- ✅ One place to change URL (.env)
- ✅ Automatic propagation
- ✅ Easy deployment

### **For Users:**
- ✅ Always current QR code
- ✅ Notification for new announcements
- ✅ Easy access to information
- ✅ Mobile-friendly

### **For Developers:**
- ✅ Clean code
- ✅ Environment-based config
- ✅ No hardcoded values
- ✅ Easy maintenance

---

## 🎯 **Summary**

### **What You Need to Do:**

1. **When Deploying:**
   - Update `APP_URL` in `.env`
   - Update `ASSET_URL` in `.env`
   - Clear caches
   - Test QR code

2. **For Announcements:**
   - Create announcement
   - Check "Featured" for red dot
   - Users see bell notification
   - No additional setup needed

3. **That's It!**
   - QR code auto-generates
   - URLs auto-update
   - Notifications work automatically

---

## 📞 **Support**

### **Common Questions:**

**Q: Do I need to regenerate QR code manually?**
A: No! It's automatic based on `APP_URL`.

**Q: What if I change domains?**
A: Just update `APP_URL` in `.env` and clear cache.

**Q: Can I customize QR code size?**
A: Yes, edit `QrCodeHelper.php` and change the size parameter.

**Q: How do I disable bell notifications?**
A: Uncheck "Featured" on all announcements.

**Q: Can I use a custom QR code design?**
A: Yes, but you'll need to modify `QrCodeHelper.php`.

---

## 🎉 **You're All Set!**

Your announcement system and QR code are now:
- ✅ Fully automated
- ✅ Environment-based
- ✅ Production-ready
- ✅ Easy to maintain

**No manual QR code updates ever again!** 🎊
