<?php
session_start();
require_once dirname(__DIR__).'/config.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success'=>false,'msg'=>'Invalid method']); exit; }

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    echo json_encode(['success'=>false,'msg'=>'Name, email and message are required.']); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success'=>false,'msg'=>'Invalid email address.']); exit;
}

$conn = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME,(int)DB_PORT);
if ($conn->connect_error) { echo json_encode(['success'=>false,'msg'=>'DB error']); exit; }
$conn->set_charset('utf8mb4');

// Create support_messages table if it doesn't exist
$conn->query("CREATE TABLE IF NOT EXISTS support_messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(100) NOT NULL,
    phone      VARCHAR(20) DEFAULT NULL,
    service    VARCHAR(50) DEFAULT NULL,
    message    TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$stmt = $conn->prepare('INSERT INTO support_messages (name,email,phone,service,message) VALUES (?,?,?,?,?)');
$stmt->bind_param('sssss', $name, $email, $phone, $service, $message);

if ($stmt->execute()) {
    echo json_encode(['success'=>true,'msg'=>'Message sent! We\'ll get back to you within 24 hours.']);
} else {
    echo json_encode(['success'=>false,'msg'=>'Failed to send message. Please try again.']);
}
$conn->close();
?>
