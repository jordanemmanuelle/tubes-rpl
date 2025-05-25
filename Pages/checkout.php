<?php
session_start();
include '../connection.php';

// Simpan cart dari POST (jika ada) ke SESSION
if (isset($_POST['cart_data'])) {
    $cartDataJson = $_POST['cart_data'];
    $cart = json_decode($cartDataJson, true);

    if ($cart && is_array($cart)) {
        $_SESSION['cart'] = [];
        foreach ($cart as $item) {
            $_SESSION['cart'][] = [
                'id' => $item['id_menu'],
                'nama_menu' => $item['nama_menu'],
                'harga' => $item['harga'],
                'qty' => $item['jumlah']
            ];
        }
    }
}

// Proses Bayar
if (isset($_POST['bayar'])) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo "<script>alert('Keranjang masih kosong!');</script>";
    } else {
       
        $id_user = $_SESSION['id_user'] ?? 1;
        $total = 0;

        // 1. Cek stok cukup
        foreach ($_SESSION['cart'] as $item) {
            $id_menu = $item['id'];
            $qty = $item['qty'];

            $result = mysqli_query($connect, "SELECT stok FROM menu WHERE id_menu = '$id_menu'");
            $data = mysqli_fetch_assoc($result);

            if (!$data || $data['stok'] < $qty) {
                echo "<script>alert('Stok untuk {$item['nama_menu']} tidak mencukupi!'); window.location='Home.php';</script>";
                exit;
            }

            $total += $item['harga'] * $qty;
        }
        
        // 2. Insert ke transaksi
        $tanggal = date('Y-m-d H:i:s');
        $query = "INSERT INTO transaksi (id_user, total, tanggal) VALUES ('$id_user', '$total', '$tanggal')";
        mysqli_query($connect, $query);
        $id_transaksi = mysqli_insert_id($connect);

        // 3. Insert ke detail_transaksi + kurangi stok
        foreach ($_SESSION['cart'] as $item) {
            $id_menu = $item['id'];
            $qty = $item['qty'];
            $harga = $item['harga'];

            // Insert detail
            $query_detail = "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga) 
                             VALUES ('$id_transaksi', '$id_menu', '$qty', '$harga')";
            mysqli_query($connect, $query_detail);

            // Kurangi stok
            mysqli_query($connect, "UPDATE menu SET stok = stok - $qty WHERE id_menu = '$id_menu'");
        }
        
        unset($_SESSION['cart']);
        echo "<script>
                alert('Pembayaran berhasil!');
                localStorage.removeItem('cart');
                window.location='Home.php';
              </script>";
        exit;
         
        echo "<script>alert('Pembayaran berhasil!'); window.location='Home.php';</script>";
        exit;
    }
}


// Ambil cart dari session
$cart = $_SESSION['cart'] ?? [];
$totalHarga = 0;
foreach ($cart as $item) {
    $totalHarga += $item['harga'] * $item['qty'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Checkout - Fore Coffee</title>
    <link rel="stylesheet" href="Checkout.css">
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
                    <td><?= (int) $item['qty'] ?></td>
                    <td>Rp<?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td>Rp<?= number_format($item['harga'] * $item['qty'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="total">Total Harga</td>
                <td class="total">Rp<?= number_format($totalHarga, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Tombol Bayar -->
    <form method="post" action="Checkout.php" style="margin-top: 20px;">
        <button type="submit" name="bayar" class="btn btn-success">Bayar Sekarang</button>
    </form>

    <p>Terima kasih telah berbelanja di Fore Coffee.</p>
    <p><a href="home.php">Kembali ke Home</a></p>

</body>

</html>