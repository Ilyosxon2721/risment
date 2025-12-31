# RISMENT - Production Deployment Guide

## 📋 Server Requirements

### Minimum Requirements:
- **PHP:** 8.2 or higher
- **MySQL/MariaDB:** 8.0+
- **Nginx/Apache:** Latest stable
- **Composer:** 2.x
- **Node.js:** 18.x LTS or higher
- **NPM:** 9.x or higher

### PHP Extensions Required:
```
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- PDO_MySQL
- Tokenizer
- XML
- GD
- Zip
```

### Server Specifications:
- **RAM:** Minimum 2GB (4GB recommended)
- **Storage:** Minimum 10GB
- **CPU:** 2 cores recommended

---

## 🚀 Deployment Steps

### 1. Clone Repository

```bash
# SSH на сервер
ssh user@your-server.com

# Перейти в директорию веб-сервера
cd /var/www

# Клонировать репозиторий
git clone https://github.com/Ilyosxon2721/risment.git
cd risment

# Или если репозиторий приватный
git clone git@github.com:Ilyosxon2721/risment.git
```

### 2. Install Dependencies

```bash
# Установить PHP зависимости
composer install --no-dev --optimize-autoloader

# Установить Node.js зависимости
npm install

# Собрать production assets
npm run build
```

### 3. Environment Configuration

```bash
# Создать .env файл из примера
cp .env.example .env

# Отредактировать .env
nano .env
```

**Обязательные настройки в .env:**

```env
# Application
APP_NAME=RISMENT
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=risment_db
DB_USERNAME=risment_user
DB_PASSWORD=STRONG_PASSWORD_HERE

# Mail (обязательно настроить!)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Analytics (получить реальные ID)
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX
YANDEX_METRIKA_ID=XXXXXXXX

# Payment - Click
CLICK_MERCHANT_ID=your_merchant_id
CLICK_SERVICE_ID=your_service_id
CLICK_SECRET_KEY=your_secret_key

# Payment - Payme
PAYME_MERCHANT_ID=your_merchant_id
PAYME_SECRET_KEY=your_secret_key

# Session & Cache (для production)
SESSION_DRIVER=database
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis (опционально, но рекомендуется)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 4. Generate Application Key

```bash
# Сгенерировать уникальный ключ приложения
php artisan key:generate
```

### 5. Database Setup

```bash
# Создать базу данных
mysql -u root -p

# В MySQL консоли:
CREATE DATABASE risment_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'risment_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON risment_db.* TO 'risment_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Запустить миграции
php artisan migrate --force

# Загрузить начальные данные (если есть seeders)
php artisan db:seed --force
```

### 6. Storage & Permissions

```bash
# Создать символическую ссылку для storage
php artisan storage:link

# Установить правильные права доступа
sudo chown -R www-data:www-data /var/www/risment
sudo chmod -R 755 /var/www/risment
sudo chmod -R 775 /var/www/risment/storage
sudo chmod -R 775 /var/www/risment/bootstrap/cache
```

### 7. Optimize Application

```bash
# Кэшировать конфигурацию
php artisan config:cache

# Кэшировать роуты
php artisan route:cache

# Кэшировать views
php artisan view:cache

# Оптимизировать autoloader
composer dump-autoload --optimize
```

### 8. Configure Web Server

#### Nginx Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    root /var/www/risment/public;
    index index.php index.html;

    # SSL Certificates
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    
    # Logs
    access_log /var/log/nginx/risment-access.log;
    error_log /var/log/nginx/risment-error.log;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript application/json;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Сохранить конфиг:**
```bash
sudo nano /etc/nginx/sites-available/risment
sudo ln -s /etc/nginx/sites-available/risment /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### Apache Configuration (.htaccess уже есть)

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/risment/public
    
    <Directory /var/www/risment/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/risment-error.log
    CustomLog ${APACHE_LOG_DIR}/risment-access.log combined
</VirtualHost>
```

### 9. SSL Certificate (Let's Encrypt)

```bash
# Установить Certbot
sudo apt install certbot python3-certbot-nginx

# Для Nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Для Apache
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Авто-обновление
sudo certbot renew --dry-run
```

### 10. Setup Cron Jobs

```bash
# Открыть crontab
crontab -e

# Добавить Laravel scheduler
* * * * * cd /var/www/risment && php artisan schedule:run >> /dev/null 2>&1
```

### 11. Setup Queue Worker (опционально)

```bash
# Создать supervisor конфиг
sudo nano /etc/supervisor/conf.d/risment-worker.conf
```

**Содержимое:**
```ini
[program:risment-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/risment/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/risment/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Запустить supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start risment-worker:*
```

---

## 🔒 Security Checklist

- [ ] **APP_DEBUG=false** в production
- [ ] **APP_ENV=production**
- [ ] Сгенерирован уникальный **APP_KEY**
- [ ] Сильные пароли для БД
- [ ] SSL сертификат установлен
- [ ] Firewall настроен (UFW/iptables)
- [ ] SSH ключи вместо паролей
- [ ] Fail2ban установлен
- [ ] Регулярные бэкапы настроены
- [ ] .env файл защищен (chmod 600)
- [ ] Скрыты .git и другие служебные папки

**Дополнительная безопасность:**

```bash
# Установить fail2ban
sudo apt install fail2ban

# Настроить UFW firewall
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# Защитить .env
chmod 600 /var/www/risment/.env
```

---

## 📊 Monitoring & Logs

### Logs Location:
```
/var/www/risment/storage/logs/laravel.log
/var/log/nginx/risment-access.log
/var/log/nginx/risment-error.log
```

### Monitoring Commands:
```bash
# Просмотр логов Laravel
tail -f /var/www/risment/storage/logs/laravel.log

# Просмотр Nginx logs
tail -f /var/log/nginx/risment-error.log

# Проверка статуса очередей
php artisan queue:work --stop-when-empty

# Мониторинг производительности
php artisan horizon (если используете)
```

---

## 🔄 Updates & Maintenance

### Обновление кода:

```bash
cd /var/www/risment

# Загрузить новые изменения
git pull origin main

# Установить новые зависимости
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Запустить миграции
php artisan migrate --force

# Очистить и пересоздать кэши
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Перезапустить queue workers (если используются)
php artisan queue:restart
```

### Backup Strategy:

```bash
# Создать скрипт бэкапа
sudo nano /usr/local/bin/backup-risment.sh
```

**Содержимое скрипта:**
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/risment"
APP_DIR="/var/www/risment"
DB_NAME="risment_db"
DB_USER="risment_user"
DB_PASS="YOUR_PASSWORD"

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $APP_DIR storage .env

# Keep only last 7 days
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
# Сделать исполняемым
sudo chmod +x /usr/local/bin/backup-risment.sh

# Добавить в cron (ежедневно в 2 AM)
sudo crontab -e
0 2 * * * /usr/local/bin/backup-risment.sh
```

---

## ✅ Final Verification

После развертывания проверьте:

1. **Главная страница:** https://yourdomain.com
2. **Регистрация/Логин:** работает ли аутентификация
3. **Калькулятор:** проверить расчеты
4. **Email:** отправка уведомлений
5. **Форма контактов:** получение лидов
6. **Личный кабинет:** доступ к dashboard
7. **Платежи:** тестовые транзакции (если настроены)
8. **Языки:** переключение RU/UZ
9. **Mobile:** responsive на телефонах
10. **SSL:** зеленый замок в браузере

---

## 🆘 Troubleshooting

### Проблема: 500 Server Error
```bash
# Проверить логи
tail -100 /var/www/risment/storage/logs/laravel.log
tail -100 /var/log/nginx/risment-error.log

# Проверить права
sudo chown -R www-data:www-data /var/www/risment
chmod -R 775 storage bootstrap/cache
```

### Проблема: Blank page
```bash
# Очистить все кэши
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Проблема: Database connection error
```bash
# Проверить .env настройки
cat .env | grep DB_

# Проверить соединение с БД
mysql -u risment_user -p risment_db
```

### Проблема: Assets не загружаются
```bash
# Пересобрать assets
npm run build

# Проверить storage link
php artisan storage:link
```

---

## 📞 Support

После развертывания рекомендуется:
- Настроить мониторинг (UptimeRobot, Pingdom)
- Подключить error tracking (Sentry, Bugsnag)
- Настроить регулярные бэкапы
- Документировать процессы

**Проект готов к production! 🚀**
