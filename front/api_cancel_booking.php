<?php
session_start();
require_once dirname(__DIR__).'/includes/db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['loggedin'])) { echo json_encode(['success'=>false,'msg'=>'Unauthorized']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success'=>false,'msg'=>'Invalid method']); exit; }

$conn = db();

$bid      = intval($_POST['booking_id'] ?? 0);
$username = $_SESSION['username'];

if (!$bid) { echo json_encode(['success'=>false,'msg'=>'Invalid booking ID']); exit; }

// Only allow cancelling own Pending bookings
$check = $conn->prepare('SELECT id, status FROM booking WHERE id=? AND user_name=?');
$check->bind_param('is', $bid, $username);
$check->execute();
$row = $check->get_result()->fetch_assoc();

if (!$row) { echo json_encode(['success'=>false,'msg'=>'Booking not found']); exit; }
if ($row['status'] !== 'Pending') { echo json_encode(['success'=>false,'msg'=>'Only pending bookings can be cancelled']); exit; }

$stmt = $conn->prepare("UPDATE booking SET status='Cancelled', cancel_reason='Cancelled by customer' WHERE id=?");
$stmt->bind_param('i', $bid);
$stmt->execute();

echo json_encode(['success'=>true,'msg'=>'Booking cancelled successfully']);
$conn->close();
?>
