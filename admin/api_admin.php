<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

if (empty($_SESSION['loggedin']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['error' => 'unauthorized']); exit;
}

$conn   = db();
$action = $_GET['action'] ?? 'overview';

switch ($action) {

    case 'overview':
        $users     = $conn->query("SELECT COUNT(*) FROM signup WHERE role != 'admin'")->fetch_row()[0] ?? 0;
        $customers = $conn->query("SELECT COUNT(*) FROM signup WHERE role='customer'")->fetch_row()[0] ?? 0;
        $owners    = $conn->query("SELECT COUNT(*) FROM signup WHERE role='owner'")->fetch_row()[0] ?? 0;
        $companies_u = $conn->query("SELECT COUNT(*) FROM signup WHERE role='company'")->fetch_row()[0] ?? 0;
        $vehicles  = $conn->query("SELECT COUNT(*) FROM vehicles")->fetch_row()[0] ?? 0;
        $avail_v   = $conn->query("SELECT COUNT(*) FROM vehicles WHERE is_available=1")->fetch_row()[0] ?? 0;
        $bookings  = $conn->query("SELECT COUNT(*) FROM booking")->fetch_row()[0] ?? 0;
        $pending_b = $conn->query("SELECT COUNT(*) FROM booking WHERE status='Pending'")->fetch_row()[0] ?? 0;
        $confirmed_b = $conn->query("SELECT COUNT(*) FROM booking WHERE status='Confirmed'")->fetch_row()[0] ?? 0;
        $cancelled_b = $conn->query("SELECT COUNT(*) FROM booking WHERE status='Cancelled'")->fetch_row()[0] ?? 0;
        $revenue   = $conn->query("SELECT COALESCE(SUM(total_cost),0) FROM booking WHERE status='Confirmed'")->fetch_row()[0] ?? 0;
        $msgs      = $conn->query("SELECT COUNT(*) FROM support_messages WHERE status='unread'")->fetch_row()[0] ?? 0;

        // Bookings last 7 days
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $cnt = $conn->query("SELECT COUNT(*) FROM booking WHERE DATE(created_at)='$d'")->fetch_row()[0] ?? 0;
            $trend[] = ['date' => date('d M', strtotime($d)), 'count' => (int)$cnt];
        }

        // Courier & movers counts
        try {
            $cpdo = courierPDO();
            $courier_co  = $cpdo->query("SELECT COUNT(*) FROM companies")->fetchColumn();
            $courier_req = $cpdo->query("SELECT COUNT(*) FROM user_requests")->fetchColumn();
            $courier_pen = $cpdo->query("SELECT COUNT(*) FROM user_requests WHERE status='Pending'")->fetchColumn();
        } catch(Exception $e) { $courier_co = $courier_req = $courier_pen = 0; }

        try {
            $mpdo = moversPDO();
            $tc = SINGLE_DB ? 'movers_companies' : 'companies';
            $tr = SINGLE_DB ? 'movers_requests'  : 'user_requests';
            $movers_co  = $mpdo->query("SELECT COUNT(*) FROM $tc")->fetchColumn();
            $movers_req = $mpdo->query("SELECT COUNT(*) FROM $tr")->fetchColumn();
            $movers_pen = $mpdo->query("SELECT COUNT(*) FROM $tr WHERE status='Pending'")->fetchColumn();
        } catch(Exception $e) { $movers_co = $movers_req = $movers_pen = 0; }

        echo json_encode([
            'users'       => (int)$users,
            'customers'   => (int)$customers,
            'owners'      => (int)$owners,
            'company_users' => (int)$companies_u,
            'vehicles'    => (int)$vehicles,
            'avail_vehicles' => (int)$avail_v,
            'bookings'    => (int)$bookings,
            'pending_b'   => (int)$pending_b,
            'confirmed_b' => (int)$confirmed_b,
            'cancelled_b' => (int)$cancelled_b,
            'revenue'     => round((float)$revenue, 2),
            'unread_msgs' => (int)$msgs,
            'courier_co'  => (int)$courier_co,
            'courier_req' => (int)$courier_req,
            'courier_pen' => (int)$courier_pen,
            'movers_co'   => (int)$movers_co,
            'movers_req'  => (int)$movers_req,
            'movers_pen'  => (int)$movers_pen,
            'total_requests' => (int)$bookings + (int)$courier_req + (int)$movers_req,
            'trend'       => $trend,
            'timestamp'   => date('H:i:s'),
        ]);
        break;

    case 'users':
        $rows = $conn->query("SELECT id,username,email,role,phone,is_active,created_at FROM signup WHERE role!='admin' ORDER BY created_at DESC LIMIT 100")->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['users' => $rows]);
        break;

    case 'bookings':
        $rows = $conn->query("
            SELECT b.id, b.user_name, b.user_mobile, b.pickup_location, b.drop_location,
                   b.distance_km, b.total_cost, b.date, b.status, b.created_at,
                   v.vehicle_category, v.owner_name AS driver
            FROM booking b JOIN vehicles v ON b.vehicle_id=v.id
            ORDER BY b.created_at DESC LIMIT 100
        ")->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['bookings' => $rows]);
        break;

    case 'vehicles':
        $rows = $conn->query("SELECT id,owner_name,mobile,email,address,capacity,rate_per_km,vehicle_category,is_available,created_at FROM vehicles ORDER BY created_at DESC LIMIT 100")->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['vehicles' => $rows]);
        break;

    case 'messages':
        $rows = $conn->query("SELECT * FROM support_messages ORDER BY created_at DESC LIMIT 50")->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['messages' => $rows]);
        break;

    case 'toggle_user':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success'=>false]); exit; }
        $uid = intval($_POST['user_id'] ?? 0);
        if (!$uid) { echo json_encode(['success'=>false,'msg'=>'Invalid user']); exit; }
        $row = $conn->query("SELECT is_active,role FROM signup WHERE id=$uid AND role!='admin'")->fetch_assoc();
        if (!$row) { echo json_encode(['success'=>false,'msg'=>'User not found']); exit; }
        $new = $row['is_active'] ? 0 : 1;
        $upd = $conn->prepare('UPDATE signup SET is_active=? WHERE id=?');
        $upd->bind_param('ii', $new, $uid);
        $upd->execute();
        $admin = $conn->real_escape_string($_SESSION['username']);
        $ip    = $conn->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '');
        $conn->query("INSERT INTO admin_logs (admin_user,action,target,ip_address) VALUES ('$admin','toggle_user','user_id:$uid','$ip')");
        echo json_encode(['success'=>true,'is_active'=>$new]);
        break;

    case 'mark_msg_read':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success'=>false]); exit; }
        $mid = intval($_POST['msg_id'] ?? 0);
        if (!$mid) { echo json_encode(['success'=>false]); exit; }
        $upd = $conn->prepare("UPDATE support_messages SET status='read' WHERE id=?");
        $upd->bind_param('i', $mid);
        $upd->execute();
        echo json_encode(['success'=>true]);
        break;

    default:
        echo json_encode(['error'=>'unknown action']);
}
$conn->close();
?>
