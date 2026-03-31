<?php
session_start();
require_once dirname(__DIR__).'/config.php';
header('Content-Type: application/json');
if (!isset($_SESSION['loggedin'])) { echo json_encode(['error'=>'unauthorized']); exit; }

$conn = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME,(int)DB_PORT);
if ($conn->connect_error) { echo json_encode(['error'=>'db']); exit; }
$conn->set_charset('utf8mb4');

$username = $_SESSION['username'];

$stmt = $conn->prepare("
    SELECT b.*, v.vehicle_category, v.capacity, v.rate_per_km,
           v.address AS vehicle_base, v.owner_name AS driver_name,
           v.mobile AS driver_mobile, v.vehicle_image
    FROM booking b
    JOIN vehicles v ON b.vehicle_id = v.id
    WHERE b.user_name = ?
    ORDER BY b.created_at DESC
");
$stmt->bind_param('s', $username);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Active = Pending OR Confirmed (not yet completed/cancelled)
$active = array_values(array_filter($bookings, fn($b) => in_array($b['status'], ['Pending','Confirmed'])));
$past   = array_values(array_filter($bookings, fn($b) => $b['status'] === 'Cancelled'));

// Total spent = confirmed bookings only
$total_spent = array_sum(array_column(
    array_filter($bookings, fn($b) => $b['status'] === 'Confirmed'),
    'total_cost'
));

// Stats breakdown
$pending_count   = count(array_filter($bookings, fn($b) => $b['status'] === 'Pending'));
$confirmed_count = count(array_filter($bookings, fn($b) => $b['status'] === 'Confirmed'));
$cancelled_count = count(array_filter($bookings, fn($b) => $b['status'] === 'Cancelled'));

echo json_encode([
    'all'         => array_values($bookings),
    'active'      => $active,
    'past'        => $past,
    'total_spent' => round($total_spent, 2),
    'count'       => count($bookings),
    'pending'     => $pending_count,
    'confirmed'   => $confirmed_count,
    'cancelled'   => $cancelled_count,
    'timestamp'   => date('H:i:s'),
]);
$conn->close();
?>
