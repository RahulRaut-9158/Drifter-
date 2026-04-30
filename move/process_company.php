<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/login.php'); exit; }
require_once dirname(__DIR__) . '/includes/db.php';
$pdo = moversPDO();

// Table names depend on environment
$tCompanies = SINGLE_DB ? 'movers_companies' : 'companies';
$tServices  = SINGLE_DB ? 'movers_services'  : 'services';

$name      = trim($_POST['company_name'] ?? '');
$desc      = trim($_POST['company_description'] ?? '');
$email     = trim($_POST['company_email'] ?? '');
$phone     = trim($_POST['company_phone'] ?? '');
$address   = trim($_POST['company_address'] ?? '');
$locations = trim($_POST['service_locations'] ?? '');
$services  = isset($_POST['services_offered']) ? implode(',', $_POST['services_offered']) : '';
$svc_type  = $_POST['service_type'] ?? '';
$prop_type = $_POST['property_type'] ?? '';
$min_price = floatval($_POST['min_price'] ?? 0);
$max_price = floatval($_POST['max_price'] ?? 0);
$username  = $_SESSION['username'];

$valid_props = ['1bhk','2bhk','3bhk','villa','office'];
$valid_works = ['packing','moving','packing_moving','full_service','vehicle','international'];

if (!$name || !$email || !$phone || !$address || !$locations || !in_array($svc_type,$valid_works) || !in_array($prop_type,$valid_props) || !$min_price || !$max_price) {
    header('Location: add_company.php'); exit;
}

$logo_path = '';
if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
    if (!is_dir('uploads')) mkdir('uploads', 0755, true);
    $ext = strtolower(pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
        $fname = uniqid() . '_' . basename($_FILES['company_logo']['name']);
        if (move_uploaded_file($_FILES['company_logo']['tmp_name'], 'uploads/' . $fname)) {
            $logo_path = 'uploads/' . $fname;
        }
    }
}

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO $tCompanies (owner_username,name,description,email,phone,address,service_locations,services_offered,logo_path) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$username,$name,$desc,$email,$phone,$address,$locations,$services,$logo_path]);
    $cid = $pdo->lastInsertId();

    $stmt2 = $pdo->prepare("INSERT INTO $tServices (company_id,service_type,min_price,max_price,property_type) VALUES (?,?,?,?,?)");
    $stmt2->execute([$cid,$svc_type,$min_price,$max_price,$prop_type]);
    $pdo->commit();
    header('Location: company_success.php'); exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    header('Location: add_company.php'); exit;
}
?>
