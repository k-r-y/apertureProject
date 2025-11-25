<?php
/**
 * API Security & Rate Limiting Functions
 * 
 * Provides rate limiting, request throttling, and API authentication
 * to prevent abuse and ensure API security.
 * 
 * @package Aperture
 * @version 1.0
 */

/**
 * Initialize rate limiting table (run once during setup)
 * 
 * Creates the api_rate_limit table if it doesn't exist
 */
function initializeRateLimitTable() {
    global $conn;
    
    $sql = "CREATE TABLE IF NOT EXISTS api_rate_limit (
        id INT AUTO_INCREMENT PRIMARY KEY,
        identifier VARCHAR(255) NOT NULL,
        endpoint VARCHAR(255) NOT NULL,
        request_count INT DEFAULT 1,
        window_start DATETIME NOT NULL,
        last_request DATETIME NOT NULL,
        is_blocked BOOLEAN DEFAULT FALSE,
        blocked_until DATETIME NULL,
        INDEX idx_identifier (identifier),
        INDEX idx_endpoint (endpoint),
        INDEX idx_window (window_start)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci" ;
    
    $conn->query($sql);
}

/**
 * Get client identifier for rate limiting
 * 
 * Uses IP address and User Agent to create unique identifier
 * 
 * @return string Client identifier
 */
function getClientIdentifier() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    // Create hash of IP + User Agent for privacy
    return hash('sha256', $ip . '|' . $userAgent);
}

/**
 * Check if request is rate limited
 * 
 * @param string $endpoint API endpoint being accessed
 * @param int $maxRequests Maximum requests allowed per window
 * @param int $windowSeconds Time window in seconds (default: 3600 = 1 hour)
 * @return array ['allowed' => bool, 'remaining' => int, 'reset_time' => int]
 */
function checkRateLimit($endpoint, $maxRequests = 100, $windowSeconds = 3600) {
    global $conn;
    
    $identifier = getClientIdentifier();
    $now = date('Y-m-d H:i:s');
    
    // Check if client is blocked
    $blockCheck = $conn->prepare("
        SELECT blocked_until 
        FROM api_rate_limit 
        WHERE identifier = ? 
        AND endpoint = ? 
        AND is_blocked = TRUE 
        AND blocked_until > NOW()
    ");
    $blockCheck->bind_param('ss', $identifier, $endpoint);
    $blockCheck->execute();
    $blockResult = $blockCheck->get_result();
    
    if ($blockResult->num_rows > 0) {
        $row = $blockResult->fetch_assoc();
        $resetTime = strtotime($row['blocked_until']);
        $blockCheck->close();
        
        return [
            'allowed' => false,
            'remaining' => 0,
            'reset_time' => $resetTime,
            'blocked' => true,
            'message' => 'Too many requests. Please try again later.'
        ];
    }
    $blockCheck->close();
    
    // Get or create rate limit record
    $stmt = $conn->prepare("
        SELECT id, request_count, window_start 
        FROM api_rate_limit 
        WHERE identifier = ? 
        AND endpoint = ? 
        AND window_start > DATE_SUB(NOW(), INTERVAL ? SECOND)
        ORDER BY window_start DESC 
        LIMIT 1
    ");
    $stmt->bind_param('ssi', $identifier, $endpoint, $windowSeconds);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Existing window
        $row = $result->fetch_assoc();
        $recordId = $row['id'];
        $requestCount = $row['request_count'];
        $windowStart = $row['window_start'];
        $stmt->close();
        
        if ($requestCount >= $maxRequests) {
            // Rate limit exceeded - block temporarily
            $blockUntil = date('Y-m-d H:i:s', strtotime($windowStart) + $windowSeconds);
            $updateStmt = $conn->prepare("
                UPDATE api_rate_limit 
                SET is_blocked = TRUE, blocked_until = ? 
                WHERE id = ?
            ");
            $updateStmt->bind_param('si', $blockUntil, $recordId);
            $updateStmt->execute();
            $updateStmt->close();
            
            // Log the rate limit violation
            error_log("Rate limit exceeded for identifier: {$identifier} on endpoint: {$endpoint}");
            
            return [
                'allowed' => false,
                'remaining' => 0,
                'reset_time' => strtotime($blockUntil),
                'blocked' => true,
                'message' => 'Rate limit exceeded. Please try again later.'
            ];
        }
        
        // Increment request count
        $updateStmt = $conn->prepare("
            UPDATE api_rate_limit 
            SET request_count = request_count + 1, last_request = NOW() 
            WHERE id = ?
        ");
        $updateStmt->bind_param('i', $recordId);
        $updateStmt->execute();
        $updateStmt->close();
        
        $remaining = $maxRequests - ($requestCount + 1);
        $resetTime = strtotime($windowStart) + $windowSeconds;
        
    } else {
        // New window
        $stmt->close();
        $insertStmt = $conn->prepare("
            INSERT INTO api_rate_limit (identifier, endpoint, request_count, window_start, last_request) 
            VALUES (?, ?, 1, NOW(), NOW())
        ");
        $insertStmt->bind_param('ss', $identifier, $endpoint);
        $insertStmt->execute();
        $insertStmt->close();
        
        $remaining = $maxRequests - 1;
        $resetTime = time() + $windowSeconds;
    }
    
    return [
        'allowed' => true,
        'remaining' => $remaining,
        'reset_time' => $resetTime,
        'blocked' => false,
        'message' => 'Request allowed'
    ];
}

/**
 * Enforce rate limit and send appropriate headers
 * 
 * @param string $endpoint API endpoint
 * @param int $maxRequests Maximum requests per window
 * @param int $windowSeconds Time window in seconds
 * @return void Exits with 429 status if rate limited
 */
function enforceRateLimit($endpoint, $maxRequests = 100, $windowSeconds = 3600) {
    $rateLimit = checkRateLimit($endpoint, $maxRequests, $windowSeconds);
    
    // Set rate limit headers
    header('X-RateLimit-Limit: ' . $maxRequests);
    header('X-RateLimit-Remaining: ' . $rateLimit['remaining']);
    header('X-RateLimit-Reset: ' . $rateLimit['reset_time']);
    
    if (!$rateLimit['allowed']) {
        http_response_code(429); // Too Many Requests
        header('Retry-After: ' . ($rateLimit['reset_time'] - time()));
        header('Content-Type: application/json');
        
        echo json_encode([
            'error' => 'Rate limit exceeded',
            'message' => $rateLimit['message'],
            'retry_after' => $rateLimit['reset_time']
        ]);
        
        exit;
    }
}

/**
 * Clean up old rate limit records
 * 
 * Should be run periodically (e.g., daily cron job)
 * 
 * @param int $daysToKeep Number of days to keep records (default: 7)
 * @return int Number of records deleted
 */
function cleanupRateLimitRecords($daysToKeep = 7) {
    global $conn;
    
    $stmt = $conn->prepare("
        DELETE FROM api_rate_limit 
        WHERE window_start < DATE_SUB(NOW(), INTERVAL ? DAY)
    ");
    $stmt->bind_param('i', $daysToKeep);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();
    
    return $affected;
}

/**
 * Add CORS headers for API endpoints
 * 
 * @param array $allowedOrigins Array of allowed origins (default: same origin only)
 * @param array $allowedMethods Array of allowed HTTP methods
 * @param array $allowedHeaders Array of allowed headers
 */
function setCORSHeaders($allowedOrigins = [], $allowedMethods = ['GET', 'POST'], $allowedHeaders = ['Content-Type', 'Authorization']) {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    // If no specific origins defined, only allow same origin
    if (empty($allowedOrigins)) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $allowedOrigins = [$protocol . '://' . $_SERVER['HTTP_HOST']];
    }
    
    // Check if origin is allowed
    if (in_array($origin, $allowedOrigins)) {
        header('Access-Control-Allow-Origin: ' . $origin);
    }
    
    header('Access-Control-Allow-Methods: ' . implode(', ', $allowedMethods));
    header('Access-Control-Allow-Headers: ' . implode(', ', $allowedHeaders));
    header('Access-Control-Max-Age: 86400'); // 24 hours
    
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/**
 * Validate API key (for admin endpoints)
 * 
 * @param string $apiKey API key from request
 * @return bool True if valid, false otherwise
 */
function validateAPIKey($apiKey) {
    // In production, store API keys in database with hashing
    // For now, check against environment variable
    $validKey = $_ENV['API_KEY'] ?? null;
    
    if (empty($validKey)) {
        error_log('API_KEY not configured in environment');
        return false;
    }
    
    return hash_equals($validKey, $apiKey);
}

/**
 * Require API authentication for endpoint
 * 
 * Checks for API key in Authorization header or query parameter
 * 
 * @return void Exits with 401 if authentication fails
 */
function requireAPIAuth() {
    $apiKey = null;
    
    // Check Authorization header
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $matches = [];
        if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
            $apiKey = $matches[1];
        }
    }
    
    // Fallback to query parameter (less secure, but convenient for testing)
    if (empty($apiKey) && isset($_GET['api_key'])) {
        $apiKey = $_GET['api_key'];
    }
    
    if (empty($apiKey) || !validateAPIKey($apiKey)) {
        http_response_code(401); // Unauthorized
        header('Content-Type: application/json');
        
        echo json_encode([
            'error' => 'Unauthorized',
            'message' => 'Valid API key required'
        ]);
        
        exit;
    }
}

/**
 * Log API request for monitoring
 * 
 * @param string $endpoint Endpoint accessed
 * @param string $method HTTP method
 * @param int $responseCode HTTP response code
 * @param float $executionTime Execution time in seconds
 */
function logAPIRequest($endpoint, $method, $responseCode, $executionTime = 0) {
    $identifier = getClientIdentifier();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $logEntry = sprintf(
        "[%s] %s %s - IP: %s - Response: %d - Time: %.3fs - UA: %s",
        date('Y-m-d H:i:s'),
        $method,
        $endpoint,
        $ip,
        $responseCode,
        $executionTime,
        substr($userAgent, 0, 100)
    );
    
    error_log($logEntry, 3, __DIR__ . '/../../../logs/api-access.log');
}
