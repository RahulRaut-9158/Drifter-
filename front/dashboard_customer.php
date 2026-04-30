<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();
if (($_SESSION['role'] ?? '') !== 'customer') {
    header('Location: ' . BASE . '/index.php'); exit;
}
$navActive = 'dashboard';
include '../includes/navbar.php';
$user = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Dashboard — Drifter</title>
<style>
/* ── HERO ── */
.dash-hero {
  padding:56px 24px 96px;
  background:var(--gradient-primary,linear-gradient(135deg,#0a1628,#0f2b5e));
  position:relative;overflow:hidden;
}
.dash-hero::before {
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 80% 40%,rgba(249,115,22,0.18) 0%,transparent 55%);
}
.dash-hero::after {
  content:'';position:absolute;bottom:-2px;left:0;right:0;height:60px;
  background:#f1f5f9;clip-path:ellipse(55% 100% at 50% 100%);
}
.dash-hero-inner {
  max-width:1280px;margin:0 auto;
  display:flex;justify-content:space-between;align-items:flex-end;
  flex-wrap:wrap;gap:20px;position:relative;z-index:1;
}
.dash-greeting h1 {font-size:1.9rem;font-weight:800;color:white;margin-bottom:6px;}
.dash-greeting h1 span {color:#fb923c;}
.dash-greeting p {color:rgba(255,255,255,0.55);font-size:0.9rem;}
.live-badge {
  display:inline-flex;align-items:center;gap:7px;
  background:rgba(249,115,22,0.15);border:1px solid rgba(249,115,22,0.30);
  color:#fb923c;padding:6px 16px;border-radius:50px;font-size:0.78rem;font-weight:700;
}
.live-badge::before {
  content:'';width:7px;height:7px;border-radius:50%;
  background:#f97316;animation:livePulse 1.5s infinite;
}
@keyframes livePulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.4;transform:scale(1.4);}}

/* ── STATS ── */
.stats-row {
  max-width:1280px;margin:-48px auto 0;padding:0 24px;
  display:grid;grid-template-columns:repeat(4,1fr);gap:16px;
  position:relative;z-index:10;
}
.stat-card {
  background:white;border-radius:16px;padding:22px;
  box-shadow:0 4px 20px rgba(10,22,40,0.10);
  border-top:3px solid var(--accent,#f97316);
  transition:transform 0.28s,box-shadow 0.28s;
  animation:slideUp 0.5s ease both;
}
.stat-card:hover {transform:translateY(-5px);box-shadow:0 12px 36px rgba(10,22,40,0.15);}
.stat-card:nth-child(1){animation-delay:0.05s;border-top-color:#f97316;}
.stat-card:nth-child(2){animation-delay:0.10s;border-top-color:#0f2b5e;}
.stat-card:nth-child(3){animation-delay:0.15s;border-top-color:#22c55e;}
.stat-card:nth-child(4){animation-delay:0.20s;border-top-color:#f59e0b;}
@keyframes slideUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.stat-icon {
  width:42px;height:42px;border-radius:12px;
  background:linear-gradient(135deg,#f97316,#fb923c);
  display:flex;align-items:center;justify-content:center;
  font-size:1.1rem;color:white;margin-bottom:12px;
  box-shadow:0 4px 12px rgba(249,115,22,0.30);
}
.stat-card:nth-child(2) .stat-icon{background:linear-gradient(135deg,#0f2b5e,#1e4a8a);}
.stat-card:nth-child(3) .stat-icon{background:linear-gradient(135deg,#22c55e,#16a34a);}
.stat-card:nth-child(4) .stat-icon{background:linear-gradient(135deg,#f59e0b,#d97706);}
.stat-num {font-size:1.8rem;font-weight:800;color:#0f172a;line-height:1;}
.stat-lbl {font-size:0.74rem;color:#64748b;margin-top:4px;font-weight:500;}

/* ── BODY ── */
.dash-body {max-width:1280px;margin:32px auto 80px;padding:0 24px;}

/* ── QUICK ACTIONS ── */
.quick-grid {
  display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
  gap:14px;margin-bottom:32px;
}
.quick-card {
  background:white;border-radius:14px;padding:20px;text-align:center;
  box-shadow:0 2px 12px rgba(10,22,40,0.08);text-decoration:none;color:#0f172a;
  transition:all 0.28s;border:2px solid transparent;
  animation:slideUp 0.4s ease both;
}
.quick-card:hover {transform:translateY(-5px);box-shadow:0 12px 32px rgba(249,115,22,0.18);border-color:#f97316;}
.quick-card:nth-child(1){animation-delay:0.05s;}
.quick-card:nth-child(2){animation-delay:0.10s;}
.quick-card:nth-child(3){animation-delay:0.15s;}
.quick-card:nth-child(4){animation-delay:0.20s;}
.qi {
  width:52px;height:52px;border-radius:14px;
  background:linear-gradient(135deg,#f97316,#fb923c);
  display:flex;align-items:center;justify-content:center;
  font-size:1.4rem;margin:0 auto 12px;
  box-shadow:0 4px 12px rgba(249,115,22,0.30);
  transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
.quick-card:hover .qi {transform:scale(1.15) rotate(-5deg);}
.quick-card h4 {font-size:0.9rem;font-weight:700;margin-bottom:3px;}
.quick-card p {font-size:0.76rem;color:#64748b;}

/* ── TABS ── */
.tab-header {
  display:flex;justify-content:space-between;align-items:center;
  margin-bottom:16px;flex-wrap:wrap;gap:12px;
}
.tab-bar {
  display:flex;gap:4px;background:white;border-radius:12px;
  padding:5px;box-shadow:0 2px 12px rgba(10,22,40,0.08);flex-wrap:wrap;
}
.tab-btn {
  flex:1;min-width:100px;padding:9px 14px;border:none;border-radius:9px;
  font-size:0.83rem;font-weight:600;cursor:pointer;transition:all 0.25s;
  background:transparent;color:#64748b;
  display:flex;align-items:center;justify-content:center;gap:7px;font-family:inherit;
}
.tab-btn.active {background:linear-gradient(135deg,#0f2b5e,#1e4a8a);color:white;box-shadow:0 4px 12px rgba(15,43,94,0.30);}
.tab-btn .badge {
  background:rgba(249,115,22,0.20);color:#ea580c;
  border-radius:50px;padding:1px 7px;font-size:0.68rem;font-weight:700;
}
.tab-btn.active .badge {background:rgba(255,255,255,0.20);color:white;}
.tab-panel {display:none;animation:fadeIn 0.3s ease;}
.tab-panel.active {display:block;}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:translateY(0);}}

/* ── SYNC BAR ── */
.sync-bar {
  display:flex;align-items:center;gap:8px;
  font-size:0.75rem;color:#64748b;margin-bottom:14px;
}
.sync-dot {
  width:7px;height:7px;border-radius:50%;background:#22c55e;
  animation:livePulse 2s infinite;display:inline-block;
}
.sync-dot.syncing {background:#f97316;}
.sync-dot.error {background:#ef4444;animation:none;}

/* ── BOOKING CARDS ── */
.bookings-list {display:flex;flex-direction:column;gap:14px;}
.bk-card {
  background:white;border-radius:14px;padding:20px 22px;
  box-shadow:0 2px 12px rgba(10,22,40,0.08);
  display:grid;grid-template-columns:auto 1fr auto auto;
  gap:16px;align-items:center;
  transition:all 0.28s;border-left:4px solid #e2e8f0;
  animation:slideUp 0.4s ease both;
}
.bk-card:hover {transform:translateY(-2px);box-shadow:0 8px 28px rgba(10,22,40,0.12);}
.bk-card.pending   {border-left-color:#f97316;}
.bk-card.confirmed {border-left-color:#22c55e;}
.bk-card.cancelled {border-left-color:#ef4444;opacity:0.75;}
.bk-icon {
  width:48px;height:48px;border-radius:12px;
  background:linear-gradient(135deg,#f1f5f9,#e2e8f0);
  display:flex;align-items:center;justify-content:center;
  font-size:1.4rem;flex-shrink:0;
}
.bk-route {font-size:0.93rem;font-weight:700;color:#0f172a;margin-bottom:5px;}
.bk-route span {color:#64748b;font-weight:400;font-size:0.84rem;}
.bk-meta {display:flex;gap:12px;flex-wrap:wrap;}
.bk-meta-item {font-size:0.76rem;color:#64748b;display:flex;align-items:center;gap:4px;}
.bk-meta-item i {color:#f97316;}
.bk-cost .amount {font-size:1.15rem;font-weight:800;color:#0f172a;}
.bk-cost .km {font-size:0.73rem;color:#64748b;}
.status-pill {padding:4px 12px;border-radius:50px;font-size:0.73rem;font-weight:700;white-space:nowrap;}
.status-pill.pending   {background:rgba(249,115,22,0.12);color:#c2410c;}
.status-pill.confirmed {background:rgba(34,197,94,0.12);color:#15803d;}
.status-pill.cancelled {background:rgba(239,68,68,0.10);color:#b91c1c;}
.cancel-btn {
  padding:5px 12px;border-radius:50px;font-size:0.70rem;font-weight:700;
  border:none;cursor:pointer;background:rgba(239,68,68,0.10);color:#b91c1c;
  transition:all 0.2s;white-space:nowrap;
}
.cancel-btn:hover {background:rgba(239,68,68,0.20);}
.cancel-btn:disabled {opacity:0.5;cursor:not-allowed;}

/* ── EMPTY STATE ── */
.empty-box {
  text-align:center;padding:56px 24px;background:white;
  border-radius:14px;box-shadow:0 2px 12px rgba(10,22,40,0.08);
}
.empty-box .ei {font-size:3rem;margin-bottom:12px;}
.empty-box h3 {font-size:1.1rem;font-weight:700;margin-bottom:7px;}
.empty-box p {color:#64748b;font-size:0.86rem;margin-bottom:18px;}
.empty-box a {
  display:inline-flex;align-items:center;gap:8px;
  padding:10px 22px;background:linear-gradient(135deg,#f97316,#fb923c);
  color:white;border-radius:10px;text-decoration:none;font-weight:600;font-size:0.86rem;
  transition:all 0.25s;box-shadow:0 4px 12px rgba(249,115,22,0.35);
}
.empty-box a:hover {transform:translateY(-2px);box-shadow:0 8px 24px rgba(249,115,22,0.50);}

@media(max-width:900px){
  .stats-row{grid-template-columns:repeat(2,1fr);}
  .bk-card{grid-template-columns:auto 1fr;}
  .bk-cost,.bk-card .status-pill{grid-column:span 2;}
}
@media(max-width:600px){
  .stats-row{grid-template-columns:1fr 1fr;}
  .quick-grid{grid-template-columns:1fr 1fr;}
}
</style>
</head>
<body>

<div class="dash-hero">
  <div class="dash-hero-inner">
    <div class="dash-greeting">
      <h1>👋 Welcome back, <span><?= $user ?></span>!</h1>
      <p>Here's everything happening with your bookings — updates every 8 seconds.</p>
    </div>
    <div class="live-badge">Live Updates</div>
  </div>
</div>

<!-- STATS -->
<div class="stats-row">
  <div class="stat-card">
    <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
    <div class="stat-num" id="s-total">—</div>
    <div class="stat-lbl">Total Bookings</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon"><i class="fas fa-clock"></i></div>
    <div class="stat-num" id="s-active">—</div>
    <div class="stat-lbl">Pending</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
    <div class="stat-num" id="s-confirmed">—</div>
    <div class="stat-lbl">Confirmed</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
    <div class="stat-num" id="s-spent">—</div>
    <div class="stat-lbl">Total Spent</div>
  </div>
</div>

<div class="dash-body">
  <!-- QUICK ACTIONS -->
  <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:14px;color:#0f172a;">
    <i class="fas fa-bolt" style="color:#f97316;margin-right:6px;"></i>Quick Actions
  </h3>
  <div class="quick-grid">
    <a href="<?= BASE ?>/transport/booking_step1.php" class="quick-card">
      <div class="qi"><i class="fas fa-truck-moving"></i></div>
      <h4>Book Transport</h4><p>Move goods anywhere</p>
    </a>
    <a href="<?= BASE ?>/travel/booking_step1.php" class="quick-card">
      <div class="qi"><i class="fas fa-bus"></i></div>
      <h4>Book Travel</h4><p>Comfortable rides</p>
    </a>
    <a href="<?= BASE ?>/courier/courier.php" class="quick-card">
      <div class="qi"><i class="fas fa-box"></i></div>
      <h4>Send Courier</h4><p>Fast delivery</p>
    </a>
    <a href="<?= BASE ?>/move/movers.php" class="quick-card">
      <div class="qi"><i class="fas fa-people-carry"></i></div>
      <h4>Packers &amp; Movers</h4><p>Stress-free relocation</p>
    </a>
  </div>

  <!-- TABS -->
  <div class="tab-header">
    <div class="tab-bar">
      <button class="tab-btn active" onclick="switchTab('all',this)">
        <i class="fas fa-list"></i> All <span class="badge" id="tb-all">0</span>
      </button>
      <button class="tab-btn" onclick="switchTab('active',this)">
        <i class="fas fa-clock"></i> Active <span class="badge" id="tb-active">0</span>
      </button>
      <button class="tab-btn" onclick="switchTab('past',this)">
        <i class="fas fa-history"></i> Past <span class="badge" id="tb-past">0</span>
      </button>
    </div>
    <div class="sync-bar" id="syncBar">
      <span class="sync-dot" id="syncDot"></span>
      <span id="syncText">Connecting...</span>
    </div>
  </div>

  <div id="tab-all"    class="tab-panel active"><div id="list-all"></div></div>
  <div id="tab-active" class="tab-panel"><div id="list-active"></div></div>
  <div id="tab-past"   class="tab-panel"><div id="list-past"></div></div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="<?= BASE ?>/assets/js/effects.js"></script>
<script>
function switchTab(n, btn) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tab-' + n).classList.add('active');
}

function pill(s) {
  const m = { 'Pending': 'pending', 'Confirmed': 'confirmed', 'Cancelled': 'cancelled' };
  return `<span class="status-pill ${m[s] || ''}">${s}</span>`;
}

function fmtDate(d) {
  if (!d) return '—';
  return new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
}

function renderBk(b, i) {
  const icon = b.vehicle_category === 'travel' ? 'fas fa-bus' : 'fas fa-truck-moving';
  const delay = (i * 0.06).toFixed(2);
  const cb = b.status === 'Pending'
    ? `<button class="cancel-btn" onclick="cancelBk(${b.id},this)"><i class="fas fa-times"></i> Cancel</button>`
    : '';
  return `<div class="bk-card ${b.status.toLowerCase()}" style="animation-delay:${delay}s" id="bk-${b.id}">
    <div class="bk-icon"><i class="${icon}" style="color:#f97316;"></i></div>
    <div>
      <div class="bk-route">${b.pickup_location} <span>→</span> ${b.drop_location}</div>
      <div class="bk-meta">
        <span class="bk-meta-item"><i class="fas fa-calendar"></i> ${fmtDate(b.date)}</span>
        <span class="bk-meta-item"><i class="fas fa-clock"></i> ${b.time ? b.time.slice(0,5) : '—'}</span>
        <span class="bk-meta-item"><i class="fas fa-road"></i> ${b.distance_km} km</span>
        <span class="bk-meta-item"><i class="fas fa-user"></i> ${b.driver_name || '—'}</span>
        <span class="bk-meta-item"><i class="fas fa-phone"></i> ${b.driver_mobile || '—'}</span>
      </div>
    </div>
    <div class="bk-cost">
      <div class="amount">₹${parseFloat(b.total_cost).toLocaleString('en-IN', {minimumFractionDigits:0})}</div>
      <div class="km">${b.distance_km}km × ₹${b.rate_per_km}/km</div>
    </div>
    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
      ${pill(b.status)}${cb}
    </div>
  </div>`;
}

function emptyBox(msg, link, lt) {
  return `<div class="empty-box"><div class="ei">🔍</div><h3>No bookings here</h3><p>${msg}</p><a href="${link}"><i class="fas fa-plus"></i> ${lt}</a></div>`;
}

function renderList(id, items, em, el, elt) {
  const e = document.getElementById(id);
  if (!items || !items.length) { e.innerHTML = emptyBox(em, el, elt); return; }
  e.innerHTML = `<div class="bookings-list">${items.map((b, i) => renderBk(b, i)).join('')}</div>`;
  if (window.observeReveal) window.observeReveal();
}

let prevCount = -1;
const syncDot = document.getElementById('syncDot');
const syncText = document.getElementById('syncText');

function setSyncing() {
  syncDot.className = 'sync-dot syncing';
  syncText.textContent = 'Syncing...';
}
function setLive(time) {
  syncDot.className = 'sync-dot';
  syncText.textContent = 'Updated at ' + time;
}
function setError() {
  syncDot.className = 'sync-dot error';
  syncText.textContent = 'Sync failed — retrying';
}

function fetchData() {
  setSyncing();
  fetch('<?= BASE ?>/front/api_my_rides.php')
    .then(r => r.json())
    .then(d => {
      if (d.error) { setError(); return; }

      // Animate counters
      const total = parseInt(document.getElementById('s-total').textContent) || 0;
      if (total !== d.count) {
        if (window.animateCounter) {
          animateCounter(document.getElementById('s-total'), d.count);
          animateCounter(document.getElementById('s-active'), d.pending || 0);
          animateCounter(document.getElementById('s-confirmed'), d.confirmed || 0);
        } else {
          document.getElementById('s-total').textContent = d.count;
          document.getElementById('s-active').textContent = d.pending || 0;
          document.getElementById('s-confirmed').textContent = d.confirmed || 0;
        }
      }
      document.getElementById('s-spent').textContent = '₹' + parseFloat(d.total_spent || 0).toLocaleString('en-IN', {minimumFractionDigits:0});

      document.getElementById('tb-all').textContent = d.count;
      document.getElementById('tb-active').textContent = d.active.length;
      document.getElementById('tb-past').textContent = d.past.length;

      renderList('list-all', d.all, 'No bookings yet.', '<?= BASE ?>/index.php', 'Book a Service');
      renderList('list-active', d.active, 'No active bookings.', '<?= BASE ?>/transport/booking_step1.php', 'Book Transport');
      renderList('list-past', d.past, 'No past rides yet.', '<?= BASE ?>/travel/booking_step1.php', 'Book Travel');

      if (prevCount !== -1 && d.count > prevCount) {
        if (window.showToast) showToast('🎉 New booking update!', 'success');
      }
      prevCount = d.count;
      setLive(d.timestamp);
    })
    .catch(() => setError());
}

fetchData();
setInterval(fetchData, 8000);

function cancelBk(id, btn) {
  if (!confirm('Cancel this booking?')) return;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  fetch('<?= BASE ?>/front/api_cancel_booking.php', {
    method: 'POST',
    body: new URLSearchParams({ booking_id: id })
  })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        if (window.showToast) showToast('Booking cancelled.', 'info');
        fetchData();
      } else {
        if (window.showToast) showToast(d.msg || 'Could not cancel.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-times"></i> Cancel';
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-times"></i> Cancel';
    });
}
</script>
</body>
</html>
