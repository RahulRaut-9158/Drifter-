<?php
session_start();
if (empty($_SESSION['loggedin'])) {
    header('Location: '.BASE.'/login.php?redirect='.urlencode(BASE.'/transport/booking_step1.php')); exit;
}
$navActive = '';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Book Transport — Drifter</title>
<link rel="stylesheet" href="<?= BASE ?>/assets/css/services.css">
<style>
.page-hero {
  padding:72px 24px 56px;text-align:center;color:white;
  position:relative;overflow:hidden;
  background:var(--gradient-primary,linear-gradient(135deg,#0a1628,#0f2b5e));
}
.page-hero::before {
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 70% 40%,rgba(249,115,22,0.22) 0%,transparent 55%);
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

.form-wrap { max-width:720px;margin:36px auto;padding:0 24px 80px; }
</style>
</head>
<body>

<div class="page-hero">
  <div class="tag"><i class="fas fa-truck-moving"></i> Transport Booking</div>
  <h1>Book a Transport Vehicle</h1>
  <p>Find verified transport vehicles near your pickup location</p>
</div>

<div class="steps-nav">
  <div class="step-item active">
    <div class="step-circle">1</div>
    <div class="step-label">Trip Details</div>
  </div>
  <div class="step-item">
    <div class="step-circle">2</div>
    <div class="step-label">Select Vehicle</div>
  </div>
  <div class="step-item">
    <div class="step-circle">3</div>
    <div class="step-label">Confirmed</div>
  </div>
</div>

<div class="form-wrap">
  <div class="form-card reveal">
    <h2><i class="fas fa-clipboard-list"></i> Enter Your Booking Details</h2>
    <div class="user-info-bar">
      <i class="fas fa-user-check"></i>
      Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
    </div>
    <form action="select_vehicle.php" method="POST" id="bookForm">
      <div class="form-grid">
        <div class="form-group">
          <label>Your Name</label>
          <input type="text" name="user_name" value="<?= htmlspecialchars($_SESSION['username']) ?>" required>
        </div>
        <div class="form-group">
          <label>Mobile Number</label>
          <input type="text" name="user_mobile" id="mob" placeholder="10-digit number" required>
          <span class="err-msg">Enter a valid 10-digit number</span>
        </div>
        <div class="form-group full">
          <label>Pickup Location / City</label>
          <input type="text" name="pickup_location" placeholder="e.g. Satara, Maharashtra" required>
        </div>
        <div class="form-group full">
          <label>Drop Location / City</label>
          <input type="text" name="drop_location" placeholder="e.g. Pune, Maharashtra" required>
        </div>
        <div class="form-group">
          <label>Date</label>
          <input type="date" name="date" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="form-group">
          <label>Time</label>
          <input type="time" name="time" required>
        </div>
        <div class="form-group full">
          <label>Distance (km)</label>
          <input type="number" name="distance_km" placeholder="Approximate distance in km" min="1" required>
        </div>
      </div>
      <button type="submit" class="submit-btn" id="sb">
        <i class="fas fa-search"></i> Find Available Vehicles
      </button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="<?= BASE ?>/assets/js/effects.js"></script>
<script>
const mob = document.getElementById('mob');
mob.addEventListener('input', () => {
  const ok = /^[0-9]{10}$/.test(mob.value.trim());
  mob.closest('.form-group').classList.toggle('has-err', mob.value && !ok);
});
document.getElementById('bookForm').addEventListener('submit', function(e) {
  if (!/^[0-9]{10}$/.test(mob.value.trim())) {
    e.preventDefault();
    mob.closest('.form-group').classList.add('has-err');
    mob.focus(); return;
  }
  const b = document.getElementById('sb');
  b.disabled = true;
  b.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
});
</script>
</body>
</html>
