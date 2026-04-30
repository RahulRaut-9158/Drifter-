<?php
session_start();
require_once dirname(__DIR__) . '/includes/db.php';
$pdo = moversPDO();

$tCompanies = SINGLE_DB ? 'movers_companies' : 'companies';
$tServices  = SINGLE_DB ? 'movers_services'  : 'services';

$current  = isset($_GET['current']) ? trim($_GET['current']) : '';
$new      = isset($_GET['new'])     ? trim($_GET['new'])     : '';
$propType = isset($_GET['type'])    ? trim($_GET['type'])    : '';
$workType = isset($_GET['work'])    ? trim($_GET['work'])    : '';
$city     = explode(',', $current)[0];

$propLabels = ['1bhk'=>'1 BHK','2bhk'=>'2 BHK','3bhk'=>'3 BHK','villa'=>'Villa','office'=>'Office'];
$workLabels = ['packing'=>'Packing Only','moving'=>'Moving Only','packing_moving'=>'Packing + Moving','full_service'=>'Full Service','vehicle'=>'Vehicle Transport','international'=>'International'];

try {
    $stmt = $pdo->prepare("SELECT DISTINCT c.* FROM $tCompanies c JOIN $tServices s ON c.id=s.company_id WHERE s.service_type=? AND s.property_type=? AND c.service_locations LIKE ? ORDER BY c.rating DESC");
    $stmt->execute([$workType, $propType, "%$city%"]);
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $companies = []; }

$navActive = '';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Packers & Movers — Drifter</title>
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
.results-wrap { max-width:1280px;margin:0 auto;padding:40px 24px 80px; }
.results-header { margin-bottom:28px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px; }
.results-header h2 { font-size:1.2rem;font-weight:700;color:#0f172a; }
.results-header p { color:#64748b;font-size:0.88rem; }
</style>
</head>
<body>

<div class="page-hero">
  <h1><i class="fas fa-people-carry"></i> Available Packers & Movers</h1>
  <p><?= htmlspecialchars($workLabels[$workType] ?? $workType) ?> service for <?= htmlspecialchars($propLabels[$propType] ?? $propType) ?></p>
</div>

<div class="trip-bar">
  <div class="trip-tag"><i class="fas fa-map-marker-alt"></i> <strong><?= htmlspecialchars($city) ?></strong></div>
  <div class="trip-tag"><i class="fas fa-arrow-right" style="color:#64748b;"></i></div>
  <div class="trip-tag"><i class="fas fa-map-marker" style="color:#22c55e;"></i> <strong><?= htmlspecialchars(explode(',',$new)[0]) ?></strong></div>
  <div class="trip-tag"><i class="fas fa-home"></i> <strong><?= htmlspecialchars($propLabels[$propType] ?? $propType) ?></strong></div>
  <div class="trip-tag"><i class="fas fa-tools"></i> <strong><?= htmlspecialchars($workLabels[$workType] ?? $workType) ?></strong></div>
</div>

<div class="results-wrap">
  <?php if (empty($companies)): ?>
    <div class="no-results reveal">
      <div class="icon">🔍</div>
      <h3>No Companies Found</h3>
      <p>No packers & movers found for your criteria in <strong><?= htmlspecialchars($city) ?></strong>. Try adjusting your search.</p>
      <a href="movers.php" class="back-btn"><i class="fas fa-arrow-left"></i> Try Again</a>
    </div>
  <?php else: ?>
    <div class="results-header">
      <div>
        <h2>Found <?= count($companies) ?> company<?= count($companies)>1?'s':'' ?></h2>
        <p>Showing results for <?= htmlspecialchars($workLabels[$workType] ?? '') ?> in <?= htmlspecialchars($city) ?></p>
      </div>
    </div>
    <div class="providers-grid">
      <?php foreach ($companies as $c):
        $priceStmt = $pdo->prepare("SELECT min_price,max_price FROM $tServices WHERE company_id=? AND service_type=? AND property_type=?");
        $priceStmt->execute([$c['id'],$workType,$propType]);
        $price = $priceStmt->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="p-card reveal">
        <?php if ($c['logo_path']): ?>
          <img src="<?= BASE ?>/move/<?= htmlspecialchars($c['logo_path']) ?>" class="p-logo" alt="Logo" loading="lazy">
        <?php else: ?>
          <div class="p-logo-ph"><i class="fas fa-truck-moving" style="color:rgba(249,115,22,0.60);font-size:3rem;"></i></div>
        <?php endif; ?>
        <div class="p-body">
          <div class="p-name"><?= htmlspecialchars($c['name']) ?></div>
          <div class="p-rating">
            <div class="stars"><?php for($i=1;$i<=5;$i++) echo $i<=round($c['rating'])? '★':'☆'; ?></div>
            <span><?= number_format($c['rating'],1) ?> (<?= $c['reviews'] ?> reviews)</span>
          </div>
          <div class="p-desc"><?= htmlspecialchars($c['description']) ?></div>
          <?php if ($price): ?>
          <div class="p-price-highlight">
            <span><?= htmlspecialchars($workLabels[$workType] ?? '') ?> — <?= htmlspecialchars($propLabels[$propType] ?? '') ?></span>
            <strong>₹<?= number_format($price['min_price']) ?> – ₹<?= number_format($price['max_price']) ?></strong>
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
