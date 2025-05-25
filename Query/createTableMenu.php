<?php
    include '../connection.php';

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

//     $sql = "INSERT INTO menu (nama_menu, deskripsi, harga, gambar, stok, jenis) VALUES
//     ('Cappuccino', 'Kopi cappuccino dengan foam lembut', 18000.00, '../Gambar/cappucino.png', 20, 'minuman'),
//     ('Croissant', 'Roti croissant hangat dan renyah', 15000.00, '../Gambar/Croissant.png', 12, 'makanan'),
//     ('Espresso', 'Espresso shot asli dengan rasa kuat', 12000.00, '../Gambar/Espresso.png', 25, 'minuman'),
//     ('Hot Latte', 'Kopi latte hangat dengan susu segar', 19000.00, '../Gambar/HotLatte.png', 15, 'minuman')";

// if (mysqli_query($connect, $sql)) {
//     echo "Dummy data inserted successfully";
// } else {
//     echo "Error inserting dummy data: " . mysqli_error($connect);
// }

?>