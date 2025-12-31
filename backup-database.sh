#!/bin/bash
# Database Backup Script for RISMENT
# Schedule with cron: 0 2 * * * /var/www/risment/backup-database.sh

set -e

# Configuration
DB_NAME="risment_production"
DB_USER="risment_user"
DB_PASSWORD="${DB_PASSWORD:-}"  # Set via environment variable
BACKUP_DIR="/var/backups/risment/database"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30

# Create backup directory
mkdir -p $BACKUP_DIR

# Create backup
echo "Creating database backup..."
if [ -z "$DB_PASSWORD" ]; then
    # Prompt for password if not set
    mysqldump -u $DB_USER -p $DB_NAME > $BACKUP_DIR/backup_$DATE.sql
else
    mysqldump -u $DB_USER -p$DB_PASSWORD $DB_NAME > $BACKUP_DIR/backup_$DATE.sql
fi

# Compress backup
gzip $BACKUP_DIR/backup_$DATE.sql
echo "✓ Backup created: $BACKUP_DIR/backup_$DATE.sql.gz"

# Clean old backups
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +$RETENTION_DAYS -delete
echo "✓ Cleaned backups older than $RETENTION_DAYS days"

# Log backup
echo "$(date): Backup completed - backup_$DATE.sql.gz" >> $BACKUP_DIR/backup.log
