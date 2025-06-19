<?php
// manage_stock.php - Mengelola stok menu
require 'db_connection.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah'])) {
        $nama = $_POST['nama'];
        $jenis = $_POST['jenis'];
        $stok = $_POST['stok'];
        $harga = $_POST['harga'];
        $keterangan = $_POST['keterangan'];

        // Panggil procedure untuk menambah menu
        $sql = "CALL tambah_menu(?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssids", $nama, $jenis, $stok, $harga, $keterangan);
        $stmt->execute();
        echo "Menu berhasil ditambahkan!";
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $jenis = $_POST['jenis'];
        $stok = $_POST['stok'];
        $harga = $_POST['harga'];
        $keterangan = $_POST['keterangan'];

        // Panggil procedure untuk mengedit menu
        $sql = "CALL edit_menu(?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issids", $id, $nama, $jenis, $stok, $harga, $keterangan);
        $stmt->execute();
        echo "Menu berhasil diedit!";
    } elseif (isset($_POST['hapus'])) {
        $id = $_POST['id'];

        // Panggil procedure untuk menghapus menu
        $sql = "CALL hapus_menu(?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo "Menu berhasil dihapus!";
    }
}

// Ambil data menu untuk ditampilkan
$sql = "SELECT * FROM view_menu";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Stock</title>
</head>
<body>
    <h1>Kelola Stok Menu</h1>
    <h2>Tambah Menu</h2>
    <form action="manage_stock.php" method="POST">
        <input type="text" name="nama" placeholder="Nama" required>
        <select name="jenis" required>
            <option value="Makanan">Makanan</option>
            <option value="Minuman">Minuman</option>
        </select>
        <input type="number" name="stok" placeholder="Stok" required>
        <input type="number" step="0.01" name="harga" placeholder="Harga" required>
        <textarea name="keterangan" placeholder="Keterangan"></textarea>
        <input type="submit" name="tambah" value="Tambah Menu">
    </form>

    <h2>Edit/Hapus Menu</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Stok</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['jenis']); ?></td>
                    <td><?php echo (int)$row['stok']; ?></td>
                    <td>Rp <?php echo number_format($row['harga'], 2, ',', '.'); ?></td>
                    <td>
                        <form action="manage_stock.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="nama" value="<?php echo htmlspecialchars($row['nama']); ?>" required>
                            <select name="jenis" required>
                                <option value="Makanan" <?php echo $row['jenis'] == 'Makanan' ? 'selected' : ''; ?>>Makanan</option>
                                <option value="Minuman" <?php echo $row['jenis'] == 'Minuman' ? 'selected' : ''; ?>>Minuman</option>
                            </select>
                            <input type="number" name="stok" value="<?php echo (int)$row['stok']; ?>" required>
                            <input type="number" step="0.01" name="harga" value="<?php echo $row['harga']; ?>" required>
                            <input type="submit" name="edit" value="Edit">
                            <input type="submit" name="hapus" value="Hapus">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

