#!/bin/bash

# Deployment Script for Kalawag Barangay System
# Usage: ./deploy.sh

set -e

echo "=========================================="
echo "🚀 Kalawag Barangay System Deployment"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Error: artisan file not found. Are you in the project root?"
    exit 1
fi

# Step 1: Pull latest code
echo "📥 Step 1: Pulling latest code..."
git pull origin main || { print_error "Git pull failed"; exit 1; }
print_success "Code updated"
echo ""

# Step 2: Install PHP dependencies
echo "📦 Step 2: Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev || { print_error "Composer install failed"; exit 1; }
print_success "PHP dependencies installed"
echo ""

# Step 3: Install Node dependencies
echo "📦 Step 3: Installing Node dependencies..."
npm install --production || { print_error "NPM install failed"; exit 1; }
print_success "Node dependencies installed"
echo ""

# Step 4: Build assets
echo "🏗️  Step 4: Building production assets..."
npm run build || { print_error "Asset build failed"; exit 1; }
print_success "Assets built successfully"
echo ""

# Step 5: Delete hot file if exists
if [ -f "public/hot" ]; then
    echo "🗑️  Step 5: Removing hot file..."
    rm public/hot
    print_success "Hot file removed"
    echo ""
fi

# Step 6: Run migrations
echo "🗄️  Step 6: Running database migrations..."
php artisan migrate --force || { print_error "Migration failed"; exit 1; }
print_success "Migrations completed"
echo ""

# Step 7: Clear all caches
echo "🧹 Step 7: Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
print_success "Caches cleared"
echo ""

# Step 8: Optimize for production
echo "⚡ Step 8: Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Application optimized"
echo ""

# Step 9: Set permissions
echo "🔐 Step 9: Setting permissions..."
chmod -R 755 storage bootstrap/cache
print_success "Permissions set"
echo ""

# Step 10: Create storage link (if not exists)
if [ ! -L "public/storage" ]; then
    echo "🔗 Step 10: Creating storage link..."
    php artisan storage:link
    print_success "Storage link created"
    echo ""
fi

# Step 11: Restart services (optional - uncomment if needed)
# echo "🔄 Step 11: Restarting services..."
# sudo systemctl restart php8.2-fpm
# sudo systemctl restart nginx
# print_success "Services restarted"
# echo ""

echo "=========================================="
print_success "Deployment completed successfully! 🎉"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Visit your website to verify deployment"
echo "2. Check logs: tail -f storage/logs/laravel.log"
echo "3. Monitor for any errors"
echo ""
