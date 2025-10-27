# 🧪 Load Testing Guide - Barangay Management System

## Overview
This guide explains how to create and manage dummy test data for load testing your Barangay Management System.

---

## 📋 Table of Contents
1. [Setup](#setup)
2. [Creating Dummy Data](#creating-dummy-data)
3. [Resetting Dummy Data](#resetting-dummy-data)
4. [Test Scenarios](#test-scenarios)
5. [Performance Monitoring](#performance-monitoring)
6. [Troubleshooting](#troubleshooting)

---

## 🚀 Setup

### Step 1: Run Migration
First, add the `is_dummy` column to the users table:

```bash
php artisan migrate
```

This adds a boolean column `is_dummy` to track test users.

---

## 👥 Creating Dummy Data

### Command
```bash
php artisan db:seed --class=DummyUsersSeeder
```

### What Gets Created

| User Type | Count | Details |
|-----------|-------|---------|
| **Barangay Captain** | 1 | Email: `captain.dummy@test.com` |
| **Barangay Kagawad** | 2 | Emails: `kagawad1.dummy@test.com`, `kagawad2.dummy@test.com` |
| **Secretary** | 1 | Email: `secretary.dummy@test.com` |
| **SK Chairman** | 1 | Email: `sk.dummy@test.com` |
| **Purok Presidents** | 1 per purok | Email: `president.purok[ID].dummy@test.com` |
| **Residents** | 500 | Distributed evenly across all puroks |
| **TOTAL** | ~508+ users | Depends on number of puroks |

### Default Credentials
- **Password for all dummy users:** `password123`
- **Email format:** `[role].dummy@test.com` or `[name].dummy@test.com`

### User Details
All dummy users have complete profiles including:
- ✅ Full name (first, middle, last, suffix)
- ✅ Contact number
- ✅ Date of birth
- ✅ Place of birth
- ✅ Sex/Gender
- ✅ Civil status
- ✅ Nationality
- ✅ Occupation
- ✅ Complete address (house number, street, purok)
- ✅ 90% approved, 10% pending (for residents)

---

## 🗑️ Resetting Dummy Data

### Command
```bash
php artisan dummy:reset
```

### With Force (Skip Confirmation)
```bash
php artisan dummy:reset --force
```

### What Gets Deleted
The reset command safely removes:
- ✅ All dummy users (marked with `is_dummy = true`)
- ✅ Service requests created by dummy users
- ✅ Incident reports created by dummy users
- ✅ Announcements created by dummy users
- ✅ All related data

### What's Protected
- ✅ **Real users** (is_dummy = false)
- ✅ **Real data** created before dummy users
- ✅ **System data** (puroks, settings, etc.)

### Safety Features
- Confirmation prompt (unless --force is used)
- Shows summary before deletion
- Transaction-based (all or nothing)
- Rollback on error

---

## 🧪 Test Scenarios

### 1. User Load Testing
**Test concurrent logins:**
```bash
# Create 500 dummy residents
php artisan db:seed --class=DummyUsersSeeder

# Test scenarios:
- 50 concurrent logins
- 100 concurrent dashboard views
- 200 concurrent page navigations
```

### 2. Request Processing
**Test service request handling:**
- Have dummy residents submit clearance requests
- Test purok leader approval workflow
- Test barangay official approval workflow
- Monitor response times

### 3. Incident Reporting
**Test incident report system:**
- Create multiple incident reports
- Test status updates
- Test notification system
- Monitor database performance

### 4. Announcement System
**Test announcement delivery:**
- Create announcements as dummy officials
- Test featured announcement notifications
- Monitor red dot indicator performance
- Test pagination with many announcements

### 5. Search & Filter
**Test search functionality:**
- Search residents across puroks
- Filter by status, role, purok
- Test pagination with 500+ users
- Monitor query performance

---

## 📊 Performance Monitoring

### Database Queries
Monitor slow queries:
```bash
# Enable query logging in .env
DB_LOG_QUERIES=true

# Check logs
tail -f storage/logs/laravel.log
```

### Response Times
Track page load times:
- Dashboard: < 2 seconds
- User lists: < 3 seconds
- Search results: < 2 seconds
- Reports: < 5 seconds

### Memory Usage
Monitor PHP memory:
```php
// Add to your code
echo memory_get_usage(true) / 1024 / 1024 . " MB\n";
```

### Recommended Tools
1. **Laravel Debugbar** - Query monitoring
2. **Laravel Telescope** - Request tracking
3. **New Relic** - APM monitoring
4. **MySQL Slow Query Log** - Database optimization

---

## 🔧 Troubleshooting

### Issue: Seeder Fails
**Solution:**
```bash
# Check if puroks exist
php artisan tinker
>>> \App\Models\Purok::count()

# If 0, create puroks first
```

### Issue: Memory Limit
**Solution:**
```bash
# Increase PHP memory limit
php -d memory_limit=512M artisan db:seed --class=DummyUsersSeeder
```

### Issue: Timeout
**Solution:**
```bash
# Increase max execution time
php -d max_execution_time=300 artisan db:seed --class=DummyUsersSeeder
```

### Issue: Can't Reset
**Solution:**
```bash
# Check if is_dummy column exists
php artisan migrate:status

# If migration pending, run:
php artisan migrate
```

---

## 💡 Best Practices

### 1. Regular Cleanup
Reset dummy data after each test session:
```bash
php artisan dummy:reset --force
```

### 2. Incremental Testing
Start small, scale up:
- Day 1: 50 users
- Day 2: 100 users
- Day 3: 250 users
- Day 4: 500 users

### 3. Monitor Resources
Watch system resources:
```bash
# CPU usage
top

# Memory usage
free -m

# Disk space
df -h
```

### 4. Backup Before Testing
```bash
# Backup database
php artisan backup:run

# Or manual backup
mysqldump -u root -p barangay_db > backup.sql
```

### 5. Test in Staging First
- Never test with production data
- Use separate database for testing
- Test reset command before using in production

---

## 📈 Suggested Load Testing Progression

### Week 1: Basic Load (50 users)
- Test basic functionality
- Monitor response times
- Identify bottlenecks

### Week 2: Medium Load (100-250 users)
- Test concurrent operations
- Monitor database performance
- Optimize slow queries

### Week 3: High Load (500 users)
- Stress test all features
- Test peak usage scenarios
- Implement caching if needed

### Week 4: Extreme Load (1000+ users)
- Test system limits
- Plan scaling strategy
- Document performance metrics

---

## 🎯 Performance Goals

| Metric | Target | Action if Exceeded |
|--------|--------|-------------------|
| Page Load | < 2s | Optimize queries, add caching |
| Database Queries | < 50 per page | Eager loading, query optimization |
| Memory Usage | < 256MB | Code optimization, garbage collection |
| Concurrent Users | 100+ | Load balancing, server scaling |

---

## 🔐 Security Notes

1. **Never use dummy data in production**
2. **Change default password** if testing in public environment
3. **Reset dummy data** before going live
4. **Monitor for dummy users** in production
5. **Use .env** to disable seeding in production

---

## 📞 Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check database logs
3. Review this guide
4. Contact system administrator

---

## 🎉 Quick Start Commands

```bash
# 1. Setup
php artisan migrate

# 2. Create dummy data
php artisan db:seed --class=DummyUsersSeeder

# 3. Test your system
# ... perform your tests ...

# 4. Reset when done
php artisan dummy:reset

# 5. Repeat as needed
```

---

**Happy Testing! 🚀**
