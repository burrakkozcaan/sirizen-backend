CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "phone" varchar,
  "avatar" varchar,
  "email_verified_at" datetime,
  "email_verified" tinyint(1) not null default '0',
  "password" varchar not null,
  "two_factor_secret" varchar,
  "two_factor_enabled" tinyint(1) not null default '0',
  "last_login_at" datetime,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "role" varchar not null default 'customer',
  "two_factor_recovery_codes" text,
  "two_factor_confirmed_at" datetime,
  "google_id" varchar,
  "apple_id" varchar,
  "email_verification_code" varchar,
  "email_verification_code_expires_at" datetime
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
CREATE TABLE IF NOT EXISTS "vendor_tiers"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "min_total_orders" integer not null default '0',
  "min_rating" numeric not null default '0',
  "max_cancel_rate" numeric not null default '100',
  "max_return_rate" numeric not null default '100',
  "priority_boost" integer not null default '0',
  "badge_icon" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "commission_rate" numeric,
  "max_products" integer,
  "description" text
);
CREATE UNIQUE INDEX "vendor_tiers_name_unique" on "vendor_tiers"("name");
CREATE TABLE IF NOT EXISTS "vendor_scores"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "total_score" numeric not null default '0',
  "delivery_score" numeric not null default '0',
  "rating_score" numeric not null default '0',
  "stock_score" numeric not null default '0',
  "support_score" numeric not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE UNIQUE INDEX "vendor_scores_vendor_id_unique" on "vendor_scores"(
  "vendor_id"
);
CREATE TABLE IF NOT EXISTS "vendor_penalties"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "reason" text not null,
  "penalty_points" integer not null,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "product_attributes"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "key" varchar not null,
  "value" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "product_attributes_product_id_key_index" on "product_attributes"(
  "product_id",
  "key"
);
CREATE TABLE IF NOT EXISTS "product_images"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "url" varchar not null,
  "is_main" tinyint(1) not null default '0',
  "order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "alt" varchar,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "product_images_product_id_is_main_index" on "product_images"(
  "product_id",
  "is_main"
);
CREATE TABLE IF NOT EXISTS "product_variants"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "sku" varchar not null,
  "color" varchar,
  "size" varchar,
  "stock" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "price" numeric,
  "weight" numeric,
  "sale_price" numeric,
  "original_price" numeric,
  "is_default" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "value" varchar,
  "barcode" varchar,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "product_variants_product_id_stock_index" on "product_variants"(
  "product_id",
  "stock"
);
CREATE UNIQUE INDEX "product_variants_sku_unique" on "product_variants"("sku");
CREATE TABLE IF NOT EXISTS "seller_reviews"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "user_id" integer not null,
  "delivery_rating" integer not null,
  "communication_rating" integer not null,
  "packaging_rating" integer not null,
  "comment" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "carts"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "carts_user_id_unique" on "carts"("user_id");
CREATE TABLE IF NOT EXISTS "favorites"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "product_id" integer not null,
  "created_at" datetime not null default CURRENT_TIMESTAMP,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE UNIQUE INDEX "favorites_user_id_product_id_unique" on "favorites"(
  "user_id",
  "product_id"
);
CREATE TABLE IF NOT EXISTS "product_campaigns"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "campaign_id" integer not null,
  foreign key("product_id") references "products"("id") on delete cascade,
  foreign key("campaign_id") references "campaigns"("id") on delete cascade
);
CREATE UNIQUE INDEX "product_campaigns_product_id_campaign_id_unique" on "product_campaigns"(
  "product_id",
  "campaign_id"
);
CREATE TABLE IF NOT EXISTS "recently_vieweds"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "product_id" integer not null,
  "viewed_at" datetime not null default CURRENT_TIMESTAMP,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE UNIQUE INDEX "recently_vieweds_user_id_product_id_unique" on "recently_vieweds"(
  "user_id",
  "product_id"
);
CREATE INDEX "recently_vieweds_user_id_viewed_at_index" on "recently_vieweds"(
  "user_id",
  "viewed_at"
);
CREATE TABLE IF NOT EXISTS "search_logs"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "query" varchar not null,
  "results_count" integer not null default '0',
  "created_at" datetime not null default CURRENT_TIMESTAMP,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE INDEX "search_logs_query_created_at_index" on "search_logs"(
  "query",
  "created_at"
);
CREATE TABLE IF NOT EXISTS "payments"(
  "id" integer primary key autoincrement not null,
  "order_id" integer not null,
  "user_id" integer not null,
  "amount" numeric not null,
  "payment_provider" varchar not null,
  "payment_type" varchar not null,
  "status" varchar not null,
  "transaction_id" varchar,
  "paid_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "commission_amount" numeric not null default '0',
  "vendor_amount" numeric not null default '0',
  "platform_amount" numeric not null default '0',
  "split_status" varchar not null default 'pending',
  "gateway" varchar,
  "method" varchar,
  "gateway_response" text,
  "error_message" text,
  "installment" integer not null default '1',
  "checkout_token" varchar,
  "callback_status" varchar,
  "callback_received_at" datetime,
  "refund_id" varchar,
  "refunded_amount" numeric,
  "refunded_at" datetime,
  "installment_count" integer,
  "currency" varchar not null default 'TRY',
  "payment_method" varchar,
  "metadata" text,
  foreign key("order_id") references "orders"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "payments_order_id_status_index" on "payments"(
  "order_id",
  "status"
);
CREATE UNIQUE INDEX "payments_transaction_id_unique" on "payments"(
  "transaction_id"
);
CREATE TABLE IF NOT EXISTS "refunds"(
  "id" integer primary key autoincrement not null,
  "order_item_id" integer not null,
  "user_id" integer not null,
  "vendor_id" integer not null,
  "reason" text not null,
  "status" varchar not null,
  "refund_amount" numeric not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("order_item_id") references "order_items"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE INDEX "refunds_order_item_id_status_index" on "refunds"(
  "order_item_id",
  "status"
);
CREATE TABLE IF NOT EXISTS "vendor_balances"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "balance" numeric not null default '0',
  "pending_balance" numeric not null default '0',
  "last_settlement_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "available_balance" numeric not null default '0',
  "total_earnings" numeric not null default '0',
  "total_withdrawn" numeric not null default '0',
  "currency" varchar not null default 'TRY',
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE UNIQUE INDEX "vendor_balances_vendor_id_unique" on "vendor_balances"(
  "vendor_id"
);
CREATE TABLE IF NOT EXISTS "vendor_payouts"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "amount" numeric not null,
  "payout_method" varchar not null,
  "status" varchar not null,
  "period_start" date not null,
  "period_end" date not null,
  "paid_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE INDEX "vendor_payouts_vendor_id_status_index" on "vendor_payouts"(
  "vendor_id",
  "status"
);
CREATE TABLE IF NOT EXISTS "search_indices"(
  "id" integer primary key autoincrement not null,
  "entity_type" varchar not null,
  "entity_id" integer not null,
  "searchable_text" text not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "search_indices_entity_type_entity_id_index" on "search_indices"(
  "entity_type",
  "entity_id"
);
CREATE TABLE IF NOT EXISTS "vendor_performance_logs"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "metric" varchar not null,
  "value" numeric not null,
  "logged_at" datetime not null default CURRENT_TIMESTAMP,
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE INDEX "vendor_performance_logs_vendor_id_metric_logged_at_index" on "vendor_performance_logs"(
  "vendor_id",
  "metric",
  "logged_at"
);
CREATE TABLE IF NOT EXISTS "activity_logs"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "action" varchar not null,
  "ip_address" varchar,
  "user_agent" text,
  "properties" text,
  "created_at" datetime not null default CURRENT_TIMESTAMP,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE INDEX "activity_logs_user_id_created_at_index" on "activity_logs"(
  "user_id",
  "created_at"
);
CREATE TABLE IF NOT EXISTS "product_stats"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "views" integer not null default '0',
  "add_to_cart" integer not null default '0',
  "purchases" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE UNIQUE INDEX "product_stats_product_id_unique" on "product_stats"(
  "product_id"
);
CREATE TABLE IF NOT EXISTS "translations"(
  "id" integer primary key autoincrement not null,
  "entity_type" varchar not null,
  "entity_id" integer not null,
  "locale" varchar not null,
  "field" varchar not null,
  "value" text not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "translations_entity_type_entity_id_locale_field_unique" on "translations"(
  "entity_type",
  "entity_id",
  "locale",
  "field"
);
CREATE TABLE IF NOT EXISTS "hero_slides"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "static_pages"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "title" varchar not null,
  "slug" varchar not null,
  "content" text not null,
  "is_active" tinyint(1) not null default '1',
  "meta_title" varchar,
  "meta_description" text
);
CREATE TABLE IF NOT EXISTS "vendor_followers"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "vendor_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE UNIQUE INDEX "vendor_followers_user_id_vendor_id_unique" on "vendor_followers"(
  "user_id",
  "vendor_id"
);
CREATE TABLE IF NOT EXISTS "follows"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "target_type" varchar not null,
  "target_id" integer not null,
  "reward_type" varchar,
  "reward_value" numeric,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "follows_user_id_target_type_target_id_index" on "follows"(
  "user_id",
  "target_type",
  "target_id"
);
CREATE UNIQUE INDEX "follows_user_id_target_type_target_id_unique" on "follows"(
  "user_id",
  "target_type",
  "target_id"
);
CREATE TABLE IF NOT EXISTS "follow_rewards"(
  "id" integer primary key autoincrement not null,
  "follow_id" integer not null,
  "coupon_id" integer,
  "used_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("follow_id") references "follows"("id") on delete cascade,
  foreign key("coupon_id") references "coupons"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "seller_badges"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "icon" varchar,
  "color" varchar,
  "description" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "seller_badges_slug_unique" on "seller_badges"("slug");
CREATE TABLE IF NOT EXISTS "seller_badge_assignments"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "badge_id" integer not null,
  "assigned_at" datetime not null default CURRENT_TIMESTAMP,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade,
  foreign key("badge_id") references "seller_badges"("id") on delete cascade
);
CREATE UNIQUE INDEX "seller_badge_assignments_vendor_id_badge_id_unique" on "seller_badge_assignments"(
  "vendor_id",
  "badge_id"
);
CREATE TABLE IF NOT EXISTS "product_bundles"(
  "id" integer primary key autoincrement not null,
  "main_product_id" integer not null,
  "title" varchar not null,
  "bundle_type" varchar not null default 'together',
  "discount_rate" numeric not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("main_product_id") references "products"("id") on delete cascade
);
CREATE INDEX "product_bundles_main_product_id_index" on "product_bundles"(
  "main_product_id"
);
CREATE TABLE IF NOT EXISTS "product_bundle_items"(
  "id" integer primary key autoincrement not null,
  "bundle_id" integer not null,
  "product_id" integer not null,
  "order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("bundle_id") references "product_bundles"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "product_bundle_items_bundle_id_product_id_index" on "product_bundle_items"(
  "bundle_id",
  "product_id"
);
CREATE TABLE IF NOT EXISTS "product_live_stats"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "view_count" integer not null default '0',
  "cart_count" integer not null default '0',
  "purchase_count" integer not null default '0',
  "view_count_24h" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE UNIQUE INDEX "product_live_stats_product_id_unique" on "product_live_stats"(
  "product_id"
);
CREATE TABLE IF NOT EXISTS "user_coupons"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "coupon_id" integer not null,
  "status" varchar not null default 'active',
  "added_at" datetime not null default CURRENT_TIMESTAMP,
  "used_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("coupon_id") references "coupons"("id") on delete cascade
);
CREATE INDEX "user_coupons_user_id_status_index" on "user_coupons"(
  "user_id",
  "status"
);
CREATE TABLE IF NOT EXISTS "product_guarantees"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "type" varchar not null,
  "description" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "product_guarantees_product_id_type_index" on "product_guarantees"(
  "product_id",
  "type"
);
CREATE TABLE IF NOT EXISTS "return_policies"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "days" integer not null default '15',
  "is_free" tinyint(1) not null default '1',
  "conditions" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE INDEX "return_policies_vendor_id_index" on "return_policies"(
  "vendor_id"
);
CREATE TABLE IF NOT EXISTS "question_votes"(
  "id" integer primary key autoincrement not null,
  "question_id" integer not null,
  "user_id" integer not null,
  "is_helpful" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("question_id") references "product_questions"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "question_votes_question_id_user_id_unique" on "question_votes"(
  "question_id",
  "user_id"
);
CREATE TABLE IF NOT EXISTS "similar_products"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "similar_product_id" integer not null,
  "score" numeric not null default '0',
  "relation_type" varchar not null default 'similar',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade,
  foreign key("similar_product_id") references "products"("id") on delete cascade
);
CREATE INDEX "similar_products_product_id_relation_type_index" on "similar_products"(
  "product_id",
  "relation_type"
);
CREATE UNIQUE INDEX "similar_products_product_id_similar_product_id_unique" on "similar_products"(
  "product_id",
  "similar_product_id"
);
CREATE TABLE IF NOT EXISTS "seller_pages"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "seo_slug" varchar not null,
  "description" text,
  "banner" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "logo" varchar,
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE INDEX "seller_pages_seo_slug_index" on "seller_pages"("seo_slug");
CREATE UNIQUE INDEX "seller_pages_seo_slug_unique" on "seller_pages"(
  "seo_slug"
);
CREATE TABLE IF NOT EXISTS "price_history"(
  "id" integer primary key autoincrement not null,
  "variant_id" integer not null,
  "price" numeric not null,
  "sale_price" numeric,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("variant_id") references "product_variants"("id") on delete cascade
);
CREATE INDEX "price_history_variant_id_created_at_index" on "price_history"(
  "variant_id",
  "created_at"
);
CREATE TABLE IF NOT EXISTS "campaign_audit_logs"(
  "id" integer primary key autoincrement not null,
  "campaign_id" integer not null,
  "product_id" integer,
  "action" varchar not null,
  "old_value" numeric,
  "new_value" numeric,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("campaign_id") references "campaigns"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete set null
);
CREATE INDEX "campaign_audit_logs_campaign_id_created_at_index" on "campaign_audit_logs"(
  "campaign_id",
  "created_at"
);
CREATE TABLE IF NOT EXISTS "related_products"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "related_product_id" integer not null,
  "type" varchar not null default 'cross',
  "order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade,
  foreign key("related_product_id") references "products"("id") on delete cascade
);
CREATE INDEX "related_products_product_id_type_index" on "related_products"(
  "product_id",
  "type"
);
CREATE UNIQUE INDEX "related_products_product_id_related_product_id_type_unique" on "related_products"(
  "product_id",
  "related_product_id",
  "type"
);
CREATE TABLE IF NOT EXISTS "variant_campaigns"(
  "id" integer primary key autoincrement not null,
  "variant_id" integer not null,
  "campaign_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("variant_id") references "product_variants"("id") on delete cascade,
  foreign key("campaign_id") references "campaigns"("id") on delete cascade
);
CREATE UNIQUE INDEX "variant_campaigns_variant_id_campaign_id_unique" on "variant_campaigns"(
  "variant_id",
  "campaign_id"
);
CREATE TABLE IF NOT EXISTS "cities"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "plate_code" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "cities_plate_code_unique" on "cities"("plate_code");
CREATE TABLE IF NOT EXISTS "districts"(
  "id" integer primary key autoincrement not null,
  "city_id" integer not null,
  "name" varchar not null,
  "slug" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "extra_delivery_days" integer not null default '0',
  foreign key("city_id") references "cities"("id") on delete cascade
);
CREATE INDEX "districts_city_id_slug_index" on "districts"("city_id", "slug");
CREATE TABLE IF NOT EXISTS "shipping_zones"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "shipping_zone_cities"(
  "id" integer primary key autoincrement not null,
  "zone_id" integer not null,
  "city_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("zone_id") references "shipping_zones"("id") on delete cascade,
  foreign key("city_id") references "cities"("id") on delete cascade
);
CREATE UNIQUE INDEX "shipping_zone_cities_zone_id_city_id_unique" on "shipping_zone_cities"(
  "zone_id",
  "city_id"
);
CREATE TABLE IF NOT EXISTS "shipping_rates"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer,
  "zone_id" integer not null,
  "base_price" numeric not null default '0',
  "per_kg_price" numeric not null default '0',
  "estimated_days_min" integer not null default '2',
  "estimated_days_max" integer not null default '5',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade,
  foreign key("zone_id") references "shipping_zones"("id") on delete cascade
);
CREATE INDEX "shipping_rates_vendor_id_zone_id_is_active_index" on "shipping_rates"(
  "vendor_id",
  "zone_id",
  "is_active"
);
CREATE TABLE IF NOT EXISTS "delivery_estimates"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "city_id" integer not null,
  "district_id" integer,
  "estimated_delivery_date" date not null,
  "business_days" integer not null,
  "calculated_at" datetime not null default CURRENT_TIMESTAMP,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade,
  foreign key("city_id") references "cities"("id") on delete cascade,
  foreign key("district_id") references "districts"("id") on delete cascade
);
CREATE INDEX "delivery_estimates_product_id_city_id_district_id_index" on "delivery_estimates"(
  "product_id",
  "city_id",
  "district_id"
);
CREATE TABLE IF NOT EXISTS "conversations"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "vendor_id" integer not null,
  "order_id" integer,
  "subject" varchar not null,
  "status" varchar not null default 'open',
  "last_message_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("vendor_id") references "vendors"("id") on delete cascade,
  foreign key("order_id") references "orders"("id") on delete set null
);
CREATE INDEX "conversations_user_id_status_index" on "conversations"(
  "user_id",
  "status"
);
CREATE INDEX "conversations_vendor_id_status_index" on "conversations"(
  "vendor_id",
  "status"
);
CREATE TABLE IF NOT EXISTS "messages"(
  "id" integer primary key autoincrement not null,
  "conversation_id" integer not null,
  "sender_type" varchar not null,
  "sender_id" integer not null,
  "message" text not null,
  "read_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("conversation_id") references "conversations"("id") on delete cascade
);
CREATE INDEX "messages_conversation_id_created_at_index" on "messages"(
  "conversation_id",
  "created_at"
);
CREATE TABLE IF NOT EXISTS "message_attachments"(
  "id" integer primary key autoincrement not null,
  "message_id" integer not null,
  "file_name" varchar not null,
  "file_path" varchar not null,
  "file_type" varchar not null,
  "file_size" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("message_id") references "messages"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "payment_methods"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "card_holder_name" varchar not null,
  "card_last_four" varchar not null,
  "card_brand" varchar not null,
  "card_token" varchar not null,
  "expiry_month" varchar not null,
  "expiry_year" varchar not null,
  "is_default" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "payment_methods_user_id_is_active_index" on "payment_methods"(
  "user_id",
  "is_active"
);
CREATE UNIQUE INDEX "payment_methods_card_token_unique" on "payment_methods"(
  "card_token"
);
CREATE TABLE IF NOT EXISTS "notification_preferences"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "email_campaigns" tinyint(1) not null default '1',
  "email_orders" tinyint(1) not null default '1',
  "email_promotions" tinyint(1) not null default '1',
  "email_reviews" tinyint(1) not null default '1',
  "sms_campaigns" tinyint(1) not null default '0',
  "sms_orders" tinyint(1) not null default '1',
  "sms_promotions" tinyint(1) not null default '0',
  "push_enabled" tinyint(1) not null default '1',
  "push_campaigns" tinyint(1) not null default '1',
  "push_orders" tinyint(1) not null default '1',
  "push_messages" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "notification_preferences_user_id_unique" on "notification_preferences"(
  "user_id"
);
CREATE TABLE IF NOT EXISTS "user_sessions"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "session_token" varchar not null,
  "ip_address" varchar not null,
  "user_agent" text not null,
  "device_type" varchar,
  "device_name" varchar,
  "browser" varchar,
  "platform" varchar,
  "last_activity" datetime not null,
  "is_current" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "user_sessions_user_id_is_current_index" on "user_sessions"(
  "user_id",
  "is_current"
);
CREATE INDEX "user_sessions_user_id_last_activity_index" on "user_sessions"(
  "user_id",
  "last_activity"
);
CREATE UNIQUE INDEX "user_sessions_session_token_unique" on "user_sessions"(
  "session_token"
);
CREATE TABLE IF NOT EXISTS "membership_programs"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "price_monthly" numeric not null default '0',
  "price_yearly" numeric not null default '0',
  "benefits" text,
  "badge_icon" varchar,
  "badge_color" varchar,
  "is_active" tinyint(1) not null default '1',
  "order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "membership_programs_slug_unique" on "membership_programs"(
  "slug"
);
CREATE TABLE IF NOT EXISTS "user_memberships"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "program_id" integer not null,
  "starts_at" datetime not null,
  "expires_at" datetime not null,
  "status" varchar not null default 'active',
  "auto_renew" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("program_id") references "membership_programs"("id") on delete cascade
);
CREATE INDEX "user_memberships_user_id_status_index" on "user_memberships"(
  "user_id",
  "status"
);
CREATE TABLE IF NOT EXISTS "user_wallets"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "balance" numeric not null default '0',
  "currency" varchar not null default 'TRY',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "user_wallets_user_id_unique" on "user_wallets"("user_id");
CREATE TABLE IF NOT EXISTS "wallet_transactions"(
  "id" integer primary key autoincrement not null,
  "wallet_id" integer not null,
  "type" varchar not null,
  "amount" numeric not null,
  "balance_after" numeric not null,
  "description" text not null,
  "reference_type" varchar,
  "reference_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("wallet_id") references "user_wallets"("id") on delete cascade
);
CREATE INDEX "wallet_transactions_wallet_id_created_at_index" on "wallet_transactions"(
  "wallet_id",
  "created_at"
);
CREATE INDEX "wallet_transactions_reference_type_reference_id_index" on "wallet_transactions"(
  "reference_type",
  "reference_id"
);
CREATE TABLE IF NOT EXISTS "raffles"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "prize_title" varchar not null,
  "prize_description" text,
  "prize_image" varchar,
  "start_date" datetime not null,
  "end_date" datetime not null,
  "max_entries_per_user" integer not null default '1',
  "total_winners" integer not null default '1',
  "status" varchar not null default 'upcoming',
  "rules" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "raffles_status_start_date_end_date_index" on "raffles"(
  "status",
  "start_date",
  "end_date"
);
CREATE UNIQUE INDEX "raffles_slug_unique" on "raffles"("slug");
CREATE TABLE IF NOT EXISTS "raffle_entries"(
  "id" integer primary key autoincrement not null,
  "raffle_id" integer not null,
  "user_id" integer not null,
  "entry_count" integer not null default '1',
  "entry_code" varchar not null,
  "is_winner" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("raffle_id") references "raffles"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "raffle_entries_raffle_id_user_id_unique" on "raffle_entries"(
  "raffle_id",
  "user_id"
);
CREATE INDEX "raffle_entries_raffle_id_is_winner_index" on "raffle_entries"(
  "raffle_id",
  "is_winner"
);
CREATE UNIQUE INDEX "raffle_entries_entry_code_unique" on "raffle_entries"(
  "entry_code"
);
CREATE TABLE IF NOT EXISTS "raffle_winners"(
  "id" integer primary key autoincrement not null,
  "raffle_id" integer not null,
  "entry_id" integer not null,
  "user_id" integer not null,
  "prize_rank" integer not null,
  "announced_at" datetime,
  "prize_claimed" tinyint(1) not null default '0',
  "claimed_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("raffle_id") references "raffles"("id") on delete cascade,
  foreign key("entry_id") references "raffle_entries"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "quick_reorders"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "product_id" integer not null,
  "variant_id" integer,
  "last_ordered_at" datetime not null,
  "order_count" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade,
  foreign key("variant_id") references "product_variants"("id") on delete cascade
);
CREATE UNIQUE INDEX "quick_reorders_user_id_product_id_variant_id_unique" on "quick_reorders"(
  "user_id",
  "product_id",
  "variant_id"
);
CREATE INDEX "quick_reorders_user_id_last_ordered_at_index" on "quick_reorders"(
  "user_id",
  "last_ordered_at"
);
CREATE TABLE IF NOT EXISTS "assistant_conversations"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "session_id" varchar not null,
  "status" varchar not null default 'active',
  "last_message_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "assistant_conversations_user_id_status_index" on "assistant_conversations"(
  "user_id",
  "status"
);
CREATE UNIQUE INDEX "assistant_conversations_session_id_unique" on "assistant_conversations"(
  "session_id"
);
CREATE TABLE IF NOT EXISTS "assistant_messages"(
  "id" integer primary key autoincrement not null,
  "conversation_id" integer not null,
  "role" varchar not null,
  "message" text not null,
  "metadata" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("conversation_id") references "assistant_conversations"("id") on delete cascade
);
CREATE INDEX "assistant_messages_conversation_id_created_at_index" on "assistant_messages"(
  "conversation_id",
  "created_at"
);
CREATE TABLE IF NOT EXISTS "shipment_events"(
  "id" integer primary key autoincrement not null,
  "shipment_id" integer not null,
  "status" varchar not null,
  "location" varchar,
  "description" text,
  "occurred_at" datetime not null,
  "meta" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("shipment_id") references "shipments"("id") on delete cascade
);
CREATE INDEX "shipment_events_shipment_id_occurred_at_index" on "shipment_events"(
  "shipment_id",
  "occurred_at"
);
CREATE TABLE IF NOT EXISTS "notifications"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  "order_id" integer,
  "shipment_id" integer,
  "type" varchar,
  "channel" varchar,
  "title" varchar,
  "message" text,
  "data" text,
  "sent_at" datetime,
  "read_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("order_id") references "orders"("id") on delete cascade,
  foreign key("shipment_id") references "shipments"("id") on delete cascade
);
CREATE INDEX "notifications_user_id_order_id_shipment_id_index" on "notifications"(
  "user_id",
  "order_id",
  "shipment_id"
);
CREATE TABLE IF NOT EXISTS "quick_links"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "label" varchar not null,
  "icon" varchar,
  "path" varchar,
  "color" varchar,
  "order" integer not null default('0'),
  "is_active" tinyint(1) not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "category_slug" varchar,
  "campaign_slug" varchar,
  "product_id" integer,
  "link_type" varchar not null default 'category'
);
CREATE UNIQUE INDEX "quick_links_key_unique" on "quick_links"("key");
CREATE TABLE IF NOT EXISTS "product_banners"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "title" varchar not null,
  "image" varchar not null,
  "position" varchar not null,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "product_banners_product_id_position_index" on "product_banners"(
  "product_id",
  "position"
);
CREATE TABLE IF NOT EXISTS "product_badges"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "label" varchar not null,
  "color" varchar not null default 'info',
  "icon" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "product_badges_product_id_color_index" on "product_badges"(
  "product_id",
  "color"
);
CREATE TABLE IF NOT EXISTS "product_features"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "title" varchar not null,
  "icon" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "product_features_product_id_index" on "product_features"(
  "product_id"
);
CREATE TABLE IF NOT EXISTS "product_blocks"(
  "id" integer primary key autoincrement not null,
  "product_id" integer,
  "block_type" varchar not null,
  "position" varchar not null,
  "priority" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete set null
);
CREATE INDEX "product_blocks_product_id_position_is_active_index" on "product_blocks"(
  "product_id",
  "position",
  "is_active"
);
CREATE INDEX "product_blocks_block_type_position_is_active_index" on "product_blocks"(
  "block_type",
  "position",
  "is_active"
);
CREATE TABLE IF NOT EXISTS "block_contents"(
  "id" integer primary key autoincrement not null,
  "block_id" integer not null,
  "title" varchar,
  "description" text,
  "icon" varchar,
  "image" varchar,
  "color" varchar,
  "cta_text" varchar,
  "cta_link" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("block_id") references "product_blocks"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "block_rules"(
  "id" integer primary key autoincrement not null,
  "block_id" integer not null,
  "rule_type" varchar not null,
  "operator" varchar not null,
  "value" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("block_id") references "product_blocks"("id") on delete cascade
);
CREATE INDEX "block_rules_block_id_rule_type_index" on "block_rules"(
  "block_id",
  "rule_type"
);
CREATE TABLE IF NOT EXISTS "product_safety_images"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "image" varchar not null,
  "title" varchar,
  "alt" varchar,
  "order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "product_safety_images_product_id_order_index" on "product_safety_images"(
  "product_id",
  "order"
);
CREATE TABLE IF NOT EXISTS "product_safety_documents"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "title" varchar not null,
  "file" varchar not null,
  "order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "product_safety_documents_product_id_order_index" on "product_safety_documents"(
  "product_id",
  "order"
);
CREATE TABLE IF NOT EXISTS "category_vendor"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "category_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade,
  foreign key("category_id") references "categories"("id") on delete cascade
);
CREATE UNIQUE INDEX "category_vendor_vendor_id_category_id_unique" on "category_vendor"(
  "vendor_id",
  "category_id"
);
CREATE INDEX "category_vendor_category_id_vendor_id_index" on "category_vendor"(
  "category_id",
  "vendor_id"
);
CREATE TABLE IF NOT EXISTS "campaigns"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "discount_type" varchar not null,
  "discount_value" numeric not null,
  "starts_at" datetime not null,
  "ends_at" datetime not null,
  "is_active" tinyint(1) not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "banner" varchar,
  "description" text,
  "slug" varchar not null,
  "vendor_id" integer,
  foreign key("vendor_id") references "vendors"("id") on delete set null
);
CREATE UNIQUE INDEX "campaigns_slug_unique" on "campaigns"("slug");
CREATE TABLE IF NOT EXISTS "product_reviews"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "user_id" integer not null,
  "rating" integer not null,
  "comment" text,
  "is_verified_purchase" tinyint(1) not null default('0'),
  "created_at" datetime,
  "updated_at" datetime,
  "vendor_id" integer,
  "vendor_response" text,
  "vendor_response_at" datetime,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("product_id") references products("id") on delete cascade on update no action,
  foreign key("vendor_id") references "vendors"("id") on delete set null
);
CREATE INDEX "product_reviews_product_id_rating_index" on "product_reviews"(
  "product_id",
  "rating"
);
CREATE TABLE IF NOT EXISTS "brands"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "logo" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "vendor_id" integer,
  "is_vendor_brand" tinyint(1) not null default '0',
  foreign key("vendor_id") references "vendors"("id") on delete set null
);
CREATE UNIQUE INDEX "brands_name_unique" on "brands"("name");
CREATE UNIQUE INDEX "brands_slug_unique" on "brands"("slug");
CREATE TABLE IF NOT EXISTS "coupons"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "vendor_id" integer,
  "product_id" integer,
  "code" varchar not null,
  "title" varchar not null,
  "description" text,
  "discount_type" varchar not null,
  "discount_value" numeric not null,
  "min_order_amount" numeric,
  "max_discount_amount" numeric,
  "usage_limit" integer,
  "per_user_limit" integer,
  "starts_at" datetime,
  "expires_at" datetime,
  "is_active" tinyint(1) not null default '1',
  foreign key("vendor_id") references "vendors"("id") on delete set null,
  foreign key("product_id") references "products"("id") on delete set null
);
CREATE UNIQUE INDEX "coupons_code_unique" on "coupons"("code");
CREATE TABLE IF NOT EXISTS "product_question_categories"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "icon" varchar,
  "order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "product_question_categories_slug_unique" on "product_question_categories"(
  "slug"
);
CREATE TABLE IF NOT EXISTS "category_question_category"(
  "category_id" integer not null,
  "product_question_category_id" integer not null,
  foreign key("category_id") references "categories"("id") on delete cascade,
  foreign key("product_question_category_id") references "product_question_categories"("id") on delete cascade,
  primary key("category_id", "product_question_category_id")
);
CREATE TABLE IF NOT EXISTS "coupon_usages"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "coupon_id" integer not null,
  "user_id" integer not null,
  "order_id" integer,
  "discount_amount" numeric not null default '0',
  "used_at" datetime,
  foreign key("coupon_id") references "coupons"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("order_id") references "orders"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "product_questions"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "user_id" integer not null,
  "question" text not null,
  "answer" text,
  "answered_by_vendor" tinyint(1) not null default('0'),
  "created_at" datetime,
  "updated_at" datetime,
  "vendor_id" integer,
  "product_question_category_id" integer,
  foreign key("vendor_id") references vendors("id") on delete set null on update no action,
  foreign key("product_id") references products("id") on delete cascade on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("product_question_category_id") references "product_question_categories"("id") on delete set null
);
CREATE INDEX "product_questions_product_id_index" on "product_questions"(
  "product_id"
);
CREATE TABLE IF NOT EXISTS "brand_vendor"(
  "brand_id" integer not null,
  "vendor_id" integer not null,
  "is_authorized" tinyint(1) not null default '1',
  "authorized_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "authorization_type" varchar not null default 'authorized_dealer',
  "authorization_document" varchar,
  "invoice_document" varchar,
  "valid_from" date,
  "valid_until" date,
  "status" varchar not null default 'pending',
  foreign key("brand_id") references "brands"("id") on delete cascade,
  foreign key("vendor_id") references "vendors"("id") on delete cascade,
  primary key("brand_id", "vendor_id")
);
CREATE TABLE IF NOT EXISTS "product_faqs"(
  "id" integer primary key autoincrement not null,
  "product_id" integer,
  "category_id" integer,
  "question" text not null,
  "answer" text not null,
  "order" integer not null default('0'),
  "is_active" tinyint(1) not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "vendor_id" integer,
  foreign key("category_id") references categories("id") on delete cascade on update no action,
  foreign key("product_id") references products("id") on delete cascade on update no action,
  foreign key("vendor_id") references "vendors"("id") on delete set null
);
CREATE INDEX "product_faqs_category_id_is_active_index" on "product_faqs"(
  "category_id",
  "is_active"
);
CREATE INDEX "product_faqs_product_id_is_active_index" on "product_faqs"(
  "product_id",
  "is_active"
);
CREATE TABLE IF NOT EXISTS "shipping_companies"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "code" varchar not null,
  "logo" varchar,
  "tracking_url" varchar,
  "api_url" varchar,
  "api_credentials" text,
  "is_active" tinyint(1) not null default '1',
  "order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "webhook_url" varchar,
  "webhook_secret" varchar,
  "supported_services" text,
  "base_price" numeric not null default '0',
  "price_per_kg" numeric not null default '0',
  "price_per_desi" numeric not null default '0',
  "free_shipping_threshold" integer,
  "coverage_areas" text
);
CREATE UNIQUE INDEX "shipping_companies_code_unique" on "shipping_companies"(
  "code"
);
CREATE TABLE IF NOT EXISTS "shipments"(
  "id" integer primary key autoincrement not null,
  "order_item_id" integer not null,
  "tracking_number" varchar not null,
  "carrier" varchar not null,
  "estimated_delivery" datetime,
  "shipped_at" datetime,
  "delivered_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "tracking_url" varchar,
  "current_location" varchar,
  "progress_percent" integer not null default('0'),
  "notify_on_status_change" tinyint(1) not null default('1'),
  "shipping_company_id" integer,
  "order_id" integer,
  "address_id" integer,
  "vendor_id" integer,
  "status" varchar not null default 'pending',
  "current_latitude" numeric,
  "current_longitude" numeric,
  "label_url" varchar,
  "barcode_url" varchar,
  "cargo_reference_id" varchar,
  foreign key("order_item_id") references order_items("id") on delete cascade on update no action,
  foreign key("shipping_company_id") references "shipping_companies"("id") on delete set null,
  foreign key("order_id") references "orders"("id") on delete cascade,
  foreign key("address_id") references "addresses"("id") on delete set null,
  foreign key("vendor_id") references "vendors"("id") on delete set null
);
CREATE UNIQUE INDEX "shipments_tracking_number_unique" on "shipments"(
  "tracking_number"
);
CREATE TABLE IF NOT EXISTS "return_images"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "product_return_id" integer not null,
  "image" varchar not null,
  foreign key("product_return_id") references "product_returns"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "review_helpful_votes"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "product_review_id" integer not null,
  "user_id" integer not null,
  "is_helpful" tinyint(1) not null default '1',
  foreign key("product_review_id") references "product_reviews"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "review_helpful_votes_product_review_id_user_id_unique" on "review_helpful_votes"(
  "product_review_id",
  "user_id"
);
CREATE TABLE IF NOT EXISTS "wishlist_items"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "wishlist_id" integer not null,
  "product_id" integer not null,
  foreign key("wishlist_id") references "wishlists"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE UNIQUE INDEX "wishlist_items_wishlist_id_product_id_unique" on "wishlist_items"(
  "wishlist_id",
  "product_id"
);
CREATE TABLE IF NOT EXISTS "wishlists"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer not null,
  "name" varchar not null default 'Favoriler',
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "wishlists_user_id_name_index" on "wishlists"("user_id", "name");
CREATE TABLE IF NOT EXISTS "addresses"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "title" varchar not null,
  "city" varchar not null,
  "district" varchar not null,
  "address_line" text not null,
  "postal_code" varchar,
  "is_default" tinyint(1) not null default('0'),
  "created_at" datetime,
  "updated_at" datetime,
  "vendor_id" integer,
  "address_type" varchar,
  "full_name" varchar,
  "phone" varchar,
  "neighborhood" varchar,
  "latitude" numeric,
  "longitude" numeric,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("vendor_id") references "vendors"("id") on delete set null
);
CREATE INDEX "addresses_user_id_is_default_index" on "addresses"(
  "user_id",
  "is_default"
);
CREATE TABLE IF NOT EXISTS "price_alerts"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer not null,
  "product_id" integer not null,
  "target_price" numeric not null,
  "is_active" tinyint(1) not null default '1',
  "notified_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "price_alerts_user_id_product_id_index" on "price_alerts"(
  "user_id",
  "product_id"
);
CREATE TABLE IF NOT EXISTS "stock_alerts"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer not null,
  "product_id" integer not null,
  "is_active" tinyint(1) not null default '1',
  "notified_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "stock_alerts_user_id_product_id_index" on "stock_alerts"(
  "user_id",
  "product_id"
);
CREATE TABLE IF NOT EXISTS "blog_posts"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer not null,
  "title" varchar not null,
  "slug" varchar not null,
  "excerpt" text,
  "content" text not null,
  "cover_image" varchar,
  "is_published" tinyint(1) not null default '0',
  "published_at" datetime,
  "meta_title" varchar,
  "meta_description" text,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "blog_posts_slug_unique" on "blog_posts"("slug");
CREATE TABLE IF NOT EXISTS "search_histories"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  "query" varchar not null,
  "results_count" integer not null default '0',
  "searched_at" datetime not null default CURRENT_TIMESTAMP,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE INDEX "search_histories_user_id_searched_at_index" on "search_histories"(
  "user_id",
  "searched_at"
);
CREATE TABLE IF NOT EXISTS "review_images"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "product_review_id" integer not null,
  "image_path" varchar not null,
  "alt_text" varchar,
  "sort_order" integer not null default '0',
  foreign key("product_review_id") references "product_reviews"("id") on delete cascade
);
CREATE UNIQUE INDEX "static_pages_slug_unique" on "static_pages"("slug");
CREATE TABLE IF NOT EXISTS "contact_messages"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  "name" varchar not null,
  "email" varchar not null,
  "phone" varchar,
  "subject" varchar,
  "message" text not null,
  "is_read" tinyint(1) not null default '0',
  "replied_at" datetime,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "notification_settings"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "email_campaigns" tinyint(1) not null default '1',
  "email_orders" tinyint(1) not null default '1',
  "email_promotions" tinyint(1) not null default '1',
  "email_reviews" tinyint(1) not null default '1',
  "sms_campaigns" tinyint(1) not null default '0',
  "sms_orders" tinyint(1) not null default '1',
  "sms_promotions" tinyint(1) not null default '0',
  "push_enabled" tinyint(1) not null default '1',
  "push_campaigns" tinyint(1) not null default '1',
  "push_orders" tinyint(1) not null default '1',
  "push_messages" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "notification_settings_user_id_unique" on "notification_settings"(
  "user_id"
);
CREATE TABLE IF NOT EXISTS "product_returns"(
  "id" integer primary key autoincrement not null,
  "order_item_id" integer not null,
  "tracking_number" varchar,
  "carrier" varchar,
  "received_at" datetime,
  "condition_status" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "reason" varchar not null,
  "reason_description" text,
  "status" varchar not null default 'pending',
  "user_id" integer,
  "vendor_id" integer,
  "refund_amount" numeric,
  "requested_at" datetime,
  "approved_at" datetime,
  "rejected_at" datetime,
  foreign key("order_item_id") references order_items("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete set null,
  foreign key("vendor_id") references "vendors"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "personal_access_tokens"(
  "id" integer primary key autoincrement not null,
  "tokenable_type" varchar not null,
  "tokenable_id" integer not null,
  "name" text not null,
  "token" varchar not null,
  "abilities" text,
  "last_used_at" datetime,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" on "personal_access_tokens"(
  "tokenable_type",
  "tokenable_id"
);
CREATE UNIQUE INDEX "personal_access_tokens_token_unique" on "personal_access_tokens"(
  "token"
);
CREATE INDEX "personal_access_tokens_expires_at_index" on "personal_access_tokens"(
  "expires_at"
);
CREATE TABLE IF NOT EXISTS "cart_items"(
  "id" integer primary key autoincrement not null,
  "cart_id" integer not null,
  "product_id" integer not null,
  "product_seller_id" integer not null,
  "quantity" integer not null default('1'),
  "price" numeric not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references products("id") on delete cascade on update no action,
  foreign key("cart_id") references carts("id") on delete cascade on update no action,
  foreign key("product_seller_id") references "product_sellers"("id") on delete cascade
);
CREATE UNIQUE INDEX "cart_items_cart_id_product_seller_id_unique" on "cart_items"(
  "cart_id",
  "product_seller_id"
);
CREATE TABLE IF NOT EXISTS "product_videos"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "title" varchar,
  "url" varchar not null,
  "video_type" varchar not null default 'youtube',
  "order" integer not null default '0',
  "is_featured" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "product_videos_product_id_video_type_index" on "product_videos"(
  "product_id",
  "video_type"
);
CREATE TABLE IF NOT EXISTS "shipping_rules"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer,
  "cutoff_time" time,
  "same_day_shipping" tinyint(1) not null default('0'),
  "free_shipping" tinyint(1) not null default('0'),
  "free_shipping_min_amount" numeric,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  "address_id" integer,
  foreign key("vendor_id") references vendors("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete set null,
  foreign key("address_id") references "addresses"("id") on delete set null
);
CREATE INDEX "shipping_rules_vendor_id_index" on "shipping_rules"("vendor_id");
CREATE TABLE IF NOT EXISTS "vendors"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "tier_id" integer,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "rating" numeric not null default('0'),
  "total_orders" integer not null default('0'),
  "followers" integer not null default('0'),
  "response_time_avg" integer not null default('0'),
  "cancel_rate" numeric not null default('0'),
  "return_rate" numeric not null default('0'),
  "late_shipment_rate" numeric not null default('0'),
  "status" varchar not null default('pending'),
  "created_at" datetime,
  "updated_at" datetime,
  "category" varchar,
  "company_type" varchar,
  "tax_number" varchar,
  "city" varchar,
  "district" varchar,
  "reference_code" varchar,
  "address" text,
  "kyc_status" varchar not null default 'pending',
  "kyc_verified_at" datetime,
  "kyc_notes" text,
  "kyc_verified_by" integer,
  "application_status" varchar not null default 'pending',
  "application_submitted_at" datetime,
  "application_reviewed_at" datetime,
  "application_reviewed_by" integer,
  "rejection_reason" text,
  "business_license_number" varchar,
  "iban" varchar,
  "bank_name" varchar,
  "account_holder_name" varchar,
  foreign key("tier_id") references vendor_tiers("id") on delete set null on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("kyc_verified_by") references "users"("id") on delete set null,
  foreign key("application_reviewed_by") references "users"("id") on delete set null
);
CREATE INDEX "vendors_slug_index" on "vendors"("slug");
CREATE UNIQUE INDEX "vendors_slug_unique" on "vendors"("slug");
CREATE INDEX "vendors_status_rating_index" on "vendors"("status", "rating");
CREATE INDEX "vendors_kyc_status_index" on "vendors"("kyc_status");
CREATE INDEX "vendors_application_status_index" on "vendors"(
  "application_status"
);
CREATE TABLE IF NOT EXISTS "vendor_documents"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "document_type" varchar not null,
  "file_path" varchar not null,
  "file_name" varchar not null,
  "mime_type" varchar,
  "file_size" integer,
  "status" varchar not null default 'pending',
  "rejection_reason" text,
  "verified_by" integer,
  "verified_at" datetime,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade,
  foreign key("verified_by") references "users"("id") on delete set null
);
CREATE INDEX "vendor_documents_vendor_id_document_type_index" on "vendor_documents"(
  "vendor_id",
  "document_type"
);
CREATE INDEX "vendor_documents_status_index" on "vendor_documents"("status");
CREATE INDEX "payments_split_status_index" on "payments"("split_status");
CREATE INDEX "payments_gateway_index" on "payments"("gateway");
CREATE TABLE IF NOT EXISTS "invoices"(
  "id" integer primary key autoincrement not null,
  "order_id" integer not null,
  "vendor_id" integer not null,
  "user_id" integer not null,
  "invoice_number" varchar not null,
  "invoice_type" varchar not null,
  "invoice_scenario" varchar not null default 'basic',
  "subtotal" numeric not null,
  "tax_amount" numeric not null,
  "total_amount" numeric not null,
  "currency" varchar not null default 'TRY',
  "status" varchar not null default 'draft',
  "uuid" varchar,
  "ettn" text,
  "invoice_data" text,
  "receiver_info" text,
  "error_message" text,
  "sent_at" datetime,
  "delivered_at" datetime,
  "cancelled_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("order_id") references "orders"("id") on delete cascade,
  foreign key("vendor_id") references "vendors"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "invoices_vendor_id_status_index" on "invoices"(
  "vendor_id",
  "status"
);
CREATE INDEX "invoices_invoice_type_index" on "invoices"("invoice_type");
CREATE INDEX "invoices_sent_at_index" on "invoices"("sent_at");
CREATE UNIQUE INDEX "invoices_invoice_number_unique" on "invoices"(
  "invoice_number"
);
CREATE UNIQUE INDEX "invoices_uuid_unique" on "invoices"("uuid");
CREATE TABLE IF NOT EXISTS "cargo_integrations"(
  "id" integer primary key autoincrement not null,
  "shipping_company_id" integer not null,
  "vendor_id" integer,
  "integration_type" varchar not null,
  "api_endpoint" varchar,
  "api_key" varchar,
  "api_secret" varchar,
  "customer_code" varchar,
  "api_credentials" text,
  "configuration" text,
  "is_active" tinyint(1) not null default '1',
  "is_test_mode" tinyint(1) not null default '0',
  "last_sync_at" datetime,
  "last_error" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("shipping_company_id") references "shipping_companies"("id") on delete cascade,
  foreign key("vendor_id") references "vendors"("id") on delete set null
);
CREATE INDEX "cargo_integrations_shipping_company_id_vendor_id_index" on "cargo_integrations"(
  "shipping_company_id",
  "vendor_id"
);
CREATE INDEX "cargo_integrations_is_active_index" on "cargo_integrations"(
  "is_active"
);
CREATE TABLE IF NOT EXISTS "product_import_logs"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "user_id" integer not null,
  "file_path" varchar not null,
  "file_name" varchar not null,
  "file_type" varchar not null,
  "total_rows" integer not null default '0',
  "success_count" integer not null default '0',
  "failed_count" integer not null default '0',
  "skipped_count" integer not null default '0',
  "status" varchar not null default 'pending',
  "errors" text,
  "summary" text,
  "started_at" datetime,
  "completed_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "product_import_logs_vendor_id_status_index" on "product_import_logs"(
  "vendor_id",
  "status"
);
CREATE INDEX "product_import_logs_status_index" on "product_import_logs"(
  "status"
);
CREATE TABLE IF NOT EXISTS "vendor_sla_metrics"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "metric_date" date not null,
  "total_orders" integer not null default '0',
  "cancelled_orders" integer not null default '0',
  "returned_orders" integer not null default '0',
  "late_shipments" integer not null default '0',
  "on_time_shipments" integer not null default '0',
  "cancel_rate" numeric not null default '0',
  "return_rate" numeric not null default '0',
  "late_shipment_rate" numeric not null default '0',
  "avg_shipment_time" integer not null default '0',
  "avg_response_time" integer not null default '0',
  "total_questions_answered" integer not null default '0',
  "total_reviews_responded" integer not null default '0',
  "customer_satisfaction_score" numeric not null default '0',
  "sla_violations" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE UNIQUE INDEX "vendor_sla_metrics_vendor_id_metric_date_unique" on "vendor_sla_metrics"(
  "vendor_id",
  "metric_date"
);
CREATE INDEX "vendor_sla_metrics_metric_date_index" on "vendor_sla_metrics"(
  "metric_date"
);
CREATE TABLE IF NOT EXISTS "disputes"(
  "id" integer primary key autoincrement not null,
  "order_item_id" integer not null,
  "user_id" integer not null,
  "vendor_id" integer not null,
  "reason" text not null,
  "status" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "assigned_to" integer,
  "resolution_notes" text,
  "resolved_by" integer,
  "resolution_type" varchar,
  "evidence_files" text,
  "assigned_at" datetime,
  "resolved_at" datetime,
  foreign key("vendor_id") references vendors("id") on delete cascade on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("order_item_id") references order_items("id") on delete cascade on update no action,
  foreign key("assigned_to") references "users"("id") on delete set null,
  foreign key("resolved_by") references "users"("id") on delete set null
);
CREATE INDEX "disputes_order_item_id_status_index" on "disputes"(
  "order_item_id",
  "status"
);
CREATE INDEX "disputes_assigned_to_index" on "disputes"("assigned_to");
CREATE INDEX "disputes_resolved_by_index" on "disputes"("resolved_by");
CREATE TABLE IF NOT EXISTS "user_consents"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "consent_type" varchar not null,
  "consent_version" varchar not null,
  "is_granted" tinyint(1) not null default '0',
  "ip_address" varchar,
  "user_agent" text,
  "granted_at" datetime,
  "revoked_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "user_consents_user_id_consent_type_index" on "user_consents"(
  "user_id",
  "consent_type"
);
CREATE INDEX "user_consents_consent_type_index" on "user_consents"(
  "consent_type"
);
CREATE TABLE IF NOT EXISTS "data_deletion_requests"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "request_type" varchar not null,
  "status" varchar not null default 'pending',
  "reason" text,
  "admin_notes" text,
  "processed_by" integer,
  "requested_at" datetime not null default CURRENT_TIMESTAMP,
  "processed_at" datetime,
  "completed_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("processed_by") references "users"("id") on delete set null
);
CREATE INDEX "data_deletion_requests_user_id_status_index" on "data_deletion_requests"(
  "user_id",
  "status"
);
CREATE INDEX "data_deletion_requests_status_index" on "data_deletion_requests"(
  "status"
);
CREATE TABLE IF NOT EXISTS "platform_revenue_reports"(
  "id" integer primary key autoincrement not null,
  "report_date" date not null,
  "period_type" varchar not null,
  "total_revenue" numeric not null default '0',
  "total_commission" numeric not null default '0',
  "vendor_payouts" numeric not null default '0',
  "total_orders" integer not null default '0',
  "total_vendors" integer not null default '0',
  "active_vendors" integer not null default '0',
  "new_vendors" integer not null default '0',
  "total_customers" integer not null default '0',
  "new_customers" integer not null default '0',
  "total_products" integer not null default '0',
  "avg_order_value" numeric not null default '0',
  "top_categories" text,
  "top_vendors" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "platform_revenue_reports_report_date_period_type_unique" on "platform_revenue_reports"(
  "report_date",
  "period_type"
);
CREATE INDEX "platform_revenue_reports_period_type_report_date_index" on "platform_revenue_reports"(
  "period_type",
  "report_date"
);
CREATE TABLE IF NOT EXISTS "vendor_daily_stats"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "stat_date" date not null,
  "total_sales" integer not null default '0',
  "revenue" numeric not null default '0',
  "commission" numeric not null default '0',
  "net_revenue" numeric not null default '0',
  "orders_count" integer not null default '0',
  "products_sold" integer not null default '0',
  "new_customers" integer not null default '0',
  "returning_customers" integer not null default '0',
  "avg_order_value" numeric not null default '0',
  "page_views" integer not null default '0',
  "product_views" integer not null default '0',
  "conversion_rate" numeric not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE UNIQUE INDEX "vendor_daily_stats_vendor_id_stat_date_unique" on "vendor_daily_stats"(
  "vendor_id",
  "stat_date"
);
CREATE INDEX "vendor_daily_stats_stat_date_index" on "vendor_daily_stats"(
  "stat_date"
);
CREATE TABLE IF NOT EXISTS "product_approvals"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "vendor_id" integer not null,
  "status" varchar not null default 'pending',
  "rejection_reason" text,
  "admin_notes" text,
  "changes_requested" text,
  "reviewed_by" integer,
  "submitted_at" datetime not null default CURRENT_TIMESTAMP,
  "reviewed_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade,
  foreign key("vendor_id") references "vendors"("id") on delete cascade,
  foreign key("reviewed_by") references "users"("id")
);
CREATE INDEX "product_approvals_product_id_status_index" on "product_approvals"(
  "product_id",
  "status"
);
CREATE INDEX "product_approvals_vendor_id_status_index" on "product_approvals"(
  "vendor_id",
  "status"
);
CREATE INDEX "product_approvals_reviewed_by_index" on "product_approvals"(
  "reviewed_by"
);
CREATE TABLE IF NOT EXISTS "vendor_analytics"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "date" date not null,
  "total_sales" numeric not null default '0',
  "total_orders" integer not null default '0',
  "average_order_value" numeric not null default '0',
  "units_sold" integer not null default '0',
  "commission_amount" numeric not null default '0',
  "net_earnings" numeric not null default '0',
  "pending_payout" numeric not null default '0',
  "active_products" integer not null default '0',
  "out_of_stock_products" integer not null default '0',
  "products_views" integer not null default '0',
  "conversion_rate" numeric not null default '0',
  "unique_customers" integer not null default '0',
  "new_customers" integer not null default '0',
  "returning_customers" integer not null default '0',
  "total_reviews" integer not null default '0',
  "average_rating" numeric not null default '0',
  "questions_answered" integer not null default '0',
  "response_time_hours" numeric not null default '0',
  "shipped_on_time" integer not null default '0',
  "late_shipments" integer not null default '0',
  "cancelled_orders" integer not null default '0',
  "returned_orders" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE UNIQUE INDEX "vendor_analytics_vendor_id_date_unique" on "vendor_analytics"(
  "vendor_id",
  "date"
);
CREATE INDEX "vendor_analytics_date_index" on "vendor_analytics"("date");
CREATE TABLE IF NOT EXISTS "orders"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "order_number" varchar not null,
  "total_price" numeric not null,
  "status" varchar not null,
  "payment_method" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "address_id" integer,
  "reordered_from_order_id" integer,
  "payment_reference" varchar,
  "payment_provider" varchar,
  "payment_status" varchar,
  "paid_at" datetime,
  foreign key("address_id") references addresses("id") on delete set null on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("reordered_from_order_id") references "orders"("id") on delete set null
);
CREATE INDEX "orders_order_number_index" on "orders"("order_number");
CREATE UNIQUE INDEX "orders_order_number_unique" on "orders"("order_number");
CREATE INDEX "orders_user_id_status_index" on "orders"("user_id", "status");
CREATE TABLE IF NOT EXISTS "product_sellers"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "vendor_id" integer not null,
  "price" numeric not null,
  "stock" integer not null default('0'),
  "dispatch_days" integer not null default('3'),
  "is_featured" tinyint(1) not null default('0'),
  "created_at" datetime,
  "updated_at" datetime,
  "shipping_type" varchar not null default('normal'),
  "variant_id" integer,
  "seller_sku" varchar,
  "sale_price" numeric,
  "free_shipping" tinyint(1) not null default '0',
  "is_buybox_winner" tinyint(1) not null default '0',
  foreign key("vendor_id") references vendors("id") on delete cascade on update no action,
  foreign key("product_id") references products("id") on delete cascade on update no action,
  foreign key("variant_id") references "product_variants"("id") on delete set null
);
CREATE UNIQUE INDEX "product_sellers_unique" on "product_sellers"(
  "product_id",
  "variant_id",
  "vendor_id"
);
CREATE INDEX "product_sellers_vendor_stock_index" on "product_sellers"(
  "vendor_id",
  "stock"
);
CREATE UNIQUE INDEX "product_variants_barcode_unique" on "product_variants"(
  "barcode"
);
CREATE TABLE IF NOT EXISTS "order_items"(
  "id" integer primary key autoincrement not null,
  "order_id" integer not null,
  "vendor_id" integer not null,
  "product_id" integer not null,
  "quantity" integer not null,
  "price" numeric not null,
  "status" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "variant_id" integer,
  "variant_snapshot" text,
  "product_seller_id" integer,
  "unit_price" numeric,
  foreign key("product_id") references products("id") on delete cascade on update no action,
  foreign key("vendor_id") references vendors("id") on delete cascade on update no action,
  foreign key("order_id") references orders("id") on delete cascade on update no action,
  foreign key("variant_id") references "product_variants"("id") on delete set null,
  foreign key("product_seller_id") references "product_sellers"("id") on delete set null
);
CREATE INDEX "order_items_order_id_vendor_id_index" on "order_items"(
  "order_id",
  "vendor_id"
);
CREATE TABLE IF NOT EXISTS "crisp_conversations"(
  "id" integer primary key autoincrement not null,
  "session_id" varchar not null,
  "website_id" varchar not null,
  "nickname" varchar,
  "crisp_user_id" varchar,
  "user_id" integer,
  "status" varchar not null default 'open',
  "last_message_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "admin_last_seen_at" datetime,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE INDEX "crisp_conversations_session_id_index" on "crisp_conversations"(
  "session_id"
);
CREATE INDEX "crisp_conversations_website_id_index" on "crisp_conversations"(
  "website_id"
);
CREATE INDEX "crisp_conversations_status_index" on "crisp_conversations"(
  "status"
);
CREATE UNIQUE INDEX "crisp_conversations_session_id_unique" on "crisp_conversations"(
  "session_id"
);
CREATE TABLE IF NOT EXISTS "crisp_messages"(
  "id" integer primary key autoincrement not null,
  "conversation_id" integer not null,
  "from" varchar not null,
  "content" text not null,
  "type" varchar not null default 'text',
  "timestamp" datetime not null,
  "metadata" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("conversation_id") references "crisp_conversations"("id") on delete cascade
);
CREATE INDEX "crisp_messages_conversation_id_index" on "crisp_messages"(
  "conversation_id"
);
CREATE INDEX "crisp_messages_timestamp_index" on "crisp_messages"("timestamp");
CREATE INDEX "crisp_messages_from_index" on "crisp_messages"("from");
CREATE INDEX "payments_checkout_token_index" on "payments"("checkout_token");
CREATE INDEX "shipments_cargo_reference_id_index" on "shipments"(
  "cargo_reference_id"
);
CREATE TABLE IF NOT EXISTS "payment_gateway_settings"(
  "id" integer primary key autoincrement not null,
  "provider" varchar not null,
  "display_name" varchar not null,
  "is_active" tinyint(1) not null default '0',
  "is_test_mode" tinyint(1) not null default '1',
  "credentials" text,
  "configuration" text,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "payment_gateway_settings_provider_unique" on "payment_gateway_settings"(
  "provider"
);
CREATE TABLE IF NOT EXISTS "commissions"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "order_item_id" integer not null,
  "commission_rate" numeric not null,
  "commission_amount" numeric not null,
  "net_amount" numeric not null,
  "status" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "payment_id" integer,
  "gross_amount" numeric,
  "currency" varchar not null default 'TRY',
  "refunded_amount" numeric,
  "settled_at" datetime,
  foreign key("order_item_id") references order_items("id") on delete cascade on update no action,
  foreign key("vendor_id") references vendors("id") on delete cascade on update no action,
  foreign key("payment_id") references "payments"("id") on delete set null
);
CREATE INDEX "commissions_vendor_id_status_index" on "commissions"(
  "vendor_id",
  "status"
);
CREATE TABLE IF NOT EXISTS "badge_definitions"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "label" varchar not null,
  "icon" varchar,
  "color" varchar not null default 'blue',
  "bg_color" varchar,
  "text_color" varchar,
  "priority" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "badge_definitions_key_is_active_index" on "badge_definitions"(
  "key",
  "is_active"
);
CREATE INDEX "badge_definitions_priority_index" on "badge_definitions"(
  "priority"
);
CREATE UNIQUE INDEX "badge_definitions_key_unique" on "badge_definitions"(
  "key"
);
CREATE TABLE IF NOT EXISTS "badge_translations"(
  "id" integer primary key autoincrement not null,
  "badge_definition_id" integer not null,
  "category_group_id" integer not null,
  "label" varchar not null,
  "icon" varchar,
  "color" varchar,
  "bg_color" varchar,
  "text_color" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("badge_definition_id") references "badge_definitions"("id") on delete cascade,
  foreign key("category_group_id") references "category_groups"("id") on delete cascade
);
CREATE UNIQUE INDEX "badge_translations_badge_definition_id_category_group_id_unique" on "badge_translations"(
  "badge_definition_id",
  "category_group_id"
);
CREATE TABLE IF NOT EXISTS "category_groups"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "name" varchar not null,
  "icon" varchar,
  "color" varchar,
  "metadata" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "category_groups_key_is_active_index" on "category_groups"(
  "key",
  "is_active"
);
CREATE UNIQUE INDEX "category_groups_key_unique" on "category_groups"("key");
CREATE TABLE IF NOT EXISTS "attribute_sets"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "name" varchar not null,
  "category_group_id" integer,
  "description" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("category_group_id") references "category_groups"("id") on delete set null
);
CREATE INDEX "attribute_sets_key_is_active_index" on "attribute_sets"(
  "key",
  "is_active"
);
CREATE UNIQUE INDEX "attribute_sets_key_unique" on "attribute_sets"("key");
CREATE TABLE IF NOT EXISTS "category_attribute_set"(
  "id" integer primary key autoincrement not null,
  "category_id" integer not null,
  "attribute_set_id" integer not null,
  "is_required" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("category_id") references "categories"("id") on delete cascade,
  foreign key("attribute_set_id") references "attribute_sets"("id") on delete cascade
);
CREATE UNIQUE INDEX "category_attribute_set_category_id_attribute_set_id_unique" on "category_attribute_set"(
  "category_id",
  "attribute_set_id"
);
CREATE TABLE IF NOT EXISTS "badge_rules"(
  "id" integer primary key autoincrement not null,
  "badge_definition_id" integer not null,
  "category_group_id" integer,
  "condition_type" varchar not null,
  "condition_config" text not null,
  "priority" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("badge_definition_id") references "badge_definitions"("id") on delete cascade,
  foreign key("category_group_id") references "category_groups"("id") on delete set null
);
CREATE INDEX "badge_rules_badge_definition_id_category_group_id_is_active_index" on "badge_rules"(
  "badge_definition_id",
  "category_group_id",
  "is_active"
);
CREATE INDEX "badge_rules_condition_type_index" on "badge_rules"(
  "condition_type"
);
CREATE TABLE IF NOT EXISTS "attribute_highlights"(
  "id" integer primary key autoincrement not null,
  "attribute_id" integer not null,
  "category_group_id" integer not null,
  "display_label" varchar,
  "icon" varchar,
  "color" varchar,
  "priority" integer not null default '0',
  "show_in_pdp" tinyint(1) not null default '1',
  "show_in_list" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("attribute_id") references "attributes"("id") on delete cascade,
  foreign key("category_group_id") references "category_groups"("id") on delete cascade
);
CREATE UNIQUE INDEX "attribute_highlights_attribute_id_category_group_id_unique" on "attribute_highlights"(
  "attribute_id",
  "category_group_id"
);
CREATE INDEX "attribute_highlights_category_group_id_priority_index" on "attribute_highlights"(
  "category_group_id",
  "priority"
);
CREATE TABLE IF NOT EXISTS "attributes"(
  "id" integer primary key autoincrement not null,
  "attribute_set_id" integer not null,
  "key" varchar not null,
  "label" varchar not null,
  "type" varchar not null default 'select',
  "options" text,
  "unit" varchar,
  "is_filterable" tinyint(1) not null default '0',
  "is_required" tinyint(1) not null default '0',
  "order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("attribute_set_id") references "attribute_sets"("id") on delete cascade
);
CREATE INDEX "attributes_attribute_set_id_is_active_index" on "attributes"(
  "attribute_set_id",
  "is_active"
);
CREATE INDEX "attributes_is_filterable_is_active_index" on "attributes"(
  "is_filterable",
  "is_active"
);
CREATE TABLE IF NOT EXISTS "product_attribute_values"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "attribute_id" integer not null,
  "value" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade,
  foreign key("attribute_id") references "attributes"("id") on delete cascade
);
CREATE UNIQUE INDEX "product_attribute_values_product_id_attribute_id_unique" on "product_attribute_values"(
  "product_id",
  "attribute_id"
);
CREATE INDEX "product_attribute_values_attribute_id_value_index" on "product_attribute_values"(
  "attribute_id",
  "value"
);
CREATE TABLE IF NOT EXISTS "filter_configs"(
  "id" integer primary key autoincrement not null,
  "category_group_id" integer not null,
  "filter_type" varchar not null,
  "attribute_id" integer,
  "display_label" varchar not null,
  "filter_component" varchar not null default 'checkbox',
  "order" integer not null default '0',
  "is_collapsed" tinyint(1) not null default '0',
  "show_count" tinyint(1) not null default '1',
  "config" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("category_group_id") references "category_groups"("id") on delete cascade,
  foreign key("attribute_id") references "attributes"("id") on delete set null
);
CREATE INDEX "filter_configs_category_group_id_filter_type_is_active_index" on "filter_configs"(
  "category_group_id",
  "filter_type",
  "is_active"
);
CREATE INDEX "filter_configs_order_index" on "filter_configs"("order");
CREATE TABLE IF NOT EXISTS "pdp_layouts"(
  "id" integer primary key autoincrement not null,
  "category_group_id" integer not null,
  "name" varchar not null,
  "layout_config" text not null,
  "is_default" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("category_group_id") references "category_groups"("id") on delete cascade
);
CREATE INDEX "pdp_layouts_category_group_id_is_active_index" on "pdp_layouts"(
  "category_group_id",
  "is_active"
);
CREATE TABLE IF NOT EXISTS "pdp_blocks"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "name" varchar not null,
  "component" varchar not null,
  "type" varchar not null default 'static',
  "default_props" text,
  "allowed_positions" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "pdp_blocks_key_is_active_index" on "pdp_blocks"(
  "key",
  "is_active"
);
CREATE UNIQUE INDEX "pdp_blocks_key_unique" on "pdp_blocks"("key");
CREATE TABLE IF NOT EXISTS "product_badge_snapshots"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "badge_definition_id" integer not null,
  "label" varchar not null,
  "icon" varchar,
  "color" varchar,
  "bg_color" varchar,
  "text_color" varchar,
  "priority" integer not null default '0',
  "calculated_at" datetime not null,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade,
  foreign key("badge_definition_id") references "badge_definitions"("id") on delete cascade
);
CREATE UNIQUE INDEX "product_badge_snapshots_product_id_badge_definition_id_unique" on "product_badge_snapshots"(
  "product_id",
  "badge_definition_id"
);
CREATE INDEX "product_badge_snapshots_product_id_priority_index" on "product_badge_snapshots"(
  "product_id",
  "priority"
);
CREATE INDEX "product_badge_snapshots_expires_at_index" on "product_badge_snapshots"(
  "expires_at"
);
CREATE TABLE IF NOT EXISTS "social_proof_rules"(
  "id" integer primary key autoincrement not null,
  "category_group_id" integer not null,
  "type" varchar not null,
  "display_format" varchar not null,
  "threshold_type" varchar not null default 'fixed',
  "threshold_value" integer not null default '0',
  "refresh_interval" integer not null default '300',
  "position" varchar not null default 'under_title',
  "color" varchar,
  "icon" varchar,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("category_group_id") references "category_groups"("id") on delete cascade
);
CREATE INDEX "social_proof_rules_category_group_id_type_is_active_index" on "social_proof_rules"(
  "category_group_id",
  "type",
  "is_active"
);
CREATE TABLE IF NOT EXISTS "categories"(
  "id" integer primary key autoincrement not null,
  "parent_id" integer,
  "name" varchar not null,
  "slug" varchar not null,
  "icon" varchar,
  "image" varchar,
  "order" integer not null default('0'),
  "created_at" datetime,
  "updated_at" datetime,
  "meta_title" varchar,
  "meta_description" text,
  "meta_keywords" varchar,
  "header_text" text,
  "footer_text" text,
  "canonical_url" varchar,
  "category_group_id" integer,
  "commission_rate" numeric,
  "is_active" tinyint(1) not null default '1',
  foreign key("parent_id") references categories("id") on delete set null on update no action,
  foreign key("category_group_id") references "category_groups"("id") on delete set null
);
CREATE INDEX "categories_canonical_url_index" on "categories"("canonical_url");
CREATE INDEX "categories_parent_id_order_index" on "categories"(
  "parent_id",
  "order"
);
CREATE UNIQUE INDEX "categories_slug_unique" on "categories"("slug");
CREATE INDEX "categories_category_group_id_is_active_index" on "categories"(
  "category_group_id",
  "is_active"
);
CREATE TABLE IF NOT EXISTS "products"(
  "id" integer primary key autoincrement not null,
  "brand_id" integer,
  "category_id" integer not null,
  "title" varchar not null,
  "slug" varchar not null,
  "description" text,
  "rating" numeric not null default('0'),
  "reviews_count" integer not null default('0'),
  "is_active" tinyint(1) not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "price" numeric,
  "discount_price" numeric,
  "short_description" text,
  "additional_information" text,
  "safety_information" text,
  "manufacturer_name" varchar,
  "manufacturer_address" text,
  "manufacturer_contact" varchar,
  "responsible_party_name" varchar,
  "responsible_party_address" text,
  "responsible_party_contact" varchar,
  "vendor_id" integer,
  "additional_info" text,
  "safety_info" text,
  "is_bestseller" tinyint(1) not null default('0'),
  "is_new" tinyint(1) not null default('0'),
  "tags" text,
  "original_price" numeric,
  "stock" integer not null default('0'),
  "currency" varchar not null default('TRY'),
  "shipping_time" integer,
  "meta_title" varchar,
  "meta_description" text,
  "meta_keywords" varchar,
  "canonical_url" varchar,
  "og_title" varchar,
  "og_description" text,
  "og_image" varchar,
  "barcode" varchar,
  "model_code" varchar,
  "category_group_id" integer,
  "attribute_set_id" integer,
  "custom_commission_rate" numeric,
  foreign key("vendor_id") references vendors("id") on delete set null on update no action,
  foreign key("brand_id") references brands("id") on delete set null on update no action,
  foreign key("category_id") references categories("id") on delete cascade on update no action,
  foreign key("category_group_id") references "category_groups"("id") on delete set null,
  foreign key("attribute_set_id") references "attribute_sets"("id") on delete set null
);
CREATE UNIQUE INDEX "products_barcode_unique" on "products"("barcode");
CREATE INDEX "products_canonical_url_index" on "products"("canonical_url");
CREATE INDEX "products_category_id_is_active_rating_index" on "products"(
  "category_id",
  "is_active",
  "rating"
);
CREATE INDEX "products_slug_index" on "products"("slug");
CREATE UNIQUE INDEX "products_slug_unique" on "products"("slug");
CREATE INDEX "products_vendor_id_index" on "products"("vendor_id");
CREATE INDEX "products_category_group_id_is_active_index" on "products"(
  "category_group_id",
  "is_active"
);
CREATE TABLE IF NOT EXISTS "collections"(
  "id" integer primary key autoincrement not null,
  "vendor_id" integer not null,
  "title" varchar not null,
  "subtitle" varchar,
  "cta" varchar,
  "start_date" date,
  "end_date" date,
  "layout_type" varchar not null default 'grid',
  "is_active" tinyint(1) not null default '1',
  "order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "badge" varchar,
  "discount_text" varchar,
  foreign key("vendor_id") references "vendors"("id") on delete cascade
);
CREATE INDEX "collections_vendor_id_is_active_index" on "collections"(
  "vendor_id",
  "is_active"
);
CREATE INDEX "collections_is_active_start_date_end_date_index" on "collections"(
  "is_active",
  "start_date",
  "end_date"
);
CREATE TABLE IF NOT EXISTS "collection_items"(
  "id" integer primary key autoincrement not null,
  "collection_id" integer not null,
  "product_id" integer not null,
  "order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("collection_id") references "collections"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE UNIQUE INDEX "collection_items_collection_id_product_id_unique" on "collection_items"(
  "collection_id",
  "product_id"
);
CREATE INDEX "collection_items_collection_id_order_index" on "collection_items"(
  "collection_id",
  "order"
);
CREATE TABLE IF NOT EXISTS "home_sections"(
  "id" integer primary key autoincrement not null,
  "position" integer not null default '0',
  "section_type" varchar not null,
  "config_json" text not null,
  "is_active" tinyint(1) not null default '1',
  "start_date" datetime,
  "end_date" datetime,
  "visible_for" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "home_sections_is_active_position_index" on "home_sections"(
  "is_active",
  "position"
);
CREATE INDEX "home_sections_is_active_start_date_end_date_index" on "home_sections"(
  "is_active",
  "start_date",
  "end_date"
);
CREATE TABLE IF NOT EXISTS "filter_counts"(
  "id" integer primary key autoincrement not null,
  "category_id" integer not null,
  "filter_key" varchar not null,
  "filter_value" varchar not null,
  "count" integer not null default '0',
  "calculated_at" datetime not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("category_id") references "categories"("id") on delete cascade
);
CREATE UNIQUE INDEX "filter_counts_category_id_filter_key_filter_value_unique" on "filter_counts"(
  "category_id",
  "filter_key",
  "filter_value"
);
CREATE INDEX "filter_counts_category_id_filter_key_index" on "filter_counts"(
  "category_id",
  "filter_key"
);
CREATE TABLE IF NOT EXISTS "cart_modal_layouts"(
  "id" integer primary key autoincrement not null,
  "category_group_id" integer not null,
  "name" varchar not null,
  "layout_config" text not null,
  "rules" text,
  "is_default" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("category_group_id") references "category_groups"("id") on delete cascade
);
CREATE INDEX "cart_modal_layouts_category_group_id_is_active_is_default_index" on "cart_modal_layouts"(
  "category_group_id",
  "is_active",
  "is_default"
);
CREATE TABLE IF NOT EXISTS "cart_modal_blocks"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "name" varchar not null,
  "component" varchar not null,
  "type" varchar check("type" in('static', 'dynamic', 'conditional')) not null default 'static',
  "default_props" text,
  "validation_rules" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "cart_modal_blocks_key_unique" on "cart_modal_blocks"(
  "key"
);
CREATE TABLE IF NOT EXISTS "login_histories"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "ip_address" varchar not null,
  "user_agent" varchar,
  "device_type" varchar,
  "browser" varchar,
  "os" varchar,
  "country" varchar,
  "city" varchar,
  "is_suspicious" tinyint(1) not null default '0',
  "is_new_location" tinyint(1) not null default '0',
  "is_new_device" tinyint(1) not null default '0',
  "logged_in_at" datetime not null,
  "logged_out_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "login_histories_user_id_logged_in_at_index" on "login_histories"(
  "user_id",
  "logged_in_at"
);
CREATE INDEX "login_histories_ip_address_logged_in_at_index" on "login_histories"(
  "ip_address",
  "logged_in_at"
);
CREATE INDEX "login_histories_is_suspicious_index" on "login_histories"(
  "is_suspicious"
);
CREATE INDEX "login_histories_ip_address_index" on "login_histories"(
  "ip_address"
);
CREATE TABLE IF NOT EXISTS "search_tags"(
  "id" integer primary key autoincrement not null,
  "label" varchar not null,
  "url" varchar not null,
  "order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2026_01_12_223855_create_vendor_tiers_table',1);
INSERT INTO migrations VALUES(5,'2026_01_12_223856_create_vendors_table',1);
INSERT INTO migrations VALUES(6,'2026_01_12_223906_create_vendor_scores_table',1);
INSERT INTO migrations VALUES(7,'2026_01_12_223907_create_brands_table',1);
INSERT INTO migrations VALUES(8,'2026_01_12_223907_create_categories_table',1);
INSERT INTO migrations VALUES(9,'2026_01_12_223907_create_products_table',1);
INSERT INTO migrations VALUES(10,'2026_01_12_223907_create_vendor_badges_table',1);
INSERT INTO migrations VALUES(11,'2026_01_12_223907_create_vendor_penalties_table',1);
INSERT INTO migrations VALUES(12,'2026_01_12_223921_create_product_attributes_table',1);
INSERT INTO migrations VALUES(13,'2026_01_12_223921_create_product_images_table',1);
INSERT INTO migrations VALUES(14,'2026_01_12_223921_create_product_reviews_table',1);
INSERT INTO migrations VALUES(15,'2026_01_12_223921_create_product_variants_table',1);
INSERT INTO migrations VALUES(16,'2026_01_12_223921_create_product_vendors_table',1);
INSERT INTO migrations VALUES(17,'2026_01_12_223921_create_seller_reviews_table',1);
INSERT INTO migrations VALUES(18,'2026_01_12_223922_create_product_questions_table',1);
INSERT INTO migrations VALUES(19,'2026_01_12_223928_create_carts_table',1);
INSERT INTO migrations VALUES(20,'2026_01_12_223929_create_cart_items_table',1);
INSERT INTO migrations VALUES(21,'2026_01_12_223929_create_favorites_table',1);
INSERT INTO migrations VALUES(22,'2026_01_12_223930_create_addresses_table',1);
INSERT INTO migrations VALUES(23,'2026_01_12_223930_create_orders_table',1);
INSERT INTO migrations VALUES(24,'2026_01_12_223931_create_order_items_table',1);
INSERT INTO migrations VALUES(25,'2026_01_12_223932_create_shipments_table',1);
INSERT INTO migrations VALUES(26,'2026_01_12_224005_create_campaigns_table',1);
INSERT INTO migrations VALUES(27,'2026_01_12_224005_create_product_campaigns_table',1);
INSERT INTO migrations VALUES(28,'2026_01_12_224005_create_recently_vieweds_table',1);
INSERT INTO migrations VALUES(29,'2026_01_12_224005_create_search_logs_table',1);
INSERT INTO migrations VALUES(30,'2026_01_12_224012_create_commissions_table',1);
INSERT INTO migrations VALUES(31,'2026_01_12_224012_create_payments_table',1);
INSERT INTO migrations VALUES(32,'2026_01_12_224012_create_refunds_table',1);
INSERT INTO migrations VALUES(33,'2026_01_12_224012_create_vendor_balances_table',1);
INSERT INTO migrations VALUES(34,'2026_01_12_224012_create_vendor_payouts_table',1);
INSERT INTO migrations VALUES(35,'2026_01_12_224020_create_product_returns_table',1);
INSERT INTO migrations VALUES(36,'2026_01_12_224020_create_search_indices_table',1);
INSERT INTO migrations VALUES(37,'2026_01_12_224020_create_vendor_performance_logs_table',1);
INSERT INTO migrations VALUES(38,'2026_01_12_224021_create_activity_logs_table',1);
INSERT INTO migrations VALUES(39,'2026_01_12_224021_create_disputes_table',1);
INSERT INTO migrations VALUES(40,'2026_01_12_224021_create_product_stats_table',1);
INSERT INTO migrations VALUES(41,'2026_01_12_224021_create_translations_table',1);
INSERT INTO migrations VALUES(42,'2026_01_13_090941_create_review_helpful_votes_table',1);
INSERT INTO migrations VALUES(43,'2026_01_13_090941_create_review_images_table',1);
INSERT INTO migrations VALUES(44,'2026_01_13_090942_create_coupon_usages_table',1);
INSERT INTO migrations VALUES(45,'2026_01_13_090942_create_coupons_table',1);
INSERT INTO migrations VALUES(46,'2026_01_13_090942_create_wishlist_items_table',1);
INSERT INTO migrations VALUES(47,'2026_01_13_090942_create_wishlists_table',1);
INSERT INTO migrations VALUES(48,'2026_01_13_090951_create_notification_settings_table',1);
INSERT INTO migrations VALUES(49,'2026_01_13_090951_create_price_alerts_table',1);
INSERT INTO migrations VALUES(50,'2026_01_13_090951_create_stock_alerts_table',1);
INSERT INTO migrations VALUES(51,'2026_01_13_090952_create_blog_posts_table',1);
INSERT INTO migrations VALUES(52,'2026_01_13_090952_create_contact_messages_table',1);
INSERT INTO migrations VALUES(53,'2026_01_13_090952_create_hero_slides_table',1);
INSERT INTO migrations VALUES(54,'2026_01_13_090952_create_search_histories_table',1);
INSERT INTO migrations VALUES(55,'2026_01_13_090952_create_static_pages_table',1);
INSERT INTO migrations VALUES(56,'2026_01_13_090952_create_vendor_followers_table',1);
INSERT INTO migrations VALUES(57,'2026_01_13_120951_create_notifications_table',1);
INSERT INTO migrations VALUES(58,'2026_01_13_120951_create_return_images_table',1);
INSERT INTO migrations VALUES(59,'2026_01_13_161222_add_vendor_application_fields_to_vendors_table',1);
INSERT INTO migrations VALUES(60,'2026_01_13_164501_add_role_to_users_table',1);
INSERT INTO migrations VALUES(61,'2026_01_13_180856_add_two_factor_columns_to_users_table',1);
INSERT INTO migrations VALUES(62,'2026_01_13_190649_add_image_fields_to_campaigns_table',1);
INSERT INTO migrations VALUES(63,'2026_01_13_191151_remove_image_add_slug_to_campaigns_table',1);
INSERT INTO migrations VALUES(64,'2026_01_14_181348_add_alt_to_product_images_table',1);
INSERT INTO migrations VALUES(65,'2026_01_14_181529_create_pdp_feature_tables',1);
INSERT INTO migrations VALUES(66,'2026_01_14_183534_create_shipping_locations_table',1);
INSERT INTO migrations VALUES(67,'2026_01_14_184829_create_user_account_features_tables',1);
INSERT INTO migrations VALUES(68,'2026_01_14_190024_add_cargo_tracking_system',1);
INSERT INTO migrations VALUES(69,'2026_01_14_202026_create_shipment_events_table',1);
INSERT INTO migrations VALUES(70,'2026_01_14_202201_add_tracking_fields_to_shipments_table',1);
INSERT INTO migrations VALUES(71,'2026_01_14_202249_add_log_fields_to_notifications_table',1);
INSERT INTO migrations VALUES(72,'2026_01_14_210000_create_quick_links_table',1);
INSERT INTO migrations VALUES(73,'2026_01_14_210500_add_relations_to_quick_links_table',1);
INSERT INTO migrations VALUES(74,'2026_01_14_215244_add_link_type_to_quick_links_table',1);
INSERT INTO migrations VALUES(75,'2026_01_14_230216_create_product_banners_table',1);
INSERT INTO migrations VALUES(76,'2026_01_14_230903_create_product_badges_table',1);
INSERT INTO migrations VALUES(77,'2026_01_14_230911_create_product_features_table',1);
INSERT INTO migrations VALUES(78,'2026_01_14_231436_create_product_blocks_table',1);
INSERT INTO migrations VALUES(79,'2026_01_14_231437_create_block_contents_table',1);
INSERT INTO migrations VALUES(80,'2026_01_14_231437_create_block_rules_table',1);
INSERT INTO migrations VALUES(81,'2026_01_14_231507_add_pricing_to_products_table',1);
INSERT INTO migrations VALUES(82,'2026_01_14_233437_create_cities_table',1);
INSERT INTO migrations VALUES(83,'2026_01_14_233437_create_districts_table',1);
INSERT INTO migrations VALUES(84,'2026_01_14_234642_create_product_safety_images_table',1);
INSERT INTO migrations VALUES(85,'2026_01_14_234712_create_product_safety_documents_table',1);
INSERT INTO migrations VALUES(86,'2026_01_14_234720_add_product_detail_fields_to_products_table',1);
INSERT INTO migrations VALUES(87,'2026_01_15_083059_add_price_and_weight_to_product_variants_table',1);
INSERT INTO migrations VALUES(88,'2026_01_15_084144_create_category_vendor_table',1);
INSERT INTO migrations VALUES(89,'2026_01_15_084150_add_vendor_id_to_campaigns_table',1);
INSERT INTO migrations VALUES(90,'2026_01_15_084230_add_vendor_id_to_product_questions_table',1);
INSERT INTO migrations VALUES(91,'2026_01_15_085035_add_vendor_response_to_product_reviews_table',1);
INSERT INTO migrations VALUES(92,'2026_01_15_094309_add_vendor_id_to_brands_table',1);
INSERT INTO migrations VALUES(93,'2026_01_15_101800_add_fields_to_coupons_table',1);
INSERT INTO migrations VALUES(94,'2026_01_15_101806_create_product_question_categories_table',1);
INSERT INTO migrations VALUES(95,'2026_01_15_101855_create_category_question_category_table',1);
INSERT INTO migrations VALUES(96,'2026_01_15_101942_add_fields_to_coupon_usages_table',1);
INSERT INTO migrations VALUES(97,'2026_01_15_102019_add_category_id_to_product_questions_table',1);
INSERT INTO migrations VALUES(98,'2026_01_15_103330_create_brand_vendor_table',1);
INSERT INTO migrations VALUES(99,'2026_01_15_114547_add_vendor_id_to_product_faqs_table',1);
INSERT INTO migrations VALUES(100,'2026_01_15_120818_create_shipping_companies_table',1);
INSERT INTO migrations VALUES(101,'2026_01_15_120929_add_shipping_company_and_relationships_to_shipments_table',1);
INSERT INTO migrations VALUES(102,'2026_01_15_131018_add_fields_to_return_images_table',1);
INSERT INTO migrations VALUES(103,'2026_01_15_131353_add_fields_to_review_helpful_votes_table',1);
INSERT INTO migrations VALUES(104,'2026_01_15_131453_add_fields_to_wishlist_items_table',1);
INSERT INTO migrations VALUES(105,'2026_01_15_131936_add_fields_to_wishlists_table',1);
INSERT INTO migrations VALUES(106,'2026_01_15_132608_add_vendor_and_type_to_addresses_table',1);
INSERT INTO migrations VALUES(107,'2026_01_15_143947_update_alerts_and_content_tables',1);
INSERT INTO migrations VALUES(108,'2026_01_15_155339_update_notification_settings_table',1);
INSERT INTO migrations VALUES(109,'2026_01_15_185507_add_vendor_id_to_products_table',1);
INSERT INTO migrations VALUES(110,'2026_01_15_190616_add_fields_to_product_returns_table',1);
INSERT INTO migrations VALUES(111,'2026_01_15_212033_add_additional_info_and_safety_info_to_products_table',1);
INSERT INTO migrations VALUES(112,'2026_01_15_213944_add_bestseller_and_new_fields_to_products_table',1);
INSERT INTO migrations VALUES(113,'2026_01_15_221244_add_tags_to_products_table',1);
INSERT INTO migrations VALUES(114,'2026_01_15_235211_create_personal_access_tokens_table',1);
INSERT INTO migrations VALUES(115,'2026_01_16_000001_add_pricing_fields_to_products_table',1);
INSERT INTO migrations VALUES(116,'2026_01_16_000002_add_pricing_fields_to_product_variants_table',1);
INSERT INTO migrations VALUES(117,'2026_01_16_000003_rename_product_vendors_to_product_sellers',1);
INSERT INTO migrations VALUES(118,'2026_01_16_000004_rename_cart_items_product_vendor_to_product_seller',1);
INSERT INTO migrations VALUES(119,'2026_01_16_000005_add_extra_delivery_days_to_districts_table',1);
INSERT INTO migrations VALUES(120,'2026_01_16_000006_create_product_videos_table',1);
INSERT INTO migrations VALUES(121,'2026_01_16_091912_add_full_name_phone_neighborhood_to_addresses_table',1);
INSERT INTO migrations VALUES(122,'2026_01_16_121349_add_address_id_and_user_id_to_shipping_rules_table',1);
INSERT INTO migrations VALUES(123,'2026_01_16_130617_add_kyc_fields_to_vendors_table',1);
INSERT INTO migrations VALUES(124,'2026_01_16_130620_create_vendor_documents_table',1);
INSERT INTO migrations VALUES(125,'2026_01_16_130640_add_coordinates_to_shipments_table',1);
INSERT INTO migrations VALUES(126,'2026_01_16_130644_add_coordinates_to_addresses_table',1);
INSERT INTO migrations VALUES(127,'2026_01_16_131617_enhance_payments_table_for_split_payment',1);
INSERT INTO migrations VALUES(128,'2026_01_16_131824_create_invoices_table',1);
INSERT INTO migrations VALUES(129,'2026_01_16_132306_create_cargo_integrations_table',1);
INSERT INTO migrations VALUES(130,'2026_01_16_132306_enhance_shipping_companies_table',1);
INSERT INTO migrations VALUES(131,'2026_01_16_132440_create_product_import_logs_table',1);
INSERT INTO migrations VALUES(132,'2026_01_16_132641_create_vendor_sla_metrics_table',1);
INSERT INTO migrations VALUES(133,'2026_01_16_132855_enhance_disputes_table',1);
INSERT INTO migrations VALUES(134,'2026_01_16_133035_add_seo_fields_to_categories_table',1);
INSERT INTO migrations VALUES(135,'2026_01_16_133035_add_seo_fields_to_products_table',1);
INSERT INTO migrations VALUES(136,'2026_01_16_133536_create_user_consents_table',1);
INSERT INTO migrations VALUES(137,'2026_01_16_133537_create_data_deletion_requests_table',1);
INSERT INTO migrations VALUES(138,'2026_01_16_134816_create_platform_revenue_reports_table',1);
INSERT INTO migrations VALUES(139,'2026_01_16_134816_create_vendor_daily_stats_table',1);
INSERT INTO migrations VALUES(140,'2026_01_16_162717_add_logo_to_seller_pages_table',1);
INSERT INTO migrations VALUES(141,'2026_01_16_164414_add_address_id_to_orders_table',1);
INSERT INTO migrations VALUES(142,'2026_01_16_165245_create_product_approvals_table',1);
INSERT INTO migrations VALUES(143,'2026_01_16_165314_create_vendor_analytics_table',1);
INSERT INTO migrations VALUES(144,'2026_01_17_144844_fix_products_sequence',1);
INSERT INTO migrations VALUES(145,'2026_01_17_145414_add_reordered_from_order_id_to_orders_table',1);
INSERT INTO migrations VALUES(146,'2026_01_17_220558_add_authorization_fields_to_brand_vendor_table',1);
INSERT INTO migrations VALUES(147,'2026_01_17_221723_update_product_sellers_for_trendyol_structure',1);
INSERT INTO migrations VALUES(148,'2026_01_17_221727_add_barcode_to_products_and_variants',1);
INSERT INTO migrations VALUES(149,'2026_01_17_222713_add_variant_id_to_order_items',1);
INSERT INTO migrations VALUES(150,'2026_01_21_134406_normalize_product_return_reasons',1);
INSERT INTO migrations VALUES(151,'2026_01_21_140225_create_crisp_conversations_table',1);
INSERT INTO migrations VALUES(152,'2026_01_21_140226_create_crisp_messages_table',1);
INSERT INTO migrations VALUES(153,'2026_01_21_201722_add_admin_last_seen_at_to_crisp_conversations_table',1);
INSERT INTO migrations VALUES(154,'2026_02_02_120214_add_callback_columns_to_payments_table',1);
INSERT INTO migrations VALUES(155,'2026_02_02_120216_add_label_columns_to_shipments_table',1);
INSERT INTO migrations VALUES(156,'2026_02_02_120222_create_payment_gateway_settings_table',1);
INSERT INTO migrations VALUES(157,'2026_02_02_130934_add_earnings_columns_to_vendor_balances_table',1);
INSERT INTO migrations VALUES(158,'2026_02_02_131059_add_payment_columns_to_commissions_table',1);
INSERT INTO migrations VALUES(159,'2026_02_02_142345_add_commission_fields_to_vendor_tiers_table',1);
INSERT INTO migrations VALUES(160,'2026_02_03_071450_create_badge_definitions_table',1);
INSERT INTO migrations VALUES(161,'2026_02_03_071449_create_category_groups_table',1);
INSERT INTO migrations VALUES(162,'2026_02_03_071451_create_attribute_sets_table',1);
INSERT INTO migrations VALUES(163,'2026_02_03_071451_create_badge_rules_table',1);
INSERT INTO migrations VALUES(164,'2026_02_03_071453_create_attribute_highlights_table',1);
INSERT INTO migrations VALUES(165,'2026_02_03_071452_create_attributes_table',1);
INSERT INTO migrations VALUES(166,'2026_02_03_071634_create_filter_configs_table',1);
INSERT INTO migrations VALUES(167,'2026_02_03_071634_create_pdp_layouts_table',1);
INSERT INTO migrations VALUES(168,'2026_02_03_071634_create_product_badges_snapshot_table',1);
INSERT INTO migrations VALUES(169,'2026_02_03_071634_create_social_proof_rules_table',1);
INSERT INTO migrations VALUES(170,'2026_02_03_071635_add_category_group_id_to_products_and_categories',1);
INSERT INTO migrations VALUES(171,'2026_01_12_224100_create_collections_table',2);
INSERT INTO migrations VALUES(172,'2026_01_12_224101_create_collection_items_table',2);
INSERT INTO migrations VALUES(173,'2026_01_12_224102_create_home_sections_table',2);
INSERT INTO migrations VALUES(174,'2026_02_03_080000_create_filter_counts_table',2);
INSERT INTO migrations VALUES(175,'2026_02_03_090000_create_cart_modal_layouts_table',2);
INSERT INTO migrations VALUES(176,'2026_01_12_224103_add_badge_and_discount_to_collections',3);
INSERT INTO migrations VALUES(177,'2026_02_05_132241_add_google_id_to_users_table',4);
INSERT INTO migrations VALUES(178,'2026_02_05_203544_add_payment_fields_to_orders_table',5);
INSERT INTO migrations VALUES(179,'2026_02_05_204000_fix_invalid_payment_providers',5);
INSERT INTO migrations VALUES(180,'2026_02_05_203952_add_commission_rate_to_categories_table',6);
INSERT INTO migrations VALUES(181,'2026_02_05_203953_add_custom_commission_rate_to_products_table',6);
INSERT INTO migrations VALUES(182,'2026_02_06_090531_create_login_histories_table',7);
INSERT INTO migrations VALUES(183,'2026_02_06_120000_add_email_verification_code_to_users_table',7);
INSERT INTO migrations VALUES(184,'2026_02_06_000001_add_settled_at_to_commissions_table',8);
INSERT INTO migrations VALUES(185,'2026_02_06_125020_add_is_active_to_categories_table',9);
INSERT INTO migrations VALUES(186,'2026_02_07_000001_create_search_tags_table',10);
