<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['loggedin'])) { echo json_encode(['success'=>false,'message'=>'Please login first.']); exit; }

require_once dirname(__DIR__) . '/includes/db.php';
$conn = db();

$vehicle_id  = intval($_POST['vehicle_id'] ?? 0);
$user_name   = trim($_POST['user_name'] ?? '');
$user_mobile = trim($_POST['user_mobile'] ?? '');
$pickup      = trim($_POST['pickup_location'] ?? '');
$drop        = trim($_POST['drop_location'] ?? '');
$distance    = floatval($_POST['distance_km'] ?? 0);
$date        = $_POST['date'] ?? '';
$time        = $_POST['time'] ?? '';

// Validate all fields
if (!$vehicle_id || !$user_name || !$user_mobile || !$pickup || !$drop || $distance <= 0 || !$date || !$time) {
    echo json_encode(['success'=>false,'message'=>'Missing required fields.']); exit;
}
if (!preg_match('/^[0-9]{10}$/', $user_mobile)) {
    echo json_encode(['success'=>false,'message'=>'Invalid mobile number.']); exit;
}
if (strtotime($date) < strtotime(date('Y-m-d'))) {
    echo json_encode(['success'=>false,'message'=>'Booking date cannot be in the past.']); exit;
}

// Get vehicle — must be available and be transport category
$vstmt = $conn->prepare("SELECT rate_per_km, owner_name FROM vehicles WHERE id=? AND is_available=1 AND vehicle_category='transport'");
$vstmt->bind_param('i', $vehicle_id);
$vstmt->execute();
$vrow = $vstmt->get_result()->fetch_assoc();
if (!$vrow) { echo json_encode(['success'=>false,'message'=>'Vehicle not available or not found.']); exit; }

// Prevent duplicate booking: same user, same vehicle, same date
$dup = $conn->prepare("SELECT id FROM booking WHERE vehicle_id=? AND user_name=? AND date=? AND status != 'Cancelled'");
$dup->bind_param('iss', $vehicle_id, $user_name, $date);
$dup->execute();
if ($dup->get_result()->num_rows > 0) {
    echo json_encode(['success'=>false,'message'=>'You already have a booking for this vehicle on this date.']); exit;
}

// Prevent owner booking their own vehicle
if ($vrow['owner_name'] === $user_name) {
    echo json_encode(['success'=>false,'message'=>'You cannot book your own vehicle.']); exit;
}

$total_cost = round($vrow['rate_per_km'] * $distance, 2);

$stmt = $conn->prepare("INSERT INTO booking (vehicle_id,user_name,user_mobile,pickup_location,drop_location,distance_km,total_cost,date,time,status) VALUES (?,?,?,?,?,?,?,?,?,'Pending')");
$stmt->bind_param('isssssdss', $vehicle_id, $user_name, $user_mobile, $pickup, $drop, $distance, $total_cost, $date, $time);

if ($stmt->execute()) {
    echo json_encode(['success'=>true,'message'=>'Booking placed successfully!','total_cost'=>number_format($total_cost,2),'booking_id'=>$conn->insert_id,'redirect'=>BASE.'/front/index.php?booking_success=true']);
} else {
    echo json_encode(['success'=>false,'message'=>'Booking failed. Please try again.']);
}
$conn->close();
?>
