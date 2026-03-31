<?php
require_once dirname(__DIR__) . '/config.php';
$dbname = SINGLE_DB ? DB_NAME : 'drifter_courier';
try {
    $pdo = new PDO('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.$dbname.';charset=utf8mb4', DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die('DB Error: ' . $e->getMessage()); }
