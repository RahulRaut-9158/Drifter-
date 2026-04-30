<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/Paginator.php';

// Main DB — transport, travel, users
function db() {
    static $c = null;
    if ($c === null) {
        $c = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);
        if ($c->connect_error) die(json_encode(['error' => 'DB connection failed: '.$c->connect_error]));
        $c->set_charset('utf8mb4');
        // Safe migrations — run once, ignored if column already exists
        @$c->query("ALTER TABLE signup ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1 AFTER phone");
        @$c->query("ALTER TABLE signup MODIFY COLUMN role ENUM('customer','owner','company','admin') DEFAULT 'customer'");
        @$c->query("ALTER TABLE support_messages ADD COLUMN IF NOT EXISTS status ENUM('unread','read','replied') DEFAULT 'unread' AFTER message");
        @$c->query("CREATE TABLE IF NOT EXISTS admin_logs (id INT AUTO_INCREMENT PRIMARY KEY, admin_user VARCHAR(100) NOT NULL, action VARCHAR(255) NOT NULL, target VARCHAR(255) DEFAULT NULL, ip_address VARCHAR(45) DEFAULT NULL, details JSON DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
    }
    return $c;
}

// Courier DB
function courierPDO() {
    static $p = null;
    if ($p === null) {
        $db  = SINGLE_DB ? DB_NAME : 'drifter_courier';
        $dsn = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.$db.';charset=utf8mb4';
        $p   = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    }
    return $p;
}

// Movers DB
function moversPDO() {
    static $p = null;
    if ($p === null) {
        $db  = SINGLE_DB ? DB_NAME : 'moveeasy';
        $dsn = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.$db.';charset=utf8mb4';
        $p   = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    }
    return $p;
}

// Movers table names (prefixed on single DB)
function mTbl($t) {
    $map = ['companies'=>'movers_companies','services'=>'movers_services','user_requests'=>'movers_requests'];
    return SINGLE_DB ? ($map[$t] ?? $t) : $t;
}
?>
