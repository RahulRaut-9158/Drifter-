<?php
session_start();
$navActive = 'support';
include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Support — Drifter</title>
<style>
/* ── HERO ── */
.page-hero{padding:100px 24px 80px;text-align:center;color:white;position:relative;overflow:hidden;}
.page-hero::before{content:'';position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1521791136064-7986c2920216?w=1920&q=80') center/cover no-repeat;filter:brightness(0.20);}
.page-hero::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(33,37,41,0.95) 0%,rgba(255,107,0,0.40) 100%);}
.page-hero-content{position:relative;z-index:2;}
.page-hero h1{font-size:clamp(2rem,5vw,3rem);font-weight:800;margin-bottom:12px;}
.page-hero p{color:rgba(255,255,255,0.70);font-size:1rem;}

/* ── SHARED ── */
.section{padding:72px 24px;}
.section-inner{max-width:1200px;margin:0 auto;}
.section-head{text-align:center;margin-bottom:52px;}
.tag{display:inline-block;background:rgba(188,159,139,0.12);color:#a08070;font-size:0.75rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:5px 14px;border-radius:50px;margin-bottom:12px;border:1px solid rgba(188,159,139,0.22);}
.section-head h2{font-size:clamp(1.7rem,4vw,2.3rem);font-weight:800;color:#212529;margin-bottom:10px;}
.section-head p{color:#6c757d;max-width:500px;margin:0 auto;font-size:0.95rem;}

/* ── CONTACT GRID ── */
.contact-grid{display:grid;grid-template-columns:1fr 1.6fr;gap:28px;}

/* ── INFO CARD ── */
.contact-info{background:white;border-radius:14px;padding:32px;box-shadow:0 2px 12px rgba(33,37,41,0.08);border:1px solid #e9ecef;}
.contact-info h3{font-size:1.1rem;font-weight:700;margin-bottom:24px;color:#212529;padding-bottom:14px;border-bottom:2px solid #F8F9FA;}
.info-item{display:flex;align-items:flex-start;gap:14px;margin-bottom:18px;padding:10px;border-radius:9px;transition:background 0.2s;}
.info-item:hover{background:#F8F9FA;}
.info-icon{width:44px;height:44px;background:linear-gradient(135deg,#BC9F8B,#a08070);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;flex-shrink:0;transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1),box-shadow 0.3s;}
.info-item:hover .info-icon{transform:scale(1.10) rotate(-5deg);box-shadow:0 4px 14px rgba(188,159,139,0.35);}
.info-content h4{font-size:0.86rem;font-weight:700;color:#212529;margin-bottom:3px;}
.info-content p,.info-content a{color:#6c757d;font-size:0.84rem;text-decoration:none;display:block;line-height:1.6;}
.info-content a:hover{color:#BC9F8B;}
.quick-links{margin-top:20px;padding-top:20px;border-top:1px solid #e9ecef;}
.quick-links h4{font-size:0.82rem;font-weight:700;color:#212529;margin-bottom:10px;}
.quick-links a{display:flex;align-items:center;gap:8px;color:#6c757d;font-size:0.83rem;text-decoration:none;padding:7px 8px;border-radius:7px;transition:color 0.2s,background 0.2s,padding-left 0.2s;}
.quick-links a:hover{color:#FF6B00;background:rgba(255,107,0,0.07);padding-left:13px;}
.quick-links a i{color:#FF6B00;width:15px;flex-shrink:0;}

/* ── FORM CARD ── */
.contact-form{background:white;border-radius:14px;padding:32px;box-shadow:0 2px 12px rgba(33,37,41,0.08);border:1px solid #e9ecef;}
.contact-form h3{font-size:1.1rem;font-weight:700;margin-bottom:24px;color:#212529;padding-bottom:14px;border-bottom:2px solid #F8F9FA;}
.form-group{margin-bottom:16px;}
.form-group label{display:block;font-size:0.78rem;font-weight:600;color:#495057;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.3px;}
.form-control{width:100%;padding:11px 14px;border:1.5px solid #e9ecef;border-radius:9px;font-size:0.92rem;font-family:inherit;transition:border-color 0.2s,box-shadow 0.2s;outline:none;background:#F8F9FA;color:#212529;}
.form-control:focus{border-color:#BC9F8B;background:white;box-shadow:0 0 0 3px rgba(188,159,139,0.14);}
.form-control::placeholder{color:#ADB5BD;}
textarea.form-control{min-height:120px;resize:vertical;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.btn-submit{width:100%;padding:13px;background:linear-gradient(135deg,#BC9F8B,#a08070);color:white;border:none;border-radius:9px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:inherit;transition:transform 0.2s,box-shadow 0.2s,filter 0.2s;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 3px 12px rgba(188,159,139,0.30);}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 7px 22px rgba(188,159,139,0.45);filter:brightness(1.06);}
.btn-submit:disabled{opacity:0.65;cursor:not-allowed;transform:none;}

/* ── ALERTS ── */
.success-msg{display:none;background:#dcfce7;color:#166534;border:1px solid #86efac;padding:12px 16px;border-radius:9px;margin-bottom:16px;font-weight:600;font-size:0.87rem;align-items:center;gap:8px;}
.success-msg.show{display:flex;}
.error-msg{display:none;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;padding:12px 16px;border-radius:9px;margin-bottom:16px;font-weight:600;font-size:0.87rem;align-items:center;gap:8px;}
.error-msg.show{display:flex;}

/* ── MAP ── */
.map-wrap{max-width:1200px;margin:0 auto;padding:0 24px 72px;}
.map-wrap h3{font-size:1rem;font-weight:700;color:#212529;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.map-wrap h3 i{color:#FF6B00;}
.map-wrap iframe{width:100%;height:360px;border:none;border-radius:14px;box-shadow:0 2px 12px rgba(33,37,41,0.10);}

@media(max-width:768px){.contact-grid{grid-template-columns:1fr;}.form-row{grid-template-columns:1fr;}}
</style>
</head>
<body>

<div class="page-hero">
  <div class="page-hero-content">
    <h1>Contact & Support</h1>
    <p>We're here to help — reach out anytime</p>
  </div>
</div>

<section class="section">
  <div class="section-inner">
    <div class="section-head">
      <div class="tag">Get In Touch</div>
      <h2>How Can We Help?</h2>
      <p>Have a question? Fill out the form and we'll get back to you within 24 hours.</p>
    </div>
    <div class="contact-grid">

      <div class="contact-info">
        <h3>Contact Information</h3>
        <div class="info-item">
          <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
          <div class="info-content"><h4>Address</h4><p>KBPCOES, Satara - 415004, Maharashtra, India</p></div>
        </div>
        <div class="info-item">
          <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
          <div class="info-content"><h4>Phone</h4><a href="tel:+919529212771">+91 87883940**</a></div>
        </div>
        <div class="info-item">
          <div class="info-icon"><i class="fas fa-envelope"></i></div>
          <div class="info-content"><h4>Email</h4><a href="mailto:info@drifter.com">info@drifter.com</a><a href="mailto:support@drifter.com">support@drifter.com</a></div>
        </div>
        <div class="info-item">
          <div class="info-icon"><i class="fas fa-clock"></i></div>
          <div class="info-content"><h4>Working Hours</h4><p>Mon–Fri: 8:00 AM – 8:00 PM</p><p>Sat: 9:00 AM – 5:00 PM</p><p>Sun: Emergency support only</p></div>
        </div>
        <div class="quick-links">
          <h4>Quick Links</h4>
          <a href="<?= BASE ?>/transport/booking_step1.php"><i class="fas fa-truck-moving"></i> Book Transport</a>
          <a href="<?= BASE ?>/travel/booking_step1.php"><i class="fas fa-bus"></i> Book Travel</a>
          <a href="<?= BASE ?>/courier/courier.php"><i class="fas fa-box"></i> Send Courier</a>
          <a href="<?= BASE ?>/move/movers.php"><i class="fas fa-people-carry"></i> Packers & Movers</a>
        </div>
      </div>

      <div class="contact-form">
        <h3>Send Us a Message</h3>
        <div class="success-msg" id="successMsg"><i class="fas fa-check-circle"></i> <span id="successText"></span></div>
        <div class="error-msg"   id="errorMsg"><i class="fas fa-exclamation-circle"></i> <span id="errorText"></span></div>
        <form id="contactForm">
          <div class="form-row">
            <div class="form-group">
              <label>Your Name *</label>
              <input type="text" class="form-control" id="c_name" name="name" placeholder="Full name" required>
            </div>
            <div class="form-group">
              <label>Phone Number</label>
              <input type="tel" class="form-control" id="c_phone" name="phone" placeholder="+91 XXXXXXXXXX">
            </div>
          </div>
          <div class="form-group">
            <label>Email Address *</label>
            <input type="email" class="form-control" id="c_email" name="email" placeholder="your@email.com" required>
          </div>
          <div class="form-group">
            <label>Service</label>
            <select class="form-control" id="c_service" name="service">
              <option value="">Select a service</option>
              <option>Transport</option>
              <option>Travel</option>
              <option>Courier</option>
              <option>Packers &amp; Movers</option>
              <option>General Inquiry</option>
            </select>
          </div>
          <div class="form-group">
            <label>Message *</label>
            <textarea class="form-control" id="c_message" name="message" placeholder="Describe your issue or question..." required></textarea>
          </div>
          <button type="submit" class="btn-submit" id="contactSubmitBtn">
            <i class="fas fa-paper-plane"></i> Send Message
          </button>
        </form>
      </div>

    </div>
  </div>
</section>

<div class="map-wrap">
  <h3><i class="fas fa-map-marked-alt"></i> Find Us</h3>
  <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d125546.2783947093!2d73.99184589309371!3d17.724353646402452!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1smr!2sin!4v1748430114004!5m2!1smr!2sin" allowfullscreen loading="lazy"></iframe>
</div>

<?php include '../includes/footer.php'; ?>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const btn        = document.getElementById('contactSubmitBtn');
  const successMsg = document.getElementById('successMsg');
  const errorMsg   = document.getElementById('errorMsg');
  successMsg.classList.remove('show');
  errorMsg.classList.remove('show');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

  fetch('<?= BASE ?>/front/api_contact.php', {
    method: 'POST',
    body: new URLSearchParams(new FormData(this))
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      document.getElementById('successText').textContent = d.msg;
      successMsg.classList.add('show');
      this.reset();
      setTimeout(() => successMsg.classList.remove('show'), 6000);
    } else {
      document.getElementById('errorText').textContent = d.msg;
      errorMsg.classList.add('show');
    }
  })
  .catch(() => {
    document.getElementById('errorText').textContent = 'Something went wrong. Please try again.';
    errorMsg.classList.add('show');
  })
  .finally(() => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
  });
});
</script>
</body>
</html>
