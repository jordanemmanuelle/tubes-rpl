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

// Ambil cart dari session dan hitung total harga
$cart = $_SESSION['cart'] ?? [];
$totalHarga = 0;
foreach ($cart as $item) {
    $totalHarga += $item['harga'] * $item['qty'];
}

// Ambil promo aktif dan valid tanggal sekarang
$today = date('Y-m-d');
$promoResult = mysqli_query($connect, "SELECT * FROM promo WHERE aktif = 1 AND tanggal_mulai <= '$today' AND tanggal_berakhir >= '$today'");
$promos = [];
while ($row = mysqli_fetch_assoc($promoResult)) {
    $promos[] = $row;
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

        // Ambil promo yang dipilih dan cek minimal transaksi
        $id_promo_terpilih = $_POST['promo'] ?? '';
        $promo_terpilih = null;
        if ($id_promo_terpilih) {
            foreach ($promos as $promo) {
                if ($promo['id_promo'] == $id_promo_terpilih) {
                    $promo_terpilih = $promo;
                    break;
                }
            }
            if ($promo_terpilih && $total < $promo_terpilih['minimal_transaksi']) {
                echo "<script>alert('Total transaksi belum memenuhi syarat minimal promo. Promo tidak digunakan.');</script>";
                $promo_terpilih = null; // batal pakai promo
            }
        }

        // Hitung potongan promo jika ada
        $potongan = 0;
        if ($promo_terpilih) {
            if ($promo_terpilih['jenis_diskon'] == 'persen') {
                $potongan = ($promo_terpilih['nilai_diskon'] / 100) * $total;
            } else { // potongan nominal
                $potongan = $promo_terpilih['nilai_diskon'];
                // jika potongan lebih besar dari total, batasi maksimal potongan
                if ($potongan > $total) $potongan = $total;
            }
        }

        $totalBayar = $total - $potongan;

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
            
            // Simpan data transaksi sementara ke session untuk halaman selanjutnya
            $_SESSION['kurir'] = $kurir;
            $_SESSION['total'] = $totalBayar;
            $_SESSION['metode'] = $metode;
            $_SESSION['promo'] = $promo_terpilih;

            header("Location: Delivery.php");
            exit;
        } else {
            // Pick Up
            $tanggal = date('Y-m-d H:i:s');
            $promo_id = $promo_terpilih ? $promo_terpilih['id_promo'] : null;
            $query = "INSERT INTO transaksi (id_user, total, tanggal, metode_pengambilan, id_promo) 
                      VALUES ('$id_user', '$totalBayar', '$tanggal', 'Pick Up', " . ($promo_id ? "'$promo_id'" : "NULL") . ")";
            if (!mysqli_query($connect, $query)) {
                die("Error insert transaksi: " . mysqli_error($connect));
            }
            $id_transaksi = mysqli_insert_id($connect);

            foreach ($_SESSION['cart'] as $item) {
                $id_menu = $item['id'];
                $qty = $item['qty'];
                $harga = $item['harga'];

                $query_detail = "INSERT INTO detail_transaksi (id_transaksi, id_menu, jumlah, harga, id_promo) 
                                 VALUES ('$id_transaksi', '$id_menu', '$qty', '$harga'," . ($promo_id ? "'$promo_id'" : "NULL") . ")";
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
            unset($_SESSION['promo']);

            echo "<script>
                    alert('Pembayaran berhasil! Terima kasih telah memilih Pick Up.');
                    localStorage.removeItem('cart');
                    window.location='Home.php';
                  </script>";
            exit;
        }
    }
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

    <form method="post" action="Checkout.php" style="margin-top: 20px;">

        <p><strong>Pilih Promo:</strong></p>
        <label for="promo">Promo:</label>
        <select name="promo" id="promo" onchange="updatePromoSelection()">
            <option value="">-- Tidak menggunakan promo --</option>
            <?php foreach ($promos as $promo): ?>
                <option value="<?= $promo['id_promo'] ?>"
                    <?= $totalHarga < $promo['minimal_transaksi'] ? 'disabled' : '' ?>>
                    <?= htmlspecialchars($promo['kode_promo']) ?> - 
                    <?php 
                        if ($promo['jenis_diskon'] == 'persen') {
                            echo $promo['nilai_diskon'] . '%';
                        } else {
                            echo 'Rp' . number_format($promo['nilai_diskon'], 0, ',', '.');
                        }
                    ?>
                    (Min. Rp<?= number_format($promo['minimal_transaksi'], 0, ',', '.') ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <p><strong>Pilih Metode Pengambilan:</strong></p>
        <label>
            <input type="radio" name="metode" value="Delivery" required> Delivery
        </label>
        <label>
            <input type="radio" name="metode" value="Pick Up"> Pick Up
        </label>

        <br /><br />
        <button type="submit" name="bayar" class="btn-main">Bayar</button>
        <a href="home.php" class="btn-main">Back</a>
    </form>

    <p><a href="home.php">Back</a></p>

    <script>
    function updatePromoSelection() {
        const select = document.getElementById('promo');
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption.disabled) {
            alert('Promo ini tidak bisa dipilih karena minimal transaksi belum terpenuhi.');
            select.value = ""; // reset ke tidak pakai promo
        }
    }
    </script>

</body>

</html>