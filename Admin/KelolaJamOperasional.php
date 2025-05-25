<?php
include '../connection.php';

if (isset($_POST['save'])) {
    $jam_buka = $_POST['jam_buka'];
    $jam_tutup = $_POST['jam_tutup'];

    // Update jam operasional (asumsi hanya 1 row)
    $update_sql = "UPDATE jam_operasional SET jam_buka = ?, jam_tutup = ? WHERE id = 1";
    $stmt = $connect->prepare($update_sql);
    $stmt->bind_param("ss", $jam_buka, $jam_tutup);
    
    if ($stmt->execute()) {
        echo "<script>alert('Jam operasional berhasil diperbarui'); window.location.href='KelolaJamOperasional.php';</script>";
        exit;
    } else {
        echo "Gagal update jam operasional: " . $stmt->error;
    }
}

$sql = "SELECT * FROM jam_operasional WHERE id = 1";
$result = mysqli_query($connect, $sql);
$jam = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Jam Operasional</title>
</head>
<body>
    <h2>Atur Jam Operasional</h2>
    <form method="POST">
        <label>Jam Buka:</label><br>
        <input type="time" name="jam_buka" value="<?= $jam['jam_buka'] ?>" required><br><br>

        <label>Jam Tutup:</label><br>
        <input type="time" name="jam_tutup" value="<?= $jam['jam_tutup'] ?>" required><br><br>

        <button type="submit" name="save">Simpan</button>
    </form>
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
</body>
</html>
