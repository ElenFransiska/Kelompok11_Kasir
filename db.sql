-- Database Kasir Lengkap dengan Fitur Tambah/Edit/Hapus Stok dan Fitur SQL Lanjutan --

-- DROP TABLES untuk instalasi ulang bersih
DROP TABLE IF EXISTS history;
DROP TABLE IF EXISTS order_detail;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS menu;
DROP TABLE IF EXISTS pelanggan;

-- Tabel Pelanggan
CREATE TABLE pelanggan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  telepon VARCHAR(15) NOT NULL
);

-- Tabel Menu (Makanan & Minuman)
CREATE TABLE menu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  jenis ENUM('Makanan', 'Minuman') NOT NULL,
  stok INT NOT NULL CHECK (stok >= 0),
  harga DECIMAL(10,2) NOT NULL,
  keterangan TEXT
);

-- INDEX untuk mempercepat pencarian berdasarkan jenis dan nama
CREATE INDEX idx_menu_jenis ON menu(jenis);
CREATE INDEX idx_menu_nama ON menu(nama);

-- Fungsi untuk mendapatkan nilai maksimal stok
DELIMITER //
CREATE FUNCTION max_stok() RETURNS INT
BEGIN
  DECLARE maxstok INT;
  SELECT MAX(stok) INTO maxstok FROM menu;
  RETURN IFNULL(maxstok, 0);
END;
//

-- Fungsi untuk mendapatkan nilai minimal stok
CREATE FUNCTION min_stok() RETURNS INT
BEGIN
  DECLARE minstok INT;
  SELECT MIN(stok) INTO minstok FROM menu;
  RETURN IFNULL(minstok, 0);
END;
//
DELIMITER ;

-- View untuk menampilkan menu lengkap
CREATE OR REPLACE VIEW view_menu AS
SELECT id, nama, jenis, stok, harga, keterangan
FROM menu;

-- Tabel Orders
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pelanggan_id INT NOT NULL,
  total_harga DECIMAL(10,2) NOT NULL,
  tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE CASCADE
);

-- Tabel Order_Detail (detil tiap item pesanan)
CREATE TABLE order_detail (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  menu_id INT NOT NULL,
  jumlah INT NOT NULL CHECK(jumlah > 0),
  harga DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (menu_id) REFERENCES menu(id)
);

-- INDEX untuk detail order berdasarkan order_id dan menu_id
CREATE INDEX idx_order_detail_order ON order_detail(order_id);
CREATE INDEX idx_order_detail_menu ON order_detail(menu_id);

-- Tabel History (menyimpan ringkasan pesanan final)
CREATE TABLE history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  pelanggan_id INT NOT NULL,
  total_harga DECIMAL(10,2) NOT NULL,
  tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE CASCADE
);

-- Trigger untuk update stok setelah insert order_detail (kurangi stok)
DELIMITER //
CREATE TRIGGER trg_reduce_stock_after_insert_order_detail
AFTER INSERT ON order_detail
FOR EACH ROW
BEGIN
  UPDATE menu SET stok = stok - NEW.jumlah WHERE id = NEW.menu_id;
END;
//

-- Trigger untuk menambah stok setelah hapus order_detail (batalkan order)
CREATE TRIGGER trg_add_stock_after_delete_order_detail
AFTER DELETE ON order_detail
FOR EACH ROW
BEGIN
  UPDATE menu SET stok = stok + OLD.jumlah WHERE id = OLD.menu_id;
END;
//

-- Trigger isi tabel history saat insert order final
CREATE TRIGGER trg_insert_history_after_insert_orders
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
  INSERT INTO history(order_id, pelanggan_id, total_harga, tanggal)
    VALUES(NEW.id, NEW.pelanggan_id, NEW.total_harga, NEW.tanggal);
END;
//
DELIMITER ;

-- Procedure untuk tambah menu baru
DELIMITER //
CREATE PROCEDURE tambah_menu(
  IN p_nama VARCHAR(100),
  IN p_jenis ENUM('Makanan', 'Minuman'),
  IN p_stok INT,
  IN p_harga DECIMAL(10,2),
  IN p_keterangan TEXT
)
BEGIN
  INSERT INTO menu(nama, jenis, stok, harga, keterangan)
  VALUES(p_nama, p_jenis, p_stok, p_harga, p_keterangan);
END;
//

-- Procedure untuk edit menu
CREATE PROCEDURE edit_menu(
  IN p_id INT,
  IN p_nama VARCHAR(100),
  IN p_jenis ENUM('Makanan', 'Minuman'),
  IN p_stok INT,
  IN p_harga DECIMAL(10,2),
  IN p_keterangan TEXT
)
BEGIN
  UPDATE menu
  SET nama = p_nama, jenis = p_jenis, stok = p_stok, harga = p_harga, keterangan = p_keterangan
  WHERE id = p_id;
END;
//

-- Procedure hapus menu berdasarkan id
CREATE PROCEDURE hapus_menu(
  IN p_id INT
)
BEGIN
  DELETE FROM menu WHERE id = p_id;
END;
//

-- Cursor contoh untuk list menu dengan stok rendah (<= 5)
DELIMITER //
CREATE PROCEDURE list_stok_rendah()
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE vid INT;
  DECLARE vnama VARCHAR(100);
  DECLARE vstok INT;
  DECLARE cur CURSOR FOR SELECT id, nama, stok FROM menu WHERE stok <= 5 ORDER BY stok ASC;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO vid, vnama, vstok;
    IF done THEN
      LEAVE read_loop;
    END IF;
    SELECT CONCAT('Menu ID: ', vid, ' Nama: ', vnama, ' Stok rendah ', vstok) AS pesan;
  END LOOP;
  CLOSE cur;
END;
//
DELIMITER ;