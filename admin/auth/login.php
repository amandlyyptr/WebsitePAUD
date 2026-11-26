<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include '../../includes/db.php';

// ===== GENERATE CAPTCHA =====
function generateCaptcha() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha = '';
    for ($i = 0; $i < 5; $i++) {
        $captcha .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $captcha;
}

// ===== REFRESH CAPTCHA — harus di atas segalanya =====
if (isset($_GET['refresh'])) {
    $_SESSION['captcha'] = generateCaptcha();
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['captcha'])) {
    $_SESSION['captcha'] = generateCaptcha();
}

$error = '';

if (isset($_POST['login'])) {

    $captcha_input = strtoupper(trim($_POST['captcha'] ?? ''));

    if ($captcha_input !== $_SESSION['captcha']) {
        $error = "Kode keamanan tidak sesuai.";
        $_SESSION['captcha'] = generateCaptcha();
    } else {
        $username = $_POST['username'];
        $password = md5($_POST['password']);

        $q = mysqli_query($conn, "
            SELECT * FROM admin 
            WHERE username='$username' 
            AND password='$password'
        ");

        if (mysqli_num_rows($q) == 1) {
            $_SESSION['admin'] = $username;
            unset($_SESSION['captcha']);
            header("Location: ../pages/dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah.";
            $_SESSION['captcha'] = generateCaptcha();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Admin | PAUD Lamongan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      background: #f0f4f8;
    }

    /* ===== PANEL KIRI ===== */
    .left-panel {
      width: 55%;
      background: linear-gradient(145deg, #0d47a1 0%, #1565c0 40%, #1976d2 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 50px 40px;
      position: relative;
      overflow: hidden;
    }

    .left-panel::before {
      content: '';
      position: absolute;
      width: 400px; height: 400px;
      border-radius: 50%;
      border: 60px solid rgba(255,255,255,0.05);
      top: -100px; left: -100px;
    }

    .left-panel::after {
      content: '';
      position: absolute;
      width: 300px; height: 300px;
      border-radius: 50%;
      border: 50px solid rgba(255,255,255,0.05);
      bottom: -80px; right: -80px;
    }

    .left-panel .circle-mid {
      position: absolute;
      width: 200px; height: 200px;
      border-radius: 50%;
      background: rgba(255,255,255,0.04);
      bottom: 120px; left: 40px;
    }

    .left-content {
      position: relative;
      z-index: 2;
      text-align: center;
      color: #fff;
    }

    .left-content img {
      width: 90px;
      margin-bottom: 24px;
      filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));
    }

    .left-content h2 {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 1.6rem;
      margin-bottom: 8px;
    }

    .left-content p {
      font-size: 0.9rem;
      color: rgba(255,255,255,0.75);
      line-height: 1.6;
      max-width: 300px;
    }

    .divider-line {
      width: 50px; height: 3px;
      background: rgba(255,255,255,0.4);
      border-radius: 3px;
      margin: 18px auto;
    }

    .info-boxes {
      display: flex;
      gap: 12px;
      margin-top: 30px;
      justify-content: center;
    }

    .info-box {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.15);
      border-radius: 10px;
      padding: 12px 16px;
      text-align: center;
      min-width: 90px;
    }

    .info-box i {
      font-size: 1.3rem;
      margin-bottom: 4px;
      display: block;
      color: rgba(255,255,255,0.85);
    }

    .info-box span {
      font-size: 0.72rem;
      color: rgba(255,255,255,0.7);
    }

    /* ===== PANEL KANAN ===== */
    .right-panel {
      width: 45%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 50px 40px;
      background: #fff;
    }

    .login-box {
      width: 100%;
      max-width: 360px;
    }

    .login-box .title { margin-bottom: 28px; }

    .login-box .title h4 {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      color: #0d253f;
      font-size: 1.4rem;
      margin-bottom: 4px;
    }

    .login-box .title p {
      color: #8a9bb0;
      font-size: 0.85rem;
    }

    .form-label {
      font-size: 0.82rem;
      font-weight: 600;
      color: #4a5568;
      margin-bottom: 6px;
      display: block;
    }

    .input-wrap {
      position: relative;
      margin-bottom: 16px;
    }

    .input-wrap .icon {
      position: absolute;
      left: 13px; top: 50%;
      transform: translateY(-50%);
      color: #a0aec0;
      font-size: 1rem;
      z-index: 2;
    }

    .input-wrap .form-control {
      height: 48px;
      padding-left: 40px;
      padding-right: 40px;
      border-radius: 10px;
      border: 1.5px solid #e2e8f0;
      font-size: 0.88rem;
      transition: border-color .2s, box-shadow .2s;
      background: #f8fafc;
    }

    .input-wrap .form-control:focus {
      border-color: #1976d2;
      box-shadow: 0 0 0 3px rgba(25,118,210,0.1);
      background: #fff;
      outline: none;
    }

    .input-wrap .toggle-pw {
      position: absolute;
      right: 12px; top: 50%;
      transform: translateY(-50%);
      background: none; border: none;
      color: #a0aec0; cursor: pointer;
      padding: 0; z-index: 2;
    }

    .input-wrap .toggle-pw:hover { color: #1976d2; }

    /* ===== CAPTCHA ===== */
    .captcha-display-row {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
    }

    .captcha-box {
      flex: 1;
      background: linear-gradient(135deg, #0d47a1, #1976d2);
      border-radius: 10px;
      padding: 12px 18px;
      letter-spacing: 8px;
      font-family: 'Courier New', monospace;
      font-size: 1.4rem;
      font-weight: 700;
      color: #fff;
      user-select: none;
      position: relative;
      overflow: hidden;
      text-align: center;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
      border: 2px solid rgba(255,255,255,0.2);
    }

    .captcha-box::before {
      content: '';
      position: absolute;
      inset: 0;
      background: repeating-linear-gradient(
        45deg,
        transparent, transparent 4px,
        rgba(255,255,255,0.04) 4px,
        rgba(255,255,255,0.04) 5px
      );
    }

    .captcha-box span {
      position: relative;
      z-index: 1;
      display: inline-block;
    }

    .captcha-box span i {
      display: inline-block;
      font-style: normal;
    }

    .captcha-box span i:nth-child(odd)  { transform: rotate(-5deg); }
    .captcha-box span i:nth-child(even) { transform: rotate(5deg); }

    .btn-refresh {
      background: #f0f4f8;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      width: 44px; height: 44px;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer;
      color: #4a5568;
      transition: background .2s, color .2s, border-color .2s;
      flex-shrink: 0;
      text-decoration: none;
      font-size: 1.1rem;
    }

    .btn-refresh:hover {
      background: #1976d2;
      color: #fff;
      border-color: #1976d2;
    }

    .captcha-input-wrap {
      position: relative;
      margin-bottom: 16px;
    }

    .captcha-input-wrap .icon {
      position: absolute;
      left: 13px; top: 50%;
      transform: translateY(-50%);
      color: #a0aec0;
      font-size: 1rem;
      z-index: 2;
    }

    .captcha-input-wrap .form-control {
      height: 48px;
      padding-left: 40px;
      border-radius: 10px;
      border: 1.5px solid #e2e8f0;
      font-size: 0.95rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 5px;
      background: #f8fafc;
      transition: border-color .2s, box-shadow .2s;
    }

    .captcha-input-wrap .form-control:focus {
      border-color: #1976d2;
      box-shadow: 0 0 0 3px rgba(25,118,210,0.1);
      background: #fff;
      outline: none;
    }

    .captcha-hint {
      font-size: 0.75rem;
      color: #a0aec0;
      margin-bottom: 16px;
    }

    .btn-login {
      height: 48px;
      background: linear-gradient(135deg, #1976d2, #0d47a1);
      border: none;
      font-weight: 600;
      border-radius: 10px;
      font-size: 0.9rem;
      letter-spacing: .3px;
      transition: opacity .2s, transform .1s;
    }

    .btn-login:hover { opacity: 0.92; transform: translateY(-1px); }
    .btn-login:active { transform: translateY(0); }

    .link-ganti-password {
      display: block;
      text-align: center;
      margin-top: 16px;
      font-size: 0.82rem;
      color: #1976d2;
      text-decoration: none;
      transition: color .2s;
    }

    .link-ganti-password:hover { color: #0d47a1; text-decoration: underline; }

    .login-footer-text {
      margin-top: 40px;
      font-size: 0.75rem;
      color: #b0bec5;
      text-align: center;
    }

    @media (max-width: 768px) {
      body { flex-direction: column; }
      .left-panel { width: 100%; padding: 40px 20px; min-height: 220px; }
      .left-panel::before, .left-panel::after { display: none; }
      .info-boxes { display: none; }
      .right-panel { width: 100%; padding: 40px 24px; }
    }
  </style>
</head>

<body>

<!-- ===== PANEL KIRI ===== -->
<div class="left-panel">
  <div class="circle-mid"></div>
  <div class="left-content">
    <img src="../../img/logo-dispendik.png" alt="Logo Dispendik">
    <h2>PAUD Lamongan</h2>
    <div class="divider-line"></div>
    <p>Panel Admin Bidang PAUD & Pendidikan Non Formal Dinas Pendidikan Kabupaten Lamongan</p>

    <div class="info-boxes">
      <div class="info-box">
        <i class="bi bi-shield-lock"></i>
        <span>Aman &<br>Terenkripsi</span>
      </div>
      <div class="info-box">
        <i class="bi bi-person-gear"></i>
        <span>Khusus<br>Admin</span>
      </div>
      <div class="info-box">
        <i class="bi bi-clipboard-data"></i>
        <span>Kelola<br>Konten</span>
      </div>
    </div>
  </div>
</div>

<!-- ===== PANEL KANAN ===== -->
<div class="right-panel">
  <div class="login-box">

    <div class="title">
      <h4>Selamat Datang </h4>
      <p>Masuk ke panel admin untuk mengelola konten</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem; border-radius:10px;">
        <i class="bi bi-exclamation-circle me-1"></i> <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="POST">

      <label class="form-label">Username</label>
      <div class="input-wrap">
        <i class="bi bi-person-fill icon"></i>
        <input type="text" name="username" class="form-control"
               placeholder="Masukkan username" required>
      </div>

      <label class="form-label">Password</label>
      <div class="input-wrap">
        <i class="bi bi-lock-fill icon"></i>
        <input type="password" name="password" id="pw_login"
               class="form-control" placeholder="Masukkan password" required>
        <button type="button" class="toggle-pw" onclick="togglePw('pw_login', this)">
          <i class="bi bi-eye"></i>
        </button>
      </div>

      <!-- ===== CAPTCHA ===== -->
      <label class="form-label">Kode Keamanan</label>

      <!-- Baris 1: Kotak captcha + tombol refresh -->
      <div class="captcha-display-row">
        <div class="captcha-box">
          <span>
            <?php foreach (str_split($_SESSION['captcha']) as $char): ?>
              <i><?= $char ?></i>
            <?php endforeach; ?>
          </span>
        </div>
        <a href="?refresh=1" class="btn-refresh" title="Ganti kode">
          <i class="bi bi-arrow-clockwise"></i>
        </a>
      </div>

      <!-- Baris 2: Input captcha (di bawah) -->
      <div class="captcha-input-wrap">
        <i class="bi bi-shield-check icon"></i>
        <input type="text" name="captcha" class="form-control"
               placeholder="Ketik kode di atas..." maxlength="5"
               required autocomplete="off">
      </div>

      <p class="captcha-hint">
        <i class="bi bi-info-circle me-1"></i>
        Tidak peka huruf besar/kecil
      </p>

      <button type="submit" name="login" class="btn btn-primary btn-login w-100">
        <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
      </button>

    </form>

    <a href="ganti_password.php" class="link-ganti-password">
      <i class="bi bi-key me-1"></i> Lupa / Ganti Password?
    </a>

    <div class="login-footer-text">
      © 2026 Dinas Pendidikan Kabupaten Lamongan
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePw(inputId, btn) {
  const input = document.getElementById(inputId);
  const icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('bi-eye', 'bi-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('bi-eye-slash', 'bi-eye');
  }
}
</script>
</body>
</html>