<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/login.php?redirect='.urlencode(BASE.'/move/add_company.php')); exit; }
$navActive = '';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register Movers Company — Drifter</title>
<style>
.page-hero{
  padding:60px 24px 40px;text-align:center;color:white;
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
.page-hero h1{font-size:2rem;font-weight:800;margin-bottom:8px;position:relative;z-index:1;}
.page-hero p{color:rgba(255,255,255,0.80);position:relative;z-index:1;}
.wrap{max-width:860px;margin:40px auto;padding:0 24px 80px;}
.card{background:white;border-radius:16px;padding:40px;box-shadow:var(--shadow);}
.card h2{font-size:1.2rem;font-weight:700;margin-bottom:24px;padding-bottom:14px;border-bottom:2px solid #f1f5f9;}
.section-label{font-size:0.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin:24px 0 16px;display:flex;align-items:center;gap:10px;}
.section-label::before,.section-label::after{content:'';flex:1;height:1px;background:#f1f5f9;}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
.form-group{display:flex;flex-direction:column;gap:6px;}
.form-group.full{grid-column:span 2;}
.form-group label{font-size:0.82rem;font-weight:600;color:#475569;}
.form-group input,.form-group select,.form-group textarea{padding:12px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:0.95rem;transition:all 0.25s;outline:none;font-family:inherit;background:#fafafa;}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:#BC9F8B;background:white;box-shadow:0 0 0 3px rgba(188,159,139,0.18);}
.form-group textarea{min-height:90px;resize:vertical;}
.checkbox-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;}
.check-item{display:flex;align-items:center;gap:8px;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;cursor:pointer;transition:all 0.2s;font-size:0.85rem;}
.check-item:hover{border-color:#BC9F8B;background:rgba(188,159,139,0.08);}
.check-item input[type=checkbox]{accent-color:#BC9F8B;width:16px;height:16px;}
.file-drop{border:2px dashed #e2e8f0;border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:all 0.25s;color:var(--muted);}
.file-drop:hover,.file-drop.selected{border-color:#BC9F8B;color:#a08070;background:rgba(188,159,139,0.08);}
.file-drop i{font-size:1.6rem;margin-bottom:6px;display:block;}
input[type=file]{display:none;}
.preview-img{width:100%;max-height:100px;object-fit:contain;border-radius:8px;margin-top:8px;display:none;}
.submit-btn{width:100%;padding:15px;margin-top:24px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border:none;border-radius:10px;font-size:1rem;font-weight:700;cursor:pointer;transition:all 0.25s;}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(188,159,139,0.40);}
.submit-btn:disabled{opacity:0.6;cursor:not-allowed;transform:none;}
@media(max-width:600px){.form-grid{grid-template-columns:1fr;}.form-group.full{grid-column:span 1;}}
</style>
</head>
<body>
<div class="page-hero">
  <h1>🚛 Register Packers & Movers Company</h1>
  <p>List your company and start receiving relocation requests</p>
</div>
<div class="wrap">
  <div class="card">
    <h2><i class="fas fa-warehouse" style="color:#f59e0b"></i> Company Registration</h2>
    <form action="process_company.php" method="POST" enctype="multipart/form-data" id="regForm">
      <div class="section-label">Company Info</div>
      <div class="form-grid">
        <div class="form-group full">
          <label>Company Name *</label>
          <input type="text" name="company_name" placeholder="e.g. SafeMove Packers" required>
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" name="company_email" placeholder="company@email.com" required>
        </div>
        <div class="form-group">
          <label>Phone *</label>
          <input type="tel" name="company_phone" placeholder="10-digit number" required>
        </div>
        <div class="form-group full">
          <label>Address *</label>
          <textarea name="company_address" placeholder="Full company address" required></textarea>
        </div>
        <div class="form-group full">
          <label>Description *</label>
          <textarea name="company_description" placeholder="Brief description of your services..." required></textarea>
        </div>
        <div class="form-group full">
          <label>Service Locations (comma separated) *</label>
          <input type="text" name="service_locations" placeholder="e.g. Mumbai, Pune, Nashik" required>
        </div>
      </div>

      <div class="section-label">Services Offered</div>
      <div class="checkbox-grid">
        <label class="check-item"><input type="checkbox" name="services_offered[]" value="packing"> 📦 Packing Only</label>
        <label class="check-item"><input type="checkbox" name="services_offered[]" value="moving"> 🚛 Moving Only</label>
        <label class="check-item"><input type="checkbox" name="services_offered[]" value="packing_moving"> 📦🚛 Pack + Move</label>
        <label class="check-item"><input type="checkbox" name="services_offered[]" value="full_service"> ⭐ Full Service</label>
        <label class="check-item"><input type="checkbox" name="services_offered[]" value="vehicle"> 🚗 Vehicle Transport</label>
        <label class="check-item"><input type="checkbox" name="services_offered[]" value="international"> 🌍 International</label>
      </div>

      <div class="section-label">Pricing</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Service Type *</label>
          <select name="service_type" required>
            <option value="">Select type</option>
            <option value="packing">Packing Only</option>
            <option value="moving">Moving Only</option>
            <option value="packing_moving">Packing + Moving</option>
            <option value="full_service">Full Service</option>
            <option value="vehicle">Vehicle Transportation</option>
            <option value="international">International Relocation</option>
          </select>
        </div>
        <div class="form-group">
          <label>Property Type *</label>
          <select name="property_type" required>
            <option value="">Select property</option>
            <option value="1bhk">1 BHK</option>
            <option value="2bhk">2 BHK</option>
            <option value="3bhk">3 BHK</option>
            <option value="villa">Villa / Bungalow</option>
            <option value="office">Office / Commercial</option>
          </select>
        </div>
        <div class="form-group">
          <label>Min Price (₹) *</label>
          <input type="number" name="min_price" placeholder="e.g. 3000" min="1" required>
        </div>
        <div class="form-group">
          <label>Max Price (₹) *</label>
          <input type="number" name="max_price" placeholder="e.g. 15000" min="1" required>
        </div>
      </div>

      <div class="section-label">Company Logo</div>
      <label class="file-drop" id="logoDrop" for="company_logo">
        <i class="fas fa-image"></i>
        <span id="logoText">Click to upload logo (optional)</span>
      </label>
      <input type="file" id="company_logo" name="company_logo" accept="image/*">
      <img id="logoPreview" class="preview-img" alt="Logo preview">

      <button type="submit" class="submit-btn" id="submitBtn">
        <i class="fas fa-paper-plane"></i> Register Company
      </button>
    </form>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
<script>
document.getElementById('company_logo').addEventListener('change', function() {
  if (this.files[0]) {
    document.getElementById('logoText').textContent = this.files[0].name;
    document.getElementById('logoDrop').classList.add('selected');
    const r = new FileReader();
    r.onload = e => { const p = document.getElementById('logoPreview'); p.src = e.target.result; p.style.display = 'block'; };
    r.readAsDataURL(this.files[0]);
  }
});
document.getElementById('regForm').addEventListener('submit', function() {
  const btn = document.getElementById('submitBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
});
</script>
</body>
</html>
