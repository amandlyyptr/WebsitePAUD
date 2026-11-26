<?php
include 'includes/db.php';

$q        = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
$kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';

if ($q == '' || $kategori == '') {
  exit;
}

$query = "SELECT * FROM seksi
          WHERE kategori = '$kategori'
          AND judul LIKE '%$q%'
          ORDER BY created_at DESC";

$data = mysqli_query($conn, $query);

if (mysqli_num_rows($data) == 0) {
  echo '<div class="alert alert-warning text-center">Dokumen tidak ditemukan</div>';
  exit;
}

while ($row = mysqli_fetch_assoc($data)) {
?>
  <div class="card mb-3 shadow-sm">
    <div class="card-body">
      <h6 class="fw-bold"><?= $row['judul']; ?></h6>

      <?php if (!empty($row['deskripsi'])) { ?>
        <p class="small text-muted"><?= nl2br($row['deskripsi']); ?></p>
      <?php } ?>

      <a href="uploads/seksi/<?= $row['file']; ?>" 
         target="_blank"
         class="btn btn-sm btn-primary">
        <i class="bi bi-file-earmark"></i> Lihat Dokumen
      </a>
    </div>
  </div>
<?php } ?>
