<?php
require_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id_produk']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $image = $conn->real_escape_string($_POST['image']);
    $keterangan = $conn->real_escape_string($_POST['keterangan']);
    $stok = intval($_POST['stok']);
    $harga = intval($_POST['harga']);

    $sql = "UPDATE produk SET 
            kategori='$kategori', 
            nama='$nama', 
            image='$image', 
            keterangan='$keterangan', 
            stok=$stok 
            harga=$harga 
            WHERE id_produk=$id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: ../Interface/admin.php?message=Produk+berhasil+diupdate");
    } else {
        header("Location: ../Interface/admin.php?message=Error: " . urlencode($conn->error));
    }
}

$conn->close();
?>
