<?php
    session_start();
    include '../connection.php';

    if (!isset($_SESSION['cart'], $_SESSION['total'], $_SESSION['metode'])) {
        echo "<script>alert('Data transaksi tidak lengkap. Silakan checkout ulang.'); window.location='Checkout.php';</script>";
        exit;
    }

    $cart = $_SESSION['cart'];
    $total = $_SESSION['total'];
    $metode = $_SESSION['metode'];
    $kurir = $_SESSION['kurir'] ?? null;

    if (isset($_POST['konfirmasi'])) {
        $id_user = $_SESSION['id_user'] ?? 1;
        $tanggal = date('Y-m-d H:i:s');

        $id_kurir = $kurir['id_kurir'] ?? null;
        $id_kurir_sql = $id_kurir ? "'$id_kurir'" : "NULL";

        $query = "INSERT INTO transaksi (id_user, total, tanggal, metode_pengambilan, id_kurir) 
                VALUES ('$id_user', '$total', '$tanggal', '$metode', $id_kurir_sql)";
        mysqli_query($connect, $query);
        $id_transaksi = mysqli_insert_id($connect);

        foreach ($cart as $item) {
            $id_menu = $item['id'];
            $qty = $item['qty'];
            $harga = $item['harga'];

            $query_detail = "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga) 
                            VALUES ('$id_transaksi', '$id_menu', '$qty', '$harga')";
            mysqli_query($connect, $query_detail);

            mysqli_query($connect, "UPDATE menu SET stok = stok - $qty WHERE id_menu = '$id_menu'");
        }

        if ($metode === 'Delivery' && $kurir) {
            mysqli_query($connect, "UPDATE kurir SET status = 'unavailable' WHERE id_kurir = '".$kurir['id_kurir']."'");
        }

        unset($_SESSION['cart'], $_SESSION['kurir'], $_SESSION['total'], $_SESSION['metode']);

        echo "<script>
                alert('Pembayaran dan pengiriman berhasil! Kurir akan segera mengantar pesanan Anda.');
                localStorage.removeItem('cart');
                window.location='Home.php';
            </script>";
            exit;
    }
?>

<!DOCTYPE html>
<html>
    <head><title>Ringkasan Pengiriman - Fore Coffee</title></head>
    <body>
        <h1>Ringkasan Pengiriman</h1>

        <?php if ($metode === 'Delivery' && $kurir): ?>
            <h2>Kurir yang akan mengantar:</h2>
            <p><strong>Nama Kurir:</strong> <?= htmlspecialchars($kurir['nama_kurir']) ?></p>
            <p><strong>Nomor Telepon:</strong> <?= htmlspecialchars($kurir['telepon']) ?></p>
        <?php else: ?>
            <p><strong>Metode Pengambilan:</strong> <?= htmlspecialchars($metode) ?></p>
        <?php endif; ?>

        <h2>Pesanan Anda:</h2>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Nama Menu</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>

            <?php foreach ($cart as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nama_menu']) ?></td>
                    <td><?= (int)$item['qty'] ?></td>
                    <td>Rp<?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td>Rp<?= number_format($item['harga'] * $item['qty'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Total Harga</strong></td>
                <td><strong>Rp<?= number_format($total, 0, ',', '.') ?></strong></td>
            </tr>
        </table>

        <form method="post" action="">
            <button type="submit" name="konfirmasi">Konfirmasi dan Bayar</button>
            <a href="Checkout.php" style="margin-left: 10px;">Kembali ke Checkout</a>
        </form>
    </body>
</html>
