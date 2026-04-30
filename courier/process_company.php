<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/login.php'); exit; }
require_once dirname(__DIR__).'/includes/db.php';
$pdo = courierPDO();

$name      = trim($_POST['company_name'] ?? '');
$desc      = trim($_POST['company_description'] ?? '');
$email     = trim($_POST['company_email'] ?? '');
$phone     = trim($_POST['company_phone'] ?? '');
$address   = trim($_POST['company_address'] ?? '');
$locations = trim($_POST['service_locations'] ?? '');
$services  = isset($_POST['services_offered']) ? implode(',', $_POST['services_offered']) : '';
$svc_type  = $_POST['service_type'] ?? '';
$min_price = floatval($_POST['min_price'] ?? 0);
$max_price = floatval($_POST['max_price'] ?? 0);
$max_weight= floatval($_POST['max_weight'] ?? 0);
$username  = $_SESSION['username'];

if (!$name || !$email || !$phone || !$address || !$locations || !$svc_type || !$min_price || !$max_price) {
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
    $stmt = $pdo->prepare('INSERT INTO companies (owner_username,name,description,email,phone,address,service_locations,services_offered,logo_path) VALUES (?,?,?,?,?,?,?,?,?)');
    $stmt->execute([$username,$name,$desc,$email,$phone,$address,$locations,$services,$logo_path]);
    $cid = $pdo->lastInsertId();

    $stmt2 = $pdo->prepare('INSERT INTO services (company_id,service_type,min_price,max_price,max_weight) VALUES (?,?,?,?,?)');
    $stmt2->execute([$cid,$svc_type,$min_price,$max_price,$max_weight]);
    $pdo->commit();
    header('Location: company_success.php'); exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    header('Location: add_company.php'); exit;
}
?>
