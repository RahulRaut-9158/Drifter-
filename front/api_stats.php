<?php
require_once dirname(__DIR__).'/config.php';
header('Content-Type: application/json');
header('Cache-Control: no-cache');

$conn = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME,(int)DB_PORT);
if ($conn->connect_error) { echo json_encode(['error'=>'db']); exit; }
$conn->set_charset('utf8mb4');

$vehicles  = $conn->query("SELECT COUNT(*) FROM vehicles WHERE is_available=1")->fetch_row()[0] ?? 0;
$bookings  = $conn->query("SELECT COUNT(*) FROM booking")->fetch_row()[0] ?? 0;
$customers = $conn->query("SELECT COUNT(*) FROM signup WHERE role='customer'")->fetch_row()[0] ?? 0;
$owners    = $conn->query("SELECT COUNT(*) FROM signup WHERE role='owner'")->fetch_row()[0] ?? 0;
$conn->close();

// Courier stats
try {
    $cpdo = courierPDO();
    $courier_req = $cpdo->query("SELECT COUNT(*) FROM user_requests")->fetchColumn();
    $courier_co  = $cpdo->query("SELECT COUNT(*) FROM companies")->fetchColumn();
} catch(Exception $e) { $courier_req = 0; $courier_co = 0; }

// Movers stats
try {
    $mpdo = moversPDO();
    $movers_req = $mpdo->query("SELECT COUNT(*) FROM user_requests")->fetchColumn();
    $movers_co  = $mpdo->query("SELECT COUNT(*) FROM companies")->fetchColumn();
} catch(Exception $e) { $movers_req = 0; $movers_co = 0; }

echo json_encode([
    'vehicles'       => (int)$vehicles,
    'bookings'       => (int)$bookings,
    'customers'      => (int)$customers,
    'owners'         => (int)$owners,
    'courier_req'    => (int)$courier_req,
    'courier_co'     => (int)$courier_co,
    'movers_req'     => (int)$movers_req,
    'movers_co'      => (int)$movers_co,
    'total_requests' => (int)$bookings + (int)$courier_req + (int)$movers_req,
]);
