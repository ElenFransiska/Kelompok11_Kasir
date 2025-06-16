<?php
// delete_history.php - Menghapus riwayat pesanan
require 'db_connection.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    // Hapus dari tabel history
    $sql = "DELETE FROM history WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "Riwayat berhasil dihapus!";
}
?>
