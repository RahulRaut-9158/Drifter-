<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: '.BASE.'/login.php?redirect='.urlencode(BASE.'/move/movers.php')); exit;
}
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
  padding:72px 24px 56px;text-align:center;color:white;
  position:relative;overflow:hidden;
  background:linear-gradient(135deg,#0a1628,#0f2b5e);
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
.form-wrap { max-width:860px;margin:36px auto;padding:0 24px 80px; }
</style>
</head>
<body>

<div class="page-hero">
  <div class="tag"><i class="fas fa-people-carry"></i> Packers & Movers</div>
  <h1>Stress-Free Relocation</h1>
  <p>We handle everything from packing to transportation — safely and on time</p>
</div>

<div class="form-wrap">
  <div class="form-card reveal">
    <h2><i class="fas fa-people-carry"></i> Moving Details</h2>
    <form action="process_request.php" method="POST" id="moversForm">

      <div class="section-divider">Location & Date</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Current Address</label>
          <input type="text" name="current_address" placeholder="Your current full address" required>
        </div>
        <div class="form-group">
          <label>New Address</label>
          <input type="text" name="new_address" placeholder="Your new full address" required>
        </div>
        <div class="form-group">
          <label>Moving Date</label>
          <input type="date" name="moving_date" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="form-group">
          <label>Property Type</label>
          <select name="property_type" required>
            <option value="">Select property type</option>
            <option value="1bhk">1 BHK Apartment</option>
            <option value="2bhk">2 BHK Apartment</option>
            <option value="3bhk">3 BHK Apartment</option>
            <option value="villa">Villa / Bungalow</option>
            <option value="office">Office / Commercial</option>
          </select>
        </div>
      </div>

      <div class="section-divider">Service Required</div>
      <div class="form-group">
        <label>Type of Work</label>
        <select name="work_type" required>
          <option value="">Select work type</option>
          <option value="packing">Packing Only</option>
          <option value="moving">Moving Only</option>
          <option value="packing_moving">Packing + Moving</option>
          <option value="full_service">Full Service (Pack + Move + Unpack)</option>
          <option value="vehicle">Vehicle Transportation</option>
          <option value="international">International Relocation</option>
        </select>
      </div>

      <div class="section-divider">Special Items</div>
      <div class="checkbox-grid">
        <label class="check-item"><input type="checkbox" name="special_items[]" value="piano"> 🎹 Piano</label>
        <label class="check-item"><input type="checkbox" name="special_items[]" value="art"> 🖼️ Artwork</label>
        <label class="check-item"><input type="checkbox" name="special_items[]" value="antique"> 🏺 Antiques</label>
        <label class="check-item"><input type="checkbox" name="special_items[]" value="pet"> 🐾 Pets</label>
        <label class="check-item"><input type="checkbox" name="special_items[]" value="plants"> 🌿 Plants</label>
        <label class="check-item"><input type="checkbox" name="special_items[]" value="fragile"> 📦 Fragile Items</label>
      </div>

      <div class="form-group" style="margin-top:18px;">
        <label>Additional Instructions (optional)</label>
        <textarea name="additional_info" placeholder="Any special requirements or instructions..."></textarea>
      </div>

      <button type="submit" class="submit-btn" id="submitBtn">
        <i class="fas fa-search"></i> Find Packers & Movers
      </button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="<?= BASE ?>/assets/js/effects.js"></script>
<script>
document.getElementById('moversForm').addEventListener('submit', function() {
  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
});
</script>
</body>
</html>
