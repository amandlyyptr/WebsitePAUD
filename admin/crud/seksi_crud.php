<?php
session_start();
include '../../includes/db.php';
include '../../includes/functions.php';
cekAdmin();

// === CONFIG SUPABASE ===
$projectUrl = "https://begzvwpbmlebwdcewnjy.supabase.co";
$apiKey     = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImJlZ3p2d3BibWxlYndkY2V3bmp5Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzcwMzA4MjEsImV4cCI6MjA5MjYwNjgyMX0.dh78z8kP9qxncGvU7fmmVyZgaRpzVADwObWOC53-J_0"; // anon key kamu
$bucket     = "dokumen";

if (isset($_POST['judul'])) {

    $judul    = mysqli_real_escape_string($conn, $_POST['judul']);
    $kategori = $_POST['kategori'];

    // ===================== UPDATE =====================
    if (!empty($_POST['id'])) {

        $id = $_POST['id'];

        // ambil data lama
        $q = mysqli_query($conn, "SELECT file FROM seksi WHERE id='$id'");
        $old = mysqli_fetch_assoc($q);
        $file_url = $old['file'];

        $tipe_file = null;
        $ukuran    = null;

        // kalau ada file baru
        if (!empty($_FILES['file']['name'])) {

            $file_name = $_FILES['file']['name'];
            $file_tmp  = $_FILES['file']['tmp_name'];
            $file_size = $_FILES['file']['size'];
            $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $nama_file = uniqid() . '_' . str_replace(" ", "_", $file_name);

            $url = "$projectUrl/storage/v1/object/$bucket/$nama_file";
            $fileData = file_get_contents($file_tmp);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $apiKey",
                "apikey: $apiKey",
                "Content-Type: application/octet-stream",
                "x-upsert: true"
            ]);
            curl_exec($ch);
            curl_close($ch);

            $file_url = "$projectUrl/storage/v1/object/public/$bucket/$nama_file";
            $tipe_file = $ext;
            $ukuran    = $file_size;
        }

        // update DB
        if ($tipe_file) {
            mysqli_query($conn, "UPDATE seksi SET 
                judul='$judul',
                kategori='$kategori',
                file='$file_url',
                tipe_file='$tipe_file',
                ukuran='$ukuran'
                WHERE id='$id'");
        } else {
            mysqli_query($conn, "UPDATE seksi SET 
                judul='$judul',
                kategori='$kategori'
                WHERE id='$id'");
        }

        header("Location: ../pages/seksi.php?update=sukses");
        exit;
    }

    // ===================== INSERT =====================

    $file_name = $_FILES['file']['name'];
    $file_tmp  = $_FILES['file']['tmp_name'];
    $file_size = $_FILES['file']['size'];
    $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $nama_file = uniqid() . '_' . str_replace(" ", "_", $file_name);

    $url = "$projectUrl/storage/v1/object/$bucket/$nama_file";
    $fileData = file_get_contents($file_tmp);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "apikey: $apiKey",
        "Content-Type: application/octet-stream",
        "x-upsert: true"
    ]);
    curl_exec($ch);
    curl_close($ch);

    $file_url = "$projectUrl/storage/v1/object/public/$bucket/$nama_file";

    mysqli_query($conn, "INSERT INTO seksi
        (judul, kategori, file, tipe_file, ukuran)
        VALUES ('$judul','$kategori','$file_url','$ext','$file_size')");

    header("Location: ../pages/seksi.php?status=sukses");
    exit;
}

if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    $q = mysqli_query($conn, "SELECT file FROM seksi WHERE id='$id'");
    $d = mysqli_fetch_assoc($q);

    // NOTE: ini belum delete dari supabase (nanti bisa upgrade)
    mysqli_query($conn, "DELETE FROM seksi WHERE id='$id'");

    header("Location: ../pages/seksi.php?hapus=sukses");
    exit;
}