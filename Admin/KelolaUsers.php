<?php
include '../connection.php';

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = md5($_POST['password']); // md5 hashing
    $is_active = $_POST['is_active'];

    $stmt = $connect->prepare("INSERT INTO users (name, email, password, role, is_active) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $email, $password, $role, $is_active);

    if ($stmt->execute()) {
        echo "<script>alert('User berhasil ditambahkan'); window.location.href='KelolaUsers.php';</script>";
        exit;
    } else {
        echo "Gagal menambahkan user: " . $stmt->error;
    }
    $stmt->close();
}

// Handle update
if (isset($_POST['update'])) {
    $id = $_POST['id_user'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $is_active = $_POST['is_active'];

    $password = isset($_POST['password']) && !empty($_POST['password']) ? md5($_POST['password']) : null;

    if ($password) {
        $update_sql = "UPDATE users SET 
            name = '$name',
            email = '$email',
            password = '$password',
            role = '$role',
            is_active = $is_active
            WHERE id_user = $id";
    } else {
        $update_sql = "UPDATE users SET 
            name = '$name',
            email = '$email',
            role = '$role',
            is_active = $is_active
            WHERE id_user = $id";
    }

    if (mysqli_query($connect, $update_sql)) {
        echo "<script>alert('User berhasil diupdate'); window.location.href='KelolaUsers.php';</script>";
        exit;
    } else {
        echo "Gagal update data user: " . mysqli_error($connect);
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_sql = "DELETE FROM users WHERE id_user = $id";
    if (mysqli_query($connect, $delete_sql)) {
        echo "<script>alert('User berhasil dihapus'); window.location.href='KelolaUsers.php';</script>";
        exit;
    } else {
        echo "Gagal hapus user: " . mysqli_error($connect);
    }
}

// Ambil data user untuk ditampilkan
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($connect, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola User</title>
    <link rel="stylesheet" href="KelolaUser.css">
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #eee; }
        .btn-delete { color: red; }
        .btn-edit { color: blue; }
    </style>
</head>
<body>

<h2>Kelola User</h2>

<!-- Form Tambah User -->
<h3>Tambah User</h3>
<form method="POST">
    <label>Nama:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Role:</label><br>
    <select name="role">
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select><br><br>

    <label>Status Aktif:</label><br>
    <select name="is_active">
        <option value="1">Aktif</option>
        <option value="0">Nonaktif</option>
    </select><br><br>

    <button type="submit" name="add">Tambah User</button>
</form>

<hr>

<?php
// Jika sedang mode edit
if (isset($_GET['edit'])):
    $id = $_GET['edit'];
    $edit_sql = "SELECT * FROM users WHERE id_user = $id";
    $edit_result = mysqli_query($connect, $edit_sql);
    $user = mysqli_fetch_assoc($edit_result);
?>

<h3>Edit User</h3>
<form method="POST">
    <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">
    
    <label>Nama:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

    <label>Password (kosong = ga ngubah):</label><br>
    <input type="password" name="password"><br><br>

    <label>Role:</label><br>
    <select name="role">
        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
    </select><br><br>

    <label>Status Aktif:</label><br>
    <select name="is_active">
        <option value="1" <?= $user['is_active'] ? 'selected' : '' ?>>Aktif</option>
        <option value="0" <?= !$user['is_active'] ? 'selected' : '' ?>>Nonaktif</option>
    </select><br><br>

    <button type="submit" name="update">Simpan Perubahan</button>
    <a href="KelolaUsers.php">Batal</a>
</form>

<hr>

<?php endif; ?>

<table>
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php while ($user = mysqli_fetch_assoc($result)) : ?>
        <tr>
            <td><?= $user['id_user'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= $user['role'] ?></td>
            <td><?= $user['is_active'] ? 'Aktif' : 'Nonaktif' ?></td>
            <td>
                <a class="btn-edit" href="?edit=<?= $user['id_user'] ?>">Edit</a> |
                <a class="btn-delete" href="?delete=<?= $user['id_user'] ?>" onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

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
