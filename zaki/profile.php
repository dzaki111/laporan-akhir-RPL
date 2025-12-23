<?php
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['siswa_id'])) {
    header('Location: login.php');
    exit();
}

$siswa_id = $_SESSION['siswa_id'];
$nama_siswa = htmlspecialchars($_SESSION['nama']);
$skor_tertinggi = 0;

// Ambil Skor Terbaik Siswa
$sql_skor = "SELECT MAX(nilai_akhir) AS max_nilai FROM HasilKuis WHERE siswa_id = '$siswa_id'";
$result_skor = $koneksi->query($sql_skor);

if ($result_skor && $result_skor->num_rows > 0) {
    $row = $result_skor->fetch_assoc();
    $skor_tertinggi = number_format($row['max_nilai'] ?? 0, 0); 
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Siswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* CSS Umum dan Layout */
        body { 
            font-family: sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background-color: #f4f4f4; 
            margin: 0; 
        }
        
        /* Kontainer Utama (Standardisasi Lebar & Padding) */
        .profile-container { 
            background: white; 
            /* Padding 40px di samping kiri/kanan agar tombol ada ruang */
            padding: 20px 40px 50px 40px; 
            border-radius: 20px; 
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); 
            width: 90%;
            max-width: 650px; /* Lebar yang seragam dengan Home/Login */
            text-align: center; 
            /* PENTING: box-sizing diatur agar padding tidak menambah lebar total */
            box-sizing: border-box; 
        }

        /* Header Navigasi (Untuk Ikon Rumah) */
        .header-nav {
            margin-bottom: 30px; 
            text-align: left; 
        }
        .header-nav a {
            font-size: 30px;
            color: black;
            text-decoration: none;
            display: inline-block; 
        }
        
        /* Ikon Profil Besar (Lingkaran dan Ikon Orang) */
        .profile-icon { 
            background-color: #3d3d3d; 
            border-radius: 50%; 
            width: 180px; 
            height: 180px; 
            margin: 0 auto 30px; 
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 100px; 
            color: white; 
            line-height: 1; 
        }
        
        /* Teks Nama dan Score */
        .profile-text {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0 5px; 
        }
        .score-text {
            font-size: 24px;
            color: #888;
            margin-bottom: 50px; 
        }

        /* Tombol Logout (Diperbaiki agar tidak keluar border) */
        /* Tombol Logout (DIJAMIN TIDAK KELUAR BORDER) */
        .btn-logout { 
            background-color: #f44336; /* Merah */
            color: white; 
            
            /* MODIFIKASI PENTING: Hapus padding horizontal (30px) */
            padding: 15px 0; /* Hanya padding atas (15px) dan bawah (15px) */
            
            border: none; 
            border-radius: 10px; 
            cursor: pointer; 
            width: 100%; /* Lebar penuh di dalam padding kontainer */
            font-size: 20px; 
            margin-top: 20px; 
            text-decoration: none;
            display: block; 
        }
        .btn-logout:hover { background-color: #d32f2f; }
    </style>
</head>
<body>

<div class="profile-container">
    
    <div class="header-nav">
        <a href="home.php">
            🏠 
        </a>
    </div>

    <div class="profile-icon">
        👤
    </div>
    
    <p class="profile-text">Nama: <?php echo $nama_siswa; ?></p>
    
    <p class="score-text">Score Terbaik: <?php echo $skor_tertinggi; ?></p>
    
    <a href="logout.php" class="btn-logout">Logout</a>
</div>

</body>
</html>