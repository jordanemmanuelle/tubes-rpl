<?php
session_start();

// Cek apakah user sudah login dan merupakan admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    // Jika bukan admin, arahkan ke halaman login
    header("Location: ../LoginRegister/FormLogin.html");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../Pages/StyleHome.css">
    <title>Admin Menu</title>
    <style>
        h1 {
            color: #333;
            text-align: center;
        }

        .menu-container {
            margin-top: 30px;
        }

        .menu-item {
            display: block;
            margin: 10px auto;
            padding: 12px 24px;
            width: 250px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        .menu-item:hover {
            background-color: #0056b3;
        }

      
    </style>
</head>

<body>

    <header>
        <h1>Fore Coffee</h1>
        <p>Your favorite coffee shop</p>
    </header>


    <h1>Selamat Datang, Admin <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>

    <div class="menu-container">
        <a href="../Admin/KelolaMenu.php" class="menu-item">Kelola Menu</a>
        <a href="../Admin/KelolaUsers.php" class="menu-item">Kelola Pengguna</a>
        <a href="../Admin/KelolaJamOperasional.php" class="menu-item">Kelola Jam Operasional</a>
        <a href="../Admin/Laporan.php" class="menu-item">Lihat Laporan</a>
        <a href="../Admin/Kurir.php" class="menu-item">Kurir</a>
        <!-- <a href="../Admin/Pengaturan.php" class="menu-item">Pengaturan Sistem</a> -->
    </div>


    <a href="../Pages/Home.php" style="
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
">Go to Login Page</a>



</body>

</html>