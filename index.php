<?php
/**
 * Router for PHP built-in server (Railway deployment)
 * Handles all requests and serves static files or routes to PHP files
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve existing static files directly (images, css, js, etc.)
if ($uri !== '/' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    // Set correct content type for common static files
    $ext = strtolower(pathinfo($uri, PATHINFO_EXTENSION));
    $mime = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'mp4'  => 'video/mp4',
        'webm' => 'video/webm',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
    ];
    if (isset($mime[$ext])) {
        header('Content-Type: ' . $mime[$ext]);
    }
    return false; // Let built-in server handle it
}

// Route / to front/index.php
if ($uri === '/' || $uri === '') {
    require __DIR__ . '/front/index.php';
    exit;
}

// Route to PHP file if it exists
$file = __DIR__ . $uri;
if (file_exists($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    require $file;
    exit;
}

// Try adding .php extension
if (file_exists($file . '.php')) {
    require $file . '.php';
    exit;
}

// 404
http_response_code(404);
echo '<h1>404 Not Found</h1><p><a href="/">Go Home</a></p>';
