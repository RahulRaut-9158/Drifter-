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
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Packers & Movers — Drifter</title>
<style>
.page-hero{
  padding:50px 24px 30px;text-align:center;color:white;
  position:relative;overflow:hidden;
}
.page-hero::before{
  content:'';position:absolute;inset:0;
  background:url('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1920&q=80') center/cover no-repeat;
  filter:brightness(0.20) saturate(1.1);
}
.page-hero::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(42,37,32,0.92) 0%,rgba(188,159,139,0.38) 100%);
}
.page-hero h1{font-size:1.8rem;font-weight:800;margin-bottom:6px;position:relative;z-index:1;}
.page-hero p{color:rgba(255,255,255,0.80);position:relative;z-index:1;}
.trip-bar{background:white;padding:14px 24px;box-shadow:0 2px 12px rgba(0,0,0,0.06);display:flex;flex-wrap:wrap;gap:20px;justify-content:center;}
.trip-tag{display:flex;align-items:center;gap:6px;font-size:0.85rem;color:var(--muted);}
.trip-tag strong{color:var(--text);}
.results-wrap{max-width:1280px;margin:0 auto;padding:40px 24px 80px;}
.results-header{margin-bottom:28px;}
.results-header h2{font-size:1.3rem;font-weight:700;margin-bottom:4px;}
.results-header p{color:var(--muted);font-size:0.9rem;}
.providers-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:24px;}
.p-card{background:white;border-radius:16px;overflow:hidden;box-shadow:var(--shadow);transition:var(--trans);}
.p-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-lg);}
.p-logo-ph{width:100%;height:160px;background:linear-gradient(135deg,var(--linen,#E7E8D8),var(--mint,#CADABF));display:flex;align-items:center;justify-content:center;font-size:3.5rem;}
.p-logo{width:100%;height:160px;object-fit:cover;}
.p-body{padding:20px;}
.p-name{font-size:1.1rem;font-weight:700;margin-bottom:6px;}
.p-rating{display:flex;align-items:center;gap:6px;margin-bottom:10px;}
.stars{color:#f59e0b;font-size:0.85rem;}
.p-rating span{font-size:0.82rem;color:var(--muted);}
.p-desc{font-size:0.85rem;color:var(--muted);line-height:1.6;margin-bottom:14px;}
.p-price{background:rgba(202,218,191,0.20);border-radius:8px;padding:10px 14px;margin-bottom:14px;display:flex;justify-content:space-between;align-items:center;border:1px solid rgba(181,207,183,0.45);}
.p-price span{font-size:0.82rem;color:#3a6b3c;}
.p-price strong{font-size:1.05rem;color:#3a6b3c;font-weight:800;}
.p-services{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;}
.svc-tag{padding:3px 10px;background:rgba(202,218,191,0.25);color:#3a6b3c;border-radius:50px;font-size:0.75rem;font-weight:600;}
.p-locations{font-size:0.8rem;color:var(--muted);margin-bottom:16px;display:flex;align-items:center;gap:6px;}
.contact-btn{width:100%;padding:11px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border:none;border-radius:10px;font-size:0.9rem;font-weight:700;cursor:pointer;transition:all 0.25s;}
.contact-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(188,159,139,0.40);}
.no-results{text-align:center;padding:80px 24px;background:white;border-radius:16px;box-shadow:var(--shadow);}
.no-results .icon{font-size:4rem;margin-bottom:16px;}
.no-results h3{font-size:1.4rem;font-weight:700;margin-bottom:8px;}
.no-results p{color:var(--muted);margin-bottom:24px;}
.back-btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border-radius:10px;text-decoration:none;font-weight:600;}
</style>
</head>
<body>
<div class="page-hero">
  <h1>🏠 Available Packers & Movers</h1>
  <p><?= htmlspecialchars($workLabels[$workType] ?? $workType) ?> service for <?= htmlspecialchars($propLabels[$propType] ?? $propType) ?></p>
</div>
<div class="trip-bar">
  <div class="trip-tag"><i class="fas fa-map-marker-alt" style="color:#f59e0b"></i> <strong><?= htmlspecialchars($city) ?></strong></div>
  <div class="trip-tag"><i class="fas fa-arrow-right"></i></div>
  <div class="trip-tag"><i class="fas fa-map-marker" style="color:#10b981"></i> <strong><?= htmlspecialchars(explode(',',$new)[0]) ?></strong></div>
  <div class="trip-tag"><i class="fas fa-home"></i> <strong><?= htmlspecialchars($propLabels[$propType] ?? $propType) ?></strong></div>
</div>
<div class="results-wrap">
  <?php if (empty($companies)): ?>
    <div class="no-results">
      <div class="icon">🔍</div>
      <h3>No Companies Found</h3>
      <p>No packers & movers found for your criteria in <strong><?= htmlspecialchars($city) ?></strong>. Try adjusting your search.</p>
      <a href="movers.php" class="back-btn"><i class="fas fa-arrow-left"></i> Try Again</a>
    </div>
  <?php else: ?>
    <div class="results-header">
      <h2>Found <?= count($companies) ?> company<?= count($companies)>1?'s':'' ?></h2>
      <p>Showing results for <?= htmlspecialchars($workLabels[$workType] ?? '') ?> in <?= htmlspecialchars($city) ?></p>
    </div>
    <div class="providers-grid">
      <?php foreach ($companies as $c):
        $priceStmt = $pdo->prepare("SELECT min_price,max_price FROM $tServices WHERE company_id=? AND service_type=? AND property_type=?");
        $priceStmt->execute([$c['id'],$workType,$propType]);
        $price = $priceStmt->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="p-card">
        <?php if ($c['logo_path']): ?>
          <img src="<?= BASE ?>/move/<?= htmlspecialchars($c['logo_path']) ?>" class="p-logo" alt="Logo">
        <?php else: ?>
          <div class="p-logo-ph">🚛</div>
        <?php endif; ?>
        <div class="p-body">
          <div class="p-name"><?= htmlspecialchars($c['name']) ?></div>
          <div class="p-rating">
            <div class="stars"><?php for($i=1;$i<=5;$i++) echo $i<=round($c['rating'])? '★':'☆'; ?></div>
            <span><?= number_format($c['rating'],1) ?> (<?= $c['reviews'] ?> reviews)</span>
          </div>
          <div class="p-desc"><?= htmlspecialchars($c['description']) ?></div>
          <?php if ($price): ?>
          <div class="p-price">
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
          <button class="contact-btn" data-phone="<?= htmlspecialchars($c['phone']) ?>" data-email="<?= htmlspecialchars($c['email']) ?>" data-name="<?= htmlspecialchars($c['name']) ?>" onclick="showContact(this)">
            <i class="fas fa-phone"></i> Contact: <?= htmlspecialchars($c['phone']) ?>
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
<style>
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:99999;align-items:center;justify-content:center;}
.modal-overlay.show{display:flex;}
.modal-box{background:white;border-radius:16px;padding:32px;max-width:380px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.25);text-align:center;}
.modal-box h3{font-size:1.1rem;font-weight:700;margin-bottom:20px;color:var(--text);}
.modal-box .m-row{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f1f5f9;font-size:0.9rem;}
.modal-box .m-row:last-of-type{border-bottom:none;}
.modal-box .m-row i{color:#3a7d44;width:18px;}
.modal-box a{color:#3a7d44;text-decoration:none;font-weight:600;}
.modal-box a:hover{text-decoration:underline;}
.modal-close{margin-top:20px;padding:10px 28px;background:#f1f5f9;border:none;border-radius:8px;font-weight:600;cursor:pointer;transition:background 0.2s;}
.modal-close:hover{background:#e2e8f0;}
</style>
<div class="modal-overlay" id="contactModal" onclick="if(event.target===this)closeModal()">
  <div class="modal-box">
    <h3 id="modalName"></h3>
    <div class="m-row"><i class="fas fa-phone"></i><a id="modalPhone" href="#"></a></div>
    <div class="m-row"><i class="fas fa-envelope"></i><a id="modalEmail" href="#"></a></div>
    <button class="modal-close" onclick="closeModal()">Close</button>
  </div>
</div>
<script>
function showContact(btn) {
  document.getElementById('modalName').textContent  = btn.dataset.name;
  const phone = btn.dataset.phone;
  const email = btn.dataset.email;
  const ph = document.getElementById('modalPhone');
  ph.textContent = phone; ph.href = 'tel:' + phone;
  const em = document.getElementById('modalEmail');
  em.textContent = email; em.href = 'mailto:' + email;
  document.getElementById('contactModal').classList.add('show');
}
function closeModal() { document.getElementById('contactModal').classList.remove('show'); }
document.addEventListener('keydown', e => { if(e.key==='Escape') closeModal(); });
</script>
