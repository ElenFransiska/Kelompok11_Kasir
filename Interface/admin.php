<?php
require_once '../db_connection.php';
// Anda mungkin ingin memulai sesi di sini jika belum dilakukan di db_connection.php
// session_start(); 

// Konfigurasi paginasi
$items_per_page = 5; // Jumlah item per halaman
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman saat ini
$offset = ($current_page - 1) * $items_per_page; // Offset untuk query

// Hitung total item
$total_items_query = "SELECT COUNT(*) AS total FROM produk";
$total_items_result = $conn->query($total_items_query);
$total_items = $total_items_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page); // Total halaman

// Ambil data produk dengan limit dan offset
$sql = "SELECT * FROM produk LIMIT $items_per_page OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk</title>
    <link rel="stylesheet" href="../css/css_product.css">
    
</head>
<body>
    <div class="logout-fixed-top-right">
        <a href="../index.php">
            <button type="submit">Logout</button>
        </a>
    </div>

    <div class="product-container">
        <h1 class="product-header">Manajemen Produk</h1> 
        
        <?php if (isset($_GET['message'])): ?>
            <div class="product-message success"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
        
        <table class="product-table">
            <tr>
                <th>ID Produk</th>
                <th>Kategori</th>
                <th>Nama</th>
                <th>Image</th>
                <th>Keterangan</th>
                <th>Stok</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_produk']; ?></td>
                        <td><?php echo $row['kategori']; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td><img src="../<?= $row['image'] ?>" alt="<?= $row['nama'] ?>"></td>
                        <td><?php echo $row['keterangan']; ?></td>
                        <td><?php echo $row['stok']; ?></td>
                        <td><?php echo $row['harga']; ?></td>
                        <td>
                            <a href="edit_form.php?id=<?php echo $row['id_produk']; ?>" class="btn btn-primary">Edit</a>
                            <a href="../logic/delete_product.php?id=<?php echo $row['id_produk']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">Tidak ada data produk.</td>
                </tr>
            <?php endif; ?>
        </table>

        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="?page=<?php echo $current_page - 1; ?>">Sebelumnya</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $current_page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo $current_page + 1; ?>">Berikutnya</a>
            <?php endif; ?>
        </div>

        <div class="product-form">
            <h2>Tambah Produk</h2>
            <form method="POST" action="../logic/add_product.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <select id="kategori" name="kategori" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Makanan">Makanan</option>
                        <option value="Minuman">Minuman</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nama">Nama Produk</label>
                    <input type="text" id="nama" name="nama" required>
                </div>
                <div class="form-group">
                    <label for="image">Gambar Produk</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan Produk</label>
                    <textarea id="keterangan" name="keterangan" required></textarea>
                </div>
                <div class="form-group">
                    <label for="stok">Stok Produk</label>
                    <input type="number" id="stok" name="stok" required>
                </div>
                    <div class="form-group">
                    <label for="harga">Harga Produk</label>
                    <input type="number" id="harga" name="harga" required>
                </div>
                <button type="submit" class="submit-btn">Tambah Produk</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>