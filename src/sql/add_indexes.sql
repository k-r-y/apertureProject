-- Add indexes to frequently queried columns
ALTER TABLE `bookings` ADD INDEX `idx_user_booking` (`userID`, `booking_status`);
ALTER TABLE `bookings` ADD INDEX `idx_event_date` (`event_date`);
ALTER TABLE `users` ADD INDEX `idx_email` (`Email`);
ALTER TABLE `users` ADD INDEX `idx_role` (`Role`);
ALTER TABLE `invoices` ADD INDEX `idx_booking_invoice` (`bookingID`);
ALTER TABLE `invoices` ADD INDEX `idx_invoice_status` (`status`);
