<?php
header('Content-Type: application/json'); // Tetapkan header sebagai JSON
require_once '../db_connection.php'; // Sesuaikan path ke db_connection.php Anda

$response = ['success' => false, 'message' => ''];
$products = [];

try {
    // Memeriksa koneksi database
    if ($conn->connect_error) {
        throw new Exception("Koneksi database gagal: " . $conn->connect_error);
    }

    $sql = "SELECT id_produk, nama AS nama_produk, harga, kategori, keterangan AS deskripsi, stok, image FROM produk ORDER BY kategori, nama_produk";
    $result = $conn->query($sql);

    if ($result === FALSE) {
        throw new Exception("Error saat mengambil produk: " . $conn->error . " SQL: " . $sql);
    }

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Memastikan nilai numerik diubah ke tipe data yang benar untuk JSON
            $row['id_produk'] = (int)$row['id_produk'];
            $row['harga'] = (float)$row['harga'];
            $row['stok'] = (int)$row['stok'];
            // Sesuaikan path gambar jika bersifat relatif. Asumsi gambar ada di folder root seperti 'img/nama_gambar.jpg'
            if (!empty($row['image']) && strpos($row['image'], 'http') !== 0) {
                $row['image'] = '../' . $row['image']; // Menambahkan '../' agar path relatif terhadap menu_baru.php
            } else if (empty($row['image'])) {
                 $row['image'] = 'https://via.placeholder.com/120?text=No+Image'; // Placeholder jika tidak ada gambar
            }
            $products[] = $row;
        }
    }
    
    $response['success'] = true;
    $response['products'] = $products;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Kesalahan pengambilan produk: " . $e->getMessage()); // Catat error untuk debugging
} finally {
    if ($conn) {
        $conn->close();
    }
}

echo json_encode($response);
?>