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

        // Hitung total dan cek stok
        foreach ($_SESSION['cart'] as $item) {
            $id_menu = $item['id'];
            $qty = $item['qty'];

            $result = mysqli_query($connect, "SELECT stok FROM menu WHERE id_menu = '$id_menu'");
            if (!$result) {
                die("Query error: " . mysqli_error($connect));
            }
            $data = mysqli_fetch_assoc($result);

            if (!$data || $data['stok'] < $qty) {
                echo "<script>alert('Stok untuk {$item['nama_menu']} tidak mencukupi!'); window.location='Home.php';</script>";
                exit;
            }

            $total += $item['harga'] * $qty;
        }

        // Ambil metode pengambilan dari form
        $metode = $_POST['metode'] ?? 'Pick Up';

        if ($metode === 'Delivery') {
            // Cari kurir yang available
            $kurirResult = mysqli_query($connect, "SELECT * FROM kurir WHERE status = 'available' LIMIT 1");
            if (!$kurirResult) {
                die("Query error: " . mysqli_error($connect));
            }
            $kurir = mysqli_fetch_assoc($kurirResult);

            if (!$kurir) {
                echo "<script>alert('Maaf, tidak ada kurir yang tersedia saat ini. Silakan pilih Pick Up.'); window.location='Checkout.php';</script>";
                exit;
            }

            // Tambahkan biaya kurir ke total
            $total += $kurir['biaya'];

            // Simpan data transaksi sementara ke session untuk halaman selanjutnya
            $_SESSION['kurir'] = $kurir;
            $_SESSION['total'] = $total;
            $_SESSION['metode'] = $metode;

            header("Location: Delivery.php");
            exit;
        } else {
            // Pick Up
            $tanggal = date('Y-m-d H:i:s');
            $query = "INSERT INTO transaksi (id_user, total, tanggal, metode_pengambilan) 
                      VALUES ('$id_user', '$total', '$tanggal', 'Pick Up')";
            if (!mysqli_query($connect, $query)) {
                die("Error insert transaksi: " . mysqli_error($connect));
            }
            $id_transaksi = mysqli_insert_id($connect);

            foreach ($_SESSION['cart'] as $item) {
                $id_menu = $item['id'];
                $qty = $item['qty'];
                $harga = $item['harga'];

                $query_detail = "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga) 
                                 VALUES ('$id_transaksi', '$id_menu', '$qty', '$harga')";
                if (!mysqli_query($connect, $query_detail)) {
                    die("Error insert detail transaksi: " . mysqli_error($connect));
                }

                if (!mysqli_query($connect, "UPDATE menu SET stok = stok - $qty WHERE id_menu = '$id_menu'")) {
                    die("Error update stok: " . mysqli_error($connect));
                }
            }

            unset($_SESSION['cart']);
            unset($_SESSION['kurir']);
            unset($_SESSION['total']);
            unset($_SESSION['metode']);

            echo "<script>
                    alert('Pembayaran berhasil! Terima kasih telah memilih Pick Up.');
                    localStorage.removeItem('cart');
                    window.location='Home.php';
                  </script>";
            exit;
        }
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
    <link rel="stylesheet" href="Checkout.css" />
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
        <p><strong>Pilih Metode Pengambilan:</strong></p>
        <label>
            <input type="radio" name="metode" value="Delivery" required> Delivery
        </label>
        <label style="margin-left: 20px;">
            <input type="radio" name="metode" value="Pick Up"> Pick Up
        </label>

        <br /><br />
        <button type="submit" name="bayar" class="btn btn-success">Bayar</button>
    </form>
    <p><a href="home.php">Back</a></p>

</body>

</html>
