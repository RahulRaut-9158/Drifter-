<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['loggedin'])) { header('Location: '.BASE.'/login.php'); exit; }

$conn = db();
$user_name       = trim($_POST['user_name'] ?? '');
$user_mobile     = trim($_POST['user_mobile'] ?? '');
$pickup_location = trim($_POST['pickup_location'] ?? '');
$drop_location   = trim($_POST['drop_location'] ?? '');
$distance_km     = floatval($_POST['distance_km'] ?? 0);
$date            = $_POST['date'] ?? date('Y-m-d');
$time            = $_POST['time'] ?? date('H:i');

if (!$user_name || !$user_mobile || !$pickup_location || !$drop_location || !$distance_km) {
    header("Location: booking_step1.php"); exit;
}

$stmt = $conn->prepare("SELECT * FROM vehicles WHERE is_available=1 AND vehicle_category='transport' AND address LIKE CONCAT('%',?,'%') ORDER BY rate_per_km ASC");
$stmt->bind_param("s", $pickup_location);
$stmt->execute();
$vehicles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$navActive = '';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Select Transport Vehicle — Drifter</title>
<link rel="stylesheet" href="<?= BASE ?>/assets/css/services.css">
<style>
.page-hero {
  padding:64px 24px 48px;text-align:center;color:white;
  position:relative;overflow:hidden;
  background:linear-gradient(135deg,#0a1628,#0f2b5e);
}
.page-hero::before {
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 30% 60%,rgba(249,115,22,0.20) 0%,transparent 55%);
}
.page-hero::after {
  content:'';position:absolute;bottom:-2px;left:0;right:0;height:60px;
  background:#f1f5f9;clip-path:ellipse(55% 100% at 50% 100%);
}
.page-hero > * { position:relative;z-index:1; }
.page-hero h1 { font-size:clamp(1.6rem,3vw,2rem);font-weight:800;margin-bottom:8px; }
.page-hero p { color:rgba(255,255,255,0.65);font-size:0.9rem; }
.vehicles-section { max-width:1280px;margin:0 auto;padding:40px 24px 80px; }
.results-meta {
  margin-bottom:24px;display:flex;align-items:center;
  justify-content:space-between;flex-wrap:wrap;gap:12px;
}
.results-meta p { color:#64748b;font-size:0.9rem; }
.results-meta strong { color:#0f172a; }
.sort-bar {
  display:flex;align-items:center;gap:8px;font-size:0.85rem;color:#64748b;
}
.sort-bar select {
  padding:6px 10px;border:1.5px solid #e2e8f0;border-radius:8px;
  font-size:0.83rem;outline:none;cursor:pointer;
  transition:border-color 0.2s;
}
.sort-bar select:focus { border-color:#f97316; }
</style>
</head>
<body>

<div class="page-hero">
  <h1><i class="fas fa-truck-moving"></i> Available Transport Vehicles</h1>
  <p>Select the best vehicle for your trip</p>
</div>

<div class="trip-bar">
  <div class="trip-tag"><i class="fas fa-map-marker-alt"></i> <strong><?= htmlspecialchars($pickup_location) ?></strong></div>
  <div class="trip-tag"><i class="fas fa-arrow-right" style="color:#64748b;"></i></div>
  <div class="trip-tag"><i class="fas fa-map-marker" style="color:#22c55e;"></i> <strong><?= htmlspecialchars($drop_location) ?></strong></div>
  <div class="trip-tag"><i class="fas fa-road"></i> <strong><?= $distance_km ?> km</strong></div>
  <div class="trip-tag"><i class="fas fa-calendar"></i> <strong><?= date('d M Y', strtotime($date)) ?></strong></div>
  <div class="trip-tag"><i class="fas fa-clock"></i> <strong><?= $time ?></strong></div>
</div>

<div class="steps-nav">
  <div class="step-item done"><div class="step-circle"><i class="fas fa-check"></i></div><div class="step-label">Trip Details</div></div>
  <div class="step-item active"><div class="step-circle">2</div><div class="step-label">Select Vehicle</div></div>
  <div class="step-item"><div class="step-circle">3</div><div class="step-label">Confirmed</div></div>
</div>

<div class="vehicles-section">
  <?php if (empty($vehicles)): ?>
    <div class="no-results reveal">
      <div class="icon">🔍</div>
      <h3>No Vehicles Found</h3>
      <p>No transport vehicles are available in <strong><?= htmlspecialchars($pickup_location) ?></strong> right now. Try a nearby city or check back later.</p>
      <a href="booking_step1.php" class="back-btn"><i class="fas fa-arrow-left"></i> Change Location</a>
    </div>
  <?php else: ?>
    <div class="results-meta">
      <p>Found <strong><?= count($vehicles) ?> vehicle<?= count($vehicles)>1?'s':'' ?></strong> available in <?= htmlspecialchars($pickup_location) ?></p>
      <div class="sort-bar">
        <i class="fas fa-sort"></i> Sort by:
        <select onchange="sortVehicles(this.value)">
          <option value="rate">Rate (Low to High)</option>
          <option value="capacity">Capacity</option>
        </select>
      </div>
    </div>
    <div class="vehicles-grid" id="vehiclesGrid">
      <?php foreach ($vehicles as $v):
        $total = floatval($v['rate_per_km']) * $distance_km;
      ?>
      <div class="v-card reveal" data-rate="<?= $v['rate_per_km'] ?>" data-capacity="<?= $v['capacity'] ?>">
        <?php if ($v['vehicle_image']): ?>
          <img src="<?= BASE ?>/transport/<?= htmlspecialchars($v['vehicle_image']) ?>" class="v-img" alt="Vehicle" loading="lazy">
        <?php else: ?>
          <div class="v-img-placeholder"><i class="fas fa-truck" style="color:rgba(249,115,22,0.60);font-size:3rem;"></i></div>
        <?php endif; ?>
        <div class="v-body">
          <div class="v-rate">₹<?= htmlspecialchars($v['rate_per_km']) ?>/km</div>
          <div class="v-name"><?= htmlspecialchars($v['owner_name']) ?>'s Vehicle</div>
          <div class="v-meta">
            <div class="v-meta-item"><strong><?= htmlspecialchars($v['capacity']) ?> tons</strong>Capacity</div>
            <div class="v-meta-item"><strong><?= htmlspecialchars($v['mobile']) ?></strong>Contact</div>
            <div class="v-meta-item" style="grid-column:span 2"><strong><?= htmlspecialchars($v['address']) ?></strong>Base Location</div>
          </div>
          <div class="v-total">
            <span>Estimated Total (<?= $distance_km ?>km)</span>
            <strong>₹<?= number_format($total, 2) ?></strong>
          </div>
          <form class="booking-form">
            <input type="hidden" name="vehicle_id" value="<?= $v['id'] ?>">
            <input type="hidden" name="user_name" value="<?= htmlspecialchars($user_name) ?>">
            <input type="hidden" name="user_mobile" value="<?= htmlspecialchars($user_mobile) ?>">
            <input type="hidden" name="pickup_location" value="<?= htmlspecialchars($pickup_location) ?>">
            <input type="hidden" name="drop_location" value="<?= htmlspecialchars($drop_location) ?>">
            <input type="hidden" name="distance_km" value="<?= $distance_km ?>">
            <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
            <input type="hidden" name="time" value="<?= htmlspecialchars($time) ?>">
            <button type="submit" class="book-btn">
              <i class="fas fa-check-circle"></i> Book This Vehicle
            </button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
<script src="<?= BASE ?>/assets/js/effects.js"></script>
<script>
document.querySelectorAll('.booking-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('.book-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking...';
    fetch('book_vehicle.php', { method:'POST', body: new FormData(this) })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          if (window.showToast) showToast('✅ ' + d.message + ' — Total: ₹' + d.total_cost, 'success', 5000);
          btn.innerHTML = '<i class="fas fa-check"></i> Booked!';
          btn.style.background = 'linear-gradient(135deg,#22c55e,#16a34a)';
          setTimeout(() => window.location.href = d.redirect || '<?= BASE ?>/front/dashboard_customer.php', 2500);
        } else {
          if (window.showToast) showToast('❌ ' + d.message, 'error');
          btn.disabled = false;
          btn.innerHTML = '<i class="fas fa-check-circle"></i> Book This Vehicle';
        }
      })
      .catch(() => {
        if (window.showToast) showToast('❌ Something went wrong. Try again.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Book This Vehicle';
      });
  });
});

function sortVehicles(by) {
  const grid = document.getElementById('vehiclesGrid');
  const cards = [...grid.querySelectorAll('.v-card')];
  cards.sort((a, b) => parseFloat(a.dataset[by]) - parseFloat(b.dataset[by]));
  cards.forEach(c => grid.appendChild(c));
}
</script>
</body>
</html>
