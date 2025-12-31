# Master ТЗ для разработки RISMENT (Antigravity) — Laravel + MySQL + Filament

Версия: 1.0  
Дата: 2025-12-25  
Стек: **PHP 8.3+**, **Laravel 11**, **MySQL 8**, Blade + TailwindCSS + Vite  
Админка: **Filament 3**  
Локализация: **RU + UZ (латиница)**

---

## 0) Контекст и цели

### 0.1 Цель MVP
Сделать продукт из 3 частей:
1) **Публичный сайт**: услуги/тарифы/калькулятор/SLA/FAQ/документы/контакты (RU/UZ)  
2) **Кабинет клиента**: остатки + inbound заявки + FBO отгрузки + тикеты  
3) **Админка (Filament)**: CMS контента + управление тарифами + управление клиентами и заявками  

Маркетплейсы: **Uzum / Wildberries / Ozon / Yandex Market**  
Схемы: **FBO / FBS / DBS / EDBS**

### 0.2 Обязательная бизнес-логика тарифов
- Категории габарита (как у Uzum): **МГТ/СГТ/КГТ** по сумме сторон `L+W+H` (см)
  - МГТ: `<= 60`
  - СГТ: `> 60 и <= 170`
  - КГТ: `> 170`
- Логистика RISMENT для **FBS/DBS**:
  - МГТ: **5 000 сум / ед.**
  - СГТ: **8 000 сум / ед.**
  - КГТ: **20 000 сум / ед.**
  - Нестандарт: **индивидуально**
- **FBO доставка:** **50 000 сум / короб 60×40×40**
- **Pick&Pack (FBS/DBS):** **7 000 / заказ + 1 200 / доп. позиция**
- **Подготовка партии (FBO)** ступени:
  - до 100: 3 000 / шт
  - 101–300: 2 000 / шт
  - 301–700: 1 800 / шт
  - 701–1200: 1 600 / шт
  - 1201–1800: 1 400 / шт
  - 1801–2500: 1 200 / шт
  - 2501+: 1 000 / шт

---

## 1) Нефункциональные требования (обязательные)

### 1.1 Безопасность
- CSRF, XSS
- Rate limiting на формы (лиды/калькулятор)
- Пароли: bcrypt/argon2
- Разграничение прав (RBAC), клиенты видят только свои данные
- **Audit log** для админ-действий

### 1.2 Производительность
- Кеширование публичного контента (страницы, тарифы)
- Оптимизация изображений (webp + lazy-load)
- Индексы в БД для списков (inventory, inbounds, shipments)

### 1.3 SEO
- `sitemap.xml`, `robots.txt`
- meta title/description (RU/UZ)
- OG теги, canonical
- ЧПУ URL

### 1.4 Качество кода
- Laravel Pint
- PHPStan (целевой уровень 6+)
- Миграции, фабрики и сидеры для демо-данных (минимум)

---

## 2) Брендбук (Design System) — строго соблюдать

### 2.1 Цвета (Design Tokens)
- Primary: `#CB4FE4`
- Primary Hover/Active: `#8E2BC6`
- Text: `#0B0B10`
- Background: `#FFFFFF`
- Soft Background: `#F6F7FB`
- Border: `#E9ECF2`
- Muted Text: `#6B7280`

System Colors:
- Success: `#22C55E`
- Warning: `#F59E0B`
- Error: `#EF4444`
- Info: `#3B82F6`

Градиент (опционально):
- `linear-gradient(135deg, #CB4FE4 0%, #8E2BC6 100%)`

### 2.2 Шрифты
- Headings: **Manrope** (600–800)
- Body: **Inter** (400–600)

### 2.3 Типографика
- H1: 40px / 48px, 800
- H2: 32px / 40px, 700
- H3: 24px / 32px, 700
- H4: 20px / 28px, 600
- Body M: 16px / 26px, 400
- Body S: 14px / 22px, 400
- Caption: 12px / 18px, 500
- Price: 20px / 28px, 700

### 2.4 Layout
- Container max-width: 1200px
- Базовый шаг отступов: 8px
- Радиусы: cards 16px, buttons/inputs 12px
- Card shadow: `0 8px 24px rgba(11,11,16,0.08)`

### 2.5 UI компоненты
- Buttons: Primary / Secondary / Ghost / Danger
- Forms: input height 44px, phone mask `+998 (__) ___-__-__`
- Pricing tables: sticky header + фильтры
- Badges: New / In progress / Done / Issue

### 2.6 Фото/графика
Нужно подготовить (реальные фото склада предпочтительнее):
- Hero: 1600×900 (webp)
- Cards: 1200×800 (webp)
- Gallery: 1000×1000 (webp)
Обязательные фото-зоны: Главная hero, О компании (галерея), Услуги, SLA/регламенты

---

## 3) Роли и доступы (RBAC)

Роли:
- `admin`
- `manager`
- `warehouse_receiver`
- `warehouse_packer`
- `logistics`
- `client_owner`
- `client_manager`
- `client_viewer`

Правила доступа:
- Клиент может видеть/создавать только объекты своей компании (`company_id`)
- Сотрудник видит объекты компаний по назначению (в MVP можно дать менеджеру доступ ко всем, остальным — по компании или всем, но логировать действия)

---

## 4) Архитектура и маршруты

### 4.1 Локали
URL:
- `/ru/*`
- `/uz/*`
Сохранение выбранной локали в cookie.

### 4.2 Публичные маршруты (минимум)
- `/ru`, `/uz` — главная
- `/ru/services`, `/uz/services`
- `/ru/pricing`, `/uz/pricing`
- `/ru/calculator`, `/uz/calculator`
- `/ru/sla`, `/uz/sla`
- `/ru/faq`, `/uz/faq`
- `/ru/about`, `/uz/about`
- `/ru/contacts`, `/uz/contacts`
- `/ru/docs/{slug}`, `/uz/docs/{slug}`

Формы:
- `POST /lead`
- `POST /calculator/lead`

### 4.3 Кабинет клиента
- `/cabinet/login`
- `/cabinet/dashboard`
- `/cabinet/inventory`
- `/cabinet/inbounds`
- `/cabinet/shipments-fbo`
- `/cabinet/tickets`
- `/cabinet/profile`

### 4.4 Админка
- `/admin` (Filament)

---

## 5) Публичный сайт (функционал)

### 5.1 Главная
- УТП + сценарии FBO/FBS/DBS
- Преимущества (SLA, кабинет, фотофиксация)
- CTA: заявка/расчет
- Блок “Как работаем” (5–7 шагов)
- Логотипы маркетплейсов

### 5.2 Услуги
- Список услуг
- Фильтры: `marketplace` × `scheme`
- Страница услуги: описание RU/UZ, что входит, сроки, CTA

### 5.3 Тарифы
- Таблица тарифов с фильтрами: marketplace × scheme × category
- Блоки:
  - Onboarding/Management
  - Inbound
  - Packing materials
  - Pick&Pack
  - Logistics (MGT/SGT/KGT)
  - FBO Shipping (50k box)
  - Reverse/Returns
  - Extras

### 5.4 Калькулятор
Ввод:
- marketplace
- scheme (FBS/DBS/FBO)
- L/W/H (см), weight (кг)
- items_count (позиций), extra_items_count (доп позиции)
- упаковка чекбоксами
- FBO: boxes_count

Логика:
- `sum = L + W + H`
- category: MGT/SGT/KGT
- logistics: 5k/8k/20k
- pickpack: 7k + 1.2k*(extra_items_count)
- FBO shipping: 50k*boxes_count
- показать смету строками + итог

### 5.5 SLA/регламенты
- правила упаковки, маркировки, ответственность
- блок “EDBS различается по площадкам” (WB vs Uzum)

### 5.6 Формы лидов
- маска телефона +998
- rate limit
- запись в БД
- success response

---

## 6) Кабинет клиента (функционал)

### 6.1 Dashboard
- остатки (кол-во SKU/шт)
- активные заявки
- уведомления

### 6.2 Inventory
- список SKU: barcode, title, dims, weight, photo, qty_total, qty_reserved, location
- фильтры: SKU, barcode, in-stock

### 6.3 Inbounds (ASN)
- создать заявку на приемку: reference, planned_at, items (sku_id, qty_planned)
- статусы: `draft` → `submitted` → `processing` → `received` → `issue` → `closed`
- приёмка (в админке): qty_received, qty_diff, notes, photos

### 6.4 Shipments FBO
- создать поставку: marketplace, warehouse_name, planned_at, items
- статус: `draft` → `submitted` → `picking` → `packed` → `shipped` → `closed`
- расчёт стоимости по тарифам (preview)

### 6.5 Tickets
- создать тикет + переписка + вложения
- статусы: open/in_progress/closed

### 6.6 Profile
- обновление пароля
- контакты

---

## 7) Админка Filament (функционал)

### 7.1 CMS
- Pages (RU/UZ, SEO поля)
- Services (RU/UZ, scheme, marketplace)
- FAQ (RU/UZ)
- Documents (RU/UZ)

### 7.2 Pricing
- Tariff Plans
- Size Categories (MGT/SGT/KGT rules + price)
- Tariff Items (fixed/range)
- Возможность включать/выключать тарифы и планы

### 7.3 Warehouse
- Companies, Users
- SKUs
- Inventory
- Inbounds + items
- Shipments FBO + items
- Leads
- Tickets
- Audit logs

---

## 8) Модель данных (MySQL) — обязательные таблицы и поля

### 8.1 Auth/RBAC
- `users`: id, name, phone, email, password, is_active, timestamps
- `roles`: id, name
- `permissions`: id, name
- `role_user`: user_id, role_id
- `permission_role`: permission_id, role_id

### 8.2 Companies
- `companies`: id, name, inn(nullable), contact_name, phone, email, address, status, manager_user_id, timestamps
- `company_users`: company_id, user_id, role_in_company(owner/manager/viewer)

### 8.3 CMS
- `pages`: slug, title_ru, title_uz, content_ru, content_uz, meta_title_ru/uz, meta_desc_ru/uz, is_published, timestamps
- `services`: slug, scheme(enum), marketplace(enum/all), title_ru/uz, content_ru/uz, sort, is_active, timestamps
- `faqs`: question_ru/uz, answer_ru/uz, sort, is_active, timestamps
- `documents`: slug, title_ru/uz, content_ru/uz, is_published, timestamps

### 8.4 Pricing
- `tariff_plans`: name, description, is_default, is_active
- `tariff_categories`: code(inbound/storage/pack/pickpack/logistics/fbo_shipping/reverse/management), title_ru/uz
- `tariff_items`:
  - plan_id, category_id
  - marketplace(nullable: uzum/wb/ozon/yandex/all)
  - scheme(nullable: fbo/fbs/dbs/edbs/all)
  - name_ru/uz, unit(шт/заказ/короб/сутки)
  - price_type(enum: fixed/range)
  - price(decimal) OR range_from/range_to/price_per_unit
  - sort, is_active
- `size_categories`:
  - code(mgt/sgt/kgt)
  - sum_min, sum_max(nullable)
  - price(decimal)

### 8.5 Leads/Support
- `leads`: name, phone, company_name, marketplaces(json), schemes(json), comment, source_page, status, timestamps
- `tickets`: company_id, user_id, subject, status, timestamps
- `ticket_messages`: ticket_id, user_id, message, attachments(json), timestamps

### 8.6 Warehouse MVP
- `skus`: company_id, sku_code, barcode, title, dims_l, dims_w, dims_h, weight, photo_path, is_active, timestamps
- `inventory`: company_id, sku_id, qty_total, qty_reserved, location_code, timestamps
- `inbounds`: company_id, reference, planned_at, status, notes, timestamps
- `inbound_items`: inbound_id, sku_id, qty_planned, qty_received, qty_diff, notes, timestamps
- `shipments_fbo`: company_id, marketplace, warehouse_name, planned_at, status, notes, timestamps
- `shipment_items`: shipment_id, sku_id, qty, timestamps

### 8.7 Audit
- `audit_logs`: user_id, action, entity_type, entity_id, payload_json, created_at

---

## 9) Приёмочные критерии (DoD)

1) RU/UZ работает на всех публичных страницах  
2) Контент редактируется в админке (Pages/Services/FAQ/Docs)  
3) Тарифы управляются в админке, отображаются на сайте  
4) Калькулятор корректно определяет МГТ/СГТ/КГТ по L+W+H и считает 5k/8k/20k + pickpack + FBO короб  
5) Кабинет: inventory + inbound + shipments FBO + tickets работает, статусы меняются  
6) Доступы корректны: клиент видит только свою компанию  
7) Брендбук соблюден: цвета/шрифты/типографика/компоненты  
8) Формы лидов пишутся в БД, есть защита от спама (rate limit)  
9) Audit log фиксирует действия админов/сотрудников по ключевым сущностям  

---

## 10) План генерации кода Antigravity (порядок выполнения)

1) Bootstrap проекта: auth, RBAC, локали  
2) Миграции + модели + сидеры (таблицы из раздела 8)  
3) Filament: ресурсы CMS + Pricing + Companies/Users  
4) Публичные страницы: Home, Services, Pricing, Calculator, SLA, FAQ, Docs, Contacts  
5) Кабинет: Dashboard, Inventory, Inbounds, Shipments FBO, Tickets, Profile  
6) Логика калькулятора + unit tests  
7) Полировка UI по брендбуку + адаптивность  
8) Финальные проверки DoD  

---

## 11) Ассеты, которые должен дать заказчик
1) Лого SVG: logo.svg, logo-mark.svg, logo-dark.svg, logo-light.svg, favicon  
2) Фото склада/процессов (10–20 фото) по требованиям раздела 2.6  
3) Контент RU/UZ: “О нас”, “Услуги”, “SLA/регламенты”, “FAQ”, “Документы”  
4) Иконки маркетплейсов (официальные)  
