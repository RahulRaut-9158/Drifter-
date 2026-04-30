<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: '.BASE.'/front/login.php?redirect='.urlencode(BASE.'/transport/add_vehicle.php'));
    exit;
}
// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/db.php';
    $conn = db();

    if (!is_dir("uploads")) mkdir("uploads", 0777, true);

    $allowed_ext = ['jpg','jpeg','png','gif','webp'];
    $err = '';

    $name     = $_SESSION['username']; // always use session username — never trust form input
    $mobile   = trim($_POST['mobile'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $capacity = floatval($_POST['capacity'] ?? 0);
    $rate     = floatval($_POST['rate_per_km'] ?? 0);

    if (!$mobile || !$email || !$address || !$capacity || !$rate) {
        $err = 'All fields are required.';
    } elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $err = 'Enter a valid 10-digit mobile number.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Enter a valid email address.';
    } elseif (!isset($_FILES['license_image']) || $_FILES['license_image']['error'] !== UPLOAD_ERR_OK) {
        $err = 'License image is required.';
    } elseif (!isset($_FILES['vehicle_image']) || $_FILES['vehicle_image']['error'] !== UPLOAD_ERR_OK) {
        $err = 'Vehicle image is required.';
    } else {
        $licExt = strtolower(pathinfo($_FILES['license_image']['name'], PATHINFO_EXTENSION));
        $vehExt = strtolower(pathinfo($_FILES['vehicle_image']['name'], PATHINFO_EXTENSION));
        if (!in_array($licExt, $allowed_ext) || !in_array($vehExt, $allowed_ext)) {
            $err = 'Only image files (jpg, jpeg, png, gif, webp) are allowed.';
        }
    }

    if (!$err) {
        $licensePath = "uploads/" . uniqid() . "_" . basename($_FILES['license_image']['name']);
        $vehiclePath  = "uploads/" . uniqid() . "_" . basename($_FILES['vehicle_image']['name']);

        if (move_uploaded_file($_FILES['license_image']['tmp_name'], $licensePath) &&
            move_uploaded_file($_FILES['vehicle_image']['tmp_name'], $vehiclePath)) {

            $stmt = $conn->prepare("INSERT INTO vehicles (owner_name,mobile,email,address,license_image,vehicle_image,capacity,rate_per_km,vehicle_category) VALUES (?,?,?,?,?,?,?,?,'transport')");
            $stmt->bind_param("ssssssdd", $name, $mobile, $email, $address, $licensePath, $vehiclePath, $capacity, $rate);

            if ($stmt->execute()) {
                header('Location: '.BASE.'/front/your_vehicle_info.php?registered=1'); exit;
            } else {
                $err = 'Database error. Please try again.';
            }
        } else {
            $err = 'File upload failed. Please try again.';
        }
    }
    $conn->close();
}
// Show form
$navActive = '';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Transport Vehicle — Drifter</title>
<style>
.page-hero{
  padding:60px 24px 40px;text-align:center;color:white;
  position:relative;overflow:hidden;
}
.page-hero::before{
  content:'';position:absolute;inset:0;
  background:url('https://images.unsplash.com/photo-1519003722824-194d4455a60c?w=1920&q=80') center/cover no-repeat;
  filter:brightness(0.20) saturate(1.2);
}
.page-hero::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(42,37,32,0.92) 0%,rgba(188,159,139,0.38) 100%);
}
.page-hero h1{font-size:2rem;font-weight:800;margin-bottom:8px;position:relative;z-index:1;}
.page-hero p{color:rgba(255,255,255,0.75);position:relative;z-index:1;}
.container{display:flex;flex-wrap:wrap;gap:2rem;padding:40px 24px 80px;max-width:1100px;margin:0 auto;}
.form-card{flex:1.5;min-width:340px;background:white;border-radius:16px;padding:36px;box-shadow:var(--shadow);position:relative;overflow:hidden;}
.form-card::before{content:'';position:absolute;top:0;left:0;width:5px;height:100%;background:linear-gradient(to bottom,#BC9F8B,#CADABF);}
.form-card h2{font-size:1.3rem;font-weight:700;color:var(--text);margin-bottom:20px;padding-bottom:14px;border-bottom:2px solid #f1f5f9;}
.user-bar{background:rgba(202,218,191,0.20);border:1px solid rgba(181,207,183,0.45);border-radius:8px;padding:10px 14px;margin-bottom:20px;font-size:0.85rem;color:#3a6b3c;display:flex;align-items:center;gap:8px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.form-group{margin-bottom:16px;}
.form-group label{display:block;margin-bottom:6px;font-weight:600;color:#475569;font-size:0.82rem;}
.form-group input,.form-group textarea{width:100%;padding:12px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:0.95rem;transition:all 0.25s;font-family:inherit;outline:none;background:#fafafa;}
.form-group input:focus,.form-group textarea:focus{border-color:#BC9F8B;background:white;box-shadow:0 0 0 3px rgba(188,159,139,0.18);}
.form-group textarea{min-height:85px;resize:vertical;}
.form-group.error input,.form-group.error textarea{border-color:#ef4444;}
.err-msg{color:#ef4444;font-size:0.77rem;margin-top:3px;display:none;}
.form-group.error .err-msg{display:block;}
.file-group{margin-bottom:16px;}
.file-group .flabel{display:block;margin-bottom:6px;font-weight:600;color:#475569;font-size:0.82rem;}
.file-drop{border:2px dashed #e2e8f0;border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:all 0.25s;color:var(--muted);}
.file-drop:hover,.file-drop.selected{border-color:#BC9F8B;color:#a08070;background:rgba(188,159,139,0.08);}
.file-drop i{font-size:1.6rem;margin-bottom:6px;display:block;}
input[type="file"]{display:none;}
.preview-img{width:100%;max-height:110px;object-fit:cover;border-radius:8px;margin-top:8px;display:none;border:1px solid #e2e8f0;}
.submit-btn{width:100%;padding:14px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border:none;border-radius:10px;font-size:1rem;font-weight:700;cursor:pointer;transition:all 0.25s;margin-top:8px;}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(188,159,139,0.40);}
.submit-btn:disabled{opacity:0.7;cursor:not-allowed;transform:none;}
.benefits-card{flex:1;min-width:250px;background:white;border-radius:16px;padding:28px;box-shadow:var(--shadow);align-self:flex-start;}
.benefits-card h3{font-size:1.1rem;font-weight:700;color:var(--text);margin-bottom:20px;text-align:center;}
.benefit{display:flex;align-items:flex-start;gap:12px;margin-bottom:18px;}
.b-icon{width:38px;height:38px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.9rem;}
.benefit h4{font-size:0.88rem;font-weight:700;color:var(--text);margin-bottom:3px;}
.benefit p{font-size:0.8rem;color:var(--muted);}
@media(max-width:768px){.container{flex-direction:column;padding:24px 16px;}.form-row{grid-template-columns:1fr;}}
</style>
</head>
<body>
<div class="page-hero">
  <h1><i class="fas fa-truck"></i> Add Transport Vehicle</h1>
  <p>Register your vehicle and start receiving transport bookings today</p>
</div>
<div class="container">
  <div class="form-card">
    <h2>Vehicle Registration</h2>
    <?php if (!empty($err)): ?>
    <div style="background:#FFD8DF;border:1px solid #FFAAB8;border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:0.88rem;color:#7a1a2a;display:flex;align-items:center;gap:8px;">
      <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($err) ?>
    </div>
    <?php endif; ?>
    <div class="user-bar"><i class="fas fa-user-check"></i> Registering as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></div>
    <form id="regForm" action="add_vehicle.php" method="POST" enctype="multipart/form-data">
      <div class="form-row">
        <div class="form-group">
          <label>Owner Name *</label>
          <input type="text" name="owner_name" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
        </div>
        <div class="form-group">
          <label>Mobile Number *</label>
          <input type="text" name="mobile" id="mobile" placeholder="10-digit number" required>
          <div class="err-msg">Enter a valid 10-digit number</div>
        </div>
      </div>
      <div class="form-group">
        <label>Email Address *</label>
        <input type="email" name="email" id="email" placeholder="your@email.com" required>
        <div class="err-msg">Enter a valid email</div>
      </div>
      <div class="form-group">
        <label>Base Location / City *</label>
        <textarea name="address" placeholder="e.g. Satara, Maharashtra" required></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Capacity (Tons) *</label>
          <input type="number" name="capacity" placeholder="e.g. 2.5" min="0.1" step="0.1" required>
        </div>
        <div class="form-group">
          <label>Rate per KM (₹) *</label>
          <input type="number" name="rate_per_km" placeholder="e.g. 15" min="1" step="0.5" required>
        </div>
      </div>
      <div class="file-group">
        <span class="flabel">License Image *</span>
        <label class="file-drop" id="licDrop" for="license_image">
          <i class="fas fa-id-card"></i><span id="licText">Click to upload license</span>
        </label>
        <input type="file" id="license_image" name="license_image" accept="image/*" required>
        <img id="licPreview" class="preview-img" alt="License preview">
      </div>
      <div class="file-group">
        <span class="flabel">Vehicle Image *</span>
        <label class="file-drop" id="vehDrop" for="vehicle_image">
          <i class="fas fa-truck"></i><span id="vehText">Click to upload vehicle photo</span>
        </label>
        <input type="file" id="vehicle_image" name="vehicle_image" accept="image/*" required>
        <img id="vehPreview" class="preview-img" alt="Vehicle preview">
      </div>
      <button type="submit" class="submit-btn" id="submitBtn"><i class="fas fa-paper-plane"></i> Register Vehicle</button>
    </form>
  </div>
  <div class="benefits-card">
    <h3>Why Register?</h3>
    <div class="benefit"><div class="b-icon"><i class="fas fa-bolt"></i></div><div><h4>Instant Bookings</h4><p>Start receiving bookings immediately after registration.</p></div></div>
    <div class="benefit"><div class="b-icon"><i class="fas fa-shield-alt"></i></div><div><h4>Verified Customers</h4><p>All customers are registered — safe and trustworthy.</p></div></div>
    <div class="benefit"><div class="b-icon"><i class="fas fa-rupee-sign"></i></div><div><h4>Set Your Rate</h4><p>You control your per-km rate. Earn what you deserve.</p></div></div>
    <div class="benefit"><div class="b-icon"><i class="fas fa-chart-line"></i></div><div><h4>Track Bookings</h4><p>View all bookings from your vehicle dashboard.</p></div></div>
    <div class="benefit"><div class="b-icon"><i class="fas fa-toggle-on"></i></div><div><h4>Availability Control</h4><p>Toggle your vehicle on/off from your dashboard anytime.</p></div></div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
<script>
function setupFile(inputId, dropId, textId, previewId) {
  document.getElementById(inputId).addEventListener('change', function() {
    if (this.files[0]) {
      document.getElementById(textId).textContent = this.files[0].name;
      document.getElementById(dropId).classList.add('selected');
      const r = new FileReader();
      r.onload = e => { const p = document.getElementById(previewId); p.src = e.target.result; p.style.display = 'block'; };
      r.readAsDataURL(this.files[0]);
    }
  });
}
setupFile('license_image','licDrop','licText','licPreview');
setupFile('vehicle_image','vehDrop','vehText','vehPreview');
document.getElementById('regForm').addEventListener('submit', function(e) {
  let ok = true;
  const mob = document.getElementById('mobile');
  const em = document.getElementById('email');
  if (!/^[0-9]{10}$/.test(mob.value.trim())) { mob.closest('.form-group').classList.add('error'); ok = false; }
  else mob.closest('.form-group').classList.remove('error');
  if (!em.validity.valid) { em.closest('.form-group').classList.add('error'); ok = false; }
  else em.closest('.form-group').classList.remove('error');
  if (!ok) { e.preventDefault(); return; }
  const btn = document.getElementById('submitBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
});
</script>
</body>
</html>
