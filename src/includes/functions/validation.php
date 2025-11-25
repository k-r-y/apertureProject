<?php
/**
 * Enhanced Input Validation & Sanitization Functions
 * 
 * Provides comprehensive validation and sanitization for user inputs
 * to prevent XSS, SQL injection, and other security vulnerabilities.
 * 
 * @package Aperture
 * @version 1.0
 */

/**
 * Validate email address format
 * 
 * @param string $email Email to validate
 * @return bool True if valid, false otherwise
 */
function validateEmail($email) {
    if (empty($email)) {
        return false;
    }
    
    // Check format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Check length (max 100 chars as per database schema)
    if (strlen($email) > 100) {
        return false;
    }
    
    // Check for common disposable email domains (optional)
    $disposableDomains = ['tempmail.com', 'throwaway.email', '10minutemail.com'];
    $domain = substr(strrchr($email, "@"), 1);
    if (in_array($domain, $disposableDomains)) {
        return false;
    }
    
    return true;
}

/**
 * Validate phone number (Philippines format)
 * 
 * @param string $phone Phone number to validate
 * @return bool True if valid, false otherwise
 */
function validatePhone($phone) {
    if (empty($phone)) {
        return false;
    }
    
    // Remove spaces, dashes, parentheses
    $cleaned = preg_replace('/[\s\-\(\)]/', '', $phone);
    
    // Check if it's numeric
    if (!ctype_digit($cleaned)) {
        return false;
    }
    
    // Philippines phone numbers: 10-11 digits
    // Mobile: 09XX-XXX-XXXX (11 digits)
    // Landline: (02) XXXX-XXXX (10 digits with area code)
    $length = strlen($cleaned);
    if ($length < 10 || $length > 11) {
        return false;
    }
    
    // Check if starts with 09 for mobile
    if ($length === 11 && substr($cleaned, 0, 2) !== '09') {
        return false;
    }
    
    return true;
}

/**
 * Validate name (first name, last name)
 * 
 * @param string $name Name to validate
 * @param int $minLength Minimum length (default: 2)
 * @param int $maxLength Maximum length (default: 50)
 * @return bool True if valid, false otherwise
 */
function validateName($name, $minLength = 2, $maxLength = 50) {
    if (empty($name)) {
        return false;
    }
    
    $length = strlen($name);
    if ($length < $minLength || $length > $maxLength) {
        return false;
    }
    
    // Allow letters, spaces, hyphens, apostrophes (for names like O'Brien, Mary-Jane)
    if (!preg_match("/^[a-zA-Z\s\-']+$/", $name)) {
        return false;
    }
    
    return true;
}

/**
 * Validate password strength
 * 
 * Requirements:
 * - Minimum 8 characters
 * - At least one uppercase letter
 * - At least one lowercase letter
 * - At least one number
 * - At least one special character
 * 
 * @param string $password Password to validate
 * @return array ['valid' => bool, 'errors' => array]
 */
function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = "Password must contain at least one special character (!@#$%^&*)";
    }
    
    // Check for common weak passwords
    $commonPasswords = ['password', '12345678', 'qwerty', 'abc123', 'password123'];
    if (in_array(strtolower($password), $commonPasswords)) {
        $errors[] = "This password is too common. Please choose a stronger password";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Validate date (for booking dates)
 * 
 * @param string $date Date string (Y-m-d format)
 * @param int $minDaysAhead Minimum days in advance (default: 5)
 * @param int $maxYearsAhead Maximum years in advance (default: 3)
 * @return array ['valid' => bool, 'error' => string]
 */
function validateDate($date, $minDaysAhead = 5, $maxYearsAhead = 3) {
    if (empty($date)) {
        return ['valid' => false, 'error' => 'Date is required'];
    }
    
    // Check format
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
        return ['valid' => false, 'error' => 'Invalid date format'];
    }
    
    $now = new DateTime();
    $minDate = (clone $now)->modify("+{$minDaysAhead} days");
    $maxDate = (clone $now)->modify("+{$maxYearsAhead} years");
    
    if ($dateObj < $minDate) {
        return ['valid' => false, 'error' => "Booking must be at least {$minDaysAhead} days in advance"];
    }
    
    if ($dateObj > $maxDate) {
        return ['valid' => false, 'error' => "Booking cannot be more than {$maxYearsAhead} years in advance"];
    }
    
    return ['valid' => true, 'error' => ''];
}

/**
 * Validate time (for booking times)
 * 
 * @param string $time Time string (H:i format)
 * @param string $minTime Minimum allowed time (default: 07:00)
 * @param string $maxTime Maximum allowed time (default: 22:00)
 * @return array ['valid' => bool, 'error' => string]
 */
function validateTime($time, $minTime = '07:00', $maxTime = '22:00') {
    if (empty($time)) {
        return ['valid' => false, 'error' => 'Time is required'];
    }
    
    // Check format
    $timeObj = DateTime::createFromFormat('H:i', $time);
    if (!$timeObj || $timeObj->format('H:i') !== $time) {
        return ['valid' => false, 'error' => 'Invalid time format'];
    }
    
    $minTimeObj = DateTime::createFromFormat('H:i', $minTime);
    $maxTimeObj = DateTime::createFromFormat('H:i', $maxTime);
    
    if ($timeObj < $minTimeObj || $timeObj > $maxTimeObj) {
        return ['valid' => false, 'error' => "Time must be between {$minTime} and {$maxTime}"];
    }
    
    return ['valid' => true, 'error' => ''];
}

/**
 * Validate time range (start time must be before end time)
 * 
 * @param string $startTime Start time (H:i format)
 * @param string $endTime End time (H:i format)
 * @param int $minDuration Minimum duration in hours (default: 1)
 * @return array ['valid' => bool, 'error' => string]
 */
function validateTimeRange($startTime, $endTime, $minDuration = 1) {
    $start = DateTime::createFromFormat('H:i', $startTime);
    $end = DateTime::createFromFormat('H:i', $endTime);
    
    if (!$start || !$end) {
        return ['valid' => false, 'error' => 'Invalid time format'];
    }
    
    if ($start >= $end) {
        return ['valid' => false, 'error' => 'End time must be after start time'];
    }
    
    $interval = $start->diff($end);
    $hours = $interval->h + ($interval->days * 24);
    
    if ($hours < $minDuration) {
        return ['valid' => false, 'error' => "Booking must be at least {$minDuration} hour(s)"];
    }
    
    return ['valid' => true, 'error' => ''];
}

/**
 * Sanitize string for output (prevent XSS)
 * 
 * @param string $string String to sanitize
 * @return string Sanitized string
 */
function sanitizeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate and sanitize text input
 * 
 * @param string $input Input to validate
 * @param int $maxLength Maximum length
 * @param bool $allowHTML Whether to allow HTML (default: false)
 * @return array ['valid' => bool, 'value' => string, 'error' => string]
 */
function validateTextInput($input, $maxLength = 255, $allowHTML = false) {
    if (empty($input)) {
        return ['valid' => false, 'value' => '', 'error' => 'This field is required'];
    }
    
    // Trim whitespace
    $input = trim($input);
    
    // Check length
    if (strlen($input) > $maxLength) {
        return ['valid' => false, 'value' => $input, 'error' => "Maximum {$maxLength} characters allowed"];
    }
    
    // Sanitize if HTML not allowed
    if (!$allowHTML) {
        $input = strip_tags($input);
    }
    
    return ['valid' => true, 'value' => $input, 'error' => ''];
}

/**
 * Validate file upload
 * 
 * @param array $file $_FILES array element
 * @param array $allowedTypes Allowed MIME types
 * @param int $maxSize Maximum file size in bytes
 * @return array ['valid' => bool, 'error' => string]
 */
function validateFileUpload($file, $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'], $maxSize = 5242880) {
    // Check if file was uploaded
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['valid' => false, 'error' => 'Invalid file upload'];
    }
    
    // Check for upload errors
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['valid' => false, 'error' => 'File size exceeds limit'];
        case UPLOAD_ERR_NO_FILE:
            return ['valid' => false, 'error' => 'No file uploaded'];
        default:
            return ['valid' => false, 'error' => 'Unknown upload error'];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        $maxSizeMB = $maxSize / 1048576;
        return ['valid' => false, 'error' => "File size must not exceed {$maxSizeMB}MB"];
    }
    
    // Verify MIME type using finfo (more secure than checking extension)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['valid' => false, 'error' => 'Invalid file type. Only JPG, PNG, and PDF files are allowed'];
    }
    
    // Additional check: verify file extension matches MIME type
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $validExtensions = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'application/pdf' => ['pdf']
    ];
    
    $expectedExtensions = $validExtensions[$mimeType] ?? [];
    if (!in_array($extension, $expectedExtensions)) {
        return ['valid' => false, 'error' => 'File extension does not match file type'];
    }
    
    return ['valid' => true, 'error' => ''];
}

/**
 * Generate safe filename for uploads
 * 
 * @param string $originalName Original filename
 * @return string Safe filename
 */
function generateSafeFilename($originalName) {
    // Get extension
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    
    // Generate unique name with timestamp
    $uniqueName = uniqid('upload_', true) . '_' . time();
    
    // Remove any potentially dangerous characters
    $uniqueName = preg_replace('/[^a-zA-Z0-9_]/', '', $uniqueName);
    
    return $uniqueName . '.' . $extension;
}

/**
 * Validate integer within range
 * 
 * @param mixed $value Value to validate
 * @param int $min Minimum value
 * @param int $max Maximum value
 * @return array ['valid' => bool, 'value' => int, 'error' => string]
 */
function validateInteger($value, $min = null, $max = null) {
    if (!is_numeric($value)) {
        return ['valid' => false, 'value' => 0, 'error' => 'Must be a number'];
    }
    
    $intValue = (int)$value;
    
    if ($min !== null && $intValue < $min) {
        return ['valid' => false, 'value' => $intValue, 'error' => "Must be at least {$min}"];
    }
    
    if ($max !== null && $intValue > $max) {
        return ['valid' => false, 'value' => $intValue, 'error' => "Must not exceed {$max}"];
    }
    
    return ['valid' => true, 'value' => $intValue, 'error' => ''];
}

/**
 * Validate URL
 * 
 * @param string $url URL to validate
 * @return bool True if valid, false otherwise
 */
function validateURL($url) {
    if (empty($url)) {
        return false;
    }
    
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}
