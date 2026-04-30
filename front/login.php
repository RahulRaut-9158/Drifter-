<?php
session_start();
require_once '../includes/db.php';
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
if (!empty($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token']!==$_SESSION['csrf_token']) {
        $error = 'Invalid request. Please try again.';
    } else {
        $input = trim($_POST['user']??'');
        $pass  = $_POST['pass']??'';
        if (!$input||!$pass) { $error='Please fill in all fields.'; }
        else {
            $conn = db();
            $stmt = $conn->prepare("SELECT * FROM signup WHERE username=? OR email=?");
            $stmt->bind_param("ss",$input,$input); $stmt->execute();
            $row  = $stmt->get_result()->fetch_assoc();
            if ($row && password_verify($pass,$row['password'])) {
                session_regenerate_id(true);
                $_SESSION['username'] = $row['username'];
                $_SESSION['loggedin'] = true;
                $_SESSION['role']     = $row['role']??'customer';
                $dest = $_GET['redirect']??'';
                if ($dest) { header("Location: $dest"); exit; }
                $map = ['customer'=>BASE.'/front/dashboard_customer.php','owner'=>BASE.'/front/your_vehicle_info.php','company'=>BASE.'/courier/company_info.php','admin'=>BASE.'/admin/index.php'];
                header('Location: '.($map[$row['role']]??BASE.'/front/index.php')); exit;
            } else { $error='Invalid username/email or password.'; }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — Drifter</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
body{min-height:100vh;font-family:'Inter',sans-serif;background:#f8fafc;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px;position:relative;overflow:hidden;
:root{--cream:#f0f9ff;--beige:#e0f2fe;--taupe:#93c5fd;--brown:#2563EB;--brown-dk:#1d4ed8;--text:#0f172a;--muted:#64748b;}

.blob{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;z-index:0;}
.blob-1{width:500px;height:500px;background:rgba(37,99,235,0.12);top:-150px;left:-150px;animation:bf 10s ease-in-out infinite;}
.blob-2{width:400px;height:400px;background:rgba(6,182,212,0.10);bottom:-120px;right:-120px;animation:bf 13s ease-in-out infinite reverse;}
@keyframes bf{0%,100%{transform:translate(0,0);}50%{transform:translate(30px,-30px);}}

.page-wrap{position:relative;z-index:1;width:100%;max-width:440px;}

.card{background:#fff;border-radius:20px;padding:44px 40px;box-shadow:0 20px 60px rgba(15,23,42,0.12),0 1px 0 rgba(255,255,255,0.8) inset;border:1px solid #e0f2fe;animation:cardIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both;}
@keyframes cardIn{from{opacity:0;transform:translateY(28px) scale(0.97);}to{opacity:1;transform:none;}}

.logo{text-align:center;margin-bottom:30px;}
.logo-icon{width:58px;height:58px;margin:0 auto 12px;background:linear-gradient(135deg,#2563EB,#06b6d4);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:white;box-shadow:0 4px 20px rgba(37,99,235,0.40);animation:pulse 3s ease-in-out infinite;}
@keyframes pulse{0%,100%{box-shadow:0 4px 20px rgba(37,99,235,0.40);}50%{box-shadow:0 6px 32px rgba(37,99,235,0.65);}}
.logo h1{font-size:1.55rem;font-weight:800;color:var(--text);letter-spacing:1px;}
.logo p{color:var(--muted);font-size:0.83rem;margin-top:4px;}

.alert{padding:11px 14px;border-radius:10px;font-size:0.83rem;margin-bottom:18px;display:flex;align-items:center;gap:8px;animation:slideDown 0.3s ease both;}
@keyframes slideDown{from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:none;}}
.alert.error{background:rgba(192,57,43,0.08);border:1px solid rgba(192,57,43,0.22);color:#7a1a0a;}
.alert.success{background:rgba(201,181,156,0.15);border:1px solid rgba(201,181,156,0.40);color:var(--brown-dk);}

.form-group{margin-bottom:16px;}
.form-group label{display:block;font-size:0.74rem;font-weight:700;color:var(--muted);margin-bottom:7px;letter-spacing:0.8px;text-transform:uppercase;}
.iw{position:relative;}
.iw .icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--taupe);font-size:0.85rem;pointer-events:none;transition:color 0.2s;}
.iw:focus-within .icon{color:var(--brown);}
.iw input{width:100%;padding:13px 42px 13px 40px;background:var(--cream);border:1.5px solid var(--taupe);border-radius:10px;color:var(--text);font-size:0.93rem;font-family:inherit;transition:border-color 0.2s,background 0.2s,box-shadow 0.2s;outline:none;}
.iw input::placeholder{color:var(--taupe);}
.iw input:focus{border-color:#2563EB;background:white;box-shadow:0 0 0 3px rgba(37,99,235,0.15);}
.eye-btn{position:absolute;right:13px;top:50%;transform:translateY(-50%);color:var(--taupe);cursor:pointer;font-size:0.85rem;transition:color 0.2s;}
.eye-btn:hover{color:var(--brown-dk);}

.btn-submit{width:100%;padding:14px;margin-top:6px;background:linear-gradient(135deg,#2563EB,#1d4ed8);color:white;border:none;border-radius:10px;font-size:0.96rem;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:0 4px 18px rgba(37,99,235,0.40);transition:transform 0.2s,box-shadow 0.2s,filter 0.2s;}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(37,99,235,0.55);filter:brightness(1.08);}
.btn-submit:disabled{opacity:0.65;cursor:not-allowed;transform:none;}

.divider{display:flex;align-items:center;gap:12px;margin:20px 0;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--beige);}
.divider span{color:var(--taupe);font-size:0.75rem;}
.foot-link{text-align:center;color:var(--muted);font-size:0.85rem;}
.foot-link a{color:#2563EB;font-weight:600;text-decoration:none;}
.foot-link a:hover{text-decoration:underline;}
@media(max-width:480px){.card{padding:32px 22px;}}
</style>
</head>
<body>
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="page-wrap">
  <div class="card">
    <div class="logo">
      <div class="logo-icon">D</div>
      <h1>DRIFTER</h1>
      <p>Welcome back! Sign in to continue.</p>
    </div>
    <?php if ($error): ?><div class="alert error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if (isset($_GET['registered'])): ?><div class="alert success"><i class="fas fa-check-circle"></i> Account created! Sign in below.</div><?php endif; ?>
    <form method="POST" action="<?= BASE ?>/front/login.php<?= isset($_GET['redirect'])?'?redirect='.urlencode($_GET['redirect']):'' ?>" id="loginForm">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <div class="form-group">
        <label>Username or Email</label>
        <div class="iw"><i class="fas fa-user icon"></i><input type="text" name="user" placeholder="Enter username or email" required autocomplete="username"></div>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="iw"><i class="fas fa-lock icon"></i><input type="password" name="pass" id="pf" placeholder="Enter your password" required autocomplete="current-password"><i class="fas fa-eye eye-btn" id="tp"></i></div>
      </div>
      <button type="submit" name="submit" class="btn-submit" id="lb"><i class="fas fa-sign-in-alt"></i> Sign In</button>
    </form>
    <div class="divider"><span>OR</span></div>
    <div class="foot-link">Don't have an account? <a href="<?= BASE ?>/front/signup.php">Create one free</a></div>
  </div>
</div>
<script>
document.getElementById('tp').addEventListener('click',function(){const f=document.getElementById('pf');f.type=f.type==='password'?'text':'password';this.classList.toggle('fa-eye');this.classList.toggle('fa-eye-slash');});
document.getElementById('loginForm').addEventListener('submit',function(){const b=document.getElementById('lb');setTimeout(()=>{b.innerHTML='<i class="fas fa-spinner fa-spin"></i> Signing in...';b.disabled=true;},0);});
</script>
</body>
</html>
