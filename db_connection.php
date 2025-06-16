<?php
// db_connection.php - koneksi ke database MySQL
$servername = "localhost";   // ganti dengan host database Anda
$username = "root";          // ganti dengan username database Anda
$password = "";              // ganti dengan password database Anda
$dbname = "kasir_db";       // ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset agar mendukung UTF-8
$conn->set_charset("utf8mb4");
?>
