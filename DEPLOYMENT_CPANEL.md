# RISMENT - Развертывание на cPanel (Shared Hosting)

## 📋 Требования cPanel

- PHP 8.2 или выше
- MySQL 8.0+
- Composer (должен быть доступен через SSH или cPanel)
- SSH доступ (рекомендуется, но необязателен)
- Доступ к cPanel

---

## 🚀 Пошаговая инструкция

### Шаг 1: Подготовка файлов

#### Вариант A: Клонирование через SSH (если доступно)

```bash
# Подключитесь к серверу по SSH
ssh username@yourdomain.com

# Перейдите в home директорию
cd ~

# Клонируйте репозиторий
git clone https://github.com/Ilyosxon2721/risment.git

# Если репозиторий приватный, используйте токен
git clone https://USERNAME:TOKEN@github.com/Ilyosxon2721/risment.git
```

#### Вариант B: Загрузка через FTP (если нет SSH)

1. Скачайте проект на локальную машину:
   ```bash
   git clone https://github.com/Ilyosxon2721/risment.git
   cd risment
   ```

2. Используйте FileZilla или любой FTP клиент
3. Подключитесь к серверу (узнайте FTP credentials в cPanel)
4. Загрузите все файлы в `~/risment` или `~/domains/yourdomain.com/`

**⚠️ ВАЖНО:** Не загружайте папки:
- `node_modules/` 
- `vendor/`
- `.git/` (опционально)

---

### Шаг 2: Создание базы данных через cPanel

1. **Войдите в cPanel**
   - Откройте: `https://yourdomain.com:2083` или через URL хостинга

2. **MySQL Databases**
   - Найдите раздел "Databases" → "MySQL Databases"

3. **Создайте новую базу данных**
   ```
   Database Name: risment_db
   ```
   - Нажмите "Create Database"
   - **Запомните имя:** обычно будет `username_risment_db`

4. **Создайте пользователя БД**
   ```
   Username: risment_user
   Password: [Создайте сильный пароль]
   ```
   - Нажмите "Create User"
   - **Запомните:** обычно будет `username_risment_user`

5. **Привяжите пользователя к БД**
   - В разделе "Add User to Database"
   - Выберите пользователя и базу
   - Нажмите "Add"
   - Выберите "All Privileges"
   - Нажмите "Make Changes"

6. **Запомните данные:**
   ```
   DB_HOST: localhost (обычно)
   DB_DATABASE: username_risment_db
   DB_USERNAME: username_risment_user
   DB_PASSWORD: ваш_пароль
   ```

---

### Шаг 3: Настройка .env файла

#### Через File Manager:

1. В cPanel → "File Manager"
2. Перейдите в папку с проектом
3. Найдите `.env.example`
4. Щелкните правой кнопкой → "Copy" 
5. Назовите копию `.env`
6. Щелкните правой кнопкой на `.env` → "Edit"

#### Обязательные настройки:

```env
APP_NAME=RISMENT
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database (используйте данные из Шага 2!)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_risment_db
DB_USERNAME=username_risment_user
DB_PASSWORD=ваш_сильный_пароль

# Mail (настройте email для уведомлений)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="RISMENT"

# Analytics (получите ID от Google и Yandex)
GOOGLE_ANALYTICS_ID=
YANDEX_METRIKA_ID=

# Payment - Click
CLICK_MERCHANT_ID=
CLICK_SERVICE_ID=
CLICK_SECRET_KEY=

# Payment - Payme
PAYME_MERCHANT_ID=
PAYME_SECRET_KEY=

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Locale
APP_LOCALE=ru
APP_FALLBACK_LOCALE=en
```

**Сохраните файл!**

---

### Шаг 4: Установка зависимостей

#### Если есть SSH доступ:

```bash
cd ~/risment

# Установите PHP зависимости
composer install --no-dev --optimize-autoloader

# Сгенерируйте ключ приложения
php artisan key:generate

# Создайте symbolic link для storage
php artisan storage:link

# Запустите миграции
php artisan migrate --force

# Оптимизируйте для production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Если НЕТ SSH доступа:

1. **Composer dependencies:**
   - Установите зависимости ЛОКАЛЬНО на своей машине
   - Загрузите папку `vendor/` через FTP
   - ⚠️ Это может занять время из-за размера папки

2. **Генерация APP_KEY:**
   - Локально запустите: `php artisan key:generate`
   - Скопируйте ключ из `.env`
   - Вставьте в `.env` на сервере

3. **Storage link:**
   - В cPanel File Manager создайте символическую ссылку:
   - Из: `~/risment/storage/app/public`
   - В: `~/risment/public/storage`

4. **Миграции:**
   - Импортируйте SQL через phpMyAdmin (см. Шаг 5)

---

### Шаг 5: Импорт базы данных (альтернатива миграциям)

Если нет SSH для запуска `php artisan migrate`:

1. **Экспортируйте БД локально:**
   ```bash
   # На локальной машине
   php artisan migrate --env=local
   php artisan db:seed
   
   # Экспортируйте БД
   mysqldump -u root -p risment_local > risment_db.sql
   ```

2. **Импортируйте через phpMyAdmin:**
   - В cPanel → "phpMyAdmin"
   - Выберите вашу БД (`username_risment_db`)
   - Вкладка "Import"
   - Выберите `risment_db.sql`
   - Нажмите "Go"

---

### Шаг 6: Настройка Document Root

**КРИТИЧЕСКИ ВАЖНО!** Laravel требует чтобы `public/` была корневой директорией.

#### Вариант A: Через cPanel (рекомендуется)

1. **cPanel → "Domains" → "Addon Domains" или "Domains"**
2. Найдите ваш домен
3. Нажмите "Manage" или "Edit"
4. Измените "Document Root" на:
   ```
   /home/username/risment/public
   ```
   или
   ```
   /home/username/domains/yourdomain.com/risment/public
   ```
5. Сохраните изменения

#### Вариант B: Переместить файлы

Если не можете изменить Document Root:

```bash
# Через SSH
cd ~/risment
cp -r public/* ~/public_html/
cp .env ~/public_html/
cp -r storage ~/public_html/
cp -r bootstrap ~/public_html/
# И так далее...
```

Потом отредактируйте `public_html/index.php`:
```php
require __DIR__.'/vendor/autoload.php';  // Проверьте путь
$app = require_once __DIR__.'/bootstrap/app.php';  // Проверьте путь
```

---

### Шаг 7: Установка прав доступа

```bash
# Через SSH
cd ~/risment
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

Или через cPanel File Manager:
- Выберите папки `storage` и `bootstrap/cache`
- "Permissions" → установите `755`
- Отметьте "Recurse into subdirectories"

---

### Шаг 8: Настройка .htaccess

Файл `.htaccess` уже есть в `public/`, но проверьте:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

### Шаг 9: Настройка SSL (Let's Encrypt)

1. В cPanel → "Security" → "SSL/TLS Status"
2. Найдите ваш домен
3. Нажмите "Run AutoSSL"
4. Подождите 1-5 минут
5. SSL сертификат установлен!

Или:

1. "Security" → "Let's Encrypt SSL"
2. Выберите домен
3. "Issue"

---

### Шаг 10: Настройка Cron Jobs (для планировщика Laravel)

1. cPanel → "Advanced" → "Cron Jobs"
2. Добавьте новый cron:
   ```
   * * * * * cd ~/risment && php artisan schedule:run >> /dev/null 2>&1
   ```
3. Или с полным путем к PHP:
   ```
   * * * * * /usr/local/bin/php ~/risment/artisan schedule:run >> /dev/null 2>&1
   ```

**Узнать путь к PHP:**
```bash
which php
# или
whereis php
```

---

### Шаг 11: Настройка Email (если используете Gmail)

1. **Включите 2FA в Google аккаунте**
2. **Создайте App Password:**
   - Google Account → Security
   - "App passwords"
   - Создайте новый для "Mail"
3. **Используйте этот пароль в .env:**
   ```env
   MAIL_PASSWORD=ваш_app_password
   ```

---

## ✅ Проверка установки

### 1. Проверьте главную страницу
```
https://yourdomain.com
```
Должна загрузиться главная страница RISMENT.

### 2. Проверьте языки
```
https://yourdomain.com/ru
https://yourdomain.com/uz
```

### 3. Проверьте регистрацию
```
https://yourdomain.com/register
```

### 4. Проверьте калькулятор
```
https://yourdomain.com/calculator
```

### 5. Проверьте логи
В cPanel File Manager:
```
~/risment/storage/logs/laravel.log
```
Не должно быть критических ошибок.

---

## 🔧 Настройка PHP версии (если нужно)

1. **cPanel → "Select PHP Version"**
2. Выберите PHP 8.2 или выше
3. Убедитесь что включены расширения:
   - ✅ bcmath
   - ✅ ctype
   - ✅ curl
   - ✅ dom
   - ✅ fileinfo
   - ✅ json
   - ✅ mbstring
   - ✅ openssl
   - ✅ pdo
   - ✅ pdo_mysql
   - ✅ tokenizer
   - ✅ xml
   - ✅ gd
   - ✅ zip

---

## 🐛 Типичные проблемы

### Проблема: 500 Server Error

**Решение:**
1. Проверьте права на `storage/` и `bootstrap/cache/`
2. Проверьте `.env` файл (особенно DB настройки)
3. Проверьте логи: `storage/logs/laravel.log`
4. Очистите кэш через SSH:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

### Проблема: Белая страница

**Решение:**
1. Проверьте Document Root (должен быть `/public`)
2. Проверьте `.htaccess` в `public/`
3. Убедитесь что `APP_KEY` сгенерирован в `.env`

### Проблема: Database connection error

**Решение:**
1. Проверьте данные БД в `.env`
2. Используйте полное имя БД: `username_risment_db`
3. Проверьте что пользователь привязан к БД в cPanel
4. Попробуйте подключиться через phpMyAdmin

### Проблема: CSS/JS не загружаются

**Решение:**
1. Проверьте `APP_URL` в `.env`
2. Запустите: `php artisan storage:link`
3. Очистите кэш браузера
4. Проверьте `.htaccess`

### Проблема: Email не отправляются

**Решение:**
1. Проверьте MAIL настройки в `.env`
2. Используйте App Password для Gmail
3. Проверьте логи: `storage/logs/laravel.log`
4. Попробуйте отправить тестовый email:
   ```bash
   php artisan tinker
   Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
   ```

---

## 🔄 Обновление проекта

### Через SSH:
```bash
cd ~/risment
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Через FTP:
1. Скачайте обновленные файлы
2. Загрузите через FTP (кроме `.env` и `vendor/`)
3. Обновите `vendor/` если изменился `composer.json`
4. Запустите миграции или импортируйте SQL

---

## 📞 Поддержка

Если возникли проблемы:

1. Проверьте логи: `storage/logs/laravel.log`
2. Проверьте error log хостинга в cPanel
3. Откройте issue на GitHub
4. Свяжитесь с поддержкой хостинга

---

## ✅ Финальный чеклист

- [ ] База данных создана в cPanel MySQL
- [ ] Пользователь БД создан и привязан
- [ ] Файлы загружены на сервер
- [ ] `.env` файл настроен с правильными данными
- [ ] `APP_KEY` сгенерирован
- [ ] `composer install` выполнен
- [ ] Миграции запущены или SQL импортирован
- [ ] Document Root указывает на `/public`
- [ ] Права доступа установлены (755)
- [ ] SSL сертификат установлен
- [ ] Cron job для scheduler настроен
- [ ] Email настроен и работает
- [ ] Сайт открывается без ошибок
- [ ] Языки переключаются (RU/UZ)
- [ ] Регистрация/логин работает

---

**Проект готов к работе! 🎉**

Не забудьте добавить реальные ID для:
- Google Analytics
- Yandex Metrika  
- Click Payment
- Payme Payment
