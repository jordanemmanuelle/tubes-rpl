<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Tambah Menu</title>
</head>

<body>
  <link rel="stylesheet" href="KelolaMenu.css">
  <form action="ProsesAddMenu.php" method="POST" enctype="multipart/form-data">
    <h2>Tambah Menu</h2>

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

    <button type="submit">Tambah Menu</button>

    <a href="../Pages/AdminMenu.php" style="
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

  </form>

</body>

</html>