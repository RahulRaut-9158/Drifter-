<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/login.php?redirect='.urlencode(BASE.'/courier/courier.php')); exit; }
$navActive = '';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Send Courier — Drifter</title>
<style>
.page-hero{
  padding:70px 24px 50px;text-align:center;color:white;
  position:relative;overflow:hidden;
}
.page-hero::before{
  content:'';position:absolute;inset:0;
  background:url('https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?w=1920&q=80') center/cover no-repeat;
  filter:brightness(0.20) saturate(1.1);
}
.page-hero::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(42,37,32,0.92) 0%,rgba(188,159,139,0.38) 100%);
}
.page-hero h1{font-size:2.2rem;font-weight:800;margin-bottom:8px;position:relative;z-index:1;}
.page-hero p{color:rgba(255,255,255,0.75);font-size:0.95rem;position:relative;z-index:1;}
.form-wrap{max-width:860px;margin:40px auto;padding:0 24px 80px;}
.form-card{background:white;border-radius:16px;padding:40px;box-shadow:var(--shadow);}
.form-card h2{font-size:1.2rem;font-weight:700;margin-bottom:24px;padding-bottom:14px;border-bottom:2px solid #f1f5f9;display:flex;align-items:center;gap:8px;}
.section-divider{font-size:0.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin:24px 0 16px;display:flex;align-items:center;gap:10px;}
.section-divider::before,.section-divider::after{content:'';flex:1;height:1px;background:#f1f5f9;}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
.form-group{display:flex;flex-direction:column;gap:6px;}
.form-group.full{grid-column:span 2;}
.form-group label{font-size:0.82rem;font-weight:600;color:#475569;}
.form-group input,.form-group textarea{padding:12px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:0.95rem;transition:all 0.25s;outline:none;font-family:inherit;background:#fafafa;}
.form-group input:focus,.form-group textarea:focus{border-color:#BC9F8B;background:white;box-shadow:0 0 0 3px rgba(188,159,139,0.18);}
.form-group textarea{min-height:100px;resize:vertical;}
.submit-btn{width:100%;padding:15px;margin-top:24px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border:none;border-radius:10px;font-size:1rem;font-weight:700;cursor:pointer;transition:all 0.25s;}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(188,159,139,0.40);}
.submit-btn:disabled{opacity:0.6;cursor:not-allowed;transform:none;}
@media(max-width:600px){.form-grid{grid-template-columns:1fr;}.form-group.full{grid-column:span 1;}}
</style>
</head>
<body>
<div class="page-hero">
  <h1>📦 Send a Courier</h1>
  <p>Find trusted courier services near you — fast, reliable, and affordable</p>
</div>
<div class="form-wrap">
  <div class="form-card">
    <h2><i class="fas fa-box" style="color:#0891b2"></i> Package & Delivery Details</h2>
    <form action="process_request.php" method="POST" id="courierForm">
      <div class="section-divider">Sender Information</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Sender Name</label>
          <input type="text" name="sender_name" value="<?= htmlspecialchars($_SESSION['username']) ?>" required>
        </div>
        <div class="form-group">
          <label>Sender Phone</label>
          <input type="tel" name="sender_phone" placeholder="10-digit number" required>
        </div>
        <div class="form-group">
          <label>Pickup Address</label>
          <input type="text" name="sender_address" placeholder="Street, City, PIN Code" required>
        </div>
        <div class="form-group">
          <label>Pickup Date</label>
          <input type="date" name="pickup_date" min="<?= date('Y-m-d') ?>" required>
        </div>
      </div>
      <div class="section-divider">Receiver Information</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Receiver Name</label>
          <input type="text" name="receiver_name" placeholder="Receiver's full name" required>
        </div>
        <div class="form-group">
          <label>Receiver Phone</label>
          <input type="tel" name="receiver_phone" placeholder="10-digit number" required>
        </div>
        <div class="form-group">
          <label>Delivery Address</label>
          <input type="text" name="receiver_address" placeholder="Street, City, PIN Code" required>
        </div>
        <div class="form-group">
          <label>Expected Delivery Date</label>
          <input type="date" name="delivery_date" min="<?= date('Y-m-d') ?>" required>
        </div>
      </div>
      <div class="section-divider">Package Details</div>
      <div class="form-grid">
        <div class="form-group full">
          <label>Package Description (optional)</label>
          <textarea name="package_details" placeholder="Dimensions, weight, fragile items, special instructions..."></textarea>
        </div>
      </div>
      <button type="submit" class="submit-btn" id="submitBtn">
        <i class="fas fa-search"></i> Find Courier Services
      </button>
    </form>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
<script>
document.getElementById('courierForm').addEventListener('submit', function() {
  const btn = document.getElementById('submitBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
});
</script>
</body></html>
