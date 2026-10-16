-- Migration: Add status tracking to contact table
-- Run this on production database

ALTER TABLE `contact` 
ADD COLUMN `status` ENUM('new', 'replied') NOT NULL DEFAULT 'new' AFTER `product`,
ADD COLUMN `replied_at` DATETIME DEFAULT NULL AFTER `status`;

-- Index for efficient querying of pending messages
ALTER TABLE `contact` ADD INDEX `idx_status_date` (`status`, `date`);