<?php
/**
 * Simple File-Based Cache System
 * Provides fast query result caching without external dependencies
 */

class Cache {
    private static $cacheDir = __DIR__ . '/../../cache/';
    
    /**
     * Initialize cache directory
     */
    private static function init() {
        if (!file_exists(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }
        
        // Create .htaccess to prevent direct access
        $htaccess = self::$cacheDir . '.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "Deny from all");
        }
    }
    
    /**
     * Generate cache filename from key
     */
    private static function getFilename($key) {
        return self::$cacheDir . md5($key) . '.cache';
    }
    
    /**
     * Store data in cache
     * 
     * @param string $key Cache key
     * @param mixed $data Data to cache
     * @param int $ttl Time to live in seconds (default: 900 = 15 minutes)
     * @return bool Success status
     */
    public static function set($key, $data, $ttl = 900) {
        self::init();
        
        $cacheData = [
            'expires' => time() + $ttl,
            'data' => $data
        ];
        
        $filename = self::getFilename($key);
        $result = file_put_contents($filename, serialize($cacheData), LOCK_EX);
        
        return $result !== false;
    }
    
    /**
     * Retrieve data from cache
     * 
     * @param string $key Cache key
     * @return mixed Cached data or null if expired/not found
     */
    public static function get($key) {
        $filename = self::getFilename($key);
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $contents = file_get_contents($filename);
        if ($contents === false) {
            return null;
        }
        
        $cacheData = unserialize($contents);
        
        // Check if expired
        if ($cacheData['expires'] < time()) {
            self::delete($key);
            return null;
        }
        
        return $cacheData['data'];
    }
    
    /**
     * Delete specific cache entry
     * 
     * @param string $key Cache key
     * @return bool Success status
     */
    public static function delete($key) {
        $filename = self::getFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return true;
    }
    
    /**
     * Clear all cache
     * 
     * @return int Number of files deleted
     */
    public static function clear() {
        self::init();
        
        $count = 0;
        $files = glob(self::$cacheDir . '*.cache');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Clean up expired cache files
     * 
     * @return int Number of expired files deleted
     */
    public static function cleanup() {
        self::init();
        
        $count = 0;
        $files = glob(self::$cacheDir . '*.cache');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $contents = file_get_contents($file);
                $cacheData = unserialize($contents);
                
                if ($cacheData['expires'] < time()) {
                    unlink($file);
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    /**
     * Get cache statistics
     * 
     * @return array Cache stats
     */
    public static function getStats() {
        self::init();
        
        $files = glob(self::$cacheDir . '*.cache');
        $totalSize = 0;
        $expiredCount = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
                
                $contents = file_get_contents($file);
                $cacheData = unserialize($contents);
                
                if ($cacheData['expires'] < time()) {
                    $expiredCount++;
                }
            }
        }
        
        return [
            'total_files' => count($files),
            'expired_files' => $expiredCount,
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2)
        ];
    }
    
    /**
     * Remember pattern - get from cache or execute callback
     * 
     * @param string $key Cache key
     * @param callable $callback Function to call if cache miss
     * @param int $ttl Time to live in seconds
     * @return mixed Cached or fresh data
     */
    public static function remember($key, $callback, $ttl = 900) {
        $data = self::get($key);
        
        if ($data !== null) {
            return $data;
        }
        
        $data = $callback();
        self::set($key, $data, $ttl);
        
        return $data;
    }
}
?>
