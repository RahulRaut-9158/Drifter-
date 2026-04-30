<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/login.php?redirect='.urlencode(BASE.'/move/movers.php')); exit; }
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
  padding:70px 24px 50px;text-align:center;color:white;
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
.page-hero h1{font-size:2.2rem;font-weight:800;margin-bottom:8px;position:relative;z-index:1;}
.page-hero p{color:rgba(255,255,255,0.80);font-size:0.95rem;position:relative;z-index:1;}
.form-wrap{max-width:860px;margin:40px auto;padding:0 24px 80px;}
.form-card{background:white;border-radius:16px;padding:40px;box-shadow:var(--shadow);}
.form-card h2{font-size:1.2rem;font-weight:700;margin-bottom:24px;padding-bottom:14px;border-bottom:2px solid #f1f5f9;display:flex;align-items:center;gap:8px;}
.section-divider{font-size:0.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin:24px 0 16px;display:flex;align-items:center;gap:10px;}
.section-divider::before,.section-divider::after{content:'';flex:1;height:1px;background:#f1f5f9;}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
.form-group{display:flex;flex-direction:column;gap:6px;}
.form-group.full{grid-column:span 2;}
.form-group label{font-size:0.82rem;font-weight:600;color:#475569;}
.form-group input,.form-group select,.form-group textarea{padding:12px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:0.95rem;transition:all 0.25s;outline:none;font-family:inherit;background:#fafafa;}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:#BC9F8B;background:white;box-shadow:0 0 0 3px rgba(188,159,139,0.18);}
.form-group textarea{min-height:90px;resize:vertical;}
.checkbox-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;}
.check-item{display:flex;align-items:center;gap:8px;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;cursor:pointer;transition:all 0.2s;font-size:0.85rem;}
.check-item:hover{border-color:#BC9F8B;background:rgba(188,159,139,0.08);}
.check-item input[type=checkbox]{accent-color:#BC9F8B;width:16px;height:16px;}
.submit-btn{width:100%;padding:15px;margin-top:24px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border:none;border-radius:10px;font-size:1rem;font-weight:700;cursor:pointer;transition:all 0.25s;}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(188,159,139,0.40);}
.submit-btn:disabled{opacity:0.6;cursor:not-allowed;transform:none;}
@media(max-width:600px){.form-grid{grid-template-columns:1fr;}.form-group.full{grid-column:span 1;}}
</style>
</head>
<body>
<div class="page-hero">
  <h1>🏠 Packers & Movers</h1>
  <p>Stress-free relocation — we handle everything from packing to transportation</p>
</div>
<div class="form-wrap">
  <div class="form-card">
    <h2><i class="fas fa-people-carry" style="color:#f59e0b"></i> Moving Details</h2>
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
      <div class="form-grid">
        <div class="form-group full">
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
      <div class="form-grid" style="margin-top:18px;">
        <div class="form-group full">
          <label>Additional Instructions (optional)</label>
          <textarea name="additional_info" placeholder="Any special requirements or instructions..."></textarea>
        </div>
      </div>
      <button type="submit" class="submit-btn" id="submitBtn">
        <i class="fas fa-search"></i> Find Packers & Movers
      </button>
    </form>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
<script>
document.getElementById('moversForm').addEventListener('submit', function() {
  const btn = document.getElementById('submitBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
});
</script>
</body></html>
