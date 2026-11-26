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
    <title>Saran | PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="/website-paud/css/saran.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            <li class="nav-item"><i class="bi bi-house-door"></i><a href="beranda.php">Beranda</a></li>
            <li class="nav-item dropdown">
                <i class="bi bi-info-circle"></i>
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">Profil</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="profil.php">Profil Bidang</a></li>
                    <li><a class="dropdown-item" href="struktur.php">Struktur Organisasi</a></li>
                    <li><a class="dropdown-item" href="tugas.php">Tugas Pokok</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <i class="bi bi-diagram-3"></i>
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">Seksi</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="seksi-pnf.php">Seksi PNF</a></li>
                    <li><a class="dropdown-item" href="seksi-sarpras.php">Seksi Sarpras</a></li>
                    <li><a class="dropdown-item" href="seksi-paud.php">Seksi PAUD</a></li>
                </ul>
            </li>
            <li class="nav-item"><i class="bi bi-folder"></i><a href="data.php">Data</a></li>
            <li class="nav-item"><i class="bi bi-journal-text"></i><a href="materi.php">Edukatif</a></li>
            <li class="nav-item"><i class="bi bi-newspaper"></i><a href="berita.php">Publikasi</a></li>
            <li class="nav-item active"><i class="bi bi-chat-dots"></i><a href="saran.php">Saran</a></li>
            <li class="nav-item">
                <a href="#" id="accessibility-btn"><i class="bi bi-universal-access"></i> Aksibilitas</a>
            </li>
            <li class="nav-item">
                <a href="admin/auth/login.php" class="login-icon"><i class="bi bi-person-fill"></i></a>
            </li>
        </ul>
    </div>
</nav>

<section class="saran py-5" data-aos="fade-left">
    <div class="container">
        <div class="header-color">Formulir Saran & Masukan</div>
        <p class="subtitle">Silakan sampaikan saran dan masukan untuk peningkatan layanan PAUD.</p>

        <form action="admin/crud/saran_crud.php?aksi=simpan" method="POST" class="saran-form shadow-sm p-4 rounded bg-white">
            <div class="mb-3">
                <label class="form-label fw-bold">Nama</label>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Saran</label>
                <textarea name="isi" class="form-control" rows="4" placeholder="Tuliskan saran Anda" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Kirim Saran</button>
        </form>
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