-- ================================================
-- RISMENT Database - Initial Data (Seeders)
-- ================================================
-- Заполнение начальными данными
-- ================================================

USE risment_production;

-- Очистить существующие данные (опционально)
SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE `subscription_plans`;
TRUNCATE TABLE `pricing_rates`;
TRUNCATE TABLE `surcharge_tiers`;
TRUNCATE TABLE `content_blocks`;
SET FOREIGN_KEY_CHECKS=1;

-- ================================================
-- 1. SUBSCRIPTION PLANS (Тарифные планы)
-- ================================================

INSERT INTO `subscription_plans` 
(`code`, `name_ru`, `name_uz`, `price_month`, `is_custom`, `min_price_month`, 
`fbs_shipments_included`, `storage_included_boxes`, `storage_included_bags`, `inbound_included_boxes`,
`shipping_included`, `priority_processing`, `personal_manager`, 
`over_fbs_mgt_fee`, `over_fbs_sgt_fee`, `over_fbs_kgt_fee`,
`over_storage_box_fee`, `over_storage_bag_fee`, `over_inbound_box_fee`,
`sort`, `is_active`, `created_at`, `updated_at`)
VALUES
-- LITE Plan
('lite', 'Лайт', 'Layt', 199000.00, 0, NULL,
100, 5, 5, 5,
1, 0, 0,
11000, 15000, 27000,
18000, 12000, 15000,
1, 1, NOW(), NOW()),

-- START Plan
('start', 'Старт', 'Start', 399000.00, 0, NULL,
300, 15, 15, 15,
1, 0, 0,
11000, 15000, 27000,
18000, 12000, 15000,
2, 1, NOW(), NOW()),

-- PRO Plan
('pro', 'Про', 'Pro', 699000.00, 0, NULL,
600, 30, 30, 30,
1, 1, 0,
11000, 15000, 27000,
18000, 12000, 15000,
3, 1, NOW(), NOW()),

-- BUSINESS Plan
('business', 'Бизнес', 'Biznes', 1299000.00, 0, NULL,
1200, 60, 60, 60,
1, 1, 1,
11000, 15000, 27000,
18000, 12000, 15000,
4, 1, NOW(), NOW()),

-- ENTERPRISE Plan (Custom)
('enterprise', 'Энтерпрайз', 'Enterprayz', 1500000.00, 1, 1500000.00,
NULL, NULL, NULL, NULL,
1, 1, 1,
11000, 15000, 27000,
18000, 12000, 15000,
5, 1, NOW(), NOW());

-- ================================================
-- 2. PRICING RATES (Ценовые ставки)
-- ================================================

INSERT INTO `pricing_rates` 
(`code`, `value`, `unit_ru`, `unit_uz`, `description_ru`, `description_uz`, `is_active`, `created_at`, `updated_at`)
VALUES
-- Pick & Pack
('PICKPACK_UNIT', 7000.00, 'сум/заказ', 'so\'m/buyurtma', 
'Сборка заказа (Pick & Pack)', 'Buyurtmani yig\'ish (Pick & Pack)', 
1, NOW(), NOW()),

-- Delivery by category
('DELIVERY_MGT', 4000.00, 'сум/шт', 'so\'m/dona', 
'Доставка FBS - MGT (≤60см)', 'FBS yetkazib berish - MGT (≤60sm)', 
1, NOW(), NOW()),

('DELIVERY_SGT', 8000.00, 'сум/шт', 'so\'m/dona', 
'Доставка FBS - SGT (61-120см)', 'FBS yetkazib berish - SGT (61-120sm)', 
1, NOW(), NOW()),

('DELIVERY_KGT', 20000.00, 'сум/шт', 'so\'m/dona', 
'Доставка FBS - KGT (>120см)', 'FBS yetkazib berish - KGT (>120sm)', 
1, NOW(), NOW()),

-- Storage
('STORAGE_BOX', 18000.00, 'сум/короб/мес', 'so\'m/korob/oy', 
'Хранение коробов', 'Korob saqlash', 
1, NOW(), NOW()),

('STORAGE_BAG', 12000.00, 'сум/мешок/мес', 'so\'m/qop/oy', 
'Хранение мешков', 'Qop saqlash', 
1, NOW(), NOW()),

-- Inbound
('INBOUND_BOX', 15000.00, 'сум/короб', 'so\'m/korob', 
'Приёмка коробов', 'Korob qabul qilish', 
1, NOW(), NOW()),

-- FBO Shipping
('FBO_BOX_STANDARD', 35000.00, 'сум/короб', 'so\'m/korob', 
'FBO отправка стандартный короб 60×40×40', 'FBO standart korob jo\'natish 60×40×40', 
1, NOW(), NOW()),

-- Additional services
('PHOTO_SERVICE', 0.00, 'сум', 'so\'m', 
'Фотофиксация (включено)', 'Fotosurat (kiritilgan)', 
1, NOW(), NOW()),

('BUBBLE_WRAP', 5000.00, 'сум/шт', 'so\'m/dona', 
'Упаковка пупырчатой пленкой', 'Qabariq plyonka bilan o\'rash', 
1, NOW(), NOW());

-- ================================================
-- 3. SURCHARGE TIERS (Надбавки по объему)
-- ================================================

INSERT INTO `surcharge_tiers` 
(`min_shipments`, `max_shipments`, `surcharge_percent`, `is_active`, `sort`, `created_at`, `updated_at`)
VALUES
-- No surcharge for small volume
(0, 49, 0.00, 1, 1, NOW(), NOW()),

-- Standard surcharge
(50, 299, 10.00, 1, 2, NOW(), NOW()),

-- High volume surcharge
(300, NULL, 20.00, 1, 3, NOW(), NOW());

-- ================================================
-- 4. CONTENT BLOCKS (Контент для сайта)
-- ================================================

-- Pricing page content
INSERT INTO `content_blocks` 
(`page_slug`, `block_key`, `title_ru`, `title_uz`, `body_ru`, `body_uz`, `sort`, `is_active`, `created_at`, `updated_at`)
VALUES
-- Plan taglines
('pricing', 'plan_lite_tagline', NULL, NULL, 
'Для начинающих селлеров', 'Boshlang\'ich sotuvchilar uchun', 
1, 1, NOW(), NOW()),

('pricing', 'plan_start_tagline', NULL, NULL, 
'Оптимальный старт', 'Optimal boshlash', 
2, 1, NOW(), NOW()),

('pricing', 'plan_pro_tagline', NULL, NULL, 
'Для роста бизнеса', 'Biznesni o\'stirish uchun', 
3, 1, NOW(), NOW()),

('pricing', 'plan_business_tagline', NULL, NULL, 
'Масштабирование продаж', 'Sotuvni kengaytirish', 
4, 1, NOW(), NOW()),

('pricing', 'plan_enterprise_tagline', NULL, NULL, 
'Индивидуальные условия', 'Individual shartlar', 
5, 1, NOW(), NOW()),

-- Schedule
('pricing', 'schedule_title', 
'График доставки', 'Yetkazib berish jadvali', NULL, NULL, 
10, 1, NOW(), NOW()),

('pricing', 'schedule_body', NULL, NULL,
'FBS доставка на маркетплейсы 3 раза в неделю: понедельник, среда, пятница',
'FBS marketpleysga yetkazib berish haftada 3 marta: dushanba, chorshanba, juma',
11, 1, NOW(), NOW()),

-- Policy
('pricing', 'policy_upgrade', NULL, NULL,
'При превышении лимитов 2 месяца подряд рекомендуем переход на следующий пакет',
'Limitdan 2 oy ketma-ket oshsa, keyingi paketga o\'tish tavsiya etiladi',
12, 1, NOW(), NOW()),

-- Calculator page
('calculator', 'explanation', NULL, NULL,
'Рассчитайте точную стоимость фулфилмента для вашего бизнеса',
'O\'z biznesingiz uchun fulfilment narxini hisoblang',
20, 1, NOW(), NOW());

-- ================================================
-- ГОТОВО!
-- ================================================

-- Проверка данных
SELECT 'Subscription Plans:', COUNT(*) FROM subscription_plans;
SELECT 'Pricing Rates:', COUNT(*) FROM pricing_rates;
SELECT 'Surcharge Tiers:', COUNT(*) FROM surcharge_tiers;
SELECT 'Content Blocks:', COUNT(*) FROM content_blocks;
