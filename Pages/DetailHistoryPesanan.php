<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../LoginRegister/FormLogin.html");
    exit();
}

$id_transaksi = intval($_GET['id'] ?? 0);

// Tidak perlu cek kepemilikan transaksi

// Ambil detail pesanan
$sql = "SELECT dt.*, m.nama_menu FROM detail_transaksi dt
        JOIN menu m ON dt.id_menu = m.id_menu
        WHERE dt.id_transaksi = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $id_transaksi);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Pesanan</title>
    <link rel="stylesheet" href="DetailHistoryPesanan.css">
    <style>
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
    <h2>Detail Pesanan #<?= $id_transaksi ?></h2>
    <table>
        <thead>
            <tr>
                <th>Nama Menu</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_menu']); ?></td>
                <td><?= $row['jumlah']; ?></td>
                <td>Rp<?= number_format($row['harga'], 0, ',', '.'); ?></td>
                <td>Rp<?= number_format($row['harga'] * $row['jumlah'], 0, ',', '.'); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <a href="HistoryPesanan.php" class="back-button">Kembali</a>
</body>
</html>