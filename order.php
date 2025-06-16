<?php
// order.php - Form untuk memesan makanan dan minuman
require 'db_connection.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $jumlah = $_POST['jumlah'];

    // Ambil data menu
    $sql = "SELECT * FROM menu WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $menu = $result->fetch_assoc();

    // Hitung total harga
    $total_harga = $menu['harga'] * $jumlah;

    // Simpan order ke database
    $sql_order = "INSERT INTO orders (pelanggan_id, id_makanan, jumlah_makanan, total_harga) VALUES (?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $pelanggan_id = 1; // Ganti dengan ID pelanggan yang sesuai
    $stmt_order->bind_param("iiid", $pelanggan_id, $menu['id'], $jumlah, $total_harga);
    $stmt_order->execute();

    // Update stok
    $sql_update = "UPDATE menu SET stok = stok - ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $jumlah, $menu['id']);
    $stmt_update->execute();

    echo "Pesanan berhasil dibuat! Total harga: Rp " . number_format($total_harga, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order</title>
</head>
<body>
    <h1>Pesan Makanan/Minuman</h1>
    <form action="order.php" method="POST">
        <label for="id">ID Menu:</label>
        <input type="number" name="id" required>
        <label for="jumlah">Jumlah:</label>
        <input type="number" name="jumlah" min="1" required>
        <input type="submit" value="Pesan">
    </form>
</body>
</html>
