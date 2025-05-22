<?php
session_start();

// Tangkap data keranjang yang dikirim lewat POST
if (!isset($_POST['cart_data'])) {
    echo "Data keranjang tidak ditemukan.";
    exit;
}

$cartDataJson = $_POST['cart_data'];
$cart = json_decode($cartDataJson, true);

if (!$cart || !is_array($cart)) {
    echo "Data keranjang tidak valid.";
    exit;
}

// Hitung total harga
$totalHarga = 0;
foreach ($cart as $item) {
    $totalHarga += $item['harga'] * $item['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Checkout - Fore Coffee</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; max-width: 600px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f5f5f5; }
        .total { font-weight: bold; }
    </style>
</head>
<body>

    <h1>Ringkasan Pesanan</h1>

    <table>
        <thead>
            <tr>
                <th>Nama Menu</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['nama_menu']) ?></td>
                <td><?= (int)$item['jumlah'] ?></td>
                <td>Rp<?= number_format($item['harga'], 0, ',', '.') ?></td>
                <td>Rp<?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="total">Total Harga</td>
                <td class="total">Rp<?= number_format($totalHarga, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <p>Terima kasih telah berbelanja di Fore Coffee.</p>

    <p><a href="home.php">Kembali ke Home</a></p>

</body>
</html>
