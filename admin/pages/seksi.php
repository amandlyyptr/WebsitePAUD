<?php
session_start();
include '../../includes/db.php';
include '../../includes/functions.php';
cekAdmin();

$username = $_SESSION['username'] ?? 'Admin';
$initial  = strtoupper(substr($username, 0, 2));

$kategori = $_GET['kategori'] ?? '';

if ($kategori) {
    $data = mysqli_query($conn, "SELECT * FROM seksi WHERE kategori='$kategori' ORDER BY created_at DESC");
} else {
    $data = mysqli_query($conn, "SELECT * FROM seksi ORDER BY created_at DESC");
}

$jml_paud    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM seksi WHERE kategori='paud'"))['total'];
$jml_pnf     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM seksi WHERE kategori='pnf'"))['total'];
$jml_sarpras = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM seksi WHERE kategori='sarpras'"))['total'];

$edit      = false;
$data_edit = [];

if (isset($_GET['edit'])) {
    $edit = true;
    $id   = $_GET['edit'];
    $q    = mysqli_query($conn, "SELECT * FROM seksi WHERE id='$id'");
    $data_edit = mysqli_fetch_assoc($q);
}

// Ambil semua data ke array untuk pagination JS
$allRows = [];
while ($row = mysqli_fetch_assoc($data)) {
    $allRows[] = $row;
}
$totalRows = count($allRows);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dokumen Seksi | Admin PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/seksi.css">
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
                <a href="struktur.php" class="nav-link"><i class="bi bi-people"></i> Struktur</a>
                <a href="seksi.php" class="nav-link active"><i class="bi bi-diagram-3"></i> Seksi</a>
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
<div class="main-content">

    <div class="topbar d-flex justify-content-between align-items-center mb-4">
        <div>
    <h4 class="mb-0 fw-bold text-primary">
        Dokumen Seksi
    </h4>
    <p class="mb-0 text-muted" style="font-size:0.85rem;">
        Upload &amp; kelola dokumen PAUD, PNF, Sarpras
    </p>
</div>
        <div class="user-avatar"><?= $initial ?></div>
    </div>

    <div class="container-fluid">

        <!-- FORM -->
        <div class="content-card mb-4">
            <div class="card-header-custom">
                <h5><?= $edit ? 'Edit Dokumen' : 'Upload Dokumen' ?></h5>
            </div>

            <div class="card-body-custom">
                <form action="../crud/seksi_crud.php" method="POST" enctype="multipart/form-data">

                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">
                    <?php endif; ?>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-card-text text-primary"></i> Judul Dokumen
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="judul" class="form-control"
                                   value="<?= htmlspecialchars($edit ? $data_edit['judul'] : '') ?>"
                                   placeholder="Masukkan judul dokumen" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-tags text-primary"></i> Kategori
                                <span class="text-danger">*</span>
                            </label>
                            <select name="kategori" class="form-select" required>
                                <option value="paud"    <?= ($edit && $data_edit['kategori'] == 'paud')    ? 'selected' : '' ?>>PAUD</option>
                                <option value="pnf"     <?= ($edit && $data_edit['kategori'] == 'pnf')     ? 'selected' : '' ?>>PNF</option>
                                <option value="sarpras" <?= ($edit && $data_edit['kategori'] == 'sarpras') ? 'selected' : '' ?>>SARPRAS</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">
                                <i class="bi bi-file-earmark-arrow-up text-primary"></i> File Dokumen
                                <?= $edit ? '<span class="text-muted fw-normal">(Opsional - kosongkan jika tidak diganti)</span>' : '<span class="text-danger">*</span>' ?>
                            </label>
                            <input type="file" name="file" class="form-control" <?= $edit ? '' : 'required' ?>>
                            <div class="form-text">Format: PDF, DOC, DOCX, XLS, XLSX</div>
                        </div>

                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-primary">
                            <i class="bi bi-<?= $edit ? 'pencil' : 'save' ?> me-1"></i>
                            <?= $edit ? 'Update' : 'Simpan' ?>
                        </button>
                        <?php if ($edit): ?>
                        <a href="seksi.php" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </a>
                        <?php endif; ?>
                    </div>

                </form>
            </div>
        </div>

        <!-- FILTER -->
        <!-- FILTER -->
<!-- FILTER -->
<div class="mb-4">
    <div class="row g-3">

        <div class="col-6 col-md-3">
            <a href="seksi.php"
               class="d-block text-decoration-none filter-card <?= $kategori == '' ? 'active-semua' : '' ?>">
                <div class="filter-btn p-3 rounded-3 text-center border">
                    <div class="filter-icon mb-2">
                        <i class="bi bi-grid-fill fs-3"></i>
                    </div>
                    <div class="fw-bold fs-5"><?= $jml_paud + $jml_pnf + $jml_sarpras ?></div>
                    <div class="small mt-1">Semua Dokumen</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="?kategori=paud"
               class="d-block text-decoration-none filter-card <?= $kategori == 'paud' ? 'active-paud' : '' ?>">
                <div class="filter-btn p-3 rounded-3 text-center border">
                    <div class="filter-icon mb-2">
                        <i class="bi bi-mortarboard-fill fs-3"></i>
                    </div>
                    <div class="fw-bold fs-5"><?= $jml_paud ?></div>
                    <div class="small mt-1">PAUD</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="?kategori=pnf"
               class="d-block text-decoration-none filter-card <?= $kategori == 'pnf' ? 'active-pnf' : '' ?>">
                <div class="filter-btn p-3 rounded-3 text-center border">
                    <div class="filter-icon mb-2">
                        <i class="bi bi-building-fill fs-3"></i>
                    </div>
                    <div class="fw-bold fs-5"><?= $jml_pnf ?></div>
                    <div class="small mt-1">PNF</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="?kategori=sarpras"
               class="d-block text-decoration-none filter-card <?= $kategori == 'sarpras' ? 'active-sarpras' : '' ?>">
                <div class="filter-btn p-3 rounded-3 text-center border">
                    <div class="filter-icon mb-2">
                        <i class="bi bi-tools fs-3"></i>
                    </div>
                    <div class="fw-bold fs-5"><?= $jml_sarpras ?></div>
                    <div class="small mt-1">SARPRAS</div>
                </div>
            </a>
        </div>

    </div>
</div>

        <!-- SEARCH -->
        <input type="text" id="globalSearch" class="form-control mb-3"
               placeholder="Cari judul / kategori...">

        <!-- TABLE -->
        <div class="content-card">

            <div class="card-header-custom d-flex justify-content-between">
                <h5>Data Dokumen</h5>
                <span class="badge bg-white text-dark border"><?= $totalRows ?> data</span>
            </div>

            <div class="card-body-custom p-0">
                <table class="table table-hover mb-0" id="tabelSeksi">
                    <thead class="text-white" style="background: linear-gradient(90deg, #3b82f6, #1d4ed8);">
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>File</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; foreach ($allRows as $row): 
                        if ($row['kategori'] == 'paud')         $badgeClass = 'bg-primary';
                        elseif ($row['kategori'] == 'pnf')      $badgeClass = 'bg-info text-dark';
                        elseif ($row['kategori'] == 'sarpras')  $badgeClass = 'bg-warning text-dark';
                        else                                     $badgeClass = 'bg-secondary';
                    ?>
                        <tr data-time="<?= $row['created_at'] ?>">
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= strtoupper(htmlspecialchars($row['kategori'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($row['file'])): ?>
                                
<a href="<?= htmlspecialchars($row['file']) ?>"
   target="_blank"
   class="btn btn-sm btn-outline-primary">
    <i class="bi bi-eye"></i> Preview
</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="?edit=<?= $row['id'] ?>"
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="../crud/seksi_crud.php?hapus=<?= $row['id'] ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Hapus dokumen ini?')"
                                       title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- PAGINATION -->
                <div class="p-3">
                    <nav>
                        <ul class="pagination justify-content-center mb-0" id="pagination"></ul>
                    </nav>
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
var rowsPerPage  = 5;
var currentPage  = 1;
var allRows      = document.querySelectorAll("#tabelSeksi tbody tr");
var pagination   = document.getElementById("pagination");

function getVisibleRows() {
    var visible = [];
    allRows.forEach(function(r) {
        if (r.getAttribute("data-hidden") !== "true") visible.push(r);
    });
    return visible;
}

function showPage(page) {
    currentPage = page;
    var visible = getVisibleRows();
    var start   = (page - 1) * rowsPerPage;
    var end     = start + rowsPerPage;

    allRows.forEach(function(r) { r.style.display = "none"; });
    visible.forEach(function(r, i) {
        r.style.display = (i >= start && i < end) ? "" : "none";
    });

    buildPagination();
}

function buildPagination() {
    var visible    = getVisibleRows();
    var totalPages = Math.ceil(visible.length / rowsPerPage);
    pagination.innerHTML = "";

    if (totalPages <= 1) return;

    // PREV
    var prevLi = document.createElement("li");
    prevLi.className = "page-item" + (currentPage <= 1 ? " disabled" : "");
    prevLi.innerHTML = '<a class="page-link" href="#"><i class="bi bi-chevron-left"></i> Prev</a>';
    prevLi.onclick = function(e) {
        e.preventDefault();
        if (currentPage > 1) showPage(currentPage - 1);
    };
    pagination.appendChild(prevLi);

    // ELLIPSIS AWAL
    var startPage = Math.max(1, currentPage - 2);
    var endPage   = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        var li = document.createElement("li");
        li.className = "page-item";
        li.innerHTML = '<a class="page-link" href="#">1</a>';
        li.onclick = function(e) { e.preventDefault(); showPage(1); };
        pagination.appendChild(li);

        if (startPage > 2) {
            var dots = document.createElement("li");
            dots.className = "page-item disabled";
            dots.innerHTML = '<span class="page-link">…</span>';
            pagination.appendChild(dots);
        }
    }

    // NOMOR HALAMAN
    for (var i = startPage; i <= endPage; i++) {
        (function(page) {
            var li = document.createElement("li");
            li.className = "page-item" + (page === currentPage ? " active" : "");
            li.innerHTML = '<a class="page-link" href="#">' + page + '</a>';
            li.onclick = function(e) { e.preventDefault(); showPage(page); };
            pagination.appendChild(li);
        })(i);
    }

    // ELLIPSIS AKHIR
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            var dots2 = document.createElement("li");
            dots2.className = "page-item disabled";
            dots2.innerHTML = '<span class="page-link">…</span>';
            pagination.appendChild(dots2);
        }
        var liLast = document.createElement("li");
        liLast.className = "page-item";
        liLast.innerHTML = '<a class="page-link" href="#">' + totalPages + '</a>';
        liLast.onclick = function(e) { e.preventDefault(); showPage(totalPages); };
        pagination.appendChild(liLast);
    }

    // NEXT
    var nextLi = document.createElement("li");
    nextLi.className = "page-item" + (currentPage >= totalPages ? " disabled" : "");
    nextLi.innerHTML = '<a class="page-link" href="#">Next <i class="bi bi-chevron-right"></i></a>';
    nextLi.onclick = function(e) {
        e.preventDefault();
        if (currentPage < totalPages) showPage(currentPage + 1);
    };
    pagination.appendChild(nextLi);
}

// SEARCH
document.getElementById("globalSearch").addEventListener("keyup", function() {
    var val = this.value.toLowerCase();
    allRows.forEach(function(r) {
        var match = r.innerText.toLowerCase().indexOf(val) !== -1;
        r.setAttribute("data-hidden", match ? "false" : "true");
        r.style.display = match ? "" : "none";
    });
    currentPage = 1;
    showPage(1);
});

// HIGHLIGHT DATA BARU (< 24 jam)
allRows.forEach(function(r) {
    var t = r.getAttribute("data-time");
    if (t && ((new Date() - new Date(t)) / 3600000 < 24)) {
        r.style.background = "#fff7cc";
    }
});

// INIT
showPage(1);
</script>

</body>
</html>