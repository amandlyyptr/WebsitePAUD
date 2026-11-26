<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: auth/login.php");
    exit;
}
?>

<h1>DASHBOARD ADMIN</h1>
<p>Login berhasil 🎉</p>
