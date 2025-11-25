<?php
/**
 * Password History Functions
 * 
 * Manages password history to prevent password reuse
 * 
 * @package Aperture
 * @version 1.0
 */

/**
 * Add password to history
 * 
 * @param int $userID User ID
 * @param string $passwordHash Hashed password
 * @return bool True on success, false on failure
 */
function addPasswordToHistory($userID, $passwordHash) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO password_history (userID, password_hash) VALUES (?, ?)");
    $stmt->bind_param('is', $userID, $passwordHash);
    $result = $stmt->execute();
    $stmt->close();
    
    // Clean up old history (keep only last 5 passwords)
    cleanupPasswordHistory($userID, 5);
    
    return $result;
}

/**
 * Check if password was used recently
 * 
 * @param int $userID User ID
 * @param string $password Plain text password to check
 * @param int $historyCount Number of previous passwords to check (default: 5)
 * @return bool True if password was used recently, false otherwise
 */
function isPasswordInHistory($userID, $password, $historyCount = 5) {
    global $conn;
    
    // Get recent password hashes
    $stmt = $conn->prepare("
        SELECT password_hash 
        FROM password_history 
        WHERE userID = ? 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->bind_param('ii', $userID, $historyCount);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password_hash'])) {
            $stmt->close();
            return true;
        }
    }
    
    $stmt->close();
    return false;
}

/**
 * Clean up old password history
 * 
 * Keeps only the most recent N passwords
 * 
 * @param int $userID User ID
 * @param int $keepCount Number of passwords to keep (default: 5)
 * @return int Number of records deleted
 */
function cleanupPasswordHistory($userID, $keepCount = 5) {
    global $conn;
    
    // Delete all but the most recent N passwords
    $stmt = $conn->prepare("
        DELETE FROM password_history 
        WHERE userID = ? 
        AND id NOT IN (
            SELECT id FROM (
                SELECT id 
                FROM password_history 
                WHERE userID = ? 
                ORDER BY created_at DESC 
                LIMIT ?
            ) AS recent
        )
    ");
    $stmt->bind_param('iii', $userID, $userID, $keepCount);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();
    
    return $affected;
}

/**
 * Get password history count for user
 * 
 * @param int $userID User ID
 * @return int Number of passwords in history
 */
function getPasswordHistoryCount($userID) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM password_history WHERE userID = ?");
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return (int)$row['count'];
}

/**
 * Initialize password history table
 * 
 * Creates the table if it doesn't exist
 * 
 * @return bool True on success, false on failure
 */
function initializePasswordHistoryTable() {
    global $conn;
    
    $sql = "CREATE TABLE IF NOT EXISTS password_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        userID INT NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user (userID),
        INDEX idx_created (created_at),
        FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $conn->query($sql);
}

/**
 * Migrate existing user passwords to history
 * 
 * Run once to populate history with current passwords
 * 
 * @return int Number of passwords migrated
 */
function migrateExistingPasswords() {
    global $conn;
    
    $count = 0;
    
    // Get all users
    $result = $conn->query("SELECT userID, Password FROM users");
    
    while ($row = $result->fetch_assoc()) {
        $stmt = $conn->prepare("
            INSERT INTO password_history (userID, password_hash) 
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE password_hash = password_hash
        ");
        $stmt->bind_param('is', $row['userID'], $row['Password']);
        
        if ($stmt->execute()) {
            $count++;
        }
        
        $stmt->close();
    }
    
    return $count;
}
