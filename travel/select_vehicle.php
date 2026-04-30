<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/login.php'); exit; }

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

$stmt = $conn->prepare("SELECT * FROM vehicles WHERE is_available=1 AND vehicle_category='travel' AND address LIKE CONCAT('%',?,'%') ORDER BY rate_per_km ASC");
$stmt->bind_param("s", $pickup_location);
$stmt->execute();
$vehicles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$navActive = '';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Select Travel Vehicle — Drifter</title>
<style>
.page-hero{background:var(--text,#2a2520);padding:50px 24px 30px;text-align:center;color:white;}
.page-hero h1{font-size:1.8rem;font-weight:800;margin-bottom:6px;}
.page-hero p{color:rgba(255,255,255,0.7);font-size:0.9rem;}
.trip-summary{background:white;padding:16px 24px;box-shadow:0 2px 12px rgba(0,0,0,0.06);display:flex;flex-wrap:wrap;gap:20px;justify-content:center;align-items:center;}
.trip-tag{display:flex;align-items:center;gap:6px;font-size:0.85rem;color:var(--muted);}
.trip-tag strong{color:var(--text);}
.steps-nav{display:flex;justify-content:center;align-items:center;gap:0;padding:20px 24px;background:#f8f9fc;}
.step-item{display:flex;flex-direction:column;align-items:center;gap:6px;position:relative;padding:0 28px;}
.step-item:not(:last-child)::after{content:'';position:absolute;right:-1px;top:18px;width:56px;height:2px;background:#e2e8f0;}
.step-circle{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;background:#e2e8f0;color:#94a3b8;}
.step-item.active .step-circle{background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;box-shadow:0 4px 12px rgba(188,159,139,0.45);}
.step-item.done .step-circle{background:#B5CFB7;color:#2a2520;}
.step-label{font-size:0.72rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;}
.step-item.active .step-label{color:#a08070;}
.vehicles-section{max-width:1280px;margin:0 auto;padding:40px 24px 80px;}
.vehicles-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:24px;}
.v-card{background:white;border-radius:16px;overflow:hidden;box-shadow:var(--shadow);transition:var(--trans);}
.v-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-lg);}
.v-img{width:100%;height:200px;object-fit:cover;}
.v-img-placeholder{width:100%;height:200px;background:linear-gradient(135deg,#f5f3ff,#ede9fe);display:flex;align-items:center;justify-content:center;font-size:3rem;}
.v-body{padding:20px;}
.v-rate{display:inline-block;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;padding:4px 12px;border-radius:50px;font-size:0.8rem;font-weight:700;margin-bottom:12px;}
.v-name{font-size:1.05rem;font-weight:700;color:var(--text);margin-bottom:8px;}
.v-meta{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px;}
.v-meta-item{font-size:0.8rem;color:var(--muted);}
.v-meta-item strong{display:block;color:var(--text);font-size:0.88rem;}
.v-total{background:rgba(202,218,191,0.20);border-radius:8px;padding:10px 14px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;border:1px solid rgba(181,207,183,0.45);}
.v-total span{font-size:0.82rem;color:#3a6b3c;font-weight:500;}
.v-total strong{font-size:1.1rem;color:#3a6b3c;font-weight:800;}
.book-btn{width:100%;padding:12px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border:none;border-radius:10px;font-size:0.95rem;font-weight:700;cursor:pointer;transition:all 0.25s;}
.book-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(188,159,139,0.45);}
.book-btn:disabled{opacity:0.6;cursor:not-allowed;transform:none;}
.no-vehicles{text-align:center;padding:80px 24px;background:white;border-radius:16px;box-shadow:var(--shadow);}
.no-vehicles .icon{font-size:4rem;margin-bottom:16px;}
.no-vehicles h3{font-size:1.4rem;font-weight:700;margin-bottom:8px;}
.no-vehicles p{color:var(--muted);margin-bottom:24px;}
.back-btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border-radius:10px;text-decoration:none;font-weight:600;transition:var(--trans);}
.back-btn:hover{transform:translateY(-2px);}
#toast{position:fixed;top:80px;right:24px;z-index:99999;padding:14px 22px;border-radius:10px;font-weight:600;font-size:0.9rem;box-shadow:0 8px 24px rgba(0,0,0,0.2);transform:translateX(120%);transition:transform 0.4s ease;max-width:320px;}
#toast.show{transform:translateX(0);}
#toast.success{background:#B5CFB7;color:#1a3a1c;}
#toast.error{background:#FFAAB8;color:#5a1a2a;}
</style>
</head>
<body>
<div class="page-hero">
  <h1>🚌 Available Travel Vehicles</h1>
  <p>Select the best vehicle for your journey</p>
</div>
<div class="trip-summary">
  <div class="trip-tag"><i class="fas fa-map-marker-alt" style="color:#7c3aed"></i> <strong><?= htmlspecialchars($pickup_location) ?></strong></div>
  <div class="trip-tag"><i class="fas fa-arrow-right" style="color:var(--muted)"></i></div>
  <div class="trip-tag"><i class="fas fa-map-marker" style="color:#10b981"></i> <strong><?= htmlspecialchars($drop_location) ?></strong></div>
  <div class="trip-tag"><i class="fas fa-road"></i> <strong><?= $distance_km ?> km</strong></div>
  <div class="trip-tag"><i class="fas fa-calendar"></i> <strong><?= date('d M Y', strtotime($date)) ?></strong></div>
</div>
<div class="steps-nav">
  <div class="step-item done"><div class="step-circle"><i class="fas fa-check"></i></div><div class="step-label">Trip Details</div></div>
  <div class="step-item active"><div class="step-circle">2</div><div class="step-label">Select Vehicle</div></div>
  <div class="step-item"><div class="step-circle">3</div><div class="step-label">Confirmed</div></div>
</div>
<div class="vehicles-section">
  <?php if (empty($vehicles)): ?>
    <div class="no-vehicles">
      <div class="icon">🔍</div>
      <h3>No Vehicles Found</h3>
      <p>No travel vehicles are available in <strong><?= htmlspecialchars($pickup_location) ?></strong> right now.</p>
      <a href="booking_step1.php" class="back-btn"><i class="fas fa-arrow-left"></i> Change Location</a>
    </div>
  <?php else: ?>
    <div style="margin-bottom:20px;color:var(--muted);font-size:0.9rem;">
      Found <strong style="color:var(--text)"><?= count($vehicles) ?> vehicle<?= count($vehicles)>1?'s':'' ?></strong> available in <?= htmlspecialchars($pickup_location) ?>
    </div>
    <div class="vehicles-grid">
      <?php foreach ($vehicles as $v):
        $total = floatval($v['rate_per_km']) * $distance_km;
      ?>
      <div class="v-card">
        <?php if ($v['vehicle_image']): ?>
          <img src="<?= BASE ?>/travel/<?= htmlspecialchars($v['vehicle_image']) ?>" class="v-img" alt="Vehicle">
        <?php else: ?>
          <div class="v-img-placeholder">🚌</div>
        <?php endif; ?>
        <div class="v-body">
          <div class="v-rate">₹<?= htmlspecialchars($v['rate_per_km']) ?>/km</div>
          <div class="v-name"><?= htmlspecialchars($v['owner_name']) ?>'s Vehicle</div>
          <div class="v-meta">
            <div class="v-meta-item"><strong><?= htmlspecialchars($v['capacity']) ?> seats</strong>Capacity</div>
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
            <button type="submit" class="book-btn">Book This Vehicle</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<div id="toast"></div>
<?php include '../includes/footer.php'; ?>
<script>
function showToast(msg, type='success') {
  const t = document.getElementById('toast');
  t.textContent = msg; t.className = 'show ' + type;
  setTimeout(() => t.className = '', 4000);
}
document.querySelectorAll('.booking-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('.book-btn');
    btn.disabled = true; btn.textContent = 'Booking...';
    fetch('book_vehicle.php', {method:'POST', body: new FormData(this)})
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          showToast('✅ ' + d.message + ' — Total: ₹' + d.total_cost, 'success');
          setTimeout(() => window.location.href = d.redirect || '<?= BASE ?>/front/index.php?booking_success=true', 3000);
        } else { showToast('❌ ' + d.message, 'error'); btn.disabled=false; btn.textContent='Book This Vehicle'; }
      })
      .catch(() => { showToast('❌ Something went wrong.', 'error'); btn.disabled=false; btn.textContent='Book This Vehicle'; });
  });
});
</script>
</body></html>
