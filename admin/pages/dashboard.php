<?php
session_start();
include '../../includes/functions.php';
include '../../includes/db.php';
cekAdmin();

$username = $_SESSION['username'] ?? 'Admin';
$initial  = strtoupper(substr($username, 0, 2));

$jml_berita = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM berita"))['total'];
$jml_materi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM materi"))['total'];
$jml_seksi  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM seksi"))['total'];
$jml_saran  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM saran"))['total'];

// Aktivitas terbaru dari semua tabel (maks 10)
$aktivitas = [];

$qBerita   = mysqli_query($conn, "SELECT id, judul, 'berita' as tipe FROM berita ORDER BY id DESC LIMIT 3");
while ($r  = mysqli_fetch_assoc($qBerita))   $aktivitas[] = $r;

$qMateri   = mysqli_query($conn, "SELECT id, judul, 'materi' as tipe FROM materi ORDER BY id DESC LIMIT 2");
while ($r  = mysqli_fetch_assoc($qMateri))   $aktivitas[] = $r;

$qSeksi    = mysqli_query($conn, "SELECT id, judul, 'seksi' as tipe FROM seksi ORDER BY created_at DESC LIMIT 2");
while ($r  = mysqli_fetch_assoc($qSeksi))    $aktivitas[] = $r;

$qStruktur = mysqli_query($conn, "SELECT id, nama as judul, 'struktur' as tipe FROM struktur ORDER BY id DESC LIMIT 1");
while ($r  = mysqli_fetch_assoc($qStruktur)) $aktivitas[] = $r;

$qSaran    = mysqli_query($conn, "SELECT id, CONCAT('Saran dari ', nama) as judul, 'saran' as tipe FROM saran ORDER BY created_at DESC LIMIT 2");
while ($r  = mysqli_fetch_assoc($qSaran))    $aktivitas[] = $r;

$aktivitas      = array_slice($aktivitas, 0, 10);
$totalAktivitas = count($aktivitas);
$adaNotifBaru   = $totalAktivitas > 0;

$beritaTerbaru = mysqli_query($conn, "SELECT judul, kategori, id FROM berita ORDER BY id DESC LIMIT 5");
$saranTerbaru  = mysqli_query($conn, "SELECT nama, isi, created_at FROM saran ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        /* ===== SIDEBAR BADGE ===== */
        .nav-link {
            justify-content: space-between;
        }
        .nav-link > span:first-child {
            display: flex;
            align-items: center;
        }
        .nav-link .sidebar-badge {
            font-size: 0.6rem;
            padding: 2px 6px;
            border-radius: 20px;
            flex-shrink: 0;
            margin-left: 6px;
            line-height: 1.4;
        }

        /* ===== STAT CARDS ===== */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 12px rgba(59,130,246,0.08);
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(59,130,246,0.15);
        }
        .stat-icon {
            width: 56px; height: 56px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            color: #1e293b;
        }
        .stat-label {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
            margin-top: 2px;
        }
        .stat-trend {
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 4px;
        }

        /* ===== SECTION CARDS ===== */
        .section-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(59,130,246,0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .section-card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .section-card-header h6 {
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
            padding: 0.875rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-item:hover { background: #f8fafc; }
        .activity-dot {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* ===== CHART CARD ===== */
        .chart-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 12px rgba(59,130,246,0.08);
            border: 1px solid #e2e8f0;
            height: 100%;
        }

        /* ===== SHORTCUT BUTTONS ===== */
        .shortcut-btn {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            text-decoration: none;
            color: #1e293b;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .shortcut-btn:hover {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(59,130,246,0.25);
        }
        .shortcut-btn i { font-size: 1.5rem; }
    </style>
</head>
<body>

<!-- ================= SIDEBAR ================= -->
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

        <!-- Dashboard + badge notif -->
        <a href="dashboard.php" class="nav-link active">
            <span>
                <i class="bi bi-speedometer2"></i>
                <span class="d-none d-lg-inline"> Dashboard</span>
            </span>
            <?php if ($adaNotifBaru): ?>
            <span class="badge bg-danger sidebar-badge d-none d-lg-inline">
                <?= $totalAktivitas ?>
            </span>
            <?php endif; ?>
        </a>

        <!-- Konten dropdown -->
        <div class="dropdown ms-4">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#adminDropdown">
                <span>
                    <i class="bi bi-folder2-open"></i>
                    <span class="d-none d-lg-inline"> Konten</span>
                </span>
            </a>
            <div class="collapse show ps-5 mt-2" id="adminDropdown">
                <a href="struktur.php" class="nav-link">
                    <span><i class="bi bi-people"></i><span class="d-none d-lg-inline"> Struktur</span></span>
                </a>
                <a href="seksi.php" class="nav-link">
                    <span><i class="bi bi-diagram-3"></i><span class="d-none d-lg-inline"> Seksi</span></span>
                </a>
                <a href="berita.php" class="nav-link">
                    <span><i class="bi bi-newspaper"></i><span class="d-none d-lg-inline"> Berita</span></span>
                </a>
                <a href="materi.php" class="nav-link">
                    <span><i class="bi bi-journal-bookmark"></i><span class="d-none d-lg-inline"> Edukatif</span></span>
                </a>
            </div>
        </div>

        <!-- Saran + badge jumlah -->
        <a href="saran.php" class="nav-link">
            <span>
                <i class="bi bi-chat-dots"></i>
                <span class="d-none d-lg-inline"> Saran</span>
            </span>
            <?php if ($jml_saran > 0): ?>
            <span class="badge bg-danger sidebar-badge d-none d-lg-inline">
                <?= $jml_saran ?>
            </span>
            <?php endif; ?>
        </a>

        <div class="sidebar-footer mt-auto pt-4">
            <a href="../auth/logout.php" class="nav-link logout-link text-danger"
               onclick="return confirm('Yakin ingin logout?')">
                <span>
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-lg-inline"> Logout</span>
                </span>
            </a>
        </div>

    </div>
</div>

<!-- ================= MAIN CONTENT ================= -->
<div class="main-content" id="mainContent">

    <!-- TOPBAR -->
    <div class="topbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="mobile-toggle btn btn-link text-dark p-0 me-3 d-lg-none fs-4" id="mobileToggle">
                <i class="bi bi-list"></i>
            </button>
            <div>
    <h4 class="mb-0 fw-bold text-primary">
        Dashboard
    </h4>
    <p class="mb-0 text-muted" style="font-size:0.85rem;">
        Selamat datang, <strong><?= htmlspecialchars($username) ?></strong> 
    </p>
</div>
        </div>

        <div class="d-flex align-items-center gap-3">

            <!-- Bell Notifikasi -->
            <div class="dropdown">
                <a href="#" class="btn btn-light position-relative rounded-circle p-2" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>
                    <?php if ($adaNotifBaru): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="font-size:0.6rem;"><?= $totalAktivitas ?></span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow p-0"
                    style="width:320px; max-height:420px; overflow-y:auto;">

                    <li class="px-3 py-2 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold small">Aktivitas Terbaru</span>
                        <span class="badge bg-primary rounded-pill" style="font-size:0.65rem;">
                            <?= $totalAktivitas ?>/10
                        </span>
                    </li>

                    <?php foreach ($aktivitas as $akt):
                        if ($akt['tipe'] == 'berita') {
                            $icon  = 'bi-newspaper';
                            $color = 'text-primary';
                            $bg    = 'bg-primary bg-opacity-10';
                            $label = 'Berita';
                            $link  = 'berita.php';
                        } elseif ($akt['tipe'] == 'materi') {
                            $icon  = 'bi-journal-bookmark';
                            $color = 'text-success';
                            $bg    = 'bg-success bg-opacity-10';
                            $label = 'Materi';
                            $link  = 'materi.php';
                        } elseif ($akt['tipe'] == 'seksi') {
                            $icon  = 'bi-folder2-open';
                            $color = 'text-warning';
                            $bg    = 'bg-warning bg-opacity-10';
                            $label = 'Dokumen';
                            $link  = 'seksi.php';
                        } elseif ($akt['tipe'] == 'struktur') {
                            $icon  = 'bi-people';
                            $color = 'text-info';
                            $bg    = 'bg-info bg-opacity-10';
                            $label = 'Struktur';
                            $link  = 'struktur.php';
                        } else {
                            $icon  = 'bi-chat-dots';
                            $color = 'text-danger';
                            $bg    = 'bg-danger bg-opacity-10';
                            $label = 'Saran';
                            $link  = 'saran.php';
                        }
                        $judulPendek = mb_substr($akt['judul'], 0, 35) . (mb_strlen($akt['judul']) > 35 ? '...' : '');
                    ?>
                    <li>
                        <a class="dropdown-item py-2 px-3" href="<?= $link ?>">
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center justify-content-center <?= $bg ?>"
                                     style="width:32px; height:32px; border-radius:8px; flex-shrink:0;">
                                    <i class="bi <?= $icon ?> <?= $color ?>"></i>
                                </div>
                                <div style="min-width:0;">
                                    <div class="small fw-semibold text-dark text-truncate">
                                        <?= htmlspecialchars($judulPendek) ?>
                                    </div>
                                    <span class="badge <?= $bg ?> <?= $color ?>" style="font-size:0.65rem;">
                                        <?= $label ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </li>
                    <?php endforeach; ?>

                    <li class="border-top">
                        <a class="dropdown-item text-center text-primary small fw-semibold py-2" href="dashboard.php">
                            <i class="bi bi-clock-history me-1"></i> Lihat Riwayat Lengkap
                        </a>
                    </li>

                </ul>
            </div>

            <!-- Avatar -->
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar"><?= $initial ?></div>
                <div class="d-none d-md-block">
                    <div class="fw-semibold small"><?= htmlspecialchars($username) ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
            </div>

        </div>
    </div>

    <!-- SESSION NOTIF -->
    <?php if (isset($_SESSION['notif'])): ?>
    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" id="notifBox">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= htmlspecialchars($_SESSION['notif']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['notif']); ?>
    <?php endif; ?>

    <div class="container-fluid py-4">

        <!-- ===== STAT CARDS ===== -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon bg-primary bg-opacity-10">
                        <i class="bi bi-newspaper text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?= number_format($jml_berita) ?></div>
                        <div class="stat-label">Total Berita</div>
                        <a href="berita.php" class="stat-trend text-primary text-decoration-none">
                            Kelola <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon bg-success bg-opacity-10">
                        <i class="bi bi-journal-bookmark text-success"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?= number_format($jml_materi) ?></div>
                        <div class="stat-label">Materi Edukatif</div>
                        <a href="materi.php" class="stat-trend text-success text-decoration-none">
                            Kelola <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon bg-warning bg-opacity-10">
                        <i class="bi bi-folder2-open text-warning"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?= number_format($jml_seksi) ?></div>
                        <div class="stat-label">Dokumen Seksi</div>
                        <a href="seksi.php" class="stat-trend text-warning text-decoration-none">
                            Kelola <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon bg-danger bg-opacity-10">
                        <i class="bi bi-chat-dots text-danger"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?= number_format($jml_saran) ?></div>
                        <div class="stat-label">Saran Masuk</div>
                        <a href="saran.php" class="stat-trend text-danger text-decoration-none">
                            Lihat <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== CHART + SHORTCUTS ===== -->
        <div class="row g-3 mb-4">
            <div class="col-lg-5">
                <div class="chart-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Ringkasan Konten</h6>
                        <span class="badge bg-primary">Overview</span>
                    </div>
                    <div style="height:240px;">
                        <canvas id="donutChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="chart-card">
                    <h6 class="fw-bold mb-3">Akses Cepat</h6>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <a href="berita.php" class="shortcut-btn">
                                <i class="bi bi-newspaper text-primary"></i> Berita
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="materi.php" class="shortcut-btn">
                                <i class="bi bi-journal-bookmark text-success"></i> Edukatif
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="seksi.php" class="shortcut-btn">
                                <i class="bi bi-folder2-open text-warning"></i> Seksi
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="struktur.php" class="shortcut-btn">
                                <i class="bi bi-people text-info"></i> Struktur
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="saran.php" class="shortcut-btn">
                                <i class="bi bi-chat-dots text-danger"></i> Saran
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="berita.php" class="shortcut-btn">
                                <i class="bi bi-plus-circle text-primary"></i> Tambah Berita
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="seksi.php" class="shortcut-btn">
                                <i class="bi bi-upload text-warning"></i> Upload Dokumen
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="struktur.php" class="shortcut-btn">
                                <i class="bi bi-person-plus text-info"></i> Tambah Staf
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== BERITA TERBARU + SARAN TERBARU ===== -->
        <div class="row g-3">

            <div class="col-lg-6">
                <div class="section-card">
                    <div class="section-card-header">
                        <h6><i class="bi bi-newspaper text-primary me-2"></i>Berita Terbaru</h6>
                        <a href="berita.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <?php while ($b = mysqli_fetch_assoc($beritaTerbaru)): ?>
                    <div class="activity-item">
                        <div class="activity-dot bg-primary bg-opacity-10">
                            <i class="bi bi-newspaper text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small text-dark">
                                <?= htmlspecialchars(mb_substr($b['judul'], 0, 50)) ?>...
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:0.7rem;">
                                <?= htmlspecialchars($b['kategori']) ?>
                            </span>
                        </div>
                        <a href="berita_detail.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-light">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="section-card">
                    <div class="section-card-header">
                        <h6><i class="bi bi-chat-dots text-danger me-2"></i>Saran Terbaru</h6>
                        <a href="saran.php" class="btn btn-sm btn-outline-danger">Lihat Semua</a>
                    </div>
                    <?php while ($s = mysqli_fetch_assoc($saranTerbaru)): ?>
                    <div class="activity-item">
                        <div class="activity-dot bg-success bg-opacity-10">
                            <span class="fw-bold text-success" style="font-size:0.75rem;">
                                <?= strtoupper(substr($s['nama'], 0, 2)) ?>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small text-dark"><?= htmlspecialchars($s['nama']) ?></div>
                            <div class="text-muted small"
                                 style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;max-width:250px;">
                                <?= htmlspecialchars(mb_substr($s['isi'], 0, 60)) ?>...
                            </div>
                        </div>
                        <small class="text-muted text-nowrap"><?= date('d M', strtotime($s['created_at'])) ?></small>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

        </div>

    </div>
</div>
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
    <div>
        © 2026 <strong class="text-dark">PAUD Lamongan</strong>. All rights reserved.
    </div>
    <div>
        Dinas Pendidikan Kabupaten Lamongan
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById('mobileToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('mobile-open');
});

var notif = document.getElementById('notifBox');
if (notif) {
    setTimeout(function() { notif.remove(); }, 4000);
}

var ctx = document.getElementById('donutChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Berita', 'Materi', 'Dokumen', 'Saran'],
        datasets: [{
            data: [<?= $jml_berita ?>, <?= $jml_materi ?>, <?= $jml_seksi ?>, <?= $jml_saran ?>],
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 16,
                    usePointStyle: true,
                    font: { size: 12, weight: '600' }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        return ' ' + ctx.label + ': ' + ctx.raw;
                    }
                }
            }
        }
    }
});
</script>
</body>
</html>