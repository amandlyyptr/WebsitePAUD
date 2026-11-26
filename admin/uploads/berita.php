<?php
include 'admin/includes/db.php';
$q = mysqli_query($conn, "SELECT * FROM berita ORDER BY tanggal DESC");
?>

<h2>Berita</h2>

<?php while ($b = mysqli_fetch_assoc($q)) { ?>
  <img src="admin/uploads/berita/<?= $b['gambar'] ?>" width="200">
  <h3><?= $b['judul'] ?></h3>
  <p><?= substr($b['isi'], 0, 150) ?>...</p>
<?php } ?>
