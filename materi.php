<?php
session_start();
include 'includes/db.php';

if(!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    mysqli_query($conn, "UPDATE counter SET jumlah = jumlah + 1 WHERE id = 1");
}

$result = mysqli_query($conn, "SELECT jumlah FROM counter WHERE id = 1");
$visitor_count = mysqli_fetch_assoc($result)['jumlah'];

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

if($cari != ''){
    $data = mysqli_query($conn,"SELECT * FROM materi WHERE judul LIKE '%$cari%' ORDER BY created_at DESC");
} else {
    $data = mysqli_query($conn,"SELECT * FROM materi ORDER BY id DESC");
}

function getYouTubeID($url) {
    $url = trim($url);
    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $match)) return $match[1];
    if (preg_match('/v=([a-zA-Z0-9_-]+)/', $url, $match)) return $match[1];
    if (preg_match('/shorts\/([a-zA-Z0-9_-]+)/', $url, $match)) return $match[1];
    return null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Materi PAUD | PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Fraunces:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/materi.css?v=5">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4fa;
            color: #1a1a2e;
        }

        .materi-header {
            background: linear-gradient(135deg, #0a1f44 0%, #1a4080 60%, #2563b8 100%);
            padding: 64px 0 84px;
            position: relative;
            overflow: hidden;
        }
        .materi-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 60% 80% at 90% 50%, rgba(99, 163, 255, 0.18) 0%, transparent 70%);
        }
        .materi-header::after {
            content: 'PAUD';
            position: absolute;
            right: -20px;
            bottom: -30px;
            font-family: 'Fraunces', serif;
            font-size: 180px;
            font-weight: 700;
            color: rgba(255,255,255,0.04);
            line-height: 1;
            pointer-events: none;
            letter-spacing: -4px;
        }
        .header-inner {
            display: flex;
            align-items: center;
            gap: 40px;
            position: relative;
            z-index: 1;
        }
        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 100px;
            padding: 6px 14px;
            color: rgba(255,255,255,0.9);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
        .header-badge i { color: #7ec8ff; font-size: 10px; }
        .header-text h1 {
            font-family: 'Fraunces', serif;
            font-size: 42px;
            font-weight: 700;
            color: #fff;
            line-height: 1.1;
            margin-bottom: 14px;
            letter-spacing: -1px;
        }
        .header-text h1 span { color: #7ec8ff; }
        .header-text p {
            color: rgba(255,255,255,0.75);
            font-size: 15px;
            line-height: 1.7;
            max-width: 480px;
            margin: 0;
        }
        .header-deco {
            flex-shrink: 0;
            width: 160px;
            height: 160px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 72px;
            transform: rotate(-6deg);
        }

    

        .materi-section { padding: 48px 0 64px; }
        .section-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }
        .section-meta h2 {
            font-family: 'Fraunces', serif;
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: -.5px;
            margin: 0;
        }
        .section-meta span { font-size: 13px; color: #6b84a8; font-weight: 500; }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .video-card {
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #dde6f3;
            transition: transform .25s, box-shadow .25s;
            text-decoration: none;
            display: block;
            color: inherit;
        }
        .video-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 36px rgba(10, 31, 68, 0.13);
            text-decoration: none;
            color: inherit;
        }
        .thumb-box {
            position: relative;
            aspect-ratio: 16/9;
            overflow: hidden;
            background: #1a1a2e;
        }
        .thumb-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s;
            display: block;
        }
        .video-card:hover .thumb-box img { transform: scale(1.05); }
        .play-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(10, 31, 68, 0.35);
            opacity: 0;
            transition: opacity .25s;
        }
        .video-card:hover .play-overlay { opacity: 1; }
        .play-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: rgba(255,255,255,0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .play-circle i { font-size: 18px; color: #0a1f44; margin-left: 3px; }
        .vid-tag {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(10, 31, 68, 0.82);
            backdrop-filter: blur(4px);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 100px;
        }
        .card-body-custom { padding: 16px 18px 12px; }
        .card-body-custom h3 {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0 0 8px 0; /* ← tambah margin bawah agar ada jarak ke deskripsi */
        }

        /* ===== TAMBAHAN: ISI EDUKATIF ===== */
        .card-deskripsi {
            font-size: 12.5px;
            color: #6b84a8;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0;
        }

        .card-footer-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 18px;
            border-top: 1px solid #edf1f9;
        }
        .date-text {
            font-size: 12px;
            color: #a0b4cc;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .watch-btn {
            font-size: 12px;
            font-weight: 700;
            color: #2563b8;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .video-card:hover .watch-btn { color: #0a1f44; }

        .more-item { display: none; }

        .show-more-wrap { text-align: center; margin-top: 36px; }
        #btnShow {
            background: transparent;
            border: 1.5px solid #2563b8;
            color: #2563b8;
            border-radius: 12px;
            padding: 12px 32px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
        }
        #btnShow:hover { background: #2563b8; color: #fff; }

        .scroll-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #ffd966;
            color: #0c85cf;
            padding: 10px 14px;
            border-radius: 50%;
            font-size: 18px;
            z-index: 999;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: 0.3s;
        }
        .scroll-top:hover { transform: translateY(-4px); background: white; }

        @media (max-width: 768px) {
            .materi-header { padding: 40px 0 56px; }
            .header-inner { flex-direction: column; gap: 24px; }
            .header-text h1 { font-size: 30px; }
            .header-deco { display: none; }
            .section-meta { flex-direction: column; align-items: flex-start; gap: 6px; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="flat-navbar">
        <div class="container d-flex justify-content-between align-items-center flex-wrap">
            <a class="navbar-brand d-flex align-items-center gap-2" href="beranda.php">
                <img src="img/logo-dispendik.png" alt="Logo" class="logo">
                <span class="brand-text">
                    Bidang PAUD dan Pendidikan Non Formal/Kesetaraan<br>
                    <small>Dinas Pendidikan Kab. Lamongan</small>
                </span>
            </a>
            <ul class="nav-list">
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
                <li class="nav-item active"><i class="bi bi-journal-text"></i><a href="materi.php">Edukatif</a></li>
                <li class="nav-item"><i class="bi bi-newspaper"></i><a href="berita.php">Publikasi</a></li>
                <li class="nav-item"><i class="bi bi-chat-dots"></i><a href="saran.php">Saran</a></li>
                <li class="nav-item">
                    <a href="#" id="accessibility-btn"><i class="bi bi-universal-access"></i> Aksibilitas</a>
                </li>
                <li class="nav-item">
                    <a href="admin/auth/login.php" class="login-icon"><i class="bi bi-person-fill"></i></a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- HEADER -->
    <section class="materi-header" data-aos="fade-down">
        <div class="container">
            <div class="header-inner">
                <div class="header-text">
                    <div class="header-badge">
                        <i class="bi bi-play-fill"></i> Konten Edukatif
                    </div>
                    <h1>Materi <span>PAUD</span><br>Lamongan</h1>
                    <p>
                        Kumpulan video, panduan, dan bahan literasi Pendidikan Anak Usia Dini (PAUD)
                        yang dapat ditonton secara daring maupun diunduh oleh pengguna.
                    </p>
                </div>
                <div class="header-deco">📚</div>
            </div>
        </div>
    </section>

    

    <!-- GRID MATERI -->
    <section class="materi-section" data-aos="fade-up">
        <div class="container">

            <div class="section-meta">
                <h2>Semua Materi</h2>
                <span>
                    <?php
                    $total = mysqli_num_rows($data);
                    echo "Menampilkan $total materi";
                    mysqli_data_seek($data, 0);
                    ?>
                </span>
            </div>

            <div class="video-grid">

                <?php
                $no = 0;
                while ($m = mysqli_fetch_assoc($data)):
                    $no++;
                    $video_url   = htmlspecialchars($m['video_url']);
                    $video_id    = getYouTubeID($m['video_url']);
                    $thumbnail   = $video_id
                        ? "https://img.youtube.com/vi/{$video_id}/hqdefault.jpg"
                        : "https://via.placeholder.com/640x360?text=Video+Tidak+Valid";
                    $tanggal     = date('d M Y', strtotime($m['created_at']));
                    $judul       = htmlspecialchars($m['judul']);

                    /* ===== AMBIL ISI EDUKATIF ===== */
                    $isi_edukatif = !empty($m['isi_edukatif'])
                        ? htmlspecialchars(strip_tags($m['isi_edukatif']))
                        : '';

                    $hidden = $no > 12 ? 'more-item' : '';
                ?>

                <a href="<?= $video_url ?>" target="_blank" class="video-card <?= $hidden ?>">
                    <div class="thumb-box">
                        <img src="<?= $thumbnail ?>" alt="<?= $judul ?>">
                        <div class="play-overlay">
                            <div class="play-circle">
                                <i class="bi bi-play-fill"></i>
                            </div>
                        </div>
                        <span class="vid-tag">
                            <i class="bi bi-play-fill me-1"></i>Video
                        </span>
                    </div>
                    <div class="card-body-custom">
                        <h3><?= $judul ?></h3>

                        <?php if ($isi_edukatif): ?>
                        <p class="card-deskripsi"><?= $isi_edukatif ?></p>
                        <?php endif; ?>

                    </div>
                    <div class="card-footer-row">
                        <span class="date-text">
                            <i class="bi bi-calendar3"></i>
                            <?= $tanggal ?>
                        </span>
                        <span class="watch-btn">
                            Tonton <i class="bi bi-arrow-right"></i>
                        </span>
                    </div>
                </a>

                <?php endwhile; ?>

            </div>

            <?php if($no > 12): ?>
            <div class="show-more-wrap">
                <button id="btnShow">
                    Tampilkan Semua <i class="bi bi-chevron-down ms-1"></i>
                </button>
            </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- FOOTER -->
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

        const btnShow = document.getElementById('btnShow');
        if (btnShow) {
            btnShow.addEventListener('click', function () {
                document.querySelectorAll('.more-item').forEach(el => {
                    el.style.display = 'block';
                });
                this.style.display = 'none';
            });
        }

        document.querySelector('.scroll-top').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>

    <?php include 'includes/accessibility.php'; ?>
</body>
</html>