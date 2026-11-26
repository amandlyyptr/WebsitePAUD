<?php
session_start();
include '../../includes/db.php';
include '../../includes/functions.php';
cekAdmin();

// === CONFIG SUPABASE ===
$projectUrl = "https://begzvwpbmlebwdcewnjy.supabase.co";
$apiKey     = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImJlZ3p2d3BibWxlYndkY2V3bmp5Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzcwMzA4MjEsImV4cCI6MjA5MjYwNjgyMX0.dh78z8kP9qxncGvU7fmmVyZgaRpzVADwObWOC53-J_0";
$bucket     = "dokumen";

// ================================================================
//  HELPER: upload satu file ke Supabase, return URL publik
// ================================================================
function uploadSupabase($tmpPath, $oriName, $projectUrl, $apiKey, $bucket) {
    if (empty($tmpPath) || !file_exists($tmpPath)) return null;

    $namaFile = uniqid() . '_' . str_replace(" ", "_", basename($oriName));
    $url      = "$projectUrl/storage/v1/object/$bucket/$namaFile";
    $fileData = file_get_contents($tmpPath);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => "PUT",
        CURLOPT_POSTFIELDS     => $fileData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Bearer $apiKey",
            "apikey: $apiKey",
            "Content-Type: application/octet-stream",
            "x-upsert: true",
        ],
    ]);
    curl_exec($ch);
    curl_close($ch);

    return "$projectUrl/storage/v1/object/public/$bucket/$namaFile";
}

// ================================================================
//  HAPUS
// ================================================================
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "INSERT INTO notifikasi (pesan) VALUES ('Admin menghapus berita')");
    mysqli_query($conn, "DELETE FROM berita WHERE id=$id");
    $_SESSION['notif'] = 'Berita berhasil dihapus.';
    header("Location: ../pages/berita.php");
    exit;
}

// ================================================================
//  SIMPAN / UPDATE — deteksi dari id: kosong/0 = tambah, ada = update
// ================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id       = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $judul    = mysqli_real_escape_string($conn, trim($_POST['judul']    ?? ''));
    $isi      = mysqli_real_escape_string($conn, trim($_POST['isi']      ?? ''));
    $kategori = mysqli_real_escape_string($conn, trim($_POST['kategori'] ?? ''));
    $status   = mysqli_real_escape_string($conn, trim($_POST['status']   ?? 'publish'));

    // Siapkan nilai gambar lama (untuk mode edit)
    $gambar_url  = '';
    $gambar2_url = '';
    if ($id > 0) {
        $qOld = mysqli_query($conn, "SELECT gambar, gambar2 FROM berita WHERE id=$id");
        $old  = mysqli_fetch_assoc($qOld);
        $gambar_url  = $old['gambar']  ?? '';
        $gambar2_url = $old['gambar2'] ?? '';
    }

    // Upload gambar 1 jika ada file baru
    if (!empty($_FILES['gambar']['name']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $up = uploadSupabase(
            $_FILES['gambar']['tmp_name'],
            $_FILES['gambar']['name'],
            $projectUrl, $apiKey, $bucket
        );
        if ($up) $gambar_url = $up;
    }

    // Upload gambar 2 jika ada file baru (opsional)
    if (!empty($_FILES['gambar2']['name']) && $_FILES['gambar2']['error'] === UPLOAD_ERR_OK) {
        $up2 = uploadSupabase(
            $_FILES['gambar2']['tmp_name'],
            $_FILES['gambar2']['name'],
            $projectUrl, $apiKey, $bucket
        );
        if ($up2) $gambar2_url = $up2;
    }

    $gambar_url  = mysqli_real_escape_string($conn, $gambar_url);
    $gambar2_url = mysqli_real_escape_string($conn, $gambar2_url);

    if ($id > 0) {
        // UPDATE
        mysqli_query($conn, "UPDATE berita SET
            judul    = '$judul',
            isi      = '$isi',
            kategori = '$kategori',
            gambar   = '$gambar_url',
            gambar2  = '$gambar2_url',
            status   = '$status'
            WHERE id = $id");

        mysqli_query($conn, "INSERT INTO notifikasi (pesan)
            VALUES ('Admin mengupdate berita: $judul')");

        $_SESSION['notif'] = 'Berita berhasil diupdate!';

    } else {
        // INSERT
        mysqli_query($conn, "INSERT INTO berita (judul, isi, kategori, gambar, gambar2, status)
            VALUES ('$judul', '$isi', '$kategori', '$gambar_url', '$gambar2_url', '$status')");

        mysqli_query($conn, "INSERT INTO notifikasi (pesan)
            VALUES ('Admin menambahkan berita: $judul')");

        $_SESSION['notif'] = 'Berita berhasil ditambahkan!';
    }

    header("Location: ../pages/berita.php");
    exit;
}