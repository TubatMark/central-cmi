<?php
/**
 * Application Configuration
 * Auto-detects base URL and provides global configuration
 */

// Prevent direct access
if (!defined('APP_LOADED')) {
    define('APP_LOADED', true);
}

/**
 * Auto-detect the base URL of the application
 * Works regardless of where the app is installed
 */
function detectBaseUrl() {
    // Check if already defined
    if (defined('BASE_URL')) {
        return BASE_URL;
    }
    
    // Get the script path
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptDir = dirname($scriptName);
    
    // Add trailing slash for consistent matching
    $scriptDirWithSlash = rtrim($scriptDir, '/') . '/';
    
    // Find the project root by looking for known directories
    $markers = ['/pages/', '/api/', '/database/', '/includes/'];
    
    foreach ($markers as $marker) {
        $pos = strpos($scriptDirWithSlash, $marker);
        if ($pos !== false) {
            $baseDir = substr($scriptDirWithSlash, 0, $pos);
            return rtrim($baseDir, '/') . '/';
        }
    }
    
    // If we're at root level (index.php, etc.)
    if ($scriptDir === '' || $scriptDir === '/') {
        return '/';
    }
    
    // Default: assume current directory is the app root
    return rtrim($scriptDir, '/') . '/';
}

// Define base URL constant
if (!defined('BASE_URL')) {
    define('BASE_URL', detectBaseUrl());
}

// Also set as global variable for backward compatibility
if (!isset($GLOBALS['base_url'])) {
    $GLOBALS['base_url'] = BASE_URL;
}

/**
 * Get the full URL for a path
 * @param string $path Relative path from app root
 * @return string Full URL path
 */
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . $path;
}

/**
 * Get the file system path for the app root
 * @return string Absolute file system path
 */
function appPath($path = '') {
    static $rootPath = null;
    
    if ($rootPath === null) {
        // Go up from config directory to get root
        $rootPath = dirname(__DIR__) . '/';
    }
    
    $path = ltrim($path, '/');
    return $rootPath . $path;
}
