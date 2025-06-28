<?php
// Pastikan tidak ada karakter atau spasi sebelum tag <?php
header('Content-Type: application/json');

// Database connection details
$servername = "localhost";    // ganti dengan host database Anda
$username = "root";           // ganti dengan username database Anda
$password = "";               // ganti dengan password database Anda
$dbname = "kasir_db";         // ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
    exit();
}

$nama_pembeli = $conn->real_escape_string($input['nama_pembeli'] ?? '');
$meja = $conn->real_escape_string($input['meja'] ?? '');
$items = $input['items'] ?? [];

if (empty($nama_pembeli) || empty($meja) || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Nama Pembeli, Nomor Meja, dan Item pesanan tidak boleh kosong.']);
    exit();
}

$conn->begin_transaction(); // Start transaction

try {
    $total_harga = 0;
    $updated_stocks = []; // Array to hold updated stock info for client-side update

    // Validate stock availability and calculate total price first
    foreach ($items as $item) {
        $product_id = (int)$item['id'];
        $quantity = (int)$item['quantity'];

        // Get current stock
        $stmt_stock = $conn->prepare("SELECT stok, harga FROM produk WHERE id_produk = ?");
        $stmt_stock->bind_param("i", $product_id);
        $stmt_stock->execute();
        $res_stock = $stmt_stock->get_result();
        $product_info = $res_stock->fetch_assoc();
        $stmt_stock->close();

        if (!$product_info || $product_info['stok'] < $quantity) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Stok tidak cukup untuk ' . htmlspecialchars($item['name']) . '. Stok tersedia: ' . ($product_info['stok'] ?? 0)]);
            exit();
        }
        $total_harga += ($product_info['harga'] * $quantity);
    }


    // 1. Insert into 'orders' table
    $stmt_order = $conn->prepare("INSERT INTO orders (nama_pembeli, meja, total_harga, created_at) VALUES (?, ?, ?, NOW())");
    $stmt_order->bind_param("ssd", $nama_pembeli, $meja, $total_harga);
    $stmt_order->execute();

    $order_id = $stmt_order->insert_id; // Get the ID of the newly inserted order
    $stmt_order->close();

    // 2. Insert into 'order_items' table for each product in the cart
    $stmt_order_item = $conn->prepare("INSERT INTO order_items (id_order, id_produk, quantity, subtotal) VALUES (?, ?, ?, ?)");
    
    // 3. Update 'produk' table (reduce stock)
    $stmt_update_stock = $conn->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");

    foreach ($items as $item) {
        $product_id = (int)$item['id'];
        $quantity = (int)$item['quantity'];
        $subtotal_item = $item['price'] * $item['quantity']; // Use price sent from client (already validated)

        // Insert into order_items
        $stmt_order_item->bind_param("iiid", $order_id, $product_id, $quantity, $subtotal_item);
        $stmt_order_item->execute();

        // Update stock
        $stmt_update_stock->bind_param("ii", $quantity, $product_id);
        $stmt_update_stock->execute();

        // Get new stock value to send back to client
        $stmt_get_new_stock = $conn->prepare("SELECT stok FROM produk WHERE id_produk = ?");
        $stmt_get_new_stock->bind_param("i", $product_id);
        $stmt_get_new_stock->execute();
        $new_stock_result = $stmt_get_new_stock->get_result()->fetch_assoc();
        $updated_stocks[] = ['id_produk' => $product_id, 'new_stock' => $new_stock_result['stok']];
        $stmt_get_new_stock->close();
    }
    $stmt_order_item->close();
    $stmt_update_stock->close();

    $conn->commit(); // Commit the transaction
    echo json_encode(['success' => true, 'message' => 'Order placed successfully!', 'order_id' => $order_id, 'updated_stocks' => $updated_stocks]);

} catch (Exception $e) {
    $conn->rollback(); // Rollback on error
    error_log("Order processing error: " . $e->getMessage()); // Log the error for debugging
    echo json_encode(['success' => false, 'message' => 'Failed to place order: ' . $e->getMessage()]);
}

$conn->close();
?>