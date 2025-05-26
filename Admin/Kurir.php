<?php
    session_start();
    include '../connection.php';

    if (isset($_POST['submit'])) {  
        $nama_kurir = mysqli_real_escape_string($connect, $_POST['nama_kurir']);
        $telepon = mysqli_real_escape_string($connect, $_POST['telepon']);
        $biaya = (int) $_POST['biaya'];

        if ($nama_kurir && $biaya > 0) {
            $query = "INSERT INTO kurir (nama_kurir, telepon, biaya, status) 
                      VALUES ('$nama_kurir', '$telepon', '$biaya', 'available')";
            if (mysqli_query($connect, $query)) {
                echo "<script>alert('Kurir berhasil ditambahkan!'); window.location='Kurir.php';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan kurir: " . mysqli_error($connect) . "');</script>";
            }
        } else {
            echo "<script>alert('Nama kurir dan biaya wajib diisi!');</script>";
        }
    }

    if (isset($_POST['id_kurir']) && isset($_POST['status'])) {
        $id_kurir = intval($_POST['id_kurir']);
        $status = $_POST['status'] === 'available' ? 'available' : 'unavailable';
        mysqli_query($connect, "UPDATE kurir SET status = '$status' WHERE id_kurir = $id_kurir");
        echo "<script>window.location='Kurir.php';</script>";
        exit;
    }

    $kurirResult = mysqli_query($connect, "SELECT * FROM kurir");

    if (!$kurirResult) {
        die("Query Error: " . mysqli_error($connect));
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Kurir - Admin</title>
    <link rel="stylesheet" href="Kurir.css">
    <style>
        table, th, td {
            border: 1px solid #333;
            border-collapse: collapse;
            padding: 8px;
        }
        table {
            margin-top: 20px;
            width: 100%;
        }
        .center {
            text-align: center;
        }
        form {
            margin-bottom: 30px;
        }
        input[type="text"], input[type="number"] {
            padding: 5px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    
    <h2 class="center">Tambah Kurir Baru</h2>

    <form method="post" action="">
        <label>Nama Kurir:</label><br>
        <input type="text" name="nama_kurir" required><br>

        <label>Telepon:</label><br>
        <input type="text" name="telepon" placeholder="Wajib" required><br>

        <label>Biaya Pengiriman (Rp):</label><br>
        <input type="number" name="biaya" required min="1000"><br><br>

        <button type="submit" name="submit">Tambah Kurir</button>
    </form>

    <h3>Daftar Kurir</h3>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Kurir</th>
                <th>Telepon</th>
                <th>Biaya</th>
                <th>Status</th>
                <th>Ubah Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($kurir = mysqli_fetch_assoc($kurirResult)): ?>
                <tr>
                    <td><?= $kurir['id_kurir'] ?></td>
                    <td><?= htmlspecialchars($kurir['nama_kurir']) ?></td>
                    <td><?= htmlspecialchars($kurir['telepon']) ?></td>
                    <td>Rp<?= number_format($kurir['biaya'], 0, ',', '.') ?></td>
                    <td><?= $kurir['status'] ?></td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="id_kurir" value="<?= $kurir['id_kurir'] ?>">
                            <input type="hidden" name="status" value="<?= $kurir['status'] === 'available' ? 'unavailable' : 'available' ?>">
                            <input type="checkbox" onchange="this.form.submit()" <?= $kurir['status'] === 'available' ? 'checked' : '' ?>>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
