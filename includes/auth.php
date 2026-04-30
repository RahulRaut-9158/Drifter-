<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';

function requireLogin() {
    if (empty($_SESSION['loggedin'])) {
        header('Location: ' . BASE . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? ''));
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if (($_SESSION['role'] ?? '') !== $role) {
        header('Location: ' . BASE . '/index.php');
        exit;
    }
}

function requireAdmin() {
    if (empty($_SESSION['loggedin']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ' . BASE . '/login.php');
        exit;
    }
}
?>
