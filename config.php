<?php
/**
 * Drifter — Central Config
 * Supports .env configuration with fallback to defaults
 */

// Load environment variables
require_once __DIR__ . '/includes/EnvLoader.php';
EnvLoader::load(__DIR__ . '/.env');

// Determine environment (local vs production)
$isLocal = (
    !isset($_SERVER['HTTP_HOST']) ||
    $_SERVER['HTTP_HOST'] === 'localhost' ||
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'localhost') !== false
);

// Database Configuration (from .env or defaults)
define('DB_HOST',   EnvLoader::get('DB_HOST', $isLocal ? 'localhost' : 'sql311.infinityfree.com'));
define('DB_USER',   EnvLoader::get('DB_USER', $isLocal ? 'root' : 'if0_41679582'));
define('DB_PASS',   EnvLoader::get('DB_PASS', ''));
define('DB_NAME',   EnvLoader::get('DB_NAME', $isLocal ? 'db' : 'if0_41679582_drifter'));
define('DB_PORT',   EnvLoader::get('DB_PORT', '3306'));
define('SINGLE_DB', EnvLoader::get('SINGLE_DB', $isLocal ? 'false' : 'true') === 'true');

// Application Configuration
define('BASE',      $isLocal ? '/Drifter' : '');
define('APP_URL',   EnvLoader::get('APP_URL', $isLocal ? 'http://localhost/Drifter' : 'https://drifter-smarttranspo.infinityfreeapp.com'));
define('APP_NAME',  'Drifter');
define('ROOT_PATH', __DIR__);

// Environment
define('APP_ENV',   EnvLoader::get('APP_ENV', $isLocal ? 'local' : 'production'));
define('APP_DEBUG', EnvLoader::get('DEBUG', $isLocal ? 'true' : 'false') === 'true');

// Error Reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Security
define('HASH_ALGORITHM', EnvLoader::get('HASH_ALGORITHM', 'bcrypt'));
?>

