#!/bin/bash
# RISMENT Production Deployment Script
# Usage: ./deploy-production.sh

set -e  # Exit on any error

echo "🚀 Starting RISMENT deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="/var/www/risment"
BACKUP_DIR="/var/backups/risment"
DATE=$(date +%Y%m%d_%H%M%S)

# Step 1: Enable maintenance mode
echo -e "${YELLOW}[1/12] Enabling maintenance mode...${NC}"
php artisan down --message="Deployment in progress. We'll be back shortly!" --retry=60

# Step 2: Create backup
echo -e "${YELLOW}[2/12] Creating database backup...${NC}"
mkdir -p $BACKUP_DIR
mysqldump -u risment_user -p risment_production > $BACKUP_DIR/backup_$DATE.sql
gzip $BACKUP_DIR/backup_$DATE.sql
echo -e "${GREEN}✓ Backup created: backup_$DATE.sql.gz${NC}"

# Step 3: Pull latest code
echo -e "${YELLOW}[3/12] Pulling latest code from git...${NC}"
git fetch origin
git reset --hard origin/main
echo -e "${GREEN}✓ Code updated${NC}"

# Step 4: Install composer dependencies
echo -e "${YELLOW}[4/12] Installing composer dependencies...${NC}"
composer install --optimize-autoloader --no-dev --no-interaction
echo -e "${GREEN}✓ Composer dependencies installed${NC}"

# Step 5: Install npm dependencies and build assets
echo -e "${YELLOW}[5/12] Building assets...${NC}"
npm ci
npm run build
echo -e "${GREEN}✓ Assets built${NC}"

# Step 6: Clear all caches
echo -e "${YELLOW}[6/12] Clearing caches...${NC}"
php artisan optimize:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
echo -e "${GREEN}✓ Caches cleared${NC}"

# Step 7: Run database migrations
echo -e "${YELLOW}[7/12] Running database migrations...${NC}"
php artisan migrate --force --no-interaction
echo -e "${GREEN}✓ Migrations completed${NC}"

# Step 8: Optimize for production
echo -e "${YELLOW}[8/12] Optimizing for production...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
echo -e "${GREEN}✓ Application optimized${NC}"

# Step 9: Set permissions
echo -e "${YELLOW}[9/12] Setting file permissions...${NC}"
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✓ Permissions set${NC}"

# Step 10: Restart services
echo -e "${YELLOW}[10/12] Restarting services...${NC}"
php artisan queue:restart
systemctl restart php8.4-fpm
systemctl reload nginx
echo -e "${GREEN}✓ Services restarted${NC}"

# Step 11: Clean up old backups (keep last 7 days)
echo -e "${YELLOW}[11/12] Cleaning old backups...${NC}"
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +7 -delete
echo -e "${GREEN}✓ Old backups cleaned${NC}"

# Step 12: Disable maintenance mode
echo -e "${YELLOW}[12/12] Disabling maintenance mode...${NC}"
php artisan up
echo -e "${GREEN}✓ Application is live!${NC}"

# Final verification
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Deployment completed successfully! ✅${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo "Post-deployment checklist:"
echo "  - Check application: https://risment.uz"
echo "  - Monitor logs: tail -f storage/logs/laravel.log"
echo "  - Check queue workers: php artisan queue:monitor"
echo ""
echo -e "${YELLOW}Backup location: $BACKUP_DIR/backup_$DATE.sql.gz${NC}"
