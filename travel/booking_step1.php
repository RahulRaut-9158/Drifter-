<?php
session_start();
if (empty($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/login.php?redirect='.urlencode(BASE.'/travel/booking_step1.php')); exit; }
$navActive=''; include '../includes/navbar.php';
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Book Travel — Drifter</title>
<style>
.page-hero{padding:60px 24px 44px;text-align:center;color:white;position:relative;overflow:hidden;background:var(--text);}
.page-hero::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(44,36,32,0.95) 0%,rgba(201,181,156,0.35) 100%);}
.page-hero h1{font-size:2rem;font-weight:800;margin-bottom:8px;position:relative;z-index:1;}
.page-hero p{color:rgba(255,255,255,0.60);font-size:0.93rem;position:relative;z-index:1;}
.steps-nav{display:flex;justify-content:center;align-items:center;gap:0;padding:24px;background:white;box-shadow:0 2px 12px rgba(44,36,32,0.06);}
.step-item{display:flex;flex-direction:column;align-items:center;gap:5px;position:relative;padding:0 28px;}
.step-item:not(:last-child)::after{content:'';position:absolute;right:-1px;top:17px;width:56px;height:2px;background:var(--linen,#E7E8D8);}
.step-circle{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.88rem;background:var(--linen,#E7E8D8);color:var(--muted,#7a7060);transition:all 0.3s;}
.step-item.active .step-circle{background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;box-shadow:0 4px 12px rgba(188,159,139,0.45);}
.step-item.done .step-circle{background:#B5CFB7;color:#2a2520;}
.step-label{font-size:0.72rem;font-weight:600;color:var(--muted,#7a7060);text-transform:uppercase;letter-spacing:0.5px;}
.step-item.active .step-label{color:#a08070;}
.form-wrap{max-width:700px;margin:36px auto;padding:0 24px 60px;}
.form-card{background:white;border-radius:16px;padding:38px;box-shadow:var(--shadow);}
.form-card h2{font-size:1.2rem;font-weight:700;margin-bottom:24px;color:var(--text,#2a2520);padding-bottom:14px;border-bottom:2px solid var(--linen,#E7E8D8);}
.user-info{background:var(--linen,#E7E8D8);border:1px solid var(--mint,#CADABF);border-radius:10px;padding:11px 15px;margin-bottom:22px;font-size:0.84rem;color:#a08070;display:flex;align-items:center;gap:8px;}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
.form-group{display:flex;flex-direction:column;gap:5px;}
.form-group.full{grid-column:span 2;}
.form-group label{font-size:0.78rem;font-weight:600;color:var(--muted);}
.form-group input{padding:11px 13px;border:1.5px solid var(--mint,#CADABF);border-radius:10px;font-size:0.93rem;transition:all 0.22s;outline:none;font-family:inherit;background:var(--bg,#F4F5EE);}
.form-group input:focus{border-color:#BC9F8B;background:white;box-shadow:0 0 0 3px rgba(188,159,139,0.18);}
.err-msg{font-size:0.73rem;color:#c0392b;display:none;}
.form-group.has-err .err-msg{display:block;}
.form-group.has-err input{border-color:#c0392b;}
.submit-btn{width:100%;padding:14px;margin-top:22px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border:none;border-radius:10px;font-size:0.97rem;font-weight:700;cursor:pointer;transition:all 0.25s;font-family:inherit;box-shadow:0 4px 18px rgba(188,159,139,0.40);}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(188,159,139,0.55);}
.submit-btn:disabled{opacity:0.6;cursor:not-allowed;transform:none;}
@media(max-width:600px){.form-grid{grid-template-columns:1fr;}.form-group.full{grid-column:span 1;}.steps-nav{padding:18px 8px;}.step-item{padding:0 12px;}}
</style>
</head>
<body>
<div class="page-hero"><h1>🚌 Book Travel Vehicle</h1><p>Find comfortable travel vehicles near your pickup location</p></div>
<div class="steps-nav">
  <div class="step-item active"><div class="step-circle">1</div><div class="step-label">Trip Details</div></div>
  <div class="step-item"><div class="step-circle">2</div><div class="step-label">Select Vehicle</div></div>
  <div class="step-item"><div class="step-circle">3</div><div class="step-label">Confirmed</div></div>
</div>
<div class="form-wrap">
  <div class="form-card">
    <h2>Enter Your Travel Details</h2>
    <div class="user-info"><i class="fas fa-user-check"></i> Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></div>
    <form action="select_vehicle.php" method="POST" id="bookForm">
      <div class="form-grid">
        <div class="form-group"><label>Your Name</label><input type="text" name="user_name" value="<?= htmlspecialchars($_SESSION['username']) ?>" required></div>
        <div class="form-group"><label>Mobile Number</label><input type="text" name="user_mobile" id="mob" placeholder="10-digit number" required><span class="err-msg">Enter a valid 10-digit number</span></div>
        <div class="form-group full"><label>Pickup Location / City</label><input type="text" name="pickup_location" placeholder="e.g. Satara, Maharashtra" required></div>
        <div class="form-group full"><label>Drop Location / City</label><input type="text" name="drop_location" placeholder="e.g. Pune, Maharashtra" required></div>
        <div class="form-group"><label>Date</label><input type="date" name="date" min="<?= date('Y-m-d') ?>" required></div>
        <div class="form-group"><label>Time</label><input type="time" name="time" required></div>
        <div class="form-group full"><label>Distance (km)</label><input type="number" name="distance_km" placeholder="Approximate distance in km" min="1" required></div>
      </div>
      <button type="submit" class="submit-btn" id="sb"><i class="fas fa-search"></i> Find Available Vehicles</button>
    </form>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
<script>
const mob=document.getElementById('mob');
mob.addEventListener('input',()=>{const ok=/^[0-9]{10}$/.test(mob.value.trim());mob.closest('.form-group').classList.toggle('has-err',mob.value&&!ok);});
document.getElementById('bookForm').addEventListener('submit',function(e){
  if(!/^[0-9]{10}$/.test(mob.value.trim())){e.preventDefault();mob.closest('.form-group').classList.add('has-err');mob.focus();return;}
  const b=document.getElementById('sb');b.disabled=true;b.innerHTML='<i class="fas fa-spinner fa-spin"></i> Searching...';
});
</script>
</body></html>
