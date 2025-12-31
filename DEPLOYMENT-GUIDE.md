# Загрузка RISMENT на Production Хостинг

Пошаговая инструкция развертывания Laravel проекта на сервере.

---

## Подготовка к деплою

### Что вам понадобится:

- ✅ VPS сервер (Ubuntu 22.04 LTS)
- ✅ SSH доступ к серверу
- ✅ Домен (risment.uz) с доступом к DNS
- ✅ Git репозиторий (GitHub/GitLab/Bitbucket)

---

## Метод 1: Git Deployment (Рекомендуется) ⭐

### Шаг 1: Подготовка локального репозитория

```bash
# На вашем локальном компьютере
cd /Applications/MAMP/htdocs/risment

# Инициализировать git (если еще не сделано)
git init

# Добавить .gitignore
cat > .gitignore << 'EOF'
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
Homestead.json
Homestead.yaml
auth.json
npm-debug.log
yarn-error.log
/.fleet
/.idea
/.vscode
EOF

# Добавить все файлы
git add .

# Сделать первый коммит
git commit -m "Initial commit - RISMENT Fulfillment Platform"

# Создать репозиторий на GitHub/GitLab
# Затем добавить remote
git remote add origin git@github.com:YOUR_USERNAME/risment.git

# Запушить код
git branch -M main
git push -u origin main
```

### Шаг 2: Подключение к серверу

```bash
# Подключиться к серверу через SSH
ssh root@YOUR_SERVER_IP

# Или если у вас есть username:
ssh username@YOUR_SERVER_IP
```

### Шаг 3: Установка необходимого ПО на сервер

```bash
# Обновить систему
sudo apt update && sudo apt upgrade -y

# Установить базовые пакеты
sudo apt install -y software-properties-common curl wget git unzip

# Добавить репозиторий PHP 8.4
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Установить PHP 8.4 и расширения
sudo apt install -y php8.4-fpm php8.4-cli php8.4-mysql php8.4-redis \
    php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip \
    php8.4-gd php8.4-bcmath php8.4-intl

# Установить Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Установить Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Установить MySQL (см. PRODUCTION-SETUP.md)
sudo apt install mysql-server -y

# Установить Redis (см. PRODUCTION-SETUP.md)
sudo apt install redis-server -y

# Установить Nginx
sudo apt install nginx -y

# Проверить все установилось
php -v
composer --version
node -v
npm -v
mysql --version
redis-server --version
nginx -v
```

### Шаг 4: Настройка Git на сервере

```bash
# Настроить SSH ключ для доступа к Git (если приватный репозиторий)
ssh-keygen -t ed25519 -C "server@risment.uz"
# Нажмите Enter на все вопросы

# Показать публичный ключ
cat ~/.ssh/id_ed25519.pub

# Скопируйте этот ключ и добавьте в GitHub:
# GitHub → Settings → SSH Keys → Add SSH Key
```

### Шаг 5: Клонирование проекта

```bash
# Создать директорию для проекта
sudo mkdir -p /var/www
cd /var/www

# Клонировать репозиторий
sudo git clone git@github.com:YOUR_USERNAME/risment.git risment

# Или HTTPS (если не настроили SSH):
sudo git clone https://github.com/YOUR_USERNAME/risment.git risment

# Перейти в директорию
cd risment

# Проверить что файлы на месте
ls -la
```

### Шаг 6: Установка зависимостей

```bash
# Установить PHP зависимости
sudo composer install --optimize-autoloader --no-dev

# Установить Node зависимости
sudo npm ci

# Собрать assets
sudo npm run build
```

### Шаг 7: Настройка прав доступа

```bash
# Изменить владельца на www-data (пользователь nginx/php-fpm)
sudo chown -R www-data:www-data /var/www/risment

# Установить правильные права
sudo chmod -R 755 /var/www/risment
sudo chmod -R 775 /var/www/risment/storage
sudo chmod -R 775 /var/www/risment/bootstrap/cache

# Создать symbolic link для storage
php artisan storage:link
```

### Шаг 8: Настройка .env файла

```bash
# Скопировать production template
sudo cp .env.example.production .env

# Редактировать .env
sudo nano .env
```

**Заполните:**
- DB_PASSWORD (из PRODUCTION-SETUP.md)
- REDIS_PASSWORD (из PRODUCTION-SETUP.md)
- MAIL_* настройки (из PRODUCTION-SETUP.md)
- APP_URL=https://risment.uz

```bash
# Сгенерировать APP_KEY
php artisan key:generate

# Проверить .env
cat .env | grep APP_KEY
# Должен быть заполнен
```

### Шаг 9: База данных

```bash
# Запустить миграции
php artisan migrate --force

# Запустить seeders (если нужно)
php artisan db:seed --force

# Проверить подключение
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit
```

### Шаг 10: Оптимизация для production

```bash
# Очистить и пересоздать кэш
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## Шаг 11: Настройка Nginx

```bash
# Создать конфигурацию для сайта
sudo nano /etc/nginx/sites-available/risment.uz
```

**Вставьте:**

```nginx
# Временная HTTP конфигурация (для получения SSL)
server {
    listen 80;
    server_name risment.uz www.risment.uz;
    
    root /var/www/risment/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # File upload limit
    client_max_body_size 50M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Allow Let's Encrypt challenge
    location ~ /.well-known {
        allow all;
    }
}
```

```bash
# Создать symbolic link
sudo ln -s /etc/nginx/sites-available/risment.uz /etc/nginx/sites-enabled/

# Удалить default сайт
sudo rm /etc/nginx/sites-enabled/default

# Проверить конфигурацию
sudo nginx -t

# Должно показать: syntax is ok

# Перезапустить Nginx
sudo systemctl restart nginx
sudo systemctl enable nginx
```

---

## Шаг 12: Настройка DNS

**В панели управления доменом (например, Beget, Reg.ru, CloudFlare):**

Добавьте A-запись:

```
Тип: A
Имя: @
Значение: YOUR_SERVER_IP
TTL: 3600

Тип: A
Имя: www
Значение: YOUR_SERVER_IP
TTL: 3600
```

Подождите 5-30 минут пока DNS обновится.

**Проверка:**
```bash
# На локальном компьютере
ping risment.uz
# Должен ответить ваш SERVER_IP
```

---

## Шаг 13: Установка SSL сертификата (Let's Encrypt)

```bash
# Установить Certbot
sudo apt install certbot python3-certbot-nginx -y

# Получить SSL сертификат
sudo certbot --nginx -d risment.uz -d www.risment.uz

# Ответьте на вопросы:
# Email: your-email@example.com
# Agree to terms: Y
# Share email: N (по желанию)
# Redirect HTTP to HTTPS: 2 (Yes)

# Проверить что сертификат установлен
sudo certbot certificates

# Автообновление (создается автоматически)
sudo systemctl status certbot.timer
```

**Nginx автоматически обновит конфиг для HTTPS!**

---

## Шаг 14: Настройка Queue Workers (Supervisor)

```bash
# Установить Supervisor
sudo apt install supervisor -y

# Создать конфиг для worker
sudo nano /etc/supervisor/conf.d/risment-worker.conf
```

**Вставьте:**

```ini
[program:risment-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/risment/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasflags=TERM
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/risment/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Применить конфигурацию
sudo supervisorctl reread
sudo supervisorctl update

# Запустить workers
sudo supervisorctl start risment-worker:*

# Проверить статус
sudo supervisorctl status
# Должно показать: RUNNING
```

---

## Шаг 15: Настройка Cron (Планировщик задач)

```bash
# Редактировать crontab для www-data
sudo crontab -e -u www-data
```

**Добавьте:**

```cron
# Laravel Scheduler
* * * * * cd /var/www/risment && php artisan schedule:run >> /dev/null 2>&1

# Database Backup (daily at 2 AM)
0 2 * * * /var/www/risment/backup-database.sh

# Clean old logs (weekly)
0 0 * * 0 find /var/www/risment/storage/logs -name "*.log" -mtime +14 -delete
```

Сохраните и выйдите (Ctrl+X, Y, Enter)

---

## Шаг 16: Финальная проверка

```bash
# 1. Проверить сайт в браузере
# Откройте: https://risment.uz

# 2. Проверить PHP-FPM
sudo systemctl status php8.4-fpm

# 3. Проверить Nginx
sudo systemctl status nginx

# 4. Проверить MySQL
sudo systemctl status mysql

# 5. Проверить Redis
sudo systemctl status redis

# 6. Проверить логи
tail -f /var/www/risment/storage/logs/laravel.log

# 7. Проверить queue workers
sudo supervisorctl status

# Все должно быть: RUNNING или active
```

---

## Обновление проекта (После первого деплоя)

**Когда вы вносите изменения:**

```bash
# На локальном компьютере
git add .
git commit -m "Feature: описание изменений"
git push origin main

# На сервере
ssh username@YOUR_SERVER_IP
cd /var/www/risment

# Запустить deployment скрипт
./deploy-production.sh

# Или вручную:
sudo -u www-data git pull origin main
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data npm ci && npm run build
php artisan migrate --force
php artisan optimize
sudo supervisorctl restart risment-worker:*
```

---

## Метод 2: FTP Upload (НЕ рекомендуется)

⚠️ **Только для shared hosting без SSH доступа**

```bash
# 1. Собрать проект локально
composer install --optimize-autoloader --no-dev
npm ci && npm run build

# 2. Скачать FileZilla или другой FTP клиент

# 3. Подключиться к FTP:
Host: ftp.your-hosting.com
Username: your_ftp_username
Password: your_ftp_password
Port: 21

# 4. Загрузить все файлы в public_html или www

# 5. Через панель хостинга:
- Создать базу данных
- Импортировать SQL дамп
- Настроить .env файл
```

**Минусы FTP:**
- Медленно
- Нет контроля версий
- Риск ошибок
- Сложно откатить изменения

---

## Устранение проблем

### Ошибка 500

```bash
# Проверить логи
tail -f /var/www/risment/storage/logs/laravel.log
tail -f /var/log/nginx/error.log

# Проверить права
sudo chown -R www-data:www-data /var/www/risment/storage
sudo chmod -R 775 /var/www/risment/storage

# Очистить кэш
php artisan cache:clear
php artisan config:clear
```

### Ошибка 403

```bash
# Проверить права на public
sudo chmod 755 /var/www/risment/public
sudo chown www-data:www-data /var/www/risment/public
```

### CSS/JS не загружаются

```bash
# Пересобрать assets
npm run build

# Очистить view кэш
php artisan view:clear
```

### Database connection error

```bash
# Проверить .env
cat .env | grep DB_

# Проверить MySQL
sudo systemctl status mysql
mysql -u risment_user -p risment_production
```

---

## Безопасность

### Firewall

```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### Fail2Ban (защита от брутфорса)

```bash
sudo apt install fail2ban -y
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### Обновления безопасности

```bash
# Настроить автообновления
sudo apt install unattended-upgrades -y
sudo dpkg-reconfigure -plow unattended-upgrades
# Выбрать: Yes
```

---

## Мониторинг

```bash
# Установить htop для мониторинга
sudo apt install htop -y

# Запустить
htop

# Проверить диск
df -h

# Проверить память
free -m

# Проверить процессы
ps aux | grep php
ps aux | grep nginx
```

---

## Checklist финального деплоя

- [ ] Сервер настроен (PHP, MySQL, Redis, Nginx)
- [ ] Проект склонирован через Git
- [ ] Зависимости установлены (composer, npm)
- [ ] .env настроен с production значениями
- [ ] APP_KEY сгенерирован
- [ ] Миграции запущены
- [ ] Права доступа установлены (www-data)
- [ ] Nginx настроен и запущен
- [ ] DNS записи указывают на сервер
- [ ] SSL сертификат установлен
- [ ] Queue workers запущены (supervisor)
- [ ] Cron настроен для scheduler
- [ ] Firewall настроен (UFW)
- [ ] Резервное копирование настроено
- [ ] Сайт открывается по HTTPS
- [ ] Логи не показывают ошибок
- [ ] Все функции работают

---

## Готово! 🎉

Ваш проект теперь работает на production сервере:

**URL:** https://risment.uz

**Логин в админку:** https://risment.uz/admin

**Мониторинг:**
```bash
# Подключение к серверу
ssh username@YOUR_SERVER_IP

# Логи
tail -f /var/www/risment/storage/logs/laravel.log

# Статус сервисов
sudo systemctl status nginx php8.4-fpm mysql redis
sudo supervisorctl status
```

**Следующие шаги:**
- Настроить регулярные бэкапы
- Настроить мониторинг (Sentry, New Relic)
- Подключить CDN для статики (Cloudflare)
- Настроить email уведомления об ошибках
