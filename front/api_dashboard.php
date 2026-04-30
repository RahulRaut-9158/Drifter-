<?php
session_start();
require_once dirname(__DIR__).'/includes/db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['loggedin'])) { echo json_encode(['error'=>'unauthorized']); exit; }

$conn = db();

$username = $_SESSION['username'];
$category = in_array($_GET['category']??'',['transport','travel']) ? $_GET['category'] : 'transport';

// Fetch all vehicles for this user+category
$vstmt = $conn->prepare("SELECT * FROM vehicles WHERE owner_name=? AND vehicle_category=? ORDER BY created_at DESC");
$vstmt->bind_param("ss",$username,$category);
$vstmt->execute();
$vehicles = $vstmt->get_result()->fetch_all(MYSQLI_ASSOC);

$out = ['vehicles'=>[], 'summary'=>['total_vehicles'=>0,'total_bookings'=>0,'total_earnings'=>0,'pending'=>0,'confirmed'=>0], 'timestamp'=>date('H:i:s')];

foreach ($vehicles as $v) {
    $vid = (int)$v['id'];
    $bstmt = $conn->prepare("SELECT * FROM booking WHERE vehicle_id=? ORDER BY created_at DESC");
    $bstmt->bind_param("i",$vid); $bstmt->execute();
    $bookings = $bstmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $earnings  = array_sum(array_column(array_filter($bookings,fn($b)=>$b['status']!=='Cancelled'),'total_cost'));
    $pending   = count(array_filter($bookings,fn($b)=>$b['status']==='Pending'));
    $confirmed = count(array_filter($bookings,fn($b)=>$b['status']==='Confirmed'));

    $out['vehicles'][$vid] = [
        'id'           => $vid,
        'owner_name'   => $v['owner_name'],
        'mobile'       => $v['mobile'],
        'email'        => $v['email'],
        'address'      => $v['address'],
        'capacity'     => $v['capacity'],
        'rate_per_km'  => $v['rate_per_km'],
        'vehicle_image'=> $v['vehicle_image'],
        'is_available' => (int)$v['is_available'],
        'created_at'   => $v['created_at'],
        'stats'        => ['earnings'=>round($earnings,2),'pending'=>$pending,'confirmed'=>$confirmed,'total'=>count($bookings)],
        'bookings'     => array_map(fn($b)=>[
            'id'=>$b['id'],'user_name'=>$b['user_name'],'user_mobile'=>$b['user_mobile'],
            'pickup_location'=>$b['pickup_location'],'drop_location'=>$b['drop_location'],
            'distance_km'=>$b['distance_km'],'total_cost'=>$b['total_cost'],
            'date'=>$b['date'],'time'=>$b['time'],'status'=>$b['status'],'created_at'=>$b['created_at']
        ], $bookings)
    ];

    $out['summary']['total_bookings'] += count($bookings);
    $out['summary']['total_earnings'] += $earnings;
    $out['summary']['pending']        += $pending;
    $out['summary']['confirmed']      += $confirmed;
}
$out['summary']['total_vehicles']  = count($vehicles);
$out['summary']['total_earnings']  = round($out['summary']['total_earnings'],2);
echo json_encode($out);
$conn->close();
?>
