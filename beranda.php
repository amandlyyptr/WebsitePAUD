<?php
session_start();
include 'includes/db.php';

if(!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    mysqli_query($conn, "UPDATE counter SET jumlah = jumlah + 1 WHERE id = 1");
}

$result = mysqli_query($conn, "SELECT jumlah FROM counter WHERE id = 1");
$visitor_count = mysqli_fetch_assoc($result)['jumlah'];

$berita_terbaru = mysqli_query($conn, "
    SELECT * FROM berita 
    WHERE status='publish' 
    ORDER BY tanggal DESC 
    LIMIT 6
");

$event_terbaru = mysqli_query($conn, "
    SELECT * FROM berita 
    WHERE status='publish' AND kategori='event' 
    ORDER BY tanggal DESC 
    LIMIT 3
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda | PAUD Lamongan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Nunito:wght@400;600;700;800;900&family=Noto+Serif+Javanese&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="/website-paud/css/beranda.css?v=2">

    <style>
        /* ========== HERO SECTION ========== */
        .hero {
            position: relative;
            width: 100%;
            min-height: 460px;
            background: #1565a7;
            overflow: visible;
            display: flex;
            align-items: stretch;
            font-family: 'Nunito', sans-serif;
        }

        .hero-left-panel {
            position: relative;
            background: #1565a7;
            flex: 1;
            display: flex;
            align-items: center;
            padding: 60px 80px 60px 60px;
            z-index: 5;
            clip-path: polygon(0 0, 100% 0, 90% 100%, 0 100%);
        }

        .hero-right-panel {
            flex: 0 0 40%;
            background: #1a9c78;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 44px;
            overflow: hidden;
            z-index: 4;
        }

        .hero-right-panel::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
        }

        .hero-right-panel::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -40px;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }

        .hero-star {
            position: absolute;
            background: #fff;
            border-radius: 2px;
            opacity: 0.15;
            transform: rotate(45deg);
            z-index: 2;
            animation: starTwinkle 3s ease-in-out infinite;
        }

        @keyframes starTwinkle {
            0%, 100% { opacity: 0.15; }
            50%       { opacity: 0.35; }
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.35);
            border-radius: 30px;
            padding: 6px 16px;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .hero-eyebrow-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #7effd4;
            animation: eyebrowPulse 1.8s ease-in-out infinite;
        }

        @keyframes eyebrowPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.4; transform: scale(0.75); }
        }

        .hero-title {
            font-size: 2rem;
            font-weight: 900;
            color: #fff;
            line-height: 1.22;
            margin-bottom: 16px;
        }

        .hero-title em {
            font-style: normal;
            color: #7effd4;
        }

        .hero-desc {
            font-size: 13.5px;
            color: rgba(255,255,255,0.82);
            line-height: 1.75;
            margin-bottom: 28px;
            max-width: 400px;
        }

        .hero-btns {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 36px;
        }

        .hero-btn-white {
            background: #fff;
            color: #1565a7;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: 14px;
            padding: 11px 28px;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.18s, box-shadow 0.18s;
        }

        .hero-btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            color: #1565a7;
        }

        .hero-btn-ghost {
            background: transparent;
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            font-size: 14px;
            padding: 11px 26px;
            border-radius: 30px;
            border: 1.5px solid rgba(255,255,255,0.55);
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.18s, background 0.18s;
        }

        .hero-btn-ghost:hover {
            background: rgba(255,255,255,0.12);
            transform: translateY(-2px);
            color: #fff;
        }

        .hero-stats {
            display: flex;
            gap: 0;
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 24px;
        }

        .hero-stat {
            flex: 1;
            padding-right: 24px;
        }

        .hero-stat + .hero-stat {
            padding-left: 24px;
            border-left: 1px solid rgba(255,255,255,0.2);
        }

        .hero-stat-num {
            font-size: 22px;
            font-weight: 900;
            color: #7effd4;
            line-height: 1;
        }

        .hero-stat-lbl {
            font-size: 11px;
            color: rgba(255,255,255,0.65);
            font-weight: 600;
            margin-top: 4px;
            letter-spacing: 0.3px;
        }

        .hero-right-content {
            position: relative;
            z-index: 5;
            width: 100%;
        }

        .hero-icon-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
        }

        .hero-icon-card {
            background: rgba(255,255,255,0.13);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 14px;
            padding: 18px 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: background 0.2s, transform 0.18s;
        }

        .hero-icon-card:hover {
            background: rgba(255,255,255,0.22);
            transform: translateY(-3px);
        }

        .hero-icon-wrap {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ic-yellow { background: #ffe066; }
        .ic-pink   { background: #ffb3d1; }
        .ic-blue   { background: #b3d9ff; }
        .ic-mint   { background: #b3f0de; }

        .hero-icon-card-label {
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
            text-align: center;
        }

        .hero-right-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: rgba(255,255,255,0.13);
            border: 1px solid rgba(255,255,255,0.28);
            border-radius: 30px;
            padding: 9px 18px;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
        }

        .hero-right-badge-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #7effd4;
        }

        .hero-wave {
            position: relative;
            line-height: 0;
            z-index: 3;
            margin-top: -2px;
        }

        .hero-wave svg {
            display: block;
            width: 100%;
        }

        @media (max-width: 768px) {
            .hero {
                flex-direction: column;
                min-height: auto;
            }
            .hero-left-panel {
                clip-path: none;
                padding: 48px 28px 40px;
            }
            .hero-right-panel {
                flex: none;
                width: 100%;
                padding: 32px 24px 40px;
            }
            .hero-title {
                font-size: 1.6rem;
            }
        }
        /* Aksara Jawa menonjol di hero */
        .hero-aksara-big {
            font-family: 'Noto Serif Javanese', serif;
            font-size: 18px;
            color: rgba(126,255,212,0.85);
            line-height: 2.1;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            text-shadow: 0 0 32px rgba(126,255,212,0.2);
        }

        /* ========== END HERO SECTION ========== */


        /* ========== SAMBUTAN BUNDA PAUD ========== */
        .sambutan-section {
            padding: 70px 0;
            background: #fff;
            font-family: 'Nunito', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .sambutan-section::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(26,156,120,0.05);
            pointer-events: none;
        }

        .sambutan-section::after {
            content: '';
            position: absolute;
            bottom: -40px; left: -40px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(21,101,167,0.04);
            pointer-events: none;
        }

        /* Foto bunda */
        .bunda-photo-wrap {
            position: relative;
            display: inline-block;
        }

        .bunda-photo-wrap::before {
            content: '';
            position: absolute;
            inset: -10px;
            border-radius: 24px;
            border: 2px dashed rgba(26,156,120,0.25);
            pointer-events: none;
        }

        .bunda-photo-wrap::after {
            content: '';
            position: absolute;
            bottom: -14px; right: -14px;
            width: 80px; height: 80px;
            border-radius: 50%;
            background: #1a9c78;
            opacity: 0.12;
            pointer-events: none;
        }

        .bunda-img {
            width: 100%;
            max-width: 240px;
            border-radius: 20px;
            object-fit: cover;
            aspect-ratio: 3/4;
            display: block;
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        }

        .bunda-name-card {
            background: #1565a7;
            color: #fff;
            border-radius: 16px;
            padding: 14px 18px;
            margin-top: 16px;
            text-align: center;
            position: relative;
        }

        .bunda-name-card h6 {
            font-size: 13.5px;
            font-weight: 800;
            margin: 0 0 4px;
            line-height: 1.4;
            color: #fff;
        }

        .bunda-name-card small {
            font-size: 11px;
            color: rgba(255,255,255,0.75);
            font-weight: 600;
        }

        /* Konten sambutan */
        .sambutan-content {
            padding-left: 20px;
        }

        .sambutan-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(26,156,120,0.1);
            border: 1px solid rgba(26,156,120,0.2);
            border-radius: 30px;
            padding: 5px 14px;
            font-size: 11.5px;
            font-weight: 700;
            color: #1a9c78;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .sambutan-label-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #1a9c78;
        }

        .sambutan-heading {
            font-size: 1.7rem;
            font-weight: 900;
            color: #1a2340;
            line-height: 1.25;
            margin-bottom: 20px;
        }

        .sambutan-heading span {
            color: #1565a7;
        }

        /* Quote box */
        .sambutan-quote {
            background: linear-gradient(135deg, #f0f7ff 0%, #e8f5f0 100%);
            border-left: 4px solid #1565a7;
            border-radius: 0 14px 14px 0;
            padding: 16px 20px;
            margin-bottom: 20px;
            position: relative;
        }

        .sambutan-quote-icon {
            position: absolute;
            top: 12px; right: 14px;
            font-size: 36px;
            color: #1565a7;
            opacity: 0.12;
            line-height: 1;
            font-family: Georgia, serif;
            font-weight: 900;
        }

        .sambutan-quote p {
            font-size: 13.5px;
            font-style: italic;
            color: #1565a7;
            font-weight: 700;
            margin: 0;
            line-height: 1.65;
        }

        /* Body teks */
        .sambutan-body {
            font-size: 14px;
            color: #444;
            line-height: 1.85;
            margin-bottom: 16px;
        }

        .sambutan-body:last-of-type {
            margin-bottom: 24px;
        }

        /* Tanda tangan */
        .sambutan-signature {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            background: #f8f9fa;
            border-radius: 14px;
            border: 1px solid #e9ecef;
        }

        .sambutan-signature-avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: #1565a7;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sambutan-signature-avatar svg {
            display: block;
        }

        .sambutan-signature-name {
            font-size: 13px;
            font-weight: 800;
            color: #1a2340;
            margin: 0 0 2px;
        }

        .sambutan-signature-title {
            font-size: 11.5px;
            color: #6c757d;
            margin: 0;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .sambutan-content {
                padding-left: 0;
                margin-top: 24px;
            }
            .bunda-photo-wrap { display: block; text-align: center; }
            .bunda-img { max-width: 180px; margin: 0 auto; }
        }
        /* ========== END SAMBUTAN ========== */


        /* ========== EVENT SECTION ========== */
        .event-section {
            padding: 64px 0;
            background: #f5f8ff;
            font-family: 'Nunito', sans-serif;
        }

        .section-eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .section-eyebrow-line {
            flex: 0 0 32px;
            height: 3px;
            border-radius: 2px;
            background: #1565a7;
        }

        .section-eyebrow-text {
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #1565a7;
        }

        .section-title-main {
            font-size: 1.6rem;
            font-weight: 900;
            color: #1a2340;
            margin-bottom: 36px;
        }

        /* Card event */
        .event-card-new {
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #e8edf5;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform 0.22s, box-shadow 0.22s;
        }

        .event-card-new:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 48px rgba(21,101,167,0.12);
        }

        .event-card-img-wrap {
            position: relative;
            height: 190px;
            overflow: hidden;
            background: #e8edf5;
        }

        .event-card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.35s;
        }

        .event-card-new:hover .event-card-img-wrap img {
            transform: scale(1.04);
        }

        .event-badge-date {
            position: absolute;
            top: 12px; left: 12px;
            background: #fff;
            border-radius: 10px;
            padding: 5px 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 700;
            color: #1565a7;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .event-badge-kat {
            position: absolute;
            top: 12px; right: 12px;
            background: #1565a7;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .event-card-body-new {
            padding: 18px 20px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .event-card-title-new {
            font-size: 14.5px;
            font-weight: 800;
            color: #1a2340;
            line-height: 1.45;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .event-card-desc-new {
            font-size: 12.5px;
            color: #6c757d;
            line-height: 1.7;
            flex: 1;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .event-card-footer-new {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #f0f2f5;
            padding-top: 14px;
        }

        .event-btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #1565a7;
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-size: 12px;
            font-weight: 800;
            padding: 8px 18px;
            border-radius: 24px;
            text-decoration: none;
            transition: background 0.18s, transform 0.18s;
        }

        .event-btn-detail:hover {
            background: #0c4a87;
            transform: translateX(2px);
            color: #fff;
        }

        .event-card-author {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #adb5bd;
            font-weight: 600;
        }

        .event-author-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #1a9c78;
        }
        /* ========== END EVENT SECTION ========== */


        /* ========== BERITA SECTION ========== */
        .berita-section {
            padding: 64px 0;
            background: #fff;
            font-family: 'Nunito', sans-serif;
        }

        /* Card berita */
        .berita-card-new {
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #e8edf5;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform 0.22s, box-shadow 0.22s;
        }

        .berita-card-new:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 48px rgba(0,0,0,0.09);
        }

        .berita-card-img-wrap {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: #e8edf5;
        }

        .berita-card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.35s;
        }

        .berita-card-new:hover .berita-card-img-wrap img {
            transform: scale(1.04);
        }

        .berita-kat-badge {
            position: absolute;
            top: 12px; left: 12px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .kat-umum   { background: #e6f1fb; color: #0c447c; }
        .kat-event  { background: #eaf3de; color: #3b6d11; }
        .kat-info   { background: #faeeda; color: #854f0b; }
        .kat-lainnya { background: #fbeaf0; color: #72243e; }

        .berita-card-body-new {
            padding: 16px 18px 18px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .berita-meta-new {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            color: #adb5bd;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .berita-meta-dot {
            width: 3px; height: 3px;
            border-radius: 50%;
            background: #adb5bd;
        }

        .berita-card-title-new {
            font-size: 14px;
            font-weight: 800;
            color: #1a2340;
            line-height: 1.45;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }

        .berita-card-desc-new {
            font-size: 12.5px;
            color: #6c757d;
            line-height: 1.7;
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .berita-card-divider {
            border: none;
            border-top: 1px solid #f0f2f5;
            margin-bottom: 12px;
        }

        .berita-read-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            font-weight: 800;
            color: #1565a7;
            text-decoration: none;
            transition: gap 0.18s;
        }

        .berita-read-link:hover {
            gap: 10px;
            color: #1565a7;
        }

        .berita-read-link-icon {
            width: 22px; height: 22px;
            border-radius: 50%;
            background: #e6f1fb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background 0.18s;
        }

        .berita-read-link:hover .berita-read-link-icon {
            background: #1565a7;
        }

        .berita-read-link:hover .berita-read-link-icon svg path {
            stroke: #fff;
        }

        /* Tombol lihat semua */
        .btn-lihat-semua {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            border: 2px solid #1565a7;
            color: #1565a7;
            font-family: 'Nunito', sans-serif;
            font-size: 13.5px;
            font-weight: 800;
            padding: 10px 32px;
            border-radius: 30px;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }

        .btn-lihat-semua:hover {
            background: #1565a7;
            color: #fff;
        }
        /* ========== END BERITA SECTION ========== */
    </style>
</head>
<body>

<nav class="flat-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="beranda.php">
            <img src="img/logo-dispendik.png" alt="Logo" class="logo">
            <span class="brand-text">
                Bidang PAUD dan Pendidikan Non Formal/Kesetaraan<br>
                <small>Dinas Pendidikan Kab. Lamongan</small>
            </span>
        </a>

        <ul class="nav-list ms-auto">
            <li class="nav-item active">
                <i class="bi bi-house-door"></i>
                <a href="beranda.php">Beranda</a>
            </li>
            <li class="nav-item dropdown">
                <i class="bi bi-info-circle"></i>
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">Profil</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="profil.php">Profil Bidang</a></li>
                    <li><a class="dropdown-item" href="struktur.php">Struktur Organisasi</a></li>
                    <li><a class="dropdown-item" href="tugas.php">Tugas Pokok</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <i class="bi bi-diagram-3"></i>
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">Seksi</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="seksi-pnf.php">Seksi PNF</a></li>
                    <li><a class="dropdown-item" href="seksi-sarpras.php">Seksi Sarpras</a></li>
                    <li><a class="dropdown-item" href="seksi-paud.php">Seksi PAUD</a></li>
                </ul>
            </li>
            <li class="nav-item"><i class="bi bi-folder"></i> <a href="data.php">Data</a></li>
            <li class="nav-item"><i class="bi bi-journal-text"></i> <a href="materi.php">Edukatif</a></li>
            <li class="nav-item"><i class="bi bi-newspaper"></i> <a href="berita.php">Publikasi</a></li>
            <li class="nav-item"><i class="bi bi-chat-dots"></i> <a href="saran.php">Saran</a></li>
            <li class="nav-item">
                <a href="#" id="accessibility-btn"><i class="bi bi-universal-access"></i> Aksibilitas</a>
            </li>
            <li class="nav-item">
                <a href="admin/auth/login.php" class="login-icon"><i class="bi bi-person-fill"></i></a>
            </li>
        </ul>
    </div>
</nav>


<!-- ========== HERO SECTION ========== -->
<section class="hero">
    <div class="hero-star" style="width:10px;height:10px;top:18%;left:4%;animation-delay:0s"></div>
    <div class="hero-star" style="width:7px;height:7px;top:60%;left:7%;animation-delay:0.8s"></div>
    <div class="hero-star" style="width:8px;height:8px;top:30%;left:30%;animation-delay:1.4s"></div>
    <div class="hero-star" style="width:6px;height:6px;top:75%;left:45%;animation-delay:0.5s"></div>

    <div class="hero-left-panel">
        <div>
            <div class="hero-eyebrow">
                <span class="hero-eyebrow-dot"></span>
                Dinas Pendidikan Kab. Lamongan
            </div>

            <!-- Aksara Jawa menonjol -->
            <div class="hero-aksara-big">
                ꧋ꦱꦱ꧀ꦭꦩꦠ꧀ꦢꦠꦱꦁꦢꦶꦧꦶꦢꦁꦥꦲꦸꦢ꧀꧈ꦥꦺꦤ꧀ꦌꦥ꧀꧈<br>ꦭꦤ꧀ꦏꦱꦼꦠꦫꦲꦤ꧀ꦢꦶꦤꦱꦥꦼꦤ꧀ꦢꦶꦢꦶꦏꦤ꧀ꦏꦧꦸꦥꦠꦺꦤ꧀ꦭꦩꦺꦴꦔꦤ꧀꧉
            </div>

            <!-- Judul utama: Selamat Datang -->
            <h1 class="hero-title">
                Selamat Datang di<br>
                <em>Bidang PAUD, PNF,</em><br>
                dan Kesetaraan
            </h1>

            <p class="hero-desc">
                Melayani pengelolaan, pengembangan, dan pembinaan lembaga PAUD
                serta pendidikan non formal di seluruh wilayah Kabupaten Lamongan.
            </p>
            <div class="hero-btns">
                <a href="profil.php" class="hero-btn-white">Profil Bidang</a>
                <a href="berita.php" class="hero-btn-ghost">Lihat Publikasi</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-num">2.400+</div>
                    <div class="hero-stat-lbl">Lembaga PAUD</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-num">38</div>
                    <div class="hero-stat-lbl">Kecamatan</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-num">3</div>
                    <div class="hero-stat-lbl">Unit Seksi</div>
                </div>
            </div>
        </div>
    </div>

    <div class="hero-right-panel">
        <div class="hero-right-content">
            <div class="hero-icon-grid">
                <a href="seksi-paud.php" class="hero-icon-card">
                    <div class="hero-icon-wrap ic-yellow">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="6" width="18" height="14" rx="3" fill="#b45309"/>
                            <rect x="6" y="9" width="12" height="2" rx="1" fill="#fff" opacity="0.85"/>
                            <rect x="6" y="13" width="8" height="2" rx="1" fill="#fff" opacity="0.6"/>
                            <rect x="8" y="3" width="8" height="4" rx="2" fill="#b45309"/>
                        </svg>
                    </div>
                    <span class="hero-icon-card-label">Seksi PAUD</span>
                </a>
                <a href="seksi-pnf.php" class="hero-icon-card">
                    <div class="hero-icon-wrap ic-pink">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="8" r="5" fill="#9d174d"/>
                            <path d="M4 20 Q4 14 12 13 Q20 14 20 20" fill="#9d174d"/>
                            <circle cx="19" cy="7" r="3" fill="#db2777" opacity="0.75"/>
                            <circle cx="5" cy="7" r="3" fill="#db2777" opacity="0.75"/>
                        </svg>
                    </div>
                    <span class="hero-icon-card-label">Seksi PNF</span>
                </a>
                <a href="seksi-sarpras.php" class="hero-icon-card">
                    <div class="hero-icon-wrap ic-blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="2" y="13" width="5" height="9" rx="1.5" fill="#1d4ed8"/>
                            <rect x="9" y="8" width="6" height="14" rx="1.5" fill="#1d4ed8"/>
                            <rect x="17" y="3" width="5" height="19" rx="1.5" fill="#1d4ed8"/>
                        </svg>
                    </div>
                    <span class="hero-icon-card-label">Seksi Sarpras</span>
                </a>
                <a href="data.php" class="hero-icon-card">
                    <div class="hero-icon-wrap ic-mint">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="3" width="18" height="18" rx="3" fill="#065f46"/>
                            <path d="M7 12 L10 15 L17 8" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="hero-icon-card-label">Data &amp; Layanan</span>
                </a>
            </div>
            <div class="hero-right-badge">
                <span class="hero-right-badge-dot"></span>
                Website Resmi Bidang PAUD Lamongan
            </div>
        </div>
    </div>
</section>

<div class="hero-wave">
    <svg viewBox="0 0 1440 56" preserveAspectRatio="none" height="56" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,0 C360,56 720,0 1080,32 C1260,48 1380,16 1440,24 L1440,0 Z" fill="#1a9c78"/>
        <path d="M0,0 C280,44 560,8 840,28 C1080,44 1300,8 1440,20 L1440,0 Z" fill="#1565a7"/>
    </svg>
</div>
<!-- ========== END HERO SECTION ========== -->


<!-- ========== SAMBUTAN BUNDA PAUD ========== -->
<section class="sambutan-section" data-aos="fade-up">
    <div class="container">
        <div class="row align-items-center g-5">

            <!-- Foto -->
            <div class="col-md-4 text-center" data-aos="fade-right" data-aos-delay="100">
                <div class="bunda-photo-wrap d-inline-block">
                    <img src="img/ibu-anis.png" alt="Bunda PAUD" class="bunda-img">
                </div>
                <div class="bunda-name-card mt-3">
                    <h6>Hj. Anis Kartikawati Yuhronur Efendi, S.Kep., Ners.</h6>
                    <small>Bunda PAUD Kabupaten Lamongan</small>
                </div>
            </div>

            <!-- Konten sambutan -->
            <div class="col-md-8 sambutan-content" data-aos="fade-left" data-aos-delay="150">
                <div class="sambutan-label">
                    <span class="sambutan-label-dot"></span>
                    Kata Sambutan
                </div>

                <h2 class="sambutan-heading">
                    Sambutan <span>Bunda PAUD</span><br>
                    Kabupaten Lamongan
                </h2>

                <div class="sambutan-quote">
                    <span class="sambutan-quote-icon">"</span>
                    <p>Assalamu'alaikum warahmatullahi wabarakatuh,</p>
                </div>

                <p class="sambutan-body">
                    Pendidikan Anak Usia Dini (PAUD) merupakan fondasi penting dalam membentuk generasi
                    yang berkualitas dan berkarakter. Oleh karena itu, penyelenggaraan PAUD di Kabupaten
                    Lamongan diarahkan agar dilakukan secara holistik dan integratif, mencakup layanan
                    pendidikan, kesehatan dan gizi, pengasuhan, perlindungan, serta kesejahteraan anak.
                </p>

                <p class="sambutan-body">
                    Kami mengajak seluruh pemangku kepentingan, orang tua, pendidik, serta masyarakat
                    untuk bersama-sama mendukung terwujudnya layanan PAUD yang berkualitas demi
                    generasi Lamongan yang cerdas, sehat, dan berkarakter mulia.
                </p>

                <p class="mt-3 mb-0" style="font-size:13.5px; color:#6c757d; font-style:italic;">
                    Wassalamu'alaikum warahmatullahi wabarakatuh.
                </p>
            </div>

        </div>
    </div>
</section>
<!-- ========== END SAMBUTAN ========== -->

<!-- ========== EVENT TERBARU ========== -->
<section class="event-section">
    <div class="container">
        <div data-aos="fade-up">
            <div class="section-eyebrow">
                <span class="section-eyebrow-line"></span>
                <span class="section-eyebrow-text">Kegiatan</span>
            </div>
            <h2 class="section-title-main">Event Terbaru</h2>
        </div>

        <div class="row g-4">
            <?php while($e = mysqli_fetch_assoc($event_terbaru)) : ?>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="event-card-new">
                    <div class="event-card-img-wrap">
                        <img src="<?= htmlspecialchars($e['gambar']); ?>" alt="<?= htmlspecialchars($e['judul']); ?>">
                        <div class="event-badge-date">
                            <i class="bi bi-calendar3"></i>
                            <?= date('d M Y', strtotime($e['tanggal'])); ?>
                        </div>
                        <span class="event-badge-kat">Event</span>
                    </div>
                    <div class="event-card-body-new">
                        <h5 class="event-card-title-new"><?= htmlspecialchars($e['judul']); ?></h5>
                        <p class="event-card-desc-new"><?= substr(strip_tags($e['isi']), 0, 90); ?>...</p>
                        <div class="event-card-footer-new">
                            <a href="detail-berita.php?id=<?= $e['id']; ?>" class="event-btn-detail">
                                Detail Event
                                <i class="bi bi-arrow-right"></i>
                            </a>
                            <span class="event-card-author">
                                <span class="event-author-dot"></span>
                                Admin
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<!-- ========== END EVENT SECTION ========== -->


<!-- ========== BERITA TERBARU ========== -->
<section class="berita-section">
    <div class="container">
        <div data-aos="fade-up">
            <div class="section-eyebrow">
                <span class="section-eyebrow-line"></span>
                <span class="section-eyebrow-text">Informasi</span>
            </div>
            <h2 class="section-title-main">Berita Terbaru</h2>
        </div>

        <div class="row g-4">
            <?php while($b = mysqli_fetch_assoc($berita_terbaru)) :
                /* Tentukan class badge berdasarkan kategori */
                $kat_class = 'kat-umum';
                $kat_label = 'Umum';
                if (!empty($b['kategori'])) {
                    $kat = strtolower($b['kategori']);
                    if ($kat === 'event')       { $kat_class = 'kat-event';  $kat_label = 'Event'; }
                    elseif ($kat === 'info')    { $kat_class = 'kat-info';   $kat_label = 'Info'; }
                    elseif ($kat === 'umum')    { $kat_class = 'kat-umum';   $kat_label = 'Umum'; }
                    else                        { $kat_class = 'kat-lainnya'; $kat_label = htmlspecialchars($b['kategori']); }
                }
            ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="80">
                <div class="berita-card-new">
                    <div class="berita-card-img-wrap">
                        <img src="<?= htmlspecialchars($b['gambar']); ?>" alt="<?= htmlspecialchars($b['judul']); ?>">
                        <span class="berita-kat-badge <?= $kat_class; ?>"><?= $kat_label; ?></span>
                    </div>
                    <div class="berita-card-body-new">
                        <div class="berita-meta-new">
                            <i class="bi bi-calendar3" style="font-size:11px;"></i>
                            <?= date('d F Y', strtotime($b['tanggal'])); ?>
                            <span class="berita-meta-dot"></span>
                            Admin
                        </div>
                        <h6 class="berita-card-title-new"><?= htmlspecialchars($b['judul']); ?></h6>
                        <p class="berita-card-desc-new"><?= substr(strip_tags($b['isi']), 0, 90); ?>...</p>
                        <hr class="berita-card-divider">
                        <a href="detail-berita.php?id=<?= $b['id']; ?>" class="berita-read-link">
                            Baca Selengkapnya
                            <span class="berita-read-link-icon">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 12h14M13 6l6 6-6 6" stroke="#1565a7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="berita.php" class="btn-lihat-semua">
                Lihat Semua Berita
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<!-- ========== END BERITA SECTION ========== -->


<!-- ========== FOOTER ========== -->
<footer class="footer" data-aos="fade-up">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <h6 class="footer-heading">Kontak Kami</h6>
                <p class="footer-address">
                    Jl. KH. Ahmad Dahlan No. 75, Kecamatan Lamongan,<br>Jawa Timur 62214
                </p>
                <ul class="footer-contact-list">
                    <li><i class="bi bi-envelope"></i> disdik@lamongankab.go.id</li>
                    <li><i class="bi bi-telephone"></i> (0322) 321021</li>
                    <li><i class="bi bi-whatsapp"></i> +62 812-4667-6547</li>
                </ul>
                <div class="visitor-badge">
                    <i class="bi bi-people-fill"></i>
                    Pengunjung: <strong><?= $visitor_count; ?></strong>
                </div>
            </div>
            <div class="col-md-3">
                <h6 class="footer-heading">Menu Cepat</h6>
                <ul class="footer-menu">
                    <li><a href="beranda.php">Beranda</a></li>
                    <li><a href="profil.php">Profil</a></li>
                    <li><a href="berita.php">Berita</a></li>
                    <li><a href="materi.php">Materi</a></li>
                    <li><a href="saran.php">Saran</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="footer-heading">Lokasi Kami</h6>
                <div class="map-box mb-3">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.0435881474013!2d112.4132808!3d-7.1209351!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e77f06915e6129b%3A0xc02014603912066!2sDinas%20Pendidikan%20Kabupaten%20Lamongan!5e0!3m2!1sid!2sid!4v1700000000000"
                        loading="lazy" allowfullscreen>
                    </iframe>
                </div>
                <div class="text-center pb-2">
                    <img src="img/lamongan-megilan-putih.png" alt="Logo Lamongan Megilan" class="footer-logo-img">
                </div>
            </div>
        </div>

        <hr class="footer-divider">
        <p class="footer-copy">
            &copy; 2026 Website PAUD &middot; Dinas Pendidikan Kabupaten Lamongan
        </p>
    </div>
</footer>

<a href="#" class="scroll-top"><i class="bi bi-arrow-up"></i></a>
<script>
  // Navbar active tab handler
  document.querySelectorAll('.nav-list .nav-item').forEach(function(item) {
    var link = item.querySelector(':scope > a');
    if (!link) return;

    link.addEventListener('click', function(e) {
      // Jangan intercept dropdown toggle dari Bootstrap
      if (link.classList.contains('dropdown-toggle')) return;

      // Set active
      document.querySelectorAll('.nav-list .nav-item').forEach(function(i) {
        i.classList.remove('active', 'clicked');
      });
      item.classList.add('active', 'clicked');
      setTimeout(function() { item.classList.remove('clicked'); }, 300);
    });
  });

  // Dropdown item selected state
  document.querySelectorAll('.dropdown-item').forEach(function(dItem) {
    dItem.addEventListener('click', function() {
      var parent = dItem.closest('.nav-item');

      // Reset semua
      document.querySelectorAll('.nav-list .nav-item').forEach(function(i) {
        i.classList.remove('active', 'clicked');
        i.querySelectorAll('.dropdown-item').forEach(function(d) {
          d.classList.remove('selected');
        });
      });

      // Aktifkan parent & item yang diklik
      parent.classList.add('active');
      dItem.classList.add('selected');
    });
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: false });
</script>

<?php include 'includes/accessibility.php'; ?>
</body>
</html>