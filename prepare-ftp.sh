#!/bin/bash

# RISMENT - Prepare files for FTP upload
# This script prepares the project for manual FTP upload

echo "🚀 Preparing RISMENT for FTP deployment..."

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check if we're in project directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Not in Laravel project directory"
    echo "Run this from: /Applications/MAMP/htdocs/risment"
    exit 1
fi

echo ""
echo "📦 Step 1: Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader
echo -e "${GREEN}✓${NC} Dependencies installed"

echo ""
echo "🔑 Step 2: Generating application key..."
if [ ! -f ".env" ]; then
    cp .env.example .env
fi
php artisan key:generate
echo -e "${GREEN}✓${NC} Key generated"

echo ""
echo "📊 Step 3: Setting up local database for export..."
php artisan migrate --force
echo -e "${GREEN}✓${NC} Database migrated"

echo ""
echo "💾 Step 4: Exporting database..."
# For MAMP
/Applications/MAMP/Library/bin/mysqldump -u root -proot risment > ~/Desktop/risment_database.sql 2>/dev/null || \
mysqldump -u root -p risment > ~/Desktop/risment_database.sql

if [ -f ~/Desktop/risment_database.sql ]; then
    echo -e "${GREEN}✓${NC} Database exported to ~/Desktop/risment_database.sql"
else
    echo -e "${YELLOW}⚠${NC} Could not export database automatically"
    echo "Please export manually from phpMyAdmin"
fi

echo ""
echo "🔒 Step 5: Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✓${NC} Application optimized"

echo ""
echo "📋 Step 6: Your APP_KEY (save this!):"
grep "APP_KEY" .env | cut -d= -f2
echo ""

echo -e "${GREEN}✅ Project is ready for FTP upload!${NC}"
echo ""
echo "📁 Files to upload via FTP:"
echo "  ✅ app/"
echo "  ✅ bootstrap/"
echo "  ✅ config/"
echo "  ✅ database/"
echo "  ✅ public/"
echo "  ✅ resources/"
echo "  ✅ routes/"
echo "  ✅ storage/"
echo "  ✅ vendor/        ← This is large! ~150MB"
echo "  ✅ .htaccess"
echo "  ✅ artisan"
echo "  ✅ composer.json"
echo "  ✅ composer.lock"
echo "  ✅ .env.example   ← Copy to .env on server"
echo ""
echo "❌ DO NOT upload:"
echo "  - node_modules/"
echo "  - .git/"
echo "  - .env (create on server instead)"
echo ""
echo "📄 Database file saved to: ~/Desktop/risment_database.sql"
echo ""
echo "Next steps:"
echo "1. Open FileZilla"
echo "2. Connect to ftp.risment.uz (or your FTP server)"
echo "3. Upload all files to /risment/ folder"
echo "4. Import risment_database.sql via phpMyAdmin"
echo "5. Create .env on server and paste your APP_KEY"
echo "6. Set Document Root to /risment/public"
echo ""
echo "🎉 Good luck!"
