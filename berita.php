<?php
session_start();
include 'includes/db.php';

if(!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    mysqli_query($conn, "UPDATE counter SET jumlah = jumlah + 1 WHERE id = 1");
}

$result = mysqli_query($conn, "SELECT jumlah FROM counter WHERE id = 1");
$visitor_count = mysqli_fetch_assoc($result)['jumlah'];

$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

$sql = "SELECT * FROM berita WHERE status='publish'";
if (!empty($kategori)) $sql .= " AND kategori='$kategori'";
if (!empty($cari)) $sql .= " AND (judul LIKE '%$cari%' OR isi LIKE '%$cari%')";
$sql .= " ORDER BY tanggal DESC";

$batas = 6;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman - 1) * $batas;

$total_query = mysqli_query($conn, $sql);
$total_data = mysqli_num_rows($total_query);
$total_halaman = ceil($total_data / $batas);

$sql_latest = "SELECT * FROM berita WHERE status='publish' ORDER BY tanggal DESC LIMIT 4";
$berita_terbaru = mysqli_query($conn, $sql_latest);

$sql_count = "SELECT COUNT(*) as total FROM berita WHERE status='publish'";
$r_total = mysqli_fetch_assoc(mysqli_query($conn, $sql_count));

$sql_count_bulan = "SELECT COUNT(*) as total FROM berita WHERE status='publish' AND MONTH(tanggal)=MONTH(NOW()) AND YEAR(tanggal)=YEAR(NOW())";
$r_bulan = mysqli_fetch_assoc(mysqli_query($conn, $sql_count_bulan));

$sql_count_kat = "SELECT COUNT(DISTINCT kategori) as total FROM berita WHERE status='publish'";
$r_kat = mysqli_fetch_assoc(mysqli_query($conn, $sql_count_kat));

$sql .= " LIMIT $halaman_awal, $batas";
$berita = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita | PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="/website-paud/css/berita.css?v=4">

    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f6ff; }

        
        /* LAYOUT — flush ke hero dan footer, tanpa margin vertikal */
        .berita-section {
            display: flex;
            align-items: stretch;
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 20px 32px;
        }

        /* MAIN */
        .berita-main {
            flex: 1;
            min-width: 0;
            padding-right: 28px;
        }

        /* GRID KARTU */
        .berita-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .bcard {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            border-top: 4px solid #1565c0;
            box-shadow: 0 2px 10px rgba(21,101,192,0.07);
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }
        .bcard:hover { transform: translateY(-3px); }
        .bcard .berita-img {
            width: 100%; height: 160px; object-fit: cover; display: block;
        }
        .bcard-body {
            padding: 16px; display: flex; flex-direction: column; flex: 1;
        }
        .bcard-meta {
            display: flex; align-items: center; gap: 8px; margin-bottom: 8px; flex-wrap: wrap;
        }
        .bcard-date { font-size: 11px; color: #888; display: flex; align-items: center; gap: 4px; }
        .badge-kat {
            font-size: 10px; padding: 2px 10px; border-radius: 20px;
            background: #e3efff; color: #1565c0; font-weight: 600; text-transform: capitalize;
        }
        .bcard-title { font-size: 14px; font-weight: 600; color: #0d47a1; margin-bottom: 7px; line-height: 1.5; }
        .bcard-text  { font-size: 12px; color: #5a6a7e; line-height: 1.7; margin-bottom: 14px; flex: 1; }
        .btn-baca {
            display: inline-block; background: #1565c0; color: #fff !important;
            font-size: 12px; padding: 6px 18px; border-radius: 20px;
            text-decoration: none !important; font-weight: 500; align-self: flex-start;
            transition: background 0.2s;
        }
        .btn-baca:hover { background: #0d47a1; }

        /* PAGINATION */
        .pagination-wrap { display: flex; justify-content: center; gap: 6px; margin-top: 28px; }
        .pagination-wrap .page-link {
            width: 34px; height: 34px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; border: 1px solid #b5d4f4;
            color: #1565c0; background: #fff; text-decoration: none;
        }
        .pagination-wrap .page-link.active,
        .pagination-wrap .page-link:hover { background: #1565c0; color: #fff; border-color: #1565c0; }

        /* SIDEBAR */
        .berita-sidebar { width: 280px; flex-shrink: 0; display: flex; flex-direction: column; }

        .sidebar-inner {
            background: #fff;
            border-radius: 14px;
            border-top: 4px solid #1565c0;
            box-shadow: 0 2px 10px rgba(21,101,192,0.07);
            padding: 22px 18px;
            display: flex;
            flex-direction: column;
            gap: 22px;
            flex: 1;              /* isi penuh tinggi sidebar */
        }

        .sb-heading {
            font-size: 13px; font-weight: 700; color: #0d47a1;
            margin-bottom: 12px; padding-bottom: 8px;
            border-bottom: 2px solid #e3efff;
            display: flex; align-items: center; gap: 8px;
        }

        /* SEARCH */
        .sb-search input {
            width: 100%; padding: 9px 12px;
            border: 1.5px solid #b5d4f4; border-radius: 8px;
            font-size: 12px; font-family: 'Poppins', sans-serif;
            color: #333; outline: none; margin-bottom: 8px;
        }
        .sb-search input:focus { border-color: #1565c0; }
        .sb-search button {
            width: 100%; background: #1565c0; color: #fff; border: none;
            border-radius: 8px; padding: 9px; font-size: 12px;
            font-family: 'Poppins', sans-serif; font-weight: 600; cursor: pointer;
        }
        .sb-search button:hover { background: #0d47a1; }

        /* STATISTIK */
        .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .stat-item {
            background: #e3efff; border-radius: 10px; padding: 10px 6px;
            text-align: center;
        }
        .stat-item .stat-num { font-size: 20px; font-weight: 700; color: #1565c0; line-height: 1; }
        .stat-item .stat-lbl { font-size: 10px; color: #5a6a7e; margin-top: 4px; line-height: 1.3; }

        /* KATEGORI */
        .cat-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 3px; }
        .cat-list li a {
            display: flex; align-items: center; gap: 10px;
            font-size: 12px; color: #444; padding: 8px 10px;
            border-radius: 8px; text-decoration: none; transition: all 0.15s;
        }
        .cat-list li a:hover,
        .cat-list li a.aktif { background: #e3efff; color: #1565c0; font-weight: 600; }
        .cat-dot { width: 8px; height: 8px; border-radius: 50%; background: #1565c0; flex-shrink: 0; }

        /* BERITA TERBARU di sidebar */
        .latest-list { display: flex; flex-direction: column; gap: 10px; }
        .latest-item {
            display: flex; gap: 10px; align-items: flex-start;
            padding-bottom: 10px; border-bottom: 1px solid #e3efff;
        }
        .latest-item:last-child { border-bottom: none; padding-bottom: 0; }
        .latest-img {
            width: 52px; height: 48px; border-radius: 8px;
            object-fit: cover; flex-shrink: 0; background: #b5d4f4;
        }
        .latest-info { flex: 1; min-width: 0; }
        .latest-title {
            font-size: 11px; font-weight: 600; color: #0d47a1;
            line-height: 1.4; margin-bottom: 3px;
            display: -webkit-box; -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
        }
        .latest-date { font-size: 10px; color: #888; }

        /* TAG CLOUD */
        .tag-cloud { display: flex; flex-wrap: wrap; gap: 6px; }
        .tag-item {
            background: #e3efff; color: #1565c0; font-size: 11px;
            padding: 4px 12px; border-radius: 20px; text-decoration: none;
            font-weight: 500; transition: all 0.15s;
        }
        .tag-item:hover { background: #1565c0; color: #fff; }

        /* EMPTY STATE */
        .empty-state {
            background: #e3efff; border-radius: 12px; padding: 32px;
            text-align: center; color: #1565c0; font-size: 14px;
        }
    </style>
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
            <li class="nav-item active"><i class="bi bi-newspaper"></i> <a href="berita.php">Publikasi</a></li>
            <li class="nav-item"><i class="bi bi-chat-dots"></i> <a href="saran.php">Saran</a></li>
            <li class="nav-item"><a href="#" id="accessibility-btn"><i class="bi bi-universal-access"></i> Aksibilitas</a></li>
            <li class="nav-item"><a href="admin/auth/login.php" class="login-icon"><i class="bi bi-person-fill"></i></a></li>
        </ul>
    </div>
</nav>

<!-- HERO -->
<section class="hero-berita" data-aos="fade-down" style="background: linear-gradient(135deg, #0d47a1, #1565c0, #1e88e5) !important; padding: 32px 0 28px !important; text-align: center; position: relative; overflow: hidden; height: auto !important; min-height: unset !important;">
    <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.06);"></div>
    <div style="position: absolute; bottom: -60px; right: -30px; width: 220px; height: 220px; border-radius: 50%; background: rgba(255,255,255,0.06);"></div>
    <div style="position: absolute; top: 20px; right: 15%; width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.04);"></div>
    <div class="container" style="position: relative; z-index: 1;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,0.15); border: 2px solid rgba(255,255,255,0.25); margin-bottom: 16px;" data-aos="zoom-in">
            <i class="bi bi-newspaper" style="font-size: 28px; color: #fff;"></i>
        </div>
        <h1 class="fw-bold mb-2" style="color: #fff; font-size: 2rem; letter-spacing: 0.5px;" data-aos="fade-down">Berita &amp; Informasi PAUD</h1>
        <p style="color: rgba(255,255,255,0.75); font-size: 0.95rem; max-width: 580px; margin: 10px auto 0;" data-aos="fade-up">
            Informasi terbaru kegiatan dan program Bidang PAUD
        </p>
        <div style="width: 50px; height: 3px; background: rgba(255,255,255,0.4); border-radius: 4px; margin: 16px auto 0;" data-aos="fade-up"></div>
    </div>
</section>

<!-- MAIN SECTION -->
<div style="background:#f0f6ff; padding-bottom: 0;">
<div class="berita-section">

    <!-- KONTEN KIRI -->
    <div class="berita-main">
        <?php if (mysqli_num_rows($berita) > 0): ?>
            <div class="berita-grid">
                <?php $no = 0; while ($b = mysqli_fetch_assoc($berita)): $no++; ?>
                <div class="bcard" data-aos="fade-up" data-aos-delay="<?= $no * 80 ?>">
                    <img src="<?= $b['gambar'] ?>" class="berita-img" alt="<?= htmlspecialchars($b['judul']) ?>">
                    <div class="bcard-body">
                        <div class="bcard-meta">
                            <span class="bcard-date">
                                <i class="bi bi-calendar3" style="font-size:11px;"></i>
                                <?= date('d F Y', strtotime($b['tanggal'])) ?>
                            </span>
                            <span class="badge-kat"><?= htmlspecialchars($b['kategori']) ?></span>
                        </div>
                        <div class="bcard-title"><?= htmlspecialchars($b['judul']) ?></div>
                        <div class="bcard-text"><?= substr(strip_tags($b['isi']), 0, 110) ?>...</div>
                        <a href="detail-berita.php?id=<?= $b['id'] ?>" class="btn-baca">Baca Selengkapnya</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="pagination-wrap">
                <?php if ($halaman > 1): ?>
                    <a class="page-link" href="?halaman=<?= $halaman-1 ?>&kategori=<?= urlencode($kategori) ?>&cari=<?= urlencode($cari) ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                    <a class="page-link <?= ($i == $halaman) ? 'active' : '' ?>"
                       href="?halaman=<?= $i ?>&kategori=<?= urlencode($kategori) ?>&cari=<?= urlencode($cari) ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                <?php if ($halaman < $total_halaman): ?>
                    <a class="page-link" href="?halaman=<?= $halaman+1 ?>&kategori=<?= urlencode($kategori) ?>&cari=<?= urlencode($cari) ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-newspaper" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                Belum ada berita yang dipublikasikan.
            </div>
        <?php endif; ?>
    </div>

    <!-- SIDEBAR KANAN -->
    <div class="berita-sidebar" data-aos="fade-left">
        <div class="sidebar-inner">

            <!-- CARI -->
            <div>
                <div class="sb-heading"><i class="bi bi-search"></i> Cari Publikasi</div>
                <div class="sb-search">
                    <form method="GET">
                        <input type="text" name="cari" placeholder="Ketik kata kunci..." value="<?= htmlspecialchars($cari) ?>">
                        <?php if (!empty($kategori)): ?>
                            <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori) ?>">
                        <?php endif; ?>
                        <button type="submit"><i class="bi bi-search me-1"></i> Cari</button>
                    </form>
                </div>
            </div>

            <!-- STATISTIK -->
            <div>
                <div class="sb-heading"><i class="bi bi-bar-chart-line"></i> Statistik Publikasi</div>
                <div class="stat-grid">
                    <div class="stat-item">
                        <div class="stat-num"><?= $r_total['total'] ?></div>
                        <div class="stat-lbl">Total Berita</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num"><?= $r_bulan['total'] ?></div>
                        <div class="stat-lbl">Bulan Ini</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num"><?= $r_kat['total'] ?></div>
                        <div class="stat-lbl">Kategori</div>
                    </div>
                </div>
            </div>

            <!-- KATEGORI -->
            <div>
                <div class="sb-heading"><i class="bi bi-grid"></i> Kategori</div>
                <ul class="cat-list">
                    <?php
                    $cats = [
                        ''            => 'Semua',
                        'pengumuman'  => 'Pengumuman',
                        'event'       => 'Event',
                        'pnf'         => 'Pendidikan Non Formal',
                        'paud'        => 'PAUD',
                        'sarpras'     => 'Sarana & Prasarana',
                    ];
                    foreach ($cats as $key => $label):
                        $aktif = ($kategori === $key) ? 'aktif' : '';
                    ?>
                    <li>
                        <a href="berita.php?kategori=<?= urlencode($key) ?><?= !empty($cari) ? '&cari='.urlencode($cari) : '' ?>" class="<?= $aktif ?>">
                            <span class="cat-dot"></span> <?= $label ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            

            <!-- BERITA TERBARU -->
            <div>
                <div class="sb-heading"><i class="bi bi-clock-history"></i> Berita Terbaru</div>
                <div class="latest-list">
                    <?php
                    mysqli_data_seek($berita_terbaru, 0);
                    while ($lt = mysqli_fetch_assoc($berita_terbaru)):
                    ?>
                    <div class="latest-item">
                        <img src="<?= $lt['gambar'] ?>" class="latest-img" alt="">
                        <div class="latest-info">
                            <a href="detail-berita.php?id=<?= $lt['id'] ?>" style="text-decoration:none;">
                                <div class="latest-title"><?= htmlspecialchars($lt['judul']) ?></div>
                            </a>
                            <div class="latest-date"><i class="bi bi-calendar3" style="font-size:9px;"></i> <?= date('d M Y', strtotime($lt['tanggal'])) ?></div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- TAG CLOUD -->
            <div>
                <div class="sb-heading"><i class="bi bi-tags"></i> Tag Populer</div>
                <div class="tag-cloud">
                    <a href="berita.php?kategori=paud" class="tag-item">PAUD</a>
                    <a href="berita.php?kategori=pengumuman" class="tag-item">Pengumuman</a>
                    <a href="berita.php?kategori=event" class="tag-item">Event</a>
                    <a href="berita.php?kategori=pnf" class="tag-item">Non Formal</a>
                    <a href="berita.php?kategori=sarpras" class="tag-item">Sarpras</a>
                    <a href="berita.php" class="tag-item">Semua</a>
                </div>
            </div>

            <!-- HUBUNGI KAMI -->
            <div>
                <div class="sb-heading"><i class="bi bi-telephone"></i> Hubungi Kami</div>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <a href="mailto:disdik@lamongankab.go.id" style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#e3efff;border-radius:10px;text-decoration:none;">
                        <i class="bi bi-envelope-fill" style="color:#1565c0;font-size:14px;flex-shrink:0;"></i>
                        <span style="font-size:11px;color:#0d47a1;font-weight:500;word-break:break-all;">disdik@lamongankab.go.id</span>
                    </a>
                    <a href="tel:03223210221" style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#e3efff;border-radius:10px;text-decoration:none;">
                        <i class="bi bi-telephone-fill" style="color:#1565c0;font-size:14px;flex-shrink:0;"></i>
                        <span style="font-size:11px;color:#0d47a1;font-weight:500;">(0322) 321021</span>
                    </a>
                    <a href="https://wa.me/6281246676547" target="_blank" style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#e3efff;border-radius:10px;text-decoration:none;">
                        <i class="bi bi-whatsapp" style="color:#1565c0;font-size:14px;flex-shrink:0;"></i>
                        <span style="font-size:11px;color:#0d47a1;font-weight:500;">+62 812-4667-6547</span>
                    </a>
                    <a href="saran.php" style="display:flex;align-items:center;justify-content:center;gap:8px;margin-top:4px;padding:10px;background:#1565c0;border-radius:10px;text-decoration:none;">
                        <i class="bi bi-chat-dots-fill" style="color:#fff;font-size:13px;"></i>
                        <span style="font-size:12px;color:#fff;font-weight:600;">Kirim Saran & Masukan</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

        </div>
    </div>

</div>
</div>

<!-- FOOTER (TIDAK DIUBAH) -->
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
<script>AOS.init({ duration: 900, once: false });</script>
<?php include 'includes/accessibility.php'; ?>
</body>
</html>