<?php
include 'koneksi.php';

// Cek status login
if (!isset($_SESSION['siswa_id'])) {
    header('Location: login.php');
    exit();
}

$jenis_kuis = $_GET['jenis'] ?? 'Penjumlahan';
$siswa_id = $_SESSION['siswa_id'];

// Logika pengambilan 5 Soal Acak dari Database
$sql_soal = "SELECT soal_id, pertanyaan, jawaban_kunci, opsi_a, opsi_b, opsi_c 
             FROM Soal 
             WHERE jenis_soal = '$jenis_kuis' 
             ORDER BY RAND() 
             LIMIT 5";

$result_soal = $koneksi->query($sql_soal);
$data_soal = [];
if ($result_soal->num_rows > 0) {
    while ($row = $result_soal->fetch_assoc()) {
        // Gabungkan Opsi dan Kunci Jawaban
        $opsi_array = [
            $row['opsi_a'], 
            $row['opsi_b'], 
            $row['opsi_c']
        ];
        if (!in_array($row['jawaban_kunci'], $opsi_array)) {
            $opsi_array[] = $row['jawaban_kunci'];
        }
        
        shuffle($opsi_array); // Acak urutan opsi jawaban
        
        $data_soal[] = [
            'soal_id' => $row['soal_id'],
            'pertanyaan' => $row['pertanyaan'],
            'kunci' => $row['jawaban_kunci'],
            'opsi' => $opsi_array
        ];
    }
} else {
    // Jika tidak ada soal, alihkan ke home
    header('Location: home.php');
    exit();
}

$soal_json = json_encode($data_soal);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuis <?php echo $jenis_kuis; ?></title>
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
        .kuis-container { 
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
            align-items: center; 
            margin-bottom: 30px; 
            font-size: 30px; 
        }
        .header-icons a { text-decoration: none; color: black; }
        .header-icons .speaker-icon { 
            cursor: pointer;
            user-select: none; 
            margin-left: 20px; 
        }
        #area-soal {
            background-color: #e0e0e0;
            color: #444;
            padding: 50px 20px;
            border-radius: 15px;
            margin-bottom: 40px;
            font-size: 60px; 
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 150px;
        }
        .opsi-jawaban {
            display: flex;
            justify-content: space-around;
        }
        .btn-opsi { 
            background-color: #d17882; 
            color: white; 
            padding: 15px 30px; 
            border: none; 
            border-radius: 10px; 
            cursor: pointer; 
            font-size: 24px; 
            font-weight: bold;
            transition: background-color 0.2s; 
            box-shadow: 0 4px #b35e68; 
            width: 30%;
            text-align: center;
        }
        .btn-opsi:active {
            box-shadow: 0 2px #b35e68;
            transform: translateY(2px);
        }
        /* Popup Style (Jawaban Benar/Salah) */
        .popup-modal {
            display: none; 
            position: fixed; 
            z-index: 10; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5); 
            justify-content: center;
            align-items: center;
        }
        .popup-content {
            background-color: white;
            padding: 50px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            width: 400px;
        }
        .popup-icon-benar { color: #4CAF50; font-size: 150px; margin-bottom: 20px; }
        .popup-icon-salah { color: #f44336; font-size: 150px; margin-bottom: 20px; }
        .popup-text { font-size: 40px; font-weight: bold; }
        .popup-text.benar { color: #4CAF50; }
        .popup-text.salah { color: #f44336; }
        .next-btn { margin-top: 30px; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>

<audio id="backgroundAudio" loop autoplay volume="0.3">
    <source src="background_music.mp3" type="audio/mp3">
    Browser Anda tidak mendukung elemen audio.
</audio>

<div class="kuis-container">
    <div class="header-icons">
        <a href="home.php">🏠</a> 
        <span style="display:flex; align-items: center;">
            <span>Soal ke: <span id="nomor-soal">1</span>/5</span>
            <span id="speakerIcon" class="speaker-icon">🔊</span>
        </span>
    </div>

    <div class="title-app">
        <h1><?php echo strtoupper($jenis_kuis); ?></h1>
    </div>

    <div id="area-soal"></div>
    <div class="opsi-jawaban" id="opsi-jawaban"></div>
</div>

<div id="popupModal" class="popup-modal">
    <div class="popup-content">
        <div id="popup-result-icon"></div>
        <div id="popup-result-text"></div>
        <button id="nextBtn" class="next-btn">Lanjut Soal</button>
    </div>
</div>

<script>
    const dataSoal = <?php echo $soal_json; ?>;
    let indexSoal = 0;
    let skor = 0;
    const totalSoal = dataSoal.length;
    const siswaId = <?php echo $siswa_id; ?>;
    const jenisKuis = '<?php echo $jenis_kuis; ?>';

    // Ambil elemen DOM
    const areaSoal = document.getElementById('area-soal');
    const opsiJawabanDiv = document.getElementById('opsi-jawaban');
    const nomorSoalSpan = document.getElementById('nomor-soal');
    const popupModal = document.getElementById('popupModal');
    const nextBtn = document.getElementById('nextBtn');
    const popupIcon = document.getElementById('popup-result-icon');
    const popupText = document.getElementById('popup-result-text');

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

    function tampilkanSoal() {
        if (indexSoal < totalSoal) {
            const soal = dataSoal[indexSoal];
            nomorSoalSpan.textContent = indexSoal + 1;
            areaSoal.textContent = soal.pertanyaan;

            opsiJawabanDiv.innerHTML = '';
            
            soal.opsi.forEach(opsi => {
                const button = document.createElement('button');
                button.className = 'btn-opsi';
                button.textContent = opsi;
                button.addEventListener('click', () => cekJawaban(opsi, soal.kunci));
                opsiJawabanDiv.appendChild(button);
            });
        } else {
            simpanHasilKuis();
        }
    }

    function cekJawaban(jawabanPilihan, kunciJawaban) {
        const isCorrect = (jawabanPilihan === kunciJawaban);
        
        if (isCorrect) {
            skor++;
            // Tampilan Benar
            popupIcon.innerHTML = '✔';
            popupIcon.className = 'popup-icon-benar';
            popupText.textContent = 'Jawaban Benar';
            popupText.className = 'popup-text benar';
        } else {
            // Tampilan Salah
            popupIcon.innerHTML = '❌';
            popupIcon.className = 'popup-icon-salah';
            popupText.textContent = 'Jawaban Salah';
            popupText.className = 'popup-text salah';
        }

        popupModal.style.display = 'flex';
        
        // Matikan tombol opsi
        Array.from(opsiJawabanDiv.children).forEach(btn => btn.disabled = true);
    }

    nextBtn.addEventListener('click', () => {
        popupModal.style.display = 'none';
        indexSoal++;
        tampilkanSoal();
    });
    
    // Fungsi Menyimpan Hasil dan Mengalihkan ke Halaman Skor
    function simpanHasilKuis() {
        const nilaiAkhir = (skor / totalSoal) * 100;
        const namaSiswa = '<?php echo $_SESSION['nama']; ?>';
        
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "simpan_hasil.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                // Sukses disimpan, alihkan ke halaman skor
                window.location.href = `hasil_akhir.php?nilai=${nilaiAkhir.toFixed(2)}&nama=${namaSiswa}`;
            } else if (this.readyState === 4 && this.status !== 200) {
                 // Gagal disimpan, tetap alihkan ke halaman skor (dengan error flag)
                 window.location.href = `hasil_akhir.php?nilai=${nilaiAkhir.toFixed(2)}&nama=${namaSiswa}&error=1`;
            }
        };
        const dataKirim = `siswa_id=${siswaId}&jenis_kuis=${jenisKuis}&skor_benar=${skor}&total_soal=${totalSoal}&nilai_akhir=${nilaiAkhir.toFixed(2)}`;
        xhr.send(dataKirim);
    }

    tampilkanSoal();
</script>

</body>
</html>