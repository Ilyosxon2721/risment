#!/bin/bash

# RISMENT - Quick Deployment Script
# This script automates the deployment process

set -e  # Exit on error

echo "🚀 Starting RISMENT deployment..."

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="/var/www/risment"
BACKUP_DIR="/var/backups/risment"

# Functions
success() {
    echo -e "${GREEN}✓${NC} $1"
}

warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

error() {
    echo -e "${RED}✗${NC} $1"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    error "Please run as root or with sudo"
    exit 1
fi

echo "📦 Step 1: Pulling latest code from GitHub..."
cd $APP_DIR
git pull origin main
success "Code updated"

echo "📦 Step 2: Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
success "PHP dependencies installed"

echo "📦 Step 3: Installing Node dependencies..."
npm install
success "Node dependencies installed"

echo "🏗️  Step 4: Building assets..."
npm run build
success "Assets built"

echo "🧹 Step 5: Clearing caches..."
php artisan optimize:clear
php artisan cache:clear
success "Caches cleared"

echo "🗄️  Step 6: Running database migrations..."
php artisan migrate --force
success "Migrations completed"

echo "⚡ Step 7: Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
success "Application optimized"

echo "👥 Step 8: Setting permissions..."
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 775 $APP_DIR/storage
chmod -R 775 $APP_DIR/bootstrap/cache
success "Permissions set"

echo "🔄 Step 9: Restarting services..."
# Restart PHP-FPM to flush OPcache (new PHP files won't load without this)
if systemctl is-active --quiet php8.4-fpm; then
    systemctl restart php8.4-fpm
    success "PHP-FPM restarted"
elif systemctl is-active --quiet php8.3-fpm; then
    systemctl restart php8.3-fpm
    success "PHP-FPM restarted"
fi

if [ -f "/etc/supervisor/conf.d/risment-worker.conf" ]; then
    php artisan queue:restart
    supervisorctl restart risment-worker:*
    success "Queue workers restarted"
fi

systemctl reload nginx
success "Nginx reloaded"

echo ""
echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo "🌐 Application URL: $(grep APP_URL $APP_DIR/.env | cut -d '=' -f2)"
echo ""
echo "📊 Next steps:"
echo "  1. Test the application"
echo "  2. Check logs: tail -f storage/logs/laravel.log"
echo "  3. Monitor server: htop"
echo ""
