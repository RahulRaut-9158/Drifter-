<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../includes/db.php';
$navActive = '';
include __DIR__ . '/../includes/navbar.php';
$admin = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Dashboard — Drifter</title>
<style>
.adm-hero{padding:48px 24px 80px;background:var(--text,#2a2520);position:relative;overflow:hidden;}
.adm-hero::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(42,37,32,0.96) 0%,rgba(188,159,139,0.30) 100%);}
.adm-hero-inner{max-width:1280px;margin:0 auto;display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;position:relative;z-index:1;}
.adm-hero h1{font-size:1.8rem;font-weight:800;color:white;margin-bottom:5px;}
.adm-hero p{color:rgba(255,255,255,0.50);font-size:0.88rem;}
.adm-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(188,159,139,0.18);border:1px solid rgba(188,159,139,0.35);color:#CADABF;padding:5px 14px;border-radius:50px;font-size:0.76rem;font-weight:700;}
.adm-badge::before{content:'';width:7px;height:7px;border-radius:50%;background:#B5CFB7;animation:pd 1.5s infinite;}
@keyframes pd{0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.4;transform:scale(1.3);}}

/* Stats grid */
.stats-row{max-width:1280px;margin:-44px auto 0;padding:0 24px;display:grid;grid-template-columns:repeat(4,1fr);gap:16px;}
.s-card{background:white;border-radius:16px;padding:20px 22px;box-shadow:var(--shadow,0 2px 16px rgba(42,37,32,0.08));border-top:3px solid var(--c,#BC9F8B);animation:slideUp 0.5s ease both;transition:transform 0.25s,box-shadow 0.25s;}
.s-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-md,0 6px 28px rgba(42,37,32,0.12));}
@keyframes slideUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.s-card:nth-child(1){--c:#BC9F8B;animation-delay:0.05s;}
.s-card:nth-child(2){--c:#B5CFB7;animation-delay:0.10s;}
.s-card:nth-child(3){--c:#CADABF;animation-delay:0.15s;}
.s-card:nth-child(4){--c:#BC9F8B;animation-delay:0.20s;}
.s-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:10px;background:var(--linen,#E7E8D8);}
.s-num{font-size:1.8rem;font-weight:800;color:var(--text,#2a2520);line-height:1;}
.s-lbl{font-size:0.73rem;color:var(--muted,#7a7060);margin-top:4px;font-weight:500;}

/* Body */
.adm-body{max-width:1280px;margin:32px auto 80px;padding:0 24px;}
.adm-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px;}
.adm-card{background:white;border-radius:16px;padding:24px;box-shadow:var(--shadow,0 2px 16px rgba(42,37,32,0.08));}
.adm-card-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--linen,#E7E8D8);}
.adm-card-head h3{font-size:0.97rem;font-weight:700;}
.adm-card-head .badge{background:rgba(188,159,139,0.18);color:#7a5a40;padding:3px 10px;border-radius:50px;font-size:0.72rem;font-weight:700;}

/* Tabs */
.tab-bar{display:flex;gap:4px;background:var(--linen,#E7E8D8);border-radius:10px;padding:4px;margin-bottom:20px;flex-wrap:wrap;}
.tab-btn{flex:1;min-width:100px;padding:9px 12px;border:none;border-radius:8px;font-size:0.82rem;font-weight:600;cursor:pointer;transition:all 0.22s;background:transparent;color:var(--muted,#7a7060);font-family:inherit;}
.tab-btn.active{background:white;color:var(--text,#2a2520);box-shadow:0 2px 8px rgba(42,37,32,0.10);}
.tab-panel{display:none;animation:fadeIn 0.3s ease;}
.tab-panel.active{display:block;}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

/* Table */
.adm-table{width:100%;border-collapse:collapse;font-size:0.82rem;}
.adm-table th{text-align:left;padding:9px 12px;font-size:0.72rem;font-weight:700;color:var(--muted,#7a7060);text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid var(--linen,#E7E8D8);}
.adm-table td{padding:10px 12px;border-bottom:1px solid var(--linen,#E7E8D8);color:var(--text,#2a2520);vertical-align:middle;}
.adm-table tr:last-child td{border-bottom:none;}
.adm-table tr:hover td{background:rgba(231,232,216,0.40);}

/* Pills */
.pill{display:inline-flex;align-items:center;padding:3px 10px;border-radius:50px;font-size:0.70rem;font-weight:700;}
.pill-customer{background:rgba(202,218,191,0.30);color:#3a6b3c;}
.pill-owner{background:rgba(188,159,139,0.18);color:#7a5a40;}
.pill-company{background:rgba(181,207,183,0.25);color:#2d5a2f;}
.pill-admin{background:rgba(42,37,32,0.12);color:#2a2520;}
.pill-pending{background:rgba(188,159,139,0.18);color:#7a5a40;}
.pill-confirmed{background:rgba(181,207,183,0.25);color:#3a6b3c;}
.pill-cancelled{background:rgba(231,232,216,0.80);color:#7a7060;}
.pill-active{background:rgba(181,207,183,0.25);color:#3a6b3c;}
.pill-inactive{background:rgba(192,57,43,0.10);color:#7a1a0a;}

/* Toggle user btn */
.toggle-user-btn{padding:4px 10px;border:none;border-radius:6px;font-size:0.70rem;font-weight:700;cursor:pointer;transition:all 0.2s;font-family:inherit;}
.toggle-user-btn.deactivate{background:rgba(192,57,43,0.10);color:#7a1a0a;}
.toggle-user-btn.deactivate:hover{background:rgba(192,57,43,0.20);}
.toggle-user-btn.activate{background:rgba(181,207,183,0.25);color:#3a6b3c;}
.toggle-user-btn.activate:hover{background:rgba(181,207,183,0.45);}

/* Trend chart */
.trend-bars{display:flex;align-items:flex-end;gap:8px;height:80px;margin-top:8px;}
.trend-bar-wrap{flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;}
.trend-bar{width:100%;background:linear-gradient(to top,#BC9F8B,#CADABF);border-radius:4px 4px 0 0;min-height:4px;transition:height 0.6s ease;}
.trend-lbl{font-size:0.62rem;color:var(--muted,#7a7060);white-space:nowrap;}
.trend-val{font-size:0.65rem;font-weight:700;color:var(--text,#2a2520);}

/* Service summary cards */
.svc-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;}
.svc-mini{background:var(--linen,#E7E8D8);border-radius:10px;padding:14px;display:flex;justify-content:space-between;align-items:center;}
.svc-mini .label{font-size:0.78rem;color:var(--muted,#7a7060);font-weight:500;}
.svc-mini .val{font-size:1.3rem;font-weight:800;color:var(--text,#2a2520);}
.svc-mini .sub{font-size:0.68rem;color:var(--muted,#7a7060);}

/* Message row */
.msg-row{padding:12px 0;border-bottom:1px solid var(--linen,#E7E8D8);display:grid;grid-template-columns:1fr auto;gap:12px;align-items:start;}
.msg-row:last-child{border-bottom:none;}
.msg-name{font-size:0.85rem;font-weight:700;margin-bottom:3px;}
.msg-text{font-size:0.80rem;color:var(--muted,#7a7060);line-height:1.5;}
.msg-meta{font-size:0.70rem;color:var(--muted,#7a7060);margin-top:4px;}
.mark-read-btn{padding:4px 10px;border:none;border-radius:6px;font-size:0.70rem;font-weight:700;cursor:pointer;background:rgba(188,159,139,0.18);color:#7a5a40;transition:all 0.2s;font-family:inherit;white-space:nowrap;}
.mark-read-btn:hover{background:rgba(188,159,139,0.35);}

/* Revenue highlight */
.revenue-box{background:linear-gradient(135deg,#BC9F8B,#a08070);border-radius:14px;padding:22px;color:white;text-align:center;margin-bottom:16px;}
.revenue-box .rev-num{font-size:2.2rem;font-weight:800;margin-bottom:4px;}
.revenue-box .rev-lbl{font-size:0.80rem;opacity:0.80;}

/* Loading */
.loading{text-align:center;padding:32px;color:var(--muted,#7a7060);font-size:0.85rem;}
.loading i{font-size:1.5rem;margin-bottom:8px;display:block;animation:spin 1s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}

@media(max-width:900px){.stats-row{grid-template-columns:repeat(2,1fr);}.adm-grid{grid-template-columns:1fr;}}
@media(max-width:600px){.stats-row{grid-template-columns:1fr 1fr;}.svc-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>

<div class="adm-hero">
  <div class="adm-hero-inner">
    <div>
      <h1>🛡️ Admin Dashboard</h1>
      <p>Platform overview and management — logged in as <strong style="color:#CADABF"><?= $admin ?></strong></p>
    </div>
    <div class="adm-badge">Live Data</div>
  </div>
</div>

<!-- STAT CARDS -->
<div class="stats-row">
  <div class="s-card"><div class="s-icon">👥</div><div class="s-num" id="a-users">—</div><div class="s-lbl">Total Users</div></div>
  <div class="s-card"><div class="s-icon">📋</div><div class="s-num" id="a-bookings">—</div><div class="s-lbl">Total Bookings</div></div>
  <div class="s-card"><div class="s-icon">💰</div><div class="s-num" id="a-revenue">—</div><div class="s-lbl">Confirmed Revenue</div></div>
  <div class="s-card"><div class="s-icon">🚗</div><div class="s-num" id="a-vehicles">—</div><div class="s-lbl">Registered Vehicles</div></div>
</div>

<div class="adm-body">

  <!-- ROW 1: Trend + Services -->
  <div class="adm-grid">
    <div class="adm-card">
      <div class="adm-card-head"><h3>📈 Bookings — Last 7 Days</h3><span class="badge" id="trend-total">—</span></div>
      <div class="trend-bars" id="trendBars"><div class="loading"><i class="fas fa-spinner"></i>Loading...</div></div>
    </div>
    <div class="adm-card">
      <div class="adm-card-head"><h3>🗂️ Service Summary</h3></div>
      <div class="revenue-box"><div class="rev-num" id="a-rev2">₹—</div><div class="rev-lbl">Total Confirmed Revenue</div></div>
      <div class="svc-grid">
        <div class="svc-mini"><div><div class="label">Transport Bookings</div><div class="sub" id="a-pending">— pending</div></div><div class="val" id="a-bk">—</div></div>
        <div class="svc-mini"><div><div class="label">Courier Requests</div><div class="sub" id="a-cpen">— pending</div></div><div class="val" id="a-cr">—</div></div>
        <div class="svc-mini"><div><div class="label">Movers Requests</div><div class="sub" id="a-mpen">— pending</div></div><div class="val" id="a-mr">—</div></div>
        <div class="svc-mini"><div><div class="label">Companies</div><div class="sub">courier + movers</div></div><div class="val" id="a-co">—</div></div>
      </div>
    </div>
  </div>

  <!-- ROW 2: Tabs for Users / Bookings / Vehicles / Messages -->
  <div class="adm-card">
    <div class="tab-bar">
      <button class="tab-btn active" onclick="switchTab('users',this)"><i class="fas fa-users"></i> Users</button>
      <button class="tab-btn" onclick="switchTab('bookings',this)"><i class="fas fa-calendar-check"></i> Bookings</button>
      <button class="tab-btn" onclick="switchTab('vehicles',this)"><i class="fas fa-truck"></i> Vehicles</button>
      <button class="tab-btn" onclick="switchTab('messages',this)"><i class="fas fa-envelope"></i> Messages <span id="msg-badge" style="background:#BC9F8B;color:white;border-radius:50px;padding:1px 7px;font-size:0.68rem;font-weight:700;display:none;"></span></button>
    </div>

    <!-- USERS -->
    <div id="tab-users" class="tab-panel active">
      <div class="loading" id="users-loading"><i class="fas fa-spinner"></i>Loading users...</div>
      <div id="users-table" style="overflow-x:auto;display:none;"></div>
    </div>

    <!-- BOOKINGS -->
    <div id="tab-bookings" class="tab-panel">
      <div class="loading" id="bookings-loading"><i class="fas fa-spinner"></i>Loading bookings...</div>
      <div id="bookings-table" style="overflow-x:auto;display:none;"></div>
    </div>

    <!-- VEHICLES -->
    <div id="tab-vehicles" class="tab-panel">
      <div class="loading" id="vehicles-loading"><i class="fas fa-spinner"></i>Loading vehicles...</div>
      <div id="vehicles-table" style="overflow-x:auto;display:none;"></div>
    </div>

    <!-- MESSAGES -->
    <div id="tab-messages" class="tab-panel">
      <div class="loading" id="messages-loading"><i class="fas fa-spinner"></i>Loading messages...</div>
      <div id="messages-list" style="display:none;"></div>
    </div>
  </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
const BASE = '<?= BASE ?>';
let loaded = { users:false, bookings:false, vehicles:false, messages:false };

function switchTab(name, btn) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tab-' + name).classList.add('active');
  if (!loaded[name]) loadTab(name);
}

function pill(cls, text) { return `<span class="pill pill-${cls}">${text}</span>`; }
function fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'}) : '—'; }

// Load overview
function loadOverview() {
  fetch(BASE + '/admin/api_admin.php?action=overview')
    .then(r => r.json()).then(d => {
      if (d.error) return;
      animNum('a-users', d.users);
      animNum('a-bookings', d.bookings);
      document.getElementById('a-revenue').textContent = '₹' + Math.round(d.revenue).toLocaleString('en-IN');
      document.getElementById('a-rev2').textContent    = '₹' + Math.round(d.revenue).toLocaleString('en-IN');
      animNum('a-vehicles', d.vehicles);
      animNum('a-bk', d.bookings);
      animNum('a-cr', d.courier_req);
      animNum('a-mr', d.movers_req);
      document.getElementById('a-co').textContent = (d.courier_co + d.movers_co);
      document.getElementById('a-pending').textContent = d.pending_b + ' pending';
      document.getElementById('a-cpen').textContent    = d.courier_pen + ' pending';
      document.getElementById('a-mpen').textContent    = d.movers_pen + ' pending';
      if (d.unread_msgs > 0) {
        const b = document.getElementById('msg-badge');
        b.textContent = d.unread_msgs; b.style.display = 'inline';
      }
      // Trend bars
      if (d.trend && d.trend.length) {
        const max = Math.max(...d.trend.map(t => t.count), 1);
        document.getElementById('trend-total').textContent = d.trend.reduce((s,t)=>s+t.count,0) + ' this week';
        document.getElementById('trendBars').innerHTML = d.trend.map(t => `
          <div class="trend-bar-wrap">
            <div class="trend-val">${t.count}</div>
            <div class="trend-bar" style="height:${Math.max(4, Math.round((t.count/max)*72))}px"></div>
            <div class="trend-lbl">${t.date}</div>
          </div>`).join('');
      }
    }).catch(() => {});
}

function loadTab(name) {
  loaded[name] = true;
  const loading = document.getElementById(name + '-loading');
  const container = document.getElementById(name + (name==='messages'?'-list':'-table'));

  fetch(BASE + '/admin/api_admin.php?action=' + name)
    .then(r => r.json()).then(d => {
      loading.style.display = 'none';
      container.style.display = 'block';

      if (name === 'users') {
        const rows = (d.users || []).map(u => `
          <tr>
            <td><strong>${u.username}</strong></td>
            <td>${u.email}</td>
            <td>${pill(u.role, u.role)}</td>
            <td>${u.phone || '—'}</td>
            <td>${fmtDate(u.created_at)}</td>
            <td>${pill(u.is_active?'active':'inactive', u.is_active?'Active':'Inactive')}</td>
            <td><button class="toggle-user-btn ${u.is_active?'deactivate':'activate'}" onclick="toggleUser(${u.id},this)">${u.is_active?'Deactivate':'Activate'}</button></td>
          </tr>`).join('');
        container.innerHTML = `<table class="adm-table"><thead><tr><th>Username</th><th>Email</th><th>Role</th><th>Phone</th><th>Joined</th><th>Status</th><th>Action</th></tr></thead><tbody>${rows || '<tr><td colspan="7" style="text-align:center;color:var(--muted)">No users found</td></tr>'}</tbody></table>`;
      }

      if (name === 'bookings') {
        const rows = (d.bookings || []).map(b => `
          <tr>
            <td><strong>${b.user_name}</strong><br><small style="color:var(--muted)">${b.user_mobile}</small></td>
            <td>${b.pickup_location} → ${b.drop_location}</td>
            <td>${b.vehicle_category === 'travel' ? '🚌' : '🚚'} ${b.driver}</td>
            <td>${b.distance_km} km</td>
            <td>₹${parseFloat(b.total_cost).toLocaleString('en-IN')}</td>
            <td>${fmtDate(b.date)}</td>
            <td>${pill(b.status.toLowerCase(), b.status)}</td>
          </tr>`).join('');
        container.innerHTML = `<table class="adm-table"><thead><tr><th>Customer</th><th>Route</th><th>Driver</th><th>Distance</th><th>Cost</th><th>Date</th><th>Status</th></tr></thead><tbody>${rows || '<tr><td colspan="7" style="text-align:center;color:var(--muted)">No bookings found</td></tr>'}</tbody></table>`;
      }

      if (name === 'vehicles') {
        const rows = (d.vehicles || []).map(v => `
          <tr>
            <td><strong>${v.owner_name}</strong></td>
            <td>${v.vehicle_category === 'travel' ? '🚌 Travel' : '🚚 Transport'}</td>
            <td>${v.capacity} ${v.vehicle_category==='travel'?'seats':'tons'}</td>
            <td>₹${v.rate_per_km}/km</td>
            <td>${v.address}</td>
            <td>${pill(v.is_available?'active':'inactive', v.is_available?'Available':'Unavailable')}</td>
            <td>${fmtDate(v.created_at)}</td>
          </tr>`).join('');
        container.innerHTML = `<table class="adm-table"><thead><tr><th>Owner</th><th>Type</th><th>Capacity</th><th>Rate</th><th>Location</th><th>Status</th><th>Added</th></tr></thead><tbody>${rows || '<tr><td colspan="7" style="text-align:center;color:var(--muted)">No vehicles found</td></tr>'}</tbody></table>`;
      }

      if (name === 'messages') {
        const msgs = d.messages || [];
        if (!msgs.length) { container.innerHTML = '<p style="text-align:center;color:var(--muted);padding:24px">No messages yet.</p>'; return; }
        container.innerHTML = msgs.map(m => `
          <div class="msg-row" id="msg-${m.id}">
            <div>
              <div class="msg-name">${m.name} <small style="color:var(--muted);font-weight:400">&lt;${m.email}&gt;</small> ${m.status==='unread'?'<span class="pill pill-pending" style="font-size:0.65rem">Unread</span>':''}</div>
              <div class="msg-text">${m.message.substring(0,180)}${m.message.length>180?'…':''}</div>
              <div class="msg-meta">${m.service ? '📌 '+m.service+' · ' : ''}${fmtDate(m.created_at)}</div>
            </div>
            ${m.status==='unread'?`<button class="mark-read-btn" onclick="markRead(${m.id},this)">Mark Read</button>`:'<span style="font-size:0.72rem;color:var(--muted)">Read</span>'}
          </div>`).join('');
      }
    }).catch(() => { loading.innerHTML = '<p style="color:#c0392b">Failed to load data.</p>'; });
}

function animNum(id, target) {
  const el = document.getElementById(id);
  if (!el) return;
  let cur = 0, step = Math.ceil(target / 25);
  const t = setInterval(() => { cur = Math.min(cur + step, target); el.textContent = cur; if (cur >= target) clearInterval(t); }, 40);
}

function toggleUser(uid, btn) {
  btn.disabled = true;
  fetch(BASE + '/admin/api_admin.php?action=toggle_user', {
    method:'POST', body: new URLSearchParams({user_id: uid})
  }).then(r => r.json()).then(d => {
    if (d.success) {
      const isActive = d.is_active;
      btn.textContent = isActive ? 'Deactivate' : 'Activate';
      btn.className = 'toggle-user-btn ' + (isActive ? 'deactivate' : 'activate');
      const row = btn.closest('tr');
      const pillCell = row.querySelectorAll('td')[5];
      pillCell.innerHTML = `<span class="pill pill-${isActive?'active':'inactive'}">${isActive?'Active':'Inactive'}</span>`;
      showToast(isActive ? 'User activated.' : 'User deactivated.', 'info');
    }
    btn.disabled = false;
  }).catch(() => { btn.disabled = false; });
}

function markRead(mid, btn) {
  fetch(BASE + '/admin/api_admin.php?action=mark_msg_read', {
    method:'POST', body: new URLSearchParams({msg_id: mid})
  }).then(r => r.json()).then(d => {
    if (d.success) {
      const row = document.getElementById('msg-' + mid);
      if (row) { row.querySelector('.pill-pending')?.remove(); btn.replaceWith(Object.assign(document.createElement('span'),{style:'font-size:0.72rem;color:var(--muted)',textContent:'Read'})); }
    }
  });
}

// Init
loadOverview();
loadTab('users');
setInterval(loadOverview, 30000);
</script>
</body>
</html>
