<?php
require_once '../db_connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Mengambil data Image dari tabel produk
    $sql = "SELECT image FROM produk WHERE id_produk = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_path = '../' . $row['image']; // Add ../ to get full path
        
        // Delete data pada product dari database
        $delete_sql = "DELETE FROM produk WHERE id_produk = $id";
        if ($conn->query($delete_sql) === TRUE) {
            // If successful
            if (file_exists($image_path)) {
                unlink($image_path); // Menghapus file gambar yang tersingkronisasi dengan data yang dihapus di db
            }
            header("Location: ../Interface/admin.php?message=Produk+berhasil+dihapus");
        } else {
            header("Location: ../Interface/admin.php?message=Error: " . urlencode($conn->error));
        }
    } else {
        header("Location: ../Interface/admin.php?message=Produk+tidak+ditemukan");
    }
}

$conn->close();
?>
