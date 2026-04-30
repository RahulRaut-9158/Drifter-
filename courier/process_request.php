<?php
session_start();
require_once dirname(__DIR__).'/includes/db.php';
if (!isset($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/login.php?redirect='.urlencode(BASE.'/courier/courier.php')); exit; }
$pdo = courierPDO();

$sender_name     = trim($_POST['sender_name'] ?? '');
$sender_phone    = trim($_POST['sender_phone'] ?? '');
$sender_address  = trim($_POST['sender_address'] ?? '');
$pickup_date     = $_POST['pickup_date'] ?? '';
$receiver_name   = trim($_POST['receiver_name'] ?? '');
$receiver_phone  = trim($_POST['receiver_phone'] ?? '');
$receiver_address= trim($_POST['receiver_address'] ?? '');
$delivery_date   = $_POST['delivery_date'] ?? '';
$package_details = trim($_POST['package_details'] ?? '');
$company_id      = intval($_POST['company_id'] ?? 0) ?: null;

if (!$sender_name || !$sender_phone || !$sender_address || !$pickup_date || !$receiver_name || !$receiver_phone || !$receiver_address || !$delivery_date) {
    header('Location: courier.php'); exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO user_requests (company_id,sender_name,sender_phone,sender_address,pickup_date,receiver_name,receiver_phone,receiver_address,delivery_date,package_details) VALUES (?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([$company_id,$sender_name,$sender_phone,$sender_address,$pickup_date,$receiver_name,$receiver_phone,$receiver_address,$delivery_date,$package_details]);
    header('Location: providers.php?pickup=' . urlencode($sender_address)); exit;
} catch (PDOException $e) {
    header('Location: courier.php'); exit;
}
?>
