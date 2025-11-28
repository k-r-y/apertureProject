-- Add meeting_link column to bookings table if it doesn't exist
ALTER TABLE `bookings` 
ADD COLUMN `meeting_link` VARCHAR(500) NULL DEFAULT NULL AFTER `admin_notes`;
