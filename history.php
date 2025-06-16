<?php
// history.php - Menampilkan riwayat pesanan
require 'db_connection.php'; // Koneksi ke database

$sql = "SELECT h.*, p.nama AS pelanggan_nama FROM history h JOIN pelanggan p ON h.pelanggan_id = p.id ORDER BY h.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>History</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Riwayat Pesanan</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>Total Harga</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['pelanggan_nama']); ?></td>
                    <td>Rp <?php echo number_format($row['total_harga'], 2, ',', '.'); ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <form action="delete_history.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="submit" value="Hapus">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
