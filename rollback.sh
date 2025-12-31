#!/bin/bash
# Rollback Script for RISMENT
# Usage: ./rollback.sh [backup_timestamp]
# Example: ./rollback.sh 20251228_120000

set -e

BACKUP_TIMESTAMP=$1
BACKUP_DIR="/var/backups/risment"
APP_DIR="/var/www/risment"

if [ -z "$BACKUP_TIMESTAMP" ]; then
    echo "Error: Please provide backup timestamp"
    echo "Usage: ./rollback.sh YYYYMMDD_HHMMSS"
    echo ""
    echo "Available backups:"
    ls -1 $BACKUP_DIR/backup_*.sql.gz | tail -5
    exit 1
fi

BACKUP_FILE="$BACKUP_DIR/backup_${BACKUP_TIMESTAMP}.sql.gz"

if [ ! -f "$BACKUP_FILE" ]; then
    echo "Error: Backup file not found: $BACKUP_FILE"
    exit 1
fi

echo "⚠️  WARNING: This will rollback to backup from $BACKUP_TIMESTAMP"
read -p "Are you sure? (yes/no): " -r
if [[ ! $REPLY =~ ^yes$ ]]; then
    echo "Rollback cancelled"
    exit 0
fi

echo "Starting rollback..."

# Enable maintenance mode
php artisan down

# Decompress backup
gunzip -c $BACKUP_FILE > /tmp/rollback_$BACKUP_TIMESTAMP.sql

# Restore database
echo "Restoring database..."
mysql -u risment_user -p risment_production < /tmp/rollback_$BACKUP_TIMESTAMP.sql

# Clear caches
php artisan optimize:clear
php artisan optimize

# Restart queue
php artisan queue:restart

# Disable maintenance mode
php artisan up

# Clean temp file
rm /tmp/rollback_$BACKUP_TIMESTAMP.sql

echo "✓ Rollback completed successfully"
