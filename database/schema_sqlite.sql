-- ============================================================
-- SIDRA EVENT TICKETING PLATFORM - SQLITE COMPATIBLE SCHEMA
-- ============================================================

PRAGMA foreign_keys = OFF;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT NOT NULL UNIQUE,
    `display_name` TEXT NOT NULL,
    `description` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT NOT NULL UNIQUE,
    `display_name` TEXT NOT NULL,
    `module` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
    `role_id` INTEGER NOT NULL,
    `permission_id` INTEGER NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `role_id` INTEGER NOT NULL,
    `name` TEXT NOT NULL,
    `email` TEXT NOT NULL UNIQUE,
    `phone` TEXT NULL,
    `password_hash` TEXT NOT NULL,
    `status` TEXT DEFAULT 'active' CHECK(`status` IN ('active', 'inactive')),
    `last_login_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT
);

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT NOT NULL,
    `email` TEXT NOT NULL UNIQUE,
    `phone` TEXT NOT NULL UNIQUE,
    `password_hash` TEXT NOT NULL,
    `status` TEXT DEFAULT 'active' CHECK(`status` IN ('active', 'suspended')),
    `avatar` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
    `email` TEXT NOT NULL,
    `token` TEXT NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS `idx_pwd_reset_lookup` ON `password_resets` (`email`, `token`);

DROP TABLE IF EXISTS `event_categories`;
CREATE TABLE `event_categories` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT NOT NULL,
    `slug` TEXT NOT NULL UNIQUE,
    `description` TEXT NULL,
    `icon` TEXT DEFAULT 'calendar',
    `is_active` INTEGER DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `venues`;
CREATE TABLE `venues` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT NOT NULL,
    `slug` TEXT NOT NULL UNIQUE,
    `address` TEXT NOT NULL,
    `city` TEXT NOT NULL DEFAULT 'Dhaka',
    `capacity` INTEGER NOT NULL DEFAULT 1000,
    `map_url` TEXT NULL,
    `contact_phone` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `category_id` INTEGER NOT NULL,
    `venue_id` INTEGER NOT NULL,
    `created_by` INTEGER NULL,
    `title` TEXT NOT NULL,
    `slug` TEXT NOT NULL UNIQUE,
    `summary` TEXT NOT NULL,
    `description` TEXT NOT NULL,
    `banner_image` TEXT NULL,
    `event_date` TEXT NOT NULL,
    `start_time` TEXT NOT NULL,
    `end_time` TEXT NOT NULL,
    `status` TEXT DEFAULT 'draft' CHECK(`status` IN ('draft', 'published', 'archived', 'cancelled')),
    `is_featured` INTEGER DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `event_categories` (`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
);
CREATE INDEX IF NOT EXISTS `idx_events_filter` ON `events` (`status`, `event_date`, `category_id`);

DROP TABLE IF EXISTS `ticket_types`;
CREATE TABLE `ticket_types` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `event_id` INTEGER NOT NULL,
    `name` TEXT NOT NULL,
    `description` TEXT NULL,
    `price` REAL NOT NULL DEFAULT 0.00,
    `total_quantity` INTEGER NOT NULL,
    `remaining_quantity` INTEGER NOT NULL,
    `max_per_user` INTEGER NOT NULL DEFAULT 5,
    `sales_start` DATETIME NULL,
    `sales_end` DATETIME NULL,
    `status` TEXT DEFAULT 'active' CHECK(`status` IN ('active', 'inactive', 'sold_out')),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS `idx_ticket_type_event` ON `ticket_types` (`event_id`, `status`);

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `booking_reference` TEXT NOT NULL UNIQUE,
    `customer_id` INTEGER NOT NULL,
    `event_id` INTEGER NOT NULL,
    `total_amount` REAL NOT NULL DEFAULT 0.00,
    `discount_amount` REAL NOT NULL DEFAULT 0.00,
    `final_amount` REAL NOT NULL DEFAULT 0.00,
    `status` TEXT DEFAULT 'pending_payment' CHECK(`status` IN ('pending_payment', 'payment_submitted', 'confirmed', 'cancelled', 'refunded')),
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE RESTRICT
);
CREATE INDEX IF NOT EXISTS `idx_booking_lookup` ON `bookings` (`booking_reference`, `customer_id`, `status`);

DROP TABLE IF EXISTS `booking_items`;
CREATE TABLE `booking_items` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `booking_id` INTEGER NOT NULL,
    `ticket_type_id` INTEGER NOT NULL,
    `quantity` INTEGER NOT NULL,
    `unit_price` REAL NOT NULL,
    `subtotal` REAL NOT NULL,
    FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON DELETE RESTRICT
);

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `payment_code` TEXT NOT NULL UNIQUE,
    `booking_id` INTEGER NOT NULL,
    `customer_id` INTEGER NOT NULL,
    `payment_method` TEXT NOT NULL CHECK(`payment_method` IN ('bkash', 'nagad', 'rocket')),
    `sender_number` TEXT NOT NULL,
    `transaction_id` TEXT NOT NULL,
    `proof_image` TEXT NULL,
    `amount` REAL NOT NULL,
    `status` TEXT DEFAULT 'pending' CHECK(`status` IN ('pending', 'approved', 'rejected')),
    `rejection_reason` TEXT NULL,
    `reviewed_by` INTEGER NULL,
    `reviewed_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (`payment_method`, `transaction_id`),
    FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
);
CREATE INDEX IF NOT EXISTS `idx_payment_status` ON `payments` (`status`, `payment_method`);

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `ticket_code` TEXT NOT NULL UNIQUE,
    `verification_token` TEXT NOT NULL UNIQUE,
    `booking_id` INTEGER NOT NULL,
    `ticket_type_id` INTEGER NOT NULL,
    `customer_id` INTEGER NOT NULL,
    `attendee_name` TEXT NOT NULL,
    `attendee_email` TEXT NULL,
    `attendee_phone` TEXT NULL,
    `price` REAL NOT NULL,
    `status` TEXT DEFAULT 'valid' CHECK(`status` IN ('valid', 'used', 'cancelled')),
    `qr_code_path` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT
);
CREATE INDEX IF NOT EXISTS `idx_ticket_token` ON `tickets` (`verification_token`);
CREATE INDEX IF NOT EXISTS `idx_ticket_lookup` ON `tickets` (`ticket_code`, `status`);

DROP TABLE IF EXISTS `ticket_checkins`;
CREATE TABLE `ticket_checkins` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `ticket_id` INTEGER NOT NULL,
    `scanned_by` INTEGER NOT NULL,
    `scanned_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `gate_name` TEXT NOT NULL DEFAULT 'Main Gate',
    `result` TEXT NOT NULL DEFAULT 'valid' CHECK(`result` IN ('valid', 'duplicate', 'invalid')),
    `notes` TEXT NULL,
    FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`scanned_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
);
CREATE INDEX IF NOT EXISTS `idx_checkin_ticket` ON `ticket_checkins` (`ticket_id`);

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `user_id` INTEGER NULL,
    `action` TEXT NOT NULL,
    `entity_type` TEXT NOT NULL,
    `entity_id` INTEGER NULL,
    `old_values` TEXT NULL,
    `new_values` TEXT NULL,
    `ip_address` TEXT NULL,
    `user_agent` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
);
CREATE INDEX IF NOT EXISTS `idx_audit_user` ON `audit_logs` (`user_id`, `action`);

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `setting_key` TEXT NOT NULL UNIQUE,
    `setting_value` TEXT NULL,
    `setting_group` TEXT NOT NULL DEFAULT 'general',
    `description` TEXT NULL,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

PRAGMA foreign_keys = ON;
