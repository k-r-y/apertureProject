-- Add consultation columns to bookings table
ALTER TABLE `bookings` 
ADD COLUMN `consultation_date` DATE DEFAULT NULL,
ADD COLUMN `consultation_time` TIME DEFAULT NULL;
