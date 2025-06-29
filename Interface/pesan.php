<?php
require_once '../db_connection.php'; // Sesuaikan path jika berbeda

$products = [];
$sql = "SELECT id_produk, nama AS nama_produk, harga, image, kategori, keterangan AS deskripsi, stok FROM produk ORDER BY kategori, nama_produk";
$result = $conn->query($sql);

if ($result === FALSE) {
    die("Error fetching products: " . $conn->error . " SQL: " . $sql);
}

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Makanan & Minuman | Kasir Anda</title>
    <link rel="stylesheet" href="../css/css_pesan.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
</head>
<body>
    <div class="main-container">
        <div class="menu-section">
            <h1>Menu Pesanan</h1>

            <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] == 'success'): ?>
                    <div class="message success">Pesanan Anda berhasil ditempatkan!</div>
                <?php elseif ($_GET['status'] == 'error'): ?>
                    <div class="message error">Terjadi kesalahan saat menempatkan pesanan Anda. Silakan coba lagi.</div>
                <?php endif; ?>
            <?php endif; ?>

            <?php
            $categorized_products = [];
            foreach ($products as $product) {
                $categorized_products[$product['kategori']][] = $product;
            }

            foreach ($categorized_products as $category => $items):
            ?>
                <div class="category-group">
                    <h3><?php echo htmlspecialchars($category); ?></h3>
                    <div class="product-grid">
                        <?php foreach ($items as $item): ?>
                            <div class="product-card"
                                 data-id="<?php echo $item['id_produk']; ?>"
                                 data-price="<?php echo $item['harga']; ?>"
                                 data-name="<?php echo htmlspecialchars($item['nama_produk']); ?>"
                                 data-stock="<?php echo $item['stok']; ?>"> <img src="../<?= htmlspecialchars($item['image'] ?? 'path/to/default/image.jpg'); ?>" alt="<?= htmlspecialchars($item['nama_produk']); ?>">
                                 <h4><?php echo htmlspecialchars($item['nama_produk']); ?></h4>
                                <p class="description"><?php echo htmlspecialchars($item['deskripsi']); ?></p>
                                <p class="stock">Stok: <span id="stock-<?php echo $item['id_produk']; ?>"><?php echo $item['stok']; ?></span></p>
                                <p class="price">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></p>
                                <div class="quantity-controls">
                                    <button onclick="updateQuantity(<?php echo $item['id_produk']; ?>, -1)">-</button>
                                    <input type="text" id="qty-<?php echo $item['id_produk']; ?>" value="0" readonly>
                                    <button onclick="updateQuantity(<?php echo $item['id_produk']; ?>, 1)"
                                            <?php echo ($item['stok'] <= 0) ? 'disabled' : ''; ?>>+</button>
                                </div>
                                <?php if ($item['stok'] <= 0): ?>
                                    <p class="out-of-stock">Stok Habis</p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-section">
            <h2>Ringkasan Pesanan</h2>
            <div class="cart-summary">
                <h3>Isi Keranjang Anda</h3>
                <ul id="cart-items">
                    <li id="empty-cart-message" style="text-align: center; color: #888;">Keranjang Anda kosong.</li>
                </ul>
                <div class="total-price">Total: <span id="cart-total">Rp 0</span></div>
            </div>

            <div class="order-details-form">
                <label for="nama_pembeli">Nama Pembeli:</label>
                <input type="text" id="nama_pembeli" name="nama_pembeli" required>

                <label for="meja">Nomor Meja:</label>
                <input type="text" id="meja" name="meja" required>
            </div>

            <button class="place-order-btn" onclick="placeOrder()">Pesan Sekarang</button>
        </div>
    </div>

    <script>
        let cart = {}; // Stores {productId: {name, price, quantity, stock}}

        function updateQuantity(productId, change) {
            const qtyInput = document.getElementById(`qty-${productId}`);
            const stockSpan = document.getElementById(`stock-${productId}`);
            const productCard = document.querySelector(`.product-card[data-id="${productId}"]`);
            // Get current stock from the displayed stock, not original data attribute,
            // as stock might have been updated by a prior successful order in the same session.
            // This ensures client-side validation uses the most recent stock shown.
            const currentAvailableStock = parseInt(stockSpan.textContent); 

            let currentQty = parseInt(qtyInput.value);
            let newQty = currentQty + change;

            // Ensure quantity doesn't go below 0
            if (newQty < 0) newQty = 0;

            // Prevent adding more than available stock
            if (change > 0 && newQty > currentAvailableStock) {
                alert('Jumlah pesanan melebihi stok yang tersedia (' + currentAvailableStock + ').');
                newQty = currentAvailableStock; // Cap at max stock
            }
            
            qtyInput.value = newQty;

            const productName = productCard.dataset.name;
            const productPrice = parseFloat(productCard.dataset.price);

            if (newQty > 0) {
                cart[productId] = {
                    id: productId, // Add id for sending to server
                    name: productName,
                    price: productPrice,
                    quantity: newQty,
                    // We don't need to pass 'stock' from here to process_order.php,
                    // as process_order.php will fetch the latest stock from DB.
                    // stock: currentAvailableStock 
                };
            } else {
                delete cart[productId]; // Remove item if quantity is 0
            }
            updateCartDisplay();
        }

        function removeItemFromCart(productId) {
            const qtyInput = document.getElementById(`qty-${productId}`);
            if (qtyInput) qtyInput.value = 0; // Reset quantity on product card
            delete cart[productId];
            updateCartDisplay();
        }

        function updateCartDisplay() {
            const cartItemsList = document.getElementById('cart-items');
            cartItemsList.innerHTML = ''; // Clear current cart display
            let total = 0;
            let hasItems = false;

            for (const productId in cart) {
                const item = cart[productId];
                const listItem = document.createElement('li');
                const itemSubtotal = item.price * item.quantity;
                listItem.innerHTML = `
                    <div class="cart-item-info">
                        <span class="cart-item-name">${item.name}</span>
                        <span class="cart-item-qty-price">${item.quantity} x Rp ${numberFormat(item.price)}</span>
                    </div>
                    <span class="cart-item-subtotal">Rp ${numberFormat(itemSubtotal)}</span>
                    <button class="remove-item-btn" onclick="removeItemFromCart(${productId})">Hapus</button>
                `;
                cartItemsList.appendChild(listItem);
                total += itemSubtotal;
                hasItems = true;
            }

            const emptyMessage = document.getElementById('empty-cart-message');
            if (!hasItems) {
                if (emptyMessage) { // If message already exists, ensure it's displayed
                    emptyMessage.style.display = 'block';
                } else { // Create and append if not exists
                    const newEmptyMessage = document.createElement('li');
                    newEmptyMessage.id = 'empty-cart-message';
                    newEmptyMessage.style.textAlign = 'center';
                    newEmptyMessage.style.color = '#888';
                    newEmptyMessage.textContent = 'Keranjang Anda kosong.';
                    cartItemsList.appendChild(newEmptyMessage);
                }
            } else {
                if (emptyMessage) emptyMessage.style.display = 'none'; // Hide if items exist
            }
            
            document.getElementById('cart-total').textContent = `Rp ${numberFormat(total)}`;
        }

        function numberFormat(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        async function placeOrder() {
            const namaPembeli = document.getElementById('nama_pembeli').value.trim();
            const meja = document.getElementById('meja').value.trim();

            if (Object.keys(cart).length === 0) {
                alert('Keranjang pesanan Anda kosong.');
                return;
            }

            if (!namaPembeli || !meja) {
                alert('Nama Pembeli dan Nomor Meja harus diisi.');
                return;
            }

            const orderData = {
                nama_pembeli: namaPembeli,
                meja: meja,
                items: Object.values(cart) // Convert cart object to an array of items
            };

            try {
                const response = await fetch('process_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });

                // Check if the response is OK (status 200) and if it's JSON
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server responded with an error:', response.status, errorText);
                    alert('Terjadi kesalahan pada server saat memproses pesanan. Silakan cek konsol browser atau log server.');
                    window.location.href = 'menu_baru.php?status=error';
                    return;
                }

                const result = await response.json();

                if (result.success) {
                    alert('Pesanan berhasil ditempatkan!');
                    // Clear cart and reset form after successful order
                    cart = {};
                    document.getElementById('nama_pembeli').value = '';
                    document.getElementById('meja').value = '';
                    
                    // Reset all quantity inputs on product cards to 0
                    document.querySelectorAll('.quantity-controls input').forEach(input => {
                        input.value = 0;
                    });
                    
                    // Update stock display on the page and disable buttons if stock becomes 0
                    if (result.updated_stocks) {
                        result.updated_stocks.forEach(item => {
                            const stockSpan = document.getElementById(`stock-${item.id_produk}`);
                            if (stockSpan) {
                                stockSpan.textContent = item.new_stock;
                                const productCard = document.querySelector(`.product-card[data-id="${item.id_produk}"]`);
                                if (item.new_stock <= 0) {
                                    // Disable '+' button
                                    const addButton = productCard.querySelector('.quantity-controls button:last-child');
                                    if (addButton) addButton.disabled = true;
                                    // Show "Stok Habis" message
                                    let outOfStockMessage = productCard.querySelector('.out-of-stock');
                                    if (!outOfStockMessage) {
                                        outOfStockMessage = document.createElement('p');
                                        outOfStockMessage.className = 'out-of-stock';
                                        productCard.appendChild(outOfStockMessage);
                                    }
                                    outOfStockMessage.textContent = 'Stok Habis';
                                    outOfStockMessage.style.display = 'block';
                                } else {
                                    // Ensure '+' button is enabled if stock > 0
                                    const addButton = productCard.querySelector('.quantity-controls button:last-child');
                                    if (addButton) addButton.disabled = false;
                                    // Hide "Stok Habis" message
                                    const outOfStockMessage = productCard.querySelector('.out-of-stock');
                                    if (outOfStockMessage) outOfStockMessage.style.display = 'none';
                                }
                            }
                        });
                    }

                    updateCartDisplay();
                    // Redirect is good for showing the status message (success/error) once.
                    // window.location.href = 'menu_baru.php?status=success'; 
                    // Consider if you want to refresh the page or just update UI dynamically.
                    // A full page reload might clear unsaved changes if there are any.
                    // For now, keeping the reload as it was.
                    window.location.href = 'menu_baru.php?status=success';
                } else {
                    alert('Gagal menempatkan pesanan: ' + result.message);
                    window.location.href = 'menu_baru.php?status=error';
                }
            } catch (error) {
                console.error('Error in fetch:', error);
                alert('Terjadi kesalahan jaringan atau respons tidak valid.');
                window.location.href = 'menu_baru.php?status=error';
            }
        }
        document.addEventListener('DOMContentLoaded', updateCartDisplay); // Initialize cart display on load
    </script>
</body>
</html>