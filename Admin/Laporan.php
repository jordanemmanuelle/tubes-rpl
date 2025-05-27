<?php
include '../connection.php';

// Ambil data laporan gabungan dari detail_transaksi, transaksi, dan menu
$sql = "SELECT 
            t.id_transaksi,
            m.id_menu,
            dt.id_promo,
            t.tanggal,
            m.nama_menu,
            dt.jumlah,
            m.harga,
            m.modal,
            (dt.jumlah * m.harga) AS subtotal,
            (dt.jumlah * (m.harga - m.modal)) AS net_profit
        FROM detail_transaksi dt
        JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
        JOIN menu m ON dt.id_menu = m.id_menu
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
    <link rel="stylesheet" href="Laporan.css">
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
            <th>ID Transaksi</th>
            <th>ID Menu</th>
            <th>Nama Menu</th>
            <th>Jumlah</th>
            <th>Harga Satuan</th>
            <th>Modal</th>
            <th>Subtotal</th>
            <th>ID Promo</th>
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
                <td><?= $row['id_transaksi'] ?></td>
                <td><?= $row['id_menu'] ?></td>
                <td><?= htmlspecialchars($row['nama_menu']) ?></td>
                <td><?= $row['jumlah'] ?></td>
                
                <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>

                <td>Rp<?= number_format($row['modal'] * $row['jumlah'], 0, ',', '.') ?></td>
                <td>Rp<?= number_format($row['subtotal'], 0, ',', '.') ?></td>
                <td><?= $row['id_promo'] ?? '-' ?></td>
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

    <a href="AdminMenu.php" style="
  display: block;
  width: 120px;
  margin: 30px auto 20px auto;
  padding: 12px 0;
  background: #6c757d;
  color: white;
  text-decoration: none;
  font-weight: 600;
  text-align: center;
  border-radius: 8px;
  font-size: 15px;
  transition: background 0.3s;
  box-shadow: 0 2px 6px rgba(108,117,125,0.08);
">Back</a>

</body>
</html>