<?php
include '../connection.php';

// Cek apakah tabel sudah ada
$tableCheck = mysqli_query($connect, "SHOW TABLES LIKE 'jam_operasional'");
if (mysqli_num_rows($tableCheck) == 0) {
    // Buat tabel jika belum ada
    $createTable = "
        CREATE TABLE jam_operasional (
            id INT PRIMARY KEY AUTO_INCREMENT,
            jam_buka TIME NOT NULL,
            jam_tutup TIME NOT NULL
        )
    ";
    if (mysqli_query($connect, $createTable)) {
        echo "Tabel 'jam_operasional' berhasil dibuat.<br>";
    } else {
        echo "Gagal membuat tabel: " . mysqli_error($connect) . "<br>";
    }
} else {
    echo "Tabel 'jam_operasional' sudah ada.<br>";
}

// Cek apakah data sudah ada
$dataCheck = mysqli_query($connect, "SELECT * FROM jam_operasional");
if (mysqli_num_rows($dataCheck) == 0) {
    // Insert data awal
    $insertData = "
        INSERT INTO jam_operasional (jam_buka, jam_tutup)
        VALUES ('07:00:00', '21:00:00')
    ";
    if (mysqli_query($connect, $insertData)) {
        echo "Data jam operasional berhasil dimasukkan.";
    } else {
        echo "Gagal memasukkan data: " . mysqli_error($connect);
    }
} else {
    echo "Data jam operasional sudah tersedia.";
}
?>
