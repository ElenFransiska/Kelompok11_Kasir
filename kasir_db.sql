-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2025 at 04:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kasir_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetOrderSummary` ()   BEGIN
    SELECT * FROM vw_order_summary;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama`, `username`, `password`) VALUES
(1, 'Admin Utama', 'admin', '21232f297a57a5a743894a0e4a801fc3'),
(2, 'Elen Kasir', 'elen', '508c71d57a2c2dd1ed8c3ede5b3012d5'),
(3, 'Raymond Kasir', 'raymond', 'f2a415aa78c7621831da5995e1447242');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id_order` int(11) NOT NULL,
  `nama_pembeli` varchar(100) NOT NULL,
  `meja` int(11) NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id_order_item` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `order_items`
--
DELIMITER $$
CREATE TRIGGER `trg_check_stock_before_order_item_insert` BEFORE INSERT ON `order_items` FOR EACH ROW BEGIN
    DECLARE current_stock INT;

    SELECT stok INTO current_stock
    FROM produk
    WHERE id_produk = NEW.id_produk;

    IF NEW.jumlah > current_stock THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok produk tidak cukup untuk pesanan ini.';
    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `kategori`, `nama`, `image`, `keterangan`, `stok`, `harga`) VALUES
(1, 'Makanan', 'Nasi Goreng Spesial', 'images/nasi_goreng_spesial.jpg', 'Nasi goreng dengan bumbu rempah pilihan dan telur mata sapi.', 50, 25000),
(2, 'Makanan', 'Mie Ayam Bakso', 'images/mie_ayam_bakso.jpg', 'Mie ayam dengan toping bakso sapi dan pangsit goreng.', 45, 22000),
(3, 'Makanan', 'Sate Ayam Madura', 'images/sate_ayam_madura.jpg', 'Sate ayam dengan bumbu kacang khas Madura.', 40, 30000),
(4, 'Makanan', 'Gado-Gado Siram', 'images/gado-gado_siram.jpg', 'Sayuran segar dengan lontong dan bumbu kacang siram.', 31, 20000),
(5, 'Makanan', 'Soto Betawi', 'images/soto_betawi.webp', 'Soto santan khas Betawi dengan daging sapi.', 28, 28000),
(6, 'Minuman', 'Es Teh Manis', 'images/es_teh_manis.webp', 'Minuman teh segar dengan gula asli.', 60, 8000),
(7, 'Minuman', 'Es Jeruk Peras', 'images/es_jeruk_peras.jpeg', 'Minuman jeruk peras asli tanpa pengawet.', 55, 10000),
(8, 'Minuman', 'Kopi Susu Dingin', 'images/kopi_susu_dingin.webp', 'Kopi robusta dengan susu dan es.', 50, 15000),
(9, 'Minuman', 'Jus Alpukat', 'images/jus_alpukat.jpg', 'Jus alpukat segar dengan susu cokelat.', 44, 18000),
(10, 'Minuman', 'Wedang Jahe', 'images/wedang_jahe.jpeg', 'Minuman hangat jahe dengan gula merah.', 36, 12000),
(51, 'Minuman', 'Vanila Spesial', 'images/vanila_spesial.jpg', 'Jus Vanila dengan ekstrak Kopi', 2, 25000),
(53, 'Makanan', 'Nasi Bakar Spesial', 'images/nasi_bakar_spesial.webp', 'Nasi yang dibakar dengan balutan daun pisang', 12, 48000);

--
-- Triggers `produk`
--
DELIMITER $$
CREATE TRIGGER `check_duplicate_nama` BEFORE INSERT ON `produk` FOR EACH ROW BEGIN
    DECLARE duplicate_count INT;

    -- Check for existing product with the same name
    SELECT COUNT(*) INTO duplicate_count
    FROM produk
    WHERE nama = NEW.nama;

    -- If a duplicate is found, signal an error
    IF duplicate_count > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk sudah ada';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_menu`
-- (See below for the actual view)
--
CREATE TABLE `view_menu` (
`id_produk` int(11)
,`kategori` varchar(50)
,`nama` varchar(100)
,`image` varchar(255)
,`keterangan` text
,`harga` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_order_summary`
-- (See below for the actual view)
--
CREATE TABLE `vw_order_summary` (
`id_order` int(11)
,`nama_pembeli` varchar(100)
,`meja` int(11)
,`total_produk` decimal(32,0)
,`total_harga` decimal(44,2)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `view_menu`
--
DROP TABLE IF EXISTS `view_menu`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_menu`  AS SELECT `produk`.`id_produk` AS `id_produk`, `produk`.`kategori` AS `kategori`, `produk`.`nama` AS `nama`, `produk`.`image` AS `image`, `produk`.`keterangan` AS `keterangan`, `produk`.`harga` AS `harga` FROM `produk` ORDER BY `produk`.`kategori` ASC, `produk`.`nama` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vw_order_summary`
--
DROP TABLE IF EXISTS `vw_order_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_order_summary`  AS SELECT `o`.`id_order` AS `id_order`, `o`.`nama_pembeli` AS `nama_pembeli`, `o`.`meja` AS `meja`, sum(`oi`.`jumlah`) AS `total_produk`, sum(`oi`.`jumlah` * `oi`.`harga_satuan`) AS `total_harga`, `o`.`created_at` AS `created_at` FROM (`orders` `o` join `order_items` `oi` on(`o`.`id_order` = `oi`.`id_order`)) GROUP BY `o`.`id_order`, `o`.`nama_pembeli`, `o`.`meja`, `o`.`created_at` ORDER BY `o`.`created_at` DESC, `o`.`nama_pembeli` ASC ;

-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id_order_item`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id_order_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

DELIMITER $$

CREATE PROCEDURE GetMenuItems()
BEGIN
  SELECT 
    id_produk,
    nama AS nama_produk,
    harga,
    kategori,
    image,
    stok,
    CASE
      WHEN stok = 0 THEN 'Produk habis'
      ELSE keterangan
    END AS keterangan
  FROM produk
  ORDER BY kategori, nama;
END$$

DELIMITER ;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
