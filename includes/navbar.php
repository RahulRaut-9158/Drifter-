<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';
$_u  = $_SESSION['username'] ?? '';
$_r  = $_SESSION['role']     ?? '';
$_li = !empty($_SESSION['loggedin']);
$_nav = $navActive ?? '';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════
   DRIFTER — DESIGN SYSTEM — PALETTE 2
   Deep Navy & Orange (Dynamic Energy)
   ═══════════════════════════════════════════ */
:root {
  --primary-dark:   #0a1628;
  --primary:        #0f2b5e;
  --primary-light:  #1e4a8a;
  --accent:         #f97316;
  --accent-dark:    #ea580c;
  --accent-light:   #fb923c;
  --text-dark:      #0f172a;
  --text-light:     #64748b;
  --text-white:     #ffffff;
  --bg-light:       #f1f5f9;
  --bg-white:       #ffffff;
  --success:        #22c55e;
  --error:          #ef4444;
  --warning:        #f59e0b;
  --shadow-sm:      0 1px 3px rgba(0,0,0,0.05);
  --shadow-md:      0 4px 12px rgba(0,0,0,0.08);
  --shadow-lg:      0 10px 25px rgba(0,0,0,0.12);
  --gradient-primary: linear-gradient(135deg,#0f2b5e 0%,#1e4a8a 100%);
  --gradient-accent:  linear-gradient(135deg,#f97316 0%,#fb923c 100%);
  --radius:         14px;
  --radius-sm:      8px;
  --trans:          all 0.26s cubic-bezier(0.4,0,0.2,1);
  --border:         #e2e8f0;
}

/* ── RESET ── */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{font-family:'Inter',sans-serif;background:var(--bg-light);color:var(--text-dark);line-height:1.6;-webkit-font-smoothing:antialiased;}
a{text-decoration:none;color:inherit;}
img{max-width:100%;}
input,select,textarea,button{font-family:inherit;}

/* ── SCROLLBAR ── */
::-webkit-scrollbar{width:6px;}
::-webkit-scrollbar-track{background:var(--bg-light);}
::-webkit-scrollbar-thumb{background:var(--accent);border-radius:3px;}

/* ── GLOBAL BUTTONS ── */
.btn-primary{
  display:inline-flex;align-items:center;gap:8px;
  padding:11px 24px;border-radius:var(--radius-sm);
  background:var(--gradient-accent);
  color:white;font-weight:700;font-size:0.9rem;
  border:none;cursor:pointer;
  box-shadow:0 3px 12px rgba(249,115,22,0.35);
  transition:var(--trans);
}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(249,115,22,0.50);filter:brightness(1.08);}
.btn-primary:active{transform:translateY(0);}

.btn-secondary{
  display:inline-flex;align-items:center;gap:8px;
  padding:11px 24px;border-radius:var(--radius-sm);
  background:var(--gradient-primary);
  color:white;font-weight:700;font-size:0.9rem;
  border:none;cursor:pointer;
  box-shadow:0 3px 12px rgba(15,43,94,0.35);
  transition:var(--trans);
}
.btn-secondary:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(15,43,94,0.50);filter:brightness(1.08);}

.btn-outline{
  display:inline-flex;align-items:center;gap:8px;
  padding:10px 22px;border-radius:var(--radius-sm);
  background:transparent;color:var(--accent);
  border:2px solid var(--accent);font-weight:600;font-size:0.9rem;
  cursor:pointer;transition:var(--trans);
}
.btn-outline:hover{background:var(--accent);color:white;transform:translateY(-2px);}

/* ── CARD ── */
.card{
  background:var(--bg-white);border-radius:var(--radius);
  box-shadow:var(--shadow-md);border:1px solid var(--border);
  transition:var(--trans);
}
.card:hover{box-shadow:var(--shadow-lg);transform:translateY(-2px);}

/* ── FORM INPUTS ── */
.form-input{
  width:100%;padding:11px 14px;
  border:1.5px solid var(--border);border-radius:var(--radius-sm);
  background:#f8fafc;color:var(--text-dark);font-size:0.92rem;
  transition:var(--trans);
}
.form-input:focus{
  border-color:var(--accent);background:var(--bg-white);
  box-shadow:0 0 0 3px rgba(249,115,22,0.15);outline:none;
}
.form-input::placeholder{color:var(--text-light);}

/* ── STATUS PILLS ── */
.pill{display:inline-flex;align-items:center;gap:5px;padding:3px 11px;border-radius:50px;font-size:0.72rem;font-weight:700;}
.pill-pending  {background:rgba(245,158,11,0.15);color:#92400e;}
.pill-confirmed{background:rgba(34,197,94,0.15);color:#14532d;}
.pill-cancelled{background:rgba(239,68,68,0.12);color:#991b1b;}
.pill-assigned {background:rgba(15,43,94,0.12);color:#0f2b5e;}
.pill-delivered{background:rgba(34,197,94,0.18);color:#14532d;}

/* ── REVEAL ANIMATION ── */
.reveal{opacity:0;transform:translateY(28px);transition:opacity 0.6s ease,transform 0.6s ease;}
.reveal.visible{opacity:1;transform:translateY(0);}
.reveal-delay-1{transition-delay:0.1s;}
.reveal-delay-2{transition-delay:0.2s;}
.reveal-delay-3{transition-delay:0.3s;}
.reveal-delay-4{transition-delay:0.4s;}

/* ── PAGE HERO ── */
.page-hero{
  padding:80px 24px 60px;text-align:center;color:white;
  position:relative;overflow:hidden;
  background:var(--gradient-primary);
}
.page-hero::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 70% 50%,rgba(249,115,22,0.25) 0%,transparent 60%);
}
.page-hero::after{
  content:'';position:absolute;bottom:-2px;left:0;right:0;height:60px;
  background:var(--bg-light);
  clip-path:ellipse(55% 100% at 50% 100%);
}
.page-hero>*{position:relative;z-index:1;}
.page-hero h1{font-size:clamp(1.8rem,4vw,2.6rem);font-weight:800;margin-bottom:10px;}
.page-hero p{color:rgba(255,255,255,0.70);font-size:1rem;}

/* ── SECTION HELPERS ── */
.section{padding:80px 24px;}
.section-inner{max-width:1280px;margin:0 auto;}
.section-head{text-align:center;margin-bottom:56px;}
.tag{
  display:inline-block;padding:4px 14px;border-radius:50px;
  background:rgba(249,115,22,0.10);color:var(--accent);
  font-size:0.72rem;font-weight:700;letter-spacing:1.8px;
  text-transform:uppercase;margin-bottom:10px;
  border:1px solid rgba(249,115,22,0.20);
}
.section-head h2{font-size:clamp(1.7rem,4vw,2.3rem);font-weight:800;color:var(--text-dark);margin-bottom:8px;}
.section-head p{color:var(--text-light);font-size:0.93rem;max-width:480px;margin:0 auto;}

/* ── TOAST ── */
#g-toast{
  position:fixed;bottom:28px;right:28px;z-index:99999;
  padding:13px 20px;border-radius:12px;font-weight:600;font-size:0.87rem;
  box-shadow:var(--shadow-lg);transform:translateY(80px);opacity:0;
  transition:all 0.35s cubic-bezier(0.34,1.56,0.64,1);
  max-width:320px;display:flex;align-items:center;gap:10px;
  pointer-events:none;
}
#g-toast.show{transform:translateY(0);opacity:1;pointer-events:auto;}
#g-toast.success{background:linear-gradient(135deg,#22c55e,#16a34a);color:white;}
#g-toast.error  {background:linear-gradient(135deg,#ef4444,#dc2626);color:white;}
#g-toast.info   {background:var(--gradient-accent);color:white;}

/* ══════════════════════════════════════
   NAVBAR
   ══════════════════════════════════════ */
.drifter-nav{
  background:rgba(10,22,40,0.97);
  backdrop-filter:blur(16px);
  border-bottom:1px solid rgba(249,115,22,0.15);
  position:sticky;top:0;z-index:9999;
  box-shadow:0 2px 20px rgba(0,0,0,0.25);
  transition:background 0.3s;
}
.nav-inner{
  max-width:1280px;margin:0 auto;
  display:flex;align-items:center;justify-content:space-between;
  padding:0 24px;height:68px;
}

/* Logo */
.nav-logo{display:flex;align-items:center;gap:10px;transition:opacity 0.2s;}
.nav-logo:hover{opacity:0.9;}
.logo-icon{
  width:40px;height:40px;border-radius:10px;
  background:var(--gradient-accent);
  display:flex;align-items:center;justify-content:center;
  font-size:1.1rem;font-weight:800;color:white;
  transition:transform 0.28s cubic-bezier(0.34,1.56,0.64,1),box-shadow 0.28s;
  box-shadow:0 3px 12px rgba(249,115,22,0.40);
}
.nav-logo:hover .logo-icon{transform:rotate(-8deg) scale(1.1);box-shadow:0 6px 20px rgba(249,115,22,0.60);}
.logo-text{font-size:1.35rem;font-weight:800;color:white;letter-spacing:1.5px;}
.logo-sub{font-size:0.58rem;color:rgba(255,255,255,0.45);letter-spacing:2px;text-transform:uppercase;display:block;margin-top:-3px;}

/* Links */
.nav-links{display:flex;align-items:center;gap:2px;list-style:none;}
.nav-links a,.nav-links .drop-toggle{
  color:rgba(255,255,255,0.65);font-size:0.86rem;font-weight:500;
  padding:7px 12px;border-radius:8px;
  display:flex;align-items:center;gap:6px;
  transition:background 0.2s,color 0.2s,transform 0.2s;
  cursor:pointer;white-space:nowrap;
}
.nav-links a:hover,.nav-links .drop-toggle:hover{
  background:rgba(249,115,22,0.12);color:var(--accent-light);transform:translateY(-1px);
}
.nav-links a.active{background:rgba(249,115,22,0.15);color:var(--accent);font-weight:600;}

/* Dropdown */
.nav-drop{position:relative;}
.drop-chevron{transition:transform 0.28s ease;display:inline-block;font-size:0.65rem !important;}
.nav-drop.drop-open .drop-chevron{transform:rotate(180deg);}
.drop-menu{
  visibility:hidden;opacity:0;pointer-events:none;
  position:absolute;top:calc(100% + 10px);left:0;
  background:#0f1e3a;border-radius:14px;
  min-width:230px;padding:8px;
  box-shadow:0 16px 40px rgba(0,0,0,0.35);
  border:1px solid rgba(249,115,22,0.15);
  z-index:10000;transform:translateY(-8px);
  transition:opacity 0.26s ease,transform 0.26s ease,visibility 0s linear 0.26s;
}
.nav-drop.drop-open .drop-menu{
  visibility:visible;opacity:1;pointer-events:auto;transform:translateY(0);
  transition:opacity 0.26s ease,transform 0.26s ease,visibility 0s linear 0s;
}
.drop-menu a{
  display:flex;align-items:center;gap:10px;
  padding:9px 13px;border-radius:8px;
  color:rgba(255,255,255,0.65);font-size:0.84rem;
  transition:background 0.18s,color 0.18s,padding-left 0.18s;
}
.drop-menu a:hover{background:rgba(249,115,22,0.12);color:var(--accent-light);padding-left:17px;}
.drop-menu a i{width:17px;text-align:center;color:var(--accent);flex-shrink:0;}
.drop-divider{height:1px;background:rgba(255,255,255,0.08);margin:5px 0;}

/* User badge */
.user-badge{
  display:flex;align-items:center;gap:8px;
  background:rgba(249,115,22,0.10);
  border:1px solid rgba(249,115,22,0.25);
  border-radius:50px;padding:4px 13px 4px 4px;
  cursor:pointer;transition:background 0.2s,transform 0.2s;
}
.user-badge:hover{background:rgba(249,115,22,0.18);transform:translateY(-1px);}
.user-avatar{
  width:28px;height:28px;border-radius:50%;
  background:var(--gradient-accent);
  display:flex;align-items:center;justify-content:center;
  font-weight:700;font-size:0.78rem;color:white;
  transition:transform 0.2s;
}
.user-badge:hover .user-avatar{transform:scale(1.1);}
.user-name{color:rgba(255,255,255,0.85);font-size:0.82rem;font-weight:600;}

/* CTA button */
.nav-cta{
  background:var(--gradient-accent) !important;
  color:white !important;border-radius:8px;
  padding:8px 18px !important;font-weight:700 !important;
  box-shadow:0 2px 10px rgba(249,115,22,0.40);
  transition:transform 0.2s,box-shadow 0.2s,filter 0.2s !important;
}
.nav-cta:hover{transform:translateY(-2px) !important;box-shadow:0 6px 20px rgba(249,115,22,0.60) !important;filter:brightness(1.08);}

/* Hamburger */
.nav-hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:8px;}
.nav-hamburger span{width:22px;height:2px;background:rgba(255,255,255,0.80);border-radius:2px;transition:var(--trans);}
.nav-hamburger.open span:nth-child(1){transform:translateY(7px) rotate(45deg);}
.nav-hamburger.open span:nth-child(2){opacity:0;transform:scaleX(0);}
.nav-hamburger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg);}

/* Mobile */
@media(max-width:900px){
  .nav-links{
    display:none;flex-direction:column;
    position:absolute;top:68px;left:0;right:0;
    background:#0a1628;padding:14px;gap:3px;
    border-bottom:1px solid rgba(249,115,22,0.15);
    box-shadow:0 8px 24px rgba(0,0,0,0.30);
  }
  .nav-links.open{display:flex;}
  .nav-hamburger{display:flex;}
  .drop-menu{
    position:static;box-shadow:none;border:none;
    background:rgba(249,115,22,0.06);margin-top:3px;
    visibility:visible;opacity:1;pointer-events:auto;
    transform:none;transition:none;display:none;
  }
  .nav-drop.drop-open .drop-menu{display:block;}
}
</style>

<nav class="drifter-nav">
  <div class="nav-inner">
    <a href="<?= BASE ?>/index.php" class="nav-logo">
      <div class="logo-icon">D</div>
      <div>
        <span class="logo-text">DRIFTER</span>
        <span class="logo-sub">Transport Services</span>
      </div>
    </a>

    <ul class="nav-links" id="navLinks">
      <li><a href="<?= BASE ?>/index.php" class="<?= $_nav==='home'?'active':'' ?>"><i class="fas fa-home"></i> Home</a></li>

      <?php if (!$_li || $_r === 'customer'): ?>
      <li class="nav-drop">
        <span class="drop-toggle"><i class="fas fa-th-large"></i> Services <i class="fas fa-chevron-down drop-chevron"></i></span>
        <div class="drop-menu">
          <a href="<?= BASE ?>/transport/booking_step1.php"><i class="fas fa-truck-moving"></i> Transport Goods</a>
          <a href="<?= BASE ?>/travel/booking_step1.php"><i class="fas fa-bus"></i> Travel / Ride</a>
          <a href="<?= BASE ?>/courier/courier.php"><i class="fas fa-box"></i> Courier</a>
          <a href="<?= BASE ?>/move/movers.php"><i class="fas fa-people-carry"></i> Packers &amp; Movers</a>
        </div>
      </li>
      <?php endif; ?>

      <?php if ($_li && $_r === 'owner'): ?>
      <li class="nav-drop">
        <span class="drop-toggle"><i class="fas fa-truck"></i> My Vehicles <i class="fas fa-chevron-down drop-chevron"></i></span>
        <div class="drop-menu">
          <a href="<?= BASE ?>/front/your_vehicle_info.php"><i class="fas fa-truck-moving"></i> Transport Vehicles</a>
          <a href="<?= BASE ?>/front/your_vehicle_travel.php"><i class="fas fa-bus"></i> Travel Vehicles</a>
          <div class="drop-divider"></div>
          <a href="<?= BASE ?>/transport/add_vehicle.php"><i class="fas fa-plus"></i> Add Transport Vehicle</a>
          <a href="<?= BASE ?>/travel/add_vehicle.php"><i class="fas fa-plus"></i> Add Travel Vehicle</a>
        </div>
      </li>
      <?php endif; ?>

      <?php if ($_li && $_r === 'company'): ?>
      <li class="nav-drop">
        <span class="drop-toggle"><i class="fas fa-building"></i> My Companies <i class="fas fa-chevron-down drop-chevron"></i></span>
        <div class="drop-menu">
          <a href="<?= BASE ?>/courier/company_info.php"><i class="fas fa-box"></i> Courier Companies</a>
          <a href="<?= BASE ?>/move/company_info.php"><i class="fas fa-warehouse"></i> Movers Companies</a>
          <div class="drop-divider"></div>
          <a href="<?= BASE ?>/courier/add_company.php"><i class="fas fa-plus"></i> Add Courier Co.</a>
          <a href="<?= BASE ?>/move/add_company.php"><i class="fas fa-plus"></i> Add Movers Co.</a>
        </div>
      </li>
      <?php endif; ?>

      <li><a href="<?= BASE ?>/about.php" class="<?= $_nav==='about'?'active':'' ?>"><i class="fas fa-info-circle"></i> About</a></li>
      <li><a href="<?= BASE ?>/support.php" class="<?= $_nav==='support'?'active':'' ?>"><i class="fas fa-headset"></i> Support</a></li>

      <?php if (!$_li): ?>
        <li><a href="<?= BASE ?>/login.php" class="nav-cta"><i class="fas fa-sign-in-alt"></i> Login</a></li>
      <?php else: ?>
        <li class="nav-drop">
          <div class="user-badge drop-toggle">
            <div class="user-avatar"><?= strtoupper(substr($_u,0,1)) ?></div>
            <span class="user-name"><?= htmlspecialchars($_u) ?></span>
            <i class="fas fa-chevron-down drop-chevron" style="color:rgba(255,255,255,0.45);font-size:0.65rem;margin-left:2px;"></i>
          </div>
          <div class="drop-menu" style="right:0;left:auto;">
            <?php if ($_r==='customer'): ?>
              <a href="<?= BASE ?>/dashboard_customer.php"><i class="fas fa-tachometer-alt"></i> My Dashboard</a>
            <?php elseif ($_r==='owner'): ?>
              <a href="<?= BASE ?>/dashboard_owner.php"><i class="fas fa-truck"></i> My Dashboard</a>
            <?php elseif ($_r==='company'): ?>
              <a href="<?= BASE ?>/courier/company_info.php"><i class="fas fa-building"></i> My Companies</a>
            <?php elseif ($_r==='admin'): ?>
              <a href="<?= BASE ?>/admin/index.php"><i class="fas fa-shield-alt"></i> Admin Panel</a>
            <?php endif; ?>
            <div class="drop-divider"></div>
            <a href="<?= BASE ?>/logout.php" style="color:#f87171 !important"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </li>
      <?php endif; ?>
    </ul>

    <div class="nav-hamburger" id="hamburger"><span></span><span></span><span></span></div>
  </div>
</nav>

<!-- Global toast -->
<div id="g-toast"></div>

<script>
(function(){
  // Hamburger
  const hb = document.getElementById('hamburger');
  const nl = document.getElementById('navLinks');
  hb.addEventListener('click', () => { nl.classList.toggle('open'); hb.classList.toggle('open'); });

  // Desktop hover with delay
  const DELAY = 400;
  document.querySelectorAll('.nav-drop').forEach(drop => {
    let t = null;
    const open  = () => { clearTimeout(t); drop.classList.add('drop-open'); };
    const close = () => { t = setTimeout(() => drop.classList.remove('drop-open'), DELAY); };
    drop.addEventListener('mouseenter', open);
    drop.addEventListener('mouseleave', close);
    const m = drop.querySelector('.drop-menu');
    if (m) { m.addEventListener('mouseenter', open); m.addEventListener('mouseleave', close); }
  });

  // Mobile tap toggle
  document.querySelectorAll('.nav-drop .drop-toggle').forEach(t => {
    t.addEventListener('click', function(e) {
      if (window.innerWidth <= 900) { e.stopPropagation(); this.closest('.nav-drop').classList.toggle('drop-open'); }
    });
  });

  // Close on outside click
  document.addEventListener('click', e => {
    if (!e.target.closest('.nav-drop')) document.querySelectorAll('.nav-drop').forEach(d => d.classList.remove('drop-open'));
  });

  // Scroll reveal
  const ro = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); ro.unobserve(e.target); } });
  }, { threshold: 0.10 });
  document.querySelectorAll('.reveal').forEach(el => ro.observe(el));

  // Navbar scroll effect
  window.addEventListener('scroll', () => {
    const nav = document.querySelector('.drifter-nav');
    if (window.scrollY > 50) {
      nav.style.background = 'rgba(10,22,40,0.99)';
      nav.style.boxShadow = '0 4px 30px rgba(0,0,0,0.40)';
    } else {
      nav.style.background = 'rgba(10,22,40,0.97)';
      nav.style.boxShadow = '0 2px 20px rgba(0,0,0,0.25)';
    }
  });

  // Global toast helper
  window.showToast = function(msg, type='info', dur=4000) {
    const t = document.getElementById('g-toast');
    const icons = { success:'check-circle', error:'times-circle', info:'info-circle' };
    t.innerHTML = `<i class="fas fa-${icons[type]||'info-circle'}"></i> ${msg}`;
    t.className = `show ${type}`;
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.className = '', dur);
  };
})();
</script>
