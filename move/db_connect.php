<?php
require_once dirname(__DIR__) . '/config.php';
// On Railway: single DB, movers tables are prefixed movers_
// Locally: separate moveeasy database
$dbname = SINGLE_DB ? DB_NAME : 'moveeasy';
try {
    $pdo = new PDO('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.$dbname.';charset=utf8mb4', DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die('DB Error: ' . $e->getMessage()); }

// On Railway, remap table names to prefixed versions
if (SINGLE_DB) {
    // Monkey-patch: queries in move/ use 'companies','services','user_requests'
    // On Railway these are movers_companies, movers_services, movers_requests
    // We handle this via views created in db_setup_railway.sql
}
