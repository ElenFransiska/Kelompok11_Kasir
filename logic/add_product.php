<?php
require_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $keterangan = $conn->real_escape_string($_POST['keterangan']);
    $stok = intval($_POST['stok']);

    // Proses upload gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Buat folder images jika belum ada
        if (!is_dir('../images')) {
            mkdir('../images', 0777, true);
        }

        // Generate nama file unik
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $target_path = "../images/" . $filename;

        // Simpan file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            // Simpan ke database
            $image_path = "images/" . $filename; // Path relatif untuk disimpan di database
            $sql = "INSERT INTO produk (kategori, nama, image, keterangan, stok) 
                    VALUES ('$kategori', '$nama', '$image_path', '$keterangan', $stok)";
            
            if ($conn->query($sql) === TRUE) {
                header("Location: ../Interface/admin.php?message=Produk+berhasil+ditambahkan");
            } else {
                header("Location: ../Interface/admin.php?message=Error: " . urlencode($conn->error));
            }
        } else {
            header("Location: ../Interface/admin.php?message=Gagal+mengupload+gambar");
        }
    } else {
        header("Location: ../Interface/admin.php?message=Gambar+tidak+valid");
    }
}

$conn->close();
?>
