<?php
    include '../connection.php';

    $sql = "CREATE TABLE IF NOT EXISTS kurir (
    id_kurir INT AUTO_INCREMENT PRIMARY KEY,
    nama_kurir VARCHAR(100) NOT NULL,
    telepon VARCHAR(20) NOT NULL,
    biaya INT NOT NULL,
    status ENUM('available', 'unavailable') DEFAULT 'available'
    )";

    if (mysqli_query($connect, $sql)) {
        echo "Tabel 'kurir' berhasil dibuat";
    } else {
        echo "Gagal membuat tabel: " . mysqli_error($connect);
    }
?>