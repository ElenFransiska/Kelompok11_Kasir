<?php
require_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id_produk']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $keterangan = $conn->real_escape_string($_POST['keterangan']);
    $stok = intval($_POST['stok']);
    $harga = intval($_POST['harga']);
    $image_path = null;

    // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Create the images directory if it doesn't exist
        if (!is_dir('../images')) {
            mkdir('../images', 0777, true);
        }

        // Generate a new filename based on the product name
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $sanitized_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nama));
        $filename = $sanitized_name . '.' . $ext;
        $target_path = "../images/" . $filename;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = "images/" . $filename; // Relative path for the database
        } else {
            header("Location: ../Interface/admin.php?message=Gagal+mengupload+gambar");
            exit();
        }
    }

    // Build the SQL query
    if ($image_path) {
        // If a new image was uploaded, include it in the update
        $sql = "UPDATE produk SET 
                kategori='$kategori', 
                nama='$nama', 
                image='$image_path', 
                keterangan='$keterangan', 
                stok=$stok,
                harga=$harga 
                WHERE id_produk=$id";
    } else {
        // If no new image was uploaded, exclude it from the update
        $sql = "UPDATE produk SET 
                kategori='$kategori', 
                nama='$nama', 
                keterangan='$keterangan', 
                stok=$stok,
                harga=$harga 
                WHERE id_produk=$id";
    }

    // Execute the query and handle the result
    if ($conn->query($sql) === TRUE) {
        header("Location: ../Interface/admin.php?message=Produk+berhasil+diupdate");
    } else {
        header("Location: ../Interface/admin.php?message=Error: " . urlencode($conn->error));
    }
}

$conn->close();
?>
