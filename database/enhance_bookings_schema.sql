-- Add admin_notes to bookings table
ALTER TABLE `bookings` ADD COLUMN `admin_notes` TEXT DEFAULT NULL AFTER `booking_status`;

-- Create booking_logs table for activity tracking
CREATE TABLE `booking_logs` (
  `logID` INT AUTO_INCREMENT PRIMARY KEY,
  `bookingID` INT NOT NULL,
  `userID` INT DEFAULT NULL, -- User who performed the action (NULL for system)
  `action` VARCHAR(50) NOT NULL, -- e.g., 'status_change', 'note_added', 'payment_verified'
  `details` TEXT, -- JSON or text details about the change
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`bookingID`) REFERENCES `bookings`(`bookingID`) ON DELETE CASCADE,
  FOREIGN KEY (`userID`) REFERENCES `users`(`userID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
