<?php
    $connect = mysqli_connect("localhost", "root", "", "foretubes");

    if (mysqli_connect_errno()) {
        echo (mysqli_connect_error());
    }

    $sql = "CREATE TABLE menu (
    id_menu INT AUTO_INCREMENT PRIMARY KEY,
    nama_menu VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    gambar VARCHAR(255),
    stok INT DEFAULT 0,
    jenis ENUM('makanan', 'minuman') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";

    if (mysqli_query($connect, $sql)) {
        echo ("Table created");
    } else {
        echo ("Error while creating table");
    }

?>