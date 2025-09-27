-- Migration script to add profile_picture column to users_tbl
-- Run this in phpMyAdmin or MySQL command line

ALTER TABLE `users_tbl` 
ADD COLUMN `profile_picture` VARCHAR(255) NULL DEFAULT NULL 
AFTER `email`;

-- Optional: Add comment to the column
ALTER TABLE `users_tbl` 
MODIFY COLUMN `profile_picture` VARCHAR(255) NULL DEFAULT NULL 
COMMENT 'Path to user profile picture image';