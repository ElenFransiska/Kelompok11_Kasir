<?php
require_once '../db_connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM produk WHERE id_produk=$id";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="../css/css_product.css">
</head>
<body>
    <div class="product-container">
        <h1 class="product-header">Edit Produk</h1>
        
        <form method="POST" action="../logic/update_product.php" class="product-form">
            <input type="hidden" name="id_produk" value="<?php echo $product['id_produk']; ?>">
            
            <div class="form-group">
                <label for="kategori">Kategori</label>
                <input type="text" id="kategori" name="kategori" value="<?php echo $product['kategori']; ?>" required>
            </div>
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" value="<?php echo $product['nama']; ?>" required>
            </div>
            <div class="form-group">
                <label for="image">Image URL</label>
                <input type="text" id="image" name="image" value="<?php echo $product['image']; ?>" required>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" required><?php echo $product['keterangan']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" id="stok" name="stok" value="<?php echo $product['stok']; ?>" required>
            </div>
            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" id="harga" name="harga" value="<?php echo $product['harga']; ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update Produk</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
