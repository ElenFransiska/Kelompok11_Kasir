<?php
header('Content-Type: application/json');

require_once '../db_connection.php'; // Sesuaikan path jika berbeda

$response = ['success' => false, 'message' => 'Terjadi kesalahan tidak dikenal.', 'updated_stocks' => []];

// Mendapatkan data JSON dari request POST
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input. Error: ' . json_last_error_msg()]);
    exit();
}

$namaPembeli = $data['nama_pembeli'] ?? '';
$meja = $data['meja'] ?? '';
$items = $data['items'] ?? [];

if (empty($namaPembeli) || empty($meja) || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Nama Pembeli, Nomor Meja, dan Item Pesanan tidak boleh kosong.']);
    exit();
}

$conn->begin_transaction(); // Mulai transaksi untuk memastikan konsistensi data

try {
    $totalHarga = 0;
    $productsToProcess = []; // Untuk menyimpan detail produk yang akan diproses

    // Loop ini sekarang hanya untuk menghitung total harga dan mengumpulkan data
    // Validasi stok sepenuhnya ditangani oleh TRIGGER database.
    foreach ($items as $item) {
        $productId = $item['id'];
        $quantity = $item['quantity'];

        // Ambil harga produk dari database untuk perhitungan total harga pesanan.
        // Tidak perlu 'FOR UPDATE' di sini karena trigger database akan menangani locking dan validasi stok.
        $stmt_get_price = $conn->prepare("SELECT harga FROM produk WHERE id_produk = ?");
        if ($stmt_get_price === FALSE) {
            throw new Exception("Prepare statement for price fetch failed: " . $conn->error);
        }
        $stmt_get_price->bind_param("i", $productId);
        $stmt_get_price->execute();
        $result_get_price = $stmt_get_price->get_result();
        $productDB = $result_get_price->fetch_assoc();
        $stmt_get_price->close();

        if (!$productDB) {
            throw new Exception("Produk dengan ID " . htmlspecialchars($productId) . " tidak ditemukan.");
        }
        
        $subtotalItem = $productDB['harga'] * $quantity;
        $totalHarga += $subtotalItem;
        
        $productsToProcess[] = [
            'id_produk' => $productId,
            'quantity_ordered' => $quantity,
            'harga_satuan' => $productDB['harga'],
            // 'current_stock' dan 'nama_produk' tidak lagi diperlukan di sini
            // karena validasi stok dan pesan kesalahan spesifik ditangani oleh trigger.
        ];
    }

    // 1. Masukkan data ke tabel `orders`
    $stmt_order = $conn->prepare("INSERT INTO orders (nama_pembeli, meja, total_harga) VALUES (?, ?, ?)");
    if ($stmt_order === FALSE) {
        throw new Exception("Prepare statement for orders failed: " . $conn->error);
    }
    $stmt_order->bind_param("ssd", $namaPembeli, $meja, $totalHarga);
    if (!$stmt_order->execute()) {
        throw new Exception("Execute statement for orders failed: " . $stmt_order->error);
    }
    $orderId = $stmt_order->insert_id; // Dapatkan ID order yang baru saja dimasukkan
    $stmt_order->close();

    // 2. Masukkan setiap item ke tabel `order_items` dan update stok
    // Jika stok tidak cukup, INSERT ini akan gagal karena TRIGGER database.
    $stmt_order_item = $conn->prepare("INSERT INTO order_items (id_order, id_produk, jumlah, harga_satuan) VALUES (?, ?, ?, ?)");
    if ($stmt_order_item === FALSE) {
        throw new Exception("Prepare statement for order_item failed: " . $conn->error);
    }
    
    // Siapkan statement untuk update stok, akan digunakan berulang kali
    $stmt_update_stock = $conn->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");
    if ($stmt_update_stock === FALSE) {
        throw new Exception("Prepare statement for stock update failed: " . $conn->error);
    }

    foreach ($productsToProcess as $product) {
        // Lakukan INSERT ke order_items. Jika trigger membatalkan, error akan tertangkap di catch.
        $stmt_order_item->bind_param("iiid", $orderId, $product['id_produk'], $product['quantity_ordered'], $product['harga_satuan']);
        if (!$stmt_order_item->execute()) {
            // Jika ada error di sini, kemungkinan besar dari trigger database
            throw new Exception("Execute statement for order_item failed: " . $stmt_order_item->error);
        }

        // Kurangi stok di tabel 'produk' HANYA JIKA INSERT ke order_items berhasil (trigger tidak membatalkan)
        $quantityToReduce = $product['quantity_ordered'];
        $productIdToUpdate = $product['id_produk'];

        $stmt_update_stock->bind_param("ii", $quantityToReduce, $productIdToUpdate);
        if (!$stmt_update_stock->execute()) {
            throw new Exception("Execute statement for stock update failed: " . $stmt_update_stock->error);
        }
        
        // Ambil stok terbaru setelah update untuk dikirim ke frontend
        $stmt_get_new_stock = $conn->prepare("SELECT stok FROM produk WHERE id_produk = ?");
        $stmt_get_new_stock->bind_param("i", $product['id_produk']);
        $stmt_get_new_stock->execute();
        $new_stock_result = $stmt_get_new_stock->get_result();
        $new_stock_row = $new_stock_result->fetch_assoc();
        $new_stock_val = $new_stock_row ? $new_stock_row['stok'] : 0;
        $stmt_get_new_stock->close();

        $response['updated_stocks'][] = ['id_produk' => $product['id_produk'], 'new_stock' => $new_stock_val];
    }
    
    $stmt_order_item->close();
    $stmt_update_stock->close();

    $conn->commit(); // Commit transaksi jika semua berhasil
    $response['success'] = true;
    $response['message'] = 'Pesanan berhasil ditempatkan!';
    $response['order_id'] = $orderId;
    echo json_encode($response);

} catch (Exception $e) {
    $conn->rollback(); // Rollback transaksi jika ada kesalahan
    error_log("Order processing error: " . $e->getMessage()); // Log error untuk debugging
    
    // Tangkap pesan kesalahan dari trigger database
    $errorMessage = $e->getMessage();
    // MySQL/MariaDB error messages from SIGNAL SQLSTATE often start with the SQLSTATE code.
    // Example: "SQLSTATE[45000]: Unknown error: Stok produk tidak cukup untuk pesanan ini."
    // We want to extract just the custom message.
    if (strpos($errorMessage, 'SQLSTATE[45000]:') !== false) {
        $errorMessage = substr($errorMessage, strpos($errorMessage, ':') + 2); // Get substring after SQLSTATE code
    }
    
    echo json_encode(['success' => false, 'message' => 'Gagal menempatkan pesanan: ' . $errorMessage]);
} finally {
    $conn->close();
}
?>