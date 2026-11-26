<?php
session_start();

include '../../includes/db.php';
include '../../includes/functions.php';
cekAdmin();

$username = $_SESSION['username'] ?? 'Admin';
$initial  = strtoupper(substr($username, 0, 2));

$edit = false;
$form = [
    'id'           => '',
    'judul'        => '',
    'video_url'    => '',
    'isi_edukatif' => ''
];

if (isset($_GET['edit'])) {
    $edit = true;
    $id   = $_GET['edit'];
    $q    = mysqli_query($conn, "SELECT * FROM materi WHERE id='$id'");
    $form = mysqli_fetch_assoc($q);
}

$limit = 5;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page  = max($page, 1);

$start = ($page - 1) * $limit;

$total_data = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM materi")
)['total'];

$total_page = max(1, ceil($total_data / $limit));
if ($page > $total_page) $page = $total_page;

$data = mysqli_query($conn, "SELECT * FROM materi ORDER BY id DESC LIMIT $start, $limit");

function getYoutubeId($url) {
    preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m);
    return $m[1] ?? '';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edukatif | Admin PAUD Lamongan</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/materi.css">
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
            <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#menu">
                <i class="bi bi-folder2-open"></i>
                <span class="d-none d-lg-inline">Konten</span>
            </a>
            <div class="collapse show ps-5 mt-2" id="menu">
                <a href="struktur.php" class="nav-link"><i class="bi bi-people"></i> Struktur</a>
                <a href="seksi.php"    class="nav-link"><i class="bi bi-diagram-3"></i> Seksi</a>
                <a href="berita.php"   class="nav-link"><i class="bi bi-newspaper"></i> Berita</a>
                <a href="materi.php"   class="nav-link active"><i class="bi bi-journal-bookmark"></i> Edukatif</a>
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
        Edukatif
    </h4>
    <p class="mb-0 text-muted" style="font-size:0.85rem;">
        Kelola video &amp; materi pembelajaran
    </p>
</div>
        <div class="user-avatar"><?= $initial ?></div>
    </div>

    <div class="container-fluid">

        <!-- FORM -->
        <div class="content-card mb-5">
            <div class="card-header-custom">
                <h5 class="mb-0">
                    <i class="bi bi-<?= $edit ? 'pencil-square' : 'plus-circle' ?> me-2"></i>
                    <?= $edit ? 'Edit Edukatif' : 'Tambah Edukatif Baru' ?>
                </h5>
            </div>

            <div class="card-body-custom">
                <form action="../crud/materi_crud.php" method="POST">

                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?= $form['id'] ?>">
                    <?php endif; ?>

                    <div class="row g-3">

                        <!-- JUDUL -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-card-heading text-primary"></i> Judul Materi
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="judul"
                                   class="form-control"
                                   value="<?= htmlspecialchars($form['judul']) ?>"
                                   placeholder="Masukkan judul materi edukatif"
                                   required>
                        </div>

                        <!-- LINK VIDEO -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-youtube text-danger"></i> Link Video YouTube
                                <span class="text-danger">*</span>
                            </label>
                            <input type="url"
                                   name="video_url"
                                   class="form-control"
                                   value="<?= htmlspecialchars($form['video_url']) ?>"
                                   placeholder="https://youtu.be/xxxxx atau https://youtube.com/watch?v=xxxxx"
                                   required>
                            <div class="form-text">Salin link dari YouTube lalu tempelkan di sini</div>
                        </div>

                        <!-- ISI EDUKATIF -->
                        <div class="col-12">
                            <label class="form-label fw-bold">
                                <i class="bi bi-file-earmark-text text-primary"></i> Isi / Deskripsi Materi
                                <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <textarea name="isi_edukatif"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Tulis deskripsi atau penjelasan singkat tentang materi ini..."><?= htmlspecialchars($form['isi_edukatif']) ?></textarea>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <?php if ($edit): ?>
                        <button type="submit" name="update" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i> Update
                        </button>
                        <?php else: ?>
                        <button type="submit" name="tambah" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <?php endif; ?>

                        <button type="reset" class="btn btn-outline-secondary" onclick="resetForm()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>

                        <?php if ($edit): ?>
                        <a href="materi.php" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle me-1"></i> Keluar Edit
                        </a>
                        <?php endif; ?>
                    </div>

                </form>
            </div>
        </div>

        <!-- TABLE -->
        <div class="content-card">

            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-table me-2"></i>Data Edukatif</h5>
                <span class="badge bg-light text-dark"><?= $total_data ?> data</span>
            </div>

            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-custom mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Video</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php
                        $no = $start + 1;
                        while ($m = mysqli_fetch_assoc($data)):
                            $vid = getYoutubeId($m['video_url']);
                        ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= $no++ ?></td>

                                <td class="fw-semibold"><?= htmlspecialchars($m['judul']) ?></td>

                                <td class="text-muted small">
                                    <?= htmlspecialchars(mb_substr($m['isi_edukatif'], 0, 70)) ?>...
                                </td>

                                <!-- KOLOM VIDEO -->
                                <td>
                                    <?php if ($vid): ?>
                                        <a href="<?= htmlspecialchars($m['video_url']) ?>"
                                           target="_blank"
                                           style="display:inline-block; position:relative; width:110px;
                                                  border-radius:8px; overflow:hidden; text-decoration:none;">
                                            <img src="https://img.youtube.com/vi/<?= $vid ?>/mqdefault.jpg"
                                                 alt="<?= htmlspecialchars($m['judul']) ?>"
                                                 style="width:110px; height:62px; object-fit:cover;
                                                        display:block; border-radius:8px;"
                                                 onerror="this.src='https://placehold.co/110x62?text=No+Image'">
                                            <div style="position:absolute; top:50%; left:50%;
                                                        transform:translate(-50%,-50%);
                                                        background:rgba(0,0,0,0.55); border-radius:50%;
                                                        width:28px; height:28px;
                                                        display:flex; align-items:center; justify-content:center;">
                                                <i class="bi bi-play-fill text-white"
                                                   style="font-size:0.9rem; margin-left:2px;"></i>
                                            </div>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= htmlspecialchars($m['video_url']) ?>"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-play-circle me-1"></i> Buka
                                        </a>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="?edit=<?= $m['id'] ?>"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="../crud/materi_crud.php?hapus=<?= $m['id'] ?>"
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
                </div>

                <!-- PAGINATION -->
                <div class="p-3">
                    <?php if ($total_page > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center mb-0">

                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">
                                    <i class="bi bi-chevron-left"></i> Prev
                                </a>
                            </li>

                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage   = min($total_page, $page + 2);

                            if ($startPage > 1): ?>
                                <li class="page-item"><a class="page-link" href="?page=1">1</a></li>
                                <?php if ($startPage > 2): ?>
                                <li class="page-item disabled"><span class="page-link">…</span></li>
                                <?php endif;
                            endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($endPage < $total_page): ?>
                                <?php if ($endPage < $total_page - 1): ?>
                                <li class="page-item disabled"><span class="page-link">…</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $total_page ?>"><?= $total_page ?></a>
                                </li>
                            <?php endif; ?>

                            <li class="page-item <?= ($page >= $total_page) ? 'disabled' : '' ?>">
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
<script>
function resetForm() {
    document.querySelector('form').reset();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>