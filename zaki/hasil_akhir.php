<?php
session_start();

// Ambil data hasil kuis dari URL
$nilai_akhir = $_GET['nilai'] ?? 0;
$nama_siswa = htmlspecialchars($_GET['nama'] ?? 'Siswa');

// 1. Logika Penentuan Pesan Utama
$pesan_utama = "Selamat " . $nama_siswa . " mendapatkan nilai";

// 2. Logika Penentuan Pesan Motivasi (dibawah 70) atau Pujian (diatas 70)
$pesan_motivasi = "";
$nilai_int = intval($nilai_akhir); 

if ($nilai_int < 70) {
    $pesan_motivasi = "Ayok terus belajar, jangan menyerah!";
    $warna_lingkaran = "#d17882"; // Merah Bata (Motivasi)
} else {
    $pesan_motivasi = "Wowww, kamu jago matematika!";
    $warna_lingkaran = "#4CAF50"; // Hijau (Pujian)
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Kuis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            font-family: sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background-color: #333d4a; 
            margin: 0; 
        }
        
        /* Kontainer Utama/Card Score (Disesuaikan lebarnya agar seragam) */
        .score-card { 
            background: white; 
            padding: 40px 40px 50px 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); 
            width: 90%; 
            max-width: 650px; /* Standardisasi lebar */
            box-sizing: border-box;
            text-align: center; 
        }

        /* Judul Score */
        .score-title {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Pesan Selamat */
        .pesan-utama {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        /* Pesan Motivasi/Pujian */
        .pesan-motivasi {
            font-size: 24px;
            font-weight: 500;
            color: <?php echo ($nilai_int < 70) ? '#d17882' : '#4CAF50'; ?>; 
            margin-bottom: 40px;
        }

        /* Lingkaran Nilai */
        .score-circle {
            background-color: <?php echo $warna_lingkaran; ?>; 
            color: white; 
            width: 200px;
            height: 200px;
            line-height: 200px;
            border-radius: 50%;
            margin: 40px auto;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden; 
            /* Ukuran font disesuaikan agar tidak keluar */
            font-size: <?php echo (strlen((string)intval($nilai_akhir)) > 3) ? '50px' : '60px'; ?>;
        }

        /* Tombol Kembali */
        .btn-kembali {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 20px;
            display: inline-block;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .btn-kembali:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<audio id="successSound" volume="1.0">
    <source src="success_sound.mp3" type="audio/mp3">
    </audio>

<div class="score-card">
    <div class="score-title">Score</div>
    
    <p class="pesan-utama"><?php echo $pesan_utama; ?></p>
    
    <div class="score-circle">
        <?php echo intval($nilai_akhir); ?>
    </div>
    
    <p class="pesan-motivasi"><?php echo $pesan_motivasi; ?></p>
    
    <a href="home.php" class="btn-kembali">Kembali ke Menu Utama</a>
</div>

<script>
    // 2. JAVASCRIPT UNTUK MEMUTAR SUARA SETELAH HALAMAN DIMUAT
    document.addEventListener('DOMContentLoaded', function() {
        const sound = document.getElementById('successSound');
        
        // Coba putar suara. Ini akan berfungsi kecuali diblokir oleh browser.
        sound.play().catch(error => {
            console.log("Memutar suara sukses diblokir oleh browser (Autoplay Policy).");
            // Pengguna harus mengklik tombol 'Kembali ke Menu Utama' sekali 
            // agar sound effect bisa diaktifkan di kemudian hari.
        });
    });
</script>

</body>
</html>