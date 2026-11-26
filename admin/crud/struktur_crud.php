<?php
session_start();
include '../../includes/db.php';
include '../../includes/functions.php';
cekAdmin();

// === CONFIG SUPABASE ===
$projectUrl = "https://begzvwpbmlebwdcewnjy.supabase.co";
$apiKey     = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImJlZ3p2d3BibWxlYndkY2V3bmp5Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzcwMzA4MjEsImV4cCI6MjA5MjYwNjgyMX0.dh78z8kP9qxncGvU7fmmVyZgaRpzVADwObWOC53-J_0"; // anon key kamu
$bucket     = "dokumen";

if (isset($_POST['simpan'])) {

  $nama     = $_POST['nama'];
  $jabatan  = $_POST['jabatan'];
  $urutan   = $_POST['urutan'];
  $kategori = $_POST['kategori'];

  $foto_url = '';

  if (!empty($_FILES['foto']['name'])) {

    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];

    $nama_foto = uniqid() . '_' . str_replace(" ", "_", $foto);

    $url = "$projectUrl/storage/v1/object/$bucket/$nama_foto";
    $fileData = file_get_contents($tmp);

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

    $foto_url = "$projectUrl/storage/v1/object/public/$bucket/$nama_foto";
  }

  mysqli_query($conn, "
    INSERT INTO struktur (nama, jabatan, foto, kategori)
    VALUES ('$nama', '$jabatan', '$foto_url', '$kategori')
  ");

  header("Location: ../pages/struktur.php");
  exit;
}

if (isset($_POST['update'])) {

  $id       = $_POST['id'];
  $nama     = $_POST['nama'];
  $jabatan  = $_POST['jabatan'];
  $kategori = $_POST['kategori'];



  // ambil foto lama
  $q = mysqli_query($conn, "SELECT foto FROM struktur WHERE id='$id'");
  $old = mysqli_fetch_assoc($q);

  $foto_url = $old['foto'];

  // kalau upload foto baru
  if (!empty($_FILES['foto']['name'])) {

    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];

    $nama_foto = uniqid() . '_' . str_replace(" ", "_", $foto);

    $url = "$projectUrl/storage/v1/object/$bucket/$nama_foto";
    $fileData = file_get_contents($tmp);

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

    $foto_url = "$projectUrl/storage/v1/object/public/$bucket/$nama_foto";
  }

  mysqli_query($conn, "
    UPDATE struktur SET
      nama='$nama',
      jabatan='$jabatan',
      foto='$foto_url',
      kategori='$kategori'
    WHERE id='$id'
  ");

  header("Location: ../pages/struktur.php");
  exit;
}
if (isset($_GET['hapus'])) {

  $id = $_GET['hapus'];

  mysqli_query($conn, "DELETE FROM struktur WHERE id='$id'");

  header("Location: ../pages/struktur.php");
  exit;
}