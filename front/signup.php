<?php
session_start();
if (isset($_SESSION['loggedin'])) { header('Location: /Drifter/front/index.php'); exit; }

$signupError = '';
if (isset($_POST['submit'])) {
    require_once '../config.php';
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $conn = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME,(int)DB_PORT);
        $conn->set_charset('utf8mb4');

        $username  = trim($_POST['user'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['pass'] ?? '';
        $cpassword = $_POST['cpass'] ?? '';
        $role      = in_array($_POST['role'] ?? '', ['customer','owner','company']) ? $_POST['role'] : 'customer';
        $phone     = trim($_POST['phone'] ?? '');

        if (strlen($username) < 3)                          $signupError = 'Username must be at least 3 characters.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $signupError = 'Invalid email format.';
        elseif (strlen($password) < 6)                      $signupError = 'Password must be at least 6 characters.';
        elseif ($password !== $cpassword)                   $signupError = 'Passwords do not match.';
        else {
            $stmt = $conn->prepare('SELECT id FROM signup WHERE username=? OR email=?');
            $stmt->bind_param('ss', $username, $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $signupError = 'Username or email already exists.';
            } else {
                $hash  = password_hash($password, PASSWORD_DEFAULT);
                $stmt2 = $conn->prepare('INSERT INTO signup (username,email,password,role,phone) VALUES (?,?,?,?,?)');
                $stmt2->bind_param('sssss', $username, $email, $hash, $role, $phone);
                if ($stmt2->execute()) {
                    $conn->close();
                    header('Location: login.php?registered=1'); exit;
                }
                $signupError = 'Registration failed. Please try again.';
            }
        }
        $conn->close();
    } catch (mysqli_sql_exception $e) {
        $signupError = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign Up — Drifter</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
body{min-height:100vh;font-family:'Inter',sans-serif;display:flex;position:relative;overflow-x:hidden;background:#111;}

/* BG */
.bg-panel{position:fixed;inset:0;background:url('https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=1920&q=85') center/cover no-repeat;filter:brightness(0.40) saturate(0.8);z-index:0;}
.bg-panel::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(21,21,21,0.88) 0%,rgba(255,107,0,0.32) 60%,rgba(255,193,7,0.12) 100%);}

/* BLOBS */
.blob{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;z-index:1;}
.blob-1{width:520px;height:520px;background:rgba(255,107,0,0.18);top:-130px;left:-110px;animation:bf 9s ease-in-out infinite;}
.blob-2{width:400px;height:400px;background:rgba(255,193,7,0.14);bottom:-100px;right:-100px;animation:bf 12s ease-in-out infinite reverse;}
.blob-3{width:260px;height:260px;background:rgba(255,107,0,0.09);top:38%;right:18%;animation:bf 7s ease-in-out infinite 1.5s;}
@keyframes bf{0%,100%{transform:translate(0,0) scale(1);}50%{transform:translate(20px,-20px) scale(1.08);}}

/* WRAP */
.page-wrap{position:relative;z-index:2;width:100%;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:28px 20px;}

/* CARD */
.card{
  width:100%;max-width:500px;
  background:rgba(255,255,255,0.06);
  backdrop-filter:blur(28px) saturate(1.6);
  -webkit-backdrop-filter:blur(28px) saturate(1.6);
  border:1px solid rgba(255,107,0,0.28);
  border-radius:20px;padding:40px 38px;
  box-shadow:0 20px 60px rgba(0,0,0,0.55),0 1px 0 rgba(255,255,255,0.07) inset;
  animation:cardIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
}
@keyframes cardIn{from{opacity:0;transform:translateY(28px) scale(0.97);}to{opacity:1;transform:none;}}

/* LOGO */
.logo{text-align:center;margin-bottom:22px;}
.logo-icon{width:56px;height:56px;margin:0 auto 10px;background:linear-gradient(135deg,#FF6B00,#FFC107);border-radius:15px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:#fff;box-shadow:0 4px 20px rgba(255,107,0,0.50);animation:pulse 3s ease-in-out infinite;}
@keyframes pulse{0%,100%{box-shadow:0 4px 20px rgba(255,107,0,0.50);}50%{box-shadow:0 6px 32px rgba(255,107,0,0.80);}}
.logo h1{font-size:1.5rem;font-weight:800;color:#fff;letter-spacing:2px;}
.logo p{color:rgba(255,255,255,0.45);font-size:0.82rem;margin-top:3px;}

/* ERROR */
.alert-error{background:rgba(239,68,68,0.14);border:1px solid rgba(239,68,68,0.35);color:#fca5a5;padding:11px 14px;border-radius:10px;font-size:0.83rem;margin-bottom:16px;display:flex;align-items:center;gap:8px;animation:slideDown 0.3s ease both;}
@keyframes slideDown{from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:none;}}

/* ROLE */
.role-label{font-size:0.72rem;font-weight:700;color:rgba(255,255,255,0.55);margin-bottom:8px;letter-spacing:1px;text-transform:uppercase;}
.role-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:18px;}
.role-card{border:1.5px solid rgba(255,255,255,0.12);border-radius:12px;padding:14px 8px;text-align:center;cursor:pointer;background:rgba(255,255,255,0.05);transition:border-color 0.2s,background 0.2s,transform 0.25s,box-shadow 0.25s;position:relative;}
.role-card input{position:absolute;opacity:0;width:0;height:0;}
.role-card .ri{font-size:1.5rem;margin-bottom:5px;display:block;transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1);}
.role-card .rl{font-size:0.75rem;font-weight:700;color:rgba(255,255,255,0.80);display:block;}
.role-card .rs{font-size:0.64rem;color:rgba(255,255,255,0.38);display:block;margin-top:2px;}
.role-card:hover{border-color:#FF6B00;background:rgba(255,107,0,0.12);transform:translateY(-3px);box-shadow:0 8px 20px rgba(255,107,0,0.22);}
.role-card:hover .ri{transform:scale(1.2) rotate(-5deg);}
.role-card.selected{border-color:#FF6B00;background:rgba(255,107,0,0.18);box-shadow:0 0 0 3px rgba(255,107,0,0.20);}
.role-card.selected .rl{color:#FFC107;}
.role-card.selected .ri{transform:scale(1.12);}

/* FORM */
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.form-group{margin-bottom:13px;}
.form-group label{display:block;font-size:0.72rem;font-weight:700;color:rgba(255,255,255,0.55);margin-bottom:6px;letter-spacing:1px;text-transform:uppercase;}
.iw{position:relative;}
.iw .ic{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#FF6B00;font-size:0.84rem;pointer-events:none;transition:color 0.2s;}
.iw:focus-within .ic{color:#FFC107;}
.iw input{width:100%;padding:12px 12px 12px 38px;background:rgba(255,255,255,0.07);border:1.5px solid rgba(255,255,255,0.11);border-radius:10px;color:#fff;font-size:0.91rem;font-family:inherit;outline:none;transition:border-color 0.2s,background 0.2s,box-shadow 0.2s;}
.iw input::placeholder{color:rgba(255,255,255,0.26);}
.iw input:focus{border-color:#FF6B00;background:rgba(255,107,0,0.08);box-shadow:0 0 0 3px rgba(255,107,0,0.18);}
.eye{position:absolute;right:12px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.32);cursor:pointer;font-size:0.84rem;transition:color 0.2s;}
.eye:hover{color:#FFC107;}

/* STRENGTH */
.strength{height:4px;border-radius:2px;background:rgba(255,255,255,0.08);margin-top:5px;overflow:hidden;}
.strength-fill{height:100%;width:0;border-radius:2px;transition:all 0.3s;}

/* BUTTON */
.btn-submit{width:100%;padding:14px;margin-top:8px;background:linear-gradient(135deg,#FF6B00,#e05e00);color:#fff;border:none;border-radius:10px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:inherit;letter-spacing:0.4px;position:relative;overflow:hidden;box-shadow:0 4px 18px rgba(255,107,0,0.45);transition:transform 0.2s,box-shadow 0.2s,filter 0.2s;}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(255,107,0,0.60);filter:brightness(1.08);}
.btn-submit:active{transform:translateY(0);}
.btn-submit:disabled{opacity:0.65;cursor:not-allowed;transform:none;}

/* FOOT */
.foot-link{text-align:center;color:rgba(255,255,255,0.42);font-size:0.84rem;margin-top:16px;}
.foot-link a{color:#FFC107;font-weight:700;text-decoration:none;transition:color 0.2s;}
.foot-link a:hover{color:#FF6B00;}

@keyframes shake{0%,100%{transform:translateX(0);}25%,75%{transform:translateX(-6px);}50%{transform:translateX(6px);}}

@media(max-width:500px){.card{padding:30px 20px;}.form-row{grid-template-columns:1fr;}.role-grid{grid-template-columns:1fr 1fr 1fr;}}
</style>
</head>
<body>
<div class="bg-panel"></div>
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>

<div class="page-wrap">
  <div class="card">
    <div class="logo">
      <div class="logo-icon">D</div>
      <h1>DRIFTER</h1>
      <p>Create your free account</p>
    </div>

    <form action="signup.php" method="POST" id="sf">
      <?php if (!empty($signupError)): ?>
      <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($signupError) ?></div>
      <?php endif; ?>

      <div class="role-label">I am a...</div>
      <div class="role-grid">
        <label class="role-card" id="rc-customer">
          <input type="radio" name="role" value="customer" checked>
          <span class="ri">🧑</span>
          <span class="rl">Customer</span>
          <span class="rs">Book services</span>
        </label>
        <label class="role-card" id="rc-owner">
          <input type="radio" name="role" value="owner">
          <span class="ri">🚚</span>
          <span class="rl">Vehicle Owner</span>
          <span class="rs">List vehicles</span>
        </label>
        <label class="role-card" id="rc-company">
          <input type="radio" name="role" value="company">
          <span class="ri">🏢</span>
          <span class="rl">Company</span>
          <span class="rs">Courier / Movers</span>
        </label>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Username</label>
          <div class="iw"><i class="fas fa-user ic"></i>
            <input type="text" name="user" placeholder="username" required minlength="3"
              value="<?= htmlspecialchars($_POST['user'] ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label>Phone</label>
          <div class="iw"><i class="fas fa-phone ic"></i>
            <input type="text" name="phone" placeholder="10-digit" pattern="[0-9]{10}"
              value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <div class="iw"><i class="fas fa-envelope ic"></i>
          <input type="email" name="email" placeholder="your@email.com" required
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Password</label>
          <div class="iw"><i class="fas fa-lock ic"></i>
            <input type="password" name="pass" id="pw" placeholder="min 6 chars" required minlength="6">
            <i class="fas fa-eye eye" id="ep"></i>
          </div>
          <div class="strength"><div class="strength-fill" id="sf2"></div></div>
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <div class="iw"><i class="fas fa-lock ic"></i>
            <input type="password" name="cpass" id="cp" placeholder="repeat password" required>
            <i class="fas fa-eye eye" id="ec"></i>
          </div>
        </div>
      </div>

      <button type="submit" name="submit" class="btn-submit" id="signupBtn">
        <i class="fas fa-user-plus"></i> Create Account
      </button>
    </form>

    <div class="foot-link">Already have an account? <a href="login.php">Sign in</a></div>
  </div>
</div>

<script>
// Role highlight — sync with checked radio on load
document.querySelectorAll('.role-card').forEach(c => {
  const radio = c.querySelector('input[type=radio]');
  if (radio.checked) c.classList.add('selected');
  c.addEventListener('click', () => {
    document.querySelectorAll('.role-card').forEach(x => x.classList.remove('selected'));
    c.classList.add('selected');
  });
});

// Password toggle
function tog(btnId, fId) {
  document.getElementById(btnId).addEventListener('click', function() {
    const f = document.getElementById(fId);
    f.type = f.type === 'password' ? 'text' : 'password';
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
  });
}
tog('ep','pw'); tog('ec','cp');

// Strength meter
document.getElementById('pw').addEventListener('input', function() {
  const v = this.value, bar = document.getElementById('sf2');
  let s = 0;
  if (v.length >= 6) s++;
  if (/[A-Z]/.test(v)) s++;
  if (/[0-9]/.test(v)) s++;
  if (/[^A-Za-z0-9]/.test(v)) s++;
  bar.style.width = (s * 25) + '%';
  bar.style.background = ['#ef4444','#f97316','#FFC107','#22c55e'][s - 1] || 'transparent';
});

// Submit validation
document.getElementById('sf').addEventListener('submit', function(e) {
  if (document.getElementById('pw').value !== document.getElementById('cp').value) {
    e.preventDefault();
    [document.getElementById('pw'), document.getElementById('cp')].forEach(inp => {
      inp.style.animation = 'none';
      inp.offsetHeight; // reflow
      inp.style.animation = 'shake 0.4s ease';
    });
    return;
  }
  const btn = document.getElementById('signupBtn');
  setTimeout(() => {
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating account...';
    btn.disabled = true;
  }, 0);
});

// Shake on server-side error
<?php if (!empty($signupError)): ?>
document.querySelectorAll('.iw input').forEach(inp => {
  inp.style.animation = 'none';
  inp.offsetHeight;
  inp.style.animation = 'shake 0.4s ease';
});
<?php endif; ?>
</script>
</body>
</html>
