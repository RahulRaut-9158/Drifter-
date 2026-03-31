<?php
require_once dirname(__DIR__) . '/config.php';

define('ROOT', dirname(__DIR__));

// ── MAIN DB (transport, travel, users) ──────────────────────────────────────
function db() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);
        if ($conn->connect_error) die('DB Error: ' . $conn->connect_error);
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

// ── COURIER DB ───────────────────────────────────────────────────────────────
// On Railway (SINGLE_DB=true): uses same DB with table prefix courier_
// Locally: uses separate drifter_courier database
function courierPDO() {
    static $pdo = null;
    if ($pdo === null) {
        $dbname = SINGLE_DB ? DB_NAME : 'drifter_courier';
        $dsn    = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.$dbname.';charset=utf8mb4';
        $pdo    = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $pdo;
}

// ── MOVERS DB ────────────────────────────────────────────────────────────────
// On Railway (SINGLE_DB=true): uses same DB with table prefix movers_
// Locally: uses separate moveeasy database
function moversPDO() {
    static $pdo = null;
    if ($pdo === null) {
        $dbname = SINGLE_DB ? DB_NAME : 'moveeasy';
        $dsn    = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.$dbname.';charset=utf8mb4';
        $pdo    = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $pdo;
}
