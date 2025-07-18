<?php
require_once '../db_connection.php'; 

// Fetch products from the database
$products = [];
$sql = "CALL GetMenuItems()";
$result = $conn->query($sql);

if ($result === FALSE) {
    die("Error fetching products: " . $conn->error . " SQL: " . $sql);
}

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['deskripsi'] = $row['keterangan'];
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
    <style>
        .back-btn {
            background-color: #f44336; 
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 15px; 
            width: 100%; 
            box-sizing: border-box; 
            text-align: center;
            text-decoration: none; 
            display: inline-block; 
        }

        .back-btn:hover {
            background-color: #d32f2f; 
        }
        .place-order-btn {
            margin-bottom: 10px; 
        }
    </style>
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
                                 data-name="<?php echo htmlspecialchars($item['nama_produk']); ?>">
                                 <img src="../<?= $item['image'] ?>" alt="<?= $item['nama_produk'] ?>">
                                 <h4><?php echo htmlspecialchars($item['nama_produk']); ?></h4>
                                 <p class="description"><?php echo htmlspecialchars($item['deskripsi']); ?></p>
                                 <p class="price">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></p>
                                 <div class="quantity-controls">
                                     <button onclick="updateQuantity(<?php echo $item['id_produk']; ?>, -1)">-</button>
                                     <input type="text" id="qty-<?php echo $item['id_produk']; ?>" value="0" readonly>
                                     <button onclick="updateQuantity(<?php echo $item['id_produk']; ?>, 1)">+</button>
                                 </div>
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
            <a href="home.php" class="back-btn">Kembali</a>
        </div>
    </div>
    <script>
        let cart = {}; 

        function updateQuantity(productId, change) {
            const qtyInput = document.getElementById(`qty-${productId}`);
            const productCard = document.querySelector(`.product-card[data-id="${productId}"]`);

            let currentQty = parseInt(qtyInput.value);
            let newQty = currentQty + change;

            if (newQty < 0) newQty = 0;
            
            qtyInput.value = newQty;

            const productName = productCard.dataset.name;
            const productPrice = parseFloat(productCard.dataset.price);

            if (newQty > 0) {
                cart[productId] = {
                    id: productId, 
                    name: productName,
                    price: productPrice,
                    quantity: newQty
                };
            } else {
                delete cart[productId]; 
            }
            updateCartDisplay();
        }

        function removeItemFromCart(productId) {
            const qtyInput = document.getElementById(`qty-${productId}`);
            if (qtyInput) qtyInput.value = 0; 
            delete cart[productId];
            updateCartDisplay();
        }

        function updateCartDisplay() {
            const cartItemsList = document.getElementById('cart-items');
            cartItemsList.innerHTML = ''; 
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
                if (emptyMessage) { 
                    emptyMessage.style.display = 'block';
                } else { 
                    const newEmptyMessage = document.createElement('li');
                    newEmptyMessage.id = 'empty-cart-message';
                    newEmptyMessage.style.textAlign = 'center';
                    newEmptyMessage.style.color = '#888';
                    newEmptyMessage.textContent = 'Keranjang Anda kosong.';
                    cartItemsList.appendChild(newEmptyMessage);
                }
            } else {
                if (emptyMessage) emptyMessage.style.display = 'none'; 
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
                items: Object.values(cart) 
            };

            try {
                const response = await fetch('process_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });

                const result = await response.json();

                if (result.success) {
                    alert('Pesanan berhasil ditempatkan!');
                    
                    // Bersihkan keranjang dan reset form setelah pesanan berhasil
                    cart = {};
                    document.getElementById('nama_pembeli').value = '';
                    document.getElementById('meja').value = '';
                    
                    document.querySelectorAll('.quantity-controls input').forEach(input => {
                        input.value = 0;
                    });
                    
                    // Pembaruan stok dihilangkan dari tampilan langsung karena trigger DB
                    
                    updateCartDisplay();
                    window.location.href = 'menu_baru.php?status=success'; // Redirect dengan pesan sukses
                } else {
                    alert('Gagal menempatkan pesanan: ' + result.message);
                    window.location.href = 'menu_baru.php?status=error'; // Redirect dengan pesan error
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pesanan.');
                window.location.href = 'menu_baru.php?status=error'; // Redirect dengan pesan error
            }
        }
        document.addEventListener('DOMContentLoaded', updateCartDisplay); // Inisialisasi tampilan keranjang saat dimuat
    </script>
</body>
</html>