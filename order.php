order.php
<?php
// order.php - Menangani pemesanan dan menampilkan ringkasan
$conn = new mysqli("localhost", "root", "", "database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $jumlah = $_POST['jumlah'];
    $stmt = $conn->prepare("CALL tambah_order(?, NULL, ?)");
    $stmt->bind_param("ii", $id, $jumlah);
    $stmt->execute();
    $stmt->close();
    echo "<h2>Pesanan Berhasil!</h2>";
    echo "<p>Anda telah memesan " . $jumlah . " item.</p>";
} else {
    echo "<h2>Pesanan Gagal!</h2>";
}
?>
<a href="menu.php">Kembali ke Menu</a>

