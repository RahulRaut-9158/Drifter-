<?php
require_once 'config.php';
$navActive = 'about';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>About Us — Drifter Transport Services</title>
<meta name="description" content="Learn about Drifter — India's leading transport services platform connecting customers with verified vehicle owners, courier companies, and packers & movers.">
<?php include 'includes/navbar.php'; ?>
<style>
/* ── HERO ── */
.about-hero{
  padding:100px 24px 80px;text-align:center;
  background:var(--gradient-primary);
  position:relative;overflow:hidden;
}
.about-hero::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 70% 40%,rgba(249,115,22,0.22) 0%,transparent 55%);
}
.about-hero::after{
  content:'';position:absolute;bottom:-2px;left:0;right:0;height:70px;
  background:var(--bg-light);clip-path:ellipse(55% 100% at 50% 100%);
}
.about-hero>*{position:relative;z-index:1;}
.about-hero .tag{margin-bottom:16px;}
.about-hero h1{
  font-size:clamp(2rem,5vw,3rem);font-weight:800;color:white;
  margin-bottom:16px;line-height:1.2;
}
.about-hero h1 span{
  background:var(--gradient-accent);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.about-hero p{color:rgba(255,255,255,0.65);font-size:1.05rem;max-width:560px;margin:0 auto 32px;}
.hero-stats{
  display:flex;justify-content:center;gap:48px;flex-wrap:wrap;
  margin-top:40px;
}
.hero-stat{text-align:center;}
.hero-stat-num{font-size:2rem;font-weight:800;color:var(--accent);display:block;}
.hero-stat-label{font-size:0.8rem;color:rgba(255,255,255,0.55);text-transform:uppercase;letter-spacing:1px;}

/* ── MISSION ── */
.mission-section{padding:80px 24px;}
.mission-inner{max-width:1100px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;}
.mission-visual{
  background:var(--gradient-primary);
  border-radius:20px;padding:48px;text-align:center;
  position:relative;overflow:hidden;
  box-shadow:0 20px 60px rgba(15,43,94,0.30);
}
.mission-visual::before{
  content:'';position:absolute;top:-30px;right:-30px;
  width:150px;height:150px;border-radius:50%;
  background:rgba(249,115,22,0.15);
}
.mission-icon{font-size:5rem;margin-bottom:16px;position:relative;z-index:1;}
.mission-visual h3{color:white;font-size:1.4rem;font-weight:700;margin-bottom:8px;position:relative;z-index:1;}
.mission-visual p{color:rgba(255,255,255,0.60);font-size:0.9rem;position:relative;z-index:1;}
.mission-content h2{font-size:clamp(1.6rem,3vw,2.2rem);font-weight:800;color:var(--text-dark);margin-bottom:20px;}
.mission-content h2 span{color:var(--accent);}
.mission-content p{color:var(--text-light);line-height:1.8;margin-bottom:16px;font-size:0.95rem;}
.mission-points{list-style:none;margin-top:24px;display:flex;flex-direction:column;gap:12px;}
.mission-points li{
  display:flex;align-items:flex-start;gap:12px;
  padding:12px 16px;background:white;border-radius:10px;
  border-left:3px solid var(--accent);
  box-shadow:var(--shadow-sm);font-size:0.9rem;color:var(--text-dark);
}
.mission-points li i{color:var(--accent);margin-top:2px;flex-shrink:0;}

/* ── VALUES ── */
.values-section{padding:80px 24px;background:white;}
.values-grid{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:24px;max-width:1200px;margin:0 auto;
}
.value-card{
  background:var(--bg-light);border-radius:16px;padding:32px 24px;
  text-align:center;transition:var(--trans);
  border:2px solid transparent;position:relative;overflow:hidden;
}
.value-card::before{
  content:'';position:absolute;inset:0;
  background:var(--gradient-accent);opacity:0;
  transition:opacity 0.3s;border-radius:14px;
}
.value-card:hover{transform:translateY(-6px);border-color:var(--accent);box-shadow:0 16px 40px rgba(249,115,22,0.18);}
.value-card:hover::before{opacity:0.04;}
.value-icon{
  width:72px;height:72px;border-radius:50%;
  background:var(--gradient-accent);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 20px;font-size:1.8rem;color:white;
  box-shadow:0 6px 20px rgba(249,115,22,0.35);
  transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
  position:relative;z-index:1;
}
.value-card:hover .value-icon{transform:scale(1.12) rotate(-5deg);}
.value-card h3{font-size:1.1rem;font-weight:700;color:var(--text-dark);margin-bottom:10px;position:relative;z-index:1;}
.value-card p{color:var(--text-light);font-size:0.88rem;line-height:1.7;position:relative;z-index:1;}

/* ── TEAM ── */
.team-section{padding:80px 24px;}
.team-grid{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
  gap:24px;max-width:1000px;margin:0 auto;
}
.team-card{
  background:white;border-radius:16px;padding:32px 20px;
  text-align:center;box-shadow:var(--shadow-md);
  transition:var(--trans);border:2px solid transparent;
}
.team-card:hover{transform:translateY(-6px);border-color:var(--accent);box-shadow:0 16px 40px rgba(249,115,22,0.15);}
.team-avatar{
  width:80px;height:80px;border-radius:50%;
  background:var(--gradient-primary);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 16px;font-size:2rem;color:white;font-weight:800;
  box-shadow:0 6px 20px rgba(15,43,94,0.30);
  transition:transform 0.3s;
}
.team-card:hover .team-avatar{transform:scale(1.08);}
.team-card h4{font-size:1rem;font-weight:700;color:var(--text-dark);margin-bottom:4px;}
.team-card .role{font-size:0.82rem;color:var(--accent);font-weight:600;margin-bottom:10px;}
.team-card p{font-size:0.83rem;color:var(--text-light);line-height:1.6;}

/* ── WHY CHOOSE ── */
.why-section{padding:80px 24px;background:var(--gradient-primary);position:relative;overflow:hidden;}
.why-section::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 30% 50%,rgba(249,115,22,0.18) 0%,transparent 60%);
}
.why-inner{max-width:1100px;margin:0 auto;position:relative;z-index:1;}
.why-section .section-head h2{color:white;}
.why-section .section-head p{color:rgba(255,255,255,0.60);}
.why-section .tag{background:rgba(249,115,22,0.20);border-color:rgba(249,115,22,0.35);}
.why-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;}
.why-card{
  background:rgba(255,255,255,0.06);
  border:1px solid rgba(255,255,255,0.10);
  border-radius:14px;padding:24px;
  transition:var(--trans);
}
.why-card:hover{background:rgba(249,115,22,0.10);border-color:rgba(249,115,22,0.30);transform:translateY(-4px);}
.why-card i{font-size:1.8rem;color:var(--accent);margin-bottom:14px;display:block;}
.why-card h4{color:white;font-size:1rem;font-weight:700;margin-bottom:8px;}
.why-card p{color:rgba(255,255,255,0.55);font-size:0.87rem;line-height:1.7;}

/* ── CTA ── */
.cta-section{
  padding:80px 24px;text-align:center;
  background:white;
}
.cta-box{
  max-width:600px;margin:0 auto;
  background:var(--gradient-primary);
  border-radius:20px;padding:56px 40px;
  position:relative;overflow:hidden;
  box-shadow:0 20px 60px rgba(15,43,94,0.25);
}
.cta-box::before{
  content:'';position:absolute;top:-40px;right:-40px;
  width:200px;height:200px;border-radius:50%;
  background:rgba(249,115,22,0.15);
}
.cta-box h2{color:white;font-size:1.8rem;font-weight:800;margin-bottom:12px;position:relative;z-index:1;}
.cta-box p{color:rgba(255,255,255,0.65);margin-bottom:28px;position:relative;z-index:1;}
.cta-buttons{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;position:relative;z-index:1;}

@media(max-width:768px){
  .mission-inner{grid-template-columns:1fr;}
  .hero-stats{gap:28px;}
  .cta-box{padding:40px 24px;}
}
</style>
</head>
<body>

<!-- HERO -->
<section class="about-hero">
  <div class="tag"><i class="fas fa-info-circle"></i> About Drifter</div>
  <h1>India's Most Trusted<br><span>Transport Platform</span></h1>
  <p>Connecting customers with verified vehicle owners, courier companies, and packers & movers across India since 2024.</p>

  <div class="hero-stats">
    <div class="hero-stat">
      <span class="hero-stat-num">500+</span>
      <span class="hero-stat-label">Verified Partners</span>
    </div>
    <div class="hero-stat">
      <span class="hero-stat-num">10K+</span>
      <span class="hero-stat-label">Happy Customers</span>
    </div>
    <div class="hero-stat">
      <span class="hero-stat-num">50+</span>
      <span class="hero-stat-label">Cities Covered</span>
    </div>
    <div class="hero-stat">
      <span class="hero-stat-num">99%</span>
      <span class="hero-stat-label">Satisfaction Rate</span>
    </div>
  </div>
</section>

<!-- MISSION -->
<section class="mission-section">
  <div class="mission-inner">
    <div class="mission-visual reveal">
      <div class="mission-icon">🚛</div>
      <h3>Our Mission</h3>
      <p>Making transport accessible, reliable, and affordable for every Indian.</p>
    </div>
    <div class="mission-content reveal reveal-delay-1">
      <h2>We're Building the Future of <span>Transport in India</span></h2>
      <p>Drifter is India's comprehensive transport services platform, designed to connect customers with verified vehicle owners, courier companies, and packers & movers across the country.</p>
      <p>Whether you need to transport goods, book a ride, send a package, or relocate your home or office, Drifter provides a seamless platform to find the right service provider for your needs.</p>
      <ul class="mission-points">
        <li><i class="fas fa-check-circle"></i> Verified and background-checked service providers</li>
        <li><i class="fas fa-check-circle"></i> Transparent pricing with no hidden charges</li>
        <li><i class="fas fa-check-circle"></i> Real-time booking and tracking system</li>
        <li><i class="fas fa-check-circle"></i> 24/7 customer support across all services</li>
      </ul>
    </div>
  </div>
</section>

<!-- VALUES -->
<section class="values-section">
  <div class="section-inner">
    <div class="section-head reveal">
      <div class="tag">Our Values</div>
      <h2>What Drives Us Every Day</h2>
      <p>Our core values shape every decision we make and every service we provide.</p>
    </div>
    <div class="values-grid">
      <div class="value-card reveal">
        <div class="value-icon"><i class="fas fa-shield-alt"></i></div>
        <h3>Trust & Safety</h3>
        <p>All our partners are thoroughly verified with background checks, license verification, and regular audits.</p>
      </div>
      <div class="value-card reveal reveal-delay-1">
        <div class="value-icon"><i class="fas fa-bolt"></i></div>
        <h3>Speed & Reliability</h3>
        <p>We ensure fast bookings, on-time pickups, and reliable delivery across all our service categories.</p>
      </div>
      <div class="value-card reveal reveal-delay-2">
        <div class="value-icon"><i class="fas fa-rupee-sign"></i></div>
        <h3>Fair Pricing</h3>
        <p>No hidden charges. Transparent, upfront pricing based on distance, vehicle type, and service requirements.</p>
      </div>
      <div class="value-card reveal reveal-delay-3">
        <div class="value-icon"><i class="fas fa-headset"></i></div>
        <h3>Customer First</h3>
        <p>Our dedicated support team is available 24/7 to assist you with any queries, issues, or feedback.</p>
      </div>
      <div class="value-card reveal">
        <div class="value-icon"><i class="fas fa-leaf"></i></div>
        <h3>Sustainability</h3>
        <p>We promote eco-friendly transport options and work towards reducing the carbon footprint of logistics.</p>
      </div>
      <div class="value-card reveal reveal-delay-1">
        <div class="value-icon"><i class="fas fa-handshake"></i></div>
        <h3>Partnership</h3>
        <p>We grow together with our service providers, offering tools and support to help them build their business.</p>
      </div>
    </div>
  </div>
</section>

<!-- WHY CHOOSE -->
<section class="why-section">
  <div class="why-inner">
    <div class="section-head reveal">
      <div class="tag">Why Drifter</div>
      <h2>Why Choose Drifter?</h2>
      <p>We're not just a booking platform — we're your complete transport partner.</p>
    </div>
    <div class="why-grid">
      <div class="why-card reveal">
        <i class="fas fa-network-wired"></i>
        <h4>Largest Network</h4>
        <p>Access thousands of verified vehicles and service providers across 50+ cities in India.</p>
      </div>
      <div class="why-card reveal reveal-delay-1">
        <i class="fas fa-mobile-alt"></i>
        <h4>Easy Booking</h4>
        <p>Book any service in under 2 minutes with our streamlined 2-step booking process.</p>
      </div>
      <div class="why-card reveal reveal-delay-2">
        <i class="fas fa-star"></i>
        <h4>Rated & Reviewed</h4>
        <p>All service providers are rated by real customers, ensuring quality and accountability.</p>
      </div>
      <div class="why-card reveal reveal-delay-3">
        <i class="fas fa-lock"></i>
        <h4>Secure Platform</h4>
        <p>Your data and payments are protected with enterprise-grade security and encryption.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="cta-box reveal">
    <h2>Ready to Get Started?</h2>
    <p>Join thousands of customers and service providers on India's most trusted transport platform.</p>
    <div class="cta-buttons">
      <a href="<?= BASE ?>/signup.php" class="btn-primary"><i class="fas fa-user-plus"></i> Create Free Account</a>
      <a href="<?= BASE ?>/support.php" class="btn-outline" style="border-color:rgba(255,255,255,0.40);color:white;"><i class="fas fa-headset"></i> Contact Us</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
