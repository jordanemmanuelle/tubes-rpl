<?php
include '../connection.php';

// Handle tambah promo
if (isset($_POST['add'])) {
    $nama_promo = $_POST['nama_promo'];
    $deskripsi = $_POST['deskripsi'];
    $kode_promo = $_POST['kode_promo'];
    $jenis_diskon = $_POST['jenis_diskon'];
    $nilai_diskon = $_POST['nilai_diskon'];
    $minimal_transaksi = $_POST['minimal_transaksi'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_berakhir = $_POST['tanggal_berakhir'];
    $aktif = $_POST['aktif'] ?? 0;

    $stmt = $connect->prepare("INSERT INTO promo (nama_promo, deskripsi, kode_promo, jenis_diskon, nilai_diskon, minimal_transaksi, tanggal_mulai, tanggal_berakhir, aktif) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssddssi", $nama_promo, $deskripsi, $kode_promo, $jenis_diskon, $nilai_diskon, $minimal_transaksi, $tanggal_mulai, $tanggal_berakhir, $aktif);

    if ($stmt->execute()) {
        echo "<script>alert('Promo berhasil ditambahkan'); window.location.href='KelolaPromo.php';</script>";
        exit;
    } else {
        echo "Gagal menambahkan promo: " . $stmt->error;
    }
    $stmt->close();
}

// Handle update promo
if (isset($_POST['update'])) {
    $id = $_POST['id_promo'];
    $nama_promo = $_POST['nama_promo'];
    $deskripsi = $_POST['deskripsi'];
    $kode_promo = $_POST['kode_promo'];
    $jenis_diskon = $_POST['jenis_diskon'];
    $nilai_diskon = $_POST['nilai_diskon'];
    $minimal_transaksi = $_POST['minimal_transaksi'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_berakhir = $_POST['tanggal_berakhir'];
    $aktif = $_POST['aktif'] ?? 0;

    $stmt = $connect->prepare("UPDATE promo SET nama_promo=?, deskripsi=?, kode_promo=?, jenis_diskon=?, nilai_diskon=?, minimal_transaksi=?, tanggal_mulai=?, tanggal_berakhir=?, aktif=? WHERE id_promo=?");
    $stmt->bind_param("ssssddssii", $nama_promo, $deskripsi, $kode_promo, $jenis_diskon, $nilai_diskon, $minimal_transaksi, $tanggal_mulai, $tanggal_berakhir, $aktif, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Promo berhasil diupdate'); window.location.href='KelolaPromo.php';</script>";
        exit;
    } else {
        echo "Gagal update promo: " . $stmt->error;
    }
    $stmt->close();
}

// Handle hapus promo
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_sql = "DELETE FROM promo WHERE id_promo = $id";
    if (mysqli_query($connect, $delete_sql)) {
        echo "<script>alert('Promo berhasil dihapus'); window.location.href='KelolaPromo.php';</script>";
        exit;
    } else {
        echo "Gagal hapus promo: " . mysqli_error($connect);
    }
}

// Ambil data promo untuk ditampilkan
$sql = "SELECT * FROM promo ORDER BY id_promo DESC";
$result = mysqli_query($connect, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Promo</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #eee; }
        .btn-delete { color: red; }
        .btn-edit { color: blue; }
    </style>
</head>
<body>

<h2>Kelola Promo</h2>

<!-- Form Tambah Promo -->
<?php if (!isset($_GET['edit'])): ?>
<h3>Tambah Promo</h3>
<form method="POST">
    <label>Nama Promo:</label><br>
    <input type="text" name="nama_promo" required><br><br>

    <label>Deskripsi:</label><br>
    <textarea name="deskripsi" rows="3" required></textarea><br><br>

    <label>Kode Promo:</label><br>
    <input type="text" name="kode_promo" required><br><br>

    <label>Jenis Diskon:</label><br>
    <select name="jenis_diskon" required>
        <option value="persen">Persen</option>
        <option value="nominal">Nominal</option>
    </select><br><br>

    <label>Nilai Diskon:</label><br>
    <input type="number" step="0.01" name="nilai_diskon" required><br><br>

    <label>Minimal Transaksi:</label><br>
    <input type="number" step="0.01" name="minimal_transaksi" required><br><br>

    <label>Tanggal Mulai:</label><br>
    <input type="date" name="tanggal_mulai" required><br><br>

    <label>Tanggal Berakhir:</label><br>
    <input type="date" name="tanggal_berakhir" required><br><br>

    <label>Aktif:</label><br>
    <select name="aktif">
        <option value="1">Ya</option>
        <option value="0">Tidak</option>
    </select><br><br>

    <button type="submit" name="add">Tambah Promo</button>
</form>
<hr>
<?php endif; ?>

<?php
// Jika mode edit
if (isset($_GET['edit'])):
    $id = $_GET['edit'];
    $edit_sql = "SELECT * FROM promo WHERE id_promo = $id";
    $edit_result = mysqli_query($connect, $edit_sql);
    $promo = mysqli_fetch_assoc($edit_result);
?>
<h3>Edit Promo</h3>
<form method="POST">
    <input type="hidden" name="id_promo" value="<?= $promo['id_promo'] ?>">
    
    <label>Nama Promo:</label><br>
    <input type="text" name="nama_promo" value="<?= htmlspecialchars($promo['nama_promo']) ?>" required><br><br>

    <label>Deskripsi:</label><br>
    <textarea name="deskripsi" rows="3" required><?= htmlspecialchars($promo['deskripsi']) ?></textarea><br><br>

    <label>Kode Promo:</label><br>
    <input type="text" name="kode_promo" value="<?= htmlspecialchars($promo['kode_promo']) ?>" required><br><br>

    <label>Jenis Diskon:</label><br>
    <select name="jenis_diskon" required>
        <option value="persen" <?= $promo['jenis_diskon'] == 'persen' ? 'selected' : '' ?>>Persen</option>
        <option value="nominal" <?= $promo['jenis_diskon'] == 'nominal' ? 'selected' : '' ?>>Nominal</option>
    </select><br><br>

    <label>Nilai Diskon:</label><br>
    <input type="number" step="0.01" name="nilai_diskon" value="<?= $promo['nilai_diskon'] ?>" required><br><br>

    <label>Minimal Transaksi:</label><br>
    <input type="number" step="0.01" name="minimal_transaksi" value="<?= $promo['minimal_transaksi'] ?>" required><br><br>

    <label>Tanggal Mulai:</label><br>
    <input type="date" name="tanggal_mulai" value="<?= $promo['tanggal_mulai'] ?>" required><br><br>

    <label>Tanggal Berakhir:</label><br>
    <input type="date" name="tanggal_berakhir" value="<?= $promo['tanggal_berakhir'] ?>" required><br><br>

    <label>Aktif:</label><br>
    <select name="aktif">
        <option value="1" <?= $promo['aktif'] ? 'selected' : '' ?>>Ya</option>
        <option value="0" <?= !$promo['aktif'] ? 'selected' : '' ?>>Tidak</option>
    </select><br><br>

    <button type="submit" name="update">Simpan Perubahan</button>
    <a href="KelolaPromo.php">Batal</a>
</form>
<hr>
<?php endif; ?>

<!-- Tabel Data Promo -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Promo</th>
            <th>Deskripsi</th>
            <th>Kode Promo</th>
            <th>Jenis Diskon</th>
            <th>Nilai Diskon</th>
            <th>Minimal Transaksi</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Berakhir</th>
            <th>Aktif</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($promo = mysqli_fetch_assoc($result)) : ?>
        <tr>
            <td><?= $promo['id_promo'] ?></td>
            <td><?= htmlspecialchars($promo['nama_promo']) ?></td>
            <td><?= htmlspecialchars($promo['deskripsi']) ?></td>
            <td><?= htmlspecialchars($promo['kode_promo']) ?></td>
            <td><?= htmlspecialchars($promo['jenis_diskon']) ?></td>
            <td><?= $promo['nilai_diskon'] ?></td>
            <td><?= $promo['minimal_transaksi'] ?></td>
            <td><?= $promo['tanggal_mulai'] ?></td>
            <td><?= $promo['tanggal_berakhir'] ?></td>
            <td><?= $promo['aktif'] ? 'Ya' : 'Tidak' ?></td>
            <td>
                <a class="btn-edit" href="?edit=<?= $promo['id_promo'] ?>">Edit</a> |
                <a class="btn-delete" href="?delete=<?= $promo['id_promo'] ?>" onclick="return confirm('Yakin hapus promo ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<a href="AdminMenu.php" style="
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
