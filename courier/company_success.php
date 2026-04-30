<?php
session_start();
require_once dirname(__DIR__).'/includes/db.php';
if (!isset($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/login.php'); exit; }
$navActive = '';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Company Registered — Drifter</title>
</head>
<body>
<div style="max-width:560px;margin:80px auto 80px;text-align:center;background:white;padding:48px 40px;border-radius:16px;box-shadow:var(--shadow);border:1px solid var(--border,#dddece);">
  <div style="font-size:4rem;margin-bottom:16px;">✅</div>
  <h2 style="font-size:1.5rem;font-weight:800;color:var(--text,#2a2520);margin-bottom:12px;">Courier Company Registered!</h2>
  <p style="color:var(--muted,#7a7060);margin-bottom:32px;line-height:1.7;">Your courier company has been added to Drifter. Customers in your service area will now be able to find and contact you.</p>
  <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
    <a href="company_info.php" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border-radius:10px;text-decoration:none;font-weight:600;transition:filter 0.2s;" onmouseover="this.style.filter='brightness(1.08)'" onmouseout="this.style.filter=''"><i class="fas fa-building"></i> View My Companies</a>
    <a href="<?= BASE ?>/front/index.php" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:var(--text,#2a2520);color:white;border-radius:10px;text-decoration:none;font-weight:600;transition:filter 0.2s;" onmouseover="this.style.filter='brightness(1.15)'" onmouseout="this.style.filter=''"><i class="fas fa-home"></i> Back to Home</a>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>
