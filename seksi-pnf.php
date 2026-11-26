<?php
session_start();
include 'includes/db.php';

if(!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    mysqli_query($conn, "UPDATE counter SET jumlah = jumlah + 1 WHERE id = 1");
}

$result = mysqli_query($conn, "SELECT jumlah FROM counter WHERE id = 1");
$visitor_count = mysqli_fetch_assoc($result)['jumlah'] ?? 0;

// DEBUG QUERY
$dokumen_query = "SELECT * FROM seksi WHERE kategori = 'pnf' ORDER BY created_at DESC";
$dokumen_result = mysqli_query($conn, $dokumen_query);

if(!$dokumen_result){
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Seksi PNF | PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/website-paud/css/beranda.css?v=2">
    <link rel="stylesheet" href="/website-paud/css/seksi-pnf.css?v=2">
    <link href="https://cdn.jsdelivr.net/npm/aos@3.0.0-beta.6/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ── HANYA UNTUK SECTION LAYANAN & DOKUMEN ── */

    /* Header seksi */
    .seksi-header {
        background: linear-gradient(135deg, #0d47a1 0%, #0077b6 50%, #00b4d8 100%);
        padding: 64px 0 56px;
        position: relative;
        overflow: hidden;
    }
    .seksi-header::before {
        content: '';
        position: absolute;
        width: 320px; height: 320px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        top: -80px; right: -60px;
    }
    .seksi-header::after {
        content: '';
        position: absolute;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        bottom: -60px; left: 10%;
    }
    .seksi-header h2 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: clamp(1.6rem, 4vw, 2.4rem);
        font-weight: 700;
        color: #fff;
        margin-bottom: 10px;
    }
    .seksi-header p {
        color: rgba(255,255,255,0.78);
        font-size: 15px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .seksi-header .header-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.22);
        color: #fff;
        font-size: 11.5px; font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        padding: 5px 14px; border-radius: 999px;
        margin-bottom: 16px;
    }

    /* Search */
    .search-wrap-custom {
        background: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.07);
        padding: 28px 0;
    }
    .search-inner { position: relative; }
    .search-inner i {
        position: absolute; left: 15px; top: 50%;
        transform: translateY(-50%);
        color: #888; font-size: 16px;
    }
    #search {
        padding: 12px 16px 12px 44px;
        border: 1.5px solid rgba(0,0,0,0.1);
        border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px;
        width: 100%;
        background: #f5f9fc;
        transition: border 0.2s, box-shadow 0.2s;
        outline: none;
    }
    #search:focus {
        border-color: #0077b6;
        box-shadow: 0 0 0 3px rgba(0,119,182,0.12);
    }
    #result { padding-bottom: 1.5rem; }

    /* Section label */
    .sec-eyebrow {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 11.5px; font-weight: 700;
        letter-spacing: 0.14em; text-transform: uppercase;
        color: #0077b6; margin-bottom: 6px;
    }
    .sec-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: clamp(1.35rem, 2.5vw, 1.85rem);
        font-weight: 700; color: #1a1a1a;
        margin-bottom: 0;
    }
    .sec-line {
        width: 36px; height: 3px;
        background: #00b4d8; border-radius: 2px;
        margin: 12px 0 32px;
    }

    /* Layanan section */
    .layanan-section {
        padding: 60px 0 52px;
        background: #f0f7fb;
    }
    .layanan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(225px, 1fr));
        gap: 18px;
    }
    .lcard {
        background: #fff;
        border-radius: 16px;
        padding: 26px 22px 24px;
        border: 1px solid rgba(0,0,0,0.06);
        transition: transform 0.25s cubic-bezier(.22,.68,0,1.2), box-shadow 0.25s;
        position: relative; overflow: hidden;
    }
    .lcard::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0; height: 3px;
        border-radius: 0 0 16px 16px;
        opacity: 0; transition: opacity 0.2s;
    }
    .lcard:hover { transform: translateY(-6px); box-shadow: 0 18px 40px rgba(0,119,182,0.13); }
    .lcard:hover::after { opacity: 1; }
    .lcard.c1::after { background: #0077b6; }
    .lcard.c2::after { background: #00b4d8; }
    .lcard.c3::after { background: #0d47a1; }
    .lcard.c4::after { background: #48cae4; }

    .lcard-icon {
        width: 50px; height: 50px; border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; margin-bottom: 18px;
    }
    .ic1 { background: #e0f3fb; color: #0077b6; }
    .ic2 { background: #caf0f8; color: #0096c7; }
    .ic3 { background: #dce8ff; color: #0d47a1; }
    .ic4 { background: #d0f4f4; color: #007b83; }

    .lcard-num {
        font-size: 10.5px; font-weight: 700;
        letter-spacing: 0.1em; color: #aaa;
        margin-bottom: 5px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .lcard-name {
        font-size: 15px; font-weight: 600; color: #1a1a1a;
        font-family: 'Plus Jakarta Sans', sans-serif;
        line-height: 1.3; margin-bottom: 6px;
    }
    .lcard-desc {
        font-size: 13px; color: #6b7280; line-height: 1.65;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Dokumen section */
    .dokumen-section {
        padding: 56px 0 64px;
        background: #fff;
    }

    /* Filter pill */
    .filter-bar {
        display: flex; align-items: center; gap: 8px;
        flex-wrap: wrap; margin-bottom: 20px;
    }
    .filter-btn {
        padding: 6px 16px; border-radius: 999px;
        font-size: 12.5px; font-weight: 600;
        border: 1.5px solid rgba(0,0,0,0.1);
        background: #fff; color: #6b7280;
        cursor: pointer; transition: all 0.15s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .filter-btn:hover, .filter-btn.active {
        background: #0077b6; color: #fff; border-color: #0077b6;
    }

    /* Table */
    .dok-table-wrap {
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.07);
        overflow: hidden;
    }
    .dok-table {
        width: 100%; border-collapse: collapse;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .dok-table thead tr { background: #f0f7fb; }
    .dok-table thead th {
        padding: 13px 18px;
        font-size: 11.5px; font-weight: 700;
        letter-spacing: 0.07em; text-transform: uppercase;
        color: #0077b6;
        border-bottom: 1px solid rgba(0,0,0,0.07);
        white-space: nowrap;
    }
    .dok-table tbody tr {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        transition: background 0.15s;
    }
    .dok-table tbody tr:last-child { border-bottom: none; }
    .dok-table tbody tr:hover { background: #f5f9fc; }
    .dok-table tbody td {
        padding: 13px 18px;
        font-size: 13.5px; color: #374151;
        vertical-align: middle;
    }
    .dok-name {
        font-weight: 600; color: #1a1a1a;
        display: flex; align-items: center; gap: 10px;
    }
    .dok-type-badge {
        display: inline-flex; align-items: center;
        font-size: 11px; font-weight: 700;
        padding: 4px 10px; border-radius: 6px;
    }
    .badge-pdf  { background: #FCEBEB; color: #A32D2D; }
    .badge-docx { background: #E6F1FB; color: #185FA5; }
    .badge-xlsx { background: #EAF3DE; color: #3B6D11; }
    .badge-pptx { background: #FAEEDA; color: #854F0B; }
    .badge-lain { background: #F1EFE8; color: #5F5E5A; }

    .btn-unduh {
        display: inline-flex; align-items: center; gap: 6px;
        background: #0077b6; color: #fff;
        padding: 7px 15px; border-radius: 8px;
        font-size: 12.5px; font-weight: 600;
        text-decoration: none;
        transition: background 0.18s, transform 0.15s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .btn-unduh:hover { background: #0d47a1; transform: translateY(-1px); color: #fff; }

    .dok-empty {
        text-align: center; padding: 52px 24px;
        color: #9ca3af;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .dok-empty i { font-size: 38px; display: block; margin-bottom: 12px; color: #d1d5db; }
    .dok-empty p { font-size: 14px; }
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
                    <li><a class="dropdown-item active" href="seksi-pnf.php">Seksi PNF</a></li>
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

<!-- HEADER SEKSI -->
<section class="seksi-header text-center" data-aos="fade-up">
    <div class="container">
        <div class="header-badge">
            <i class="bi bi-patch-check-fill"></i> Bidang PAUD &amp; PNF/Kesetaraan
        </div>
        <h2 class="fw-bold">Seksi Pendidikan Non Formal (PNF)</h2>
        <p>
            Bidang PAUD dan Pendidikan Non Formal/Kesetaraan<br>
            Dinas Pendidikan Kabupaten Lamongan
        </p>
    </div>
</section>

<!-- SEARCH -->
<div class="search-wrap-custom" data-aos="fade-up">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="search-inner">
                    <i class="bi bi-search"></i>
                    <input type="text" id="search" data-kategori="pnf"
                           placeholder="Cari dokumen PNF...">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div id="result" class="mt-4"></div>
</div>

<!-- LAYANAN SECTION -->
<section class="layanan-section" data-aos="fade-up">
    <div class="container">
        <p class="sec-eyebrow">Tugas &amp; Fungsi</p>
        <h2 class="sec-title">Ruang Lingkup Layanan</h2>
        <div class="sec-line"></div>

        <div class="layanan-grid">
            <div class="lcard c1" data-aos="fade-up" data-aos-delay="0">
                <div class="lcard-icon ic1"><i class="bi bi-journal-bookmark"></i></div>
                <div class="lcard-num">01</div>
                <div class="lcard-name">Pendidikan Kesetaraan</div>
                <div class="lcard-desc">Pengelolaan program Paket A, B, dan C bagi warga belajar di Kabupaten Lamongan.</div>
            </div>
            <div class="lcard c2" data-aos="fade-up" data-aos-delay="80">
                <div class="lcard-icon ic2"><i class="bi bi-pencil-square"></i></div>
                <div class="lcard-num">02</div>
                <div class="lcard-name">Pendidikan Keaksaraan</div>
                <div class="lcard-desc">Program pemberantasan buta huruf dan peningkatan keaksaraan fungsional masyarakat.</div>
            </div>
            <div class="lcard c3" data-aos="fade-up" data-aos-delay="160">
                <div class="lcard-icon ic3"><i class="bi bi-tools"></i></div>
                <div class="lcard-num">03</div>
                <div class="lcard-name">Kursus dan Pelatihan</div>
                <div class="lcard-desc">Pembinaan lembaga kursus dan pelatihan keterampilan vokasional masyarakat.</div>
            </div>
            <div class="lcard c4" data-aos="fade-up" data-aos-delay="240">
                <div class="lcard-icon ic4"><i class="bi bi-clipboard2-data"></i></div>
                <div class="lcard-num">04</div>
                <div class="lcard-name">Monitoring Lembaga PNF</div>
                <div class="lcard-desc">Pembinaan dan pengawasan berkala terhadap seluruh lembaga PNF di lapangan.</div>
            </div>
        </div>
    </div>
</section>

<!-- DOKUMEN SECTION -->
<section class="dokumen-section" data-aos="fade-up">
    <div class="container">
        <p class="sec-eyebrow">Unduhan</p>
        <h2 class="sec-title">Dokumen PNF</h2>
        <div class="sec-line"></div>

        <div class="filter-bar" id="filterBar">
            <button class="filter-btn active" data-filter="semua">Semua</button>
            <button class="filter-btn" data-filter="pdf">PDF</button>
            <button class="filter-btn" data-filter="docx">Word</button>
            <button class="filter-btn" data-filter="xlsx">Excel</button>
            <button class="filter-btn" data-filter="pptx">PowerPoint</button>
        </div>

        <div class="dok-table-wrap">
            <table class="dok-table" id="dokTable">
                <thead>
                    <tr>
                        <th style="width:42px">#</th>
                        <th>Nama Dokumen</th>
                        <th>Tipe</th>
                        <th>Tanggal</th>
                        <th style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                if ($dokumen_result && mysqli_num_rows($dokumen_result) > 0):
                    while ($dok = mysqli_fetch_assoc($dokumen_result)):
                        $tipe = strtolower(trim($dok['tipe_file'] ?? ''));
                        $ext  = $tipe ?: strtolower(pathinfo($dok['file'], PATHINFO_EXTENSION));

                        $badge_class = match(true) {
                            $ext === 'pdf'                 => 'badge-pdf',
                            in_array($ext,['doc','docx'])  => 'badge-docx',
                            in_array($ext,['xls','xlsx'])  => 'badge-xlsx',
                            in_array($ext,['ppt','pptx'])  => 'badge-pptx',
                            default                        => 'badge-lain',
                        };
                        $icon = match(true) {
                            $ext === 'pdf'                 => 'bi-file-earmark-pdf-fill',
                            in_array($ext,['doc','docx'])  => 'bi-file-earmark-word-fill',
                            in_array($ext,['xls','xlsx'])  => 'bi-file-earmark-excel-fill',
                            in_array($ext,['ppt','pptx'])  => 'bi-file-earmark-ppt-fill',
                            default                        => 'bi-file-earmark-fill',
                        };

                        $ext_label = strtoupper($ext ?: 'FILE');
                        $judul     = htmlspecialchars($dok['judul'] ?? 'Dokumen');
                        $deskripsi = htmlspecialchars($dok['deskripsi'] ?? '');
                        $tgl       = !empty($dok['created_at']) ? date('d M Y', strtotime($dok['created_at'])) : '-';
                        $file_url  = htmlspecialchars($dok['file']);
                        $ukuran = '';
                        if (!empty($dok['ukuran'])) {
                            $kb = $dok['ukuran'] / 1024;
                            $ukuran = $kb >= 1024
                                ? number_format($kb / 1024, 1) . ' MB'
                                : number_format($kb, 0) . ' KB';
                        }
                ?>
                    <tr data-type="<?= htmlspecialchars($ext) ?>">
                        <td style="color:#bbb;font-size:13px;font-weight:500"><?= $no++ ?></td>
                        <td>
                            <div class="dok-name">
                                <i class="bi <?= $icon ?>" style="font-size:18px;color:#1a9e5c;flex-shrink:0"></i>
                                <div>
                                    <?= $judul ?>
                                    <?php if ($deskripsi): ?>
                                        <div style="font-size:12px;color:#9ca3af;font-weight:400;margin-top:2px"><?= $deskripsi ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="dok-type-badge <?= $badge_class ?>"><?= $ext_label ?></span>
                        </td>
                        <td style="color:#9ca3af;font-size:13px;white-space:nowrap">
                            <?= $tgl ?>
                            <?php if ($ukuran): ?>
                                <div style="font-size:11.5px;margin-top:2px"><?= $ukuran ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= $file_url ?>" target="_blank" class="btn-unduh">
                                <i class="bi bi-download"></i> Unduh
                            </a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="dok-empty">
                                <i class="bi bi-folder2-open"></i>
                                <p>Belum ada dokumen PNF yang tersedia.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
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

    // Filter tipe dokumen
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('#dokTable tbody tr[data-type]').forEach(row => {
                row.style.display = (filter === 'semua' || row.dataset.type === filter) ? '' : 'none';
            });
        });
    });
</script>

<?php include 'includes/accessibility.php'; ?>
</body>
</html>