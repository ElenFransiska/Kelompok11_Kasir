<?php
header('Content-Type: application/json');

require_once '../db_connection.php';

// Get the raw POST data
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

$conn->begin_transaction(); // Start transaction

try {
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

    foreach ($items as $item) {
        $productId = $item['id'];
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

    $conn->commit(); // Commit transaction if all successful
    echo json_encode(['success' => true, 'message' => 'Pesanan berhasil ditempatkan!', 'order_id' => $orderId]);

} catch (Exception $e) {
    $conn->rollback(); // Rollback transaction on error
    error_log("Order processing error: " . $e->getMessage()); // Log the error for debugging
    echo json_encode(['success' => false, 'message' => 'Gagal menempatkan pesanan: ' . $e->getMessage()]);
}

$conn->close();
?>
