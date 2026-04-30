<?php
session_start();
$navActive = 'home';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Drifter — India's Transport Platform</title>
<style>
/* ── HERO ── */
.hero {
  min-height:100vh; display:flex; align-items:center; justify-content:center;
  text-align:center; padding:100px 24px 60px; position:relative; overflow:hidden;
}
.hero-bg {
  position:absolute; inset:0;
  background:url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1920&q=80') center/cover no-repeat;
  filter:brightness(0.22) saturate(0.6);
  transform:scale(1.04);
  animation:heroZoom 18s ease-in-out infinite alternate;
}
@keyframes heroZoom { from{transform:scale(1.04);} to{transform:scale(1.12);} }
.hero-overlay {
  position:absolute; inset:0;
  background:linear-gradient(160deg,rgba(15,23,42,0.95) 0%,rgba(37,99,235,0.40) 100%);
}
.hero-content { position:relative; z-index:2; max-width:860px; }
.hero-badge {
  display:inline-flex; align-items:center; gap:8px;
  background:rgba(37,99,235,0.20); border:1px solid rgba(37,99,235,0.45);
  color:#93c5fd; padding:7px 20px; border-radius:50px;
  font-size:0.76rem; font-weight:700; letter-spacing:2px; text-transform:uppercase;
  margin-bottom:28px; animation:fadeUp 0.8s ease both;
}
.hero h1 {
  font-size:clamp(2.6rem,6vw,4.4rem); font-weight:800; color:white;
  line-height:1.1; margin-bottom:22px; animation:fadeUp 0.9s ease 0.1s both;
}
.hero h1 span {
  background:linear-gradient(135deg,#60a5fa,#06b6d4);
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.hero p {
  font-size:1.12rem; color:rgba(255,255,255,0.72); max-width:560px;
  margin:0 auto 40px; line-height:1.8; animation:fadeUp 1s ease 0.2s both;
}
.hero-btns { display:flex; gap:14px; justify-content:center; flex-wrap:wrap; animation:fadeUp 1.1s ease 0.3s both; }
@keyframes fadeUp { from{opacity:0;transform:translateY(24px);} to{opacity:1;transform:translateY(0);} }

.btn-hero-primary {
  background:linear-gradient(135deg,#2563EB,#1d4ed8); color:white;
  padding:15px 34px; border-radius:12px; font-weight:700; font-size:1rem;
  display:inline-flex; align-items:center; gap:9px;
  box-shadow:0 4px 24px rgba(37,99,235,0.50);
  transition:transform 0.22s, box-shadow 0.22s, filter 0.22s;
}
.btn-hero-primary:hover { transform:translateY(-3px); box-shadow:0 12px 36px rgba(37,99,235,0.65); filter:brightness(1.08); }
.btn-hero-outline {
  background:rgba(255,255,255,0.08); color:white;
  border:2px solid rgba(255,255,255,0.30); padding:15px 34px;
  border-radius:12px; font-weight:600; font-size:1rem;
  display:inline-flex; align-items:center; gap:9px;
  backdrop-filter:blur(8px);
  transition:background 0.22s, border-color 0.22s, transform 0.22s;
}
.btn-hero-outline:hover { background:rgba(37,99,235,0.25); border-color:#60a5fa; transform:translateY(-3px); }

.scroll-hint {
  position:absolute; bottom:32px; left:50%; transform:translateX(-50%);
  z-index:2; display:flex; flex-direction:column; align-items:center; gap:6px;
  color:rgba(255,255,255,0.35); font-size:0.72rem; letter-spacing:1px;
  animation:bounce 2s ease-in-out infinite;
}
@keyframes bounce { 0%,100%{transform:translateX(-50%) translateY(0);} 50%{transform:translateX(-50%) translateY(8px);} }

/* ── STATS BAR ── */
.stats-bar {
  background:white; padding:32px 24px;
  box-shadow:0 4px 24px rgba(15,23,42,0.07);
  border-bottom:2px solid #f0f9ff;
}
.stats-inner {
  max-width:1280px; margin:0 auto;
  display:grid; grid-template-columns:repeat(4,1fr); gap:20px; text-align:center;
}
.stat-item { padding:8px; }
.stat-item .num {
  font-size:2.2rem; font-weight:800;
  background:linear-gradient(135deg,#2563EB,#06b6d4);
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.stat-item .lbl { font-size:0.78rem; color:#64748b; font-weight:500; margin-top:5px; }
.stat-item .icon { font-size:1.4rem; margin-bottom:6px; }
@media(max-width:600px){ .stats-inner{grid-template-columns:repeat(2,1fr);} }

/* ── SERVICE CARDS ── */
.services-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:24px; }
.service-card {
  background:white; border-radius:20px; padding:36px 28px;
  text-align:center; box-shadow:0 2px 16px rgba(15,23,42,0.07);
  transition:transform 0.30s ease, box-shadow 0.30s ease;
  border:2px solid transparent; position:relative; overflow:hidden;
  cursor:pointer; text-decoration:none; display:block; color:inherit;
}
.service-card::before {
  content:''; position:absolute; bottom:0; left:0; right:0; height:4px;
  background:var(--card-color,#2563EB);
  transform:scaleX(0); transform-origin:left; transition:transform 0.32s ease;
}
.service-card:hover { transform:translateY(-10px); box-shadow:0 20px 48px rgba(15,23,42,0.14); border-color:var(--card-color,#2563EB); }
.service-card:hover::before { transform:scaleX(1); }
.service-icon {
  width:76px; height:76px; border-radius:22px;
  background:var(--icon-bg,#eff6ff);
  display:flex; align-items:center; justify-content:center;
  font-size:2.2rem; margin:0 auto 20px;
  transition:transform 0.32s cubic-bezier(0.34,1.56,0.64,1);
}
.service-card:hover .service-icon { transform:scale(1.18) rotate(-8deg); }
.service-card h3 { font-size:1.15rem; font-weight:700; margin-bottom:10px; transition:color 0.2s; }
.service-card:hover h3 { color:var(--card-color,#2563EB); }
.service-card p { font-size:0.86rem; color:#64748b; line-height:1.7; margin-bottom:20px; }
.service-link { display:inline-flex; align-items:center; gap:6px; font-size:0.84rem; font-weight:700; color:var(--card-color,#2563EB); }
.service-link i { transition:transform 0.22s; }
.service-card:hover .service-link i { transform:translateX(6px); }

/* ── HOW IT WORKS ── */
.how-section { padding:90px 24px; background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 100%); }
.steps-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:28px; max-width:1280px; margin:0 auto; }
.step-card {
  text-align:center; padding:36px 24px; border-radius:20px;
  background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08);
  transition:background 0.25s, transform 0.25s, border-color 0.25s;
}
.step-card:hover { background:rgba(37,99,235,0.15); transform:translateY(-6px); border-color:rgba(37,99,235,0.40); }
.step-num {
  width:60px; height:60px; border-radius:50%;
  background:linear-gradient(135deg,#2563EB,#06b6d4); color:white;
  font-size:1.4rem; font-weight:800;
  display:flex; align-items:center; justify-content:center;
  margin:0 auto 18px;
  transition:transform 0.32s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.32s;
}
.step-card:hover .step-num { transform:scale(1.15); box-shadow:0 8px 24px rgba(37,99,235,0.55); }
.step-card h4 { color:white; font-size:1rem; font-weight:700; margin-bottom:10px; }
.step-card p  { color:rgba(255,255,255,0.55); font-size:0.85rem; line-height:1.7; }

/* ── FEATURES ── */
.features-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:20px; }
.feature-card {
  background:white; border-radius:16px; padding:28px;
  box-shadow:0 2px 16px rgba(15,23,42,0.07); display:flex; gap:18px; align-items:flex-start;
  transition:transform 0.26s, box-shadow 0.26s, border-left-color 0.26s;
  border-left:4px solid transparent;
}
.feature-card:hover { transform:translateY(-6px); box-shadow:0 12px 32px rgba(15,23,42,0.12); border-left-color:#2563EB; }
.feat-icon {
  width:50px; height:50px; border-radius:14px; flex-shrink:0;
  background:linear-gradient(135deg,#2563EB,#1d4ed8);
  display:flex; align-items:center; justify-content:center;
  font-size:1.2rem; color:white;
  transition:transform 0.32s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.32s;
}
.feature-card:hover .feat-icon { transform:scale(1.15) rotate(-6deg); box-shadow:0 6px 18px rgba(37,99,235,0.45); }
.feature-card h4 { font-size:0.97rem; font-weight:700; margin-bottom:6px; transition:color 0.2s; }
.feature-card:hover h4 { color:#2563EB; }
.feature-card p { font-size:0.84rem; color:#64748b; line-height:1.7; }

/* ── TESTIMONIALS ── */
.testimonials-section { padding:90px 24px; background:#f0f9ff; }
.testimonials-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:24px; max-width:1280px; margin:0 auto; }
.testi-card {
  background:white; border-radius:18px; padding:30px;
  box-shadow:0 2px 16px rgba(15,23,42,0.07); transition:transform 0.26s, box-shadow 0.26s;
  position:relative;
}
.testi-card:hover { transform:translateY(-6px); box-shadow:0 12px 32px rgba(15,23,42,0.12); }
.testi-card::before {
  content:'"'; position:absolute; top:18px; right:24px;
  font-size:5rem; color:#eff6ff; font-family:Georgia,serif; line-height:1;
}
.testi-stars { color:#f59e0b; font-size:0.95rem; margin-bottom:14px; letter-spacing:2px; }
.testi-text { font-size:0.89rem; color:#334155; line-height:1.8; margin-bottom:20px; font-style:italic; }
.testi-author { display:flex; align-items:center; gap:13px; }
.testi-avatar {
  width:44px; height:44px; border-radius:50%;
  background:linear-gradient(135deg,#2563EB,#06b6d4);
  display:flex; align-items:center; justify-content:center;
  font-weight:700; color:white; font-size:1rem; flex-shrink:0;
}
.testi-name { font-size:0.9rem; font-weight:700; color:#0f172a; }
.testi-role { font-size:0.76rem; color:#64748b; }

/* ── CTA BANNER ── */
.cta-banner {
  padding:100px 24px; text-align:center;
  background:linear-gradient(135deg,#1d4ed8 0%,#0891b2 100%);
  position:relative; overflow:hidden;
}
.cta-banner::before {
  content:''; position:absolute; inset:0;
  background:url('https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=1920&q=80') center/cover no-repeat;
  opacity:0.06;
}
.cta-banner > * { position:relative; z-index:1; }
.cta-banner h2 { font-size:clamp(1.9rem,4vw,2.7rem); font-weight:800; color:white; margin-bottom:14px; }
.cta-banner p  { color:rgba(255,255,255,0.82); font-size:1rem; margin-bottom:34px; }
.btn-cta {
  background:white; color:#1d4ed8;
  padding:15px 40px; border-radius:12px; font-weight:700; font-size:1rem;
  display:inline-flex; align-items:center; gap:9px;
  box-shadow:0 4px 24px rgba(0,0,0,0.15);
  transition:transform 0.22s, box-shadow 0.22s;
}
.btn-cta:hover { transform:translateY(-3px); box-shadow:0 12px 36px rgba(0,0,0,0.22); }

/* ── BOOKING TOAST ── */
.booking-toast {
  position:fixed; top:80px; right:24px; z-index:99999;
  background:linear-gradient(135deg,#10b981,#059669); color:white;
  padding:14px 22px; border-radius:12px; font-weight:600; font-size:0.9rem;
  box-shadow:0 8px 32px rgba(16,185,129,0.40); transform:translateX(120%); transition:transform 0.4s ease;
  display:flex; align-items:center; gap:10px;
}
.booking-toast.show { transform:translateX(0); }

/* ── SECTION TAG OVERRIDE ── */
.tag-cyan {
  background:rgba(6,182,212,0.12); color:#0891b2;
  border:1px solid rgba(6,182,212,0.25);
}
</style>
</head>
<body>

<?php if (isset($_GET['booking_success'])): ?>
<div class="booking-toast" id="bToast"><i class="fas fa-check-circle"></i> Booking confirmed! Check your dashboard.</div>
<script>const _t=document.getElementById('bToast');setTimeout(()=>_t.classList.add('show'),100);setTimeout(()=>_t.classList.remove('show'),5000);</script>
<?php endif; ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-badge"><i class="fas fa-bolt"></i> India's #1 Transport Platform</div>
    <h1>Move Anything,<br><span>Anywhere in India</span></h1>
    <p>Transport goods, book travel vehicles, send couriers, or hire packers &amp; movers — all from one trusted platform.</p>
    <div class="hero-btns">
      <a href="#services" class="btn-hero-primary"><i class="fas fa-rocket"></i> Explore Services</a>
      <?php if (!isset($_SESSION['loggedin'])): ?>
        <a href="<?= BASE ?>/front/signup.php" class="btn-hero-outline"><i class="fas fa-user-plus"></i> Join Free</a>
      <?php else: ?>
        <a href="<?= BASE ?>/front/dashboard_customer.php" class="btn-hero-outline"><i class="fas fa-tachometer-alt"></i> My Dashboard</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="scroll-hint"><i class="fas fa-chevron-down"></i><span>Scroll</span></div>
</section>

<!-- LIVE STATS -->
<div class="stats-bar">
  <div class="stats-inner">
    <div class="stat-item reveal"><div class="icon">🚛</div><div class="num" id="st-v">—</div><div class="lbl">Available Vehicles</div></div>
    <div class="stat-item reveal reveal-delay-1"><div class="icon">📋</div><div class="num" id="st-b">—</div><div class="lbl">Total Bookings</div></div>
    <div class="stat-item reveal reveal-delay-2"><div class="icon">😊</div><div class="num" id="st-c">—</div><div class="lbl">Happy Customers</div></div>
    <div class="stat-item reveal reveal-delay-3"><div class="icon">🕐</div><div class="num">24/7</div><div class="lbl">Support Available</div></div>
  </div>
</div>

<!-- SERVICES -->
<section class="section" id="services" style="background:#f8fafc;">
  <div class="section-inner">
    <div class="section-head reveal">
      <div class="tag">What We Offer</div>
      <h2>Our Services</h2>
      <p>Everything you need for transportation, all in one trusted platform.</p>
    </div>
    <div class="services-grid">
      <a href="<?= isset($_SESSION['loggedin']) ? BASE.'/transport/booking_step1.php' : BASE.'/front/login.php?redirect='.urlencode(BASE.'/transport/booking_step1.php') ?>" class="service-card reveal reveal-delay-1" style="--card-color:#2563EB;--icon-bg:#eff6ff">
        <div class="service-icon">🚚</div>
        <h3>Transport Goods</h3>
        <p>Reliable goods transportation for all cargo sizes with verified drivers across India.</p>
        <span class="service-link">Book Now <i class="fas fa-arrow-right"></i></span>
      </a>
      <a href="<?= isset($_SESSION['loggedin']) ? BASE.'/travel/booking_step1.php' : BASE.'/front/login.php?redirect='.urlencode(BASE.'/travel/booking_step1.php') ?>" class="service-card reveal reveal-delay-2" style="--card-color:#06b6d4;--icon-bg:#ecfeff">
        <div class="service-icon">🚌</div>
        <h3>Travel / Ride</h3>
        <p>Comfortable travel vehicles for daily commutes and long-distance trips.</p>
        <span class="service-link">Book Now <i class="fas fa-arrow-right"></i></span>
      </a>
      <a href="<?= isset($_SESSION['loggedin']) ? BASE.'/courier/courier.php' : BASE.'/front/login.php?redirect='.urlencode(BASE.'/courier/courier.php') ?>" class="service-card reveal reveal-delay-3" style="--card-color:#7c3aed;--icon-bg:#f5f3ff">
        <div class="service-icon">📦</div>
        <h3>Courier</h3>
        <p>Fast and secure package delivery — same-day, next-day, or scheduled.</p>
        <span class="service-link">Send Now <i class="fas fa-arrow-right"></i></span>
      </a>
      <a href="<?= isset($_SESSION['loggedin']) ? BASE.'/move/movers.php' : BASE.'/front/login.php?redirect='.urlencode(BASE.'/move/movers.php') ?>" class="service-card reveal reveal-delay-4" style="--card-color:#059669;--icon-bg:#ecfdf5">
        <div class="service-icon">🏠</div>
        <h3>Packers &amp; Movers</h3>
        <p>Professional home and office relocation with full packing service.</p>
        <span class="service-link">Get Quote <i class="fas fa-arrow-right"></i></span>
      </a>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<div class="how-section">
  <div class="section-head reveal" style="margin-bottom:52px;">
    <div class="tag" style="background:rgba(96,165,250,0.15);color:#93c5fd;border-color:rgba(96,165,250,0.25);">Simple Process</div>
    <h2 style="color:white;">How It Works</h2>
    <p style="color:rgba(255,255,255,0.55);">Get your service booked in 4 easy steps.</p>
  </div>
  <div class="steps-grid">
    <div class="step-card reveal reveal-delay-1"><div class="step-num">1</div><h4>Create Account</h4><p>Sign up free in under a minute. No credit card required.</p></div>
    <div class="step-card reveal reveal-delay-2"><div class="step-num">2</div><h4>Choose Service</h4><p>Select transport, travel, courier, or movers based on your need.</p></div>
    <div class="step-card reveal reveal-delay-3"><div class="step-num">3</div><h4>Enter Details</h4><p>Fill in pickup, drop, date and distance to find available providers.</p></div>
    <div class="step-card reveal reveal-delay-4"><div class="step-num">4</div><h4>Confirm Booking</h4><p>Pick your provider, confirm, and you're done. That simple.</p></div>
  </div>
</div>

<!-- WHY DRIFTER -->
<section class="section" style="background:white;">
  <div class="section-inner">
    <div class="section-head reveal">
      <div class="tag tag-cyan">Why Choose Us</div>
      <h2>The Drifter Advantage</h2>
      <p>We combine technology with trust to deliver exceptional service every time.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card reveal"><div class="feat-icon"><i class="fas fa-shield-alt"></i></div><div><h4>Verified Providers</h4><p>Every driver and company is background-checked before listing.</p></div></div>
      <div class="feature-card reveal"><div class="feat-icon"><i class="fas fa-clock"></i></div><div><h4>On-Time Guarantee</h4><p>98% on-time rate across all services. We value your time.</p></div></div>
      <div class="feature-card reveal"><div class="feat-icon"><i class="fas fa-rupee-sign"></i></div><div><h4>Transparent Pricing</h4><p>No hidden charges. See the full price before you confirm.</p></div></div>
      <div class="feature-card reveal"><div class="feat-icon"><i class="fas fa-headset"></i></div><div><h4>24/7 Support</h4><p>Our support team is always available to help you.</p></div></div>
      <div class="feature-card reveal"><div class="feat-icon"><i class="fas fa-route"></i></div><div><h4>Nationwide Network</h4><p>Partners in every major city — we cover the whole country.</p></div></div>
      <div class="feature-card reveal"><div class="feat-icon"><i class="fas fa-mobile-alt"></i></div><div><h4>Easy Booking</h4><p>Book in under 2 minutes from any device, anywhere.</p></div></div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<div class="testimonials-section">
  <div class="section-head reveal" style="max-width:1280px;margin:0 auto 52px;">
    <div class="tag">What People Say</div>
    <h2>Customer Reviews</h2>
    <p>Trusted by thousands of customers across India.</p>
  </div>
  <div class="testimonials-grid">
    <div class="testi-card reveal reveal-delay-1">
      <div class="testi-stars">★★★★★</div>
      <p class="testi-text">Booked a transport vehicle for my goods from Pune to Mumbai. The driver was on time, professional, and the pricing was exactly as shown. No surprises!</p>
      <div class="testi-author"><div class="testi-avatar">R</div><div><div class="testi-name">Rahul Sharma</div><div class="testi-role">Transport Customer, Pune</div></div></div>
    </div>
    <div class="testi-card reveal reveal-delay-2">
      <div class="testi-stars">★★★★★</div>
      <p class="testi-text">Used Drifter for my home relocation. The packers were careful with all my furniture. Highly recommend the full-service option — worth every rupee.</p>
      <div class="testi-author"><div class="testi-avatar">P</div><div><div class="testi-name">Priya Nair</div><div class="testi-role">Movers Customer, Bangalore</div></div></div>
    </div>
    <div class="testi-card reveal reveal-delay-3">
      <div class="testi-stars">★★★★☆</div>
      <p class="testi-text">As a vehicle owner, I've been getting consistent bookings since I registered. The dashboard makes it easy to manage everything. Great platform!</p>
      <div class="testi-author"><div class="testi-avatar">A</div><div><div class="testi-name">Amit Patil</div><div class="testi-role">Vehicle Owner, Satara</div></div></div>
    </div>
  </div>
</div>

<!-- CTA -->
<div class="cta-banner">
  <h2>Ready to Get Started?</h2>
  <p>Join thousands of customers who trust Drifter for all their transport needs.</p>
  <?php if (!isset($_SESSION['loggedin'])): ?>
    <a href="<?= BASE ?>/front/signup.php" class="btn-cta"><i class="fas fa-user-plus"></i> Create Free Account</a>
  <?php else: ?>
    <a href="#services" class="btn-cta"><i class="fas fa-rocket"></i> Book a Service Now</a>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
<script>
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const t = document.querySelector(a.getAttribute('href'));
    if (t) { e.preventDefault(); t.scrollIntoView({behavior:'smooth'}); }
  });
});

function animCount(el, target) {
  let cur = 0, step = Math.ceil(target / 40);
  const t = setInterval(() => {
    cur = Math.min(cur + step, target);
    el.textContent = cur + '+';
    if (cur >= target) clearInterval(t);
  }, 30);
}
fetch('<?= BASE ?>/front/api_stats.php')
  .then(r => r.json())
  .then(d => {
    if (d.error) return;
    animCount(document.getElementById('st-v'), d.vehicles || 0);
    animCount(document.getElementById('st-b'), d.total_requests || 0);
    animCount(document.getElementById('st-c'), d.customers || 0);
  }).catch(() => {});
</script>
</body>
</html>
