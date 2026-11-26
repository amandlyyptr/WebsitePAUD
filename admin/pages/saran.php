<?php
session_start();
include '../../includes/db.php';
include '../../includes/functions.php';
cekAdmin();

$username = $_SESSION['username'] ?? 'Admin';
$initial  = strtoupper(substr($username, 0, 2));

$jml_saran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM saran"))['total'];
$saran     = mysqli_query($conn, "SELECT * FROM saran ORDER BY created_at DESC");

// Ambil semua data ke array untuk pagination JS
$allSaran = [];
while ($row = mysqli_fetch_assoc($saran)) {
    $allSaran[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Saran | Admin PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1e293b;
            --light: #f8fafc;
            --border: #e2e8f0;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #3b82f6 0%, #1d4ed8 50%, #1e40af 100%);
            position: fixed;
            left: 0; top: 0;
            z-index: 1000;
            box-shadow: 4px 0 25px rgba(59,130,246,0.3);
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }

        .logo { height: 44px; width: auto; }

        .brand-text {
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            line-height: 1.2;
        }

        .brand-text small { font-weight: 400; opacity: 0.9; font-size: 0.85rem; }

        .sidebar-menu { padding: 1.5rem 0; }

        .nav-link {
            color: rgba(255,255,255,0.85);
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            margin: 0 1rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(4px);
        }

        .nav-link i { width: 20px; margin-right: 12px; font-size: 1.1rem; }

        .topbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid var(--border);
        }

        .topbar-title {
        font-weight: 700;
        font-size: 1.5rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        -webkit-background-clip: text; /* Untuk Chrome/Safari */
        background-clip: text;         /* Standar untuk browser lain */
        -webkit-text-fill-color: transparent;
    }

        .user-avatar {
            width: 42px; height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 600; font-size: 0.9rem;
        }

        .main-content { margin-left: 280px; min-height: 100vh; padding: 2rem; }

        .content-card {
            background: rgba(255,255,255,0.95);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(59,130,246,0.1);
            border: 1px solid rgba(59,130,246,0.1);
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
            padding: 1rem 1.5rem;
            color: white;
        }

        /* ACCORDION SARAN */
        .saran-item {
            border-bottom: 1px solid var(--border);
            transition: background 0.2s;
        }

        .saran-item:last-child { border-bottom: none; }

        .saran-item:hover { background: #f8fafc; }

        .saran-toggle {
            width: 100%;
            background: none;
            border: none;
            text-align: left;
            padding: 1rem 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .saran-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: linear-gradient(135deg, var(--success), #059669);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 0.9rem;
            flex-shrink: 0;
        }

        .saran-body {
            padding: 0 1.5rem 1.25rem 1.5rem;
            display: none;
        }

        .saran-body.show { display: block; }

        .saran-isi {
    background: #f0f9ff;
    border-left: 4px solid var(--primary);
    border-radius: 0 8px 8px 0;
    padding: 1rem 1.25rem;
    color: var(--dark);
    line-height: 1.7;
    white-space: pre-wrap;
    margin: 0;
    text-align: left;
}

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--secondary);
        }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; padding: 1rem; }
            .sidebar.mobile-open { transform: translateX(0); }
        }
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
            <div class="collapse ps-5 mt-2" id="menu">
                <a href="struktur.php" class="nav-link"><i class="bi bi-people"></i> Struktur</a>
                <a href="seksi.php"    class="nav-link"><i class="bi bi-diagram-3"></i> Seksi</a>
                <a href="berita.php"   class="nav-link"><i class="bi bi-newspaper"></i> Berita</a>
                <a href="materi.php"   class="nav-link"><i class="bi bi-journal-bookmark"></i> Edukatif</a>
            </div>
        </div>

        <a href="saran.php" class="nav-link active">
            <i class="bi bi-chat-dots"></i>
            <span class="d-none d-lg-inline">Saran</span>
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

<!-- ================= MAIN ================= -->
<div class="main-content" id="mainContent">

    <!-- TOPBAR -->
    <div class="topbar d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-0 fw-bold text-primary">
            Manajemen Saran
        </h4>
        <p class="mb-0 text-muted" style="font-size:0.85rem;">
            Kelola masukan &amp; saran dari pengguna
        </p>
    </div>

    <div class="user-avatar d-flex align-items-center justify-content-center">
        <?= $initial ?>
    </div>

</div>

    <div class="container-fluid">

        <!-- INFO -->
        <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <span>Terdapat <strong><?= $jml_saran ?> saran</strong> masuk dari pengunjung website. Klik nama untuk membaca isi saran.</span>
        </div>

        <!-- SEARCH -->
        <!-- SEARCH + EXPORT -->
        <div class="d-flex gap-2 mb-3">
            <input type="text" id="searchSaran" class="form-control"
                   placeholder="Cari nama, email, atau isi saran...">
            <button onclick="exportExcel()" class="btn btn-success text-nowrap">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </button>
        </div>

        <!-- DAFTAR SARAN -->
        <div class="content-card">

            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Daftar Saran Masuk</h5>
                <span class="badge bg-white text-primary fw-bold"><?= $jml_saran ?> saran</span>
            </div>

            <?php if (count($allSaran) > 0): ?>

            <div id="saranList">
                <?php $no = 1; foreach ($allSaran as $row): ?>
                <div class="saran-item" data-search="<?= strtolower(htmlspecialchars($row['nama'] . ' ' . $row['email'] . ' ' . $row['isi'])) ?>">

                    <button class="saran-toggle" onclick="toggleSaran(this)">

                        <!-- Avatar -->
                        <div class="saran-avatar">
                            <?= strtoupper(substr($row['nama'], 0, 2)) ?>
                        </div>

                        <!-- Info -->
                        <div class="flex-grow-1 text-start">
                            <div class="fw-semibold text-dark"><?= htmlspecialchars($row['nama']) ?></div>
                            <small class="text-muted">
                                <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($row['email']) ?>
                                <span class="mx-2">·</span>
                                <i class="bi bi-calendar3 me-1"></i><?= date('d M Y, H:i', strtotime($row['created_at'])) ?>
                            </small>
                        </div>

                        <!-- Preview isi -->
                        <div class="text-muted small d-none d-md-block me-3"
                             style="max-width:200px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">
                            <?= htmlspecialchars(mb_substr($row['isi'], 0, 60)) ?>...
                        </div>

                        <i class="bi bi-chevron-down text-muted"></i>
                    </button>

                    <!-- ISI SARAN (tersembunyi, muncul saat diklik) -->
                    <div class="saran-body">
                        <div class="saran-isi mb-3"><?= nl2br(htmlspecialchars($row['isi'])) ?></div>
                        <a href="../crud/saran_crud.php?aksi=hapus&id=<?= $row['id'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Yakin hapus saran dari <?= addslashes(htmlspecialchars($row['nama'])) ?>?')">
                            <i class="bi bi-trash"></i> Hapus Saran
                        </a>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <!-- PAGINATION -->
            <div class="p-3">
                <nav>
                    <ul class="pagination justify-content-center mb-0" id="pagination"></ul>
                </nav>
            </div>

            <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-chat-dots display-1 text-muted"></i>
                <h5 class="mt-3 text-muted">Belum ada saran masuk</h5>
                <p class="text-muted">Saran akan muncul di sini ketika pengunjung mengirimkan masukan.</p>
            </div>
            <?php endif; ?>

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
// TOGGLE SARAN
function toggleSaran(btn) {
    var body    = btn.nextElementSibling;
    var icon    = btn.querySelector('.bi-chevron-down, .bi-chevron-up');
    var isOpen  = body.classList.contains('show');

    // Tutup semua
    document.querySelectorAll('.saran-body.show').forEach(function(b) {
        b.classList.remove('show');
    });
    document.querySelectorAll('.bi-chevron-up').forEach(function(i) {
        i.classList.replace('bi-chevron-up', 'bi-chevron-down');
    });

    // Buka yang diklik (kalau belum terbuka)
    if (!isOpen) {
        body.classList.add('show');
        icon.classList.replace('bi-chevron-down', 'bi-chevron-up');
    }
}

// SEARCH
document.getElementById('searchSaran').addEventListener('keyup', function() {
    var val = this.value.toLowerCase();
    document.querySelectorAll('.saran-item').forEach(function(item) {
        var match = item.getAttribute('data-search').indexOf(val) !== -1;
        item.style.display = match ? '' : 'none';
    });
    currentPage = 1;
    buildPagination();
});

// PAGINATION
var rowsPerPage = 10;
var currentPage = 1;
var pagination  = document.getElementById('pagination');

function getVisibleItems() {
    var items = [];
    document.querySelectorAll('.saran-item').forEach(function(item) {
        if (item.style.display !== 'none') items.push(item);
    });
    return items;
}

function showPage(page) {
    currentPage = page;
    var items = getVisibleItems();
    var start = (page - 1) * rowsPerPage;
    var end   = start + rowsPerPage;

    document.querySelectorAll('.saran-item').forEach(function(item) {
        item.style.display = 'none';
    });
    items.forEach(function(item, i) {
        if (i >= start && i < end) item.style.display = '';
    });

    buildPagination();
}

function buildPagination() {
    var items      = getVisibleItems();
    var totalPages = Math.ceil(items.length / rowsPerPage);
    pagination.innerHTML = '';

    if (totalPages <= 1) return;

    // PREV
    var prevLi = document.createElement('li');
    prevLi.className = 'page-item' + (currentPage <= 1 ? ' disabled' : '');
    prevLi.innerHTML = '<a class="page-link" href="#"><i class="bi bi-chevron-left"></i> Prev</a>';
    prevLi.onclick = function(e) { e.preventDefault(); if (currentPage > 1) showPage(currentPage - 1); };
    pagination.appendChild(prevLi);

    var startPage = Math.max(1, currentPage - 2);
    var endPage   = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        var li = document.createElement('li');
        li.className = 'page-item';
        li.innerHTML = '<a class="page-link" href="#">1</a>';
        li.onclick = function(e) { e.preventDefault(); showPage(1); };
        pagination.appendChild(li);
        if (startPage > 2) {
            var dots = document.createElement('li');
            dots.className = 'page-item disabled';
            dots.innerHTML = '<span class="page-link">…</span>';
            pagination.appendChild(dots);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        (function(p) {
            var li = document.createElement('li');
            li.className = 'page-item' + (p === currentPage ? ' active' : '');
            li.innerHTML = '<a class="page-link" href="#">' + p + '</a>';
            li.onclick = function(e) { e.preventDefault(); showPage(p); };
            pagination.appendChild(li);
        })(i);
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            var dots2 = document.createElement('li');
            dots2.className = 'page-item disabled';
            dots2.innerHTML = '<span class="page-link">…</span>';
            pagination.appendChild(dots2);
        }
        var liLast = document.createElement('li');
        liLast.className = 'page-item';
        liLast.innerHTML = '<a class="page-link" href="#">' + totalPages + '</a>';
        liLast.onclick = function(e) { e.preventDefault(); showPage(totalPages); };
        pagination.appendChild(liLast);
    }

    // NEXT
    var nextLi = document.createElement('li');
    nextLi.className = 'page-item' + (currentPage >= totalPages ? ' disabled' : '');
    nextLi.innerHTML = '<a class="page-link" href="#">Next <i class="bi bi-chevron-right"></i></a>';
    nextLi.onclick = function(e) { e.preventDefault(); if (currentPage < totalPages) showPage(currentPage + 1); };
    pagination.appendChild(nextLi);
}
// EXPORT EXCEL
function exportExcel() {
    var rows = [['No', 'Nama', 'Email', 'Isi Saran', 'Tanggal']];
    var no   = 1;

    document.querySelectorAll('.saran-item').forEach(function(item) {
        var toggle  = item.querySelector('.saran-toggle');
        var nama    = toggle.querySelector('.fw-semibold') ? toggle.querySelector('.fw-semibold').innerText.trim() : '';
        var smalls  = toggle.querySelectorAll('small');
        var meta    = smalls.length ? smalls[0].innerText.trim() : '';
        var isi     = item.querySelector('.saran-isi') ? item.querySelector('.saran-isi').innerText.trim() : '';

        // Pisah email dan tanggal dari meta
        var metaParts = meta.split('·');
        var email     = metaParts[0] ? metaParts[0].replace(/\s+/g, ' ').trim() : '';
        var tanggal   = metaParts[1] ? metaParts[1].replace(/\s+/g, ' ').trim() : '';

        rows.push([no++, nama, email, isi, tanggal]);
    });

    // Buat CSV dengan BOM agar Excel baca UTF-8 dengan benar
    var csv = rows.map(function(row) {
        return row.map(function(cell) {
            return '"' + String(cell).replace(/"/g, '""') + '"';
        }).join(',');
    }).join('\n');

    var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href     = url;
    a.download = 'saran_paud_' + new Date().toISOString().slice(0, 10) + '.csv';
    a.click();
    URL.revokeObjectURL(url);
}
// INIT
showPage(1);

// Mobile sidebar
document.getElementById('sidebar').addEventListener('click', function() {
    this.classList.toggle('mobile-open');
});
</script>

</body>
</html>