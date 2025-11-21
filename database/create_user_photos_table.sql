-- Create user_photos table for storing uploaded photos
CREATE TABLE IF NOT EXISTS user_photos (
    photoID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    fileName VARCHAR(255) NOT NULL,
    originalName VARCHAR(255) NOT NULL,
    uploadedBy INT NOT NULL,
    uploadDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    caption TEXT,
    FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE,
    FOREIGN KEY (uploadedBy) REFERENCES users(userID) ON DELETE SET NULL,
    INDEX idx_userID (userID),
    INDEX idx_uploadDate (uploadDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
