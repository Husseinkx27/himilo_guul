-- initial_schema.sql
-- HimiloGuul database schema + seed data
-- Run this file in your MySQL server (e.g., via phpMyAdmin or `mysql` CLI)

DROP DATABASE IF EXISTS `himiloGuul`;
CREATE DATABASE `himiloGuul` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `himiloGuul`;

-- ======================
-- Users table
-- ======================
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `sex` ENUM('Male','Female','Other') DEFAULT 'Other',
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `profile_picture` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('Buyer','Seller','Admin') DEFAULT 'Buyer',
  `status` ENUM('active','not_active') DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ======================
-- Businesses table
-- ======================
CREATE TABLE `businesses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `owner_id` INT NOT NULL,
  `business_name` VARCHAR(255) NOT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `description` TEXT DEFAULT NULL,
  `status` ENUM('available','sold','inactive') DEFAULT 'available',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_owner` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- FK: business owner -> users
ALTER TABLE `businesses`
  ADD CONSTRAINT `fk_business_owner` FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ======================
-- Business images (one image per row)
-- ======================
CREATE TABLE `business_images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `business_id` INT NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `alt_text` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_biz_img` (`business_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `business_images`
  ADD CONSTRAINT `fk_business_images_business` FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ======================
-- Contacts table (when a buyer contacts about a business)
-- ======================
CREATE TABLE `contacts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `business_id` INT NOT NULL,
  `from_user_id` INT DEFAULT NULL,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `status` ENUM('new','seen','resolved') DEFAULT 'new',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_contacts_business` (`business_id`),
  INDEX `idx_contacts_user` (`from_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `contacts`
  ADD CONSTRAINT `fk_contacts_business` FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_contacts_user` FOREIGN KEY (`from_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- ======================
-- Authentication tokens (for "remember me")
-- ======================
CREATE TABLE `auth_tokens` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `selector` VARCHAR(255) NOT NULL UNIQUE,
  `token_hash` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_auth_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `fk_authtokens_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ======================
-- Seed data (demo)
-- NOTE: Replace the `password` values with hashes generated from PHP's password_hash() in a secure environment.
-- Example: INSERT ... VALUES (..., '$2y$10$...'); where the hash is produced by password_hash('admin123', PASSWORD_DEFAULT);
-- ======================

-- Users (admin, seller, buyer)
INSERT INTO `users` (`first_name`,`last_name`,`sex`,`username`,`email`,`password`,`phone`,`role`,`status`)
VALUES
  ('Site','Admin','Other','admin','admin@example.com','ys3xchXGqdwMQPuNWF6.zuM/dXUWBOJzbmDAfgM8KL8oUk/TNb1OG','+0000000000','Admin','active'),
  ('Seller','One','Female','seller1','seller1@example.com','y$HSgQIYXvqt8xs7xPt.v9ueMa6yA.1FQopP3JxB6UU7knAuGk3BQIy','+1111111111','Seller','active'),
  ('Buyer','One','Male','buyer1','buyer1@example.com','y$eRDSH8r3Ne1nBPW03I8okOBzvhiT50kM0W3ndhRR4iqsoki.kULGe','+2222222222','Buyer','active');

-- Grab the seller id and create a sample business
SET @seller_id = (SELECT `id` FROM `users` WHERE `username` = 'seller1' LIMIT 1);

INSERT INTO `businesses` (`owner_id`,`business_name`,`address`,`price`,`description`,`status`)
VALUES
  (@seller_id,'Cafe Janno','123 Main St, Hargeisa',15000.00,'A charming neighborhood cafe with steady customers and equipment included.','available');

-- Add images for the business (update file_path to actual uploaded files later)
SET @biz_id = (SELECT `id` FROM `businesses` WHERE `business_name` = 'Cafe Janno' LIMIT 1);
INSERT INTO `business_images` (`business_id`,`file_path`,`alt_text`)
VALUES
  (@biz_id,'uploads/cafe_janno_1.jpg','Front view of Cafe Janno'),
  (@biz_id,'uploads/cafe_janno_2.jpg','Interior seating area');

-- Add sample contact request
INSERT INTO `contacts` (`business_id`,`from_user_id`,`name`,`email`,`phone`,`message`)
VALUES
  (@biz_id, (SELECT `id` FROM `users` WHERE `username` = 'buyer1' LIMIT 1), 'Buyer One','buyer1@example.com','+2222222222','I am interested in purchasing this cafe. Can we discuss details?');

-- Example auth_tokens row (empty example; create tokens via app logic)
-- INSERT INTO `auth_tokens` (`user_id`,`selector`,`token_hash`,`expires_at`) VALUES (1,'selector_value','token_hash_value', DATE_ADD(NOW(), INTERVAL 30 DAY));

-- Done. Please update the <REPLACE_WITH_PASSWORD_HASH> placeholders using a secure password hashing method.
-- Tip: use a small PHP script to generate a hash and then run an UPDATE statement like:
-- UPDATE `users` SET `password` = '<hash>' WHERE `username` = 'admin';

-- End of initial_schema.sql
