    <?php
    session_start();
    include '../connection.php';

    // Validasi session data yang dibutuhkan
    if (!isset($_SESSION['cart'], $_SESSION['total'], $_SESSION['metode'], $_SESSION['kurir'])) {
        echo "<script>alert('Data transaksi tidak lengkap. Silakan checkout ulang.'); window.location='Checkout.php';</script>";
        exit;
    }

    $cart = $_SESSION['cart'];
    $total = (float)$_SESSION['total']; // Pastikan numerik
    $metode = $_SESSION['metode'];
    $kurir = $_SESSION['kurir'];
    $biaya_kurir = (float)$kurir['biaya']; // Pastikan numerik
    $promo = $_SESSION['promo'];
    //MASALAH ADA DI TOTAL

    // Proses form konfirmasi dan simpan transaksi
    if (isset($_POST['konfirmasi'])) {
        $nama_penerima = trim($_POST['nama_penerima'] ?? '');
        $alamat_penerima = trim($_POST['alamat_penerima'] ?? '');

        if ($nama_penerima === '' || $alamat_penerima === '') {
            echo "<script>alert('Nama penerima dan alamat penerima wajib diisi.');</script>";
        } else {
            $id_user = $_SESSION['id_user'] ?? 1;
            $tanggal = date('Y-m-d H:i:s');
            $id_kurir = $kurir['id_kurir'];

            // Tambahkan biaya kurir ke total
            $total_dengan_kurir = $total + $biaya_kurir;

            // Simpan ke tabel transaksi
            $query = "INSERT INTO transaksi (id_user, total, tanggal, metode_pengambilan, id_kurir) 
            VALUES (?, ?, ?, ?, ?)";
  
  $stmt = mysqli_prepare($connect, $query);
  if (!$stmt) {
      die("QUERY PREPARE FAILED: " . mysqli_error($connect));
  }
            mysqli_stmt_bind_param($stmt, "idssi", $id_user, $total_dengan_kurir, $tanggal, $metode, $id_kurir);
            if (!mysqli_stmt_execute($stmt)) {
                die("Error insert transaksi: " . mysqli_error($connect));
            }
            $id_transaksi = mysqli_insert_id($connect);
            mysqli_stmt_close($stmt);

            // Hitung total item untuk membagi biaya kurir
            $total_item = 0;
            foreach ($cart as $item) {
                $total_item += $item['qty'];
            }
            $biaya_per_item = $total_item > 0 ? ($biaya_kurir / $total_item) : 0;

            // Simpan detail_transaksi
            foreach ($cart as $item) {
                $id_menu = $item['id'];
                $qty = $item['qty'];

                $query_detail = "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga, nama_penerima, alamat_penerima, id_promo) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

                $stmt = mysqli_prepare($connect, $query_detail);
                $promo_id = is_array($promo) ? $promo['id_promo'] : null;
                mysqli_stmt_bind_param($stmt, "iiidssi", $id_transaksi, $id_menu, $qty, $total_dengan_kurir, $nama_penerima, $alamat_penerima, $promo_id);
                
                if (!mysqli_stmt_execute($stmt)) {
                    die("Error insert detail transaksi: " . mysqli_error($connect));
                }
                mysqli_stmt_close($stmt);

                // Update stok produk
                $update_stok = "UPDATE menu SET stok = stok - ? WHERE id_menu = ?";
                $stmt_update = mysqli_prepare($connect, $update_stok);
                mysqli_stmt_bind_param($stmt_update, "ii", $qty, $id_menu);
                if (!mysqli_stmt_execute($stmt_update)) {
                    var_dump($query);
                    die("Error insert transaksi: " . mysqli_error($connect));
                    
                }
                mysqli_stmt_close($stmt_update);
            }

            // Set kurir menjadi unavailable
            $update_kurir = "UPDATE kurir SET status = 'unavailable' WHERE id_kurir = ?";
            $stmt_kurir = mysqli_prepare($connect, $update_kurir);
            mysqli_stmt_bind_param($stmt_kurir, "i", $id_kurir);
            mysqli_stmt_execute($stmt_kurir);
            mysqli_stmt_close($stmt_kurir);

            // Bersihkan session checkout
            unset($_SESSION['cart'],$_SESSION['promo'], $_SESSION['kurir'], $_SESSION['total'], $_SESSION['metode']);

            echo "<script>
                    alert('Pembayaran dan pengiriman berhasil! Kurir akan segera mengantar pesanan Anda.');
                    localStorage.removeItem('cart');
                    window.location='Home.php';
                </script>";
            exit;
        }
    }
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Ringkasan Pengiriman - Fore Coffee</title>
        <link rel="stylesheet" href="Delivery.css">
    </head>
    <body>
        <h1>Ringkasan Pengiriman</h1>
        <?php if (isset($_SESSION['promo']) && $_SESSION['promo'] != 0): ?>





    <?php endif; ?>


        <h2>Kurir yang akan mengantar:</h2>
        <p><strong>Nama Kurir:</strong> <?= htmlspecialchars($kurir['nama_kurir']) ?></p>
        <p><strong>Nomor Telepon:</strong> <?= htmlspecialchars($kurir['telepon']) ?></p>
        <p><strong>Biaya Kurir:</strong> Rp<?= number_format($biaya_kurir, 0, ',', '.') ?></p>

        <h2>Pesanan Anda:</h2>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Nama Menu</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
                <th>Promo</th>
            </tr>

            <?php foreach ($cart as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nama_menu']) ?></td>
                    <td><?= (int)$item['qty'] ?></td>
                    
                    <td>Rp<?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td>Rp<?= number_format($item['harga'] * $item['qty'], 0, ',', '.') ?></td>
                    
                    <td><?= htmlspecialchars($promo['nilai_diskon']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4"><strong>Total Harga (Termasuk Ongkir : Rp<?= number_format($biaya_kurir, 0, ',', '.') ?>    )</strong><br></td>
                <td><strong>Rp<?= number_format($total + $biaya_kurir, 0, ',', '.') ?></strong></td>
            </tr>

        </table>

        <h2>Masukkan Data Pengiriman</h2>
        <form method="post" action="">
            <label for="nama_penerima">Nama Penerima:</label><br>
            <input type="text" id="nama_penerima" name="nama_penerima" required><br><br>

            <label for="alamat_penerima">Alamat Penerima:</label><br>
            <textarea id="alamat_penerima" name="alamat_penerima" rows="3" required></textarea><br><br>

            <button type="submit" name="konfirmasi">Konfirmasi dan Bayar</button>
            <a href="Checkout.php" style="margin-left: 10px;">Kembali ke Checkout</a>
        </form>
    </body>
    </html>
