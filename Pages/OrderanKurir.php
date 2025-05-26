<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['id_kurir'])) {
    header("Location: ../Login/FormLogin.html");
    exit();
}

$id_kurir = $_SESSION['id_kurir'];
$nama_kurir = htmlspecialchars($_SESSION['nama_kurir']);

// Proses form update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selesai'])) {
    foreach ($_POST['selesai'] as $id_transaksi) {
        $update = "UPDATE transaksi SET status = 'selesai' WHERE id_transaksi = ? AND id_kurir = ?";
        $stmt = mysqli_prepare($connect, $update);
        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($connect));
        }
        mysqli_stmt_bind_param($stmt, "ii", $id_transaksi, $id_kurir);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: OrderanKurir.php");
    exit();
}

// Ambil order yang ongoing untuk kurir ini
$query = "SELECT t.id_transaksi, t.tanggal, u.name, dt.nama_penerima, dt.alamat_penerima 
          FROM transaksi t 
          JOIN users u ON t.id_user = u.id_user
          JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
          WHERE t.id_kurir = ? AND t.status = 'ongoing'
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
    <title>Orderan Kurir</title>
</head>
<body>
    <h2>Selamat datang, Kurir <?= $nama_kurir ?></h2>
    <h3>Daftar Orderan yang Belum Selesai</h3>

    <form method="post" action="">
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Centang Jika Selesai</th>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Nama Pemesan</th>
                <th>Nama Penerima</th>
                <th>Alamat Pengiriman</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><input type="checkbox" name="selesai[]" value="<?= $row['id_transaksi'] ?>"></td>
                    <td><?= $row['id_transaksi'] ?></td>
                    <td><?= $row['tanggal'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['nama_penerima']) ?></td>
                    <td><?= htmlspecialchars($row['alamat_penerima']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <br>
        <button type="submit">Tandai Selesai</button>
    </form>

    <br><a href="HistoryKurir.php">Lihat Riwayat Pengantaran</a>
</body>
</html>
