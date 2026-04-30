<?php
/**
 * Rate Limiting Middleware
 * Prevents API abuse
 */
class RateLimiter {
    private static $storageFile = null;

    public static function init($storageDir = null) {
        self::$storageFile = ($storageDir ?? __DIR__ . '/../storage') . '/rate_limits.json';
        $dir = dirname(self::$storageFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    public static function check($identifier, $limit = 100, $window = 3600) {
        if (!self::$storageFile) {
            self::init();
        }

        $now = time();
        $limits = self::loadLimits();
        $key = $identifier;

        // Clean old entries
        if (isset($limits[$key])) {
            $limits[$key] = array_filter($limits[$key], function($time) use ($now, $window) {
                return ($now - $time) < $window;
            });
        } else {
            $limits[$key] = [];
        }

        // Check if limit exceeded
        if (count($limits[$key]) >= $limit) {
            return false;
        }

        // Add new request
        $limits[$key][] = $now;
        self::saveLimits($limits);

        return true;
    }

    public static function remaining($identifier, $limit = 100, $window = 3600) {
        if (!self::$storageFile) {
            self::init();
        }

        $now = time();
        $limits = self::loadLimits();
        $key = $identifier;

        if (!isset($limits[$key])) {
            return $limit;
        }

        $limits[$key] = array_filter($limits[$key], function($time) use ($now, $window) {
            return ($now - $time) < $window;
        });

        return max(0, $limit - count($limits[$key]));
    }

    private static function loadLimits() {
        if (!file_exists(self::$storageFile)) {
            return [];
        }
        return json_decode(file_get_contents(self::$storageFile), true) ?? [];
    }

    private static function saveLimits($limits) {
        file_put_contents(self::$storageFile, json_encode($limits));
    }
}
?>
