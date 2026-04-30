<?php
session_start();
require_once dirname(__DIR__).'/includes/db.php';
$pdo = courierPDO();

$pickup = isset($_GET['pickup']) ? trim($_GET['pickup']) : '';
$city   = explode(',', $pickup)[0];

try {
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE service_locations LIKE ? ORDER BY rating DESC");
    $stmt->execute(["%$city%"]);
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $companies = [];
}

$navActive = '';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Courier Services — Drifter</title>
<link rel="stylesheet" href="<?= BASE ?>/assets/css/services.css">
<style>
.page-hero {
  padding:64px 24px 48px;text-align:center;color:white;
  position:relative;overflow:hidden;
  background:linear-gradient(135deg,#0a1628,#0f2b5e);
}
.page-hero::before {
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 70% 40%,rgba(249,115,22,0.20) 0%,transparent 55%);
}
.page-hero::after {
  content:'';position:absolute;bottom:-2px;left:0;right:0;height:60px;
  background:#f1f5f9;clip-path:ellipse(55% 100% at 50% 100%);
}
.page-hero > * { position:relative;z-index:1; }
.page-hero h1 { font-size:clamp(1.6rem,3vw,2rem);font-weight:800;margin-bottom:8px; }
.page-hero p { color:rgba(255,255,255,0.65);font-size:0.9rem; }
.results-wrap { max-width:1280px;margin:0 auto;padding:40px 24px 80px; }
.results-header { margin-bottom:28px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px; }
.results-header h2 { font-size:1.2rem;font-weight:700;color:#0f172a; }
.results-header p { color:#64748b;font-size:0.88rem; }
</style>
</head>
<body>

<div class="page-hero">
  <h1><i class="fas fa-box"></i> Available Courier Services</h1>
  <p>Based on your pickup location: <strong><?= htmlspecialchars($pickup) ?></strong></p>
</div>

<div class="results-wrap">
  <?php if (empty($companies)): ?>
    <div class="no-results reveal">
      <div class="icon">🔍</div>
      <h3>No Services Found</h3>
      <p>No courier companies found serving <strong><?= htmlspecialchars($city) ?></strong>. Try a different address.</p>
      <a href="courier.php" class="back-btn"><i class="fas fa-arrow-left"></i> Try Again</a>
    </div>
  <?php else: ?>
    <div class="results-header">
      <div>
        <h2>Found <?= count($companies) ?> courier service<?= count($companies)>1?'s':'' ?></h2>
        <p>Serving <?= htmlspecialchars($city) ?> and surrounding areas</p>
      </div>
    </div>
    <div class="providers-grid">
      <?php foreach ($companies as $c):
        $svcStmt = $pdo->prepare("SELECT * FROM services WHERE company_id=?");
        $svcStmt->execute([$c['id']]);
        $services = $svcStmt->fetchAll(PDO::FETCH_ASSOC);
        $typeLabels = ['same_day'=>'Same Day','next_day'=>'Next Day','standard'=>'Standard','international'=>'International'];
      ?>
      <div class="p-card reveal">
        <?php if ($c['logo_path']): ?>
          <img src="<?= BASE ?>/courier/<?= htmlspecialchars($c['logo_path']) ?>" class="p-logo" alt="Logo" loading="lazy">
        <?php else: ?>
          <div class="p-logo-ph"><i class="fas fa-box" style="color:rgba(249,115,22,0.60);font-size:3rem;"></i></div>
        <?php endif; ?>
        <div class="p-body">
          <div class="p-name"><?= htmlspecialchars($c['name']) ?></div>
          <div class="p-rating">
            <div class="stars">
              <?php for($i=1;$i<=5;$i++) echo $i<=round($c['rating']) ? '★' : '☆'; ?>
            </div>
            <span><?= number_format($c['rating'],1) ?> (<?= $c['reviews'] ?> reviews)</span>
          </div>
          <div class="p-desc"><?= htmlspecialchars($c['description']) ?></div>
          <?php if (!empty($services)): ?>
          <div class="p-pricing">
            <?php foreach ($services as $s): ?>
            <div class="p-pricing-row">
              <span><?= $typeLabels[$s['service_type']] ?? $s['service_type'] ?></span>
              <strong>₹<?= number_format($s['min_price']) ?> – ₹<?= number_format($s['max_price']) ?><?= $s['max_weight'] ? ' (up to '.$s['max_weight'].'kg)' : '' ?></strong>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <div class="p-services">
            <?php foreach (array_map('trim', explode(',', $c['services_offered'])) as $sv): ?>
              <span class="svc-tag"><?= ucfirst(str_replace('_',' ',$sv)) ?></span>
            <?php endforeach; ?>
          </div>
          <div class="p-locations"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($c['address']) ?></div>
          <button class="contact-btn"
            data-phone="<?= htmlspecialchars($c['phone']) ?>"
            data-email="<?= htmlspecialchars($c['email']) ?>"
            data-name="<?= htmlspecialchars($c['name']) ?>"
            onclick="showContact(this)">
            <i class="fas fa-phone"></i> Contact Company
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

<div class="modal-overlay" id="contactModal" onclick="if(event.target===this)closeModal()">
  <div class="modal-box">
    <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#f97316,#fb923c);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.4rem;color:white;box-shadow:0 4px 14px rgba(249,115,22,0.40);">
      <i class="fas fa-building"></i>
    </div>
    <h3 id="modalName"></h3>
    <div class="m-row"><i class="fas fa-phone"></i><a id="modalPhone" href="#"></a></div>
    <div class="m-row"><i class="fas fa-envelope"></i><a id="modalEmail" href="#"></a></div>
    <button class="modal-close" onclick="closeModal()">Close</button>
  </div>
</div>

<script src="<?= BASE ?>/assets/js/effects.js"></script>
<script>
function showContact(btn) {
  document.getElementById('modalName').textContent = btn.dataset.name;
  const ph = document.getElementById('modalPhone');
  ph.textContent = btn.dataset.phone; ph.href = 'tel:' + btn.dataset.phone;
  const em = document.getElementById('modalEmail');
  em.textContent = btn.dataset.email; em.href = 'mailto:' + btn.dataset.email;
  document.getElementById('contactModal').classList.add('show');
}
function closeModal() { document.getElementById('contactModal').classList.remove('show'); }
document.addEventListener('keydown', e => { if(e.key==='Escape') closeModal(); });
</script>
</body>
</html>
