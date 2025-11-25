-- Client Notes Table
CREATE TABLE IF NOT EXISTS `client_notes` (
  `noteID` INT AUTO_INCREMENT PRIMARY KEY,
  `userID` INT NOT NULL,
  `adminID` INT NOT NULL,
  `note` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`userID`) REFERENCES `users`(`userID`) ON DELETE CASCADE,
  FOREIGN KEY (`adminID`) REFERENCES `users`(`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Client Tags Table
CREATE TABLE IF NOT EXISTS `client_tags` (
  `tagID` INT AUTO_INCREMENT PRIMARY KEY,
  `tag_name` VARCHAR(50) NOT NULL UNIQUE,
  `tag_color` VARCHAR(7) DEFAULT '#d4af37',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Tags (Many-to-Many relationship)
CREATE TABLE IF NOT EXISTS `user_tags` (
  `userID` INT NOT NULL,
  `tagID` INT NOT NULL,
  `assigned_by` INT NOT NULL,
  `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userID`, `tagID`),
  FOREIGN KEY (`userID`) REFERENCES `users`(`userID`) ON DELETE CASCADE,
  FOREIGN KEY (`tagID`) REFERENCES `client_tags`(`tagID`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_by`) REFERENCES `users`(`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Communication Log Table
CREATE TABLE IF NOT EXISTS `communication_log` (
  `logID` INT AUTO_INCREMENT PRIMARY KEY,
  `userID` INT NOT NULL,
  `adminID` INT NOT NULL,
  `type` ENUM('email', 'call', 'meeting', 'message') NOT NULL,
  `subject` VARCHAR(255),
  `notes` TEXT,
  `communication_date` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`userID`) REFERENCES `users`(`userID`) ON DELETE CASCADE,
  FOREIGN KEY (`adminID`) REFERENCES `users`(`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
