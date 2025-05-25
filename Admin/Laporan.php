<?php
include '../connection.php';

// Ambil data laporan gabungan dari detail_transaksi, transaksi, dan menu
$sql = "SELECT 
            t.tanggal,
            m.nama_menu,
            dt.jumlah,
            dt.harga,
            m.modal,
            (dt.jumlah * dt.harga) AS subtotal,
            (dt.jumlah * (dt.harga - m.modal)) AS net_profit
        FROM detail_transaksi dt
        JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
        JOIN menu m ON dt.id_produk = m.id_menu
        ORDER BY t.tanggal DESC";


$result = mysqli_query($connect, $sql);

// Hitung total pendapatan
$total = 0;
$netTotal = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        h2 {
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <h1>Laporan Keuangan</h1>
    <table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Nama Menu</th>
            <th>Jumlah</th>
            <th>Harga Satuan</th>
            <th>Modal</th>
            <th>Subtotal</th>
            <th>Net Profit</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)) : 
                $total += $row['subtotal'];
                $netTotal += $row['net_profit'];
            ?>
            <tr>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td><?= htmlspecialchars($row['nama_menu']) ?></td>
                <td><?= $row['jumlah'] ?></td>
                <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                <td>Rp<?= number_format($row['modal'] * $row['jumlah'], 0, ',', '.') ?></td>
                <td>Rp<?= number_format($row['subtotal'], 0, ',', '.') ?></td>
                <td>Rp<?= number_format($row['net_profit'], 0, ',', '.') ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">Belum ada transaksi.</td></tr>
        <?php endif; ?>
    </tbody>
</table>


    <h2>Total Pendapatan: Rp<?= number_format($total, 0, ',', '.') ?></h2>
    <h2>Net Profit: Rp<?= number_format($netTotal, 0, ',', '.') ?></h2>


<a href="../Pages/AdminMenu.php" style="
  display: inline-block;
  margin-top: 20px;
  margin-bottom: 10px;
  padding: 10px 15px;
  background-color: #6c757d;
  color: white;
  text-decoration: none;
  font-weight: 600;
  text-align: center;
  border-radius: 6px;
  transition: background-color 0.3s ease;
">Back</a>
</body>
</html>
