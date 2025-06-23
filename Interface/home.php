<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di [Nama Cafe/Restoran Anda]</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/css_home.css">
</head>
<body>
    <header class="navbar">
        <div class="navbar-brand">
            System Kasir
        </div>
        <nav class="navbar-nav">
            <a href="index.php" class="nav-link active">Home</a>
            <a href="order.php" class="nav-link">Menu</a>
            <a href="pesan.php" class="nav-link">Pesan Sekarang</a>
            <a href="contact.php" class="nav-link">Hubungi Kami</a>
        </nav>
    </header>

    <div class="main-content home-customer-page">
        <section class="hero-section text-center">
            <h1>Welcome di System Kasir Raymond & Elen</h1>
            <p>Nikmati berbagai pilihan hidangan dan minuman spesial kami. Pesan dengan mudah dan cepat!</p>
        </section>

        <section class="info-cards-section">
            <div class="info-cards-grid">
                <div class="info-card">
                    <i class="fas fa-utensils info-icon"></i>
                    <h3>Pesan Makanan</h3>
                    <p>Lihat menu lengkap kami dan pesan hidangan favorit Anda dengan beberapa klik.</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-clock info-icon"></i>
                    <h3>Layanan Cepat</h3>
                    <p>Kami melayani Anda dengan sigap untuk pengalaman makan yang menyenangkan.</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-concierge-bell info-icon"></i>
                    <h3>Bantuan Pelanggan</h3>
                    <p>Tim kami siap membantu Anda dengan pertanyaan atau kebutuhan khusus.</p>
                </div>
            </div>
        </section>

        <section class="action-buttons-section text-center">
            <a href="pesan_makanan.php" class="btn btn-primary large-btn">
                <i class="fas fa-shopping-basket"></i> Pesan Sekarang
            </a>
            <a href="contact.php" class="btn btn-outline-secondary large-btn">
                <i class="fas fa-headset"></i> Hubungi Kami
            </a>
            </section>  
    </div>

    <footer class="footer">
        <p>© <?php echo date("Y"); ?> RaymondElen. Semua Hak Dilindungi.</p>
    </footer>
</body>
</html>