CREATE TABLE promo (
    id_promo INT PRIMARY KEY AUTO_INCREMENT,
    nama_promo VARCHAR(100),
    deskripsi TEXT,
    kode_promo VARCHAR(50),
    jenis_diskon ENUM('persen', 'nominal'),
    nilai_diskon DECIMAL(10,2),
    minimal_transaksi DECIMAL(10,2),
    tanggal_mulai DATE,
    tanggal_berakhir DATE,
    aktif BOOLEAN
);

INSERT INTO promo (
    nama_promo,
    deskripsi,
    kode_promo,
    jenis_diskon,
    nilai_diskon,
    minimal_transaksi,
    tanggal_mulai,
    tanggal_berakhir,
    aktif
) VALUES (
    'Promo Grand Opening',
    'Diskon 15% Dengan minimal pembelian Rp 50.000',
    'RAMADHAN2025',
    'persen',
    15.00,
    50000.00,
    '2025-05-27',
    '2025-09-27',
    1
);
