<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/login.php?redirect='.urlencode(BASE.'/move/movers.php')); exit; }
require_once dirname(__DIR__) . '/includes/db.php';
$pdo = moversPDO();

$tRequests = SINGLE_DB ? 'movers_requests' : 'user_requests';

$current_address = trim($_POST['current_address'] ?? '');
$new_address     = trim($_POST['new_address'] ?? '');
$moving_date     = $_POST['moving_date'] ?? '';
$property_type   = $_POST['property_type'] ?? '';
$work_type       = $_POST['work_type'] ?? '';
$special_items   = isset($_POST['special_items']) ? implode(',', $_POST['special_items']) : '';
$additional_info = trim($_POST['additional_info'] ?? '');
$customer_name   = $_SESSION['username'];

$valid_props = ['1bhk','2bhk','3bhk','villa','office'];
$valid_works = ['packing','moving','packing_moving','full_service','vehicle','international'];

if (!$current_address || !$new_address || !$moving_date || !in_array($property_type,$valid_props) || !in_array($work_type,$valid_works)) {
    header('Location: movers.php'); exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO $tRequests (customer_name,current_address,new_address,moving_date,property_type,work_type,special_items,additional_info) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute([$customer_name,$current_address,$new_address,$moving_date,$property_type,$work_type,$special_items,$additional_info]);
    header('Location: providers.php?current='.urlencode($current_address).'&new='.urlencode($new_address).'&type='.$property_type.'&work='.$work_type); exit;
} catch (PDOException $e) {
    header('Location: movers.php'); exit;
}
?>
