<?php
session_start();

include '../../includes/db.php';
include '../../includes/functions.php';
cekAdmin();

$username = $_SESSION['username'] ?? 'Admin';
$initial  = strtoupper(substr($username, 0, 2));

$limit = 4;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM struktur");
$totalData  = mysqli_fetch_assoc($totalQuery)['total'];

$totalPage  = max(1, ceil($totalData / $limit));

if ($page > $totalPage) $page = $totalPage;

$start = ($page - 1) * $limit;

$data = mysqli_query($conn, "
    SELECT * FROM struktur 
    ORDER BY kategori='inti' DESC, id ASC 
    LIMIT $start, $limit
");

$edit = false;
$form = [
    'id'       => '',
    'nama'     => '',
    'jabatan'  => '',
    'urutan'   => 1,
    'foto'     => '',
    'kategori' => 'inti'
];

if (isset($_GET['edit'])) {
    $edit = true;
    $id = $_GET['edit'];
    $q  = mysqli_query($conn, "SELECT * FROM struktur WHERE id='$id'");
    $form = mysqli_fetch_assoc($q);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struktur Organisasi | Admin PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/struktur.css">
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

        <a href="dashboard.php" class="nav-link">
            <i class="bi bi-speedometer2"></i>
            <span class="d-none d-lg-inline">Dashboard</span>
        </a>

        <div class="dropdown ms-4">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#menu">
                <i class="bi bi-folder2-open"></i>
                <span class="d-none d-lg-inline">Konten</span>
            </a>

            <div class="collapse show ps-5 mt-2" id="menu">
                <a href="struktur.php" class="nav-link active">
                    <i class="bi bi-people"></i> Struktur
                </a>
                <a href="seksi.php" class="nav-link"><i class="bi bi-diagram-3"></i> Seksi</a>
                <a href="berita.php" class="nav-link"><i class="bi bi-newspaper"></i> Berita</a>
                <a href="materi.php" class="nav-link"><i class="bi bi-journal-bookmark"></i> Edukatif</a>
            </div>
        </div>

        <a href="saran.php" class="nav-link">
            <i class="bi bi-chat-dots"></i>
            <span class="d-none d-lg-inline">Saran</span>
        </a>

        <div class="sidebar-footer mt-auto pt-3">
            <a href="../auth/logout.php" class="nav-link text-danger"
               onclick="return confirm('Yakin logout?')">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-lg-inline">Logout</span>
            </a>
        </div>

    </div>
</div>

<!-- ================= MAIN ================= -->
<div class="main-content" id="mainContent">

    <!-- TOPBAR -->
    <div class="topbar d-flex justify-content-between align-items-center mb-4">
        <div>
    <h4 class="mb-0 fw-bold text-primary">
        Struktur Organisasi
    </h4>
    <p class="mb-0 text-muted" style="font-size:0.85rem;">
        Kelola data pengurus dan staf
    </p>
</div>
        <div class="d-flex align-items-center gap-3">
            <div class="user-avatar"><?= $initial ?></div>
        </div>
    </div>

    <div class="container-fluid">

        <!-- FORM -->
        <div class="content-card mb-5">
            <div class="card-header-custom">
                <h5 class="mb-0">
                    <?= $edit ? 'Edit Struktur' : 'Tambah Struktur' ?>
                </h5>
            </div>

            <div class="card-body-custom">
                <form action="../crud/struktur_crud.php" method="POST" enctype="multipart/form-data">

                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?= $form['id'] ?>">
                    <?php endif; ?>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-person text-primary"></i> Nama
                            </label>
                            <input type="text" name="nama" class="form-control"
                                   placeholder="Masukkan nama"
                                   value="<?= htmlspecialchars($form['nama']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-briefcase text-primary"></i> Jabatan
                            </label>
                            <input type="text" name="jabatan" class="form-control"
                                   placeholder="Masukkan jabatan"
                                   value="<?= htmlspecialchars($form['jabatan']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-diagram-3 text-primary"></i> Kategori
                            </label>
                            <select name="kategori" class="form-select" required>
                                <option value="inti"    <?= $form['kategori'] == 'inti'    ? 'selected' : '' ?>>Inti</option>
                                <option value="lainnya" <?= $form['kategori'] == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-image text-primary"></i> Foto
                                <span class="text-muted fw-normal">(Opsional)</span>
                            </label>

                            <div class="d-flex align-items-center gap-3 mb-2">
                                <?php if ($edit && !empty($form['foto'])): ?>
                                <div>
                                    <img src="<?= htmlspecialchars($form['foto']) ?>"
                                         class="avatar-sm rounded-circle border" alt="Foto saat ini">
                                    <div class="text-muted small">Foto saat ini</div>
                                </div>
                                <?php endif; ?>

                                <div id="previewBox" style="display:none;">
                                    <img id="previewImg" class="avatar-sm rounded-circle border border-primary" alt="Preview">
                                    <div class="text-primary small">Foto baru</div>
                                </div>
                            </div>

                            <input type="file" name="foto" class="form-control"
                                   accept="image/*"
                                   onchange="previewFoto(this)">
                            <div class="form-text">Format: JPG, PNG — Maks. 2MB</div>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" name="<?= $edit ? 'update' : 'simpan' ?>" class="btn btn-primary">
                            <i class="bi <?= $edit ? 'bi-pencil' : 'bi-save' ?> me-1"></i>
                            <?= $edit ? 'Update' : 'Simpan' ?>
                        </button>

                        <button type="reset" class="btn btn-outline-secondary" onclick="resetPreview()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>

                        <?php if ($edit): ?>
                        <a href="struktur.php" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </a>
                        <?php endif; ?>
                    </div>

                </form>
            </div>
        </div>

        <!-- TABLE -->
        <div class="content-card">

            <div class="card-header-custom d-flex justify-content-between">
                <h5 class="mb-0">Data Struktur</h5>
                <span class="badge bg-light text-dark">
                    <?= $totalData ?> data
                </span>
            </div>

            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-custom mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php while ($s = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= $s['id'] ?></td>

                                <td>
                                    <?php if ($s['foto']): ?>
                                        <img src="<?= htmlspecialchars($s['foto']) ?>"
                                             class="avatar-sm rounded-circle" alt="Foto">
                                    <?php else: ?>
                                        <div class="avatar-empty">
                                            <?= strtoupper(substr($s['nama'], 0, 2)) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td><div class="fw-semibold"><?= htmlspecialchars($s['nama']) ?></div></td>

                                <td><?= htmlspecialchars($s['jabatan']) ?></td>

                                <td>
                                    <span class="badge <?= $s['kategori'] == 'inti' ? 'bg-primary' : 'bg-warning text-dark' ?>">
                                        <?= $s['kategori'] ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="?edit=<?= $s['id'] ?>"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="../crud/struktur_crud.php?hapus=<?= $s['id'] ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Hapus data ini?')"
                                           title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        <?php endwhile; ?>

                        </tbody>
                    </table>

                    <!-- PAGINATION -->
                    <div class="p-3">
                        <?php if ($totalPage > 1): ?>
                        <nav>
                            <ul class="pagination justify-content-center mb-0">

                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>">
                                        <i class="bi bi-chevron-left"></i> Prev
                                    </a>
                                </li>

                                <?php
                                $startPage = max(1, $page - 2);
                                $endPage   = min($totalPage, $page + 2);

                                if ($startPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=1">1</a>
                                    </li>
                                    <?php if ($startPage > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                    <?php endif;
                                endif; ?>

                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                                <?php endfor; ?>

                                <?php if ($endPage < $totalPage): ?>
                                    <?php if ($endPage < $totalPage - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $totalPage ?>"><?= $totalPage ?></a>
                                    </li>
                                <?php endif; ?>

                                <li class="page-item <?= ($page >= $totalPage) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>">
                                        Next <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>

                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>

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
<script>
function previewFoto(input) {
    var file = input.files[0];
    if (!file) return;

    var reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('previewBox').style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function resetPreview() {
    document.getElementById('previewBox').style.display = 'none';
    document.querySelector('input[name="foto"]').value = '';
}

document.getElementById('sidebar').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('mobile-open');
});
</script>

</body>
</html>