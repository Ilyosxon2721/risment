#!/bin/bash

# RISMENT - Create ZIP archive for FTP upload
# This creates a production-ready archive

echo "📦 Creating RISMENT archive for FTP upload..."

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check if we're in project directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Not in Laravel project directory"
    exit 1
fi

# Archive name in project root
ARCHIVE="risment-production.zip"

echo ""
echo "🗑️  Removing old archive if exists..."
rm -f "$ARCHIVE"

echo ""
echo "📦 Creating ZIP archive..."
echo "   This may take 2-3 minutes..."

# Create archive (excluding unnecessary files)
zip -r "$ARCHIVE" . \
  -x "*.git/*" \
  -x "node_modules/*" \
  -x ".env" \
  -x "*.DS_Store" \
  -x "storage/logs/*" \
  -x "storage/framework/cache/*" \
  -x "storage/framework/sessions/*" \
  -x "storage/framework/views/*" \
  -x "tests/*" \
  -x "$ARCHIVE" \
  > /dev/null 2>&1

if [ -f "$ARCHIVE" ]; then
    SIZE=$(du -h "$ARCHIVE" | cut -f1)
    echo -e "${GREEN}✓${NC} Archive created successfully!"
    echo ""
    echo "📁 Location: $(pwd)/$ARCHIVE"
    echo "📊 Size: $SIZE"
    echo ""
    echo "✅ Ready to upload!"
    echo ""
    echo "Next steps:"
    echo "1. Find $ARCHIVE in your project folder"
    echo "2. Open FileZilla and connect to ftp.risment.uz"
    echo "3. Upload $ARCHIVE to home directory"
    echo "4. In cPanel File Manager, extract the archive"
    echo "5. Continue with deployment guide"
    echo ""
else
    echo -e "${YELLOW}⚠${NC} Failed to create archive"
    exit 1
fi
