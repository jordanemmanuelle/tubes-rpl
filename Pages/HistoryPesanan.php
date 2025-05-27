<?php
session_start();
include '../connection.php';

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../LoginRegister/FormLogin.html");
    exit();
}

// Ambil seluruh data pesanan (tanpa filter user)
$sql = "SELECT t.id_transaksi, t.tanggal, t.status, t.total, t.metode_pengambilan
        FROM transaksi t
        ORDER BY t.tanggal DESC";
$stmt = $connect->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<p style='color:red;text-align:center;'>Tidak ada detail pesanan untuk transaksi #$id_transaksi</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>History Pesanan</title>
    <link rel="stylesheet" href="HistoryPesanan.css">
    <style>
        h2 {
    text-align: center;
}
        .back-button {
            display: block;
            width: 120px;
            margin: 30px auto 20px auto;
            padding: 12px 0;
            background: #6c757d;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            border-radius: 8px;
            font-size: 15px;
            transition: background 0.3s;
            box-shadow: 0 2px 6px rgba(108,117,125,0.08);
        }
        .back-button:hover {
            background: #495057;
        }
    </style>
</head>
<body>
    <h2>History Pesanan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Metode</th>
                <th>Total</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['id_transaksi']; ?></td>
                <td><?= $row['tanggal']; ?></td>
                <td><?= ucfirst($row['status']); ?></td>
                <td><?= $row['metode_pengambilan']; ?></td>
                <td>Rp<?= number_format($row['total'], 0, ',', '.'); ?></td>
                <td>
                    <a href="DetailHistoryPesanan.php?id=<?= $row['id_transaksi']; ?>" font-weight:600;>Lihat</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <a href="Home.php" class="back-button">Back</a>
</body>
</html>