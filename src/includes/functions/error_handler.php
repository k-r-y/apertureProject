<?php
/**
 * Centralized Error Handler for Aperture
 * 
 * Features:
 * - Custom exception handling
 * - Error logging with severity levels
 * - User-friendly error pages
 * - Development vs Production modes
 */

class ErrorHandler {
    
    private static $logDir;
    private static $environment;
    
    public static function init() {
        self::$logDir = dirname(__DIR__, 3) . '/logs';
        self::$environment = $_ENV['APP_ENV'] ?? 'production';
        
        // Create logs directory if it doesn't exist
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
        
        // Set error and exception handlers
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleFatalError']);
    }
    
    /**
     * Handle PHP errors
     */
    public static function handleError($errno, $errstr, $errfile, $errline) {
        // Don't handle suppressed errors (@)
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $errorType = self::getErrorType($errno);
        
        self::log($errorType, $errstr, [
            'file' => $errfile,
            'line' => $errline,
            'type' => $errorType
        ]);
        
        // In development, show detailed errors
        if (self::$environment === 'development') {
            echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;padding:15px;margin:10px;border-radius:4px;'>";
            echo "<strong>$errorType:</strong> $errstr<br>";
            echo "<small><strong>File:</strong> $errfile<br><strong>Line:</strong> $errline</small>";
            echo "</div>";
        }
        
        return true;
    }
    
    /**
     * Handle uncaught exceptions
     */
    public static function handleException($exception) {
        self::log('EXCEPTION', $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'class' => get_class($exception)
        ]);
        
        if (self::$environment === 'development') {
            self::displayExceptionDev($exception);
        } else {
            self::displayExceptionProd($exception);
        }
    }
    
    /**
     * Handle fatal errors
     */
    public static function handleFatalError() {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::log('FATAL', $error['message'], [
                'file' => $error['file'],
                'line' => $error['line'],
                'type' => self::getErrorType($error['type'])
            ]);
            
            if (self::$environment === 'production') {
                http_response_code(500);
                include __DIR__ . '/../../error_pages/500.php';
            }
        }
    }
    
    /**
     * Log error to file
     */
    public static function log($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $logFile = self::$logDir . '/' . date('Y-m-d') . '.log';
        
        $logEntry = [
            'timestamp' => $timestamp,
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'user_id' => $_SESSION['userId'] ?? 'guest',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'url' => $_SERVER['REQUEST_URI'] ?? 'unknown'
        ];
        
        $logLine = json_encode($logEntry, JSON_UNESCAPED_SLASHES) . PHP_EOL;
        
        error_log($logLine, 3, $logFile);
    }
    
    /**
     * Display exception in development mode
     */
    private static function displayExceptionDev($exception) {
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Exception: <?= htmlspecialchars($exception->getMessage()) ?></title>
            <style>
                body { font-family: Arial, sans-serif; background: #1a1a1a; color: #fff; padding: 20px; }
                .error-container { max-width: 900px; margin: 0 auto; background: #2a2a2a; padding: 20px; border-radius: 8px; }
                .error-header { background: #dc3545; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
                .error-details { background: #3a3a3a; padding: 15px; border-radius: 4px; margin-bottom: 10px; }
                pre { background: #1a1a1a; padding: 15px; overflow-x: auto; border-radius: 4px; }
                .label { color: #d4af37; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-header">
                    <h1>⚠️ Exception Occurred</h1>
                    <p><?= htmlspecialchars($exception->getMessage()) ?></p>
                </div>
                <div class="error-details">
                    <p><span class="label">Type:</span> <?= get_class($exception) ?></p>
                    <p><span class="label">File:</span> <?= $exception->getFile() ?></p>
                    <p><span class="label">Line:</span> <?= $exception->getLine() ?></p>
                </div>
                <h3>Stack Trace:</h3>
                <pre><?= htmlspecialchars($exception->getTraceAsString()) ?></pre>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    /**
     * Display generic error in production mode
     */
    private static function displayExceptionProd($exception) {
        http_response_code(500);
        include __DIR__ . '/../../error_pages/500.php';
        exit;
    }
    
    /**
     * Get human-readable error type
     */
    private static function getErrorType($type) {
        $types = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE ERROR',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE ERROR',
            E_CORE_WARNING => 'CORE WARNING',
            E_COMPILE_ERROR => 'COMPILE ERROR',
            E_COMPILE_WARNING => 'COMPILE WARNING',
            E_USER_ERROR => 'USER ERROR',
            E_USER_WARNING => 'USER WARNING',
            E_USER_NOTICE => 'USER NOTICE',
            E_STRICT => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER DEPRECATED'
        ];
        
        return $types[$type] ?? 'UNKNOWN';
    }
}

/**
 * Custom Exception Classes
 */

class DatabaseException extends Exception {}
class ValidationException extends Exception {}
class AuthenticationException extends Exception {}
class AuthorizationException extends Exception {}
class NotFoundException extends Exception {}
class MailException extends Exception {}

?>
