<?php
session_start();
require_once dirname(__DIR__).'/includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'msg'=>'Invalid method']); exit;
}

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$phone   = trim($_POST['phone']   ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    echo json_encode(['success'=>false,'msg'=>'Name, email and message are required.']); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success'=>false,'msg'=>'Invalid email address.']); exit;
}

$conn = db();
$stmt = $conn->prepare('INSERT INTO support_messages (name,email,phone,service,message,status) VALUES (?,?,?,?,?,\'unread\')');
$stmt->bind_param('sssss', $name, $email, $phone, $service, $message);

if ($stmt->execute()) {
    echo json_encode(['success'=>true,'msg'=>"Message sent! We'll get back to you within 24 hours."]);
} else {
    echo json_encode(['success'=>false,'msg'=>'Failed to send message. Please try again.']);
}
?>
