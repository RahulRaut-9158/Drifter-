<?php
session_start();
$navActive = 'about';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About — Drifter</title>
<style>
/* ── HERO ── */
.page-hero{padding:100px 24px 80px;text-align:center;color:white;position:relative;overflow:hidden;}
.page-hero::before{content:'';position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1601584115197-04ecc0da31d8?w=1920&q=80') center/cover no-repeat;filter:brightness(0.22);}
.page-hero::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(33,37,41,0.95) 0%,rgba(255,107,0,0.45) 100%);}
.page-hero-content{position:relative;z-index:2;}
.page-hero h1{font-size:clamp(2rem,5vw,3rem);font-weight:800;margin-bottom:12px;}
.page-hero p{color:rgba(255,255,255,0.70);font-size:1rem;max-width:540px;margin:0 auto;}

/* ── SHARED ── */
.section{padding:72px 24px;}
.section-inner{max-width:1200px;margin:0 auto;}
.section-head{text-align:center;margin-bottom:52px;}
.tag{display:inline-block;background:rgba(188,159,139,0.12);color:#a08070;font-size:0.75rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:5px 14px;border-radius:50px;margin-bottom:12px;border:1px solid rgba(188,159,139,0.22);}
.section-head h2{font-size:clamp(1.7rem,4vw,2.3rem);font-weight:800;color:#212529;margin-bottom:10px;}
.section-head p{color:#6c757d;max-width:500px;margin:0 auto;font-size:0.95rem;}

/* ── STORY ── */
.about-grid{display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:center;}
.about-img{width:100%;border-radius:14px;box-shadow:0 12px 48px rgba(33,37,41,0.18);}
.about-text .tag{margin-bottom:14px;}
.about-text h3{font-size:1.55rem;font-weight:800;color:#212529;margin-bottom:14px;}
.about-text p{color:#6c757d;line-height:1.8;margin-bottom:12px;font-size:0.95rem;}

/* ── FEATURES ── */
.features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;}
.feature-card{background:white;border-radius:12px;padding:26px;box-shadow:0 2px 12px rgba(33,37,41,0.08);display:flex;gap:16px;align-items:flex-start;border:1px solid #e9ecef;border-left:3px solid transparent;transition:transform 0.22s,box-shadow 0.22s,border-left-color 0.22s;}
.feature-card:hover{transform:translateY(-5px);box-shadow:0 12px 36px rgba(33,37,41,0.12);border-left-color:#FF6B00;}
.feat-icon{width:46px;height:46px;border-radius:11px;flex-shrink:0;background:linear-gradient(135deg,#BC9F8B,#a08070);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#fff;transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1),box-shadow 0.3s;}
.feature-card:hover .feat-icon{transform:scale(1.14) rotate(-6deg);box-shadow:0 4px 14px rgba(188,159,139,0.40);}
.feature-card h4{font-size:0.95rem;font-weight:700;margin-bottom:5px;color:#212529;transition:color 0.2s;}
.feature-card:hover h4{color:#FF6B00;}
.feature-card p{font-size:0.83rem;color:#6c757d;line-height:1.6;}

/* ── STATS ── */
.stats-section{padding:72px 24px;text-align:center;position:relative;overflow:hidden;background:#212529;}
.stats-section::after{content:'';position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?w=1920&q=80') center/cover no-repeat;filter:brightness(0.08);z-index:0;}
.stats-section>*{position:relative;z-index:1;}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;max-width:860px;margin:36px auto 0;}
.stat-box{background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.10);border-radius:12px;padding:26px;transition:background 0.22s,transform 0.22s;}
.stat-box:hover{background:rgba(255,107,0,0.12);transform:translateY(-5px);}
.stat-num{font-size:2.6rem;font-weight:800;color:#BC9F8B;}
.stat-label{color:rgba(255,255,255,0.65);margin-top:5px;font-size:0.88rem;}

/* ── SERVICES ── */
.services-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:20px;}
.service-card{background:white;border-radius:12px;padding:26px;text-align:center;box-shadow:0 2px 12px rgba(33,37,41,0.08);border:1px solid #e9ecef;border-top:4px solid var(--card-color,#FF6B00);transition:transform 0.25s,box-shadow 0.25s;}
.service-card:hover{transform:translateY(-7px);box-shadow:0 16px 40px rgba(33,37,41,0.13);}
.service-card .si{font-size:2.4rem;margin-bottom:12px;display:block;transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1);}
.service-card:hover .si{transform:scale(1.18) rotate(-5deg);}
.service-card h3{font-size:1.05rem;font-weight:700;color:#212529;margin-bottom:7px;}
.service-card p{color:#6c757d;font-size:0.84rem;margin-bottom:14px;line-height:1.6;}
.service-card a{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:var(--card-color,#BC9F8B);color:white;border-radius:7px;text-decoration:none;font-size:0.82rem;font-weight:600;transition:filter 0.2s,transform 0.2s;}
.service-card a:hover{filter:brightness(1.10);transform:translateY(-2px);}

@media(max-width:768px){.about-grid{grid-template-columns:1fr;gap:28px;}}
</style>
</head>
<body>

<div class="page-hero">
  <div class="page-hero-content">
    <h1>About Drifter</h1>
    <p>Your all-in-one platform for transport, travel, courier & packers and movers across India.</p>
  </div>
</div>

<!-- OUR STORY -->
<section class="section">
  <div class="section-inner">
    <div class="about-grid">
      <div class="about-text">
        <div class="tag">Our Story</div>
        <h3>Redefining Transportation Since 2025</h3>
        <p>Drifter was built with one goal — to make transportation simple, reliable, and accessible for everyone. Whether you're moving homes, shipping a package, booking a travel vehicle, or transporting goods, Drifter connects you with the right service provider instantly.</p>
        <p>We verify every service provider on our platform to ensure safety, transparency, and quality. Our technology-driven approach gives you real-time pricing, easy booking, and complete peace of mind.</p>
        <p>From individuals to businesses, Drifter serves thousands of customers every day with a growing network of trusted partners across the country.</p>
      </div>
      <img src="https://images.unsplash.com/photo-1601584115197-04ecc0da31d8?w=700" alt="Drifter Transport" class="about-img">
    </div>
  </div>
</section>

<!-- WHY CHOOSE US -->
<section class="section" style="background:#F8F9FA;">
  <div class="section-inner">
    <div class="section-head">
      <div class="tag">Why Choose Us</div>
      <h2>The Drifter Advantage</h2>
      <p>We combine technology with trust to deliver exceptional service every time.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card"><div class="feat-icon"><i class="fas fa-shield-alt"></i></div><div><h4>Verified Providers</h4><p>Every driver and company is background-checked and verified before listing.</p></div></div>
      <div class="feature-card"><div class="feat-icon"><i class="fas fa-clock"></i></div><div><h4>On-Time Guarantee</h4><p>98% on-time rate across all services. We value your time as much as you do.</p></div></div>
      <div class="feature-card"><div class="feat-icon"><i class="fas fa-rupee-sign"></i></div><div><h4>Transparent Pricing</h4><p>No hidden charges. See the full price before you confirm any booking.</p></div></div>
      <div class="feature-card"><div class="feat-icon"><i class="fas fa-headset"></i></div><div><h4>24/7 Support</h4><p>Our support team is always available to help you with any issue.</p></div></div>
      <div class="feature-card"><div class="feat-icon"><i class="fas fa-route"></i></div><div><h4>Nationwide Network</h4><p>Partners in every major city — we cover the whole country.</p></div></div>
      <div class="feature-card"><div class="feat-icon"><i class="fas fa-leaf"></i></div><div><h4>Eco-Friendly</h4><p>We're actively transitioning to electric and hybrid vehicles.</p></div></div>
    </div>
  </div>
</section>

<!-- STATS -->
<div class="stats-section">
  <div class="section-head" style="margin-bottom:0;">
    <div class="tag" style="background:rgba(255,193,7,0.15);color:#FFC107;border-color:rgba(255,193,7,0.30);">By The Numbers</div>
    <h2 style="color:white;">Drifter In Numbers</h2>
  </div>
  <div class="stats-grid">
    <div class="stat-box"><div class="stat-num">500+</div><div class="stat-label">Fleet Vehicles</div></div>
    <div class="stat-box"><div class="stat-num">50K+</div><div class="stat-label">Happy Customers</div></div>
    <div class="stat-box"><div class="stat-num">100+</div><div class="stat-label">Cities Covered</div></div>
    <div class="stat-box"><div class="stat-num">24/7</div><div class="stat-label">Customer Support</div></div>
  </div>
</div>

<!-- SERVICES -->
<section class="section">
  <div class="section-inner">
    <div class="section-head">
      <div class="tag">What We Offer</div>
      <h2>Our Services</h2>
      <p>Everything you need for transportation, all in one place.</p>
    </div>
    <div class="services-grid">
      <div class="service-card" style="--card-color:#FF6B00;">
        <div class="si">🚚</div><h3>Transport</h3>
        <p>Reliable goods transportation for all cargo sizes with verified drivers.</p>
        <a href="<?= isset($_SESSION['loggedin']) ? BASE.'/transport/booking_step1.php' : BASE.'/front/login.php?redirect='.urlencode(BASE.'/transport/booking_step1.php') ?>">Book Now <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="service-card" style="--card-color:#FFC107;">
        <div class="si">🚌</div><h3>Travel</h3>
        <p>Comfortable travel vehicles for daily commutes and long-distance trips.</p>
        <a href="<?= isset($_SESSION['loggedin']) ? BASE.'/travel/booking_step1.php' : BASE.'/front/login.php?redirect='.urlencode(BASE.'/travel/booking_step1.php') ?>">Book Now <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="service-card" style="--card-color:#ADB5BD;">
        <div class="si">📦</div><h3>Courier</h3>
        <p>Fast and secure package delivery with same-day and scheduled options.</p>
        <a href="<?= isset($_SESSION['loggedin']) ? BASE.'/courier/courier.php' : BASE.'/front/login.php?redirect='.urlencode(BASE.'/courier/courier.php') ?>">Send Now <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="service-card" style="--card-color:#e05e00;">
        <div class="si">🏠</div><h3>Packers & Movers</h3>
        <p>Professional home and office relocation with full packing service.</p>
        <a href="<?= isset($_SESSION['loggedin']) ? BASE.'/move/movers.php' : BASE.'/front/login.php?redirect='.urlencode(BASE.'/move/movers.php') ?>">Get Quote <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>
