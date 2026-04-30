<?php
session_start();
if (empty($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/login.php'); exit; }
require_once '../includes/db.php';
$navActive = '';
include '../includes/navbar.php';
$user = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Dashboard — Drifter</title>
<style>
.dash-hero{padding:52px 24px 88px;position:relative;overflow:hidden;background:var(--text,#2a2520);}
.dash-hero::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(44,36,32,0.96) 0%,rgba(201,181,156,0.30) 100%);}
.dash-hero-inner{max-width:1280px;margin:0 auto;display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:20px;position:relative;z-index:1;}
.dash-greeting h1{font-size:1.85rem;font-weight:800;color:white;margin-bottom:6px;}
.dash-greeting p{color:rgba(255,255,255,0.55);font-size:0.90rem;}
.live-dot{display:inline-flex;align-items:center;gap:6px;background:rgba(188,159,139,0.18);border:1px solid rgba(188,159,139,0.35);color:#CADABF;padding:5px 14px;border-radius:50px;font-size:0.78rem;font-weight:600;}
.live-dot::before{content:'';width:7px;height:7px;border-radius:50%;background:#BC9F8B;animation:pd 1.5s infinite;}
@keyframes pd{0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.4;transform:scale(1.3);}}

.stats-row{max-width:1280px;margin:-44px auto 0;padding:0 24px;display:grid;grid-template-columns:repeat(4,1fr);gap:16px;}
.stat-card{background:white;border-radius:16px;padding:20px 22px;box-shadow:var(--shadow);transition:transform 0.25s,box-shadow 0.25s;animation:slideUp 0.5s ease both;border-top:3px solid var(--linen,#E7E8D8);}
.stat-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-lg);}
.stat-card:nth-child(1){animation-delay:0.05s;border-top-color:#BC9F8B;}
.stat-card:nth-child(2){animation-delay:0.10s;border-top-color:#CADABF;}
.stat-card:nth-child(3){animation-delay:0.15s;border-top-color:#B5CFB7;}
.stat-card:nth-child(4){animation-delay:0.20s;border-top-color:#BC9F8B;}
@keyframes slideUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.stat-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:10px;background:var(--linen,#E7E8D8);}
.stat-num{font-size:1.75rem;font-weight:800;color:var(--text);line-height:1;}
.stat-lbl{font-size:0.74rem;color:var(--muted);margin-top:4px;font-weight:500;}

.dash-body{max-width:1280px;margin:32px auto 80px;padding:0 24px;}
.tab-bar{display:flex;gap:4px;background:white;border-radius:14px;padding:6px;box-shadow:var(--shadow);margin-bottom:26px;flex-wrap:wrap;}
.tab-btn{flex:1;min-width:110px;padding:10px 14px;border:none;border-radius:10px;font-size:0.84rem;font-weight:600;cursor:pointer;transition:all 0.25s;background:transparent;color:var(--muted);display:flex;align-items:center;justify-content:center;gap:7px;font-family:inherit;}
.tab-btn.active{background:var(--text);color:white;box-shadow:0 4px 12px rgba(44,36,32,0.25);}
.tab-btn .badge{background:#BC9F8B;color:white;border-radius:50px;padding:1px 7px;font-size:0.68rem;font-weight:700;}
.tab-btn.active .badge{background:rgba(255,255,255,0.25);}
.tab-panel{display:none;animation:fadeIn 0.3s ease;}
.tab-panel.active{display:block;}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:translateY(0);}}

.bookings-list{display:flex;flex-direction:column;gap:14px;}
.bk-card{background:white;border-radius:14px;padding:20px 22px;box-shadow:var(--shadow);display:grid;grid-template-columns:auto 1fr auto auto;gap:16px;align-items:center;transition:all 0.25s;border-left:4px solid var(--linen,#E7E8D8);animation:slideUp 0.4s ease both;}
.bk-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-lg);}
.bk-card.pending{border-left-color:#BC9F8B;}
.bk-card.confirmed{border-left-color:#B5CFB7;}
.bk-card.cancelled{border-left-color:#CADABF;opacity:0.72;}
.bk-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.35rem;flex-shrink:0;background:var(--linen,#E7E8D8);}
.bk-route{font-size:0.93rem;font-weight:700;color:var(--text);margin-bottom:4px;}
.bk-route span{color:var(--muted);font-weight:400;font-size:0.84rem;}
.bk-meta{display:flex;gap:12px;flex-wrap:wrap;}
.bk-meta-item{font-size:0.76rem;color:var(--muted);display:flex;align-items:center;gap:4px;}
.bk-meta-item i{color:#BC9F8B;}
.bk-cost .amount{font-size:1.15rem;font-weight:800;color:var(--text);}
.bk-cost .km{font-size:0.73rem;color:var(--muted);}
.status-pill{padding:4px 12px;border-radius:50px;font-size:0.73rem;font-weight:700;white-space:nowrap;}
.status-pill.pending{background:rgba(188,159,139,0.18);color:#7a5a40;}
.status-pill.confirmed{background:rgba(181,207,183,0.25);color:#3a6b3c;}
.status-pill.cancelled{background:rgba(231,232,216,0.80);color:var(--muted,#7a7060);}

.quick-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px;margin-bottom:30px;}
.quick-card{background:white;border-radius:14px;padding:20px;text-align:center;box-shadow:var(--shadow);text-decoration:none;color:var(--text);transition:all 0.25s;border:2px solid transparent;animation:slideUp 0.4s ease both;}
.quick-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);border-color:#BC9F8B;}
.quick-card .qi{width:50px;height:50px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin:0 auto 10px;background:var(--linen,#E7E8D8);}
.quick-card h4{font-size:0.90rem;font-weight:700;margin-bottom:3px;}
.quick-card p{font-size:0.76rem;color:var(--muted);}

.empty-box{text-align:center;padding:56px 24px;background:white;border-radius:14px;box-shadow:var(--shadow);}
.empty-box .ei{font-size:3rem;margin-bottom:12px;}
.empty-box h3{font-size:1.15rem;font-weight:700;margin-bottom:7px;}
.empty-box p{color:var(--muted);font-size:0.86rem;margin-bottom:18px;}
.empty-box a{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border-radius:10px;text-decoration:none;font-weight:600;font-size:0.86rem;transition:all 0.25s;}
.empty-box a:hover{transform:translateY(-2px);}

.cancel-btn{padding:4px 12px;border-radius:50px;font-size:0.70rem;font-weight:700;border:none;cursor:pointer;background:rgba(192,57,43,0.10);color:#7a1a0a;transition:all 0.2s;white-space:nowrap;}
.cancel-btn:hover{background:rgba(192,57,43,0.18);}
.cancel-btn:disabled{opacity:0.5;cursor:not-allowed;}
.last-updated{font-size:0.73rem;color:var(--muted);display:flex;align-items:center;gap:5px;margin-bottom:14px;}
#toast{position:fixed;bottom:28px;right:28px;z-index:99999;padding:13px 20px;border-radius:12px;font-weight:600;font-size:0.87rem;box-shadow:var(--shadow-lg);transform:translateY(80px);opacity:0;transition:all 0.35s cubic-bezier(0.34,1.56,0.64,1);max-width:320px;display:flex;align-items:center;gap:10px;}
#toast.show{transform:translateY(0);opacity:1;}
#toast.success{background:#B5CFB7;color:#1a3a1c;}
#toast.error{background:#c0392b;color:white;}
#toast.info{background:var(--text);color:white;}
@media(max-width:900px){.stats-row{grid-template-columns:repeat(2,1fr);}.bk-card{grid-template-columns:auto 1fr;}.bk-cost,.bk-card .status-pill{grid-column:span 2;}}
@media(max-width:600px){.stats-row{grid-template-columns:1fr 1fr;}.quick-grid{grid-template-columns:1fr 1fr;}}
</style>
</head>
<body>
<div class="dash-hero">
  <div class="dash-hero-inner">
    <div class="dash-greeting"><h1>👋 Welcome back, <?= $user ?>!</h1><p>Here's everything happening with your bookings.</p></div>
    <div class="live-dot">Live Updates</div>
  </div>
</div>

<div class="stats-row">
  <div class="stat-card"><div class="stat-icon">📋</div><div class="stat-num" id="s-total">—</div><div class="stat-lbl">Total Bookings</div></div>
  <div class="stat-card"><div class="stat-icon">⏳</div><div class="stat-num" id="s-active">—</div><div class="stat-lbl">Pending</div></div>
  <div class="stat-card"><div class="stat-icon">✅</div><div class="stat-num" id="s-confirmed">—</div><div class="stat-lbl">Confirmed</div></div>
  <div class="stat-card"><div class="stat-icon">💰</div><div class="stat-num" id="s-spent">—</div><div class="stat-lbl">Total Spent</div></div>
</div>

<div class="dash-body">
  <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:14px;color:var(--text)">Quick Actions</h3>
  <div class="quick-grid">
    <a href="<?= BASE ?>/transport/booking_step1.php" class="quick-card"><div class="qi">🚚</div><h4>Book Transport</h4><p>Move goods anywhere</p></a>
    <a href="<?= BASE ?>/travel/booking_step1.php" class="quick-card"><div class="qi">🚌</div><h4>Book Travel</h4><p>Comfortable rides</p></a>
    <a href="<?= BASE ?>/courier/courier.php" class="quick-card"><div class="qi">📦</div><h4>Send Courier</h4><p>Fast delivery</p></a>
    <a href="<?= BASE ?>/move/movers.php" class="quick-card"><div class="qi">🏠</div><h4>Packers &amp; Movers</h4><p>Stress-free relocation</p></a>
  </div>

  <div class="tab-bar">
    <button class="tab-btn active" onclick="switchTab('all',this)"><i class="fas fa-list"></i> All <span class="badge" id="tb-all">0</span></button>
    <button class="tab-btn" onclick="switchTab('active',this)"><i class="fas fa-clock"></i> Active <span class="badge" id="tb-active">0</span></button>
    <button class="tab-btn" onclick="switchTab('past',this)"><i class="fas fa-history"></i> Past <span class="badge" id="tb-past">0</span></button>
  </div>
  <div class="last-updated"><i class="fas fa-sync-alt"></i> Last updated: <span id="lastUpdated">—</span></div>
  <div id="tab-all" class="tab-panel active"><div id="list-all"></div></div>
  <div id="tab-active" class="tab-panel"><div id="list-active"></div></div>
  <div id="tab-past" class="tab-panel"><div id="list-past"></div></div>
</div>
<div id="toast"></div>
<?php include '../includes/footer.php'; ?>
<script>
function switchTab(n,btn){document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));document.querySelectorAll('.tab-panel').forEach(p=>p.classList.remove('active'));btn.classList.add('active');document.getElementById('tab-'+n).classList.add('active');}
function pill(s){const m={'pending':'pending','confirmed':'confirmed','cancelled':'cancelled'};return`<span class="status-pill ${(m[s.toLowerCase()]||'')}">${s}</span>`;}
function fmtDate(d){if(!d)return'—';return new Date(d).toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'});}
function renderBk(b,i){
  const icon=b.vehicle_category==='travel'?'🚌':'🚚';
  const delay=(i*0.06).toFixed(2);
  const cb=b.status==='Pending'?`<button class="cancel-btn" onclick="cancelBk(${b.id},this)"><i class="fas fa-times"></i> Cancel</button>`:'';
  return`<div class="bk-card ${b.status.toLowerCase()}" style="animation-delay:${delay}s" id="bk-${b.id}">
    <div class="bk-icon">${icon}</div>
    <div>
      <div class="bk-route">${b.pickup_location} <span>→</span> ${b.drop_location}</div>
      <div class="bk-meta">
        <span class="bk-meta-item"><i class="fas fa-calendar"></i> ${fmtDate(b.date)}</span>
        <span class="bk-meta-item"><i class="fas fa-clock"></i> ${b.time?b.time.slice(0,5):'—'}</span>
        <span class="bk-meta-item"><i class="fas fa-road"></i> ${b.distance_km} km</span>
        <span class="bk-meta-item"><i class="fas fa-user"></i> ${b.driver_name||'—'}</span>
        <span class="bk-meta-item"><i class="fas fa-phone"></i> ${b.driver_mobile||'—'}</span>
      </div>
    </div>
    <div class="bk-cost"><div class="amount">₹${parseFloat(b.total_cost).toLocaleString('en-IN',{minimumFractionDigits:2})}</div><div class="km">${b.distance_km}km × ₹${b.rate_per_km}/km</div></div>
    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">${pill(b.status)}${cb}</div>
  </div>`;
}
function emptyBox(msg,link,lt){return`<div class="empty-box"><div class="ei">🔍</div><h3>No bookings here</h3><p>${msg}</p><a href="${link}">${lt}</a></div>`;}
function renderList(id,items,em,el,elt){const e=document.getElementById(id);if(!items||!items.length){e.innerHTML=emptyBox(em,el,elt);return;}e.innerHTML=`<div class="bookings-list">${items.map((b,i)=>renderBk(b,i)).join('')}</div>`;}
function animNum(id,target){const el=document.getElementById(id);const start=parseInt(el.textContent)||0;if(start===target)return;let step=0;const t=setInterval(()=>{step++;el.textContent=Math.round(start+(target-start)*(step/20));if(step>=20)clearInterval(t);},30);}
function showToast(msg,type='info'){const t=document.getElementById('toast');t.innerHTML=`<i class="fas fa-${type==='success'?'check-circle':type==='error'?'times-circle':'info-circle'}"></i> ${msg}`;t.className='show '+type;setTimeout(()=>t.className='',4000);}
let prevCount=-1;
function fetchData(){
  fetch('<?= BASE ?>/front/api_my_rides.php').then(r=>r.json()).then(d=>{
    if(d.error)return;
    animNum('s-total',d.count);animNum('s-active',d.pending||0);animNum('s-confirmed',d.confirmed||0);
    document.getElementById('s-spent').textContent='₹'+parseFloat(d.total_spent||0).toLocaleString('en-IN',{minimumFractionDigits:0});
    document.getElementById('tb-all').textContent=d.count;
    document.getElementById('tb-active').textContent=d.active.length;
    document.getElementById('tb-past').textContent=d.past.length;
    document.getElementById('lastUpdated').textContent=d.timestamp;
    renderList('list-all',d.all,'No bookings yet.','<?= BASE ?>/front/index.php#services','🚀 Book a Service');
    renderList('list-active',d.active,'No active bookings.','<?= BASE ?>/transport/booking_step1.php','🚚 Book Transport');
    renderList('list-past',d.past,'No past rides yet.','<?= BASE ?>/travel/booking_step1.php','🚌 Book Travel');
    if(prevCount!==-1&&d.count>prevCount)showToast('New booking update!','success');
    prevCount=d.count;
  }).catch(()=>{});
}
fetchData();setInterval(fetchData,8000);
function cancelBk(id,btn){
  if(!confirm('Cancel this booking?'))return;
  btn.disabled=true;btn.textContent='Cancelling...';
  fetch('<?= BASE ?>/front/api_cancel_booking.php',{method:'POST',body:new URLSearchParams({booking_id:id})})
    .then(r=>r.json()).then(d=>{
      if(d.success){showToast('Booking cancelled.','info');fetchData();}
      else{showToast(d.msg||'Could not cancel.','error');btn.disabled=false;btn.textContent='Cancel';}
    }).catch(()=>{btn.disabled=false;btn.textContent='Cancel';});
}
</script>
</body></html>
