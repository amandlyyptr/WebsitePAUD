<?php
session_start();
include 'includes/db.php';

if(!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    mysqli_query($conn, "UPDATE counter SET jumlah = jumlah + 1 WHERE id = 1");
}

$result = mysqli_query($conn, "SELECT jumlah FROM counter WHERE id = 1");
$visitor_count = mysqli_fetch_assoc($result)['jumlah'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tugas Pokok | PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/website-paud/css/tugas.css?v=2">
</head>
<body>

<nav class="flat-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="beranda.php">
            <img src="img/logo-dispendik.png" alt="Logo" class="logo">
            <span class="brand-text">
                Bidang PAUD dan Pendidikan Non Formal/Kesetaraan<br>
                <small>Dinas Pendidikan Kab. Lamongan</small>
            </span>
        </a>

        <ul class="nav-list ms-auto">
            <li class="nav-item"><i class="bi bi-house-door"></i> <a href="beranda.php">Beranda</a></li>
            <li class="nav-item dropdown">
                <i class="bi bi-info-circle"></i> <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">Profil</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="profil.php">Profil Bidang</a></li>
                    <li><a class="dropdown-item" href="struktur.php">Struktur Organisasi</a></li>
                    <li><a class="dropdown-item active" href="tugas.php">Tugas Pokok</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <i class="bi bi-diagram-3"></i> <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">Seksi</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="seksi-pnf.php">Seksi PNF</a></li>
                    <li><a class="dropdown-item" href="seksi-sarpras.php">Seksi Sarpras</a></li>
                    <li><a class="dropdown-item" href="seksi-paud.php">Seksi PAUD</a></li>
                </ul>
            </li>
            <li class="nav-item"><i class="bi bi-folder"></i> <a href="data.php">Data</a></li>
            <li class="nav-item"><i class="bi bi-journal-text"></i> <a href="materi.php">Edukatif</a></li>
            <li class="nav-item"><i class="bi bi-newspaper"></i> <a href="berita.php">Publikasi</a></li>
            <li class="nav-item"><i class="bi bi-chat-dots"></i> <a href="saran.php">Saran</a></li>
            <li class="nav-item"><a href="#" id="accessibility-btn"><i class="bi bi-universal-access"></i> Aksibilitas</a></li>
            <li class="nav-item"><a href="admin/auth/login.php" class="login-icon"><i class="bi bi-person-fill"></i></a></li>
        </ul>
    </div>
</nav>

<section class="bg-light pb-5">
<div class="judul-section" style="background: linear-gradient(135deg, #0d47a1, #1565c0, #1e88e5); padding: 56px 0 48px; text-align: center; position: relative; overflow: hidden;">
    <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.06);"></div>
    <div style="position: absolute; bottom: -60px; right: -30px; width: 220px; height: 220px; border-radius: 50%; background: rgba(255,255,255,0.06);"></div>
    <div style="position: absolute; top: 20px; right: 15%; width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.04);"></div>
    <div class="container" style="position: relative; z-index: 1;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,0.15); border: 2px solid rgba(255,255,255,0.25); margin-bottom: 16px;" data-aos="zoom-in">
            <i class="bi bi-clipboard2-check-fill" style="font-size: 28px; color: #fff;"></i>
        </div>
        <h2 class="fw-bold mb-2" style="color: #fff; font-size: 2rem; letter-spacing: 0.5px;" data-aos="fade-down">Tugas Pokok</h2>
        <p style="color: rgba(255,255,255,0.75); font-size: 0.95rem; max-width: 580px; margin: 10px auto 0;" data-aos="fade-up">
            Bidang PAUD dan Pendidikan Non Formal/Kesetaraan mempunyai tugas melaksanakan perumusan dan pelaksanaan kebijakan teknis, pembinaan, serta pengelolaan di bidang pendidikan anak usia dini dan pendidikan non formal sesuai dengan ketentuan yang berlaku.
        </p>
        <div style="width: 50px; height: 3px; background: rgba(255,255,255,0.4); border-radius: 4px; margin: 16px auto 0;" data-aos="fade-up"></div>
    </div>
</div>

<div class="container" style="margin-top: 40px;">

        <div class="row g-4">
            <div class="col-md-6" data-aos="zoom-in" data-aos-delay="100">
                <div class="card h-100 shadow-sm border-0 p-3">
                    <i class="bi bi-journal-text fs-2 text-primary"></i>
                    <h6 class="fw-bold mt-3">Perumusan Kebijakan Teknis</h6>
                    <p class="text-muted mb-0">Menyusun kebijakan teknis di bidang PAUD dan Pendidikan Non Formal/Kesetaraan.</p>
                </div>
            </div>

            <div class="col-md-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="card h-100 shadow-sm border-0 p-3">
                    <i class="bi bi-people-fill fs-2 text-success"></i>
                    <h6 class="fw-bold mt-3">Pembinaan Lembaga Pendidikan</h6>
                    <p class="text-muted mb-0">Melaksanakan pembinaan dan pengembangan lembaga PAUD dan PKBM.</p>
                </div>
            </div>

            <div class="col-md-6" data-aos="zoom-in" data-aos-delay="300">
                <div class="card h-100 shadow-sm border-0 p-3">
                    <i class="bi bi-bar-chart-line-fill fs-2 text-warning"></i>
                    <h6 class="fw-bold mt-3">Monitoring dan Evaluasi</h6>
                    <p class="text-muted mb-0">Melakukan monitoring dan evaluasi terhadap pelaksanaan program PAUD dan PNF.</p>
                </div>
            </div>

            <div class="col-md-6" data-aos="zoom-in" data-aos-delay="400">
                <div class="card h-100 shadow-sm border-0 p-3">
                    <i class="bi bi-diagram-3-fill fs-2 text-danger"></i>
                    <h6 class="fw-bold mt-3">Koordinasi dan Kerja Sama</h6>
                    <p class="text-muted mb-0">Melaksanakan koordinasi dengan satuan pendidikan dan pemangku kepentingan terkait.</p>
                </div>
            </div>

            <div class="col-md-6" data-aos="zoom-in" data-aos-delay="500">
                <div class="card h-100 shadow-sm border-0 p-3">
                    <i class="bi bi-database-fill fs-2 text-info"></i>
                    <h6 class="fw-bold mt-3">Pengelolaan Data Pendidikan</h6>
                    <p class="text-muted mb-0">Mengelola data dan informasi pendidikan PAUD dan Pendidikan Non Formal.</p>
                </div>
            </div>

            <div class="col-md-6" data-aos="zoom-in" data-aos-delay="600">
                <div class="card h-100 shadow-sm border-0 p-3">
                    <i class="bi bi-briefcase-fill fs-2 text-secondary"></i>
                    <h6 class="fw-bold mt-3">Pelaksanaan Tugas Kedinasan</h6>
                    <p class="text-muted mb-0">Melaksanakan tugas lain yang diberikan oleh pimpinan sesuai kewenangan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer" data-aos="fade-up">
    <div class="container">
        <div class="row g-4">

            <div class="col-md-5">
                <h6 class="footer-heading">Kontak Kami</h6>
                <p class="footer-address">
                    Jl. KH. Ahmad Dahlan No. 75, Kecamatan Lamongan,<br>Jawa Timur 62214
                </p>
                <ul class="footer-contact-list">
                    <li><i class="bi bi-envelope"></i> disdik@lamongankab.go.id</li>
                    <li><i class="bi bi-telephone"></i> (0322) 321021</li>
                    <li><i class="bi bi-whatsapp"></i> +62 812-4667-6547</li>
                </ul>
                <div class="visitor-badge">
                    <i class="bi bi-people-fill"></i>
                    Pengunjung: <strong><?= $visitor_count; ?></strong>
                </div>
            </div>

            <div class="col-md-3">
                <h6 class="footer-heading">Menu Cepat</h6>
                <ul class="footer-menu">
                    <li><a href="beranda.php">Beranda</a></li>
                    <li><a href="profil.php">Profil</a></li>
                    <li><a href="berita.php">Berita</a></li>
                    <li><a href="materi.php">Materi</a></li>
                    <li><a href="saran.php">Saran</a></li>
                </ul>
            </div>

            <div class="col-md-4">
                <h6 class="footer-heading">Lokasi Kami</h6>
                <div class="map-box mb-3">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.0435881474013!2d112.4132808!3d-7.1209351!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e77f06915e6129b%3A0xc02014603912066!2sDinas%20Pendidikan%20Kabupaten%20Lamongan!5e0!3m2!1sid!2sid!4v1700000000000"
                        loading="lazy" allowfullscreen>
                    </iframe>
                </div>
                <div class="text-center pb-2">
                    <img src="img/lamongan-megilan-putih.png" alt="Logo Lamongan Megilan" class="footer-logo-img">
                </div>
            </div>

        </div>

        <hr class="footer-divider">

        <p class="footer-copy">
            &copy; 2026 Website PAUD &middot; Dinas Pendidikan Kabupaten Lamongan
        </p>
    </div>
</footer>

<a href="#" class="scroll-top"><i class="bi bi-arrow-up"></i></a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: false });
    </script>

    <?php include 'includes/accessibility.php'; ?>
</body>
</html>