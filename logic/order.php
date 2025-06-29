<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/css_order.css"> 
</head>
<body>
    <header class="navbar">
        <div class="navbar-brand">
            <span class="logo-placeholder">E/R</span> 
            System Kasir
        </div>
        <nav class="navbar-nav">
            <a href="home.php" class="nav-link">Home</a>
            <a href="menu_page.html" class="nav-link active">Menu</a> 
            <a href="pesan.php" class="nav-link">Pesan Sekarang</a>
        </nav>
    </header>

    <div class="main-content">
        <h1 class="menu-page-title">Daftar Menu Kami</h1>
        <p class="menu-page-description">Jelajahi berbagai pilihan makanan dan minuman lezat yang kami tawarkan. Setiap hidangan dibuat dengan bahan-bahan segar pilihan untuk memastikan cita rasa terbaik.</p>

        <div id="message-area"></div>

        <div id="menu-container">
            <p style="text-align: center; padding: 20px;">Memuat daftar menu...</p>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; <span id="current-year"></span> [Nama Cafe/Restoran Anda]. Semua Hak Dilindungi.</p>
        <p>Powered by Program Kasir.</p>
    </footer>

    <script>
        // Fungsi helper untuk menghindari XSS saat menampilkan data dari database
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Fungsi untuk memformat harga ke format Rupiah
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Fungsi untuk memuat data menu dari API dan menampilkannya
        async function loadMenuItems() {
            const menuContainer = document.getElementById('menu-container');
            const messageArea = document.getElementById('message-area');
            menuContainer.innerHTML = '<p style="text-align: center; padding: 20px;">Memuat daftar menu...</p>';
            messageArea.innerHTML = ''; // Bersihkan pesan sebelumnya

            try {
                const response = await fetch('api/menu_items.php'); // Sesuaikan path ke API Anda
                const data = await response.json();

                if (data.success) {
                    if (data.menu_items.length > 0) {
                        let categorized_menu = {};
                        data.menu_items.forEach(item => {
                            if (!categorized_menu[item.kategori]) {
                                categorized_menu[item.kategori] = [];
                            }
                            categorized_menu[item.kategori].push(item);
                        });

                        let menuHtml = '';
                        for (const category in categorized_menu) {
                            menuHtml += `
                                <section class="menu-category-section">
                                    <h3>${escapeHtml(category)}</h3>
                                    <div class="menu-grid">
                            `;
                            categorized_menu[category].forEach(menu_item => {
                                const imageUrl = menu_item.image ? `../${escapeHtml(menu_item.image)}` : 'https://via.placeholder.com/150?text=No+Image';
                                menuHtml += `
                                    <div class="menu-item-card">
                                        <img src="${imageUrl}" alt="${escapeHtml(menu_item.nama)}">
                                        <div class="card-content">
                                            <div>
                                                <h4>${escapeHtml(menu_item.nama)}</h4>
                                                <p class="description">${escapeHtml(menu_item.keterangan)}</p>
                                                <p class="price">${formatRupiah(menu_item.harga)}</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            menuHtml += `</div></section>`;
                        }
                        menuContainer.innerHTML = menuHtml;
                    } else {
                        // Jika tidak ada item menu
                        messageArea.innerHTML = `
                            <div class="message-box success">
                                <i class="fas fa-info-circle"></i>
                                ${escapeHtml(data.message)}
                            </div>
                        `;
                        menuContainer.innerHTML = ''; // Kosongkan area menu
                    }
                } else {
                    // Jika ada error dari API
                    messageArea.innerHTML = `
                        <div class="message-box error">
                            <i class="fas fa-exclamation-circle"></i>
                            ${escapeHtml(data.message)}
                        </div>
                    `;
                    menuContainer.innerHTML = ''; // Kosongkan area menu
                }
            } catch (error) {
                console.error('Error fetching menu items:', error);
                messageArea.innerHTML = `
                    <div class="message-box error">
                        <i class="fas fa-exclamation-circle"></i>
                        Terjadi kesalahan saat memuat daftar menu. Mohon coba lagi nanti.
                    </div>
                `;
                menuContainer.innerHTML = ''; // Kosongkan area menu
            }
        }

        // Set tahun saat ini di footer
        document.getElementById('current-year').textContent = new Date().getFullYear();

        // Panggil fungsi untuk memuat menu saat DOM selesai dimuat
        document.addEventListener('DOMContentLoaded', loadMenuItems);
    </script>
</body>
</html>