<?php
include 'koneksi.php';

if (!isset($_SESSION['siswa_id'])) {
    header('Location: login.php');
    exit();
}

$nama_siswa = htmlspecialchars($_SESSION['nama']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Menu Utama - Belajar Matematika</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            font-family: sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background-color: #f4f4f4; 
            margin: 0; 
        }
        .menu-container { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); 
            /* Standardisasi Lebar */
            width: 90%; 
            max-width: 650px; 
            box-sizing: border-box;
            text-align: center; 
        }
        .header-icons { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 50px; 
            font-size: 30px; 
        }
        /* Ikon Speaker kini bisa diklik */
        .header-icons .speaker-icon { 
            cursor: pointer;
            user-select: none;
        }
        .header-icons a { text-decoration: none; color: black; }
        .title-app { 
            text-align: center; 
            margin-bottom: 40px; 
            line-height: 1.1; 
        }
        .title-app h1 { font-size: 36px; margin: 0; font-weight: bold; }
        .menu-box { 
            background-color: #f8f8f8; 
            padding: 30px; 
            border-radius: 15px; 
            text-align: center; 
        }
        .menu-btn { 
            display: block; 
            background-color: #d17882; 
            color: white; 
            padding: 20px; 
            margin-bottom: 20px; 
            border: none; 
            border-radius: 10px; 
            text-decoration: none; 
            font-size: 24px; 
            font-weight: bold;
            transition: background-color 0.2s; 
            box-shadow: 0 5px #b35e68; 
        }
        .menu-btn:active {
            box-shadow: 0 2px #b35e68;
            transform: translateY(3px);
        }
    </style>
</head>
<body>

<audio id="backgroundAudio" loop autoplay volume="0.3">
    <source src="background_music.mp3" type="audio/mp3">
    Browser Anda tidak mendukung elemen audio.
</audio>

<div class="menu-container">
    <div class="header-icons">
        <span id="speakerIcon" class="speaker-icon">🔊</span> 
        <a href="profile.php">👤</a>
    </div>

    <div class="title-app">
        <h1>pembelajaran</h1>
        <h1>Matematika</h1>
    </div>

    <div class="menu-box">
        <a href="kuis.php?jenis=Penjumlahan" class="menu-btn">pertambahan</a>
        <a href="kuis.php?jenis=Pengurangan" class="menu-btn">pengurangan</a>
        <a href="kuis.php?jenis=Perkalian" class="menu-btn">perkalian</a> 
    </div>
    
    <p style="text-align: center; margin-top: 40px; font-size: 14px; color: #888;">Selamat datang, <?php echo $nama_siswa; ?>!</p>
</div>

<script>
    // --- KONTROL MUSIK DAN SINKRONISASI TEMPO ---
    const audio = document.getElementById('backgroundAudio');
    const speakerIcon = document.getElementById('speakerIcon');
    const storageKeyMute = 'musicMuted';
    const storageKeyTime = 'musicTime'; 

    // 1. Ambil status mute dan waktu dari Local Storage
    const isMuted = localStorage.getItem(storageKeyMute) === 'true';
    const storedTime = parseFloat(localStorage.getItem(storageKeyTime)) || 0;

    // 2. Setel audio berdasarkan status tersimpan
    audio.muted = isMuted;
    speakerIcon.textContent = isMuted ? '🔇' : '🔊';

    // PENTING: Setel waktu putar ke detik yang tersimpan
    if (storedTime > 0) {
        audio.currentTime = storedTime;
    }

    // 3. Coba mainkan audio
    if (!audio.muted) {
        audio.play().catch(error => {
            console.log("Auto-play diblokir. Musik akan mulai setelah interaksi.");
        });
    }

    // 4. Sinkronisasi Waktu Putar saat berpindah halaman
    // Simpan waktu putar setiap detik ke Local Storage saat musik sedang berjalan
    audio.addEventListener('timeupdate', () => {
        localStorage.setItem(storageKeyTime, audio.currentTime.toFixed(2));
    });


    // 5. Fungsi Toggle Mute saat ikon diklik
    speakerIcon.addEventListener('click', () => {
        if (audio.muted) {
            // Unmute
            audio.muted = false;
            localStorage.setItem(storageKeyMute, 'false');
            speakerIcon.textContent = '🔊';
            audio.play().catch(error => {
                console.log("Play button error.");
            });
        } else {
            // Mute
            audio.muted = true;
            localStorage.setItem(storageKeyMute, 'true');
            speakerIcon.textContent = '🔇';
        }
    });
    // -----------------------------------------------------------------
</script>

</body>
</html>