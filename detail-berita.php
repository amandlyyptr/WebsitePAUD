<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    mysqli_query($conn, "UPDATE counter SET jumlah = jumlah + 1 WHERE id = 1");
}

$result = mysqli_query($conn, "SELECT jumlah FROM counter WHERE id = 1");
$visitor_count = mysqli_fetch_assoc($result)['jumlah'];

if (!isset($_GET['id'])) {
    header("Location: berita.php");
    exit;
}

$id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM berita WHERE id='$id' AND status='publish'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Berita tidak ditemukan.";
    exit;
}

mysqli_query($conn, "UPDATE berita SET views = views + 1 WHERE id = $id");

$berita_terbaru = mysqli_query($conn, "
    SELECT * FROM berita WHERE status='publish' AND id != $id
    ORDER BY tanggal DESC LIMIT 4
");

$kategori_list = mysqli_query($conn, "
    SELECT kategori, COUNT(*) as jumlah
    FROM berita WHERE status='publish'
    GROUP BY kategori
");

$pengumuman_terbaru = mysqli_query($conn, "
    SELECT id, judul, tanggal FROM berita
    WHERE status='publish' AND kategori='pengumuman' AND id != $id
    ORDER BY tanggal DESC LIMIT 5
");

$sql_count      = "SELECT COUNT(*) as total FROM berita WHERE status='publish'";
$r_total        = mysqli_fetch_assoc(mysqli_query($conn, $sql_count));
$sql_count_view = "SELECT SUM(views) as total FROM berita WHERE status='publish'";
$r_views        = mysqli_fetch_assoc(mysqli_query($conn, $sql_count_view));

// Berita per bulan untuk kalender
$bulan_ini  = date('m');
$tahun_ini  = date('Y');
$berita_bln = mysqli_query($conn, "
    SELECT DAY(tanggal) as hari, COUNT(*) as jml
    FROM berita
    WHERE status='publish'
      AND MONTH(tanggal) = '$bulan_ini'
      AND YEAR(tanggal)  = '$tahun_ini'
    GROUP BY DAY(tanggal)
");
$hari_ada_berita = [];
while ($row = mysqli_fetch_assoc($berita_bln)) {
    $hari_ada_berita[(int)$row['hari']] = (int)$row['jml'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['judul']) ?> | PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/website-paud/css/detail-berita.css?v=3">

    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f6ff; }

        /* ── HERO ── */
        .hero-detail { background: #1565c0; padding: 48px 0 36px; }
        .hero-detail .breadcrumb-bar {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: #b5d4f4; margin-bottom: 16px;
        }
        .hero-detail .breadcrumb-bar a { color: #b5d4f4; text-decoration: none; }
        .hero-detail .breadcrumb-bar a:hover { color: #fff; }
        .hero-detail .breadcrumb-bar i { font-size: 10px; }
        .hero-kat-badge {
            display: inline-block; background: rgba(255,255,255,0.18);
            color: #fff; font-size: 11px; font-weight: 600;
            padding: 4px 14px; border-radius: 20px; margin-bottom: 14px;
            text-transform: capitalize; letter-spacing: 0.5px;
        }
        .hero-detail h1 { color: #fff; font-size: 24px; font-weight: 700; line-height: 1.5; margin-bottom: 16px; }
        .hero-meta-row { display: flex; flex-wrap: wrap; gap: 16px; align-items: center; }
        .hero-meta-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #b5d4f4; }
        .hero-meta-item i { font-size: 13px; color: #7db8f0; }

        /* ── LAYOUT ── */
        .detail-wrapper {
            max-width: 1200px; margin: 0 auto;
            padding: 32px 20px; display: flex; gap: 28px; align-items: flex-start;
        }

        /* ── ARTIKEL ── */
        .artikel-wrap { flex: 1; min-width: 0; }

        .artikel-card {
            background: #fff; border-radius: 16px;
            border-top: 4px solid #1565c0;
            box-shadow: 0 2px 12px rgba(21,101,192,0.07);
            overflow: hidden;
        }

        /* Gambar 1 langsung di atas tanpa judul */
        .gambar-berita { width: 100%; display: block; max-height: 420px; object-fit: cover; }

        /* Gambar 2 muncul di tengah isi */
        .gambar-tengah {
            display: block; margin: 24px -32px;
            width: calc(100% + 64px); max-height: 380px; object-fit: cover;
        }

        .artikel-body { padding: 28px 32px 32px; }

        .artikel-isi { font-size: 14px; color: #3a4a5c; line-height: 1.9; text-align: justify; }
        .artikel-isi p { margin-bottom: 14px; }

        /* Share bar */
        .share-bar {
            display: flex; align-items: center; gap: 10px;
            margin-top: 28px; padding-top: 20px;
            border-top: 2px solid #e3efff; flex-wrap: wrap;
        }
        .share-label { font-size: 13px; font-weight: 600; color: #0d47a1; }
        .share-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 16px; border-radius: 20px; font-size: 12px;
            font-weight: 600; text-decoration: none; transition: opacity 0.2s;
        }
        .share-btn:hover { opacity: 0.85; }
        .share-wa  { background: #25d366; color: #fff; }
        .share-fb  { background: #1877f2; color: #fff; }
        .share-tw  { background: #1da1f2; color: #fff; }
        .share-cp  { background: #e3efff; color: #1565c0; cursor: pointer; border: none; }

        .btn-kembali {
            display: inline-flex; align-items: center; gap: 8px;
            margin-top: 20px; padding: 9px 22px;
            background: #1565c0; color: #fff !important;
            border-radius: 24px; font-size: 13px; font-weight: 600;
            text-decoration: none !important; transition: background 0.2s;
        }
        .btn-kembali:hover { background: #0d47a1; }

        /* Berita terkait */
        .related-title {
            font-size: 15px; font-weight: 700; color: #0d47a1;
            margin: 32px 0 16px; padding-bottom: 10px;
            border-bottom: 2px solid #e3efff;
            display: flex; align-items: center; gap: 8px;
        }
        .related-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .rcard {
            background: #fff; border-radius: 12px; border-top: 3px solid #1565c0;
            box-shadow: 0 2px 8px rgba(21,101,192,0.06);
            overflow: hidden; text-decoration: none;
            transition: transform 0.2s; display: block;
        }
        .rcard:hover { transform: translateY(-3px); }
        .rcard img { width: 100%; height: 110px; object-fit: cover; display: block; }
        .rcard-body { padding: 12px 14px 14px; }
        .rcard-kat {
            font-size: 10px; background: #e3efff; color: #1565c0;
            padding: 2px 8px; border-radius: 10px; font-weight: 600;
            text-transform: capitalize; margin-bottom: 6px; display: inline-block;
        }
        .rcard-title {
            font-size: 12px; font-weight: 600; color: #0d47a1; line-height: 1.5;
            display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .rcard-date { font-size: 10px; color: #888; margin-top: 5px; }

        /* ── SIDEBAR ── */
        .detail-sidebar { width: 300px; flex-shrink: 0; }
        .sidebar-inner {
            background: #fff; border-radius: 16px; border-top: 4px solid #1565c0;
            box-shadow: 0 2px 12px rgba(21,101,192,0.07);
            padding: 22px 18px; display: flex; flex-direction: column; gap: 24px;
        }
        .sb-heading {
            font-size: 13px; font-weight: 700; color: #0d47a1;
            margin-bottom: 12px; padding-bottom: 8px;
            border-bottom: 2px solid #e3efff;
            display: flex; align-items: center; gap: 8px;
        }

        /* Statistik */
        .stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .stat-item { background: #e3efff; border-radius: 10px; padding: 12px 8px; text-align: center; }
        .stat-num { font-size: 22px; font-weight: 700; color: #1565c0; line-height: 1; }
        .stat-lbl { font-size: 10px; color: #5a6a7e; margin-top: 4px; line-height: 1.3; }

        /* Kategori */
        .kat-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 4px; }
        .kat-list li a {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 12px; border-radius: 8px; font-size: 12px;
            color: #444; text-decoration: none; transition: all 0.15s;
        }
        .kat-list li a:hover { background: #e3efff; color: #1565c0; font-weight: 600; }
        .kat-list li a .cat-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #1565c0; flex-shrink: 0; margin-right: 8px;
        }
        .kat-list li a .kat-left { display: flex; align-items: center; }
        .kat-badge-count {
            background: #1565c0; color: #fff; font-size: 10px;
            padding: 1px 8px; border-radius: 10px; font-weight: 600;
        }

        /* Mini Kalender */
        .mini-cal { width: 100%; }
        .cal-header { display: flex; align-items: center; justify-content: center; gap: 6px; margin-bottom: 10px; }
        .cal-header .cal-month { font-size: 13px; font-weight: 700; color: #0d47a1; }
        .cal-header .cal-year  { font-size: 11px; color: #7a8fa6; font-weight: 500; }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; text-align: center; }
        .cal-day-name { font-size: 10px; font-weight: 700; color: #7a8fa6; padding: 4px 0; text-transform: uppercase; }
        .cal-day-name:first-child { color: #e53935; }
        .cal-day-name:last-child  { color: #1565c0; }
        .cal-cell { font-size: 11px; font-weight: 500; color: #3a4a5c; padding: 5px 2px; border-radius: 6px; line-height: 1; }
        .cal-cell.sunday   { color: #e53935; }
        .cal-cell.saturday { color: #1565c0; }
        .cal-cell.today    { background: #1565c0; color: #fff !important; font-weight: 700; }
        .cal-cell.has-berita:not(.today) { background: #e3efff; color: #0d47a1; font-weight: 700; cursor: pointer; }
        .cal-cell.has-berita:not(.today):hover { background: #b5d4f4; }
        .cal-dot { display: block; width: 4px; height: 4px; border-radius: 50%; background: #f59e0b; margin: 2px auto 0; }
        .cal-cell.today .cal-dot { background: #fff; }
        .cal-legend { display: flex; align-items: center; gap: 12px; margin-top: 10px; justify-content: center; }
        .cal-legend-item { display: flex; align-items: center; gap: 4px; font-size: 10px; color: #7a8fa6; }
        .leg-dot { width: 8px; height: 8px; border-radius: 2px; }

        /* Pengumuman */
        .pengumuman-list { display: flex; flex-direction: column; gap: 10px; }
        .pengumuman-item {
            padding: 10px 12px; background: #f0f6ff;
            border-radius: 10px; border-left: 3px solid #1565c0;
            text-decoration: none; display: block; transition: all 0.15s;
        }
        .pengumuman-item:hover { background: #e3efff; }
        .pengumuman-item .p-title {
            font-size: 12px; font-weight: 600; color: #0d47a1; line-height: 1.4; margin-bottom: 4px;
            display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .pengumuman-item .p-date { font-size: 10px; color: #888; display: flex; align-items: center; gap: 4px; }

        /* Kontak */
        .kontak-list { display: flex; flex-direction: column; gap: 8px; }
        .kontak-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; background: #e3efff; border-radius: 10px; text-decoration: none;
        }
        .kontak-item i { color: #1565c0; font-size: 14px; flex-shrink: 0; }
        .kontak-item span { font-size: 11px; color: #0d47a1; font-weight: 500; word-break: break-all; }
        .btn-saran {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-top: 4px; padding: 10px; background: #1565c0;
            border-radius: 10px; text-decoration: none; transition: background 0.2s;
        }
        .btn-saran:hover { background: #0d47a1; }
        .btn-saran span { font-size: 12px; color: #fff; font-weight: 600; }

        /* Tag cloud */
        .tag-cloud { display: flex; flex-wrap: wrap; gap: 6px; }
        .tag-item {
            background: #e3efff; color: #1565c0; font-size: 11px;
            padding: 4px 12px; border-radius: 20px; text-decoration: none;
            font-weight: 500; transition: all 0.15s;
        }
        .tag-item:hover { background: #1565c0; color: #fff; }
    </style>
</head>
<body>

<!-- NAVBAR -->
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
            <li class="nav-item"><i class="bi bi-folder"></i> <a href="data.php">Data</a></li>
            <li class="nav-item"><i class="bi bi-journal-text"></i> <a href="materi.php">Edukatif</a></li>
            <li class="nav-item"><i class="bi bi-newspaper"></i> <a href="berita.php">Publikasi</a></li>
            <li class="nav-item"><i class="bi bi-chat-dots"></i> <a href="saran.php">Saran</a></li>
            <li class="nav-item"><a href="#" id="accessibility-btn"><i class="bi bi-universal-access"></i> Aksibilitas</a></li>
            <li class="nav-item"><a href="admin/auth/login.php" class="login-icon"><i class="bi bi-person-fill"></i></a></li>
        </ul>
    </div>
</nav>

<!-- HERO — judul tampil di sini saja, tidak diulang di bawah -->
<section class="hero-detail" data-aos="fade-down">
    <div class="container">
        <div class="breadcrumb-bar">
            <a href="beranda.php">Beranda</a>
            <i class="bi bi-chevron-right"></i>
            <a href="berita.php">Publikasi</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:#fff;">Detail Berita</span>
        </div>
        <span class="hero-kat-badge">
            <i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($data['kategori']) ?>
        </span>
        <h1><?= htmlspecialchars($data['judul']) ?></h1>
        <div class="hero-meta-row">
            <div class="hero-meta-item">
                <i class="bi bi-calendar3"></i>
                <?= date('l, d F Y', strtotime($data['tanggal'])) ?>
            </div>
            <div class="hero-meta-item">
                <i class="bi bi-eye"></i>
                <?= number_format($data['views']) ?> views
            </div>
            <div class="hero-meta-item">
                <i class="bi bi-person-fill"></i>
                Sekretariat PAUD
            </div>
        </div>
    </div>
</section>

<!-- KONTEN -->
<div style="background:#f0f6ff;">
<div class="detail-wrapper">

    <!-- KIRI: ARTIKEL -->
    <div class="artikel-wrap">
        <div class="artikel-card" data-aos="fade-up">

            <!-- GAMBAR 1 langsung tanpa judul lagi -->
            <?php if (!empty($data['gambar'])): ?>
                <img src="<?= $data['gambar'] ?>" alt="<?= htmlspecialchars($data['judul']) ?>" class="gambar-berita">
            <?php endif; ?>

            <div class="artikel-body">
                <?php
                $isi          = $data['isi'];
                $isi_awal     = $isi;
                $isi_lanjutan = '';

                if (!empty($data['gambar2'])) {
                    $paragraphs = preg_split('/\n\s*\n/', trim($isi));
                    $total      = count($paragraphs);
                    if ($total > 1) {
                        $half         = (int) ceil($total / 2);
                        $isi_awal     = implode("\n\n", array_slice($paragraphs, 0, $half));
                        $isi_lanjutan = implode("\n\n", array_slice($paragraphs, $half));
                    } else {
                        $kalimat  = preg_split('/(?<=[.!?])\s+/', trim($isi));
                        $k_half   = (int) ceil(count($kalimat) / 2);
                        $isi_awal     = implode(' ', array_slice($kalimat, 0, $k_half));
                        $isi_lanjutan = implode(' ', array_slice($kalimat, $k_half));
                    }
                }
                ?>

                <!-- ISI BAGIAN 1 -->
                <div class="artikel-isi">
                    <?= nl2br(htmlspecialchars_decode(htmlspecialchars($isi_awal, ENT_NOQUOTES))) ?>
                </div>

                <!-- GAMBAR 2 di tengah (jika ada) -->
                <?php if (!empty($data['gambar2'])): ?>
                    <img src="<?= $data['gambar2'] ?>"
                         alt="<?= htmlspecialchars($data['judul']) ?>"
                         class="gambar-tengah">
                    <?php if (!empty($isi_lanjutan)): ?>
                    <div class="artikel-isi" style="margin-top:20px;">
                        <?= nl2br(htmlspecialchars_decode(htmlspecialchars($isi_lanjutan, ENT_NOQUOTES))) ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- SHARE -->
                <div class="share-bar">
                    <span class="share-label"><i class="bi bi-share-fill me-1"></i> Bagikan:</span>
                    <?php
                    $url       = urlencode("https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
                    $judul_enc = urlencode($data['judul']);
                    ?>
                    <a href="https://wa.me/?text=<?= $judul_enc ?>%20<?= $url ?>" target="_blank" class="share-btn share-wa">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $url ?>" target="_blank" class="share-btn share-fb">
                        <i class="bi bi-facebook"></i> Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=<?= $judul_enc ?>&url=<?= $url ?>" target="_blank" class="share-btn share-tw">
                        <i class="bi bi-twitter-x"></i> Twitter
                    </a>
                    <button class="share-btn share-cp" onclick="navigator.clipboard.writeText(window.location.href);this.innerHTML='<i class=\'bi bi-check-lg\'></i> Tersalin!'">
                        <i class="bi bi-link-45deg"></i> Salin Link
                    </button>
                </div>

                <a href="berita.php" class="btn-kembali">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Berita
                </a>
            </div>
        </div>

        <!-- BERITA TERKAIT -->
        <?php
        mysqli_data_seek($berita_terbaru, 0);
        $ada_terkait = mysqli_num_rows($berita_terbaru) > 0;
        ?>
        <?php if ($ada_terkait): ?>
        <div class="related-title" data-aos="fade-up">
            <i class="bi bi-newspaper"></i> Berita Lainnya
        </div>
        <div class="related-grid" data-aos="fade-up">
            <?php while ($r = mysqli_fetch_assoc($berita_terbaru)): ?>
            <a href="detail-berita.php?id=<?= $r['id'] ?>" class="rcard">
                <img src="<?= $r['gambar'] ?>" alt="<?= htmlspecialchars($r['judul']) ?>">
                <div class="rcard-body">
                    <span class="rcard-kat"><?= htmlspecialchars($r['kategori']) ?></span>
                    <div class="rcard-title"><?= htmlspecialchars($r['judul']) ?></div>
                    <div class="rcard-date"><i class="bi bi-calendar3" style="font-size:9px;"></i> <?= date('d M Y', strtotime($r['tanggal'])) ?></div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- KANAN: SIDEBAR -->
    <div class="detail-sidebar" data-aos="fade-left">
        <div class="sidebar-inner">

            <!-- STATISTIK -->
            <div>
                <div class="sb-heading"><i class="bi bi-bar-chart-line"></i> Statistik</div>
                <div class="stat-grid">
                    <div class="stat-item">
                        <div class="stat-num"><?= $r_total['total'] ?></div>
                        <div class="stat-lbl">Total Berita</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num"><?= number_format($data['views']) ?></div>
                        <div class="stat-lbl">Views Artikel Ini</div>
                    </div>
                </div>
            </div>

            <!-- KATEGORI -->
            <div>
                <div class="sb-heading"><i class="bi bi-grid"></i> Kategori Berita</div>
                <ul class="kat-list">
                    <?php
                    mysqli_data_seek($kategori_list, 0);
                    while ($k = mysqli_fetch_assoc($kategori_list)):
                    ?>
                    <li>
                        <a href="berita.php?kategori=<?= urlencode($k['kategori']) ?>">
                            <span class="kat-left">
                                <span class="cat-dot"></span>
                                <?= ucwords(htmlspecialchars($k['kategori'])) ?>
                            </span>
                            <span class="kat-badge-count"><?= $k['jumlah'] ?></span>
                        </a>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- MINI KALENDER -->
            <div>
                <div class="sb-heading"><i class="bi bi-calendar3"></i> Kalender Publikasi</div>
                <?php
                $nama_bulan = [
                    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                    5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                    9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                ];
                $first_day   = mktime(0, 0, 0, $bulan_ini, 1, $tahun_ini);
                $start_dow   = (int) date('w', $first_day);
                $days_in_mon = (int) date('t', $first_day);
                $today_day   = (int) date('j');
                ?>
                <div class="mini-cal">
                    <div class="cal-header">
                        <span class="cal-month"><?= $nama_bulan[(int)$bulan_ini] ?></span>
                        <span class="cal-year"><?= $tahun_ini ?></span>
                    </div>
                    <div class="cal-grid">
                        <?php foreach (['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $h): ?>
                            <div class="cal-day-name"><?= $h ?></div>
                        <?php endforeach; ?>
                        <?php for ($e = 0; $e < $start_dow; $e++): ?>
                            <div class="cal-cell empty"></div>
                        <?php endfor; ?>
                        <?php for ($d = 1; $d <= $days_in_mon; $d++):
                            $dow   = ($start_dow + $d - 1) % 7;
                            $cls   = '';
                            if ($dow === 0) $cls .= ' sunday';
                            if ($dow === 6) $cls .= ' saturday';
                            if ($d === $today_day) $cls .= ' today';
                            $has_b = isset($hari_ada_berita[$d]);
                            if ($has_b) $cls .= ' has-berita';
                            $tgl_link = $tahun_ini.'-'.str_pad($bulan_ini,2,'0',STR_PAD_LEFT).'-'.str_pad($d,2,'0',STR_PAD_LEFT);
                        ?>
                        <?php if ($has_b && $d !== $today_day): ?>
                            <a href="berita.php?tanggal=<?= $tgl_link ?>" title="<?= $hari_ada_berita[$d] ?> berita"
                               class="cal-cell<?= $cls ?>" style="text-decoration:none;color:inherit;">
                                <?= $d ?><span class="cal-dot"></span>
                            </a>
                        <?php else: ?>
                            <div class="cal-cell<?= $cls ?>">
                                <?= $d ?>
                                <?php if ($has_b): ?><span class="cal-dot"></span><?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <div class="cal-legend">
                        <div class="cal-legend-item">
                            <div class="leg-dot" style="background:#1565c0;border-radius:50%;"></div> Hari ini
                        </div>
                        <div class="cal-legend-item">
                            <div class="leg-dot" style="background:#e3efff;border:1px solid #b5d4f4;"></div> Ada berita
                        </div>
                    </div>
                </div>
            </div>

            <!-- PENGUMUMAN TERBARU -->
            <div>
                <div class="sb-heading"><i class="bi bi-megaphone"></i> Pengumuman Terbaru</div>
                <div class="pengumuman-list">
                    <?php
                    mysqli_data_seek($pengumuman_terbaru, 0);
                    while ($p = mysqli_fetch_assoc($pengumuman_terbaru)):
                    ?>
                    <a href="detail-berita.php?id=<?= $p['id'] ?>" class="pengumuman-item">
                        <div class="p-title"><?= htmlspecialchars($p['judul']) ?></div>
                        <div class="p-date">
                            <i class="bi bi-calendar3" style="font-size:9px;"></i>
                            <?= date('d M Y', strtotime($p['tanggal'])) ?>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- TAG -->
            <div>
                <div class="sb-heading"><i class="bi bi-tags"></i> Tag Populer</div>
                <div class="tag-cloud">
                    <a href="berita.php?kategori=paud"       class="tag-item">PAUD</a>
                    <a href="berita.php?kategori=pengumuman" class="tag-item">Pengumuman</a>
                    <a href="berita.php?kategori=event"      class="tag-item">Event</a>
                    <a href="berita.php?kategori=pnf"        class="tag-item">Non Formal</a>
                    <a href="berita.php?kategori=sarpras"    class="tag-item">Sarpras</a>
                </div>
            </div>

            <!-- HUBUNGI KAMI -->
            <div>
                <div class="sb-heading"><i class="bi bi-telephone"></i> Hubungi Kami</div>
                <div class="kontak-list">
                    <a href="mailto:disdik@lamongankab.go.id" class="kontak-item">
                        <i class="bi bi-envelope-fill"></i>
                        <span>disdik@lamongankab.go.id</span>
                    </a>
                    <a href="tel:03223210221" class="kontak-item">
                        <i class="bi bi-telephone-fill"></i>
                        <span>(0322) 321021</span>
                    </a>
                    <a href="https://wa.me/6281246676547" target="_blank" class="kontak-item">
                        <i class="bi bi-whatsapp"></i>
                        <span>+62 812-4667-6547</span>
                    </a>
                    <a href="saran.php" class="btn-saran">
                        <i class="bi bi-chat-dots-fill" style="color:#fff;font-size:13px;"></i>
                        <span>Kirim Saran &amp; Masukan</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
</div>

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
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init({ duration: 900, once: true });</script>
<?php include 'includes/accessibility.php'; ?>
</body>
</html>