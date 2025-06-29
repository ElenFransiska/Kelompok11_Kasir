<?php
require_once '../db_connection.php'; // Sesuaikan path jika berbeda

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Terjadi kesalahan tidak dikenal.', 'updated_stocks' => []];

// Mendapatkan data JSON dari request POST
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    $response['message'] = 'Invalid JSON input: ' . json_last_error_msg();
    echo json_encode($response);
    exit();
}

$namaPembeli = $data['nama_pembeli'] ?? '';
$nomorMeja = $data['meja'] ?? '';
$items = $data['items'] ?? [];

if (empty($namaPembeli) || empty($nomorMeja) || empty($items)) {
    $response['message'] = 'Nama pembeli, nomor meja, dan item pesanan tidak boleh kosong.';
    echo json_encode($response);
    exit();
}

$conn->begin_transaction(); // Mulai transaksi untuk memastikan konsistensi data

try {
<<<<<<< HEAD
    $totalHargaPesanan = 0;
    $productsToUpdate = [];
=======
    $totalHarga = 0;
    foreach ($items as $item) {
        $totalHarga += $item['price'] * $item['quantity'];
    }

    // 1. Insert into 'orders' table
    $stmt_order = $conn->prepare("INSERT INTO orders (nama_pembeli, meja, total_harga) VALUES (?, ?, ?)");
    if ($stmt_order === FALSE) {
        throw new Exception("Prepare statement for orders failed: " . $conn->error);
    }
    $stmt_order->bind_param("ssd", $namaPembeli, $meja, $totalHarga);
    if (!$stmt_order->execute()) {
        throw new Exception("Execute statement for orders failed: " . $stmt_order->error);
    }
    $orderId = $stmt_order->insert_id; // Get the last inserted order ID
    $stmt_order->close();

    // 2. Insert into 'order_items' table for each item and its quantity
    $stmt_order_item = $conn->prepare("INSERT INTO order_items (id_order, id_produk, harga_satuan) VALUES (?, ?, ?)");
    if ($stmt_order_item === FALSE) {
        throw new Exception("Prepare statement for order_item failed: " . $conn->error);
    }
>>>>>>> 88937e32e22482fa05372350e983e8d0a0b166ae

    // Validasi stok dan hitung total harga
    foreach ($items as $item) {
        $productId = $item['id'];
<<<<<<< HEAD
        $quantity = $item['quantity'];

        // Ambil stok terbaru dari database (penting untuk menghindari masalah race condition)
        $stmt = $conn->prepare("SELECT stok, harga FROM produk WHERE id_produk = ? FOR UPDATE"); // FOR UPDATE untuk locking
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $productDB = $result->fetch_assoc();
        $stmt->close();
=======
        $itemPrice = $item['price'];
        $quantity = $item['quantity'];

        // Loop for each quantity
        for ($i = 0; $i < $quantity; $i++) {
            $stmt_order_item->bind_param("iid", $orderId, $productId, $itemPrice);
            if (!$stmt_order_item->execute()) {
                throw new Exception("Execute statement for order_item failed: " . $stmt_order_item->error);
            }
        }

        // 3. Reduce stock in 'produk' table
        $stmt_update_stock = $conn->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");
        if ($stmt_update_stock === FALSE) {
            throw new Exception("Prepare statement for stock update failed: " . $conn->error);
        }
        $stmt_update_stock->bind_param("ii", $quantity, $productId);
        if (!$stmt_update_stock->execute()) {
            throw new Exception("Execute statement for stock update failed: " . $stmt_update_stock->error);
        }
        $stmt_update_stock->close();
    }
    $stmt_order_item->close();
>>>>>>> 88937e32e22482fa05372350e983e8d0a0b166ae

        if (!$productDB || $productDB['stok'] < $quantity) {
            throw new Exception("Stok untuk produk " . htmlspecialchars($item['name']) . " tidak cukup. Stok tersedia: " . ($productDB['stok'] ?? 0));
        }

        $subtotalItem = $productDB['harga'] * $quantity;
        $totalHargaPesanan += $subtotalItem;
        
        $productsToUpdate[] = [
            'id_produk' => $productId,
            'quantity_ordered' => $quantity,
            'harga_satuan' => $productDB['harga'],
            'subtotal' => $subtotalItem, // Keep this calculated subtotal for main order total
            'current_stock' => $productDB['stok']
        ];
    }

    // 1. Masukkan data ke tabel `orders` (tadi Anda menggunakan 'pesanan' di komentar, tapi di query 'orders')
    $stmt = $conn->prepare("INSERT INTO orders (nama_pembeli, meja, total_harga) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    $stmt->bind_param("ssd", $namaPembeli, $nomorMeja, $totalHargaPesanan);
    if (!$stmt->execute()) {
        throw new Exception("Error inserting into orders: " . $stmt->error);
    }
    $idPesanan = $conn->insert_id;
    $stmt->close();

    // 2. Masukkan setiap item ke tabel `order_items` dan update stok
    foreach ($productsToUpdate as $product) {
        // Insert ke order_items
        // Based on image_e0c489.png, your table is `order_items` and has `id_order`, `id_produk`, `jumlah`, `harga_satuan`.
        // It does NOT have a `subtotal` column.
        $stmt = $conn->prepare("INSERT INTO order_items (id_order, id_produk, jumlah, harga_satuan) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        // Bind parameters: 'iiid' for (id_order, id_produk, jumlah, harga_satuan)
        // 'i' for id_order (integer)
        // 'i' for id_produk (integer)
        // 'i' for jumlah (integer)
        // 'd' for harga_satuan (decimal/double)
        $stmt->bind_param("iiid", $idPesanan, $product['id_produk'], $product['quantity_ordered'], $product['harga_satuan']);
        
        if (!$stmt->execute()) {
            throw new Exception("Error inserting into order_items: " . $stmt->error);
        }
        $stmt->close();

        // Update stok di tabel produk
        $newStock = $product['current_stock'] - $product['quantity_ordered'];
        $stmt = $conn->prepare("UPDATE produk SET stok = ? WHERE id_produk = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("ii", $newStock, $product['id_produk']);
        if (!$stmt->execute()) {
            throw new Exception("Error updating product stock: " . $stmt->error);
        }
        $stmt->close();

        $response['updated_stocks'][] = ['id_produk' => $product['id_produk'], 'new_stock' => $newStock];
    }

    $conn->commit(); // Commit transaksi jika semua berhasil
    $response['success'] = true;
    $response['message'] = 'Pesanan berhasil ditempatkan!';

} catch (Exception $e) {
    $conn->rollback(); // Rollback transaksi jika ada kesalahan
    $response['message'] = $e->getMessage();
    error_log("Order processing error: " . $e->getMessage()); // Log error untuk debugging
} finally {
    $conn->close();
    echo json_encode($response);
}
<<<<<<< HEAD
?>
=======

$conn->close();
?>
>>>>>>> 88937e32e22482fa05372350e983e8d0a0b166ae
