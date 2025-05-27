<?php
    include '../connection.php';

    $sql = "CREATE TABLE detail_transaksi (
        id_detail INT AUTO_INCREMENT PRIMARY KEY,
        id_transaksi INT NOT NULL,
        id_produk INT NOT NULL,
        id_promo INT NULL,
        jumlah INT NOT NULL,
        harga DECIMAL(10,2) NOT NULL,
        nama_penerima VARCHAR(100) NULL,
        alamat_penerima TEXT NULL,
        FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
        FOREIGN KEY (id_produk) REFERENCES menu(id_menu),
        FOREIGN KEY (id_promo) REFERENCES promo(id_promo)
    )";

    if (mysqli_query($connect, $sql)) {
        echo ("Table created");
    } else {
        echo ("Error while creating table: " . mysqli_error($connect));
    }
?>
