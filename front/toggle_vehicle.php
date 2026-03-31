<?php
session_start();
require_once dirname(__DIR__).'/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']))         { echo json_encode(['success'=>false,'msg'=>'Unauthorized']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success'=>false,'msg'=>'Invalid method']); exit; }

$conn     = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME,(int)DB_PORT);
$id       = intval($_POST['id'] ?? 0);
$username = $_SESSION['username'];

if (!$id) { echo json_encode(['success'=>false,'msg'=>'Invalid vehicle id']); exit; }

// Get owner's email to handle legacy owner_name = email records
$emailRow = $conn->query("SELECT email FROM signup WHERE username='".mysqli_real_escape_string($conn,$username)."' LIMIT 1")->fetch_assoc();
$ownerEmail = $emailRow['email'] ?? '';

// Verify ownership — match against both username and email
$check = $conn->prepare('SELECT id, is_available FROM vehicles WHERE id=? AND (owner_name=? OR owner_name=?)');
$check->bind_param('iss', $id, $username, $ownerEmail);
$check->execute();
$row = $check->get_result()->fetch_assoc();

if (!$row) { echo json_encode(['success'=>false,'msg'=>'Vehicle not found or not yours']); exit; }

$new_val = $row['is_available'] ? 0 : 1;
$upd = $conn->prepare('UPDATE vehicles SET is_available=? WHERE id=?');
$upd->bind_param('ii', $new_val, $id);
$upd->execute();

echo json_encode(['success'=>true, 'is_available'=>$new_val]);
$conn->close();
?>
