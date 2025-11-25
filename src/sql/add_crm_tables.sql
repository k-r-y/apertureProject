CREATE TABLE IF NOT EXISTS `client_notes` (
  `noteID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`noteID`),
  KEY `userID` (`userID`),
  KEY `adminID` (`adminID`),
  CONSTRAINT `client_notes_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  CONSTRAINT `client_notes_ibfk_2` FOREIGN KEY (`adminID`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tags` (
  `tagID` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(50) NOT NULL,
  `tag_color` varchar(20) NOT NULL DEFAULT '#D4AF37',
  PRIMARY KEY (`tagID`),
  UNIQUE KEY `tag_name` (`tag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `client_tags` (
  `userID` int(11) NOT NULL,
  `tagID` int(11) NOT NULL,
  PRIMARY KEY (`userID`,`tagID`),
  KEY `tagID` (`tagID`),
  CONSTRAINT `client_tags_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  CONSTRAINT `client_tags_ibfk_2` FOREIGN KEY (`tagID`) REFERENCES `tags` (`tagID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `communication_logs` (
  `logID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL,
  `type` enum('email','call','meeting','message') NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `communication_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`logID`),
  KEY `userID` (`userID`),
  KEY `adminID` (`adminID`),
  CONSTRAINT `communication_logs_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  CONSTRAINT `communication_logs_ibfk_2` FOREIGN KEY (`adminID`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default tags
INSERT IGNORE INTO `tags` (`tag_name`, `tag_color`) VALUES
('VIP', '#FFD700'),
('New Client', '#4CAF50'),
('High Value', '#9C27B0'),
('Corporate', '#2196F3'),
('Wedding', '#E91E63'),
('Potential', '#FF9800');
