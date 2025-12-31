# Настройка Production Сервера для RISMENT

Пошаговая инструкция для Ubuntu 22.04 LTS

---

## 1. Установка и настройка MySQL 8

### Установка

```bash
# Обновить систему
sudo apt update && sudo apt upgrade -y

# Установить MySQL 8
sudo apt install mysql-server -y

# Проверить версию
mysql --version
```

### Безопасная настройка

```bash
# Запустить мастер безопасности
sudo mysql_secure_installation
```

**Ответы:**
- VALIDATE PASSWORD COMPONENT: `Y` (да)
- Password strength: `2` (STRONG)
- New root password: `ВАSH_СИЛЬНЫЙ_ПАРОЛЬ`
- Remove anonymous users: `Y`
- Disallow root login remotely: `Y`
- Remove test database: `Y`
- Reload privilege tables: `Y`

### Создание базы данных и пользователя

```bash
# Войти в MySQL
sudo mysql

# В MySQL консоли:
```

```sql
-- Создать базу данных
CREATE DATABASE risment_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Создать пользователя
CREATE USER 'risment_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';

-- Дать права
GRANT ALL PRIVILEGES ON risment_production.* TO 'risment_user'@'localhost';

-- Применить изменения
FLUSH PRIVILEGES;

-- Проверить
SHOW DATABASES;
SELECT user, host FROM mysql.user WHERE user='risment_user';

-- Выйти
EXIT;
```

### Тест подключения

```bash
# Проверить подключение
mysql -u risment_user -p risment_production

# Должно успешно войти
```

### Оптимизация MySQL для production

```bash
# Редактировать конфиг
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Добавить/изменить:

```ini
[mysqld]
# Performance
max_connections = 200
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2

# Charset
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

# Slow query log (для мониторинга)
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2
```

```bash
# Перезапустить MySQL
sudo systemctl restart mysql
sudo systemctl enable mysql
```

---

## 2. Установка и настройка Redis

### Установка

```bash
# Установить Redis
sudo apt install redis-server -y

# Проверить версию
redis-server --version
```

### Настройка безопасности

```bash
# Редактировать конфиг
sudo nano /etc/redis/redis.conf
```

**Критические изменения:**

```ini
# Слушать только localhost (безопасность)
bind 127.0.0.1 ::1

# Защита паролем (ОБЯЗАТЕЛЬНО!)
requirepass STRONG_REDIS_PASSWORD_HERE

# Максимальная память (настроить под ваш сервер)
maxmemory 512mb
maxmemory-policy allkeys-lru

# Persistence
save 900 1
save 300 10
save 60 10000

# AOF для надежности
appendonly yes
appendfsync everysec
```

### Настройка systemd для Redis

```bash
# Редактировать service файл
sudo nano /etc/systemd/system/redis.service
```

Добавить:

```ini
[Unit]
Description=Redis In-Memory Data Store
After=network.target

[Service]
User=redis
Group=redis
ExecStart=/usr/bin/redis-server /etc/redis/redis.conf
ExecStop=/usr/bin/redis-cli shutdown
Restart=always

[Install]
WantedBy=multi-user.target
```

### Запуск и автостарт

```bash
# Перезапустить Redis
sudo systemctl restart redis
sudo systemctl enable redis

# Проверить статус
sudo systemctl status redis

# Должно показать: active (running)
```

### Тест подключения

```bash
# Подключиться к Redis
redis-cli

# В Redis CLI:
AUTH STRONG_REDIS_PASSWORD_HERE
PING
# Должно ответить: PONG

SET test "Hello RISMENT"
GET test
# Должно вернуть: "Hello RISMENT"

EXIT
```

### Настройка для Laravel

Laravel использует 3 отдельные БД Redis:

```bash
# В .env укажите:
REDIS_DB=0          # Основной
REDIS_CACHE_DB=1    # Кэш
REDIS_QUEUE_DB=2    # Очереди
```

---

## 3. Настройка SMTP (Email)

У вас есть несколько вариантов:

### Вариант 1: Gmail SMTP (для тестирования)

⚠️ **Не рекомендуется для production!** Лимиты: 500 писем/день

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-specific-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Как получить App Password для Gmail:**
1. Включите 2FA на аккаунте Google
2. Перейдите: https://myaccount.google.com/apppasswords
3. Создайте App Password для "Mail"
4. Используйте этот пароль в .env

---

### Вариант 2: Mailgun (Рекомендуется)

✅ **Бесплатно:** 5,000 писем/месяц

**Регистрация:**
1. Зайдите на https://www.mailgun.com/
2. Зарегистрируйтесь
3. Подтвердите домен risment.uz
4. Получите API ключ и SMTP credentials

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@mg.risment.uz
MAIL_PASSWORD=YOUR_MAILGUN_SMTP_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@risment.uz
MAIL_FROM_NAME="RISMENT Fulfillment"
```

**DNS записи для Mailgun:**
```
Тип: TXT
Host: mg.risment.uz
Value: v=spf1 include:mailgun.org ~all

Тип: TXT
Host: k1._domainkey.mg.risment.uz
Value: (получите из Mailgun панели)

Тип: CNAME
Host: email.mg.risment.uz
Value: mailgun.org
```

---

### Вариант 3: SendGrid

✅ **Бесплатно:** 100 писем/день

**Регистрация:**
1. https://signup.sendgrid.com/
2. Создайте API Key
3. Настройте sender authentication

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=YOUR_SENDGRID_API_KEY
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@risment.uz
MAIL_FROM_NAME="RISMENT Fulfillment"
```

---

### Вариант 4: Свой SMTP сервер (Postfix)

⚠️ **Сложно!** Требует опыта с настройкой почтовых серверов

```bash
# Установка Postfix
sudo apt install postfix -y

# Выбрать: Internet Site
# System mail name: risment.uz
```

**Не рекомендуется** без опыта DevOps, так как:
- Сложная настройка SPF/DKIM/DMARC
- Риск попасть в спам
- Нужен reverse DNS
- Требует постоянного мониторинга

---

## 4. Полная настройка .env для production

```bash
# Скопировать шаблон
cd /var/www/risment
cp .env.example.production .env

# Редактировать
nano .env
```

**Заполните все переменные:**

```env
# Application
APP_NAME="RISMENT Fulfillment"
APP_ENV=production
APP_KEY=  # Будет сгенерировано позже
APP_DEBUG=false
APP_URL=https://risment.uz

# Локализация
APP_LOCALE=ru
APP_FALLBACK_LOCALE=ru
APP_TIMEZONE=Asia/Tashkent

# MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=risment_production
DB_USERNAME=risment_user
DB_PASSWORD=ВАШ_MYSQL_ПАРОЛЬ  # Из шага 1

# Redis
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=ВАШ_REDIS_ПАРОЛЬ  # Из шага 2
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_QUEUE_DB=2

# SMTP (выберите один вариант из шага 3)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@mg.risment.uz
MAIL_PASSWORD=ВАШ_MAILGUN_ПАРОЛЬ
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@risment.uz
MAIL_FROM_NAME="${APP_NAME}"
```

### Генерация APP_KEY

```bash
php artisan key:generate --ansi
```

---

## 5. Проверка подключений

### Тест базы данных

```bash
php artisan tinker

# В tinker:
DB::connection()->getPdo();
# Должно показать: PDO object

DB::select('SELECT VERSION()');
# Должно показать версию MySQL

exit
```

### Тест Redis

```bash
php artisan tinker

# В tinker:
Cache::put('test', 'Hello RISMENT', 60);
Cache::get('test');
# Должно вернуть: "Hello RISMENT"

exit
```

### Тест SMTP

Создайте тестовый маршрут:

```bash
# Временно добавьте в routes/web.php
Route::get('/test-email', function() {
    Mail::raw('Test email from RISMENT', function($message) {
        $message->to('your-email@example.com')
                ->subject('RISMENT Email Test');
    });
    return 'Email sent!';
});
```

Посетите: `https://risment.uz/test-email` (после настройки nginx)

---

## 6. Проверка безопасности

```bash
# Проверить открытые порты
sudo netstat -tuln | grep LISTEN

# Должны быть открыты только:
# 22 (SSH)
# 80 (HTTP - временно, для Let's Encrypt)
# 443 (HTTPS)
# 3306 (MySQL - только localhost)
# 6379 (Redis - только localhost)
```

### Firewall (UFW)

```bash
# Включить firewall
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# Проверить статус
sudo ufw status
```

---

## 7. Мониторинг сервисов

```bash
# Проверить все сервисы
sudo systemctl status mysql
sudo systemctl status redis
sudo systemctl status nginx
sudo systemctl status php8.4-fpm

# Все должны быть: active (running)
```

---

## Решение проблем

### MySQL не подключается

```bash
# Проверить логи
sudo tail -f /var/log/mysql/error.log

# Проверить пользователя
sudo mysql
SELECT user, host FROM mysql.user;

# Пересоздать пользователя если нужно
DROP USER 'risment_user'@'localhost';
CREATE USER 'risment_user'@'localhost' IDENTIFIED BY 'NEW_PASSWORD';
GRANT ALL ON risment_production.* TO 'risment_user'@'localhost';
FLUSH PRIVILEGES;
```

### Redis не подключается

```bash
# Проверить логи
sudo tail -f /var/log/redis/redis-server.log

# Проверить конфиг
sudo nano /etc/redis/redis.conf

# Перезапустить
sudo systemctl restart redis

# Проверить через CLI
redis-cli
AUTH your_password
PING
```

### Email не отправляется

```bash
# Проверить Laravel логи
tail -f storage/logs/laravel.log

# Проверить конфигурацию
php artisan config:clear
php artisan config:cache

# Тест через tinker
php artisan tinker
Mail::raw('test', fn($m) => $m->to('test@example.com')->subject('test'));
```

---

## Следующие шаги

После настройки DB/Redis/SMTP:

1. ✅ Запустите миграции: `php artisan migrate --force`
2. ✅ Запустите seeders: `php artisan db:seed --force`
3. ✅ Очистите кэш: `php artisan optimize`
4. ✅ Настройте nginx (следующая инструкция)
5. ✅ Установите SSL сертификат
6. ✅ Запустите deployment скрипт

---

## Полезные команды

```bash
# Проверка всех подключений Laravel
php artisan about

# Очистка всех кэшей
php artisan optimize:clear

# Перезапуск всех сервисов
sudo systemctl restart mysql redis php8.4-fpm nginx

# Мониторинг ресурсов
htop
df -h
free -m
```

**Готово!** Теперь ваш сервер настроен для production. 🚀
