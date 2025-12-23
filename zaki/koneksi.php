<?php
// Mulai session untuk manajemen status login
session_start();

// Konfigurasi Database
$host = "localhost";
$user = "root";
$password = "";
$database = "pembelajaran_matematika";

// Buat koneksi
$koneksi = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
$koneksi->set_charset("utf8");

?>