-- Add gallery columns to bookings table
ALTER TABLE `bookings` 
ADD COLUMN `gallery_token` VARCHAR(64) UNIQUE DEFAULT NULL,
ADD COLUMN `gallery_pin` VARCHAR(6) DEFAULT NULL,
ADD COLUMN `gallery_expiry` DATETIME DEFAULT NULL;

-- Add index for token lookup
ALTER TABLE `bookings` ADD INDEX `idx_gallery_token` (`gallery_token`);
