<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE')) { require_once dirname(__DIR__).'/config.php'; }
$_active = $navActive ?? '';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
  /* ── NEW PALETTE ── */
  --bg:       #F8F9FA;   /* Off-White / Silver Tint */
  --primary:  #212529;   /* Charcoal */
  --orange:   #FF6B00;   /* Hi-Vis Orange */
  --yellow:   #FFC107;   /* Safety Yellow */
  --silver:   #ADB5BD;   /* Reflective Silver */

  /* ── KEPT (unused, removed) ── */
  --brand-lt: #fff3eb;

  /* ── SEMANTIC ALIASES ── */
  --brand:    var(--orange);
  --brand2:   #e05e00;
  --accent:   var(--orange);
  --accent2:  var(--yellow);
  --light:    var(--bg);
  --text:     var(--primary);
  --muted:    #6c757d;
  --white:    #ffffff;
  --radius:   10px;
  --shadow:   0 4px 24px rgba(33,37,41,0.10);
  --shadow-lg:0 12px 48px rgba(33,37,41,0.18);
  --trans:    all 0.25s ease;
}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);}

/* ── GLOBAL INTERACTIVE HOVER POLISH ── */
.submit-btn,.book-btn,.add-btn,.back-btn,.contact-btn,
.btn-submit,.btn-primary-global {
  transition: transform 0.22s ease, box-shadow 0.22s ease, filter 0.22s ease !important;
}
.submit-btn:hover,.book-btn:hover,.add-btn:hover,.back-btn:hover,
.contact-btn:hover,.btn-submit:hover,.btn-primary-global:hover {
  transform: translateY(-3px) !important;
  filter: brightness(1.07);
}
.submit-btn:active,.book-btn:active,.add-btn:active,.back-btn:active,
.contact-btn:active,.btn-submit:active { transform: translateY(-1px) !important; }

.v-card { transition: transform 0.28s ease, box-shadow 0.28s ease !important; }
.v-card:hover { transform: translateY(-8px) !important; box-shadow: 0 20px 48px rgba(0,0,0,0.13) !important; }
.p-card { transition: transform 0.28s ease, box-shadow 0.28s ease !important; }
.p-card:hover { transform: translateY(-8px) !important; box-shadow: 0 20px 48px rgba(0,0,0,0.13) !important; }
.c-card { transition: transform 0.28s ease, box-shadow 0.28s ease !important; }
.c-card:hover { transform: translateY(-6px) !important; box-shadow: 0 16px 40px rgba(0,0,0,0.12) !important; }
.quick-card { transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease !important; }
.quick-card:hover { transform: translateY(-6px) !important; box-shadow: 0 16px 40px rgba(0,0,0,0.12) !important; }
.quick-card:hover .qi { transform: scale(1.12) rotate(-5deg); transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1); }
.bk-card { transition: transform 0.22s ease, box-shadow 0.22s ease !important; }
.bk-card:hover { transform: translateY(-3px) !important; box-shadow: 0 12px 32px rgba(0,0,0,0.1) !important; }
.stat-card { transition: transform 0.22s ease, background 0.22s ease !important; }
.stat-card:hover { transform: translateY(-5px) !important; }
input:focus, select:focus, textarea:focus { outline: none; }
.toggle-btn { transition: background 0.2s ease, transform 0.2s ease !important; }
.toggle-btn:hover { transform: translateY(-1px) !important; filter: brightness(0.95); }
.act-btn { transition: background 0.18s ease, transform 0.18s ease !important; }
.act-btn:hover:not(:disabled) { transform: scale(1.1) !important; }
.cancel-btn { transition: background 0.18s ease, transform 0.18s ease !important; }
.cancel-btn:hover:not(:disabled) { transform: scale(1.05) !important; }
.role-card { transition: border-color 0.2s ease, background 0.2s ease, transform 0.2s ease !important; }
.role-card:hover { transform: translateY(-2px) !important; }
.check-item { transition: border-color 0.2s ease, background 0.2s ease, transform 0.18s ease !important; }
.check-item:hover { transform: translateY(-1px) !important; }
.file-drop { transition: border-color 0.2s ease, background 0.2s ease, transform 0.2s ease !important; }
.file-drop:hover { transform: scale(1.01) !important; }

/* ── NAVBAR ── */
.drifter-nav{
  background:var(--primary);
  position:sticky;top:0;z-index:9999;
  box-shadow:0 2px 20px rgba(0,0,0,0.35);
  border-bottom:3px solid var(--orange);
}
.nav-inner{
  max-width:1280px;margin:0 auto;
  display:flex;align-items:center;justify-content:space-between;
  padding:0 24px;height:68px;
}

/* Logo */
.nav-logo{
  display:flex;align-items:center;gap:10px;
  text-decoration:none;
  transition:opacity 0.2s ease;
}
.nav-logo:hover{opacity:0.90;}
.nav-logo .logo-icon{
  width:40px;height:40px;
  background:linear-gradient(135deg,var(--orange),var(--yellow));
  border-radius:10px;
  display:flex;align-items:center;justify-content:center;
  font-size:1.2rem;color:#fff;font-weight:800;
  transition:transform 0.25s ease,box-shadow 0.25s ease;
}
.nav-logo:hover .logo-icon{
  transform:rotate(-6deg) scale(1.08);
  box-shadow:0 4px 16px rgba(255,107,0,0.55);
}
.nav-logo .logo-text{
  font-size:1.5rem;font-weight:800;
  color:white;letter-spacing:1px;
}
.nav-logo .logo-sub{
  font-size:0.65rem;color:var(--silver);
  letter-spacing:2px;text-transform:uppercase;
  display:block;margin-top:-4px;
}

/* Nav links */
.nav-links{
  display:flex;align-items:center;gap:4px;
  list-style:none;
}
.nav-links a,.nav-links .drop-toggle{
  color:rgba(255,255,255,0.78);
  text-decoration:none;font-size:0.88rem;font-weight:500;
  padding:8px 14px;border-radius:8px;
  display:flex;align-items:center;gap:6px;
  transition:background 0.2s ease,color 0.2s ease,transform 0.2s ease;
  cursor:pointer;white-space:nowrap;
}
.nav-links a:hover,.nav-links .drop-toggle:hover{
  background:rgba(255,107,0,0.18);
  color:#fff;
  transform:translateY(-1px);
}
.nav-links a:active,.nav-links .drop-toggle:active{transform:translateY(0);}
.nav-links a.active{
  background:rgba(255,107,0,0.22);
  color:var(--orange);
}

/* Dropdown */
.nav-drop{position:relative;}
.drop-chevron{transition:transform 0.3s ease;display:inline-block;font-size:0.7rem!important;}
.nav-drop.drop-open .drop-chevron{transform:rotate(180deg);}

.drop-menu{
  visibility:hidden;
  opacity:0;
  pointer-events:none;
  position:absolute;top:calc(100% + 8px);left:0;
  background:#2b2f33;border-radius:12px;
  min-width:230px;padding:8px;
  box-shadow:0 12px 40px rgba(0,0,0,0.45);
  border:1px solid rgba(255,107,0,0.20);
  z-index:10000;
  transform:translateY(-6px);
  transition:opacity 0.28s ease,transform 0.28s ease,visibility 0s linear 0.28s;
}
.nav-drop.drop-open .drop-menu{
  visibility:visible;
  opacity:1;
  pointer-events:auto;
  transform:translateY(0);
  transition:opacity 0.28s ease,transform 0.28s ease,visibility 0s linear 0s;
}
.drop-menu a{
  display:flex;align-items:center;gap:10px;
  padding:10px 14px;border-radius:8px;
  color:rgba(255,255,255,0.80);font-size:0.85rem;
  text-decoration:none;
  transition:background 0.2s ease,color 0.2s ease,padding-left 0.2s ease;
}
.drop-menu a:hover{
  background:rgba(255,107,0,0.15);
  color:#fff;
  padding-left:18px;
}
.drop-menu a i{width:18px;text-align:center;color:var(--orange);flex-shrink:0;}
.drop-divider{height:1px;background:rgba(255,107,0,0.15);margin:6px 0;}

/* User badge */
.user-badge{
  display:flex;align-items:center;gap:8px;
  background:rgba(255,107,0,0.12);
  border:1px solid rgba(255,107,0,0.25);
  border-radius:50px;padding:6px 14px 6px 6px;
  cursor:pointer;
  transition:background 0.2s ease,border-color 0.2s ease,transform 0.2s ease;
}
.user-badge:hover{
  background:rgba(255,107,0,0.22);
  border-color:rgba(255,107,0,0.45);
  transform:translateY(-1px);
}
.user-avatar{
  width:32px;height:32px;border-radius:50%;
  background:linear-gradient(135deg,var(--orange),var(--yellow));
  display:flex;align-items:center;justify-content:center;
  font-weight:700;font-size:0.85rem;color:#fff;
  transition:transform 0.2s ease;
}
.user-badge:hover .user-avatar{transform:scale(1.1);}
.user-name{color:white;font-size:0.85rem;font-weight:600;}

/* CTA button */
.nav-cta{
  background:linear-gradient(135deg,var(--orange),var(--brand2))!important;
  color:#fff!important;border-radius:8px;
  padding:8px 18px!important;font-weight:700!important;
  box-shadow:0 2px 8px rgba(255,107,0,0.40);
  transition:transform 0.2s ease,box-shadow 0.2s ease,filter 0.2s ease!important;
}
.nav-cta:hover{
  transform:translateY(-2px)!important;
  box-shadow:0 6px 18px rgba(255,107,0,0.60)!important;
  filter:brightness(1.08);
}
.nav-cta:active{transform:translateY(0)!important;}

/* Hamburger */
.nav-hamburger{
  display:none;flex-direction:column;gap:5px;
  cursor:pointer;padding:8px;
  transition:opacity 0.2s;
}
.nav-hamburger:hover{opacity:0.75;}
.nav-hamburger span{
  width:24px;height:2px;background:white;
  border-radius:2px;transition:var(--trans);
}
.nav-hamburger.open span:nth-child(1){transform:translateY(7px) rotate(45deg);}
.nav-hamburger.open span:nth-child(2){opacity:0;transform:scaleX(0);}
.nav-hamburger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg);}

@media(max-width:900px){
  .nav-links{
    display:none;flex-direction:column;
    position:absolute;top:68px;left:0;right:0;
    background:#212529;padding:16px;gap:4px;
    border-bottom:2px solid var(--orange);
  }
  .nav-links.open{display:flex;}
  .nav-hamburger{display:flex;}
  .drop-menu{
    position:static;box-shadow:none;border:none;
    background:rgba(255,107,0,0.07);margin-top:4px;
    visibility:visible;opacity:1;pointer-events:auto;
    transform:none;transition:none;
    display:none;
  }
  .nav-drop.drop-open .drop-menu{display:block;}
}
</style>

<nav class="drifter-nav">
  <div class="nav-inner">
    <a href="<?= BASE ?>/front/index.php" class="nav-logo">
      <div class="logo-icon">D</div>
      <div>
        <span class="logo-text">DRIFTER</span>
        <span class="logo-sub">Transport Services</span>
      </div>
    </a>

    <ul class="nav-links" id="navLinks">
      <li><a href="<?= BASE ?>/front/index.php" class="<?= $_active==='home'?'active':'' ?>"><i class="fas fa-home"></i> Home</a></li>

      <?php
      $role = $_SESSION['role'] ?? 'guest';
      if (!isset($_SESSION['loggedin']) || $role === 'customer'): ?>
      <li class="nav-drop">
        <span class="drop-toggle"><i class="fas fa-truck"></i> Services <i class="fas fa-chevron-down drop-chevron"></i></span>
        <div class="drop-menu">
          <a href="<?= BASE ?>/transport/booking_step1.php"><i class="fas fa-truck-moving"></i> Transport Goods</a>
          <a href="<?= BASE ?>/travel/booking_step1.php"><i class="fas fa-bus"></i> Travel / Ride</a>
          <a href="<?= BASE ?>/courier/courier.php"><i class="fas fa-box"></i> Courier</a>
          <a href="<?= BASE ?>/move/movers.php"><i class="fas fa-people-carry"></i> Packers &amp; Movers</a>
        </div>
      </li>
      <?php endif; ?>

      <?php if (isset($_SESSION['loggedin']) && $role === 'owner'): ?>
      <li class="nav-drop">
        <span class="drop-toggle"><i class="fas fa-plus-circle"></i> My Vehicles <i class="fas fa-chevron-down drop-chevron"></i></span>
        <div class="drop-menu">
          <a href="<?= BASE ?>/front/your_vehicle_info.php"><i class="fas fa-truck"></i> Transport Vehicles</a>
          <a href="<?= BASE ?>/front/your_vehicle_travel.php"><i class="fas fa-bus-alt"></i> Travel Vehicles</a>
          <div class="drop-divider"></div>
          <a href="<?= BASE ?>/transport/add_vehicle.php"><i class="fas fa-plus"></i> Add Transport Vehicle</a>
          <a href="<?= BASE ?>/travel/add_vehicle.php"><i class="fas fa-plus"></i> Add Travel Vehicle</a>
        </div>
      </li>
      <?php endif; ?>

      <?php if (isset($_SESSION['loggedin']) && $role === 'company'): ?>
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

      <li><a href="<?= BASE ?>/front/about.php" class="<?= $_active==='about'?'active':'' ?>"><i class="fas fa-info-circle"></i> About</a></li>
      <li><a href="<?= BASE ?>/front/support.php" class="<?= $_active==='support'?'active':'' ?>"><i class="fas fa-headset"></i> Support</a></li>

      <?php if (!isset($_SESSION['loggedin'])): ?>
        <li><a href="<?= BASE ?>/front/login.php" class="nav-cta"><i class="fas fa-sign-in-alt"></i> Login</a></li>
      <?php else: ?>
        <li class="nav-drop">
          <div class="user-badge drop-toggle">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['username'],0,1)) ?></div>
            <span class="user-name"><?= htmlspecialchars($_SESSION['username']) ?></span>
            <i class="fas fa-chevron-down drop-chevron" style="color:rgba(255,255,255,0.5);"></i>
          </div>
          <div class="drop-menu" style="right:0;left:auto;">
            <?php if ($role === 'customer'): ?>
              <a href="<?= BASE ?>/front/dashboard_customer.php"><i class="fas fa-tachometer-alt"></i> My Bookings</a>
            <?php elseif ($role === 'owner'): ?>
              <a href="<?= BASE ?>/front/your_vehicle_info.php"><i class="fas fa-truck"></i> Transport Vehicles</a>
              <a href="<?= BASE ?>/front/your_vehicle_travel.php"><i class="fas fa-bus"></i> Travel Vehicles</a>
            <?php elseif ($role === 'company'): ?>
              <a href="<?= BASE ?>/courier/company_info.php"><i class="fas fa-box"></i> Courier Companies</a>
              <a href="<?= BASE ?>/move/company_info.php"><i class="fas fa-people-carry"></i> Movers Companies</a>
            <?php endif; ?>
            <div class="drop-divider"></div>
            <a href="<?= BASE ?>/front/logout.php" style="color:#ff8c69!important"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </li>
      <?php endif; ?>
    </ul>

    <div class="nav-hamburger" id="hamburger">
      <span></span><span></span><span></span>
    </div>
  </div>
</nav>
<script>
(function(){
  const hb = document.getElementById('hamburger');
  const nl = document.getElementById('navLinks');
  hb.addEventListener('click',()=>{
    nl.classList.toggle('open');
    hb.classList.toggle('open');
  });
  const CLOSE_DELAY = 400;
  document.querySelectorAll('.nav-drop').forEach(drop => {
    let closeTimer = null;
    const open  = () => { clearTimeout(closeTimer); drop.classList.add('drop-open'); };
    const close = () => { closeTimer = setTimeout(() => drop.classList.remove('drop-open'), CLOSE_DELAY); };
    drop.addEventListener('mouseenter', open);
    drop.addEventListener('mouseleave', close);
    const menu = drop.querySelector('.drop-menu');
    if (menu) {
      menu.addEventListener('mouseenter', open);
      menu.addEventListener('mouseleave', close);
    }
  });
  document.querySelectorAll('.nav-drop .drop-toggle').forEach(t=>{
    t.addEventListener('click',function(e){
      if(window.innerWidth<=900){
        e.stopPropagation();
        this.closest('.nav-drop').classList.toggle('drop-open');
      }
    });
  });
  document.addEventListener('click', e => {
    if (!e.target.closest('.nav-drop')) {
      document.querySelectorAll('.nav-drop').forEach(d => d.classList.remove('drop-open'));
    }
  });
})();
</script>
