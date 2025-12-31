CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "phone" varchar,
  "is_active" tinyint(1) not null default '1',
  "locale" varchar not null default 'ru'
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "permissions"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "permissions_name_guard_name_unique" on "permissions"(
  "name",
  "guard_name"
);
CREATE TABLE IF NOT EXISTS "roles"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "roles_name_guard_name_unique" on "roles"(
  "name",
  "guard_name"
);
CREATE TABLE IF NOT EXISTS "model_has_permissions"(
  "permission_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  primary key("permission_id", "model_id", "model_type")
);
CREATE INDEX "model_has_permissions_model_id_model_type_index" on "model_has_permissions"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "model_has_roles"(
  "role_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("role_id", "model_id", "model_type")
);
CREATE INDEX "model_has_roles_model_id_model_type_index" on "model_has_roles"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "role_has_permissions"(
  "permission_id" integer not null,
  "role_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("permission_id", "role_id")
);
CREATE TABLE IF NOT EXISTS "company_user"(
  "id" integer primary key autoincrement not null,
  "company_id" integer not null,
  "user_id" integer not null,
  "role_in_company" varchar check("role_in_company" in('owner', 'manager', 'viewer')) not null default 'viewer',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "company_user_company_id_user_id_unique" on "company_user"(
  "company_id",
  "user_id"
);
CREATE TABLE IF NOT EXISTS "pages"(
  "id" integer primary key autoincrement not null,
  "slug" varchar not null,
  "title_ru" varchar not null,
  "title_uz" varchar not null,
  "content_ru" text,
  "content_uz" text,
  "meta_title_ru" varchar,
  "meta_title_uz" varchar,
  "meta_description_ru" text,
  "meta_description_uz" text,
  "is_published" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "pages_slug_unique" on "pages"("slug");
CREATE TABLE IF NOT EXISTS "services"(
  "id" integer primary key autoincrement not null,
  "slug" varchar not null,
  "scheme" varchar check("scheme" in('fbo', 'fbs', 'dbs', 'edbs', 'all')) not null default 'all',
  "marketplace" varchar check("marketplace" in('uzum', 'wb', 'ozon', 'yandex', 'all')) not null default 'all',
  "title_ru" varchar not null,
  "title_uz" varchar not null,
  "content_ru" text,
  "content_uz" text,
  "sort" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "services_scheme_marketplace_is_active_index" on "services"(
  "scheme",
  "marketplace",
  "is_active"
);
CREATE UNIQUE INDEX "services_slug_unique" on "services"("slug");
CREATE TABLE IF NOT EXISTS "documents"(
  "id" integer primary key autoincrement not null,
  "slug" varchar not null,
  "title_ru" varchar not null,
  "title_uz" varchar not null,
  "content_ru" text,
  "content_uz" text,
  "file_path" varchar,
  "is_published" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "documents_slug_unique" on "documents"("slug");
CREATE TABLE IF NOT EXISTS "faqs"(
  "id" integer primary key autoincrement not null,
  "question_ru" text not null,
  "question_uz" text not null,
  "answer_ru" text not null,
  "answer_uz" text not null,
  "sort" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "tariff_categories"(
  "id" integer primary key autoincrement not null,
  "code" varchar check("code" in('onboarding', 'inbound', 'storage', 'packing', 'pickpack', 'logistics', 'fbo_shipping', 'reverse', 'extras')) not null,
  "title_ru" varchar not null,
  "title_uz" varchar not null,
  "sort" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "tariff_categories_code_unique" on "tariff_categories"(
  "code"
);
CREATE TABLE IF NOT EXISTS "tariff_items"(
  "id" integer primary key autoincrement not null,
  "plan_id" integer not null,
  "category_id" integer not null,
  "marketplace" varchar check("marketplace" in('uzum', 'wb', 'ozon', 'yandex', 'all')),
  "scheme" varchar check("scheme" in('fbo', 'fbs', 'dbs', 'edbs', 'all')),
  "name_ru" varchar not null,
  "name_uz" varchar not null,
  "unit" varchar check("unit" in('шт', 'заказ', 'короб', 'сутки', 'месяц')) not null,
  "price_type" varchar check("price_type" in('fixed', 'range')) not null default 'fixed',
  "price" numeric,
  "range_from" integer,
  "range_to" integer,
  "price_per_unit" numeric,
  "sort" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("plan_id") references "tariff_plans"("id") on delete cascade,
  foreign key("category_id") references "tariff_categories"("id") on delete cascade
);
CREATE INDEX "tariff_items_plan_id_category_id_is_active_index" on "tariff_items"(
  "plan_id",
  "category_id",
  "is_active"
);
CREATE TABLE IF NOT EXISTS "tariff_plans"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "is_default" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "size_categories"(
  "id" integer primary key autoincrement not null,
  "code" varchar check("code" in('mgt', 'sgt', 'kgt')) not null,
  "sum_min" integer not null,
  "sum_max" integer,
  "price" numeric not null,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "size_categories_code_unique" on "size_categories"("code");
CREATE TABLE IF NOT EXISTS "leads"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "phone" varchar not null,
  "company_name" varchar,
  "marketplaces" text,
  "schemes" text,
  "comment" text,
  "source_page" varchar,
  "status" varchar check("status" in('new', 'contacted', 'converted', 'rejected')) not null default 'new',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "leads_status_created_at_index" on "leads"(
  "status",
  "created_at"
);
CREATE TABLE IF NOT EXISTS "ticket_messages"(
  "id" integer primary key autoincrement not null,
  "ticket_id" integer not null,
  "user_id" integer not null,
  "message" text not null,
  "attachments" text,
  "is_internal" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("ticket_id") references "tickets"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "ticket_messages_ticket_id_index" on "ticket_messages"(
  "ticket_id"
);
CREATE TABLE IF NOT EXISTS "tickets"(
  "id" integer primary key autoincrement not null,
  "company_id" integer not null,
  "user_id" integer not null,
  "subject" varchar not null,
  "status" varchar check("status" in('open', 'in_progress', 'closed')) not null default 'open',
  "priority" varchar check("priority" in('low', 'medium', 'high')) not null default 'medium',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "tickets_company_id_status_index" on "tickets"(
  "company_id",
  "status"
);
CREATE TABLE IF NOT EXISTS "inbound_items"(
  "id" integer primary key autoincrement not null,
  "inbound_id" integer not null,
  "sku_id" integer not null,
  "qty_planned" integer not null default '0',
  "qty_received" integer,
  "qty_diff" integer,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("inbound_id") references "inbounds"("id") on delete cascade,
  foreign key("sku_id") references "skus"("id") on delete cascade
);
CREATE INDEX "inbound_items_inbound_id_index" on "inbound_items"("inbound_id");
CREATE TABLE IF NOT EXISTS "inbounds"(
  "id" integer primary key autoincrement not null,
  "company_id" integer not null,
  "reference" varchar not null,
  "planned_at" datetime,
  "status" varchar check("status" in('draft', 'submitted', 'processing', 'received', 'issue', 'closed')) not null default 'draft',
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade
);
CREATE INDEX "inbounds_company_id_status_index" on "inbounds"(
  "company_id",
  "status"
);
CREATE TABLE IF NOT EXISTS "inventory"(
  "id" integer primary key autoincrement not null,
  "company_id" integer not null,
  "sku_id" integer not null,
  "qty_total" integer not null default '0',
  "qty_reserved" integer not null default '0',
  "location_code" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade,
  foreign key("sku_id") references "skus"("id") on delete cascade
);
CREATE UNIQUE INDEX "inventory_company_id_sku_id_unique" on "inventory"(
  "company_id",
  "sku_id"
);
CREATE TABLE IF NOT EXISTS "skus"(
  "id" integer primary key autoincrement not null,
  "company_id" integer not null,
  "sku_code" varchar not null,
  "barcode" varchar,
  "title" varchar not null,
  "dims_l" numeric,
  "dims_w" numeric,
  "dims_h" numeric,
  "weight" numeric,
  "photo_path" varchar,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade
);
CREATE UNIQUE INDEX "skus_company_id_sku_code_unique" on "skus"(
  "company_id",
  "sku_code"
);
CREATE INDEX "skus_sku_code_index" on "skus"("sku_code");
CREATE TABLE IF NOT EXISTS "audit_logs"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "action" varchar not null,
  "entity_type" varchar,
  "entity_id" integer,
  "payload" text,
  "ip_address" varchar,
  "user_agent" text,
  "created_at" datetime not null default CURRENT_TIMESTAMP,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE INDEX "audit_logs_entity_type_entity_id_index" on "audit_logs"(
  "entity_type",
  "entity_id"
);
CREATE INDEX "audit_logs_created_at_index" on "audit_logs"("created_at");
CREATE TABLE IF NOT EXISTS "shipment_items"(
  "id" integer primary key autoincrement not null,
  "shipment_id" integer not null,
  "sku_id" integer not null,
  "qty" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("shipment_id") references "shipments_fbo"("id") on delete cascade,
  foreign key("sku_id") references "skus"("id") on delete cascade
);
CREATE INDEX "shipment_items_shipment_id_index" on "shipment_items"(
  "shipment_id"
);
CREATE TABLE IF NOT EXISTS "shipments_fbo"(
  "id" integer primary key autoincrement not null,
  "company_id" integer not null,
  "marketplace" varchar check("marketplace" in('uzum', 'wb', 'ozon', 'yandex')) not null,
  "warehouse_name" varchar not null,
  "planned_at" datetime,
  "status" varchar check("status" in('draft', 'submitted', 'picking', 'packed', 'shipped', 'closed')) not null default 'draft',
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade
);
CREATE INDEX "shipments_fbo_company_id_status_index" on "shipments_fbo"(
  "company_id",
  "status"
);
CREATE TABLE IF NOT EXISTS "companies"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "inn" varchar,
  "contact_name" varchar not null,
  "phone" varchar not null,
  "email" varchar not null,
  "address" text,
  "status" varchar not null default('active'),
  "manager_user_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "subscription_plan_id" integer,
  "plan_started_at" datetime,
  "plan_status" varchar check("plan_status" in('active', 'paused', 'cancelled')) not null default 'active',
  "billing_day" integer,
  "balance" numeric not null default '0',
  foreign key("manager_user_id") references users("id") on delete set null on update no action,
  foreign key("subscription_plan_id") references "subscription_plans"("id") on delete set null
);
CREATE INDEX "companies_status_index" on "companies"("status");
CREATE INDEX "companies_subscription_plan_id_index" on "companies"(
  "subscription_plan_id"
);
CREATE INDEX "companies_plan_status_index" on "companies"("plan_status");
CREATE TABLE IF NOT EXISTS "monthly_usages"(
  "id" integer primary key autoincrement not null,
  "company_id" integer not null,
  "month" varchar not null,
  "fbs_shipments_count" integer not null default '0',
  "inbound_boxes_count" integer not null default '0',
  "storage_boxes_peak" integer not null default '0',
  "storage_bags_peak" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade
);
CREATE UNIQUE INDEX "monthly_usages_company_id_month_unique" on "monthly_usages"(
  "company_id",
  "month"
);
CREATE INDEX "monthly_usages_month_index" on "monthly_usages"("month");
CREATE TABLE IF NOT EXISTS "content_blocks"(
  "id" integer primary key autoincrement not null,
  "page_slug" varchar not null,
  "block_key" varchar not null,
  "title_ru" varchar,
  "title_uz" varchar,
  "body_ru" text,
  "body_uz" text,
  "sort" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "content_blocks_page_slug_block_key_unique" on "content_blocks"(
  "page_slug",
  "block_key"
);
CREATE INDEX "content_blocks_page_slug_index" on "content_blocks"("page_slug");
CREATE INDEX "content_blocks_is_active_index" on "content_blocks"("is_active");
CREATE TABLE IF NOT EXISTS "marketplace_services"(
  "id" integer primary key autoincrement not null,
  "service_group" varchar check("service_group" in('launch', 'management', 'ads_addon', 'infographics')) not null,
  "marketplace" varchar check("marketplace" in('uzum', 'wildberries', 'ozon', 'yandex', 'all')),
  "code" varchar not null,
  "name_ru" varchar not null,
  "name_uz" varchar not null,
  "description_ru" text,
  "description_uz" text,
  "unit_ru" varchar not null,
  "unit_uz" varchar not null,
  "price" numeric not null,
  "sku_limit" integer,
  "sort" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "marketplace_services_service_group_index" on "marketplace_services"(
  "service_group"
);
CREATE INDEX "marketplace_services_marketplace_index" on "marketplace_services"(
  "marketplace"
);
CREATE INDEX "marketplace_services_is_active_index" on "marketplace_services"(
  "is_active"
);
CREATE INDEX "marketplace_services_service_group_marketplace_is_active_index" on "marketplace_services"(
  "service_group",
  "marketplace",
  "is_active"
);
CREATE UNIQUE INDEX "marketplace_services_code_unique" on "marketplace_services"(
  "code"
);
CREATE TABLE IF NOT EXISTS "bundle_discounts"(
  "id" integer primary key autoincrement not null,
  "type" varchar check("type" in('management')) not null,
  "marketplaces_count" integer not null,
  "discount_percent" numeric not null,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "bundle_discounts_type_marketplaces_count_unique" on "bundle_discounts"(
  "type",
  "marketplaces_count"
);
CREATE INDEX "bundle_discounts_is_active_index" on "bundle_discounts"(
  "is_active"
);
CREATE TABLE IF NOT EXISTS "subscription_plans"(
  "id" integer primary key autoincrement not null,
  "code" varchar not null,
  "name_ru" varchar not null,
  "name_uz" varchar not null,
  "description_ru" text,
  "description_uz" text,
  "price_month" numeric not null,
  "is_custom" tinyint(1) not null default('0'),
  "min_price_month" numeric,
  "fbs_shipments_included" integer,
  "storage_included_boxes" integer,
  "storage_included_bags" integer,
  "inbound_included_boxes" integer,
  "shipping_included" tinyint(1) not null default('0'),
  "schedule_ru" varchar,
  "schedule_uz" varchar,
  "priority_processing" tinyint(1) not null default('0'),
  "sla_high" tinyint(1) not null default('0'),
  "personal_manager" tinyint(1) not null default('0'),
  "over_fbs_shipment_fee" numeric,
  "over_storage_box_fee" numeric not null default('18000'),
  "over_storage_bag_fee" numeric not null default('12000'),
  "over_inbound_box_fee" numeric not null default('15000'),
  "sort" integer not null default('0'),
  "is_active" tinyint(1) not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "over_fbs_mgt_fee" numeric not null default '15000',
  "over_fbs_sgt_fee" numeric not null default '19000',
  "over_fbs_kgt_fee" numeric not null default '32000',
  "recommended_price_month" numeric
);
CREATE INDEX "subscription_plans_code_index" on "subscription_plans"("code");
CREATE UNIQUE INDEX "subscription_plans_code_unique" on "subscription_plans"(
  "code"
);
CREATE INDEX "subscription_plans_is_active_index" on "subscription_plans"(
  "is_active"
);
CREATE TABLE IF NOT EXISTS "company_settings"(
  "id" integer primary key autoincrement not null,
  "company_logo" varchar,
  "company_name" varchar not null default 'RISMENT',
  "phone" varchar,
  "email" varchar,
  "address_ru" text,
  "address_uz" text,
  "warehouse_address_ru" text,
  "warehouse_address_uz" text,
  "social_facebook" varchar,
  "social_instagram" varchar,
  "social_telegram" varchar,
  "stat_orders" varchar not null default '10 000+',
  "stat_sla" varchar not null default '99%',
  "stat_support" varchar not null default '24/7',
  "stat_warehouse_size" varchar not null default '5 000+',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "marketplace_logos"(
  "id" integer primary key autoincrement not null,
  "marketplace_code" varchar not null,
  "name_ru" varchar not null,
  "name_uz" varchar not null,
  "logo_image" varchar,
  "is_active" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "marketplace_logos_marketplace_code_unique" on "marketplace_logos"(
  "marketplace_code"
);
CREATE TABLE IF NOT EXISTS "testimonials"(
  "id" integer primary key autoincrement not null,
  "author_name" varchar not null,
  "category_ru" varchar not null,
  "category_uz" varchar not null,
  "text_ru" text not null,
  "text_uz" text not null,
  "rating" integer not null default '5',
  "is_active" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "faq_items"(
  "id" integer primary key autoincrement not null,
  "question_ru" text not null,
  "question_uz" text not null,
  "answer_ru" text not null,
  "answer_uz" text not null,
  "category" varchar,
  "is_active" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "invoice_items"(
  "id" integer primary key autoincrement not null,
  "invoice_id" integer not null,
  "description" varchar not null,
  "quantity" integer not null,
  "unit_price" numeric not null,
  "total_price" numeric not null,
  "service_type" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("invoice_id") references "invoices"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "invoices"(
  "id" integer primary key autoincrement not null,
  "company_id" integer not null,
  "invoice_number" varchar not null,
  "status" varchar check("status" in('draft', 'sent', 'paid', 'overdue', 'cancelled')) not null default 'draft',
  "issue_date" date not null,
  "due_date" date not null,
  "subtotal" numeric not null,
  "tax" numeric not null default '0',
  "total" numeric not null,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade
);
CREATE UNIQUE INDEX "invoices_invoice_number_unique" on "invoices"(
  "invoice_number"
);
CREATE TABLE IF NOT EXISTS "payments"(
  "id" integer primary key autoincrement not null,
  "company_id" integer not null,
  "invoice_id" integer,
  "amount" numeric not null,
  "payment_date" date not null,
  "method" varchar check("method" in('cash', 'bank_transfer', 'card', 'payme', 'click')) not null default 'bank_transfer',
  "reference" varchar,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade,
  foreign key("invoice_id") references "invoices"("id") on delete set null
);
CREATE INDEX "users_email_index" on "users"("email");
CREATE TABLE IF NOT EXISTS "pricing_rates"(
  "id" integer primary key autoincrement not null,
  "code" varchar not null,
  "value" numeric not null,
  "unit_ru" varchar not null,
  "unit_uz" varchar not null,
  "description_ru" text,
  "description_uz" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "pricing_rates_code_index" on "pricing_rates"("code");
CREATE INDEX "pricing_rates_is_active_index" on "pricing_rates"("is_active");
CREATE UNIQUE INDEX "pricing_rates_code_unique" on "pricing_rates"("code");
CREATE TABLE IF NOT EXISTS "plan_limits"(
  "id" integer primary key autoincrement not null,
  "plan_id" integer not null,
  "included_shipments" integer not null default '0',
  "included_boxes" integer not null default '0',
  "included_bags" integer not null default '0',
  "included_inbound_boxes" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("plan_id") references "subscription_plans"("id") on delete cascade
);
CREATE INDEX "plan_limits_plan_id_index" on "plan_limits"("plan_id");
CREATE TABLE IF NOT EXISTS "overage_rules"(
  "id" integer primary key autoincrement not null,
  "code" varchar not null,
  "scope" varchar check("scope" in('plan')) not null default 'plan',
  "type" varchar check("type" in('shipments', 'storage_boxes', 'storage_bags', 'inbound_boxes')) not null,
  "pricing_mode" varchar check("pricing_mode" in('per_unit_base', 'fixed_by_category', 'fixed')) not null,
  "fee_mgt" numeric,
  "fee_sgt" numeric,
  "fee_kgt" numeric,
  "fee" numeric,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "overage_rules_code_index" on "overage_rules"("code");
CREATE INDEX "overage_rules_type_index" on "overage_rules"("type");
CREATE UNIQUE INDEX "overage_rules_code_unique" on "overage_rules"("code");
CREATE TABLE IF NOT EXISTS "plan_overage_rules"(
  "id" integer primary key autoincrement not null,
  "plan_id" integer not null,
  "overage_rule_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("plan_id") references "subscription_plans"("id") on delete cascade,
  foreign key("overage_rule_id") references "overage_rules"("id") on delete cascade
);
CREATE UNIQUE INDEX "plan_overage_rules_plan_id_overage_rule_id_unique" on "plan_overage_rules"(
  "plan_id",
  "overage_rule_id"
);
CREATE TABLE IF NOT EXISTS "surcharge_tiers"(
  "id" integer primary key autoincrement not null,
  "min_shipments" integer not null,
  "max_shipments" integer,
  "surcharge_percent" numeric not null,
  "is_active" tinyint(1) not null default '1',
  "sort" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "surcharge_tiers_is_active_sort_index" on "surcharge_tiers"(
  "is_active",
  "sort"
);
CREATE TABLE IF NOT EXISTS "tariff_audit_logs"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "entity_type" varchar not null,
  "entity_id" integer not null,
  "before_json" text,
  "after_json" text not null,
  "created_at" datetime not null,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE INDEX "tariff_audit_logs_entity_type_index" on "tariff_audit_logs"(
  "entity_type"
);
CREATE INDEX "tariff_audit_logs_created_at_index" on "tariff_audit_logs"(
  "created_at"
);
CREATE INDEX "tariff_audit_logs_entity_type_entity_id_index" on "tariff_audit_logs"(
  "entity_type",
  "entity_id"
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2025_12_25_164551_add_fields_to_users_table',1);
INSERT INTO migrations VALUES(5,'2025_12_25_164552_create_permission_tables',1);
INSERT INTO migrations VALUES(6,'2025_12_25_164553_create_companies_table',1);
INSERT INTO migrations VALUES(7,'2025_12_25_164553_create_company_user_table',1);
INSERT INTO migrations VALUES(8,'2025_12_25_164553_create_pages_table',1);
INSERT INTO migrations VALUES(9,'2025_12_25_164553_create_services_table',1);
INSERT INTO migrations VALUES(10,'2025_12_25_164601_create_documents_table',1);
INSERT INTO migrations VALUES(11,'2025_12_25_164601_create_faqs_table',1);
INSERT INTO migrations VALUES(12,'2025_12_25_164602_create_tariff_categories_table',1);
INSERT INTO migrations VALUES(13,'2025_12_25_164602_create_tariff_items_table',1);
INSERT INTO migrations VALUES(14,'2025_12_25_164602_create_tariff_plans_table',1);
INSERT INTO migrations VALUES(15,'2025_12_25_164603_create_size_categories_table',1);
INSERT INTO migrations VALUES(16,'2025_12_25_164604_create_leads_table',1);
INSERT INTO migrations VALUES(17,'2025_12_25_164604_create_ticket_messages_table',1);
INSERT INTO migrations VALUES(18,'2025_12_25_164604_create_tickets_table',1);
INSERT INTO migrations VALUES(19,'2025_12_25_164605_create_inbound_items_table',1);
INSERT INTO migrations VALUES(20,'2025_12_25_164605_create_inbounds_table',1);
INSERT INTO migrations VALUES(21,'2025_12_25_164605_create_inventory_table',1);
INSERT INTO migrations VALUES(22,'2025_12_25_164605_create_skus_table',1);
INSERT INTO migrations VALUES(23,'2025_12_25_164606_create_audit_logs_table',1);
INSERT INTO migrations VALUES(24,'2025_12_25_164606_create_shipment_items_table',1);
INSERT INTO migrations VALUES(25,'2025_12_25_164606_create_shipments_fbo_table',1);
INSERT INTO migrations VALUES(26,'2025_12_25_185009_create_subscription_plans_table',2);
INSERT INTO migrations VALUES(27,'2025_12_25_185010_add_subscription_to_companies_table',2);
INSERT INTO migrations VALUES(28,'2025_12_25_185011_create_monthly_usage_table',2);
INSERT INTO migrations VALUES(29,'2025_12_25_185239_create_content_blocks_table',3);
INSERT INTO migrations VALUES(30,'2025_12_25_223249_create_marketplace_services_table',4);
INSERT INTO migrations VALUES(31,'2025_12_26_090623_create_bundle_discounts_table',5);
INSERT INTO migrations VALUES(33,'2025_12_26_140349_add_category_fbs_fees_to_subscription_plans_table',6);
INSERT INTO migrations VALUES(34,'2025_12_26_175336_create_company_settings_table',7);
INSERT INTO migrations VALUES(35,'2025_12_26_175337_create_marketplace_logos_table',7);
INSERT INTO migrations VALUES(36,'2025_12_26_175338_create_testimonials_table',7);
INSERT INTO migrations VALUES(37,'2025_12_26_175339_create_faq_items_table',7);
INSERT INTO migrations VALUES(38,'2025_12_27_131431_create_invoice_items_table',8);
INSERT INTO migrations VALUES(39,'2025_12_27_131431_create_invoices_table',8);
INSERT INTO migrations VALUES(40,'2025_12_27_131432_create_payments_table',8);
INSERT INTO migrations VALUES(44,'2025_12_27_131433_add_balance_to_companies_table',9);
INSERT INTO migrations VALUES(45,'2025_12_28_082508_add_locale_to_users_table',10);
INSERT INTO migrations VALUES(46,'2025_12_28_084713_add_recommended_price_to_subscription_plans_table',11);
INSERT INTO migrations VALUES(47,'2025_12_28_085737_create_pricing_rates_table',12);
INSERT INTO migrations VALUES(48,'2025_12_28_085738_create_plan_limits_table',12);
INSERT INTO migrations VALUES(49,'2025_12_28_085739_create_overage_rules_table',12);
INSERT INTO migrations VALUES(50,'2025_12_28_085740_create_plan_overage_rules_table',12);
INSERT INTO migrations VALUES(51,'2025_12_28_085742_create_surcharge_tiers_table',13);
INSERT INTO migrations VALUES(52,'2025_12_28_085742_create_tariff_audit_logs_table',13);
INSERT INTO migrations VALUES(53,'2025_12_28_111741_update_plan_display_names',14);
INSERT INTO migrations VALUES(54,'2025_12_28_111759_update_plan_display_names',14);
