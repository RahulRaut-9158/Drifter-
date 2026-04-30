<?php
/**
 * Environment Configuration Loader
 * Loads variables from .env file
 */
class EnvLoader {
    private static $env = [];
    private static $loaded = false;

    public static function load($filePath = null) {
        if (self::$loaded) return;

        $filePath = $filePath ?? __DIR__ . '/../.env';
        
        if (!file_exists($filePath)) {
            error_log("⚠️ .env file not found at $filePath");
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) continue; // Skip comments
            if (!strpos($line, '=')) continue;

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }
            
            self::$env[$key] = $value;
            putenv("$key=$value");
        }

        self::$loaded = true;
    }

    public static function get($key, $default = null) {
        return self::$env[$key] ?? $default;
    }

    public static function all() {
        return self::$env;
    }
}
?>
