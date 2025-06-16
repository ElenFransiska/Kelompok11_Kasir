history.php
<?php
// history.php - Menampilkan riwayat pesanan
$conn = new mysqli("localhost", "root", "", "database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$result = $conn->query("SELECT h.tanggal, o.jumlah, m.nama AS makanan, mn.nama AS minuman FROM history h JOIN orders o ON h.id_order = o.id LEFT JOIN makanan m ON o.id_makanan = m.id LEFT JOIN minuman mn ON o.id_minuman = mn.id ORDER BY h.tanggal DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kasir - Riwayat Pesanan</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Riwayat Pesanan</h1>
    <table>
        <tr>
            <th>Tanggal</th>
            <th>Nama Makanan</th>
            <th>Nama Minuman</th>
            <th>Jumlah</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['tanggal']; ?></td>
            <td><?php echo $row['makanan']; ?></td>
            <td><?php echo $row['minuman']; ?></td>
            <td><?php echo $row['jumlah']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="menu.php">Kembali ke Menu</a>
</body>
</html>

