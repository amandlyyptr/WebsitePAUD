<?php
session_start();

$currentPage = basename($_SERVER['PHP_SELF']);

include '../../includes/functions.php';
include '../../includes/db.php';
cekAdmin();

$username = $_SESSION['username'] ?? 'Admin';
$initial  = strtoupper(substr($username, 0, 2));

$limit = 6;

$pageNumber = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$totalData = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM berita")
)['total'];

$totalPage  = max(1, ceil($totalData / $limit));
$pageNumber = max(1, min($pageNumber, $totalPage));
$offset     = ($pageNumber - 1) * $limit;

$data = mysqli_query($conn, "SELECT * FROM berita ORDER BY id DESC LIMIT $offset, $limit");
if (!$data) die("Query error: " . mysqli_error($conn));

$editData   = null;
$isEditMode = false;
if (isset($_GET['edit'])) {
    $idEdit   = (int)$_GET['edit'];
    $qEdit    = mysqli_query($conn, "SELECT * FROM berita WHERE id=$idEdit LIMIT 1");
    $editData = mysqli_fetch_assoc($qEdit);
    if ($editData) $isEditMode = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Berita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/berita.css">
</head>
<body>

<!-- ================= SIDEBAR ================= -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="d-flex align-items-center">
            <img src="../../img/logo-dispendik.png" class="logo" alt="Logo">
            <div class="brand-text d-none d-lg-block ms-3">
                PAUD Lamongan
                <small>Admin Panel</small>
            </div>
        </div>
    </div>
    <div class="sidebar-menu">
        <a href="dashboard.php" class="nav-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            <span class="d-none d-lg-inline">Dashboard</span>
        </a>
        <div class="dropdown ms-4">
            <a class="nav-link dropdown-toggle" href="#menu" data-bs-toggle="collapse" data-bs-target="#menu">
                <i class="bi bi-folder2-open"></i>
                <span class="d-none d-lg-inline">Konten</span>
            </a>
            <div class="collapse show ps-5 mt-2" id="menu">
                <a href="struktur.php" class="nav-link <?= $currentPage == 'struktur.php' ? 'active' : '' ?>">
                    <i class="bi bi-people"></i> Struktur
                </a>
                <a href="seksi.php" class="nav-link <?= $currentPage == 'seksi.php' ? 'active' : '' ?>">
                    <i class="bi bi-diagram-3"></i> Seksi
                </a>
                <a href="berita.php" class="nav-link <?= $currentPage == 'berita.php' ? 'active' : '' ?>">
                    <i class="bi bi-newspaper"></i> Berita
                </a>
                <a href="materi.php" class="nav-link <?= $currentPage == 'materi.php' ? 'active' : '' ?>">
                    <i class="bi bi-journal-bookmark"></i> Edukatif
                </a>
            </div>
        </div>
        <a href="saran.php" class="nav-link <?= $currentPage == 'saran.php' ? 'active' : '' ?>">
            <i class="bi bi-chat-dots"></i>
            <span class="d-none d-lg-inline">Saran</span>
        </a>
        <div class="sidebar-footer mt-auto pt-3">
            <a href="../auth/logout.php" class="nav-link text-danger" onclick="return confirm('Yakin logout?')">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-lg-inline">Logout</span>
            </a>
        </div>
    </div>
</div>

<!-- ================= MAIN ================= -->
<div class="main-content">

    <!-- TOPBAR -->
    <!-- TOPBAR -->
<div class="topbar d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-0" style="color:#3b82f6; font-weight:bold;">Manajemen Berita</h4>
        <p class="mb-0 text-muted" style="font-size:0.85rem;">Tambah, edit &amp; kelola berita PAUD Lamongan</p>
    </div>
    <div class="user-avatar"><?= $initial ?></div>
</div>

    <div class="container-fluid">

        <!-- NOTIF -->
        <?php if (!empty($_SESSION['notif'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" id="notifBox">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['notif']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['notif']); endif; ?>

        <!-- ================= FORM TAMBAH / EDIT ================= -->
        <div class="content-card mb-4">

            <form action="../crud/berita_crud.php" method="post" enctype="multipart/form-data">

                <!--
                    KUNCI PERBAIKAN:
                    - hidden 'action' menggantikan name="simpan" / name="update"
                    - hidden 'id' untuk mode edit
                -->
                <input type="hidden" name="action" value="<?= $isEditMode ? 'update' : 'simpan' ?>">
                <input type="hidden" name="id"     value="<?= (int)($editData['id'] ?? 0) ?>">

                <div class="card-body">
                    <div class="row g-3">

                        <!-- JUDUL -->
                        <div class="col-md-8">
                            <label for="judul" class="form-label fw-bold">
                                <i class="bi bi-card-text text-primary"></i>
                                Judul Berita <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="judul" name="judul" class="form-control"
                                   value="<?= htmlspecialchars($editData['judul'] ?? '') ?>"
                                   placeholder="Masukkan judul berita" required>
                        </div>

                        <!-- KATEGORI -->
                        <div class="col-md-4">
                            <label for="kategori" class="form-label fw-bold">
                                <i class="bi bi-tags text-primary"></i>
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select id="kategori" name="kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php
                                $kategoriList = ['Pengumuman', 'Event', 'PAUD', 'PNF'];
                                foreach ($kategoriList as $kat):
                                    $sel = ($editData['kategori'] ?? '') === $kat ? 'selected' : '';
                                ?>
                                <option value="<?= $kat ?>" <?= $sel ?>><?= $kat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- STATUS — field yang hilang di versi lama -->
                        <div class="col-md-4">
                            <label for="status" class="form-label fw-bold">
                                <i class="bi bi-toggle-on text-primary"></i>
                                Status <span class="text-danger">*</span>
                            </label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="draft"   <?= ($editData['status'] ?? 'draft') === 'draft'   ? 'selected' : '' ?>>Draft</option>
                                <option value="publish" <?= ($editData['status'] ?? '')       === 'publish' ? 'selected' : '' ?>>Publish</option>
                            </select>
                        </div>

                        <!-- ISI -->
                        <div class="col-12">
                            <label for="isi" class="form-label fw-bold">
                                <i class="bi bi-file-earmark-text text-primary"></i>
                                Isi Berita <span class="text-danger">*</span>
                            </label>
                            <textarea id="isi" name="isi" class="form-control" rows="5"
                                      placeholder="Masukkan isi berita..." required><?= htmlspecialchars($editData['isi'] ?? '') ?></textarea>
                        </div>

                        <!-- GAMBAR 1 -->
                        <div class="col-md-6">
                            <label for="gambar" class="form-label fw-bold">
                                <i class="bi bi-image text-primary"></i>
                                Gambar Utama <?= !$isEditMode ? '<span class="text-danger">*</span>' : '<span class="text-muted fw-normal">(Opsional saat edit)</span>' ?>
                            </label>

                            <?php if (!empty($editData['gambar'])): ?>
                            <div class="mb-2 p-2 bg-light rounded">
                                <small class="text-muted d-block mb-1">Gambar saat ini:</small>
                                <img src="<?= htmlspecialchars($editData['gambar']) ?>"
                                     width="120" class="rounded border img-thumbnail" alt="Gambar Utama"
                                     onerror="this.style.display='none'">
                            </div>
                            <?php endif; ?>

                            <input type="file" id="gambar" name="gambar" class="form-control"
                                   accept="image/*" <?= !$isEditMode ? 'required' : '' ?>
                                   onchange="previewImg(this, 'preview1')">
                            <div class="form-text">Format: JPG, PNG — Maks. 5MB</div>
                            <img id="preview1" class="rounded border img-thumbnail mt-2 d-none" width="120" alt="Preview">
                        </div>

                        <!-- GAMBAR 2 -->
                        <div class="col-md-6">
                            <label for="gambar2" class="form-label fw-bold">
                                <i class="bi bi-images text-primary"></i>
                                Gambar Tambahan
                                <span class="text-muted fw-normal">(Opsional)</span>
                            </label>

                            <?php if (!empty($editData['gambar2'])): ?>
                            <div class="mb-2 p-2 bg-light rounded">
                                <small class="text-muted d-block mb-1">Gambar saat ini:</small>
                                <img src="<?= htmlspecialchars($editData['gambar2']) ?>"
                                     width="120" class="rounded border img-thumbnail" alt="Gambar Tambahan"
                                     onerror="this.style.display='none'">
                            </div>
                            <?php endif; ?>

                            <input type="file" id="gambar2" name="gambar2" class="form-control"
                                   accept="image/*"
                                   onchange="previewImg(this, 'preview2')">
                            <div class="form-text">Format: JPG, PNG — Maks. 5MB</div>
                            <img id="preview2" class="rounded border img-thumbnail mt-2 d-none" width="120" alt="Preview">
                        </div>

                    </div><!-- /row -->
                </div><!-- /card-body -->

                <div class="card-footer bg-light border-0 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        <?= $isEditMode ? 'Update Berita' : 'Simpan Berita' ?>
                    </button>
                    <button type="reset" class="btn btn-secondary" onclick="resetPreviews()">
                        <i class="bi bi-x-circle"></i> Reset Form
                    </button>
                    <?php if ($isEditMode): ?>
                    <a href="?page=<?= $pageNumber ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- ================= CARD KATALOG BERITA ================= -->
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php if (mysqli_num_rows($data) > 0): ?>
                <?php while ($b = mysqli_fetch_assoc($data)):
                    $judulAman = addslashes(htmlspecialchars($b['judul']));
                    // Gambar bisa URL Supabase (https://...) atau nama file lokal
                    $thumbSrc  = !empty($b['gambar']) ? htmlspecialchars($b['gambar']) : '';
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">

                        <?php if ($thumbSrc): ?>
                        <img src="<?= $thumbSrc ?>"
                             class="card-img-top"
                             style="height:180px;object-fit:cover;"
                             alt="<?= htmlspecialchars($b['judul']) ?>"
                             onerror="this.style.display='none'">
                        <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center bg-light"
                             style="height:180px;">
                            <i class="bi bi-image text-muted" style="font-size:2.5rem;"></i>
                        </div>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <!-- Badge Status -->
                            <div class="d-flex gap-1 mb-2">
                                <?php if (!empty($b['kategori'])): ?>
                                <span class="badge bg-primary">
                                    <i class="bi bi-tag"></i> <?= htmlspecialchars($b['kategori']) ?>
                                </span>
                                <?php endif; ?>
                                <span class="badge <?= ($b['status'] ?? '') === 'publish' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst($b['status'] ?? 'draft') ?>
                                </span>
                                <?php if (!empty($b['gambar2'])): ?>
                                <span class="badge bg-info text-dark" title="Ada 2 foto">
                                    <i class="bi bi-images"></i>
                                </span>
                                <?php endif; ?>
                            </div>

                            <h6 class="card-title mb-1"><?= htmlspecialchars($b['judul']) ?></h6>

                            <p class="card-text text-muted small flex-grow-1">
                                <?= htmlspecialchars(mb_substr(strip_tags($b['isi']), 0, 90)) ?>...
                            </p>

                            <div class="d-flex gap-2 mt-2">
                                <a href="berita_detail.php?id=<?= $b['id'] ?>"
                                   class="btn btn-sm btn-outline-info flex-fill text-center">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <a href="?page=<?= $pageNumber ?>&edit=<?= $b['id'] ?>"
                                   class="btn btn-sm btn-outline-warning flex-fill text-center">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="../crud/berita_crud.php?hapus=<?= $b['id'] ?>"
                                   class="btn btn-sm btn-outline-danger flex-fill text-center"
                                   onclick="return confirm('Yakin hapus berita <?= $judulAman ?>?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-newspaper display-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Belum ada berita</h5>
                        <p class="text-muted">Tambahkan berita pertama Anda!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ================= PAGINATION ================= -->
        <?php if ($totalPage > 1): ?>
        <div class="p-3 mt-2">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $pageNumber <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $pageNumber - 1 ?>">
                            <i class="bi bi-chevron-left"></i> Prev
                        </a>
                    </li>
                    <?php
                    $startPage = max(1, $pageNumber - 2);
                    $endPage   = min($totalPage, $pageNumber + 2);
                    if ($startPage > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=1">1</a></li>
                        <?php if ($startPage > 2): ?>
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif;
                    endif;
                    for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= $i == $pageNumber ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor;
                    if ($endPage < $totalPage): ?>
                        <?php if ($endPage < $totalPage - 1): ?>
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif; ?>
                        <li class="page-item"><a class="page-link" href="?page=<?= $totalPage ?>"><?= $totalPage ?></a></li>
                    <?php endif; ?>
                    <li class="page-item <?= $pageNumber >= $totalPage ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $pageNumber + 1 ?>">
                            Next <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>

    </div><!-- /container-fluid -->
</div><!-- /main-content -->

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
<script src="../assets/js/berita.js"></script>
<script>
// Preview gambar sebelum upload
function previewImg(input, previewId) {
    var preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.classList.add('d-none');
    }
}

// Reset preview saat tombol Reset diklik
function resetPreviews() {
    ['preview1','preview2'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) { el.src = ''; el.classList.add('d-none'); }
    });
}

// Auto-dismiss notif
var notif = document.getElementById('notifBox');
if (notif) setTimeout(function(){ notif.remove(); }, 4000);
</script>
</body>
</html>