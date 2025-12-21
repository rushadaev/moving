-- Add price and packing_material_id columns to materials table
-- Run this SQL on your server if migrations are not working

-- 1. Add price column
ALTER TABLE `materials`
ADD COLUMN `price` DECIMAL(8,2) NOT NULL DEFAULT 0 AFTER `quantity`;

-- 2. Create packing_materials table (if not exists)
CREATE TABLE IF NOT EXISTS `packing_materials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_full_service` tinyint(1) NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `packing_materials_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Add foreign key to materials table
ALTER TABLE `materials`
ADD COLUMN `packing_material_id` bigint unsigned NULL AFTER `request_id`,
ADD CONSTRAINT `materials_packing_material_id_foreign`
  FOREIGN KEY (`packing_material_id`)
  REFERENCES `packing_materials` (`id`)
  ON DELETE SET NULL;

-- 4. Add hourly_rate to landing_page_settings
ALTER TABLE `landing_page_settings`
ADD COLUMN `hourly_rate` DECIMAL(8,2) NOT NULL DEFAULT 100.00 AFTER `email`;

-- Done! Now your database is ready.
