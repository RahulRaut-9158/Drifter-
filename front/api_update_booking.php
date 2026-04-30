<?php
session_start();
require_once dirname(__DIR__).'/includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']))         { echo json_encode(['success'=>false,'msg'=>'Unauthorized']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success'=>false,'msg'=>'Invalid method']); exit; }
if (($_SESSION['role'] ?? '') !== 'owner') { echo json_encode(['success'=>false,'msg'=>'Not an owner account']); exit; }

$conn     = db();
$bid      = intval($_POST['booking_id'] ?? 0);
$action   = trim($_POST['action'] ?? '');
$username = $_SESSION['username'];

if (!$bid || !in_array($action, ['confirm','cancel'])) {
    echo json_encode(['success'=>false,'msg'=>'Invalid parameters']); exit;
}

// Get the owner's email from signup table (handles legacy records where owner_name = email)
$emailRow = $conn->query("SELECT email FROM signup WHERE username='".mysqli_real_escape_string($conn,$username)."' LIMIT 1")->fetch_assoc();
$ownerEmail = $emailRow['email'] ?? '';

// Verify booking belongs to a vehicle owned by this user
// Match owner_name against BOTH username and email to handle all cases
$check = $conn->prepare("
    SELECT b.id, b.status
    FROM booking b
    INNER JOIN vehicles v ON b.vehicle_id = v.id
    WHERE b.id = ?
      AND b.status IN ('Pending','Confirmed')
      AND (v.owner_name = ? OR v.owner_name = ?)
");
$check->bind_param("iss", $bid, $username, $ownerEmail);
$check->execute();
$row = $check->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode(['success'=>false,'msg'=>'Booking not found, not yours, or already cancelled']); exit;
}

// Confirm only if Pending; Cancel allowed from both Pending and Confirmed
if ($action === 'confirm' && $row['status'] !== 'Pending') {
    echo json_encode(['success'=>false,'msg'=>'Only pending bookings can be confirmed']); exit;
}

$newStatus = ($action === 'confirm') ? 'Confirmed' : 'Cancelled';
$upd = $conn->prepare("UPDATE booking SET status=? WHERE id=?");
$upd->bind_param("si", $newStatus, $bid);

if ($upd->execute()) {
    echo json_encode(['success'=>true, 'status'=>$newStatus, 'booking_id'=>$bid]);
} else {
    echo json_encode(['success'=>false, 'msg'=>'DB update failed']);
}
$conn->close();
?>
