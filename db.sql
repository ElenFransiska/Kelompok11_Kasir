-- Membuat tabel produk
CREATE TABLE produk (
    id_produk INT PRIMARY KEY AUTO_INCREMENT,
    kategori VARCHAR(50),
    nama VARCHAR(100),
    image VARCHAR(255),
    keterangan TEXT,
    stok INT
);

-- Menambahkan dummy data untuk kategori makanan
INSERT INTO produk (kategori, nama, image, keterangan, stok) VALUES
('Makanan', 'Nasi Goreng', 'nasi_goreng.jpg', 'Nasi goreng spesial dengan telur dan ayam.', 10),
('Makanan', 'Sate Ayam', 'sate_ayam.jpg', 'Sate ayam dengan bumbu kacang.', 15),
('Makanan', 'Rendang', 'rendang.jpg', 'Daging sapi yang dimasak dengan rempah-rempah.', 8),
('Makanan', 'Mie Goreng', 'mie_goreng.jpg', 'Mie goreng dengan sayuran dan telur.', 12),
('Makanan', 'Bakso', 'bakso.jpg', 'Bakso daging sapi dengan kuah kaldu.', 20);

-- Menambahkan dummy data untuk kategori minuman
INSERT INTO produk (kategori, nama, image, keterangan, stok) VALUES
('Minuman', 'Es Teh Manis', 'es_teh_manis.jpg', 'Teh manis yang disajikan dengan es.', 25),
('Minuman', 'Jus Jeruk', 'jus_jeruk.jpg', 'Jus jeruk segar tanpa tambahan gula.', 30),
('Minuman', 'Kopi Hitam', 'kopi_hitam.jpg', 'Kopi hitam yang disajikan panas.', 18),
('Minuman', 'Soda', 'soda.jpg', 'Minuman bersoda dengan rasa buah.', 22),
('Minuman', 'Air Mineral', 'air_mineral.jpg', 'Air mineral segar dan sehat.', 50);

