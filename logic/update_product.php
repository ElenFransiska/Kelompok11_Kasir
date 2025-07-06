<?php
require_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari form
    $id = (int)$_POST['id_produk'];
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $keterangan = $conn->real_escape_string($_POST['keterangan']);
    $stok = (int)$_POST['stok'];
    $harga = (int)$_POST['harga'];

    $image_path = null;

    // Jika user meng-upload gambar baru
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        //Connection ekstensi file
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        // untuk buat nama file dan disimpan di folder
        $filename = strtolower(str_replace(' ', '_', $nama)) . '.' . $ext;
        $target_path = "../images/$filename";

        // Simpan file ke folder
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = "images/$filename";
        } else {
            header("Location: ../Interface/admin.php?message=Gagal+upload+gambar");
            exit;
        }
    }

    //update data produk
    $setImage = $image_path ? ", image='$image_path'" : "";
    $sql = "UPDATE produk SET 
                kategori='$kategori',
                nama='$nama',
                keterangan='$keterangan',
                stok=$stok,
                harga=$harga
                $setImage
            WHERE id_produk=$id";

    // Eksekusi query
    $message = $conn->query($sql)
        ? "Produk+berhasil+diupdate"
        : "Error:+" . urlencode($conn->error);

    header("Location: ../Interface/admin.php?message=$message");
}