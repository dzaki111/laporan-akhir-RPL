<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $siswa_id = $_POST['siswa_id'] ?? null;
    $jenis_kuis = $koneksi->real_escape_string($_POST['jenis_kuis'] ?? '');
    $skor_benar = $_POST['skor_benar'] ?? 0;
    $total_soal = $_POST['total_soal'] ?? 0;
    $nilai_akhir = $_POST['nilai_akhir'] ?? 0.00;

    if ($siswa_id && $jenis_kuis) {
        $sql = "INSERT INTO HasilKuis (siswa_id, jenis_kuis, skor_benar, total_soal, nilai_akhir) 
                VALUES ('$siswa_id', '$jenis_kuis', '$skor_benar', '$total_soal', '$nilai_akhir')";

        if ($koneksi->query($sql) === TRUE) {
            echo "Sukses menyimpan hasil.";
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo "Error: " . $koneksi->error;
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo "Data tidak lengkap.";
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Metode tidak diizinkan.";
}

$koneksi->close();
?>