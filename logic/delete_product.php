<?php
require_once '../db_connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM produk WHERE id_produk=$id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: ../Interface/admin.php?message=Produk+berhasil+dihapus");
    } else {
        header("Location: ../Interface/admin.php?message=Error: " . urlencode($conn->error));
    }
}

$conn->close();
?>
