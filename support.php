<?php
require_once 'config.php';
require_once 'includes/db.php';
$navActive = 'support';

// Handle AJAX form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $service = trim($_POST['service'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }

    try {
        $conn = db();
        $stmt = $conn->prepare("INSERT INTO support_messages (name, email, phone, service, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $service, $message);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully! We\'ll respond within 24 hours.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again.']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Support & Contact — Drifter Transport Services</title>
<meta name="description" content="Get help and support from Drifter's team. Contact us via phone, email, or our support form.">
<?php include 'includes/navbar.php'; ?>
<style>
/* ── HERO ── */
.support-hero{
  padding:90px 24px 70px;text-align:center;
  background:var(--gradient-primary);
  position:relative;overflow:hidden;
}
.support-hero::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 30% 60%,rgba(249,115,22,0.20) 0%,transparent 55%);
}
.support-hero::after{
  content:'';position:absolute;bottom:-2px;left:0;right:0;height:70px;
  background:var(--bg-light);clip-path:ellipse(55% 100% at 50% 100%);
}
.support-hero>*{position:relative;z-index:1;}
.support-hero h1{font-size:clamp(1.8rem,4vw,2.6rem);font-weight:800;color:white;margin-bottom:12px;}
.support-hero h1 span{color:var(--accent);}
.support-hero p{color:rgba(255,255,255,0.65);font-size:1rem;max-width:500px;margin:0 auto;}

/* ── CONTACT CARDS ── */
.contact-cards-section{padding:60px 24px 40px;}
.contact-cards{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
  gap:20px;max-width:1000px;margin:0 auto;
}
.contact-card{
  background:white;border-radius:16px;padding:28px 20px;
  text-align:center;box-shadow:var(--shadow-md);
  transition:var(--trans);border:2px solid transparent;
  cursor:pointer;
}
.contact-card:hover{transform:translateY(-6px);border-color:var(--accent);box-shadow:0 16px 40px rgba(249,115,22,0.15);}
.contact-card-icon{
  width:64px;height:64px;border-radius:50%;
  background:var(--gradient-accent);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 16px;font-size:1.5rem;color:white;
  box-shadow:0 6px 20px rgba(249,115,22,0.35);
  transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
.contact-card:hover .contact-card-icon{transform:scale(1.12) rotate(-5deg);}
.contact-card h3{font-size:1rem;font-weight:700;color:var(--text-dark);margin-bottom:6px;}
.contact-card p{font-size:0.85rem;color:var(--text-light);line-height:1.6;}
.contact-card a{color:var(--accent);font-weight:600;font-size:0.85rem;}

/* ── MAIN CONTENT ── */
.support-main{padding:20px 24px 80px;}
.support-inner{max-width:1100px;margin:0 auto;display:grid;grid-template-columns:1fr 1.4fr;gap:48px;align-items:start;}

/* FAQ */
.faq-section h2{font-size:1.4rem;font-weight:800;color:var(--text-dark);margin-bottom:24px;}
.faq-item{
  background:white;border-radius:12px;margin-bottom:12px;
  box-shadow:var(--shadow-sm);border:1px solid var(--border);
  overflow:hidden;transition:var(--trans);
}
.faq-item:hover{box-shadow:var(--shadow-md);}
.faq-question{
  padding:16px 20px;cursor:pointer;
  display:flex;justify-content:space-between;align-items:center;
  font-weight:600;font-size:0.9rem;color:var(--text-dark);
  transition:background 0.2s;
}
.faq-question:hover{background:rgba(249,115,22,0.04);}
.faq-question.active{color:var(--accent);}
.faq-question i{transition:transform 0.3s;color:var(--accent);flex-shrink:0;}
.faq-question.active i{transform:rotate(180deg);}
.faq-answer{
  max-height:0;overflow:hidden;
  transition:max-height 0.35s ease,padding 0.35s ease;
  font-size:0.87rem;color:var(--text-light);line-height:1.7;
  padding:0 20px;
}
.faq-answer.open{max-height:200px;padding:0 20px 16px;}

/* Contact Form */
.contact-form-card{
  background:white;border-radius:20px;padding:36px;
  box-shadow:var(--shadow-lg);border:1px solid var(--border);
}
.contact-form-card h2{font-size:1.4rem;font-weight:800;color:var(--text-dark);margin-bottom:6px;}
.contact-form-card p{color:var(--text-light);font-size:0.87rem;margin-bottom:28px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.form-group{margin-bottom:18px;}
.form-group label{
  display:block;font-size:0.78rem;font-weight:700;
  color:var(--text-dark);margin-bottom:7px;
  letter-spacing:0.5px;text-transform:uppercase;
}
.form-group input,.form-group select,.form-group textarea{
  width:100%;padding:12px 14px;
  border:1.5px solid var(--border);border-radius:10px;
  background:#f8fafc;color:var(--text-dark);font-size:0.9rem;
  transition:var(--trans);outline:none;font-family:inherit;
}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{
  border-color:var(--accent);background:white;
  box-shadow:0 0 0 3px rgba(249,115,22,0.12);
}
.form-group textarea{resize:vertical;min-height:120px;}
.form-group select{cursor:pointer;}
.submit-btn{
  width:100%;padding:14px;border:none;border-radius:10px;cursor:pointer;
  background:var(--gradient-accent);color:white;
  font-size:0.95rem;font-weight:700;font-family:inherit;
  box-shadow:0 4px 16px rgba(249,115,22,0.40);
  transition:var(--trans);display:flex;align-items:center;justify-content:center;gap:8px;
}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(249,115,22,0.55);filter:brightness(1.06);}
.submit-btn:disabled{opacity:0.65;cursor:not-allowed;transform:none;}
.form-alert{
  padding:12px 16px;border-radius:10px;font-size:0.87rem;
  margin-bottom:18px;display:none;align-items:center;gap:8px;
  animation:slideDown 0.3s ease;
}
@keyframes slideDown{from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:none;}}
.form-alert.success{background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.30);color:#14532d;display:flex;}
.form-alert.error{background:rgba(239,68,68,0.10);border:1px solid rgba(239,68,68,0.25);color:#991b1b;display:flex;}

/* Hours */
.hours-card{
  background:var(--gradient-primary);border-radius:16px;padding:24px;
  margin-top:20px;
}
.hours-card h4{color:var(--accent);font-size:0.78rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:14px;}
.hours-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.08);font-size:0.85rem;}
.hours-row:last-child{border-bottom:none;}
.hours-row span:first-child{color:rgba(255,255,255,0.60);}
.hours-row span:last-child{color:white;font-weight:600;}

@media(max-width:768px){
  .support-inner{grid-template-columns:1fr;}
  .form-row{grid-template-columns:1fr;}
  .contact-form-card{padding:24px;}
}
</style>
</head>
<body>

<!-- HERO -->
<section class="support-hero">
  <div class="tag"><i class="fas fa-headset"></i> Support Center</div>
  <h1>How Can We <span>Help You?</span></h1>
  <p>Our dedicated support team is here to assist you with any questions, issues, or feedback about our services.</p>
</section>

<!-- CONTACT CARDS -->
<section class="contact-cards-section">
  <div class="contact-cards">
    <div class="contact-card reveal" onclick="window.location='tel:+919529212771'">
      <div class="contact-card-icon"><i class="fas fa-phone"></i></div>
      <h3>Call Us</h3>
      <p>Mon–Sat, 8 AM – 8 PM</p>
      <a href="tel:+919529212771">+91 95292 12771</a>
    </div>
    <div class="contact-card reveal reveal-delay-1" onclick="window.location='mailto:info@drifter.com'">
      <div class="contact-card-icon"><i class="fas fa-envelope"></i></div>
      <h3>Email Us</h3>
      <p>Response within 24 hours</p>
      <a href="mailto:info@drifter.com">info@drifter.com</a>
    </div>
    <div class="contact-card reveal reveal-delay-2">
      <div class="contact-card-icon"><i class="fas fa-map-marker-alt"></i></div>
      <h3>Visit Us</h3>
      <p>KBPCOES, Satara</p>
      <a href="#">Maharashtra — 415004</a>
    </div>
    <div class="contact-card reveal reveal-delay-3">
      <div class="contact-card-icon"><i class="fab fa-whatsapp"></i></div>
      <h3>WhatsApp</h3>
      <p>Quick responses</p>
      <a href="https://wa.me/919529212771" target="_blank">Chat Now</a>
    </div>
  </div>
</section>

<!-- MAIN: FAQ + FORM -->
<section class="support-main">
  <div class="support-inner">

    <!-- FAQ -->
    <div class="faq-section reveal">
      <h2><i class="fas fa-question-circle" style="color:var(--accent);margin-right:8px;"></i>Frequently Asked Questions</h2>

      <?php
      $faqs = [
        ['How do I book a transport vehicle?', 'Go to Services → Transport Goods, enter your pickup city, destination, date, and distance. Then select a vehicle from available options and confirm your booking.'],
        ['Can I cancel my booking?', 'Yes, you can cancel a pending booking from your Customer Dashboard. Cancellations are free if done before the vehicle owner confirms.'],
        ['How do I register my vehicle?', 'Sign up as a Vehicle Owner, then go to My Vehicles → Add Transport/Travel Vehicle. Fill in your vehicle details, upload images, and submit.'],
        ['What payment methods are accepted?', 'Currently, payments are handled directly between customers and service providers. Online payment integration is coming soon.'],
        ['How are service providers verified?', 'All vehicle owners and companies go through a verification process including license verification, identity check, and document review.'],
        ['How do I track my booking status?', 'Login to your Customer Dashboard to see real-time status updates for all your bookings — Pending, Confirmed, or Cancelled.'],
        ['Can I register as both a customer and owner?', 'Each account has one role. You can create separate accounts for different roles using different email addresses.'],
      ];
      foreach ($faqs as $i => $faq): ?>
      <div class="faq-item">
        <div class="faq-question" onclick="toggleFaq(this)">
          <span><?= htmlspecialchars($faq[0]) ?></span>
          <i class="fas fa-chevron-down"></i>
        </div>
        <div class="faq-answer"><?= htmlspecialchars($faq[1]) ?></div>
      </div>
      <?php endforeach; ?>

      <!-- Hours -->
      <div class="hours-card reveal">
        <h4><i class="fas fa-clock"></i> Support Hours</h4>
        <div class="hours-row"><span>Monday – Friday</span><span>8:00 AM – 8:00 PM</span></div>
        <div class="hours-row"><span>Saturday</span><span>9:00 AM – 6:00 PM</span></div>
        <div class="hours-row"><span>Sunday</span><span>10:00 AM – 4:00 PM</span></div>
        <div class="hours-row"><span>Emergency</span><span style="color:var(--accent)">24/7 WhatsApp</span></div>
      </div>
    </div>

    <!-- Contact Form -->
    <div class="contact-form-card reveal reveal-delay-1">
      <h2>Send Us a Message</h2>
      <p>Fill out the form below and we'll get back to you as soon as possible.</p>

      <div id="formAlert" class="form-alert"></div>

      <form id="contactForm">
        <div class="form-row">
          <div class="form-group">
            <label>Full Name *</label>
            <input type="text" name="name" placeholder="Your full name" required>
          </div>
          <div class="form-group">
            <label>Phone Number</label>
            <input type="tel" name="phone" placeholder="+91 XXXXX XXXXX">
          </div>
        </div>

        <div class="form-group">
          <label>Email Address *</label>
          <input type="email" name="email" placeholder="your@email.com" required>
        </div>

        <div class="form-group">
          <label>Service Type</label>
          <select name="service">
            <option value="">Select a service (optional)</option>
            <option value="transport">Transport Goods</option>
            <option value="travel">Travel & Ride</option>
            <option value="courier">Courier Services</option>
            <option value="movers">Packers & Movers</option>
            <option value="account">Account Issue</option>
            <option value="payment">Payment Issue</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div class="form-group">
          <label>Message *</label>
          <textarea name="message" placeholder="Describe your query or issue in detail..." required></textarea>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">
          <i class="fas fa-paper-plane"></i> Send Message
        </button>
      </form>
    </div>

  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
// FAQ toggle
function toggleFaq(el) {
  const answer = el.nextElementSibling;
  const isOpen = answer.classList.contains('open');
  document.querySelectorAll('.faq-answer').forEach(a => a.classList.remove('open'));
  document.querySelectorAll('.faq-question').forEach(q => q.classList.remove('active'));
  if (!isOpen) { answer.classList.add('open'); el.classList.add('active'); }
}

// Contact form AJAX
document.getElementById('contactForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('submitBtn');
  const alert = document.getElementById('formAlert');
  const formData = new FormData(this);
  formData.append('ajax', '1');

  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
  btn.disabled = true;
  alert.className = 'form-alert';
  alert.style.display = 'none';

  try {
    const res = await fetch('support.php', { method: 'POST', body: formData });
    const data = await res.json();

    alert.className = 'form-alert ' + (data.success ? 'success' : 'error');
    alert.innerHTML = `<i class="fas fa-${data.success ? 'check-circle' : 'exclamation-circle'}"></i> ${data.message}`;

    if (data.success) {
      this.reset();
      if (window.showToast) showToast(data.message, 'success');
    }
  } catch (err) {
    alert.className = 'form-alert error';
    alert.innerHTML = '<i class="fas fa-exclamation-circle"></i> Something went wrong. Please try again.';
  }

  btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
  btn.disabled = false;
});
</script>
</body>
</html>
