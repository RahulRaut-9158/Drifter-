<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: ".BASE."/front/login.php"); exit; }
if (($_SESSION['role'] ?? '') !== 'customer') { header("Location: ".BASE."/front/index.php"); exit; }
$navActive = '';
include '../includes/navbar.php';
$user = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Dashboard — Drifter</title>
<style>
.dash-hero{
  padding:56px 24px 88px;position:relative;overflow:hidden;
  background:linear-gradient(135deg,#212529 0%,#343a40 100%);
}
.dash-hero::after{
  content:'';position:absolute;inset:0;
  background:url('https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=1920&q=80') center/cover no-repeat;
  filter:brightness(0.12);z-index:0;
}
.dash-hero-inner{max-width:1280px;margin:0 auto;display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:20px;position:relative;z-index:1;}
.dash-greeting h1{font-size:1.9rem;font-weight:800;color:white;margin-bottom:6px;}
.dash-greeting p{color:rgba(255,255,255,0.55);font-size:0.92rem;}
.live-dot{display:inline-flex;align-items:center;gap:6px;background:rgba(255,107,0,0.15);border:1px solid rgba(255,107,0,0.35);color:#FF6B00;padding:5px 14px;border-radius:50px;font-size:0.78rem;font-weight:600;}
.live-dot::before{content:'';width:7px;height:7px;border-radius:50%;background:#FF6B00;animation:pdot 1.5s infinite;}
@keyframes pdot{0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.4;transform:scale(1.4);}}

.stats-row{max-width:1280px;margin:-44px auto 0;padding:0 24px;display:grid;grid-template-columns:repeat(4,1fr);gap:16px;}
.stat-card{background:white;border-radius:14px;padding:20px 22px;box-shadow:var(--shadow);border-top:3px solid var(--orange);animation:slideUp 0.5s ease both;transition:transform 0.2s,box-shadow 0.2s;}
.stat-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);}
.stat-card:nth-child(1){animation-delay:0.05s;border-top-color:var(--orange);}
.stat-card:nth-child(2){animation-delay:0.10s;border-top-color:#f59e0b;}
.stat-card:nth-child(3){animation-delay:0.15s;border-top-color:#22c55e;}
.stat-card:nth-child(4){animation-delay:0.20s;border-top-color:var(--yellow);}
@keyframes slideUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.stat-icon{font-size:1.5rem;margin-bottom:10px;}
.stat-num{font-size:1.8rem;font-weight:800;color:var(--primary);line-height:1;}
.stat-lbl{font-size:0.75rem;color:var(--muted);margin-top:4px;font-weight:500;}

.dash-body{max-width:1280px;margin:32px auto 80px;padding:0 24px;}
.section-title{font-size:1rem;font-weight:700;color:var(--primary);margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.section-title::after{content:'';flex:1;height:1px;background:#e9ecef;}

.tab-bar{display:flex;gap:4px;background:white;border-radius:12px;padding:5px;box-shadow:var(--shadow);margin-bottom:24px;flex-wrap:wrap;}
.tab-btn{flex:1;min-width:110px;padding:9px 14px;border:none;border-radius:8px;font-size:0.84rem;font-weight:600;cursor:pointer;transition:all 0.2s;background:transparent;color:var(--muted);display:flex;align-items:center;justify-content:center;gap:6px;font-family:inherit;}
.tab-btn.active{background:var(--orange);color:white;box-shadow:0 3px 10px rgba(255,107,0,0.35);}
.tab-btn .badge{background:rgba(255,107,0,0.15);color:var(--orange);border-radius:50px;padding:1px 7px;font-size:0.68rem;font-weight:700;}
.tab-btn.active .badge{background:rgba(255,255,255,0.25);color:white;}
.tab-panel{display:none;animation:fadeIn 0.25s ease;}
.tab-panel.active{display:block;}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:none;}}

.quick-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:32px;}
.quick-card{background:white;border-radius:12px;padding:20px;text-align:center;box-shadow:var(--shadow);text-decoration:none;color:var(--text);transition:all 0.22s;border:2px solid transparent;}
.quick-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);border-color:var(--orange);}
.quick-card .qi{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin:0 auto 10px;}
.quick-card h4{font-size:0.88rem;font-weight:700;margin-bottom:3px;color:var(--primary);}
.quick-card p{font-size:0.75rem;color:var(--muted);}

.bookings-list{display:flex;flex-direction:column;gap:12px;}
.bk-card{background:white;border-radius:12px;padding:18px 20px;box-shadow:var(--shadow);display:grid;grid-template-columns:auto 1fr auto auto;gap:14px;align-items:center;border-left:4px solid #e9ecef;transition:all 0.2s;}
.bk-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-lg);}
.bk-card.pending{border-left-color:#f59e0b;}
.bk-card.confirmed{border-left-color:#22c55e;}
.bk-card.cancelled{border-left-color:#ef4444;opacity:0.75;}
.bk-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;background:#f8f9fa;}
.bk-route{font-size:0.93rem;font-weight:700;color:var(--primary);margin-bottom:4px;}
.bk-route span{color:var(--muted);font-weight:400;font-size:0.82rem;}
.bk-meta{display:flex;gap:12px;flex-wrap:wrap;}
.bk-meta-item{font-size:0.76rem;color:var(--muted);display:flex;align-items:center;gap:4px;}
.bk-meta-item i{color:var(--orange);}
.bk-cost .amount{font-size:1.1rem;font-weight:800;color:var(--primary);}
.bk-cost .km{font-size:0.72rem;color:var(--muted);}
.status-pill{padding:4px 12px;border-radius:50px;font-size:0.72rem;font-weight:700;white-space:nowrap;}
.status-pill.pending{background:#fef3c7;color:#92400e;}
.status-pill.confirmed{background:#dcfce7;color:#166534;}
.status-pill.cancelled{background:#fee2e2;color:#991b1b;}
.cancel-btn{padding:5px 12px;border-radius:50px;font-size:0.72rem;font-weight:700;border:none;cursor:pointer;background:#fee2e2;color:#991b1b;transition:all 0.2s;white-space:nowrap;}
.cancel-btn:hover{background:#fecaca;}
.cancel-btn:disabled{opacity:0.5;cursor:not-allowed;}

.empty-box{text-align:center;padding:56px 24px;background:white;border-radius:12px;box-shadow:var(--shadow);}
.empty-box .ei{font-size:3rem;margin-bottom:12px;}
.empty-box h3{font-size:1.1rem;font-weight:700;margin-bottom:6px;color:var(--primary);}
.empty-box p{color:var(--muted);font-size:0.86rem;margin-bottom:18px;}
.empty-box a{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:var(--orange);color:white;border-radius:8px;text-decoration:none;font-weight:600;font-size:0.86rem;transition:all 0.2s;}
.empty-box a:hover{background:var(--brand2);transform:translateY(-2px);}

.last-updated{font-size:0.73rem;color:var(--muted);display:flex;align-items:center;gap:5px;margin-bottom:14px;}

#toast{position:fixed;bottom:24px;right:24px;z-index:99999;padding:12px 18px;border-radius:10px;font-weight:600;font-size:0.86rem;box-shadow:0 8px 24px rgba(0,0,0,0.18);transform:translateY(80px);opacity:0;transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1);max-width:300px;display:flex;align-items:center;gap:8px;}
#toast.show{transform:translateY(0);opacity:1;}
#toast.success{background:#22c55e;color:white;}
#toast.error{background:#ef4444;color:white;}
#toast.info{background:var(--orange);color:white;}

@media(max-width:900px){.stats-row{grid-template-columns:repeat(2,1fr);}.bk-card{grid-template-columns:auto 1fr;}.bk-cost,.bk-card>div:last-child{grid-column:span 2;}}
@media(max-width:600px){.stats-row{grid-template-columns:1fr 1fr;}.quick-grid{grid-template-columns:1fr 1fr;}}
</style>
</head>
<body>

<div class="dash-hero">
  <div class="dash-hero-inner">
    <div class="dash-greeting">
      <h1>👋 Welcome back, <?= $user ?>!</h1>
      <p>Here's everything happening with your bookings.</p>
    </div>
    <div class="live-dot">Live Updates</div>
  </div>
</div>

<!-- STAT CARDS -->
<div class="stats-row" id="statsRow">
  <div class="stat-card"><div class="stat-icon">📋</div><div class="stat-num" id="s-total">—</div><div class="stat-lbl">Total Bookings</div></div>
  <div class="stat-card"><div class="stat-icon">⏳</div><div class="stat-num" id="s-active">—</div><div class="stat-lbl">Pending</div></div>
  <div class="stat-card"><div class="stat-icon">✅</div><div class="stat-num" id="s-confirmed">—</div><div class="stat-lbl">Confirmed</div></div>
  <div class="stat-card"><div class="stat-icon">💰</div><div class="stat-num" id="s-spent">—</div><div class="stat-lbl">Total Spent</div></div>
</div>

<div class="dash-body">

  <div class="section-title"><i class="fas fa-bolt" style="color:var(--orange)"></i> Quick Actions</div>
  <div class="quick-grid">
    <a href="<?= BASE ?>/transport/booking_step1.php" class="quick-card">
      <div class="qi" style="background:#fff3eb;">🚚</div><h4>Book Transport</h4><p>Move goods anywhere</p>
    </a>
    <a href="<?= BASE ?>/travel/booking_step1.php" class="quick-card">
      <div class="qi" style="background:#f0f9ff;">🚌</div><h4>Book Travel</h4><p>Comfortable rides</p>
    </a>
    <a href="<?= BASE ?>/courier/courier.php" class="quick-card">
      <div class="qi" style="background:#f8f9fa;">📦</div><h4>Send Courier</h4><p>Fast delivery</p>
    </a>
    <a href="<?= BASE ?>/move/movers.php" class="quick-card">
      <div class="qi" style="background:#fffbeb;">🏠</div><h4>Packers &amp; Movers</h4><p>Stress-free relocation</p>
    </a>
  </div>

  <!-- TABS -->
  <div class="tab-bar">
    <button class="tab-btn active" onclick="switchTab('all',this)"><i class="fas fa-list"></i> All Bookings <span class="badge" id="tb-all">0</span></button>
    <button class="tab-btn" onclick="switchTab('active',this)"><i class="fas fa-clock"></i> Active <span class="badge" id="tb-active">0</span></button>
    <button class="tab-btn" onclick="switchTab('past',this)"><i class="fas fa-history"></i> Past Rides <span class="badge" id="tb-past">0</span></button>
  </div>

  <div class="last-updated"><i class="fas fa-sync-alt"></i> Last updated: <span id="lastUpdated">—</span></div>

  <div id="tab-all" class="tab-panel active"><div id="list-all"></div></div>
  <div id="tab-active" class="tab-panel"><div id="list-active"></div></div>
  <div id="tab-past" class="tab-panel"><div id="list-past"></div></div>
</div>

<div id="toast"></div>
<?php include '../includes/footer.php'; ?>

<script>
let allData = {all:[],active:[],past:[]};

function switchTab(name, btn) {
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  document.querySelectorAll('.tab-panel').forEach(p=>p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tab-'+name).classList.add('active');
}

function statusPill(s) {
  return `<span class="status-pill ${s.toLowerCase()}">${s}</span>`;
}

function renderBooking(b, i) {
  const icon = b.vehicle_category === 'travel' ? '🚌' : '🚚';
  const iconClass = b.vehicle_category === 'travel' ? 'travel' : 'transport';
  const delay = (i * 0.06).toFixed(2);
  const cancelBtn = b.status === 'Pending'
    ? `<button class="cancel-btn" onclick="cancelBooking(${b.id},this)"><i class="fas fa-times"></i> Cancel</button>`
    : '';
  return `
  <div class="bk-card ${b.status.toLowerCase()}" style="animation-delay:${delay}s" id="bk-${b.id}">
    <div class="bk-icon ${iconClass}">${icon}</div>
    <div>
      <div class="bk-route">${b.pickup_location} <span>→</span> ${b.drop_location}</div>
      <div class="bk-meta">
        <span class="bk-meta-item"><i class="fas fa-calendar"></i> ${formatDate(b.date)}</span>
        <span class="bk-meta-item"><i class="fas fa-clock"></i> ${b.time ? b.time.slice(0,5) : '—'}</span>
        <span class="bk-meta-item"><i class="fas fa-road"></i> ${b.distance_km} km</span>
        <span class="bk-meta-item"><i class="fas fa-user"></i> ${b.driver_name||'—'}</span>
        <span class="bk-meta-item"><i class="fas fa-phone"></i> ${b.driver_mobile||'—'}</span>
      </div>
    </div>
    <div class="bk-cost">
      <div class="amount">₹${parseFloat(b.total_cost).toLocaleString('en-IN',{minimumFractionDigits:2})}</div>
      <div class="km">${b.distance_km} km × ₹${b.rate_per_km}/km</div>
    </div>
    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">${statusPill(b.status)}${cancelBtn}</div>
  </div>`;
}

function emptyBox(msg, link, linkText) {
  return `<div class="empty-box"><div class="ei">🔍</div><h3>No bookings here</h3><p>${msg}</p><a href="${link}">${linkText}</a></div>`;
}

function formatDate(d) {
  if (!d) return '—';
  const dt = new Date(d);
  return dt.toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'});
}

function renderList(id, items, emptyMsg, emptyLink, emptyLinkText) {
  const el = document.getElementById(id);
  if (!items || items.length === 0) {
    el.innerHTML = emptyBox(emptyMsg, emptyLink, emptyLinkText); return;
  }
  el.innerHTML = `<div class="bookings-list">${items.map((b,i)=>renderBooking(b,i)).join('')}</div>`;
}

function updateStats(d) {
  animateNum('s-total', d.count);
  animateNum('s-active', d.pending || 0);
  animateNum('s-confirmed', d.confirmed || 0);
  document.getElementById('s-spent').textContent = '₹'+parseFloat(d.total_spent||0).toLocaleString('en-IN',{minimumFractionDigits:0});
  document.getElementById('tb-all').textContent    = d.count;
  document.getElementById('tb-active').textContent  = d.active.length;
  document.getElementById('tb-past').textContent    = d.past.length;
  document.getElementById('lastUpdated').textContent = d.timestamp;
}

function animateNum(id, target) {
  const el = document.getElementById(id);
  const start = parseInt(el.textContent) || 0;
  const diff = target - start;
  if (diff === 0) return;
  let step = 0, steps = 20;
  const t = setInterval(()=>{
    step++;
    el.textContent = Math.round(start + diff * (step/steps));
    if (step >= steps) clearInterval(t);
  }, 30);
}

function showToast(msg, type='info') {
  const t = document.getElementById('toast');
  t.innerHTML = `<i class="fas fa-${type==='success'?'check-circle':type==='error'?'times-circle':'info-circle'}"></i> ${msg}`;
  t.className = 'show ' + type;
  setTimeout(()=>t.className='', 4000);
}

let prevCount = -1;
const BASE_URL = '<?= BASE ?>';

function fetchData() {
  fetch(BASE_URL+'/front/api_my_rides.php')
    .then(r=>r.json())
    .then(d=>{
      if (d.error) return;
      allData = d;
      updateStats(d);
      renderList('list-all',   d.all,    'You have no bookings yet.',          BASE_URL+'/front/index.php#services', '🚀 Book a Service');
      renderList('list-active',d.active, 'No active bookings right now.',      BASE_URL+'/transport/booking_step1.php', '🚚 Book Transport');
      renderList('list-past',  d.past,   'No past rides yet. Start exploring!',BASE_URL+'/travel/booking_step1.php', '🚌 Book Travel');
      if (prevCount !== -1 && d.count > prevCount) showToast('New booking update received!','success');
      prevCount = d.count;
    }).catch(()=>{});
}

fetchData();
setInterval(fetchData, 8000);

function cancelBooking(id, btn) {
  if (!confirm('Cancel this booking?')) return;
  btn.disabled = true; btn.textContent = 'Cancelling...';
  fetch(BASE_URL+'/front/api_cancel_booking.php', {
    method: 'POST',
    body: new URLSearchParams({booking_id: id})
  }).then(r => r.json()).then(d => {
    if (d.success) {
      showToast('Booking cancelled.', 'info');
      fetchData();
    } else {
      showToast(d.msg || 'Could not cancel.', 'error');
      btn.disabled = false; btn.textContent = 'Cancel';
    }
  }).catch(() => { btn.disabled = false; btn.textContent = 'Cancel'; });
}
</script>
</body></html>
