<?php
session_start();
require_once '../includes/db.php';
if (!empty($_SESSION['loggedin'])) { header('Location: '.BASE.'/front/index.php'); exit; }
$err = '';
if (isset($_POST['submit'])) {
    $conn     = db();
    $username = trim($_POST['user']??'');
    $email    = trim($_POST['email']??'');
    $password = $_POST['pass']??'';
    $cpass    = $_POST['cpass']??'';
    $role     = in_array($_POST['role']??'',['customer','owner','company'])?$_POST['role']:'customer';
    $phone    = trim($_POST['phone']??'');
    if (strlen($username)<3)                        $err='Username must be at least 3 characters.';
    elseif (!filter_var($email,FILTER_VALIDATE_EMAIL)) $err='Invalid email format.';
    elseif (strlen($password)<6)                    $err='Password must be at least 6 characters.';
    elseif ($password!==$cpass)                     $err='Passwords do not match.';
    else {
        $s=$conn->prepare('SELECT id FROM signup WHERE username=? OR email=?');
        $s->bind_param('ss',$username,$email); $s->execute(); $s->store_result();
        if ($s->num_rows>0) $err='Username or email already exists.';
        else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $s2=$conn->prepare('INSERT INTO signup (username,email,password,role,phone) VALUES (?,?,?,?,?)');
            $s2->bind_param('sssss',$username,$email,$hash,$role,$phone);
            if ($s2->execute()) { header('Location: '.BASE.'/front/login.php?registered=1'); exit; }
            else $err='Registration failed. Please try again.';
        }
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
body{min-height:100vh;font-family:'Inter',sans-serif;background:#f8fafc;display:flex;align-items:center;justify-content:center;padding:28px 20px;position:relative;overflow-x:hidden;}
:root{--cream:#f0f9ff;--beige:#e0f2fe;--taupe:#93c5fd;--brown:#2563EB;--brown-dk:#1d4ed8;--text:#0f172a;--muted:#64748b;}
.blob{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;z-index:0;}
.blob-1{width:520px;height:520px;background:rgba(37,99,235,0.12);top:-150px;left:-120px;animation:bf 10s ease-in-out infinite;}
.blob-2{width:400px;height:400px;background:rgba(6,182,212,0.10);bottom:-100px;right:-100px;animation:bf 13s ease-in-out infinite reverse;}
@keyframes bf{0%,100%{transform:translate(0,0);}50%{transform:translate(28px,-28px);}}
.page-wrap{position:relative;z-index:1;width:100%;max-width:520px;}
.card{background:white;border-radius:22px;padding:40px 38px;box-shadow:0 20px 60px rgba(15,23,42,0.12);border:1px solid #e0f2fe;animation:cardIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both;}
@keyframes cardIn{from{opacity:0;transform:translateY(28px) scale(0.97);}to{opacity:1;transform:none;}}
.logo{text-align:center;margin-bottom:22px;}
.logo-icon{width:54px;height:54px;margin:0 auto 10px;background:linear-gradient(135deg,#2563EB,#06b6d4);border-radius:15px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:800;color:white;box-shadow:0 4px 20px rgba(37,99,235,0.40);}
.logo h1{font-size:1.45rem;font-weight:800;color:var(--text);letter-spacing:1px;}
.logo p{color:var(--muted);font-size:0.82rem;margin-top:3px;}
.alert-error{background:rgba(192,57,43,0.08);border:1px solid rgba(192,57,43,0.22);color:#7a1a0a;padding:11px 14px;border-radius:10px;font-size:0.83rem;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.role-label{font-size:0.72rem;font-weight:700;color:var(--muted);margin-bottom:8px;letter-spacing:0.8px;text-transform:uppercase;}
.role-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:18px;}
.role-card{border:2px solid var(--taupe);border-radius:12px;padding:13px 8px;text-align:center;cursor:pointer;background:var(--cream);transition:border-color 0.2s,background 0.2s,transform 0.22s;position:relative;}
.role-card input{position:absolute;opacity:0;width:0;height:0;}
.role-card .ri{font-size:1.4rem;margin-bottom:5px;display:block;}
.role-card .rl{font-size:0.75rem;font-weight:700;color:var(--text);display:block;}
.role-card .rs{font-size:0.64rem;color:var(--muted);display:block;margin-top:2px;}
.role-card:hover{border-color:#2563EB;background:#eff6ff;transform:translateY(-2px);}
.role-card.selected{border-color:#2563EB;background:#eff6ff;}
.role-card.selected .rl{color:#1d4ed8;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.form-group{margin-bottom:13px;}
.form-group label{display:block;font-size:0.72rem;font-weight:700;color:var(--muted);margin-bottom:6px;letter-spacing:0.8px;text-transform:uppercase;}
.iw{position:relative;}
.iw .ic{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--taupe);font-size:0.84rem;pointer-events:none;transition:color 0.2s;}
.iw:focus-within .ic{color:var(--brown);}
.iw input{width:100%;padding:12px 12px 12px 38px;background:var(--cream);border:1.5px solid var(--taupe);border-radius:10px;color:var(--text);font-size:0.91rem;font-family:inherit;outline:none;transition:border-color 0.2s,background 0.2s,box-shadow 0.2s;}
.iw input::placeholder{color:var(--taupe);}
.iw input:focus{border-color:#2563EB;background:white;box-shadow:0 0 0 3px rgba(37,99,235,0.15);}
.eye{position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--taupe);cursor:pointer;font-size:0.84rem;transition:color 0.2s;}
.eye:hover{color:var(--brown-dk);}
.strength{height:3px;border-radius:2px;background:var(--beige);margin-top:5px;overflow:hidden;}
.strength-fill{height:100%;width:0;border-radius:2px;transition:all 0.3s;}
.btn-submit{width:100%;padding:13px;margin-top:8px;background:linear-gradient(135deg,#2563EB,#1d4ed8);color:white;border:none;border-radius:10px;font-size:0.96rem;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:0 4px 18px rgba(37,99,235,0.40);transition:transform 0.2s,box-shadow 0.2s,filter 0.2s;}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(37,99,235,0.55);filter:brightness(1.08);}
.btn-submit:disabled{opacity:0.65;cursor:not-allowed;transform:none;}
.foot-link{text-align:center;color:var(--muted);font-size:0.85rem;margin-top:16px;}
.foot-link a{color:#2563EB;font-weight:600;text-decoration:none;}
.foot-link a:hover{text-decoration:underline;}
@media(max-width:500px){.card{padding:30px 20px;}.form-row{grid-template-columns:1fr;}}
</style>
</head>
<body>
<div class="blob blob-1"></div><div class="blob blob-2"></div>
<div class="page-wrap">
  <div class="card">
    <div class="logo"><div class="logo-icon">D</div><h1>DRIFTER</h1><p>Create your free account</p></div>
    <form action="<?= BASE ?>/front/signup.php" method="POST" id="sf">
      <?php if ($err): ?><div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($err) ?></div><?php endif; ?>
      <div class="role-label">I am a...</div>
      <div class="role-grid">
        <label class="role-card selected" id="rc-c"><input type="radio" name="role" value="customer" checked><span class="ri">🧑</span><span class="rl">Customer</span><span class="rs">Book services</span></label>
        <label class="role-card" id="rc-o"><input type="radio" name="role" value="owner"><span class="ri">🚚</span><span class="rl">Vehicle Owner</span><span class="rs">List vehicles</span></label>
        <label class="role-card" id="rc-co"><input type="radio" name="role" value="company"><span class="ri">🏢</span><span class="rl">Company</span><span class="rs">Courier/Movers</span></label>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Username</label><div class="iw"><i class="fas fa-user ic"></i><input type="text" name="user" placeholder="username" required minlength="3" value="<?= htmlspecialchars($_POST['user']??'') ?>"></div></div>
        <div class="form-group"><label>Phone</label><div class="iw"><i class="fas fa-phone ic"></i><input type="text" name="phone" placeholder="10-digit" pattern="[0-9]{10}" value="<?= htmlspecialchars($_POST['phone']??'') ?>"></div></div>
      </div>
      <div class="form-group"><label>Email Address</label><div class="iw"><i class="fas fa-envelope ic"></i><input type="email" name="email" placeholder="your@email.com" required value="<?= htmlspecialchars($_POST['email']??'') ?>"></div></div>
      <div class="form-row">
        <div class="form-group"><label>Password</label><div class="iw"><i class="fas fa-lock ic"></i><input type="password" name="pass" id="pw" placeholder="min 6 chars" required minlength="6"><i class="fas fa-eye eye" id="ep"></i></div><div class="strength"><div class="strength-fill" id="sf2"></div></div></div>
        <div class="form-group"><label>Confirm Password</label><div class="iw"><i class="fas fa-lock ic"></i><input type="password" name="cpass" id="cp" placeholder="repeat password" required><i class="fas fa-eye eye" id="ec"></i></div></div>
      </div>
      <button type="submit" name="submit" class="btn-submit" id="sb"><i class="fas fa-user-plus"></i> Create Account</button>
    </form>
    <div class="foot-link">Already have an account? <a href="<?= BASE ?>/front/login.php">Sign in</a></div>
  </div>
</div>
<script>
document.querySelectorAll('.role-card').forEach(c=>{c.addEventListener('click',()=>{document.querySelectorAll('.role-card').forEach(x=>x.classList.remove('selected'));c.classList.add('selected');});});
function tog(b,f){document.getElementById(b).addEventListener('click',function(){const i=document.getElementById(f);i.type=i.type==='password'?'text':'password';this.classList.toggle('fa-eye');this.classList.toggle('fa-eye-slash');});}
tog('ep','pw');tog('ec','cp');
document.getElementById('pw').addEventListener('input',function(){const v=this.value,bar=document.getElementById('sf2');let s=0;if(v.length>=6)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;bar.style.width=(s*25)+'%';bar.style.background=['#c0392b','#e67e22','#C9B59C','#27ae60'][s-1]||'transparent';});
document.getElementById('sf').addEventListener('submit',function(e){if(document.getElementById('pw').value!==document.getElementById('cp').value){e.preventDefault();alert('Passwords do not match!');return;}const b=document.getElementById('sb');setTimeout(()=>{b.innerHTML='<i class="fas fa-spinner fa-spin"></i> Creating...';b.disabled=true;},0);});
</script>
</body>
</html>
