<?php
require_once '../db_connection.php';
require_once '../logic/logic_history.php'; // Include the logic file

// Handle deletion of order history
$message = '';
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $message = deleteOrderHistory($conn);
}

// Fetch order summary
$result = fetchOrderSummary($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Pesanan</title>
    <link rel="stylesheet" href="../css/css_history.css">
</head>
<body>
    <div class="history-container">
        <h1>History Pesanan</h1>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Back Button -->
        <a href="admin.php" class="back-btn">Kembali</a>

        <!-- Delete Link -->
        <a href="?action=delete" class="delete-link" onclick="return confirm('Apakah Anda yakin ingin menghapus seluruh riwayat pesanan?');">Hapus Riwayat</a>

        <table class="history-table">
            <tr>
                <th>ID Pesanan</th>
                <th>Nama Pembeli</th>
                <th>Meja</th>
                <th>Total Produk</th>
                <th>Total Harga</th>
                <th>Tanggal</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_order']; ?></td>
                        <td><?php echo htmlspecialchars($row['nama_pembeli']); ?></td>
                        <td><?php echo $row['meja']; ?></td>
                        <td><?php echo $row['total_produk']; ?></td>
                        <td>Rp <?php echo number_format($row['total_harga'], 2, ',', '.'); ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Tidak ada data pesanan.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
