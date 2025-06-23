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

DELIMITER //

CREATE TRIGGER check_duplicate_nama
BEFORE INSERT ON produk
FOR EACH ROW
BEGIN
    DECLARE duplicate_count INT;

    -- Check for existing product with the same name
    SELECT COUNT(*) INTO duplicate_count
    FROM produk
    WHERE nama = NEW.nama;

    -- If a duplicate is found, signal an error
    IF duplicate_count > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk sudah ada';
    END IF;
END;

//

DELIMITER ;

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `nama_menu` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `kategori` varchar(50) NOT NULL,
  `gambar_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `nama_menu`, `deskripsi`, `harga`, `stok`, `kategori`, `gambar_url`) VALUES
(1, 'Kopi Latte', 'Espresso dengan susu steamed dan sedikit busa.', 25000.00, 100, 'Kopi', 'https://placehold.co/400x250/F8F8F8/222222?text=Kopi+Latte'),
(2, 'Cappuccino', 'Espresso dengan susu dan busa tebal.', 27000.00, 90, 'Kopi', 'https://placehold.co/400x250/F8F8F8/222222?text=Cappuccino'),
(3, 'Espresso', 'Sajian kopi kental pekat.', 20000.00, 120, 'Kopi', 'https://placehold.co/400x250/F8F8F8/222222?text=Espresso'),
(4, 'Nasi Goreng Spesial', 'Nasi goreng dengan telur, ayam, dan sayuran.', 35000.00, 50, 'Makanan', 'https://placehold.co/400x250/F8F8F8/222222?text=Nasi+Goreng'),
(5, 'Mie Ayam Bakso', 'Mie dengan topping ayam, bakso, dan pangsit.', 32000.00, 45, 'Makanan', 'https://placehold.co/400x250/F8F8F8/222222?text=Mie+Ayam'),
(6, 'Kentang Goreng', 'Kentang goreng renyah.', 18000.00, 80, 'Cemilan', 'https://placehold.co/400x250/F8F8F8/222222?text=Kentang+Goreng'),
(7, 'Es Teh Manis', 'Teh hitam manis dingin.', 10000.00, 150, 'Minuman', 'https://placehold.co/400x250/F8F8F8/222222?text=Es+Teh'),
(8, 'Jus Jeruk', 'Jus jeruk segar.', 22000.00, 70, 'Minuman', 'https://placehold.co/400x250/F8F8F8/222222?text=Jus+Jeruk');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
