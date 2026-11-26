<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../../includes/db.php';
include '../../includes/functions.php';

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

/* ===================== SIMPAN ===================== */
if ($aksi === 'simpan') {

    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $isi   = mysqli_real_escape_string($conn, $_POST['isi']);

    $query = "INSERT INTO saran (nama, email, isi) 
              VALUES ('$nama', '$email', '$isi')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../../saran.php?status=ok");
        exit;
    } else {
        echo "Gagal menyimpan saran: " . mysqli_error($conn);
        exit;
    }
}

/* ===================== HAPUS ===================== */
if ($aksi === 'hapus') {

    cekAdmin();

    $id = intval($_GET['id']);

    $query = "DELETE FROM saran WHERE id=$id";

    if (mysqli_query($conn, $query)) {
        header("Location: ../pages/saran.php?hapus=ok");
        exit;
    } else {
        echo "Gagal menghapus saran: " . mysqli_error($conn);
        exit;
    }
}

/* ===================== DEFAULT ===================== */
header("Location: ../pages/saran.php");
exit;
?>