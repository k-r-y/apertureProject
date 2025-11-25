<?php
/**
 * Structured Logger for Aperture
 * 
 * Provides consistent logging across the application with
 * different log levels and contexts.
 */

class Logger {
    
    const DEBUG = 'DEBUG';
    const INFO = 'INFO';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const CRITICAL = 'CRITICAL';
    
    private static $logDir;
    
    public static function init() {
        self::$logDir = dirname(__DIR__, 3) . '/logs';
        
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
    }
    
    /**
     * Log a debug message
     */
    public static function debug($message, $context = []) {
        self::log(self::DEBUG, $message, $context);
    }
    
    /**
     * Log an info message
     */
    public static function info($message, $context = []) {
        self::log(self::INFO, $message, $context);
    }
    
    /**
     * Log a warning
     */
    public static function warning($message, $context = []) {
        self::log(self::WARNING, $message, $context);
    }
    
    /**
     * Log an error
     */
    public static function error($message, $context = []) {
        self::log(self::ERROR, $message, $context);
    }
    
    /**
     * Log a critical error
     */
    public static function critical($message, $context = []) {
        self::log(self::CRITICAL, $message, $context);
    }
    
    /**
     * Log user activity
     */
    public static function activity($action, $details = []) {
        self::log('ACTIVITY', $action, array_merge($details, [
            'category' => 'user_activity'
        ]), 'activity');
    }
    
    /**
     * Log security events
     */
    public static function security($event, $details = []) {
        self::log('SECURITY', $event, array_merge($details, [
            'category' => 'security'
        ]), 'security');
    }
    
    /**
     * Core logging function
     */
    private static function log($level, $message, $context = [], $logType = 'app') {
        $timestamp = date('Y-m-d H:i:s');
        $logFile = self::$logDir . '/' . $logType . '_' . date('Y-m-d') . '.log';
        
        $logEntry = [
            'timestamp' => $timestamp,
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'user_id' => $_SESSION['userId'] ?? null,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'url' => $_SERVER['REQUEST_URI'] ?? 'cli',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI'
        ];
        
        $logLine = json_encode($logEntry, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL;
        
        error_log($logLine, 3, $logFile);
    }
    
    /**
     * Get logs for a specific date and type
     */
    public static function getLogs($date = null, $type = 'app') {
        $date = $date ?? date('Y-m-d');
        $logFile = self::$logDir . '/' . $type . '_' . $date . '.log';
        
        if (!file_exists($logFile)) {
            return [];
        }
        
        $contents = file_get_contents($logFile);
        $lines = explode(PHP_EOL, trim($contents));
        
        $logs = [];
        foreach ($lines as $line) {
            if (!empty($line)) {
                $logs[] = json_decode($line, true);
            }
        }
        
        return $logs;
    }
}

?>
