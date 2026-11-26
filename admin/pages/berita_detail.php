<?php
session_start();
include '../../includes/functions.php';
include '../../includes/db.php';
cekAdmin();

$username = $_SESSION['username'] ?? 'Admin';
$initial  = strtoupper(substr($username, 0, 2));

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: berita.php');
    exit;
}

// Query berita berdasarkan ID
$query  = mysqli_query($conn, "SELECT * FROM berita WHERE id = $id LIMIT 1");
$berita = mysqli_fetch_assoc($query);

if (!$berita) {
    $_SESSION['notif_error'] = 'Berita tidak ditemukan.';
    header('Location: berita.php');
    exit;
}

// Berita sebelum dan sesudah untuk navigasi
$prevBerita = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, judul FROM berita WHERE id < $id ORDER BY id DESC LIMIT 1"));
$nextBerita = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, judul FROM berita WHERE id > $id ORDER BY id ASC  LIMIT 1"));

// Berita terkait — pakai kolom yang benar-benar ada (tidak ada kolom 'kategori')
// Ambil berita lain saja (bukan berita ini), tanpa filter kategori
$qTerkait      = mysqli_query($conn, "SELECT id, judul FROM berita WHERE id != $id ORDER BY id DESC LIMIT 4");
$beritaTerkait = [];
while ($r = mysqli_fetch_assoc($qTerkait)) $beritaTerkait[] = $r;

// Siapkan variabel gambar
$gambar1    = !empty($berita['gambar'])  ? trim($berita['gambar'])  : '';
$gambar2    = !empty($berita['gambar2']) ? trim($berita['gambar2']) : '';
$hasGambar1 = $gambar1 !== '';
$hasGambar2 = $gambar2 !== '';

// Resolve src: URL Supabase penuh atau path lokal
function resolveImgSrc($val) {
    if (empty($val)) return '';
    return (strpos($val, 'http://') === 0 || strpos($val, 'https://') === 0)
        ? $val
        : '../../uploads/berita/' . $val;
}
$src1 = resolveImgSrc($gambar1);
$src2 = resolveImgSrc($gambar2);

// Siapkan variabel isi
$isiKolom = $berita['isi'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($berita['judul']) ?> | Admin PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        :root {
            --blue:       #3b82f6;
            --blue-light: #eff6ff;
            --blue-dark:  #1d4ed8;
            --green:      #10b981;
            --amber:      #f59e0b;
            --red:        #ef4444;
            --slate-50:   #f8fafc;
            --slate-100:  #f1f5f9;
            --slate-200:  #e2e8f0;
            --slate-500:  #64748b;
            --slate-700:  #334155;
            --slate-900:  #0f172a;
            --radius:     16px;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ── Breadcrumb ── */
        .breadcrumb-wrap {
            background: #fff;
            border-bottom: 1px solid var(--slate-200);
            padding: 0.75rem 1.5rem;
        }
        .breadcrumb { margin: 0; font-size: 0.82rem; }
        .breadcrumb-item a { color: var(--blue); text-decoration: none; }
        .breadcrumb-item a:hover { text-decoration: underline; }
        .breadcrumb-item.active { color: var(--slate-500); }

        /* ── Hero banner ── */
        .article-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #0ea5e9 100%);
            padding: 2.5rem 2rem 2rem;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .article-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .article-hero .badge-status {
            background: rgba(255,255,255,0.2);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(8px);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .article-hero h1 {
            font-family: 'Lora', serif;
            font-size: clamp(1.4rem, 3vw, 2rem);
            font-weight: 600;
            line-height: 1.35;
            margin: 0.75rem 0;
        }
        .article-hero .meta-info {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            font-size: 0.82rem;
            opacity: 0.85;
        }
        .article-hero .meta-info span {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* ── Main layout ── */
        .content-wrap { background: var(--slate-50); min-height: calc(100vh - 120px); padding: 2rem 1.5rem; }

        /* ── Article card ── */
        .article-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: 0 2px 16px rgba(59,130,246,0.07);
            border: 1px solid var(--slate-200);
            overflow: hidden;
        }

        /* ── Gambar ── */
        .images-section {
            padding: 1.5rem 1.75rem 0;
        }
        /* 1 gambar: full width, tinggi tetap */
        .single-image-wrap {
            width: 100%;
            height: 380px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .single-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
            display: block;
        }
        .single-image-wrap:hover img { transform: scale(1.02); }

        /* 2 gambar: berdampingan, tinggi seragam */
        .dual-image-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 1.5rem;
        }
        .dual-image-wrap .img-box {
            height: 280px;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }
        .dual-image-wrap .img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }
        .dual-image-wrap .img-box:hover img { transform: scale(1.03); }
        .dual-image-wrap .img-label {
            position: absolute;
            bottom: 8px;
            left: 8px;
            background: rgba(0,0,0,0.55);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 6px;
            backdrop-filter: blur(4px);
        }

        @media (max-width: 576px) {
            .dual-image-wrap { grid-template-columns: 1fr; }
            .dual-image-wrap .img-box { height: 220px; }
            .single-image-wrap { height: 240px; }
        }

        /* ── Isi artikel ── */
        .article-body {
            padding: 1.75rem;
            font-family: 'Lora', serif;
            font-size: 1.05rem;
            line-height: 1.85;
            color: var(--slate-700);
        }
        .article-body p  { margin-bottom: 1.25rem; }
        .article-body h2,
        .article-body h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; color: var(--slate-900); margin: 1.75rem 0 0.75rem; }
        .article-body img { max-width: 100%; border-radius: 10px; margin: 1rem 0; }
        .article-body ul,
        .article-body ol  { padding-left: 1.5rem; margin-bottom: 1.25rem; }
        .article-body blockquote {
            border-left: 4px solid var(--blue);
            padding: 0.75rem 1.25rem;
            background: var(--blue-light);
            border-radius: 0 8px 8px 0;
            margin: 1.5rem 0;
            font-style: italic;
            color: var(--blue-dark);
        }

        /* ── Navigasi artikel ── */
        .nav-berita {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--slate-200);
            overflow: hidden;
        }
        .nav-berita a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            text-decoration: none;
            color: var(--slate-700);
            transition: background 0.15s;
            font-size: 0.875rem;
        }
        .nav-berita a:hover { background: var(--blue-light); color: var(--blue); }
        .nav-berita .divider { border-top: 1px solid var(--slate-200); }
        .nav-berita .nav-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--slate-500);
            margin-bottom: 2px;
        }
        .nav-berita .nav-title {
            font-weight: 600;
            font-size: 0.85rem;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ── Sidebar ── */
        .sidebar-section {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--slate-200);
            overflow: hidden;
            margin-bottom: 1.25rem;
        }
        .sidebar-section-header {
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid var(--slate-200);
            font-weight: 700;
            font-size: 0.88rem;
            color: var(--slate-900);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .related-item {
            display: flex;
            gap: 0.75rem;
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid var(--slate-100);
            text-decoration: none;
            color: var(--slate-700);
            transition: background 0.15s;
            align-items: flex-start;
        }
        .related-item:last-child { border-bottom: none; }
        .related-item:hover { background: var(--slate-50); color: var(--blue); }
        .related-item .ri-num {
            width: 28px; height: 28px;
            background: var(--blue-light);
            color: var(--blue);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
            flex-shrink: 0;
        }
        .related-item .ri-title {
            font-size: 0.83rem;
            font-weight: 600;
            line-height: 1.4;
            
            /* Mengaktifkan fitur clamp */
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2; /* Memotong teks di baris ke-2 */
            
            /* Standar baru (beberapa editor menyukainya) */
            line-clamp: 2; 
            
            overflow: hidden;
        }

        /* ── Action buttons ── */
        .action-bar {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            padding: 1.25rem 1.75rem;
            border-top: 1px solid var(--slate-200);
            background: var(--slate-50);
        }

        /* ── Print ── */
        @media print {
            .sidebar, .topbar, .breadcrumb-wrap,
            .action-bar, .nav-berita, .sidebar-section,
            footer { display: none !important; }
            .main-content { margin-left: 0 !important; }
            .article-hero { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        @media (max-width: 991px) {
            .article-body { padding: 1.25rem; }
            .action-bar   { padding: 1rem 1.25rem; }
            .images-section { padding: 1rem 1rem 0; }
        }
    </style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="d-flex align-items-center">
            <img src="../../img/logo-dispendik.png" class="logo" alt="Logo">
            <div class="brand-text d-none d-lg-block ms-3">
                PAUD Lamongan
                <small>Admin Dashboard</small>
            </div>
        </div>
    </div>
    <div class="sidebar-menu">
        <a href="dashboard.php" class="nav-link">
            <span><i class="bi bi-speedometer2"></i><span class="d-none d-lg-inline"> Dashboard</span></span>
        </a>
        <div class="dropdown ms-4">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#adminDropdown">
                <span><i class="bi bi-folder2-open"></i><span class="d-none d-lg-inline"> Konten</span></span>
            </a>
            <div class="collapse show ps-5 mt-2" id="adminDropdown">
                <a href="struktur.php" class="nav-link">
                    <span><i class="bi bi-people"></i><span class="d-none d-lg-inline"> Struktur</span></span>
                </a>
                <a href="seksi.php" class="nav-link">
                    <span><i class="bi bi-diagram-3"></i><span class="d-none d-lg-inline"> Seksi</span></span>
                </a>
                <a href="berita.php" class="nav-link active">
                    <span><i class="bi bi-newspaper"></i><span class="d-none d-lg-inline"> Berita</span></span>
                </a>
                <a href="materi.php" class="nav-link">
                    <span><i class="bi bi-journal-bookmark"></i><span class="d-none d-lg-inline"> Edukatif</span></span>
                </a>
            </div>
        </div>
        <a href="saran.php" class="nav-link">
            <span><i class="bi bi-chat-dots"></i><span class="d-none d-lg-inline"> Saran</span></span>
        </a>
        <div class="sidebar-footer mt-auto pt-4">
            <a href="../auth/logout.php" class="nav-link logout-link text-danger"
               onclick="return confirm('Yakin ingin logout?')">
                <span><i class="bi bi-box-arrow-right"></i><span class="d-none d-lg-inline"> Logout</span></span>
            </a>
        </div>
    </div>
</div>

<!-- ===== MAIN CONTENT ===== -->
<div class="main-content" id="mainContent">

    <!-- TOPBAR -->
    <div class="topbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="mobile-toggle btn btn-link text-dark p-0 me-3 d-lg-none fs-4" id="mobileToggle">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <h1 class="topbar-title mb-0">Detail Berita</h1>
                <small class="text-muted">Pratinjau konten berita</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar"><?= $initial ?></div>
                <div class="d-none d-md-block">
                    <div class="fw-semibold small"><?= htmlspecialchars($username) ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
            </div>
        </div>
    </div>

    <!-- BREADCRUMB -->
    <div class="breadcrumb-wrap">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php"><i class="bi bi-house-door me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="berita.php">Berita</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars(mb_substr($berita['judul'], 0, 40)) ?>...</li>
            </ol>
        </nav>
    </div>

    <!-- HERO BANNER -->
    <div class="article-hero">
        <div class="position-relative">
            <?php if (!empty($berita['status'])): ?>
            <span class="badge-status mb-2 d-inline-block">
                <i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>
                <?= htmlspecialchars(ucfirst($berita['status'])) ?>
            </span>
            <?php endif; ?>
            <h1><?= htmlspecialchars($berita['judul']) ?></h1>
            <div class="meta-info">
                <span><i class="bi bi-hash"></i> ID #<?= $berita['id'] ?></span>
                <?php if (!empty($berita['tanggal'])): ?>
                <span><i class="bi bi-calendar3"></i> <?= date('d F Y', strtotime($berita['tanggal'])) ?></span>
                <?php endif; ?>
                <?php if ($hasGambar1 || $hasGambar2): ?>
                <span><i class="bi bi-images"></i>
                    <?= ($hasGambar1 && $hasGambar2) ? '2 Foto' : '1 Foto' ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SESSION NOTIF -->
    <?php if (isset($_SESSION['notif'])): ?>
    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" id="notifBox">
        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['notif']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['notif']); endif; ?>

    <!-- CONTENT AREA -->
    <div class="content-wrap">
        <div class="row g-4">

            <!-- ===== KOLOM KIRI: ARTIKEL ===== -->
            <div class="col-lg-8">

                <div class="article-card">

                    <!-- ══ SECTION GAMBAR ══ -->
                    <?php if ($hasGambar1 || $hasGambar2): ?>
                    <div class="images-section">

                        <?php if ($hasGambar1 && $hasGambar2): ?>
                        <!-- DUA GAMBAR: berdampingan -->
                        <div class="dual-image-wrap">
                            <div class="img-box">
                                <img src="<?= htmlspecialchars($src1) ?>"
                                     alt="Foto 1 – <?= htmlspecialchars($berita['judul']) ?>"
                                     onerror="this.closest('.img-box').style.display='none'">
                                <span class="img-label"><i class="bi bi-image me-1"></i>Foto 1</span>
                            </div>
                            <div class="img-box">
                                <img src="<?= htmlspecialchars($src2) ?>"
                                     alt="Foto 2 – <?= htmlspecialchars($berita['judul']) ?>"
                                     onerror="this.closest('.img-box').style.display='none'">
                                <span class="img-label"><i class="bi bi-image me-1"></i>Foto 2</span>
                            </div>
                        </div>

                        <?php elseif ($hasGambar1): ?>
                        <!-- HANYA GAMBAR 1 -->
                        <div class="single-image-wrap">
                            <img src="<?= htmlspecialchars($src1) ?>"
                                 alt="<?= htmlspecialchars($berita['judul']) ?>"
                                 onerror="this.closest('.single-image-wrap').style.display='none'">
                        </div>

                        <?php else: ?>
                        <!-- HANYA GAMBAR 2 (edge case) -->
                        <div class="single-image-wrap">
                            <img src="<?= htmlspecialchars($src2) ?>"
                                 alt="<?= htmlspecialchars($berita['judul']) ?>"
                                 onerror="this.closest('.single-image-wrap').style.display='none'">
                        </div>
                        <?php endif; ?>

                    </div><!-- /images-section -->
                    <?php endif; ?>
                    <!-- ══ END SECTION GAMBAR ══ -->

                    <!-- Isi Artikel -->
                    <div class="article-body">
                        <?php if (!empty($isiKolom)): ?>
                            <?php if (strip_tags($isiKolom) === $isiKolom): ?>
                                <?= nl2br(htmlspecialchars($isiKolom)) ?>
                            <?php else: ?>
                                <?= $isiKolom ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted fst-italic">Isi berita tidak tersedia.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Action Bar -->
                    <div class="action-bar">
                        <a href="berita.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                        <a href="berita.php?edit=<?= $berita['id'] ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i> Edit Berita
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-printer me-1"></i> Cetak
                        </button>
                        <a href="../crud/berita_crud.php?hapus=<?= $berita['id'] ?>"
                           class="btn btn-outline-danger btn-sm"
                           onclick="return confirm('Yakin hapus berita ini?')">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </a>
                    </div>
                </div>

                <!-- Navigasi Prev / Next -->
                <?php if ($prevBerita || $nextBerita): ?>
                <div class="nav-berita mt-3">
                    <?php if ($prevBerita): ?>
                    <a href="berita_detail.php?id=<?= $prevBerita['id'] ?>">
                        <i class="bi bi-chevron-left" style="color:var(--blue);"></i>
                        <div>
                            <div class="nav-label">Berita Sebelumnya</div>
                            <div class="nav-title"><?= htmlspecialchars($prevBerita['judul']) ?></div>
                        </div>
                    </a>
                    <?php endif; ?>
                    <?php if ($prevBerita && $nextBerita): ?>
                    <div class="divider"></div>
                    <?php endif; ?>
                    <?php if ($nextBerita): ?>
                    <a href="berita_detail.php?id=<?= $nextBerita['id'] ?>" class="justify-content-end text-end">
                        <div>
                            <div class="nav-label text-end">Berita Selanjutnya</div>
                            <div class="nav-title"><?= htmlspecialchars($nextBerita['judul']) ?></div>
                        </div>
                        <i class="bi bi-chevron-right" style="color:var(--blue);"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div>

            <!-- ===== KOLOM KANAN: SIDEBAR ===== -->
            <div class="col-lg-4">

                <!-- Info Berita -->
                <div class="sidebar-section mb-3">
                    <div class="sidebar-section-header">
                        <i class="bi bi-info-circle text-primary"></i> Informasi Berita
                    </div>
                    <div class="p-3">
                        <table class="table table-sm table-borderless mb-0" style="font-size:0.83rem;">
                            <tr>
                                <td class="text-muted" style="width:40%;">ID</td>
                                <td class="fw-semibold">#<?= $berita['id'] ?></td>
                            </tr>
                            <?php if (!empty($berita['status'])): ?>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td>
                                    <span class="badge <?= $berita['status'] === 'publish' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ucfirst($berita['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($berita['tanggal'])): ?>
                            <tr>
                                <td class="text-muted">Tanggal</td>
                                <td class="fw-semibold"><?= date('d M Y', strtotime($berita['tanggal'])) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-muted">Foto</td>
                                <td class="fw-semibold">
                                    <?php
                                    if ($hasGambar1 && $hasGambar2) echo '<span class="text-success">2 foto</span>';
                                    elseif ($hasGambar1 || $hasGambar2) echo '<span class="text-primary">1 foto</span>';
                                    else echo '<span class="text-muted">Tidak ada</span>';
                                    ?>
                                </td>
                            </tr>
                            <?php if (!empty($berita['views'])): ?>
                            <tr>
                                <td class="text-muted">Views</td>
                                <td class="fw-semibold"><?= number_format($berita['views']) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <!-- Preview Thumbnail Gambar di Sidebar -->
                <?php if ($hasGambar1 || $hasGambar2): ?>
                <div class="sidebar-section mb-3">
                    <div class="sidebar-section-header">
                        <i class="bi bi-images text-primary"></i> Foto Berita
                    </div>
                    <div class="p-3 d-flex flex-column gap-2">
                        <?php if ($hasGambar1): ?>
                        <div>
                            <div class="text-muted mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;">Foto 1</div>
                            <img src="<?= htmlspecialchars($src1) ?>"
                                 alt="Foto 1"
                                 class="img-fluid rounded"
                                 style="width:100%;height:110px;object-fit:cover;"
                                 onerror="this.closest('div').style.display='none'">
                        </div>
                        <?php endif; ?>
                        <?php if ($hasGambar2): ?>
                        <div>
                            <div class="text-muted mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;">Foto 2</div>
                            <img src="<?= htmlspecialchars($src2) ?>"
                                 alt="Foto 2"
                                 class="img-fluid rounded"
                                 style="width:100%;height:110px;object-fit:cover;"
                                 onerror="this.closest('div').style.display='none'">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Berita Terkait -->
                <?php if (!empty($beritaTerkait)): ?>
                <div class="sidebar-section">
                    <div class="sidebar-section-header">
                        <i class="bi bi-newspaper text-primary"></i> Berita Lainnya
                    </div>
                    <?php foreach ($beritaTerkait as $i => $bt): ?>
                    <a href="berita_detail.php?id=<?= $bt['id'] ?>" class="related-item">
                        <div class="ri-num"><?= $i + 1 ?></div>
                        <div class="ri-title"><?= htmlspecialchars($bt['judul']) ?></div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Quick Actions -->
                <div class="sidebar-section mt-3">
                    <div class="sidebar-section-header">
                        <i class="bi bi-lightning text-warning"></i> Aksi Cepat
                    </div>
                    <div class="p-3 d-grid gap-2">
                        <a href="berita.php?edit=<?= $berita['id'] ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil-square me-1"></i> Edit Berita Ini
                        </a>
                        <a href="berita.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-list-ul me-1"></i> Semua Berita
                        </a>
                        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-speedometer2 me-1"></i> Ke Dashboard
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div><!-- /main-content -->

<!-- FOOTER -->
<footer style="
    background: #fff;
    border-top: 1px solid #e2e8f0;
    padding: 1rem 2rem;
    margin-left: 280px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #64748b;
">
    <div>© 2026 <strong class="text-dark">PAUD Lamongan</strong>. All rights reserved.</div>
    <div>Developed by <strong class="text-primary">Amanda</strong></div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Mobile sidebar toggle
document.getElementById('mobileToggle').addEventListener('click', function () {
    document.getElementById('sidebar').classList.toggle('mobile-open');
});

// Auto-dismiss notif
var notif = document.getElementById('notifBox');
if (notif) setTimeout(function () { notif.remove(); }, 4000);

</script>
</body>
</html>