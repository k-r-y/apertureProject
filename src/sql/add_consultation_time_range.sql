-- Update bookings table to support consultation start and end times
-- This replaces the single consultation_time with start and end times

ALTER TABLE `bookings` 
ADD COLUMN `consultation_start_time` TIME DEFAULT NULL AFTER `consultation_time`,
ADD COLUMN `consultation_end_time` TIME DEFAULT NULL AFTER `consultation_start_time`;

-- Optional: Comment out the old consultation_time column if you want to keep data
-- ALTER TABLE `bookings` DROP COLUMN `consultation_time`;
