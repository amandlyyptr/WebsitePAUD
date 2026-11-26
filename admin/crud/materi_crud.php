<?php
session_start();
include '../../includes/db.php';
include '../../includes/functions.php';
cekAdmin();


// ================== TAMBAH ==================
if (isset($_POST['tambah'])) {

    $judul       = mysqli_real_escape_string($conn, $_POST['judul']);
    $video_url   = mysqli_real_escape_string($conn, $_POST['video_url']);
    $isi_edukatif = mysqli_real_escape_string($conn, $_POST['isi_edukatif']);

    mysqli_query($conn, "
        INSERT INTO materi (judul, video_url, isi_edukatif, created_at)
        VALUES ('$judul', '$video_url', '$isi_edukatif', NOW())
    ");

    header("Location: ../pages/materi.php");
    exit;
}


// ================== UPDATE ==================
if (isset($_POST['update'])) {

    $id          = mysqli_real_escape_string($conn, $_POST['id']);
    $judul       = mysqli_real_escape_string($conn, $_POST['judul']);
    $video_url   = mysqli_real_escape_string($conn, $_POST['video_url']);
    $isi_edukatif = mysqli_real_escape_string($conn, $_POST['isi_edukatif']);

    mysqli_query($conn, "
        UPDATE materi 
        SET judul='$judul', video_url='$video_url', isi_edukatif='$isi_edukatif'
        WHERE id='$id'
    ");

    header("Location: ../pages/materi.php");
    exit;
}


// ================== HAPUS ==================
if (isset($_GET['hapus'])) {

    $id = mysqli_real_escape_string($conn, $_GET['hapus']);

    mysqli_query($conn, "DELETE FROM materi WHERE id='$id'");

    header("Location: ../pages/materi.php");
    exit;
}
?>