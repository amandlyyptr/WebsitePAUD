<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    $update = mysqli_query($conn, "UPDATE counter SET jumlah = jumlah + 1 WHERE id = 1");
    if (!$update) die("Update counter error: " . mysqli_error($conn));
}

$result = mysqli_query($conn, "SELECT jumlah FROM counter WHERE id = 1");
if (!$result) die("Query counter error: " . mysqli_error($conn));
$data = mysqli_fetch_assoc($result);
$visitor_count = $data['jumlah'] ?? 0;

$inti = mysqli_query($conn, "SELECT * FROM struktur WHERE kategori='inti' ORDER BY id ASC");
if (!$inti) die("Query inti error: " . mysqli_error($conn));

$lainnya = mysqli_query($conn, "SELECT * FROM struktur WHERE kategori='lainnya' ORDER BY id ASC");
if (!$lainnya) die("Query lainnya error: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struktur Organisasi | PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/website-paud/css/struktur.css?v=7">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* { box-sizing: border-box; }
body { font-family: 'Poppins', sans-serif; background: #f4f6fb; color: #333; }

/* ── HERO JUDUL ── */
.judul-section {
  background: linear-gradient(135deg, #1565C0 0%, #1976D2 60%, #42A5F5 100%);
  padding: 3rem 0 2.5rem;
  color: #fff;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.judul-section::before {
  content: '';
  position: absolute;
  width: 300px; height: 300px;
  border-radius: 50%;
  background: rgba(255,255,255,0.05);
  top: -80px; right: -60px;
}
.judul-section::after {
  content: '';
  position: absolute;
  width: 200px; height: 200px;
  border-radius: 50%;
  background: rgba(255,255,255,0.05);
  bottom: -60px; left: -40px;
}
.judul-section h2 { font-size: 1.9rem; font-weight: 700; margin-bottom: 0.4rem; position: relative; z-index: 1; }
.judul-section p { font-size: 0.92rem; opacity: 0.88; position: relative; z-index: 1; }
.judul-breadcrumb {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(255,255,255,0.15);
  border-radius: 20px;
  padding: 4px 14px;
  font-size: 12px;
  margin-bottom: 1rem;
  color: rgba(255,255,255,0.9);
  position: relative; z-index: 1;
}

/* ── SECTION LABEL ── */
.section-label {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 1rem;
  font-weight: 600;
  color: #1565C0;
  margin-bottom: 1.8rem;
}
.section-label::after {
  content: '';
  flex: 1;
  height: 1.5px;
  background: linear-gradient(90deg, #dce8ff, transparent);
}

/* ── PENGURUS INTI ── */
.inti-grid {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}
.inti-row-top   { display: flex; justify-content: center; }
.inti-row-bawah { display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; }

.kartu-pimpinan {
    background: #fff;
    border: 0.5px solid #dce8ff;
    border-left: 4px solid #1565C0;
    border-radius: 0 20px 20px 0;
    padding: 1.4rem 1.8rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    width: 380px;
    transition: transform .2s, box-shadow .2s;
}
.kartu-pimpinan:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(21,101,192,.1);
}
.kartu-pimpinan .pim-foto {
    width: 84px; height: 84px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #dce8ff;
    flex-shrink: 0;
}
.kartu-pimpinan .pim-info { flex: 1; }
.kartu-pimpinan .pim-nama {
    font-size: 1rem; font-weight: 700;
    color: #1a1a2e; line-height: 1.3; margin-bottom: .2rem;
}
.kartu-pimpinan .pim-jabatan {
    font-size: .78rem; color: #7b8fa6; line-height: 1.4;
}

.kartu-anggota {
    background: #fff;
    border: 0.5px solid #dce8ff;
    border-radius: 16px;
    padding: 1.2rem 1rem 1rem;
    text-align: center;
    width: 152px;
    transition: transform .2s, box-shadow .2s;
}
.kartu-anggota:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(21,101,192,.1);
}
.kartu-anggota .angg-foto {
    width: 64px; height: 64px;
    border-radius: 50%;
    object-fit: cover;
    border: 2.5px solid #dce8ff;
    display: block;
    margin: 0 auto .7rem;
}
.kartu-anggota .angg-nama    { font-size: .75rem; font-weight: 600; color: #1a1a2e; line-height: 1.3; margin-bottom: .2rem; }
.kartu-anggota .angg-jabatan { font-size: .69rem; color: #7b8fa6; line-height: 1.3; }

/* ── SEARCH BOX ── */
.search-wrap {
  position: relative;
  max-width: 280px;
}
.search-wrap i {
  position: absolute;
  left: 12px; top: 50%;
  transform: translateY(-50%);
  color: #aaa; font-size: 13px;
}
.search-wrap input {
  width: 100%;
  padding: 7px 12px 7px 34px;
  border: 1px solid #dce8ff;
  border-radius: 8px;
  font-size: 13px;
  font-family: 'Poppins', sans-serif;
  outline: none;
  background: #fafbff;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.search-wrap input:focus {
  border-color: #1565C0;
  box-shadow: 0 0 0 3px rgba(21,101,192,0.08);
}

/* ── TABEL SIMPLE ── */
.tabel-anggota {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}
.tabel-anggota thead th {
  padding: 10px 14px;
  font-weight: 600;
  color: #555;
  border-bottom: 2px solid #e8eef8;
  text-align: left;
  font-size: 12.5px;
  background: #f8f9fc;
}
.tabel-anggota tbody tr {
  border-bottom: 1px solid #f0f2f8;
  transition: background 0.12s;
}
.tabel-anggota tbody tr:hover { background: #f5f7ff; }
.tabel-anggota td {
  padding: 10px 14px;
  vertical-align: middle;
  color: #444;
}
.tabel-anggota .td-foto img {
  width: 40px; height: 40px;
  object-fit: cover;
  border-radius: 50%;
  border: 2px solid #e8eef8;
}

/* ── KARTU WRAPPER ── */
.card-section {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 2px 14px rgba(0,0,0,0.055);
  padding: 2rem 2rem 1.5rem;
  margin-bottom: 1.5rem;
}
</style>
</head>
<body>

<!-- NAVBAR — tidak diubah -->
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
                    <li><a class="dropdown-item active" href="struktur.php">Struktur Organisasi</a></li>
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

<!-- HERO JUDUL -->
<div class="judul-section">
    <div class="container">
        <div class="judul-breadcrumb">
            <i class="bi bi-house-door" style="font-size:11px;"></i> Beranda &rsaquo; Profil &rsaquo; Struktur Organisasi
        </div>
        <h2>Struktur Organisasi</h2>
        <p>Bidang PAUD dan Pendidikan Non Formal/Kesetaraan<br>Dinas Pendidikan Kabupaten Lamongan</p>
    </div>
</div>

<!-- KONTEN -->
<section class="py-5">
    <div class="container">

        <!-- Pengurus Inti -->
        <div class="card-section" data-aos="fade-up">
            <div class="section-label">Pengurus Inti</div>

            <?php
            $inti_data = [];
            while ($s = mysqli_fetch_assoc($inti)) $inti_data[] = $s;
            ?>

            <div class="inti-grid">
                <div class="inti-row-top">
                    <?php if (!empty($inti_data[0])): $s = $inti_data[0]; ?>
                    <div class="kartu-pimpinan">
                        <img src="<?= htmlspecialchars($s['foto']) ?>" class="pim-foto" onerror="this.src='img/default.png'" alt="<?= htmlspecialchars($s['nama']) ?>">
                        <div class="pim-info">
                            <div class="pim-nama"><?= htmlspecialchars($s['nama']) ?></div>
                            <div class="pim-jabatan"><?= htmlspecialchars($s['jabatan']) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="inti-row-bawah">
                    <?php foreach (array_slice($inti_data, 1) as $s): ?>
                    <div class="kartu-anggota">
                        <img src="<?= htmlspecialchars($s['foto']) ?>" class="angg-foto" onerror="this.src='img/default.png'" alt="<?= htmlspecialchars($s['nama']) ?>">
                        <div class="angg-nama"><?= htmlspecialchars($s['nama']) ?></div>
                        <div class="angg-jabatan"><?= htmlspecialchars($s['jabatan']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Seluruh Anggota -->
        <div class="card-section" data-aos="fade-up">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                <div class="section-label mb-0" style="flex:1;">Seluruh Anggota</div>
                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchStruktur" placeholder="Cari nama atau jabatan..." onkeyup="filterStruktur()">
                </div>
            </div>

            <div class="table-responsive">
                <table class="tabel-anggota" id="tabelStruktur">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th width="60">Foto</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($s = mysqli_fetch_assoc($lainnya)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="td-foto">
                                <img src="<?= htmlspecialchars($s['foto']) ?>" onerror="this.src='img/default.png'" alt="foto">
                            </td>
                            <td><?= htmlspecialchars($s['nama']) ?></td>
                            <td><?= htmlspecialchars($s['jabatan']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<!-- FOOTER — tidak diubah -->
<footer class="footer" data-aos="fade-up">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <h6 class="footer-heading">Kontak Kami</h6>
                <p class="footer-address">Jl. KH. Ahmad Dahlan No. 75, Kecamatan Lamongan,<br>Jawa Timur 62214</p>
                <ul class="footer-contact-list">
                    <li><i class="bi bi-envelope"></i> disdik@lamongankab.go.id</li>
                    <li><i class="bi bi-telephone"></i> (0322) 321021</li>
                    <li><i class="bi bi-whatsapp"></i> +62 812-4667-6547</li>
                </ul>
                <div class="visitor-badge">
                    <i class="bi bi-people-fill"></i>
                    Pengunjung: <strong><?= $visitor_count ?></strong>
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
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.0435881474013!2d112.4132808!3d-7.1209351!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e77f06915e6129b%3A0xc02014603912066!2sDinas%20Pendidikan%20Kabupaten%20Lamongan!5e0!3m2!1sid!2sid!4v1700000000000" loading="lazy" allowfullscreen></iframe>
                </div>
                <div class="text-center pb-2">
                    <img src="img/lamongan-megilan-putih.png" alt="Logo Lamongan Megilan" class="footer-logo-img">
                </div>
            </div>
        </div>
        <hr class="footer-divider">
        <p class="footer-copy">&copy; 2026 Website PAUD &middot; Dinas Pendidikan Kabupaten Lamongan</p>
    </div>
</footer>

<a href="#" class="scroll-top"><i class="bi bi-arrow-up"></i></a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration: 900, once: true });

function filterStruktur() {
    var q = document.getElementById('searchStruktur').value.toLowerCase();
    document.querySelectorAll('#tabelStruktur tbody tr').forEach(function(row) {
        row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
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