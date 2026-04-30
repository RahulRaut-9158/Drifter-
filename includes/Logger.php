<?php
/**
 * Centralized Logging System
 */
class Logger {
    const LOG_LEVEL_DEBUG = 'DEBUG';
    const LOG_LEVEL_INFO = 'INFO';
    const LOG_LEVEL_WARNING = 'WARNING';
    const LOG_LEVEL_ERROR = 'ERROR';

    private static $logFile;
    private static $logLevel;

    public static function init($logFile = null, $logLevel = 'info') {
        self::$logFile = $logFile ?? __DIR__ . '/../logs/app.log';
        self::$logLevel = strtoupper($logLevel);

        // Create logs directory if it doesn't exist
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Rotate log file if it exceeds 10MB
        if (file_exists(self::$logFile) && filesize(self::$logFile) > 10 * 1024 * 1024) {
            rename(self::$logFile, self::$logFile . '.' . time());
        }
    }

    private static function log($level, $message, $context = []) {
        if (!self::$logFile) {
            self::init();
        }

        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $user = $_SESSION['username'] ?? 'guest';

        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $logMessage = "[$timestamp] [$level] [$user@$ip] $message$contextStr\n";

        error_log($logMessage, 3, self::$logFile);
    }

    public static function debug($message, $context = []) {
        self::log(self::LOG_LEVEL_DEBUG, $message, $context);
    }

    public static function info($message, $context = []) {
        self::log(self::LOG_LEVEL_INFO, $message, $context);
    }

    public static function warning($message, $context = []) {
        self::log(self::LOG_LEVEL_WARNING, $message, $context);
    }

    public static function error($message, $context = []) {
        self::log(self::LOG_LEVEL_ERROR, $message, $context);
    }

    public static function exception(\Exception $e) {
        self::log(self::LOG_LEVEL_ERROR, $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    public static function auditLog($action, $target = null, $details = null) {
        $conn = db();
        $username = $_SESSION['username'] ?? 'system';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $stmt = $conn->prepare("INSERT INTO admin_logs (admin_user, action, target, ip_address, details) VALUES (?, ?, ?, ?, ?)");
        $details = $details ? json_encode($details) : null;
        $stmt->bind_param("sssss", $username, $action, $target, $ip, $details);
        $stmt->execute();
        
        self::info("Audit Log", ['action' => $action, 'target' => $target]);
    }
}
?>
