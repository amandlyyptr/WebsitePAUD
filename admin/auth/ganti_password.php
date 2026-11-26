<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include '../../includes/db.php';

$success = '';
$error   = '';

if (isset($_POST['ganti'])) {

    $username      = mysqli_real_escape_string($conn, $_POST['username']);
    $password_lama = md5($_POST['password_lama']);
    $password_baru = $_POST['password_baru'];
    $konfirmasi    = $_POST['konfirmasi'];

    $q = mysqli_query($conn, "
        SELECT * FROM admin 
        WHERE username='$username' 
        AND password='$password_lama'
    ");

    if (mysqli_num_rows($q) == 0) {
        $error = "Username atau password lama salah.";
    } elseif (strlen($password_baru) < 6) {
        $error = "Password baru minimal 6 karakter.";
    } elseif ($password_baru !== $konfirmasi) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        $password_baru_md5 = md5($password_baru);
        mysqli_query($conn, "
            UPDATE admin 
            SET password='$password_baru_md5' 
            WHERE username='$username'
        ");
        $success = "Password berhasil diubah! Silakan login kembali.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Ganti Password | PAUD Lamongan</title>
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

    /* Steps info */
    .steps {
      margin-top: 32px;
      display: flex;
      flex-direction: column;
      gap: 14px;
      text-align: left;
    }

    .step-item {
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .step-num {
      width: 28px; height: 28px;
      border-radius: 50%;
      background: rgba(255,255,255,0.15);
      border: 1.5px solid rgba(255,255,255,0.3);
      display: flex; align-items: center; justify-content: center;
      font-size: 0.75rem;
      font-weight: 700;
      flex-shrink: 0;
      color: #fff;
    }

    .step-text {
      font-size: 0.82rem;
      color: rgba(255,255,255,0.8);
      line-height: 1.5;
      padding-top: 4px;
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

    .form-box {
      width: 100%;
      max-width: 360px;
    }

    .form-box .title {
      margin-bottom: 28px;
    }

    .form-box .title h4 {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      color: #0d253f;
      font-size: 1.4rem;
      margin-bottom: 4px;
    }

    .form-box .title p {
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
      color: #a0aec0;
      cursor: pointer;
      padding: 0; z-index: 2;
    }

    .input-wrap .toggle-pw:hover { color: #1976d2; }

    .btn-simpan {
      height: 48px;
      background: linear-gradient(135deg, #1976d2, #0d47a1);
      border: none;
      font-weight: 600;
      border-radius: 10px;
      font-size: 0.9rem;
      letter-spacing: .3px;
      transition: opacity .2s, transform .1s;
    }

    .btn-simpan:hover {
      opacity: 0.92;
      transform: translateY(-1px);
    }

    .link-back {
      display: block;
      text-align: center;
      margin-top: 16px;
      font-size: 0.82rem;
      color: #1976d2;
      text-decoration: none;
      transition: color .2s;
    }

    .link-back:hover {
      color: #0d47a1;
      text-decoration: underline;
    }

    .footer-text {
      margin-top: 40px;
      font-size: 0.75rem;
      color: #b0bec5;
      text-align: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
      body { flex-direction: column; }
      .left-panel { width: 100%; padding: 40px 20px; min-height: 200px; }
      .left-panel::before, .left-panel::after { display: none; }
      .steps { display: none; }
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
    <h2>Ganti Password</h2>
    <div class="divider-line"></div>
    <p>Pastikan password baru Anda kuat dan mudah diingat</p>

    <div class="steps">
      <div class="step-item">
        <div class="step-num">1</div>
        <div class="step-text">Masukkan username dan password lama Anda</div>
      </div>
      <div class="step-item">
        <div class="step-num">2</div>
        <div class="step-text">Buat password baru minimal 6 karakter</div>
      </div>
      <div class="step-item">
        <div class="step-num">3</div>
        <div class="step-text">Konfirmasi password baru lalu simpan</div>
      </div>
      <div class="step-item">
        <div class="step-num">4</div>
        <div class="step-text">Login kembali menggunakan password baru</div>
      </div>
    </div>
  </div>
</div>

<!-- ===== PANEL KANAN ===== -->
<div class="right-panel">
  <div class="form-box">

    <div class="title">
      <h4>Ubah Password 🔑</h4>
      <p>Isi form berikut untuk mengganti password akun Anda</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem; border-radius:10px;">
        <i class="bi bi-exclamation-circle me-1"></i> <?= $error ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success py-2 mb-3" style="font-size:.85rem; border-radius:10px;">
        <i class="bi bi-check-circle me-1"></i> <?= $success ?>
      </div>
    <?php endif; ?>

    <form method="POST">

      <label class="form-label">Username</label>
      <div class="input-wrap">
        <i class="bi bi-person-fill icon"></i>
        <input type="text" name="username" class="form-control"
               placeholder="Masukkan username" required>
      </div>

      <label class="form-label">Password Lama</label>
      <div class="input-wrap">
        <i class="bi bi-lock-fill icon"></i>
        <input type="password" name="password_lama" id="pw_lama"
               class="form-control" placeholder="Masukkan password lama" required>
        <button type="button" class="toggle-pw" onclick="togglePw('pw_lama', this)">
          <i class="bi bi-eye"></i>
        </button>
      </div>

      <label class="form-label">Password Baru</label>
      <div class="input-wrap">
        <i class="bi bi-key-fill icon"></i>
        <input type="password" name="password_baru" id="pw_baru"
               class="form-control" placeholder="Min. 6 karakter" required>
        <button type="button" class="toggle-pw" onclick="togglePw('pw_baru', this)">
          <i class="bi bi-eye"></i>
        </button>
      </div>

      <label class="form-label">Konfirmasi Password Baru</label>
      <div class="input-wrap" style="margin-bottom: 20px;">
        <i class="bi bi-key-fill icon"></i>
        <input type="password" name="konfirmasi" id="pw_konfirmasi"
               class="form-control" placeholder="Ulangi password baru" required>
        <button type="button" class="toggle-pw" onclick="togglePw('pw_konfirmasi', this)">
          <i class="bi bi-eye"></i>
        </button>
      </div>

      <button type="submit" name="ganti" class="btn btn-primary btn-simpan w-100">
        <i class="bi bi-check-lg me-2"></i> Simpan Password
      </button>

    </form>

    <a href="login.php" class="link-back">
      <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
    </a>

    <div class="footer-text">
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