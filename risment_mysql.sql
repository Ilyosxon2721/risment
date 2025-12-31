-- ================================================
-- RISMENT Database - MySQL Production Schema
-- Generated: 2025-12-28
-- Safe version with IF NOT EXISTS checks
-- ================================================
-- 
-- USAGE: 
-- 1. Create database if needed: CREATE DATABASE IF NOT EXISTS risment_production;
-- 2. Import this file: mysql -u root -p risment_production < risment_mysql.sql
--
-- This file is safe to run multiple times - it won't fail if tables exist
-- ================================================

CREATE DATABASE IF NOT EXISTS risment_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE risment_production;

SET FOREIGN_KEY_CHECKS=0;

-- Основные таблицы пользователей и компаний
-- ================================================

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NULL,
  `locale` VARCHAR(10) DEFAULT 'ru',
  `is_active` TINYINT(1) DEFAULT 1,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица компаний
CREATE TABLE IF NOT EXISTS `companies` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `inn` VARCHAR(50) NULL,
  `contact_name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `address` TEXT NULL,
  `status` VARCHAR(50) DEFAULT 'active',
  `manager_user_id` BIGINT UNSIGNED NULL,
  `subscription_plan_id` BIGINT UNSIGNED NULL,
  `plan_started_at` DATETIME NULL,
  `plan_status` ENUM('active', 'paused', 'cancelled') DEFAULT 'active',
  `billing_day` INT NULL,
  `balance` DECIMAL(15, 2) DEFAULT 0.00,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  KEY `companies_status_index` (`status`),
  KEY `companies_subscription_plan_id_index` (`subscription_plan_id`),
  KEY `companies_plan_status_index` (`plan_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Связь пользователей и компаний
CREATE TABLE IF NOT EXISTS `company_user` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role_in_company` ENUM('owner', 'manager', 'viewer') DEFAULT 'viewer',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `company_user_company_id_user_id_unique` (`company_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Тарифы и цены
-- ================================================

-- Подписочные планы
CREATE TABLE IF NOT EXISTS `subscription_plans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL,
  `name_ru` VARCHAR(255) NOT NULL,
  `name_uz` VARCHAR(255) NOT NULL,
  `description_ru` TEXT NULL,
  `description_uz` TEXT NULL,
  `price_month` DECIMAL(12, 2) NOT NULL,
  `is_custom` TINYINT(1) DEFAULT 0,
  `min_price_month` DECIMAL(12, 2) NULL,
  `fbs_shipments_included` INT NULL,
  `storage_included_boxes` INT NULL,
  `storage_included_bags` INT NULL,
  `inbound_included_boxes` INT NULL,
  `shipping_included` TINYINT(1) DEFAULT 0,
  `priority_processing` TINYINT(1) DEFAULT 0,
  `personal_manager` TINYINT(1) DEFAULT 0,
  `over_fbs_mgt_fee` DECIMAL(12, 2) DEFAULT 15000,
  `over_fbs_sgt_fee` DECIMAL(12, 2) DEFAULT 19000,
  `over_fbs_kgt_fee` DECIMAL(12, 2) DEFAULT 32000,
  `over_storage_box_fee` DECIMAL(12, 2) DEFAULT 18000,
  `over_storage_bag_fee` DECIMAL(12, 2) DEFAULT 12000,
  `over_inbound_box_fee` DECIMAL(12, 2) DEFAULT 15000,
  `recommended_price_month` DECIMAL(12, 2) NULL,
  `sort` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `subscription_plans_code_unique` (`code`),
  KEY `subscription_plans_code_index` (`code`),
  KEY `subscription_plans_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ценовые ставки
CREATE TABLE IF NOT EXISTS `pricing_rates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL,
  `value` DECIMAL(12, 2) NOT NULL,
  `unit_ru` VARCHAR(50) NOT NULL,
  `unit_uz` VARCHAR(50) NOT NULL,
  `description_ru` TEXT NULL,
  `description_uz` TEXT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `pricing_rates_code_unique` (`code`),
  KEY `pricing_rates_code_index` (`code`),
  KEY `pricing_rates_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Надбавки по объему
CREATE TABLE IF NOT EXISTS `surcharge_tiers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `min_shipments` INT NOT NULL,
  `max_shipments` INT NULL,
  `surcharge_percent` DECIMAL(5, 2) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `sort` INT DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  KEY `surcharge_tiers_is_active_sort_index` (`is_active`, `sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Лимиты планов
CREATE TABLE IF NOT EXISTS `plan_limits` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `plan_id` BIGINT UNSIGNED NOT NULL,
  `included_shipments` INT DEFAULT 0,
  `included_boxes` INT DEFAULT 0,
  `included_bags` INT DEFAULT 0,
  `included_inbound_boxes` INT DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  KEY `plan_limits_plan_id_index` (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Правила перерасхода
CREATE TABLE IF NOT EXISTS `overage_rules` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL,
  `scope` ENUM('plan') DEFAULT 'plan',
  `type` ENUM('shipments', 'storage_boxes', 'storage_bags', 'inbound_boxes') NOT NULL,
  `pricing_mode` ENUM('per_unit_base', 'fixed_by_category', 'fixed') NOT NULL,
  `fee_mgt` DECIMAL(12, 2) NULL,
  `fee_sgt` DECIMAL(12, 2) NULL,
  `fee_kgt` DECIMAL(12, 2) NULL,
  `fee` DECIMAL(12, 2) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `overage_rules_code_unique` (`code`),
  KEY `overage_rules_code_index` (`code`),
  KEY `overage_rules_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Связь планов и правил перерасхода
CREATE TABLE IF NOT EXISTS `plan_overage_rules` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `plan_id` BIGINT UNSIGNED NOT NULL,
  `overage_rule_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `plan_overage_rules_plan_id_overage_rule_id_unique` (`plan_id`, `overage_rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Контент и CMS
-- ================================================

-- Контент блоки
CREATE TABLE IF NOT EXISTS `content_blocks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `page_slug` VARCHAR(100) NOT NULL,
  `block_key` VARCHAR(100) NOT NULL,
  `title_ru` VARCHAR(500) NULL,
  `title_uz` VARCHAR(500) NULL,
  `body_ru` TEXT NULL,
  `body_uz` TEXT NULL,
  `sort` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `content_blocks_page_slug_block_key_unique` (`page_slug`, `block_key`),
  KEY `content_blocks_page_slug_index` (`page_slug`),
  KEY `content_blocks_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Товары (SKU)
-- ================================================

CREATE TABLE IF NOT EXISTS `skus` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `sku_code` VARCHAR(100) NOT NULL,
  `barcode` VARCHAR(100) NULL,
  `title` VARCHAR(255) NOT NULL,
  `dims_l` DECIMAL(10, 2) NULL,
  `dims_w` DECIMAL(10, 2) NULL,
  `dims_h` DECIMAL(10, 2) NULL,
  `weight` DECIMAL(10, 3) NULL,
  `photo_path` VARCHAR(255) NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `skus_company_id_sku_code_unique` (`company_id`, `sku_code`),
  KEY `skus_sku_code_index` (`sku_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Остатки
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `sku_id` BIGINT UNSIGNED NOT NULL,
  `qty_total` INT DEFAULT 0,
  `qty_reserved` INT DEFAULT 0,
  `location_code` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `inventory_company_id_sku_id_unique` (`company_id`, `sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Финансы
-- ================================================

-- Счета
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `invoice_number` VARCHAR(50) NOT NULL,
  `status` ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
  `issue_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `subtotal` DECIMAL(15, 2) NOT NULL,
  `tax` DECIMAL(15, 2) DEFAULT 0,
  `total` DECIMAL(15, 2) NOT NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `deleted_at` TIMESTAMP NULL,
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Позиции счетов
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `invoice_id` BIGINT UNSIGNED NOT NULL,
  `description` VARCHAR(500) NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(12, 2) NOT NULL,
  `total_price` DECIMAL(15, 2) NOT NULL,
  `service_type` VARCHAR(50) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Платежи
CREATE TABLE IF NOT EXISTS `payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `invoice_id` BIGINT UNSIGNED NULL,
  `amount` DECIMAL(15, 2) NOT NULL,
  `payment_date` DATE NOT NULL,
  `method` ENUM('cash', 'bank_transfer', 'card', 'payme', 'click') DEFAULT 'bank_transfer',
  `reference` VARCHAR(100) NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Аудит
-- ================================================

CREATE TABLE IF NOT EXISTS `tariff_audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `entity_type` VARCHAR(255) NOT NULL,
  `entity_id` BIGINT UNSIGNED NOT NULL,
  `before_json` TEXT NULL,
  `after_json` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL,
  KEY `tariff_audit_logs_entity_type_index` (`entity_type`),
  KEY `tariff_audit_logs_created_at_index` (`created_at`),
  KEY `tariff_audit_logs_entity_type_entity_id_index` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Laravel системные таблицы
-- ================================================

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` VARCHAR(255) PRIMARY KEY,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(255) PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache` (
  `key` VARCHAR(255) PRIMARY KEY,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` VARCHAR(255) PRIMARY KEY,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL,
  `reserved_at` INT UNSIGNED NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;

-- ================================================
-- Готово!
-- Теперь можно безопасно загрузить данные из risment_data.sql
-- ================================================
