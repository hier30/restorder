-- ============================================================
-- Sistem Pemesanan Restoran - Schema Database
-- Database: restorder
-- Engine : MySQL / MariaDB
-- ============================================================

CREATE DATABASE IF NOT EXISTS restorder
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE restorder;

-- ------------------------------------------------------------
-- Tabel: meja
-- Menyimpan data meja di restoran beserta statusnya
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS meja (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomor_meja  VARCHAR(10) NOT NULL UNIQUE COMMENT 'Nomor identitas meja (misal: M01)',
    kapasitas   TINYINT UNSIGNED NOT NULL DEFAULT 4 COMMENT 'Jumlah kursi',
    status      ENUM('tersedia', 'terisi', 'dipesan') NOT NULL DEFAULT 'tersedia',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabel: pelanggan
-- Menyimpan data pelanggan yang melakukan pemesanan
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pelanggan (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(100) NOT NULL,
    no_telepon  VARCHAR(20) DEFAULT NULL,
    email       VARCHAR(100) DEFAULT NULL,
    alamat      TEXT DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabel: menu
-- Daftar makanan & minuman yang tersedia
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS menu (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_menu   VARCHAR(100) NOT NULL,
    kategori    ENUM('makanan', 'minuman', 'dessert') NOT NULL DEFAULT 'makanan',
    harga       DECIMAL(12, 2) NOT NULL DEFAULT 0.00 COMMENT 'Harga satuan dalam Rupiah',
    deskripsi   TEXT DEFAULT NULL,
    tersedia    TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = tersedia, 0 = habis',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabel: pesanan
-- Header pesanan, berisi info siapa yang memesan, tipe, dan total
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pesanan (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pelanggan_id    INT UNSIGNED NOT NULL,
    meja_id         INT UNSIGNED DEFAULT NULL COMMENT 'NULL jika take-away',
    tipe_pesanan    ENUM('dine_in', 'take_away') NOT NULL DEFAULT 'dine_in',
    status_pesanan  ENUM('pending', 'diproses', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'pending',
    biaya_layanan   DECIMAL(12, 2) NOT NULL DEFAULT 0.00 COMMENT 'Biaya layanan meja (dine-in only)',
    total           DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    tanggal_pesanan DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    catatan         TEXT DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_pesanan_pelanggan FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_pesanan_meja FOREIGN KEY (meja_id) REFERENCES meja(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Index pada tanggal_pesanan untuk laporan (filter berdasarkan tanggal)
CREATE INDEX idx_pesanan_tanggal ON pesanan (tanggal_pesanan);
-- Index pada pelanggan_id untuk laporan per pelanggan
CREATE INDEX idx_pesanan_pelanggan ON pesanan (pelanggan_id);

-- ------------------------------------------------------------
-- Tabel: detail_pesanan
-- Item-item yang dipesan dalam satu pesanan
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS detail_pesanan (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pesanan_id  INT UNSIGNED NOT NULL,
    menu_id     INT UNSIGNED NOT NULL,
    jumlah      SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    harga_saat_pesan DECIMAL(12, 2) NOT NULL COMMENT 'Harga menu saat dipesan (snapshot)',
    subtotal    DECIMAL(12, 2) NOT NULL COMMENT 'jumlah * harga_saat_pesan',

    CONSTRAINT fk_detail_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_detail_menu FOREIGN KEY (menu_id) REFERENCES menu(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA (Data Contoh)
-- ============================================================

-- Data meja
INSERT INTO meja (nomor_meja, kapasitas, status) VALUES
    ('M01', 2, 'tersedia'),
    ('M02', 4, 'tersedia'),
    ('M03', 6, 'terisi'),
    ('M04', 4, 'tersedia'),
    ('M05', 8, 'dipesan');

-- Data pelanggan
INSERT INTO pelanggan (nama, no_telepon, email, alamat) VALUES
    ('Budi Santoso',   '081234567890', 'budi@email.com',   'Jl. Merdeka No. 10, Bandar Lampung'),
    ('Siti Nurhaliza',  '082345678901', 'siti@email.com',   'Jl. Raden Intan No. 5, Bandar Lampung'),
    ('Ahmad Dahlan',   '083456789012', 'ahmad@email.com',  'Jl. Kartini No. 22, Bandar Lampung');

-- Data menu
INSERT INTO menu (nama_menu, kategori, harga, deskripsi, tersedia) VALUES
    ('Nasi Goreng Spesial',  'makanan',  35000.00, 'Nasi goreng dengan telur, ayam, dan sayuran', 1),
    ('Ayam Bakar Madu',      'makanan',  45000.00, 'Ayam bakar dengan saus madu pilihan',          1),
    ('Mie Goreng Seafood',   'makanan',  40000.00, 'Mie goreng dengan udang, cumi, dan sayuran',   1),
    ('Es Teh Manis',         'minuman',  8000.00,  'Teh manis dingin segar',                       1),
    ('Jus Alpukat',          'minuman',  15000.00, 'Jus alpukat segar dengan susu coklat',          1),
    ('Pudding Coklat',       'dessert',  20000.00, 'Pudding coklat lembut dengan whipped cream',    1);

-- Data pesanan contoh
INSERT INTO pesanan (pelanggan_id, meja_id, tipe_pesanan, status_pesanan, biaya_layanan, total, tanggal_pesanan, catatan) VALUES
    (1, 3, 'dine_in',   'selesai',  10000.00, 88000.00, '2024-10-10 12:30:00', 'Tidak pedas'),
    (2, NULL, 'take_away', 'selesai',  0.00,    55000.00, '2024-10-11 13:00:00', 'Bungkus rapi'),
    (3, 3, 'dine_in',   'diproses', 10000.00, 73000.00, '2024-10-12 11:45:00', NULL);

-- Data detail pesanan
-- Pesanan #1: Nasi Goreng + Ayam Bakar + Es Teh = 35000+45000+8000 = 88000 (termasuk biaya layanan 10000)
INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah, harga_saat_pesan, subtotal) VALUES
    (1, 1, 1, 35000.00, 35000.00),
    (1, 2, 1, 45000.00, 45000.00),
    (1, 4, 1, 8000.00,  8000.00);

-- Pesanan #2: Mie Goreng + Jus Alpukat = 40000+15000 = 55000
INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah, harga_saat_pesan, subtotal) VALUES
    (2, 3, 1, 40000.00, 40000.00),
    (2, 5, 1, 15000.00, 15000.00);

-- Pesanan #3: Nasi Goreng + Pudding + Es Teh x2 = 35000+20000+8000+8000 = 71000 (+ layanan 10000 belum di-update total)
INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah, harga_saat_pesan, subtotal) VALUES
    (3, 1, 1, 35000.00, 35000.00),
    (3, 6, 1, 20000.00, 20000.00),
    (3, 4, 2, 8000.00,  16000.00);
