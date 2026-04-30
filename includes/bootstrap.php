<?php
/**
 * Application Bootstrap
 * Initializes all core services
 */

// Load environment variables
require_once __DIR__ . '/EnvLoader.php';
EnvLoader::load(__DIR__ . '/../.env');

// Load core classes
require_once __DIR__ . '/Logger.php';
require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/ApiResponse.php';
require_once __DIR__ . '/RateLimiter.php';
require_once __DIR__ . '/EmailService.php';

// Initialize services safely
try {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    Logger::init(
        EnvLoader::get('LOG_FILE', $logDir . '/app.log'),
        EnvLoader::get('LOG_LEVEL', 'info')
    );
} catch (Exception $e) { /* silent fail */ }

try {
    EmailService::init(
        EnvLoader::get('MAIL_FROM', 'noreply@drifter.com'),
        'Drifter'
    );
} catch (Exception $e) { /* silent fail */ }

try {
    $storageDir = __DIR__ . '/../storage';
    if (!is_dir($storageDir)) @mkdir($storageDir, 0755, true);
    RateLimiter::init($storageDir);
} catch (Exception $e) { /* silent fail */ }

// Set error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    Logger::error("PHP Error", [
        'code' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ]);
});

// Set exception handler
set_exception_handler(function($exception) {
    Logger::exception($exception);
    http_response_code(500);
    header('Content-Type: application/json');
    echo ApiResponse::serverError();
});
?>
