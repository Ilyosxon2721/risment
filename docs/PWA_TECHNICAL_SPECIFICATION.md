# Техническое задание: PWA для платформы Risment

**Версия:** 1.0
**Дата:** 2026-03-08
**Платформа:** Risment — SaaS-система управления 3PL-фулфилментом

---

## 1. Общие сведения о проекте

### 1.1. Описание текущей системы

Risment — это веб-платформа для управления складским фулфилментом (3PL), построенная на:

- **Backend:** Laravel (PHP)
- **Frontend:** Blade-шаблоны + Alpine.js + Tailwind CSS
- **Сборка:** Vite 7
- **HTTP-клиент:** Axios (глобально)
- **Аутентификация:** Два guard'а — `web` (клиенты) и `manager` (менеджеры склада)
- **Локализация:** RU / UZ / EN
- **Платёжные шлюзы:** Click, Payme (Узбекистан)
- **Интеграции:** SellerMind, маркетплейсы (Wildberries, Ozon, Uzum, Yandex Market)
- **Админ-панель:** Filament

### 1.2. Цель PWA

Превратить существующее веб-приложение в Progressive Web App, чтобы:

- Приложение можно было установить на мобильные устройства и десктоп
- Обеспечить быструю загрузку и отзывчивый интерфейс
- Поддержать работу при нестабильном соединении (offline-first для критичных страниц)
- Реализовать push-уведомления
- Сохранить 100% функциональности текущего приложения

---

## 2. Области функциональности (Scope)

PWA должно покрывать **все** существующие разделы приложения:

### 2.1. Публичные страницы
| Страница | Маршрут | Функциональность |
|---|---|---|
| Главная (лендинг) | `/`, `/ru` | Информация о компании, CTA |
| Услуги | `/services`, `/services/{slug}` | Каталог услуг |
| Тарифы | `/pricing` | Планы подписки |
| Калькулятор стоимости | `/calculator` | Интерактивный расчёт стоимости (POST) |
| Калькулятор маркетплейсов | `/marketplace-calculator` | Расчёт стоимости для маркетплейсов |
| FAQ | `/faq` | Часто задаваемые вопросы |
| Контакты | `/contacts` | Форма обратной связи |
| О компании | `/about` | Информация |
| SLA | `/sla` | Соглашение об уровне сервиса |

### 2.2. Аутентификация
| Функция | Маршрут | Описание |
|---|---|---|
| Вход (клиент) | `/login` | Email + пароль |
| Регистрация | `/register` | Email + пароль + имя |
| Восстановление пароля | `/forgot-password` | Отправка ссылки на email |
| Сброс пароля | `/reset-password` | Новый пароль по токену |
| Верификация email | `/verify-email` | Подтверждение email |
| Вход менеджера | `/manager/login` | Отдельная форма входа |

### 2.3. Кабинет клиента (`/cabinet`)
| Раздел | Функциональность |
|---|---|
| Дашборд | Статистика, графики, калькулятор стоимости (Alpine.js) |
| Компания | CRUD компании, переключение между компаниями (fetch) |
| Товары | CRUD товаров с вариантами, изображениями, атрибутами |
| SKU | CRUD артикулов |
| Склад (Inventory) | Просмотр остатков, поиск по SKU |
| Поставки (Inbounds) | Создание/редактирование поставок с динамическим списком товаров |
| Отгрузки (Shipments) | Создание/просмотр отгрузок на маркетплейсы |
| Тикеты | Создание тикетов, переписка, вложения файлов |
| Финансы | Обзор, счета, история платежей |
| Биллинг | Начисления, счета, транзакции, баланс |
| Оплата счетов | Инициация оплаты через Click / Payme |
| Подписка | Выбор и подтверждение плана |
| Маркетплейсы | Управление API-ключами (WB, Ozon, Uzum, Yandex) |
| Интеграции | SellerMind, синхронизация товаров |
| Профиль | Редактирование профиля, смена пароля, язык |

### 2.4. Панель менеджера (`/manager`)
| Раздел | Функциональность |
|---|---|
| Дашборд | Обзор задач, статистика |
| Задачи | Создание/подтверждение задач (inbound, pickpack, delivery, storage, returns, shipping, packaging, labeling, photo, inventory) |
| Подтверждения | Подтверждение отгрузок SellerMind |
| Биллинг | Обзор начислений за месяц |
| Склад | Просмотр и корректировка остатков |
| Отгрузки | Отслеживание и обновление статусов |
| Поставки | Приёмка поставок |

### 2.5. Административная панель (Filament)
- Filament уже предоставляет свой собственный интерфейс
- PWA **не затрагивает** Filament — он останётся доступен через веб-браузер
- В навигации PWA для пользователей с ролью `admin` остаётся ссылка на `/admin`

---

## 3. Технические требования PWA

### 3.1. Web App Manifest (`manifest.webmanifest`)

```json
{
  "name": "Risment — Фулфилмент платформа",
  "short_name": "Risment",
  "description": "Управление складским фулфилментом",
  "start_url": "/cabinet/dashboard",
  "display": "standalone",
  "orientation": "portrait-primary",
  "background_color": "#FFFFFF",
  "theme_color": "#CB4FE4",
  "lang": "ru",
  "icons": [
    { "src": "/icons/icon-72x72.png", "sizes": "72x72", "type": "image/png" },
    { "src": "/icons/icon-96x96.png", "sizes": "96x96", "type": "image/png" },
    { "src": "/icons/icon-128x128.png", "sizes": "128x128", "type": "image/png" },
    { "src": "/icons/icon-144x144.png", "sizes": "144x144", "type": "image/png" },
    { "src": "/icons/icon-152x152.png", "sizes": "152x152", "type": "image/png" },
    { "src": "/icons/icon-192x192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/icon-384x384.png", "sizes": "384x384", "type": "image/png" },
    { "src": "/icons/icon-512x512.png", "sizes": "512x512", "type": "image/png", "purpose": "any maskable" }
  ],
  "screenshots": [
    { "src": "/screenshots/dashboard.png", "sizes": "1280x720", "type": "image/png", "form_factor": "wide" },
    { "src": "/screenshots/mobile-dashboard.png", "sizes": "750x1334", "type": "image/png", "form_factor": "narrow" }
  ],
  "categories": ["business", "productivity"],
  "shortcuts": [
    { "name": "Склад", "url": "/cabinet/inventory", "icon": "/icons/inventory.png" },
    { "name": "Поставки", "url": "/cabinet/inbounds", "icon": "/icons/inbounds.png" },
    { "name": "Отгрузки", "url": "/cabinet/shipments", "icon": "/icons/shipments.png" }
  ]
}
```

**Требования к иконкам:**
- Подготовить набор PNG-иконок: 72, 96, 128, 144, 152, 192, 384, 512 px
- Maskable-иконка 512×512 с safe zone (отступ 20% от краёв)
- Фавикон: favicon.ico (16, 32, 48 px), apple-touch-icon (180 px)
- Скриншоты для install prompt: широкий (1280×720) и узкий (750×1334)

### 3.2. Service Worker

Файл: `public/sw.js`

#### 3.2.1. Стратегии кэширования

| Ресурс | Стратегия | Описание |
|---|---|---|
| App Shell (HTML-разметка layout'ов) | Cache First, Network Fallback | Базовая структура интерфейса |
| CSS/JS-бандлы (Vite) | Cache First | Версионированные файлы — кэш навсегда |
| Шрифты (Manrope, Inter) | Cache First | Редко меняются |
| Иконки и статика `/icons/`, `/images/` | Cache First | Статические ресурсы |
| API-данные (GET-запросы cabinet/manager) | Network First, Cache Fallback | Актуальные данные, но с оффлайн-резервом |
| POST/PUT/DELETE запросы | Network Only + Background Sync | Сохранение в IndexedDB при офлайне |
| Изображения товаров | Stale While Revalidate | Быстрый показ + фоновое обновление |
| Внешние ресурсы (CDN Alpine.js) | Cache First | Стабильные CDN-версии |
| Страницы-ошибки | Cache First | Оффлайн-fallback страница |

#### 3.2.2. Именованные кэши

```
risment-shell-v{N}      — App Shell (layout HTML)
risment-static-v{N}     — CSS, JS, шрифты
risment-images-v{N}     — Изображения
risment-api-v{N}        — API-ответы (GET)
risment-pages-v{N}      — Кэшированные HTML-страницы
```

#### 3.2.3. Жизненный цикл

1. **Install:** Предварительный кэш App Shell + ключевых статических ресурсов
2. **Activate:** Очистка устаревших кэшей (`v{N-1}`)
3. **Fetch:** Маршрутизация запросов по стратегиям
4. **Sync:** Повторная отправка отложенных POST/PUT запросов при восстановлении сети
5. **Push:** Обработка push-уведомлений

#### 3.2.4. Предварительное кэширование (Precache)

При установке Service Worker кэшируются:
- `/offline.html` — страница-заглушка для офлайн-режима
- Скомпилированные CSS/JS-бандлы (из Vite manifest)
- Основные шрифты
- Иконки приложения
- Ключевые маршруты кабинета: `/cabinet/dashboard`, `/cabinet/inventory`

### 3.3. Offline-режим

#### 3.3.1. Полноценный офлайн (с кэшированными данными)

Следующие страницы должны работать офлайн при наличии кэша:

| Страница | Описание |
|---|---|
| Дашборд кабинета | Последние загруженные данные + интерфейс |
| Склад (Inventory) | Кэшированные остатки |
| Список товаров | Кэшированный каталог |
| Список поставок | Кэшированный список |
| Список отгрузок | Кэшированный список |
| Список тикетов | Кэшированный список |
| Финансы | Кэшированные данные |
| Профиль | Кэшированные данные пользователя |

#### 3.3.2. Offline-fallback

Для страниц без кэша — показ `/offline.html` с:
- Логотипом Risment
- Сообщением: «Нет подключения к интернету»
- Кнопка «Повторить» (перезагрузка страницы)
- Локализация на основе сохранённого языка

#### 3.3.3. Background Sync (отложенные действия)

При отсутствии сети следующие действия сохраняются в IndexedDB и выполняются при восстановлении:

| Действие | Описание |
|---|---|
| Создание тикета | Текст сохраняется, отправляется при reconnect |
| Переключение компании | Запрос откладывается |
| Обновление профиля | Изменения откладываются |
| Подтверждение задач (менеджер) | Действия откладываются |

### 3.4. Push-уведомления

#### 3.4.1. Серверная часть (Laravel)

- Использовать пакет **`laravel-notification-channels/webpush`**
- Хранение подписок в таблице `push_subscriptions`
- Генерация VAPID-ключей (`php artisan webpush:vapid`)

#### 3.4.2. Типы уведомлений

| Событие | Получатель | Текст |
|---|---|---|
| Новая поставка принята | Клиент | «Ваша поставка #{id} принята на склад» |
| Отгрузка отправлена | Клиент | «Отгрузка #{id} передана в доставку» |
| Новый счёт | Клиент | «Выставлен счёт #{id} на сумму {amount}» |
| Ответ на тикет | Клиент | «Новый ответ в тикете #{id}» |
| Новая задача | Менеджер | «Новая задача: {type} для {company}» |
| Подтверждение SellerMind | Менеджер | «Новое подтверждение отгрузки» |
| Баланс ниже порога | Клиент | «Баланс {company} ниже {threshold}» |
| Подписка истекает | Клиент | «Подписка истекает через {days} дней» |

#### 3.4.3. Клиентская часть

- Запрос разрешения на уведомления после входа в кабинет (не на публичных страницах)
- Отправка подписки на сервер: `POST /api/push-subscriptions`
- Обработка клика по уведомлению — переход на соответствующую страницу
- Бейдж на иконке приложения (Badge API) для непрочитанных уведомлений

### 3.5. Установка приложения (Install Prompt)

#### 3.5.1. Кастомный баннер установки

- Перехват события `beforeinstallprompt`
- Показ баннера внизу экрана после 2-го визита в кабинет
- Текст: «Установите Risment для быстрого доступа»
- Кнопки: «Установить» / «Позже»
- Сохранение выбора в `localStorage` (не показывать повторно 30 дней)
- Для iOS: инструкция «Нажмите "Поделиться" → "На экран Домой"»

#### 3.5.2. Мета-теги для iOS

```html
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Risment">
<link rel="apple-touch-icon" href="/icons/apple-touch-icon-180x180.png">
<link rel="apple-touch-startup-image" href="/icons/splash.png">
```

---

## 4. Адаптация интерфейса

### 4.1. Текущее состояние

Интерфейс уже использует Tailwind CSS с responsive-классами, однако требуется дополнительная адаптация для мобильных устройств в PWA-режиме.

### 4.2. Мобильная адаптация кабинета

#### 4.2.1. Боковая панель (Sidebar)

**Текущее:** Фиксированная боковая панель `w-64` всегда видна.

**PWA-адаптация:**
- На экранах < 768px: скрыть sidebar по умолчанию
- Добавить кнопку-гамбургер в верхней панели
- Sidebar открывается как overlay (slide-in слева) с Alpine.js
- Backdrop-overlay при открытом sidebar
- Жест swipe-right от левого края для открытия (touch events)
- Жест swipe-left для закрытия

#### 4.2.2. Таблицы данных

**Текущее:** Широкие HTML-таблицы могут выходить за границы экрана.

**PWA-адаптация:**
- На мобильных: горизонтальный скролл для таблиц (`overflow-x-auto`)
- Альтернативно: card-view для списков (товары, поставки, отгрузки)
- Responsive breakpoint: `< 640px` — card-view, `>= 640px` — таблица

#### 4.2.3. Формы

**Текущее:** Многие формы имеют 2-3 колонки.

**PWA-адаптация:**
- На мобильных: все формы в одну колонку
- Динамические списки товаров (Alpine.js): переход от горизонтальной раскладки к вертикальным карточкам
- Кнопки действий: полная ширина на мобильных

#### 4.2.4. Навигация

**PWA-адаптация:**
- Нижняя навигационная панель (bottom navigation bar) для основных разделов кабинета:
  - Дашборд | Склад | Поставки | Отгрузки | Ещё (выпадающее меню)
- Высота: 56px, фиксирована внизу
- Активный элемент подсвечивается цветом бренда `#CB4FE4`
- Скрывается при прокрутке вниз, появляется при прокрутке вверх
- Только в режиме `display: standalone` (PWA-режим)

#### 4.2.5. Pull-to-Refresh

- На основных страницах списков (Склад, Товары, Поставки, Отгрузки, Тикеты)
- Визуальная индикация (спиннер) при свайпе вниз
- Перезагрузка данных с сервера

### 4.3. Standalone-режим

Определение PWA-режима через CSS и JS:

```css
@media (display-mode: standalone) {
  /* PWA-specific styles */
  .pwa-bottom-nav { display: flex; }
  .web-footer { display: none; }
  body { padding-bottom: 56px; /* под bottom nav */ }
}
```

```javascript
const isPWA = window.matchMedia('(display-mode: standalone)').matches
  || window.navigator.standalone === true;
```

В standalone-режиме:
- Убрать основной футер публичного сайта (в кабинете его нет)
- Показать нижнюю навигацию
- Добавить safe area insets для iPhone с вырезом

### 4.4. Safe Area (iPhone Notch / Dynamic Island)

```css
body {
  padding-top: env(safe-area-inset-top);
  padding-bottom: env(safe-area-inset-bottom);
  padding-left: env(safe-area-inset-left);
  padding-right: env(safe-area-inset-right);
}
```

```html
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
```

---

## 5. Техническая реализация

### 5.1. Новые файлы

| Файл | Описание |
|---|---|
| `public/manifest.webmanifest` | Web App Manifest |
| `public/sw.js` | Service Worker |
| `public/offline.html` | Офлайн-страница |
| `public/icons/` | Директория с иконками PWA |
| `public/screenshots/` | Скриншоты для install prompt |
| `resources/js/pwa/register-sw.js` | Регистрация Service Worker |
| `resources/js/pwa/install-prompt.js` | Логика баннера установки |
| `resources/js/pwa/push-notifications.js` | Подписка на push-уведомления |
| `resources/js/pwa/offline-sync.js` | Background Sync логика |
| `resources/views/components/pwa-install-banner.blade.php` | Компонент баннера установки |
| `resources/views/components/bottom-nav.blade.php` | Нижняя навигация (PWA) |
| `resources/views/offline.blade.php` | Шаблон офлайн-страницы |
| `app/Notifications/PushNotification.php` | Базовый класс push-уведомлений |
| `database/migrations/xxxx_create_push_subscriptions_table.php` | Таблица подписок |

### 5.2. Изменения в существующих файлах

| Файл | Изменения |
|---|---|
| `resources/views/layouts/app.blade.php` | Добавить: `<link rel="manifest">`, мета-теги PWA, iOS мета-теги, подключение `register-sw.js` |
| `resources/views/layouts/auth.blade.php` | Добавить: `<link rel="manifest">`, мета-теги PWA |
| `resources/views/cabinet/layout.blade.php` | Мобильный sidebar (гамбургер), bottom nav, pull-to-refresh, safe area insets, подключение push-notifications |
| `resources/views/manager/layout.blade.php` | Мобильный sidebar (гамбургер), safe area insets, подключение push-notifications |
| `resources/js/app.js` | Импорт PWA-модулей (`register-sw.js`, `install-prompt.js`) |
| `resources/css/app.css` | Стили для standalone-режима, bottom nav, install banner, pull-to-refresh, safe area |
| `vite.config.js` | Генерация SW-манифеста для precache (список версионированных ассетов) |
| `routes/api.php` | Маршруты для push-подписок: `POST /api/push-subscriptions`, `DELETE /api/push-subscriptions` |
| `app/Models/User.php` | Добавить трейт `HasPushSubscriptions` |

### 5.3. Зависимости (npm / composer)

**Composer:**
```
laravel-notification-channels/webpush  — Push-уведомления через Web Push API
```

**npm:** Дополнительные пакеты не требуются. Service Worker пишется на чистом JS без фреймворков (Workbox не обязателен для данного масштаба, но может быть рассмотрен).

**Опциональные:**
```
npm: workbox-cli — для автоматической генерации precache-манифеста из Vite-билда
```

### 5.4. Интеграция Service Worker с Vite

Vite генерирует хэшированные имена файлов (`app-DaH2k9.js`). Service Worker должен знать актуальные имена:

**Подход:** При `vite build` генерировать файл `public/sw-manifest.json` со списком ассетов из `public/build/manifest.json`. Service Worker при установке загружает этот файл и кэширует все перечисленные ассеты.

```javascript
// В sw.js:
self.addEventListener('install', async (event) => {
  const manifest = await fetch('/sw-manifest.json').then(r => r.json());
  const cache = await caches.open('risment-static-v1');
  await cache.addAll(manifest.assets);
});
```

**Vite plugin** (или postbuild-скрипт): парсит `public/build/.vite/manifest.json` и создаёт `public/sw-manifest.json`.

### 5.5. CSRF-токен и Service Worker

Service Worker не должен кэшировать CSRF-токены. Стратегия:
- HTML-страницы с CSRF: **Network First** (всегда получать свежий токен)
- При офлайне: показывать кэшированную страницу с предупреждением, что формы недоступны без сети
- Background Sync: CSRF-токен запрашивается в момент фактической отправки (при восстановлении сети), через `GET /api/csrf-token`

### 5.6. Аутентификация и Service Worker

- Service Worker не кэширует cookie-сессии
- При истечении сессии: сервер вернёт 401 → Service Worker перенаправляет на `/login`
- Кэшированные страницы кабинета не содержат персональных данных пользователей других компаний (контроль через `company_id`)

---

## 6. Оплата и платёжные шлюзы

### 6.1. Click и Payme в PWA

Платёжные шлюзы (Click, Payme) работают через редирект на внешние сайты:

- **В PWA-режиме (standalone):** Оплата открывается в системном браузере (`window.open(url, '_blank')`)
- **Callback URL:** Серверный endpoint `/payment/click` и `/payment/payme` остаётся без изменений
- **Return URL:** После оплаты пользователь перенаправляется на `/cabinet/finance/invoices/{id}/payment-success` или `/payment-failed`
- **Важно:** Return URL должен содержать deep link для возврата в PWA

### 6.2. Deep Linking

Для возврата из платёжного шлюза в PWA:
- На страницах payment-success/payment-failed добавить `<a href="/cabinet/finance">` для навигации обратно в PWA
- Опционально: использование `getInstalledRelatedApps()` API для определения, установлено ли PWA

---

## 7. Многоязычность в PWA

### 7.1. Manifest по языку

Динамическая генерация манифеста на основе текущей локали:

```
GET /manifest.webmanifest?lang=ru  → name: "Risment — Фулфилмент платформа"
GET /manifest.webmanifest?lang=uz  → name: "Risment — Fulfillment platforma"
GET /manifest.webmanifest?lang=en  → name: "Risment — Fulfillment Platform"
```

Реализация через Laravel-маршрут, возвращающий JSON с правильными переводами.

### 7.2. Push-уведомления

Текст push-уведомлений формируется на сервере с учётом языка пользователя (`user.locale`).

### 7.3. Offline-страница

Offline-страница (`/offline.html`) содержит все три языка, переключение по сохранённому `localStorage.locale`.

---

## 8. Метрики и аналитика

### 8.1. Отслеживание PWA-событий

Отправлять в Google Analytics / Yandex Metrika:

| Событие | Описание |
|---|---|
| `pwa_install_prompt_shown` | Показан баннер установки |
| `pwa_install_accepted` | Пользователь установил PWA |
| `pwa_install_dismissed` | Пользователь отклонил установку |
| `pwa_launched_standalone` | Приложение запущено в standalone-режиме |
| `pwa_offline_page_shown` | Показана офлайн-страница |
| `pwa_push_subscribed` | Подписка на push-уведомления |
| `pwa_push_unsubscribed` | Отписка от push |
| `pwa_background_sync` | Выполнена отложенная синхронизация |

---

## 9. Тестирование

### 9.1. Lighthouse-аудит

PWA должно проходить Lighthouse-аудит со следующими минимальными показателями:

| Категория | Минимум |
|---|---|
| Performance | ≥ 80 |
| Accessibility | ≥ 90 |
| Best Practices | ≥ 90 |
| SEO | ≥ 90 |
| PWA | Полное прохождение (все чеклисты) |

### 9.2. Тестовые сценарии

#### Установка
- [ ] Показ баннера установки в Chrome (Android)
- [ ] Установка через баннер
- [ ] Инструкция для iOS Safari
- [ ] Установка на десктоп (Chrome, Edge)
- [ ] Запуск в standalone-режиме
- [ ] Корректные иконки на домашнем экране
- [ ] Splash screen при запуске

#### Офлайн
- [ ] Офлайн-страница при отсутствии кэша
- [ ] Дашборд работает с кэшированными данными
- [ ] Склад отображает кэшированные остатки
- [ ] Индикатор отсутствия сети в UI
- [ ] Формы заблокированы в офлайне (с уведомлением)
- [ ] Background Sync: тикет создаётся при восстановлении сети

#### Push-уведомления
- [ ] Запрос разрешения после входа
- [ ] Уведомление при новой поставке
- [ ] Уведомление при ответе на тикет
- [ ] Уведомление при выставлении счёта
- [ ] Клик по уведомлению открывает нужную страницу
- [ ] Корректная работа на Android и iOS

#### Мобильная адаптация
- [ ] Sidebar в кабинете: гамбургер + overlay на мобильных
- [ ] Bottom navigation в standalone-режиме
- [ ] Таблицы не выходят за пределы экрана
- [ ] Формы корректно отображаются на мобильных
- [ ] Safe area на iPhone с вырезом/Dynamic Island
- [ ] Pull-to-refresh на страницах списков

#### Платежи
- [ ] Click: оплата из PWA, возврат в приложение
- [ ] Payme: оплата из PWA, возврат в приложение

#### Обновление
- [ ] При обновлении SW показывается баннер «Доступна новая версия»
- [ ] Кнопка «Обновить» перезагружает приложение с новым SW

### 9.3. Устройства для тестирования

| Платформа | Устройства/Браузеры |
|---|---|
| Android | Chrome (основной), Samsung Internet, Firefox |
| iOS | Safari (основной), Chrome iOS |
| Desktop | Chrome, Edge, Firefox |

---

## 10. Этапы реализации

### Этап 1: Базовый PWA (1-2 недели)
1. Создание Web App Manifest
2. Подготовка иконок и splash-screen
3. Базовый Service Worker (precache статики)
4. Регистрация SW в основных layout'ах
5. Мета-теги PWA и iOS
6. Офлайн-страница (`/offline.html`)
7. Интеграция с Vite (sw-manifest.json)
8. Lighthouse-аудит — достижение PWA-чеклиста

### Этап 2: Мобильная адаптация (1-2 недели)
1. Мобильный sidebar с гамбургером (кабинет + менеджер)
2. Bottom navigation для standalone-режима
3. Адаптация таблиц (responsive / card-view)
4. Адаптация форм (одна колонка на мобильных)
5. Safe area insets (iPhone)
6. Standalone-режим CSS (`display-mode: standalone`)

### Этап 3: Кэширование и офлайн (1-2 недели)
1. Network First для страниц кабинета
2. Cache First для статики
3. Stale While Revalidate для изображений
4. Кэширование API-ответов (GET)
5. Индикатор состояния сети в UI
6. Баннер «Нет подключения» на формах
7. Background Sync для тикетов и профиля

### Этап 4: Push-уведомления (1 неделя)
1. Установка `laravel-notification-channels/webpush`
2. Миграция `push_subscriptions`
3. VAPID-ключи
4. Клиентский код подписки
5. Серверные уведомления (поставки, отгрузки, счета, тикеты)
6. Обработка клика по уведомлению

### Этап 5: Баннер установки и финализация (0.5-1 неделя)
1. Кастомный баннер установки
2. Инструкция для iOS
3. Аналитика PWA-событий
4. Обновление SW — баннер «Новая версия»
5. Финальное тестирование на устройствах
6. Lighthouse-аудит — финальный

---

## 11. Критерии приёмки

1. **Installable:** Приложение устанавливается на Android (Chrome), iOS (Safari), Desktop (Chrome/Edge)
2. **Offline-capable:** Кабинет работает с кэшированными данными при отсутствии сети
3. **Push-enabled:** Уведомления приходят на устройства при ключевых событиях
4. **Mobile-friendly:** Интерфейс кабинета и менеджера полностью адаптирован для мобильных
5. **Fast:** Lighthouse Performance ≥ 80, время загрузки повторных визитов < 2 сек
6. **No regression:** Все существующие функции работают без изменений в веб-браузере
7. **Secure:** HTTPS, корректная обработка CSRF, нет утечки данных через кэш

---

## 12. Ограничения и допущения

1. **Filament (админ-панель)** не входит в scope PWA — остаётся как веб-интерфейс
2. **Background Sync** ограничен простыми операциями (тикеты, профиль); создание поставок/отгрузок требует сети
3. **iOS Safari** имеет ограничения: нет Background Sync, ограниченный Push API (с iOS 16.4+), нет `beforeinstallprompt`
4. **Платёжные шлюзы** (Click, Payme) работают через внешний браузер — это нормальное поведение
5. **Загрузка файлов** (вложения тикетов, фото товаров) требует сетевого подключения
