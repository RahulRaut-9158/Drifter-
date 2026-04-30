<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: '.BASE.'/login.php?redirect='.urlencode(BASE.'/courier/courier.php')); exit;
}
$navActive = '';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Send Courier — Drifter</title>
<link rel="stylesheet" href="<?= BASE ?>/assets/css/services.css">
<style>
.page-hero {
  padding:72px 24px 56px;text-align:center;color:white;
  position:relative;overflow:hidden;
  background:linear-gradient(135deg,#0a1628,#0f2b5e);
}
.page-hero::before {
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 30% 60%,rgba(249,115,22,0.22) 0%,transparent 55%);
}
.page-hero::after {
  content:'';position:absolute;bottom:-2px;left:0;right:0;height:60px;
  background:#f1f5f9;clip-path:ellipse(55% 100% at 50% 100%);
}
.page-hero > * { position:relative;z-index:1; }
.page-hero .tag {
  display:inline-block;padding:4px 14px;border-radius:50px;
  background:rgba(249,115,22,0.15);color:#fb923c;
  font-size:0.72rem;font-weight:700;letter-spacing:1.8px;
  text-transform:uppercase;margin-bottom:12px;
  border:1px solid rgba(249,115,22,0.25);
}
.page-hero h1 { font-size:clamp(1.8rem,4vw,2.4rem);font-weight:800;margin-bottom:10px; }
.page-hero p { color:rgba(255,255,255,0.65);font-size:0.95rem; }
.form-wrap { max-width:860px;margin:36px auto;padding:0 24px 80px; }

/* How it works */
.how-it-works {
  background:white;padding:40px 24px;
  border-bottom:1px solid #f1f5f9;
}
.how-inner { max-width:860px;margin:0 auto; }
.how-inner h3 {
  font-size:0.78rem;font-weight:700;color:#64748b;
  text-transform:uppercase;letter-spacing:1.5px;
  text-align:center;margin-bottom:24px;
}
.how-steps { display:grid;grid-template-columns:repeat(3,1fr);gap:20px; }
.how-step { text-align:center; }
.how-step-num {
  width:44px;height:44px;border-radius:50%;
  background:linear-gradient(135deg,#f97316,#fb923c);
  color:white;font-weight:800;font-size:1rem;
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 10px;
  box-shadow:0 4px 14px rgba(249,115,22,0.35);
}
.how-step h4 { font-size:0.88rem;font-weight:700;color:#0f172a;margin-bottom:4px; }
.how-step p { font-size:0.78rem;color:#64748b; }
@media(max-width:600px) { .how-steps { grid-template-columns:1fr; } }
</style>
</head>
<body>

<div class="page-hero">
  <div class="tag"><i class="fas fa-box"></i> Courier Service</div>
  <h1>Send a Courier</h1>
  <p>Find trusted courier services near you — fast, reliable, and affordable</p>
</div>

<!-- How it works -->
<div class="how-it-works">
  <div class="how-inner">
    <h3>How It Works</h3>
    <div class="how-steps">
      <div class="how-step reveal">
        <div class="how-step-num">1</div>
        <h4>Fill Details</h4>
        <p>Enter sender, receiver, and package information</p>
      </div>
      <div class="how-step reveal reveal-delay-1">
        <div class="how-step-num">2</div>
        <h4>Find Services</h4>
        <p>Browse verified courier companies in your area</p>
      </div>
      <div class="how-step reveal reveal-delay-2">
        <div class="how-step-num">3</div>
        <h4>Contact & Ship</h4>
        <p>Contact the company and schedule your pickup</p>
      </div>
    </div>
  </div>
</div>

<div class="form-wrap">
  <div class="form-card reveal">
    <h2><i class="fas fa-box"></i> Package & Delivery Details</h2>
    <form action="process_request.php" method="POST" id="courierForm">

      <div class="section-divider">Sender Information</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Sender Name</label>
          <input type="text" name="sender_name" value="<?= htmlspecialchars($_SESSION['username']) ?>" required>
        </div>
        <div class="form-group">
          <label>Sender Phone</label>
          <input type="tel" name="sender_phone" id="senderPhone" placeholder="10-digit number" required>
          <span class="err-msg">Enter a valid 10-digit number</span>
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
          <input type="tel" name="receiver_phone" id="receiverPhone" placeholder="10-digit number" required>
          <span class="err-msg">Enter a valid 10-digit number</span>
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
      <div class="form-group">
        <label>Package Description (optional)</label>
        <textarea name="package_details" placeholder="Dimensions, weight, fragile items, special instructions..."></textarea>
      </div>

      <button type="submit" class="submit-btn" id="submitBtn">
        <i class="fas fa-search"></i> Find Courier Services
      </button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="<?= BASE ?>/assets/js/effects.js"></script>
<script>
function validatePhone(input) {
  input.addEventListener('input', () => {
    const ok = /^[0-9]{10}$/.test(input.value.trim());
    input.closest('.form-group').classList.toggle('has-err', input.value && !ok);
  });
}
validatePhone(document.getElementById('senderPhone'));
validatePhone(document.getElementById('receiverPhone'));

document.getElementById('courierForm').addEventListener('submit', function(e) {
  const phones = [document.getElementById('senderPhone'), document.getElementById('receiverPhone')];
  let valid = true;
  phones.forEach(p => {
    if (!/^[0-9]{10}$/.test(p.value.trim())) {
      p.closest('.form-group').classList.add('has-err');
      valid = false;
    }
  });
  if (!valid) { e.preventDefault(); return; }
  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
});
</script>
</body>
</html>
