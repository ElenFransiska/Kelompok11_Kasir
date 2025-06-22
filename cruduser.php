<?php
// cruduser.php

// --- Fungsi untuk koneksi Database MySQL ---
function getDbConnection() {
    // Sesuaikan kredensial database Anda
    $dbHost = 'localhost';
    $dbName = 'kasir_db'; // Database sesuai permintaan: kasir_db
    $dbUser = 'root';    // Ganti dengan username database Anda
    $dbPass = '';        // Ganti dengan password database Anda

    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Dalam produksi, log error ini, jangan tampilkan ke pengguna
        error_log("Database connection error: " . $e->getMessage());
        // Untuk pengembangan, bisa tampilkan
        die("Koneksi database gagal: " . $e->getMessage());
    }
}

// --- Fungsi Otentikasi ---
function otentik($username, $password) {
    $pdo = getDbConnection();
    
    try {
        // Buat tabel admin jika belum ada (sesuai MySQL syntax dan gambar)
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
            id_admin INT(11) PRIMARY KEY AUTO_INCREMENT,
            nama VARCHAR(100) DEFAULT NULL,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        )");

        // --- Contoh data awal jika tabel kosong ---
        // PENTING: Jangan lakukan ini di produksi. Ini hanya untuk kemudahan pengujian.
        $stmt = $pdo->query("SELECT COUNT(*) FROM admin");
        if ($stmt->fetchColumn() == 0) {
            // Hash password menggunakan MD5 sesuai contoh gambar
            // Peringatan: MD5 sangat tidak aman untuk password di produksi!
            $pdo->exec("INSERT INTO admin (username, password, nama) VALUES ('admin', '" . md5('admin') . "', 'Admin Utama')");
            $pdo->exec("INSERT INTO admin (username, password, nama) VALUES ('elen', '" . md5('elen') . "', 'Elen Kasir')");
            $pdo->exec("INSERT INTO admin (username, password, nama) VALUES ('raymond', '" . md5('raymond') . "', 'Raymond Kasir')");
        }

        // Query database untuk username yang cocok dari tabel 'admin'
        $stmt = $pdo->prepare("SELECT id_admin, username, password, nama FROM admin WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verifikasi password menggunakan MD5 (sesuai data di gambar Anda)
            // PENTING: Untuk produksi, gunakan password_verify() dengan hash yang lebih kuat.
            if (md5($password) === $user['password']) {
                // Otentikasi berhasil
                // Simpan data user ke sesi
                $_SESSION['user_logged_in'] = true;
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id_admin'];
                $_SESSION['user_name_display'] = $user['nama'];
                $_SESSION['user_role'] = 'Admin'; // Asumsi semua user di tabel 'admin' adalah Admin
                return true;
            }
        }
    } catch (PDOException $e) {
        error_log("Authentication query error: " . $e->getMessage());
        return false; // Ada error database, anggap otentikasi gagal
    } finally {
        // Tutup koneksi database
        $pdo = null;
    }
    return false; // Default: otentikasi gagal
}
?>