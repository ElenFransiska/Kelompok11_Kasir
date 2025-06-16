menu.php
<?php
// menu.php - Menampilkan daftar makanan dan minuman
$conn = new mysqli("localhost", "username", "password", "database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$result = $conn->query("SELECT * FROM view_menu");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kasir - Menu</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Menu Makanan dan Minuman</h1>
    <table>
        <tr>
            <th>Gambar</th>
            <th>Nama</th>
            <th>Keterangan</th>
            <th>Stok</th>
            <th>Harga</th>
            <th>Pesan</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><img src="https://placehold.co/100x100" alt="<?php echo $row['nama']; ?>" /></td>
            <td><?php echo $row['nama']; ?></td>
            <td><?php echo "Keterangan untuk " . $row['nama']; ?></td>
            <td><?php echo $row['stok']; ?></td>
            <td><?php echo "Rp " . number_format($row['harga'], 2, ',', '.'); ?></td>
            <td>
                <form action="order.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="number" name="jumlah" min="1" max="<?php echo $row['stok']; ?>" required>
                    <input type="submit" value="Pesan">
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

