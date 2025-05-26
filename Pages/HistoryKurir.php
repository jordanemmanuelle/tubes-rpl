<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['id_kurir'])) {
    header("Location: ../Login/FormLogin.html");
    exit();
}

$id_kurir = $_SESSION['id_kurir'];

// Query untuk ambil riwayat pengiriman yang sudah selesai
$query = "SELECT t.id_transaksi, t.tanggal, u.name, dt.nama_penerima, dt.alamat_penerima 
          FROM transaksi t 
          JOIN users u ON t.id_user = u.id_user
          JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
          WHERE t.id_kurir = ? AND t.status = 'selesai'
          GROUP BY t.id_transaksi";

$stmt = mysqli_prepare($connect, $query);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($connect));
}

mysqli_stmt_bind_param($stmt, "i", $id_kurir);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Pengiriman Kurir</title>
    <link rel="stylesheet" href="HistoryKurir.css">
</head>
<body>
    <h2>Riwayat Pengiriman - <?= htmlspecialchars($_SESSION['nama_kurir']) ?></h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Nama Pemesan</th>
            <th>Nama Penerima</th>
            <th>Alamat Pengiriman</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['id_transaksi'] ?></td>
                <td><?= $row['tanggal'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['nama_penerima']) ?></td>
                <td><?= htmlspecialchars($row['alamat_penerima']) ?></td>
            </tr>
        <?php endwhile; ?>
        <a href="../Pages/OrderanKurir.php">Kembali ke Menu Kurir</a>
    </table>
</body>
</html>
