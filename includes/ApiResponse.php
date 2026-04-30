<?php
/**
 * Standardized API Response Handler
 * All APIs should return responses in consistent format
 */
class ApiResponse {
    private static $httpCode = 200;

    public static function success($data = null, $message = 'Success', $code = 200) {
        self::$httpCode = $code;
        http_response_code($code);
        header('Content-Type: application/json');
        
        return json_encode([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    public static function error($message = 'Error', $code = 400, $errors = null) {
        self::$httpCode = $code;
        http_response_code($code);
        header('Content-Type: application/json');
        
        return json_encode([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    public static function unauthorized($message = 'Unauthorized') {
        return self::error($message, 401);
    }

    public static function forbidden($message = 'Forbidden') {
        return self::error($message, 403);
    }

    public static function notFound($message = 'Not Found') {
        return self::error($message, 404);
    }

    public static function validation($errors) {
        return self::error('Validation failed', 422, $errors);
    }

    public static function serverError($message = 'Internal Server Error') {
        Logger::error($message);
        return self::error($message, 500);
    }

    public static function paginated($items, $total, $page, $limit, $message = 'Success') {
        $totalPages = ceil($total / $limit);
        
        return json_encode([
            'success' => true,
            'code' => 200,
            'message' => $message,
            'data' => $items,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => $totalPages
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}
?>
