<?php
include '../connection.php';

$sqlCreate = "CREATE TABLE IF NOT EXISTS transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    tanggal DATETIME NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id_user)
)";

if (mysqli_query($connect, $sqlCreate)) {
    echo "Tabel transaksi berhasil dibuat atau sudah ada.<br>";
} else {
    echo "Error saat membuat tabel transaksi: " . mysqli_error($connect) . "<br>";
}

// 2. Cek dan tambahkan kolom metode_pengambilan jika belum ada
$sqlCheckMetode = "SHOW COLUMNS FROM transaksi LIKE 'metode_pengambilan'";
$resultMetode = mysqli_query($connect, $sqlCheckMetode);

if (mysqli_num_rows($resultMetode) == 0) {
    $sqlAddMetode = "ALTER TABLE transaksi 
        ADD metode_pengambilan ENUM('Delivery', 'Pick Up') NOT NULL DEFAULT 'Pick Up'";
    if (mysqli_query($connect, $sqlAddMetode)) {
        echo "Kolom metode_pengambilan berhasil ditambahkan.<br>";
    } else {
        echo "Error saat menambahkan kolom metode_pengambilan: " . mysqli_error($connect) . "<br>";
    }
} else {
    echo "Kolom metode_pengambilan sudah ada.<br>";
}

// 3. Cek dan tambahkan kolom id_kurir jika belum ada
$sqlCheckKurir = "SHOW COLUMNS FROM transaksi LIKE 'id_kurir'";
$resultKurir = mysqli_query($connect, $sqlCheckKurir);

if (mysqli_num_rows($resultKurir) == 0) {
    // Tambah kolom id_kurir dan buat foreign key-nya
    $sqlAddKurir = "ALTER TABLE transaksi 
        ADD id_kurir INT NULL,
        ADD CONSTRAINT fk_transaksi_kurir FOREIGN KEY (id_kurir) REFERENCES kurir(id_kurir)";
    if (mysqli_multi_query($connect, $sqlAddKurir)) {
        // Karena multi query, kita perlu consume results
        do {
            if ($res = mysqli_store_result($connect)) {
                mysqli_free_result($res);
            }
        } while (mysqli_more_results($connect) && mysqli_next_result($connect));
        
        echo "Kolom id_kurir dan foreign key berhasil ditambahkan.<br>";
    } else {
        echo "Error saat menambahkan kolom id_kurir atau foreign key: " . mysqli_error($connect) . "<br>";
    }
} else {
    echo "Kolom id_kurir sudah ada.<br>";
}

// 4. Cek dan tambahkan kolom status jika belum ada
$sqlCheckStatus = "SHOW COLUMNS FROM transaksi LIKE 'status'";
$resultStatus = mysqli_query($connect, $sqlCheckStatus);

if (mysqli_num_rows($resultStatus) == 0) {
    $sqlAddStatus = "ALTER TABLE transaksi 
        ADD status ENUM('ongoing', 'selesai') NOT NULL DEFAULT 'ongoing'";
    if (mysqli_query($connect, $sqlAddStatus)) {
        echo "Kolom status berhasil ditambahkan.<br>";
    } else {
        echo "Error saat menambahkan kolom status: " . mysqli_error($connect) . "<br>";
    }
} else {
    echo "Kolom status sudah ada.<br>";
}

?>
