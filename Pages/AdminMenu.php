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
    <title>Admin Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 40px;
            text-align: center;
        }
        h1 {
            color: #333;
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
        .logout {
            margin-top: 40px;
            color: red;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <h1>Selamat Datang, Admin <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>

    <div class="menu-container">
        <a href="KelolaUsers.php" class="menu-item">Kelola Pengguna</a>
        <a href="KelolaData.php" class="menu-item">Kelola Data</a>
        <a href="Laporan.php" class="menu-item">Lihat Laporan</a>
        <a href="Pengaturan.php" class="menu-item">Pengaturan Sistem</a>
    </div>

    <a href="../LoginRegister/Logout.php" class="logout">Logout</a>

</body>
</html>
