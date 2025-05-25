<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Tambah Menu</title>
  <style>
  * {
    box-sizing: border-box;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f2f2f2;
    margin: 0;
    padding: 40px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
  }

  form {
    background-color: #fff;
    padding: 30px;
    width: 100%;
    max-width: 450px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  }

  h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #333;
  }

  label {
    margin-top: 15px;
    font-weight: 600;
    color: #333;
    display: block;
  }

  input[type="text"],
  input[type="number"],
  input[type="file"],
  textarea,
  select {
    width: 100%;
    padding: 10px 12px;
    margin-top: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
    transition: border 0.3s ease;
  }

  input:focus,
  textarea:focus,
  select:focus {
    border-color: #007BFF;
    outline: none;
  }

  textarea {
    resize: vertical;
  }

  button {
    margin-top: 25px;
    width: 100%;
    background-color: #007BFF;
    color: white;
    padding: 12px;
    border: none;
    font-size: 16px;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  button:hover {
    background-color: #0056b3;
  }

  @media (max-width: 600px) {
    body {
      padding: 20px;
    }

    form {
      padding: 20px;
      max-width: 100%;
      box-shadow: none;
      border-radius: 0;
    }

    button {
      font-size: 14px;
      padding: 10px;
    }
  }
</style>

</head>

<body>

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