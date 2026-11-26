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
    <title>Profil | PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="/website-paud/css/profil.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ===== HERO SLIDER ===== */
        .profil-hero {
            position: relative;
            height: 520px;
            overflow: hidden;
        }

        .hero-slides {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .hero-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.9s ease, transform 1.2s ease;
            transform: scale(1.04);
        }

        .hero-slide.active {
            opacity: 1;
            transform: scale(1);
        }

        .hero-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .hero-slide::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(2, 44, 83, 0.82) 0%,
                rgba(24, 95, 165, 0.50) 50%,
                rgba(2, 44, 83, 0.28) 100%
            );
        }

        .hero-caption {
            position: absolute;
            inset: 0;
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 0 0 60px 64px;
            pointer-events: none;
        }

        .hero-caption .badge-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(55, 138, 221, 0.22);
            border: 1px solid rgba(133, 183, 235, 0.40);
            backdrop-filter: blur(6px);
            color: #b5d4f4;
            font-size: 11.5px;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 100px;
            margin-bottom: 18px;
            width: fit-content;
        }

        .hero-caption h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.9rem, 4vw, 2.9rem);
            font-weight: 700;
            color: #fff;
            line-height: 1.25;
            margin-bottom: 12px;
        }

        .hero-caption p {
            font-size: 15px;
            color: rgba(255,255,255,0.70);
            font-weight: 300;
            margin: 0;
        }

        .hero-indicators {
            position: absolute;
            bottom: 28px;
            right: 48px;
            z-index: 20;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .hero-dot {
            width: 7px;
            height: 7px;
            border-radius: 100px;
            background: rgba(255,255,255,0.35);
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            padding: 0;
        }

        .hero-dot.active {
            width: 28px;
            background: #fff;
        }

        .hero-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 20;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.22);
            backdrop-filter: blur(6px);
            color: #fff;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        .hero-arrow:hover { background: rgba(55,138,221,0.50); }
        .hero-arrow.prev  { left: 20px; }
        .hero-arrow.next  { right: 20px; }

        /* ===== SECTION: QUICK INFO ===== */
        .section-info {
            background: #f0f6ff;
            padding: 56px 0 48px;
        }

        .section-title-group { margin-bottom: 36px; }

        .section-title-group .eyebrow {
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #185fa5;
            margin-bottom: 8px;
        }

        .section-title-group h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.45rem, 3vw, 2rem);
            font-weight: 700;
            color: #042c53;
            margin: 0;
        }

        .info-card {
            background: #fff;
            border-radius: 16px;
            padding: 26px 20px;
            border: 1px solid #daeaf8;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            height: 100%;
        }

        .info-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(24,95,165,0.11);
        }

        .info-card .ic-icon {
            width: 46px;
            height: 46px;
            background: #e6f1fb;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            color: #185fa5;
            margin-bottom: 14px;
        }

        .info-card .ic-label {
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            color: #378add;
            margin-bottom: 6px;
        }

        .info-card .ic-value {
            font-size: 14.5px;
            font-weight: 600;
            color: #042c53;
            line-height: 1.4;
            margin: 0;
        }

        /* ===== SECTION: DESKRIPSI ===== */
        .section-desc {
            padding: 64px 0;
            background: #fff;
        }

        .desc-text-col p {
            font-size: 15.5px;
            line-height: 1.85;
            color: #3a4a5c;
        }

        .desc-visual {
            background: linear-gradient(145deg, #042c53 0%, #185fa5 100%);
            border-radius: 20px;
            padding: 40px 36px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 270px;
        }

        .desc-visual .dv-number {
            font-family: 'Playfair Display', serif;
            font-size: 3.2rem;
            font-weight: 700;
            color: rgba(255,255,255,0.18);
            line-height: 1;
        }

        .desc-visual .dv-text {
            font-size: 15.5px;
            font-weight: 600;
            color: #b5d4f4;
            margin-top: 8px;
        }

        .desc-visual .dv-sub {
            font-size: 13px;
            color: rgba(255,255,255,0.45);
            margin-top: 4px;
        }

        .desc-visual .dv-divider {
            height: 1px;
            background: rgba(255,255,255,0.10);
            margin: 22px 0;
        }

        /* ===== SECTION: PERAN ===== */
        .section-peran {
            background: #f0f6ff;
            padding: 64px 0;
        }

        .peran-card {
            background: #fff;
            border-radius: 16px;
            padding: 30px 24px;
            border: 1px solid #daeaf8;
            border-top: 4px solid #185fa5;
            height: 100%;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .peran-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(24,95,165,0.11);
        }

        .peran-card .pc-icon {
            width: 50px;
            height: 50px;
            background: #e6f1fb;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            color: #185fa5;
            margin-bottom: 18px;
        }

        .peran-card h5 {
            font-size: 15px;
            font-weight: 700;
            color: #042c53;
            margin-bottom: 10px;
        }

        .peran-card p {
            font-size: 14px;
            color: #5a7190;
            line-height: 1.7;
            margin: 0;
        }

        @media (max-width: 768px) {
            .profil-hero { height: 380px; }
            .hero-caption { padding: 0 0 44px 28px; }
            .hero-caption h1 { font-size: 1.55rem; }
            .hero-indicators { right: 20px; bottom: 16px; }
        }
    </style>
</head>
<body>

<!-- NAVBAR (tidak diubah) -->
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
            <li class="nav-item">
                <i class="bi bi-house-door"></i>
                <a href="beranda.php">Beranda</a>
            </li>
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
            <li class="nav-item">
                <i class="bi bi-folder"></i>
                <a href="data.php">Data</a>
            </li>
            <li class="nav-item">
                <i class="bi bi-journal-text"></i>
                <a href="materi.php">Edukatif</a>
            </li>
            <li class="nav-item">
                <i class="bi bi-newspaper"></i>
                <a href="berita.php">Publikasi</a>
            </li>
            <li class="nav-item">
                <i class="bi bi-chat-dots"></i>
                <a href="saran.php">Saran</a>
            </li>
            <li class="nav-item">
                <a href="#" id="accessibility-btn">
                    <i class="bi bi-universal-access"></i> Aksibilitas
                </a>
            </li>
            <li class="nav-item">
                <a href="admin/auth/login.php" class="login-icon">
                    <i class="bi bi-person-fill"></i>
                </a>
            </li>
        </ul>
    </div>
</nav>


<!-- HERO SLIDER (diperbarui) -->
<section class="profil-hero">
    <div class="hero-slides" id="heroSlides">
        <div class="hero-slide active">
            <img src="img/img-slider/slide1.jpeg" alt="Slide 1">
        </div>
        <div class="hero-slide">
            <img src="img/img-slider/slide2.jpeg" alt="Slide 2">
        </div>
        <div class="hero-slide">
            <img src="img/img-slider/slide3.jpg" alt="Slide 3">
        </div>
        <div class="hero-slide">
            <img src="img/img-slider/slide4.jpg" alt="Slide 4">
        </div>
    </div>

    <div class="hero-caption">
        <span class="badge-label"><i class="bi bi-building"></i> Dinas Pendidikan Kab. Lamongan</span>
        <h1>Profil Bidang PAUD<br>dan Pendidikan Non Formal</h1>
        <p>Bidang PAUD dan PNF/Kesetaraan &mdash; Kabupaten Lamongan</p>
    </div>

    <button class="hero-arrow prev" id="heroPrev"><i class="bi bi-chevron-left"></i></button>
    <button class="hero-arrow next" id="heroNext"><i class="bi bi-chevron-right"></i></button>

    <div class="hero-indicators" id="heroIndicators">
        <button class="hero-dot active" data-index="0"></button>
        <button class="hero-dot" data-index="1"></button>
        <button class="hero-dot" data-index="2"></button>
        <button class="hero-dot" data-index="3"></button>
    </div>
</section>


<!-- QUICK INFO CARDS -->
<section class="section-info">
    <div class="container">
        <div class="section-title-group text-center">
            <p class="eyebrow">Identitas Bidang</p>
            <h2>Mengenal Bidang PAUD dan PNF</h2>
        </div>
        <div class="row g-4">
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="0">
                <div class="info-card">
                    <div class="ic-icon"><i class="bi bi-diagram-3"></i></div>
                    <p class="ic-label">Nama Bidang</p>
                    <p class="ic-value">PAUD dan Pendidikan Non Formal/Kesetaraan</p>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="80">
                <div class="info-card">
                    <div class="ic-icon"><i class="bi bi-building"></i></div>
                    <p class="ic-label">Unit Kerja</p>
                    <p class="ic-value">Bidang PAUD dan PNF</p>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="160">
                <div class="info-card">
                    <div class="ic-icon"><i class="bi bi-bank"></i></div>
                    <p class="ic-label">Instansi</p>
                    <p class="ic-value">Dinas Pendidikan</p>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="240">
                <div class="info-card">
                    <div class="ic-icon"><i class="bi bi-geo-alt"></i></div>
                    <p class="ic-label">Wilayah Kerja</p>
                    <p class="ic-value">Kabupaten Lamongan</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- DESKRIPSI BIDANG -->
<section class="section-desc">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-md-7" data-aos="fade-right">
                <div class="section-title-group mb-4">
                    <p class="eyebrow">Tentang Kami</p>
                    <h2>Deskripsi Bidang</h2>
                </div>
                <div class="desc-text-col">
                    <p>
                        Bidang PAUD dan Pendidikan Non Formal/Kesetaraan merupakan salah satu
                        bidang pada Dinas Pendidikan Kabupaten Lamongan yang memiliki peran
                        dalam mendukung arah kebijakan pendidikan anak usia dini serta
                        pendidikan non formal dan kesetaraan di tingkat daerah.
                    </p>
                    <p>
                        Bidang ini menjadi bagian penting dalam upaya pembangunan sumber daya
                        manusia melalui penguatan fondasi pendidikan sejak usia dini serta
                        perluasan akses pendidikan non formal dan kesetaraan.
                    </p>
                </div>
            </div>
            <div class="col-md-5" data-aos="fade-left">
                <div class="desc-visual">
                    <div class="dv-number">PAUD</div>
                    <div class="dv-text">Pendidikan Anak Usia Dini</div>
                    <div class="dv-sub">Dinas Pendidikan Kabupaten Lamongan</div>
                    <div class="dv-divider"></div>
                    <div class="dv-number">PNF</div>
                    <div class="dv-text">Pendidikan Non Formal &amp; Kesetaraan</div>
                    <div class="dv-sub">Perluasan akses pendidikan masyarakat</div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- PERAN BIDANG -->
<section class="section-peran">
    <div class="container">
        <div class="section-title-group text-center mb-5" data-aos="fade-up">
            <p class="eyebrow">Fungsi Strategis</p>
            <h2>Peran Bidang PAUD dan PNF</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
                <div class="peran-card">
                    <div class="pc-icon"><i class="bi bi-people"></i></div>
                    <h5>Penguatan PAUD</h5>
                    <p>Mendukung penguatan pendidikan anak usia dini di seluruh wilayah Kabupaten Lamongan secara merata dan berkualitas.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="peran-card">
                    <div class="pc-icon"><i class="bi bi-journal-check"></i></div>
                    <h5>Keselarasan Kebijakan PNF</h5>
                    <p>Mendorong keselarasan kebijakan pendidikan non formal dan kesetaraan agar sesuai dengan kebutuhan masyarakat.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="peran-card">
                    <div class="pc-icon"><i class="bi bi-bar-chart"></i></div>
                    <h5>Peningkatan Kualitas</h5>
                    <p>Menjadi bagian dari upaya peningkatan kualitas pendidikan daerah melalui program-program yang terukur dan berkelanjutan.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- FOOTER (tidak diubah) -->
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
    AOS.init({ duration: 900, once: false });

    (function () {
        var slides  = document.querySelectorAll('.hero-slide');
        var dots    = document.querySelectorAll('.hero-dot');
        var current = 0;
        var timer;

        function goTo(n) {
            slides[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = (n + slides.length) % slides.length;
            slides[current].classList.add('active');
            dots[current].classList.add('active');
        }

        function next() { goTo(current + 1); }
        function prev() { goTo(current - 1); }

        function startTimer() {
            clearInterval(timer);
            timer = setInterval(next, 4200);
        }

        document.getElementById('heroNext').addEventListener('click', function () { next(); startTimer(); });
        document.getElementById('heroPrev').addEventListener('click', function () { prev(); startTimer(); });

        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                goTo(parseInt(this.dataset.index));
                startTimer();
            });
        });

        startTimer();
    })();
</script>

<?php include 'includes/accessibility.php'; ?>
</body>
</html>