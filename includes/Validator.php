<?php
/**
 * Input Validation & Sanitization
 */
class Validator {
    private static $errors = [];

    public static function reset() {
        self::$errors = [];
    }

    public static function errors() {
        return self::$errors;
    }

    public static function hasErrors() {
        return !empty(self::$errors);
    }

    public static function addError($field, $message) {
        if (!isset(self::$errors[$field])) {
            self::$errors[$field] = [];
        }
        self::$errors[$field][] = $message;
    }

    // Sanitization methods
    public static function sanitizeEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    public static function sanitizeString($str) {
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeInt($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function sanitizeFloat($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    public static function sanitizePhone($phone) {
        return preg_replace('/[^0-9+\-\s()]/', '', $phone);
    }

    // Validation methods
    public static function required($value, $field = 'Field') {
        if (empty(trim($value))) {
            self::addError($field, "$field is required");
            return false;
        }
        return true;
    }

    public static function email($email, $field = 'Email') {
        $email = self::sanitizeEmail($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::addError($field, "$field is invalid");
            return false;
        }
        return true;
    }

    public static function minLength($value, $min, $field = 'Field') {
        if (strlen($value) < $min) {
            self::addError($field, "$field must be at least $min characters");
            return false;
        }
        return true;
    }

    public static function maxLength($value, $max, $field = 'Field') {
        if (strlen($value) > $max) {
            self::addError($field, "$field must not exceed $max characters");
            return false;
        }
        return true;
    }

    public static function password($password, $field = 'Password') {
        if (strlen($password) < 8) {
            self::addError($field, "$field must be at least 8 characters");
            return false;
        }
        if (!preg_match('/[A-Z]/', $password)) {
            self::addError($field, "$field must contain uppercase letter");
            return false;
        }
        if (!preg_match('/[a-z]/', $password)) {
            self::addError($field, "$field must contain lowercase letter");
            return false;
        }
        if (!preg_match('/[0-9]/', $password)) {
            self::addError($field, "$field must contain number");
            return false;
        }
        return true;
    }

    public static function phone($phone, $field = 'Phone') {
        $phone = self::sanitizePhone($phone);
        if (!preg_match('/^[0-9+\-\s()]{10,15}$/', $phone)) {
            self::addError($field, "$field is invalid");
            return false;
        }
        return true;
    }

    public static function numeric($value, $field = 'Field') {
        if (!is_numeric($value)) {
            self::addError($field, "$field must be numeric");
            return false;
        }
        return true;
    }

    public static function inArray($value, $array, $field = 'Field') {
        if (!in_array($value, $array)) {
            self::addError($field, "$field is invalid");
            return false;
        }
        return true;
    }

    public static function unique($conn, $table, $column, $value, $field = 'Field') {
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM $table WHERE $column = ?");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result['cnt'] > 0) {
            self::addError($field, "$field already exists");
            return false;
        }
        return true;
    }

    public static function url($url, $field = 'URL') {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            self::addError($field, "$field is invalid");
            return false;
        }
        return true;
    }

    public static function date($date, $format = 'Y-m-d', $field = 'Date') {
        $d = \DateTime::createFromFormat($format, $date);
        if (!$d || $d->format($format) !== $date) {
            self::addError($field, "$field format is invalid");
            return false;
        }
        return true;
    }
}
?>
