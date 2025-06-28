<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kasir_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database
$products = [];
// Perhatikan: Menggunakan 'nama' dan 'keterangan' sesuai struktur tabel Anda
// Hapus 'stok' dan 'image' dari SELECT statement jika memang tidak digunakan/tidak ada
// Jika 'image' ada tapi tidak ingin ditampilkan, biarkan saja di sini
$sql = "SELECT id_produk, nama AS nama_produk, harga, kategori, keterangan AS deskripsi, image FROM produk ORDER BY kategori, nama_produk"; // Hapus 'stok' di sini
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .main-container {
            display: flex;
            gap: 25px;
            max-width: 1200px;
            width: 100%;
        }
        .menu-section {
            flex: 2;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .cart-section {
            flex: 1;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        h1, h2, h3 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
        .category-group {
            margin-bottom: 30px;
        }
        .category-group h3 {
            background-color: #5a7d7c; /* Warna baru */
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.5em;
            text-align: left;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        .product-card {
            background-color: #f9fbfb;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }
        .product-card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }
        .product-card h4 {
            margin: 0 0 8px;
            color: #333;
            font-size: 1.15em;
            font-weight: 600;
        }
        .product-card .description {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 10px;
            height: 40px; /* Fixed height for description */
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        /* Hapus atau sembunyikan .product-card .stock */
        /* .product-card .stock {
            font-size: 0.8em;
            color: #888;
            margin-bottom: 10px;
        } */
        .product-card .price {
            font-weight: bold;
            color: #28a745;
            font-size: 1.2em;
            margin-bottom: 15px;
        }
        .product-card .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .product-card .quantity-controls button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1.1em;
            transition: background-color 0.2s;
        }
        .product-card .quantity-controls button:hover {
            background-color: #0056b3;
        }
        .product-card .quantity-controls input {
            width: 50px;
            text-align: center;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
        /* Hapus atau sembunyikan .product-card .out-of-stock */
        /* .product-card .out-of-stock {
            color: red;
            font-weight: bold;
        } */
        .product-card button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        /* Cart Styles */
        .cart-summary {
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .cart-summary h3 {
            margin-top: 0;
            margin-bottom: 15px;
            text-align: left;
            font-size: 1.3em;
            color: #333;
        }
        #cart-items {
            list-style: none;
            padding: 0;
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 15px;
        }
        #cart-items li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
            font-size: 0.95em;
        }
        #cart-items li:last-child {
            border-bottom: none;
        }
        .cart-item-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .cart-item-name {
            font-weight: 600;
            color: #555;
        }
        .cart-item-qty-price {
            font-size: 0.85em;
            color: #777;
        }
        .cart-item-subtotal {
            font-weight: bold;
            color: #007bff;
        }
        .remove-item-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            cursor: pointer;
            font-size: 0.8em;
            margin-left: 10px;
            transition: background-color 0.2s;
        }
        .remove-item-btn:hover {
            background-color: #c82333;
        }
        .total-price {
            font-size: 1.5em;
            font-weight: bold;
            text-align: right;
            margin-top: 15px;
            color: #28a745;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .order-details-form {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .order-details-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .order-details-form input {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 1em;
        }
        .place-order-btn {
            background-color: #28a745;
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2em;
            width: 100%;
            transition: background-color 0.2s;
        }
        .place-order-btn:hover {
            background-color: #218838;
        }
        .message {
            text-align: center;
            padding: 12px;
            margin-top: 20px;
            border-radius: 8px;
            font-weight: 600;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
                                 <img src="<?php echo htmlspecialchars($item['image'] ?? 'https://via.placeholder.com/120?text=No+Image'); ?>" alt="<?php echo htmlspecialchars($item['nama_produk']); ?>">
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
        </div>
    </div>

    <script>
        let cart = {}; // Stores {productId: {id, name, price, quantity}}

        // Fungsi untuk mengupdate kuantitas produk di keranjang
        function updateQuantity(productId, change) {
            const qtyInput = document.getElementById(`qty-${productId}`);
            const productCard = document.querySelector(`.product-card[data-id="${productId}"]`);

            let currentQty = parseInt(qtyInput.value);
            let newQty = currentQty + change;

            if (newQty < 0) newQty = 0; // Kuantitas tidak boleh negatif
            
            qtyInput.value = newQty;

            const productName = productCard.dataset.name;
            const productPrice = parseFloat(productCard.dataset.price);

            if (newQty > 0) {
                cart[productId] = {
                    id: productId, // Sertakan ID produk untuk dikirim ke server
                    name: productName,
                    price: productPrice,
                    quantity: newQty
                    // Hapus 'stock' dari objek cart
                };
            } else {
                delete cart[productId]; // Hapus item jika kuantitas 0
            }
            updateCartDisplay();
        }

        // Fungsi untuk menghapus item dari keranjang secara langsung
        function removeItemFromCart(productId) {
            const qtyInput = document.getElementById(`qty-${productId}`);
            if (qtyInput) qtyInput.value = 0; // Reset kuantitas pada kartu produk
            delete cart[productId];
            updateCartDisplay();
        }

        // Fungsi untuk memperbarui tampilan keranjang dan total harga
        function updateCartDisplay() {
            const cartItemsList = document.getElementById('cart-items');
            cartItemsList.innerHTML = ''; // Hapus tampilan keranjang saat ini
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
                    emptyMessage.style.display = 'block'; // Tampilkan pesan kosong jika sudah ada
                } else {
                    const newEmptyMessage = document.createElement('li'); // Buat jika belum ada
                    newEmptyMessage.id = 'empty-cart-message';
                    newEmptyMessage.style.textAlign = 'center';
                    newEmptyMessage.style.color = '#888';
                    newEmptyMessage.textContent = 'Keranjang Anda kosong.';
                    cartItemsList.appendChild(newEmptyMessage);
                }
            } else {
                if (emptyMessage) emptyMessage.style.display = 'none'; // Sembunyikan pesan kosong jika ada item
            }
            
            document.getElementById('cart-total').textContent = `Rp ${numberFormat(total)}`;
        }

        // Fungsi untuk format angka ke format Rupiah
        function numberFormat(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Fungsi untuk menempatkan pesanan
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
                items: Object.values(cart) // Ubah objek cart menjadi array item untuk dikirim
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
                    // Kosongkan keranjang dan reset form setelah pesanan berhasil
                    cart = {};
                    document.getElementById('nama_pembeli').value = '';
                    document.getElementById('meja').value = '';
                    
                    // Reset semua input kuantitas pada kartu produk
                    document.querySelectorAll('.quantity-controls input').forEach(input => {
                        input.value = 0;
                    });
                    
                    // Tidak ada lagi logika update_stocks di frontend karena stok tidak ditampilkan/diurus di sini

                    updateCartDisplay(); // Perbarui tampilan keranjang
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
        document.addEventListener('DOMContentLoaded', updateCartDisplay); // Inisialisasi tampilan keranjang saat load
    </script>
</body>
</html>