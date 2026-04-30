<?php
session_start();
require_once '../includes/db.php';
if (empty($_SESSION['loggedin']) || $_SESSION['role'] !== 'owner') {
    header('Location: '.BASE.'/front/login.php'); exit;
}
$conn = db();
$username = $_SESSION['username'];

// Vehicles
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE owner_name=? AND vehicle_category='transport' ORDER BY created_at DESC");
$stmt->bind_param("s",$username); $stmt->execute();
$vehicles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$vids = array_column($vehicles,'id');

// Stats
$totalVehicles = count($vehicles);
$availCount = count(array_filter($vehicles, fn($v)=>$v['is_available']));

$pendingCount = $confirmedCount = $cancelledCount = $totalEarnings = 0;
if ($vids) {
    $in = implode(',', array_fill(0, count($vids), '?'));
    $types = str_repeat('i', count($vids));
    $s = $conn->prepare("SELECT status, SUM(total_cost) as earn, COUNT(*) as cnt FROM booking WHERE vehicle_id IN ($in) GROUP BY status");
    $s->bind_param($types, ...$vids); $s->execute();
    foreach ($s->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
        if ($row['status']==='Pending')   $pendingCount   = $row['cnt'];
        if ($row['status']==='Confirmed') { $confirmedCount = $row['cnt']; $totalEarnings = $row['earn']; }
        if ($row['status']==='Cancelled') $cancelledCount = $row['cnt'];
    }
}

$navActive = '';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Transport Dashboard — Drifter</title>
<style>
.dash-hero {
  background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 100%);
  padding:48px 24px 36px; color:white;
}
.dash-hero-inner { max-width:1280px; margin:0 auto; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; }
.dash-hero h1 { font-size:1.7rem; font-weight:800; margin-bottom:4px; }
.dash-hero p  { color:rgba(255,255,255,0.60); font-size:0.9rem; }
.hero-actions { display:flex; gap:10px; flex-wrap:wrap; }
.btn-add {
  display:inline-flex; align-items:center; gap:8px;
  padding:11px 22px; background:linear-gradient(135deg,#2563EB,#1d4ed8);
  color:white; border-radius:10px; font-weight:700; font-size:0.88rem;
  text-decoration:none; transition:transform 0.2s, box-shadow 0.2s;
  box-shadow:0 3px 12px rgba(37,99,235,0.40);
}
.btn-add:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(37,99,235,0.55); }
.btn-add-outline {
  display:inline-flex; align-items:center; gap:8px;
  padding:10px 20px; background:rgba(255,255,255,0.10);
  color:white; border-radius:10px; font-weight:600; font-size:0.88rem;
  text-decoration:none; border:1px solid rgba(255,255,255,0.25);
  transition:background 0.2s;
}
.btn-add-outline:hover { background:rgba(255,255,255,0.18); }

/* Stats */
.stats-row { max-width:1280px; margin:-28px auto 0; padding:0 24px; display:grid; grid-template-columns:repeat(4,1fr); gap:16px; position:relative; z-index:10; }
.stat-card {
  background:white; border-radius:14px; padding:20px 22px;
  box-shadow:0 4px 20px rgba(15,23,42,0.10); display:flex; align-items:center; gap:16px;
  border-left:4px solid var(--c,#2563EB);
}
.stat-icon { width:46px; height:46px; border-radius:12px; background:var(--bg,#eff6ff); display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0; }
.stat-val  { font-size:1.6rem; font-weight:800; color:#0f172a; line-height:1; }
.stat-lbl  { font-size:0.74rem; color:#64748b; margin-top:3px; }
@media(max-width:768px){ .stats-row{grid-template-columns:repeat(2,1fr);} }
@media(max-width:480px){ .stats-row{grid-template-columns:1fr 1fr;} }

/* Main layout */
.dash-body { max-width:1280px; margin:0 auto; padding:36px 24px 80px; }
.flash-ok { background:#ecfdf5; border:1px solid #6ee7b7; border-radius:12px; padding:13px 18px; margin-bottom:24px; font-weight:600; color:#065f46; display:flex; align-items:center; gap:10px; }

/* Tab nav */
.tab-nav { display:flex; gap:4px; background:#f1f5f9; border-radius:12px; padding:4px; margin-bottom:28px; width:fit-content; }
.tab-btn { padding:9px 20px; border-radius:9px; border:none; background:transparent; font-weight:600; font-size:0.86rem; color:#64748b; cursor:pointer; transition:all 0.2s; font-family:inherit; }
.tab-btn.active { background:white; color:#2563EB; box-shadow:0 2px 8px rgba(15,23,42,0.10); }

/* Vehicle cards */
.vehicles-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:22px; }
.v-card {
  background:white; border-radius:18px; overflow:hidden;
  box-shadow:0 2px 16px rgba(15,23,42,0.08);
  transition:transform 0.28s, box-shadow 0.28s;
  border:1px solid #e2e8f0;
}
.v-card:hover { transform:translateY(-5px); box-shadow:0 12px 36px rgba(15,23,42,0.13); }
.v-img { width:100%; height:185px; object-fit:cover; }
.v-img-ph { width:100%; height:185px; background:linear-gradient(135deg,#eff6ff,#dbeafe); display:flex; align-items:center; justify-content:center; font-size:3.5rem; }
.v-body { padding:18px 20px; }
.v-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; }
.v-name { font-size:1rem; font-weight:700; color:#0f172a; }
.avail-badge { padding:4px 12px; border-radius:50px; font-size:0.71rem; font-weight:700; }
.avail-badge.on  { background:#dcfce7; color:#166534; }
.avail-badge.off { background:#fee2e2; color:#991b1b; }
.v-meta { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px; }
.v-meta-item { font-size:0.78rem; color:#64748b; }
.v-meta-item strong { display:block; color:#0f172a; font-size:0.88rem; margin-bottom:1px; }
.toggle-btn {
  width:100%; padding:10px; border:none; border-radius:9px;
  font-weight:700; font-size:0.84rem; cursor:pointer; transition:all 0.22s;
  margin-bottom:12px; font-family:inherit; display:flex; align-items:center; justify-content:center; gap:7px;
}
.toggle-btn.on  { background:#fee2e2; color:#991b1b; }
.toggle-btn.on:hover  { background:#fecaca; }
.toggle-btn.off { background:#dcfce7; color:#166534; }
.toggle-btn.off:hover { background:#bbf7d0; }

/* Bookings inside card */
.bookings-section { border-top:1px solid #f1f5f9; padding-top:14px; }
.bookings-title { font-size:0.82rem; font-weight:700; color:#0f172a; margin-bottom:10px; display:flex; align-items:center; gap:6px; }
.booking-row {
  display:grid; grid-template-columns:1fr auto auto auto;
  gap:8px; padding:10px 0; border-bottom:1px solid #f8fafc;
  font-size:0.78rem; align-items:center;
}
.booking-row:last-child { border-bottom:none; }
.bk-user { font-weight:600; color:#0f172a; }
.bk-route { color:#64748b; font-size:0.73rem; margin-top:1px; }
.bk-info  { text-align:right; color:#64748b; }
.bk-info strong { display:block; color:#0f172a; }
.status-pill { padding:3px 10px; border-radius:50px; font-size:0.69rem; font-weight:700; white-space:nowrap; }
.status-pill.pending   { background:#fef3c7; color:#92400e; }
.status-pill.confirmed { background:#dcfce7; color:#166534; }
.status-pill.cancelled { background:#fee2e2; color:#991b1b; }
.action-btns { display:flex; gap:5px; }
.act-btn { padding:5px 10px; border:none; border-radius:7px; font-size:0.70rem; font-weight:700; cursor:pointer; transition:all 0.2s; font-family:inherit; }
.act-btn.confirm { background:#dcfce7; color:#166534; }
.act-btn.confirm:hover { background:#bbf7d0; }
.act-btn.cancel  { background:#fee2e2; color:#991b1b; }
.act-btn.cancel:hover  { background:#fecaca; }
.act-btn:disabled { opacity:0.5; cursor:not-allowed; }
.no-bookings { text-align:center; padding:20px; color:#94a3b8; font-size:0.83rem; }

/* All bookings table */
.bookings-table-wrap { background:white; border-radius:16px; box-shadow:0 2px 16px rgba(15,23,42,0.08); overflow:hidden; border:1px solid #e2e8f0; }
.table-header { padding:20px 24px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; }
.table-header h3 { font-size:1rem; font-weight:700; }
table { width:100%; border-collapse:collapse; }
th { background:#f8fafc; padding:12px 16px; text-align:left; font-size:0.75rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; }
td { padding:13px 16px; font-size:0.84rem; border-bottom:1px solid #f8fafc; color:#334155; }
tr:last-child td { border-bottom:none; }
tr:hover td { background:#fafbff; }
.empty-state { text-align:center; padding:72px 24px; background:white; border-radius:18px; box-shadow:0 2px 16px rgba(15,23,42,0.08); border:1px solid #e2e8f0; }
.empty-state .icon { font-size:3.5rem; margin-bottom:16px; }
.empty-state h3 { font-size:1.3rem; font-weight:700; margin-bottom:8px; }
.empty-state p { color:#64748b; margin-bottom:24px; }
</style>
</head>
<body>

<div class="dash-hero">
  <div class="dash-hero-inner">
    <div>
      <h1>🚚 Transport Dashboard</h1>
      <p>Welcome back, <?= htmlspecialchars($username) ?> — manage your transport vehicles &amp; bookings</p>
    </div>
    <div class="hero-actions">
      <a href="<?= BASE ?>/front/your_vehicle_travel.php" class="btn-add-outline"><i class="fas fa-bus"></i> Travel Vehicles</a>
      <a href="<?= BASE ?>/transport/add_vehicle.php" class="btn-add"><i class="fas fa-plus"></i> Add Vehicle</a>
    </div>
  </div>
</div>

<!-- STATS -->
<div class="stats-row">
  <div class="stat-card" style="--c:#2563EB;--bg:#eff6ff">
    <div class="stat-icon">🚚</div>
    <div><div class="stat-val"><?= $totalVehicles ?></div><div class="stat-lbl">Total Vehicles</div></div>
  </div>
  <div class="stat-card" style="--c:#10b981;--bg:#ecfdf5">
    <div class="stat-icon">✅</div>
    <div><div class="stat-val"><?= $availCount ?></div><div class="stat-lbl">Available Now</div></div>
  </div>
  <div class="stat-card" style="--c:#f59e0b;--bg:#fffbeb">
    <div class="stat-icon">⏳</div>
    <div><div class="stat-val"><?= $pendingCount ?></div><div class="stat-lbl">Pending Bookings</div></div>
  </div>
  <div class="stat-card" style="--c:#06b6d4;--bg:#ecfeff">
    <div class="stat-icon">💰</div>
    <div><div class="stat-val">₹<?= number_format($totalEarnings) ?></div><div class="stat-lbl">Total Earnings</div></div>
  </div>
</div>

<div class="dash-body">
  <?php if (isset($_GET['registered'])): ?>
  <div class="flash-ok"><i class="fas fa-check-circle"></i> Vehicle registered successfully! You can now receive bookings.</div>
  <?php endif; ?>

  <!-- TABS -->
  <div class="tab-nav">
    <button class="tab-btn active" onclick="showTab('vehicles',this)"><i class="fas fa-truck"></i> My Vehicles</button>
    <button class="tab-btn" onclick="showTab('bookings',this)"><i class="fas fa-calendar-alt"></i> All Bookings <?php if($pendingCount): ?><span style="background:#ef4444;color:white;border-radius:50px;padding:1px 7px;font-size:0.68rem;margin-left:4px;"><?= $pendingCount ?></span><?php endif; ?></button>
  </div>

  <!-- VEHICLES TAB -->
  <div id="tab-vehicles">
    <?php if (empty($vehicles)): ?>
      <div class="empty-state">
        <div class="icon">🚚</div>
        <h3>No Vehicles Yet</h3>
        <p>Register your first transport vehicle to start receiving bookings.</p>
        <a href="<?= BASE ?>/transport/add_vehicle.php" class="btn-add"><i class="fas fa-plus"></i> Add Your First Vehicle</a>
      </div>
    <?php else: ?>
      <div class="vehicles-grid">
        <?php foreach ($vehicles as $v):
          $vid = (int)$v['id'];
          $bs = $conn->prepare("SELECT * FROM booking WHERE vehicle_id=? ORDER BY id DESC LIMIT 5");
          $bs->bind_param("i",$vid); $bs->execute();
          $bookings = $bs->get_result()->fetch_all(MYSQLI_ASSOC);
        ?>
        <div class="v-card" id="vcard-<?= $vid ?>">
          <?php if ($v['vehicle_image']): ?>
            <img src="<?= BASE ?>/transport/<?= htmlspecialchars($v['vehicle_image']) ?>" class="v-img" alt="Vehicle">
          <?php else: ?><div class="v-img-ph">🚚</div><?php endif; ?>
          <div class="v-body">
            <div class="v-top">
              <div class="v-name">Vehicle #<?= $vid ?></div>
              <span class="avail-badge <?= $v['is_available']?'on':'off' ?>" id="badge-<?= $vid ?>"><?= $v['is_available']?'Available':'Unavailable' ?></span>
            </div>
            <div class="v-meta">
              <div class="v-meta-item"><strong><?= htmlspecialchars($v['capacity']) ?> tons</strong>Capacity</div>
              <div class="v-meta-item"><strong>₹<?= htmlspecialchars($v['rate_per_km']) ?>/km</strong>Rate</div>
              <div class="v-meta-item"><strong><?= htmlspecialchars($v['mobile']) ?></strong>Mobile</div>
              <div class="v-meta-item" style="grid-column:span 2"><strong><?= htmlspecialchars($v['address']) ?></strong>Base Location</div>
            </div>
            <button class="toggle-btn <?= $v['is_available']?'on':'off' ?>" id="tbtn-<?= $vid ?>" onclick="toggleAvail(<?= $vid ?>,<?= $v['is_available'] ?>)">
              <?= $v['is_available']?'<i class="fas fa-times-circle"></i> Mark Unavailable':'<i class="fas fa-check-circle"></i> Mark Available' ?>
            </button>
            <div class="bookings-section">
              <div class="bookings-title"><i class="fas fa-calendar-alt"></i> Recent Bookings (<?= count($bookings) ?>)</div>
              <?php if (empty($bookings)): ?>
                <div class="no-bookings"><i class="fas fa-inbox"></i> No bookings yet</div>
              <?php else: foreach ($bookings as $b): ?>
              <div class="booking-row" id="brow-<?= $b['id'] ?>">
                <div>
                  <div class="bk-user"><?= htmlspecialchars($b['user_name']) ?></div>
                  <div class="bk-route"><?= htmlspecialchars($b['pickup_location']) ?> → <?= htmlspecialchars($b['drop_location']) ?></div>
                </div>
                <div class="bk-info">
                  <strong>₹<?= number_format($b['total_cost'],0) ?></strong>
                  <?= date('d M',strtotime($b['date'])) ?>
                </div>
                <span class="status-pill <?= strtolower($b['status']) ?>" id="bstatus-<?= $b['id'] ?>"><?= $b['status'] ?></span>
                <?php if ($b['status']==='Pending'): ?>
                <div class="action-btns">
                  <button class="act-btn confirm" onclick="updateBk(<?= $b['id'] ?>,'confirm',this)" title="Confirm"><i class="fas fa-check"></i></button>
                  <button class="act-btn cancel"  onclick="updateBk(<?= $b['id'] ?>,'cancel',this)"  title="Cancel"><i class="fas fa-times"></i></button>
                </div>
                <?php else: ?><div></div><?php endif; ?>
              </div>
              <?php endforeach; endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- ALL BOOKINGS TAB -->
  <div id="tab-bookings" style="display:none;">
    <?php
    $allBookings = [];
    if ($vids) {
        $in = implode(',', array_fill(0, count($vids), '?'));
        $types = str_repeat('i', count($vids));
        $s = $conn->prepare("SELECT b.*, v.capacity, v.rate_per_km FROM booking b JOIN vehicles v ON b.vehicle_id=v.id WHERE b.vehicle_id IN ($in) ORDER BY b.id DESC");
        $s->bind_param($types, ...$vids); $s->execute();
        $allBookings = $s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    ?>
    <div class="bookings-table-wrap">
      <div class="table-header">
        <h3><i class="fas fa-list"></i> All Transport Bookings (<?= count($allBookings) ?>)</h3>
      </div>
      <?php if (empty($allBookings)): ?>
        <div style="text-align:center;padding:48px;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:2rem;margin-bottom:12px;display:block;"></i>No bookings yet</div>
      <?php else: ?>
      <div style="overflow-x:auto;">
        <table>
          <thead>
            <tr>
              <th>#</th><th>Customer</th><th>Route</th><th>Date</th><th>Distance</th><th>Amount</th><th>Status</th><th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($allBookings as $b): ?>
            <tr id="trow-<?= $b['id'] ?>">
              <td style="color:#94a3b8;font-size:0.75rem;">#<?= $b['id'] ?></td>
              <td>
                <div style="font-weight:600;color:#0f172a;"><?= htmlspecialchars($b['user_name']) ?></div>
                <div style="font-size:0.75rem;color:#94a3b8;"><?= htmlspecialchars($b['user_mobile']) ?></div>
              </td>
              <td>
                <div style="font-size:0.8rem;"><?= htmlspecialchars($b['pickup_location']) ?></div>
                <div style="font-size:0.75rem;color:#94a3b8;">→ <?= htmlspecialchars($b['drop_location']) ?></div>
              </td>
              <td><?= date('d M Y', strtotime($b['date'])) ?></td>
              <td><?= $b['distance_km'] ?> km</td>
              <td style="font-weight:700;color:#0f172a;">₹<?= number_format($b['total_cost'],0) ?></td>
              <td><span class="status-pill <?= strtolower($b['status']) ?>" id="tstatus-<?= $b['id'] ?>"><?= $b['status'] ?></span></td>
              <td>
                <?php if ($b['status']==='Pending'): ?>
                <div class="action-btns">
                  <button class="act-btn confirm" onclick="updateBkTable(<?= $b['id'] ?>,'confirm',this)"><i class="fas fa-check"></i> Confirm</button>
                  <button class="act-btn cancel"  onclick="updateBkTable(<?= $b['id'] ?>,'cancel',this)"><i class="fas fa-times"></i> Cancel</button>
                </div>
                <?php else: ?><span style="color:#94a3b8;font-size:0.78rem;">—</span><?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
function showTab(name, btn) {
  document.getElementById('tab-vehicles').style.display = name==='vehicles' ? '' : 'none';
  document.getElementById('tab-bookings').style.display = name==='bookings' ? '' : 'none';
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

function toggleAvail(id, cur) {
  const btn=document.getElementById('tbtn-'+id), badge=document.getElementById('badge-'+id);
  btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Updating...';
  fetch('<?= BASE ?>/front/toggle_vehicle.php',{method:'POST',body:new URLSearchParams({id})})
    .then(r=>r.json()).then(d=>{
      if(d.success){
        const on=d.is_available;
        badge.textContent=on?'Available':'Unavailable'; badge.className='avail-badge '+(on?'on':'off');
        btn.className='toggle-btn '+(on?'on':'off');
        btn.innerHTML=on?'<i class="fas fa-times-circle"></i> Mark Unavailable':'<i class="fas fa-check-circle"></i> Mark Available';
        btn.onclick=()=>toggleAvail(id,on);
      }
      btn.disabled=false;
    });
}

function updateBk(id, action, btn) {
  btn.disabled=true;
  fetch('<?= BASE ?>/front/api_update_booking.php',{method:'POST',body:new URLSearchParams({booking_id:id,action})})
    .then(r=>r.json()).then(d=>{
      if(d.success){
        const p=document.getElementById('bstatus-'+id);
        if(p){p.textContent=d.status;p.className='status-pill '+d.status.toLowerCase();}
        const row=document.getElementById('brow-'+id);
        if(row){const ab=row.querySelector('.action-btns');if(ab)ab.remove();}
        if(typeof showToast==='function') showToast('Booking '+d.status.toLowerCase()+'!','success');
      }
      btn.disabled=false;
    });
}

function updateBkTable(id, action, btn) {
  btn.disabled=true;
  fetch('<?= BASE ?>/front/api_update_booking.php',{method:'POST',body:new URLSearchParams({booking_id:id,action})})
    .then(r=>r.json()).then(d=>{
      if(d.success){
        const p=document.getElementById('tstatus-'+id);
        if(p){p.textContent=d.status;p.className='status-pill '+d.status.toLowerCase();}
        const row=document.getElementById('trow-'+id);
        if(row){const ab=row.querySelector('.action-btns');if(ab)ab.innerHTML='<span style="color:#94a3b8;font-size:0.78rem;">—</span>';}
        if(typeof showToast==='function') showToast('Booking '+d.status.toLowerCase()+'!','success');
      }
      btn.disabled=false;
    });
}
</script>
</body>
</html>
