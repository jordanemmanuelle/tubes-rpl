<?php
include '../connection.php';

// Handle update menu
if (isset($_POST['update'])) {
    $id = $_POST['id_menu'];
    $nama_menu = $_POST['nama_menu'];
    $deskripsi = $_POST['deskripsi'];
    $modal = $_POST['modal'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $jenis = $_POST['jenis'];

    $update_sql = "UPDATE menu SET 
        nama_menu = '$nama_menu',
        deskripsi = '$deskripsi',
        modal = $modal,
        harga = $harga,
        stok = $stok,
        jenis = '$jenis'
        WHERE id_menu = $id";

    if (mysqli_query($connect, $update_sql)) {
        echo "<script>alert('Menu berhasil diupdate'); window.location.href='KelolaMenu.php';</script>";
        exit;
    } else {
        echo "Gagal update data menu: " . mysqli_error($connect);
    }
}

// Handle hapus menu
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_sql = "DELETE FROM menu WHERE id_menu = $id";
    if (mysqli_query($connect, $delete_sql)) {
        echo "<script>alert('Menu berhasil dihapus'); window.location.href='KelolaMenu.php';</script>";
        exit;
    } else {
        echo "Gagal hapus menu: " . mysqli_error($connect);
    }
}

// Ambil data menu
$sql = "SELECT * FROM menu ORDER BY created_at DESC";
$result = mysqli_query($connect, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Menu</title>
    <link rel="stylesheet" href="KelolaMenu.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 30px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin: 2px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-edit {
            color: white;
            background-color: #007bff;
        }

        .btn-delete {
            color: white;
            background-color: #dc3545;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
        }

        form {
            margin-top: 20px;
            margin-bottom: 40px;
        }

        label {
            display: block;
            margin-top: 12px;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            box-sizing: border-box;
        }

        h2, h3 {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<h2>Kelola Menu</h2>

<?php
// Jika sedang mode edit
if (isset($_GET['edit'])):
    $id = $_GET['edit'];
    $edit_sql = "SELECT * FROM menu WHERE id_menu = $id";
    $edit_result = mysqli_query($connect, $edit_sql);
    $menu = mysqli_fetch_assoc($edit_result);
?>
    <h3>Edit Menu</h3>
    <form method="POST">
        <input type="hidden" name="id_menu" value="<?= $menu['id_menu'] ?>">

        <label for="nama_menu">Nama Menu</label>
        <input type="text" name="nama_menu" value="<?= htmlspecialchars($menu['nama_menu']) ?>" required>

        <label for="deskripsi">Deskripsi</label>
        <textarea name="deskripsi" rows="3" required><?= htmlspecialchars($menu['deskripsi']) ?></textarea>

        <label for="modal">Modal</label>
        <input type="number" name="modal" value="<?= $menu['modal'] ?>" required>

        <label for="harga">Harga</label>
        <input type="number" name="harga" value="<?= $menu['harga'] ?>" required>

        <label for="stok">Stok</label>
        <input type="number" name="stok" value="<?= $menu['stok'] ?>" required>

        <label for="jenis">Jenis</label>
        <select name="jenis" required>
            <option value="makanan" <?= $menu['jenis'] == 'makanan' ? 'selected' : '' ?>>Makanan</option>
            <option value="minuman" <?= $menu['jenis'] == 'minuman' ? 'selected' : '' ?>>Minuman</option>
        </select>

        <button type="submit" name="update" class="btn btn-edit">Simpan Perubahan</button>
        <a href="KelolaMenu.php" class="btn btn-back">Batal</a>
    </form>
<?php else: ?>
    <h3>Tambah Menu</h3>
    <form action="ProsesAddMenu.php" method="POST" enctype="multipart/form-data">
        <label for="nama_menu">Nama Menu</label>
        <input type="text" name="nama_menu" required>

        <label for="deskripsi">Deskripsi</label>
        <textarea name="deskripsi" rows="3" required></textarea>

        <label for="modal">Modal</label>
        <input type="number" name="modal" required>

        <label for="harga">Harga</label>
        <input type="number" name="harga" required>

        <label for="gambar">Upload Gambar</label>
        <input type="file" name="gambar" accept="image/*" required>

        <label for="stok">Stok</label>
        <input type="number" name="stok" required>

        <label for="jenis">Jenis</label>
        <select name="jenis" required>
            <option value="makanan">Makanan</option>
            <option value="minuman">Minuman</option>
        </select>

        <button type="submit" class="btn btn-edit">Tambah Menu</button>
    </form>
<?php endif; ?>

<!-- Tabel Data Menu -->
<h3>Daftar Menu</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Menu</th>
            <th>Deskripsi</th>
            <th>Modal</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Jenis</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($menu = mysqli_fetch_assoc($result)) : ?>
        <tr>
            <td><?= $menu['id_menu'] ?></td>
            <td><?= htmlspecialchars($menu['nama_menu']) ?></td>
            <td><?= htmlspecialchars($menu['deskripsi']) ?></td>
            <td><?= $menu['modal'] ?></td>
            <td><?= $menu['harga'] ?></td>
            <td><?= $menu['stok'] ?></td>
            <td><?= $menu['jenis'] ?></td>
            <td>
                <?php if ($menu['gambar']) : ?>
                    <img src="../uploads/<?= $menu['gambar'] ?>" width="50">
                <?php else : ?>
                    Tidak ada
                <?php endif; ?>
            </td>
            <td>
                <a class="btn btn-edit" href="?edit=<?= $menu['id_menu'] ?>">Edit</a>
                <a class="btn btn-delete" href="?delete=<?= $menu['id_menu'] ?>" onclick="return confirm('Yakin hapus menu ini?')">Hapus</a>
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
