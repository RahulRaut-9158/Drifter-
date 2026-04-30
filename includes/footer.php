<?php require_once __DIR__ . '/../config.php'; ?>
<style>
.drifter-footer{
  background:linear-gradient(135deg,#0a1628 0%,#0f2b5e 100%);
  color:rgba(255,255,255,0.55);
  padding:72px 24px 28px;
  margin-top:80px;
  position:relative;
  overflow:hidden;
}
.drifter-footer::before{
  content:'';position:absolute;top:-60px;right:-60px;
  width:300px;height:300px;border-radius:50%;
  background:radial-gradient(circle,rgba(249,115,22,0.12) 0%,transparent 70%);
  pointer-events:none;
}
.drifter-footer::after{
  content:'';position:absolute;bottom:-40px;left:-40px;
  width:200px;height:200px;border-radius:50%;
  background:radial-gradient(circle,rgba(249,115,22,0.08) 0%,transparent 70%);
  pointer-events:none;
}
.f-inner{max-width:1280px;margin:0 auto;position:relative;z-index:1;}
.f-grid{
  display:grid;
  grid-template-columns:1.4fr repeat(3,1fr);
  gap:44px;
  margin-bottom:44px;
}
.f-brand{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
.f-brand-icon{
  width:38px;height:38px;
  background:linear-gradient(135deg,#f97316,#fb923c);
  border-radius:10px;display:flex;align-items:center;justify-content:center;
  font-weight:800;color:white;font-size:1rem;
  transition:transform 0.28s cubic-bezier(0.34,1.56,0.64,1),box-shadow 0.28s;
  flex-shrink:0;box-shadow:0 3px 12px rgba(249,115,22,0.40);
}
.f-brand:hover .f-brand-icon{transform:rotate(-8deg) scale(1.1);box-shadow:0 6px 20px rgba(249,115,22,0.60);}
.f-brand-name{font-size:1.2rem;font-weight:800;color:white;letter-spacing:1px;}
.f-desc{font-size:0.85rem;line-height:1.75;color:rgba(255,255,255,0.45);max-width:260px;}
.f-social{display:flex;gap:10px;margin-top:18px;}
.f-social a{
  width:36px;height:36px;border-radius:9px;
  background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.10);
  display:flex;align-items:center;justify-content:center;
  color:rgba(255,255,255,0.50);font-size:0.85rem;
  transition:background 0.2s,color 0.2s,transform 0.2s,box-shadow 0.2s;
}
.f-social a:hover{
  background:linear-gradient(135deg,#f97316,#fb923c);
  color:white;transform:translateY(-3px);
  box-shadow:0 6px 16px rgba(249,115,22,0.40);
  border-color:transparent;
}

.drifter-footer h4{
  color:#fb923c;font-size:0.78rem;font-weight:700;
  margin-bottom:16px;letter-spacing:1.5px;text-transform:uppercase;
}
.drifter-footer ul{list-style:none;display:flex;flex-direction:column;gap:9px;}
.drifter-footer ul li a{
  color:rgba(255,255,255,0.45);font-size:0.85rem;
  display:inline-flex;align-items:center;gap:7px;
  transition:color 0.2s,padding-left 0.2s;
}
.drifter-footer ul li a:hover{color:#fb923c;padding-left:4px;}
.drifter-footer ul li a i{color:#f97316;width:14px;flex-shrink:0;}

.f-contact li{font-size:0.85rem;display:flex;align-items:center;gap:8px;color:rgba(255,255,255,0.45);margin-bottom:9px;}
.f-contact a{color:rgba(255,255,255,0.45);transition:color 0.2s;}
.f-contact a:hover{color:#fb923c;}
.f-contact i{color:#f97316;width:14px;flex-shrink:0;}

.f-bottom{
  border-top:1px solid rgba(255,255,255,0.07);
  padding-top:22px;
  display:flex;justify-content:space-between;align-items:center;
  flex-wrap:wrap;gap:10px;
  font-size:0.78rem;color:rgba(255,255,255,0.28);
}
.f-bottom a{color:#fb923c;}
.f-bottom a:hover{text-decoration:underline;}

/* Newsletter strip */
.f-newsletter{
  background:rgba(249,115,22,0.08);
  border:1px solid rgba(249,115,22,0.18);
  border-radius:14px;padding:24px 28px;
  display:flex;align-items:center;justify-content:space-between;
  gap:20px;margin-bottom:44px;flex-wrap:wrap;
}
.f-newsletter-text h4{color:white;font-size:1rem;font-weight:700;margin-bottom:4px;}
.f-newsletter-text p{color:rgba(255,255,255,0.45);font-size:0.83rem;}
.f-newsletter-form{display:flex;gap:10px;flex-wrap:wrap;}
.f-newsletter-form input{
  padding:10px 16px;border-radius:8px;border:1px solid rgba(249,115,22,0.25);
  background:rgba(255,255,255,0.06);color:white;font-size:0.85rem;
  min-width:220px;outline:none;transition:border-color 0.2s,background 0.2s;
}
.f-newsletter-form input::placeholder{color:rgba(255,255,255,0.30);}
.f-newsletter-form input:focus{border-color:#f97316;background:rgba(249,115,22,0.08);}
.f-newsletter-form button{
  padding:10px 20px;border-radius:8px;border:none;cursor:pointer;
  background:linear-gradient(135deg,#f97316,#fb923c);
  color:white;font-weight:700;font-size:0.85rem;
  transition:transform 0.2s,box-shadow 0.2s;
  box-shadow:0 3px 10px rgba(249,115,22,0.35);
}
.f-newsletter-form button:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(249,115,22,0.50);}

@media(max-width:900px){
  .f-grid{grid-template-columns:1fr 1fr;gap:32px;}
  .f-newsletter{flex-direction:column;text-align:center;}
}
@media(max-width:560px){
  .f-grid{grid-template-columns:1fr;}
  .f-bottom{flex-direction:column;text-align:center;}
  .f-newsletter-form{flex-direction:column;width:100%;}
  .f-newsletter-form input{min-width:unset;width:100%;}
}
</style>

<footer class="drifter-footer">
  <div class="f-inner">

    <!-- Newsletter -->
    <div class="f-newsletter reveal">
      <div class="f-newsletter-text">
        <h4><i class="fas fa-bell" style="color:#f97316;margin-right:8px;"></i>Stay Updated</h4>
        <p>Get the latest transport deals and service updates directly in your inbox.</p>
      </div>
      <div class="f-newsletter-form">
        <input type="email" placeholder="Enter your email address" id="footerEmail">
        <button onclick="subscribeNewsletter()"><i class="fas fa-paper-plane"></i> Subscribe</button>
      </div>
    </div>

    <div class="f-grid">
      <div class="reveal">
        <div class="f-brand">
          <div class="f-brand-icon">D</div>
          <span class="f-brand-name">DRIFTER</span>
        </div>
        <p class="f-desc">Your all-in-one platform for transport, travel, courier &amp; packers and movers across India. Verified partners, transparent pricing.</p>
        <div class="f-social">
          <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

      <div class="reveal reveal-delay-1">
        <h4>Services</h4>
        <ul>
          <li><a href="<?= BASE ?>/transport/booking_step1.php"><i class="fas fa-truck-moving"></i> Transport Goods</a></li>
          <li><a href="<?= BASE ?>/travel/booking_step1.php"><i class="fas fa-bus"></i> Travel / Ride</a></li>
          <li><a href="<?= BASE ?>/courier/courier.php"><i class="fas fa-box"></i> Courier</a></li>
          <li><a href="<?= BASE ?>/move/movers.php"><i class="fas fa-people-carry"></i> Packers &amp; Movers</a></li>
        </ul>
      </div>

      <div class="reveal reveal-delay-2">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="<?= BASE ?>/index.php"><i class="fas fa-home"></i> Home</a></li>
          <li><a href="<?= BASE ?>/about.php"><i class="fas fa-info-circle"></i> About Us</a></li>
          <li><a href="<?= BASE ?>/support.php"><i class="fas fa-headset"></i> Support</a></li>
          <li><a href="<?= BASE ?>/signup.php"><i class="fas fa-user-plus"></i> Register</a></li>
          <li><a href="<?= BASE ?>/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
        </ul>
      </div>

      <div class="reveal reveal-delay-3">
        <h4>Contact</h4>
        <ul class="f-contact">
          <li><i class="fas fa-map-marker-alt"></i> KBPCOES, Satara — 415004</li>
          <li><i class="fas fa-phone"></i><a href="tel:+919529212771">+91 95292 12771</a></li>
          <li><i class="fas fa-envelope"></i><a href="mailto:info@drifter.com">info@drifter.com</a></li>
          <li><i class="fas fa-clock"></i> Mon–Sat: 8 AM – 8 PM</li>
        </ul>
      </div>
    </div>

    <div class="f-bottom">
      <span>&copy; <?= date('Y') ?> <a href="<?= BASE ?>/index.php">Drifter Transport Services</a>. All rights reserved.</span>
      <span>Made with <span style="color:#f97316">♥</span> in India</span>
    </div>
  </div>
</footer>

<script>
function subscribeNewsletter() {
  const email = document.getElementById('footerEmail').value.trim();
  if (!email || !email.includes('@')) {
    if (window.showToast) showToast('Please enter a valid email address.', 'error');
    return;
  }
  if (window.showToast) showToast('Thank you for subscribing! 🎉', 'success');
  document.getElementById('footerEmail').value = '';
}
</script>
