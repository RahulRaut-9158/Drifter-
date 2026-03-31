<?php
session_start();
require_once '../includes/db.php';
$conn = db();

if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $dest = isset($_GET['redirect']) && !empty($_GET['redirect']) ? $_GET['redirect'] : BASE.'/front/index.php';
    header("Location: $dest"); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid request. Please try again.';
    } else {
        $input = trim($_POST['user'] ?? '');
        $pass  = $_POST['pass'] ?? '';
        if ($input === '' || $pass === '') {
            $error = 'Please fill in all fields.';
        } else {
            $stmt = $conn->prepare("SELECT * FROM signup WHERE username=? OR email=?");
            if (!$stmt) {
                $error = 'Server error: ' . $conn->error;
            } else {
                $stmt->bind_param("ss", $input, $input);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                if ($row && password_verify($pass, $row['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['loggedin'] = true;
                    $_SESSION['role']     = $row['role'] ?? 'customer';
                    if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
                        header("Location: ".$_GET['redirect']); exit;
                    }
                    $roleMap = [
                        'customer' => BASE.'/front/dashboard_customer.php',
                        'owner'    => BASE.'/front/your_vehicle_info.php',
                        'company'  => BASE.'/courier/company_info.php'
                    ];
                    header("Location: ".($roleMap[$row['role']] ?? BASE.'/front/index.php')); exit;
                } else {
                    $error = 'Invalid username/email or password.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — Drifter</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
body{min-height:100vh;font-family:'Inter',sans-serif;display:flex;position:relative;overflow:hidden;background:#111;}

/* BG */
.bg-panel{position:fixed;inset:0;background:url('https://images.unsplash.com/photo-1519003722824-194d4455a60c?w=1920&q=85') center/cover no-repeat;filter:brightness(0.45) saturate(0.8);z-index:0;}
.bg-panel::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(21,21,21,0.85) 0%,rgba(255,107,0,0.35) 60%,rgba(255,193,7,0.15) 100%);}

/* BLOBS */
.blob{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;z-index:1;}
.blob-1{width:500px;height:500px;background:rgba(255,107,0,0.18);top:-120px;left:-120px;animation:bf 9s ease-in-out infinite;}
.blob-2{width:400px;height:400px;background:rgba(255,193,7,0.14);bottom:-100px;right:-100px;animation:bf 11s ease-in-out infinite reverse;}
.blob-3{width:250px;height:250px;background:rgba(255,107,0,0.10);top:45%;left:40%;animation:bf 7s ease-in-out infinite 2s;}
@keyframes bf{0%,100%{transform:translate(0,0) scale(1);}50%{transform:translate(22px,-22px) scale(1.09);}}

/* WRAP */
.page-wrap{position:relative;z-index:2;width:100%;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}

/* CARD */
.card{
  width:100%;max-width:420px;
  background:rgba(255,255,255,0.06);
  backdrop-filter:blur(28px) saturate(1.6);
  -webkit-backdrop-filter:blur(28px) saturate(1.6);
  border:1px solid rgba(255,107,0,0.30);
  border-radius:20px;
  padding:44px 40px;
  box-shadow:0 20px 60px rgba(0,0,0,0.50),0 1px 0 rgba(255,255,255,0.08) inset;
  animation:cardIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
}
@keyframes cardIn{from{opacity:0;transform:translateY(28px) scale(0.97);}to{opacity:1;transform:none;}}

/* LOGO */
.logo{text-align:center;margin-bottom:30px;}
.logo-icon{
  width:60px;height:60px;margin:0 auto 12px;
  background:linear-gradient(135deg,#FF6B00,#FFC107);
  border-radius:16px;display:flex;align-items:center;justify-content:center;
  font-size:1.6rem;font-weight:800;color:#fff;
  box-shadow:0 4px 20px rgba(255,107,0,0.50);
  animation:pulse 3s ease-in-out infinite;
}
@keyframes pulse{0%,100%{box-shadow:0 4px 20px rgba(255,107,0,0.50);}50%{box-shadow:0 6px 32px rgba(255,107,0,0.80);}}
.logo h1{font-size:1.6rem;font-weight:800;color:#fff;letter-spacing:2px;}
.logo p{color:rgba(255,255,255,0.50);font-size:0.84rem;margin-top:4px;}

/* ALERTS */
.alert{padding:11px 14px;border-radius:10px;font-size:0.84rem;margin-bottom:18px;display:flex;align-items:center;gap:8px;animation:slideDown 0.3s ease both;}
@keyframes slideDown{from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:none;}}
.alert.error{background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.35);color:#fca5a5;}
.alert.success{background:rgba(255,193,7,0.12);border:1px solid rgba(255,193,7,0.35);color:#FFC107;}

/* LABELS */
.form-group{margin-bottom:16px;}
.form-group label{display:block;font-size:0.75rem;font-weight:700;color:rgba(255,255,255,0.60);margin-bottom:7px;letter-spacing:1px;text-transform:uppercase;}

/* INPUTS */
.iw{position:relative;}
.iw .icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#FF6B00;font-size:0.85rem;pointer-events:none;transition:color 0.2s;}
.iw:focus-within .icon{color:#FFC107;}
.iw input{
  width:100%;padding:13px 42px 13px 40px;
  background:rgba(255,255,255,0.07);
  border:1.5px solid rgba(255,255,255,0.12);
  border-radius:10px;color:#fff;font-size:0.93rem;font-family:inherit;
  transition:border-color 0.2s,background 0.2s,box-shadow 0.2s;outline:none;
}
.iw input::placeholder{color:rgba(255,255,255,0.28);}
.iw input:focus{border-color:#FF6B00;background:rgba(255,107,0,0.08);box-shadow:0 0 0 3px rgba(255,107,0,0.20);}
.iw input.shake{animation:shake 0.4s ease;}
@keyframes shake{0%,100%{transform:translateX(0);}25%,75%{transform:translateX(-6px);}50%{transform:translateX(6px);}}
.eye-btn{position:absolute;right:13px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.35);cursor:pointer;font-size:0.85rem;transition:color 0.2s;}
.eye-btn:hover{color:#FFC107;}

/* BUTTON */
.btn-submit{
  width:100%;padding:14px;margin-top:8px;
  background:linear-gradient(135deg,#FF6B00,#e05e00);
  color:#fff;border:none;border-radius:10px;
  font-size:0.96rem;font-weight:700;cursor:pointer;font-family:inherit;
  letter-spacing:0.4px;position:relative;overflow:hidden;
  box-shadow:0 4px 18px rgba(255,107,0,0.45);
  transition:transform 0.2s,box-shadow 0.2s,filter 0.2s;
}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(255,107,0,0.60);filter:brightness(1.08);}
.btn-submit:active{transform:translateY(0);}
.btn-submit:disabled{opacity:0.65;cursor:not-allowed;transform:none;}

/* DIVIDER */
.divider{display:flex;align-items:center;gap:12px;margin:20px 0;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:rgba(255,255,255,0.10);}
.divider span{color:rgba(255,255,255,0.35);font-size:0.75rem;letter-spacing:1px;}

/* FOOT */
.foot-link{text-align:center;color:rgba(255,255,255,0.45);font-size:0.85rem;}
.foot-link a{color:#FFC107;font-weight:700;text-decoration:none;transition:color 0.2s;}
.foot-link a:hover{color:#FF6B00;}

@media(max-width:480px){.card{padding:32px 22px;}}
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
      <p>Welcome back! Sign in to continue.</p>
    </div>

    <?php if ($error): ?>
    <div class="alert error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['registered'])): ?>
    <div class="alert success"><i class="fas fa-check-circle"></i> Account created! Sign in below.</div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE ?>/front/login.php<?= isset($_GET['redirect']) ? '?redirect='.urlencode($_GET['redirect']) : '' ?>" id="loginForm">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <div class="form-group">
        <label>Username or Email</label>
        <div class="iw">
          <i class="fas fa-user icon"></i>
          <input type="text" name="user" placeholder="Enter username or email" required autocomplete="username">
        </div>
      </div>

      <div class="form-group">
        <label>Password</label>
        <div class="iw">
          <i class="fas fa-lock icon"></i>
          <input type="password" name="pass" id="passField" placeholder="Enter your password" required autocomplete="current-password">
          <i class="fas fa-eye eye-btn" id="togglePass"></i>
        </div>
      </div>

      <button type="submit" name="submit" class="btn-submit" id="loginBtn">
        <i class="fas fa-sign-in-alt"></i> Sign In
      </button>
    </form>

    <div class="divider"><span>OR</span></div>
    <div class="foot-link">Don't have an account? <a href="signup.php">Create one free</a></div>
  </div>
</div>

<script>
// Password toggle
document.getElementById('togglePass').addEventListener('click', function() {
  const f = document.getElementById('passField');
  f.type = f.type === 'password' ? 'text' : 'password';
  this.classList.toggle('fa-eye');
  this.classList.toggle('fa-eye-slash');
});

// Shake on error
<?php if ($error): ?>
document.querySelectorAll('.iw input').forEach(inp => {
  inp.classList.add('shake');
  inp.addEventListener('animationend', () => inp.classList.remove('shake'), {once: true});
});
<?php endif; ?>

// Loading state — set AFTER form data is captured by browser
document.getElementById('loginForm').addEventListener('submit', function(e) {
  // Let the form submit naturally, just show loading UI after a tick
  const btn = document.getElementById('loginBtn');
  setTimeout(() => {
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
    btn.disabled = true;
  }, 0);
});
</script>
</body>
</html>
