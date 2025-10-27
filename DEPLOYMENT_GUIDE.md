# Deployment Guide - Kalawag Barangay System

## 📋 Table of Contents
1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Environment Configuration](#environment-configuration)
3. [Building Assets](#building-assets)
4. [Deployment Steps](#deployment-steps)
5. [Post-Deployment](#post-deployment)
6. [Rollback Procedure](#rollback-procedure)

---

## 🔍 Pre-Deployment Checklist

### Code Review
- [ ] All features tested locally
- [ ] No console errors in browser
- [ ] All forms submit correctly
- [ ] File uploads work
- [ ] Database migrations tested
- [ ] Sensitive data removed from code

### Security
- [ ] `.env` file NOT in git repository
- [ ] Database credentials are secure
- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production`
- [ ] HTTPS enabled
- [ ] CSRF protection working
- [ ] File upload validation in place

### Performance
- [ ] Assets built for production (`npm run build`)
- [ ] Database indexes optimized
- [ ] Cache configured properly
- [ ] Queue workers set up (if needed)

---

## ⚙️ Environment Configuration

### Development vs Production

| Setting | Development | Production |
|---------|-------------|------------|
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `APP_URL` | `http://127.0.0.1:8000` | `https://yourdomain.com` |
| `LOG_LEVEL` | `debug` | `error` |
| `SESSION_DRIVER` | `file` | `database` |
| `FILESYSTEM_DISK` | `local` | `public` |
| Vite Dev Server | Required | Not needed |

### Critical Changes for Production

#### 1. Remove Vite Dev Server Configuration
```env
# ❌ Remove these in production .env:
# VITE_DEV_SERVER_URL="http://127.0.0.1:5173"
# VITE_DEV_SERVER_HOST=127.0.0.1
```

#### 2. Update URLs
```env
# Development:
APP_URL=http://127.0.0.1:8000

# Production:
APP_URL=https://yourdomain.com
```

#### 3. Secure Database
```env
# Development:
DB_HOST=127.0.0.1
DB_PORT=3308
DB_USERNAME=root
DB_PASSWORD=edisonlaurico

# Production:
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=secure_user
DB_PASSWORD=very_secure_password_here
```

#### 4. Update Mail Configuration
```env
# Development (Mailtrap):
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525

# Production (Gmail/SMTP):
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

#### 5. Update Reverb/WebSocket
```env
# Development:
VITE_REVERB_HOST=127.0.0.1
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http

# Production:
VITE_REVERB_HOST=yourdomain.com
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https
```

---

## 🏗️ Building Assets

### Step 1: Install Dependencies
```bash
npm install --production
```

### Step 2: Build for Production
```bash
npm run build
```

This creates optimized assets in `public/build/` directory.

### Step 3: Verify Build
Check that `public/build/manifest.json` exists and contains your assets.

### ⚠️ Important Notes:
- **DO NOT** run `npm run dev` on production server
- **DO NOT** commit `public/build/` to git (add to `.gitignore`)
- **DO** build assets before deploying
- **DO** delete `public/hot` file if it exists

---

## 🚀 Deployment Steps

### Option 1: Manual Deployment

#### 1. Prepare Production Environment
```bash
# On production server
cd /path/to/your/app

# Pull latest code
git pull origin main

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install and build assets
npm install --production
npm run build
```

#### 2. Configure Environment
```bash
# Copy production environment file
cp .env.production.example .env

# Edit .env with production values
nano .env

# Generate application key (if needed)
php artisan key:generate
```

#### 3. Set Permissions
```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 4. Run Migrations
```bash
# Run database migrations
php artisan migrate --force

# Seed database (if needed)
php artisan db:seed --force
```

#### 5. Optimize Application
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 6. Create Storage Link
```bash
php artisan storage:link
```

### Option 2: Automated Deployment (Recommended)

Create a deployment script: `deploy.sh`

```bash
#!/bin/bash
set -e

echo "🚀 Starting deployment..."

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install --production

# Build assets
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx

echo "✅ Deployment complete!"
```

---

## 🔧 Post-Deployment

### 1. Verify Deployment
- [ ] Visit production URL
- [ ] Check all pages load correctly
- [ ] Test user registration/login
- [ ] Test form submissions
- [ ] Test file uploads
- [ ] Check database connections
- [ ] Verify email sending
- [ ] Test real-time features (Pusher)

### 2. Monitor Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Nginx/Apache logs
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

### 3. Performance Check
- [ ] Page load times acceptable
- [ ] Assets loading correctly
- [ ] No 404 errors
- [ ] HTTPS working
- [ ] CDN configured (if applicable)

---

## 🔄 Rollback Procedure

If deployment fails:

### Quick Rollback
```bash
# Revert to previous commit
git reset --hard HEAD~1

# Reinstall dependencies
composer install --optimize-autoloader --no-dev

# Rebuild assets
npm run build

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Database Rollback
```bash
# Rollback last migration
php artisan migrate:rollback --step=1

# Or rollback all migrations from last batch
php artisan migrate:rollback
```

---

## 📝 Configuration Files Comparison

### vite.config.js

**✅ Current (Works for both Dev & Production):**
```javascript
server: {
    host: '127.0.0.1',  // For local development
    port: 5173,
    hmr: {
        protocol: 'ws',
        host: '127.0.0.1',
    },
}
```

**❌ Don't change back to `0.0.0.0`** - This caused your issues!

**Why?**
- In **development**: Vite runs, serves assets from port 5173
- In **production**: Vite doesn't run, assets are pre-built in `public/build/`
- The `vite.config.js` is only used during development and build time
- Production servers never run Vite, so the host setting doesn't matter

### Key Point:
**You DON'T need to change `vite.config.js` for production!**
- Just run `npm run build` before deploying
- The built assets go to `public/build/`
- Laravel automatically uses built assets when `public/hot` doesn't exist

---

## 🎯 Quick Reference

### Development Commands
```bash
# Start development
npm run dev
php artisan serve

# Access at:
http://127.0.0.1:8000
```

### Production Commands
```bash
# Build for production
npm run build

# Deploy
git pull
composer install --no-dev
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Files
- **Development:** `.env` (use `.env.local.example` as template)
- **Production:** `.env` (use `.env.production.example` as template)

---

## ⚠️ Common Mistakes to Avoid

1. ❌ Running `npm run dev` on production server
2. ❌ Forgetting to run `npm run build` before deploying
3. ❌ Leaving `APP_DEBUG=true` in production
4. ❌ Using development database credentials in production
5. ❌ Not setting proper file permissions
6. ❌ Forgetting to run migrations
7. ❌ Not clearing caches after deployment
8. ❌ Committing `.env` file to git
9. ❌ Not testing deployment in staging first
10. ❌ Changing `vite.config.js` host back to `0.0.0.0`

---

## ✅ Deployment Checklist Summary

### Before Deployment
- [ ] Code tested locally
- [ ] `.env.production.example` configured
- [ ] Assets built (`npm run build`)
- [ ] Database backup created
- [ ] Deployment script tested

### During Deployment
- [ ] Code pulled/uploaded
- [ ] Dependencies installed
- [ ] Assets built
- [ ] Migrations run
- [ ] Caches cleared and rebuilt
- [ ] Permissions set

### After Deployment
- [ ] Site accessible
- [ ] All features working
- [ ] Logs monitored
- [ ] Performance verified
- [ ] Team notified

---

## 🆘 Troubleshooting

### Issue: Assets not loading
**Solution:**
```bash
npm run build
php artisan config:clear
php artisan cache:clear
```

### Issue: 500 Server Error
**Solution:**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Set permissions
chmod -R 755 storage bootstrap/cache
```

### Issue: Database connection failed
**Solution:**
- Verify `.env` database credentials
- Check database server is running
- Verify firewall rules

### Issue: Styles not applying
**Solution:**
- Ensure `npm run build` was run
- Check `public/build/manifest.json` exists
- Verify no `public/hot` file exists
- Clear browser cache

---

## 📞 Support

For deployment issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server logs
3. Verify `.env` configuration
4. Review this deployment guide
5. Check Laravel documentation: https://laravel.com/docs/deployment

---

**Remember:** The `127.0.0.1` configuration in `vite.config.js` is CORRECT and should NOT be changed for production!
