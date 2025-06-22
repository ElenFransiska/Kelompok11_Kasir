<?php
require_once '../db_connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // First get the image path before deleting
    $sql = "SELECT image FROM produk WHERE id_produk = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_path = '../' . $row['image']; // Add ../ to get full path
        
        // Delete the product from database
        $delete_sql = "DELETE FROM produk WHERE id_produk = $id";
        if ($conn->query($delete_sql) === TRUE) {
            // If database deletion successful, delete the image file
            if (file_exists($image_path)) {
                unlink($image_path); // Delete the image file
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
