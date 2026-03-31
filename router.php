<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve existing files (css, js, images, etc.) directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Redirect root to front/index.php
if ($uri === '/') {
    require __DIR__ . '/front/index.php';
    exit;
}

// Route /front/*, /transport/*, /travel/*, /courier/*, /move/*, /includes/*
$file = __DIR__ . $uri;
if (file_exists($file . '.php')) {
    require $file . '.php';
    exit;
}
if (file_exists($file) && is_file($file)) {
    return false;
}

http_response_code(404);
echo '404 Not Found';
