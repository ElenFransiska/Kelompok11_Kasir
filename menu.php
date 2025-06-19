<?php
// menu.php - Menampilkan daftar menu makanan dan minuman
require 'db_connection.php'; // Koneksi ke database

$sql = "SELECT * FROM menu";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        img { max-width: 100px; }
    </style>
</head>
<body>
    <h1>Daftar Menu</h1>
    <table>
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Keterangan</th>
                <th>Stok</th>
                <th>Harga</th>
                <th>Pesan</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="https://placehold.co/100x100?text=<?php echo urlencode($row['nama']); ?>" alt="<?php echo htmlspecialchars($row['nama']); ?>"></td>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                    <td><?php echo (int)$row['stok']; ?></td>
                    <td>Rp <?php echo number_format($row['harga'], 2, ',', '.'); ?></td>
                    <td>
                        <form action="order.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="jumlah" min="1" max="<?php echo $row['stok']; ?>" value="1" required>
                            <input type="submit" value="Pesan">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
