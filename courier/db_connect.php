<?php
// Use central DB connection — works on both XAMPP and InfinityFree
require_once dirname(__DIR__) . '/includes/db.php';
$pdo = courierPDO();
function courierPDO_local() { return courierPDO(); }
?>
