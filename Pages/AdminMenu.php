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
    <link rel="stylesheet" href="StyleHome.css">
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
    <nav>
        <a href="home.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
        <?php if (isset($_SESSION['id_user'])): ?>
            <a href="../LoginRegister/Logout.php">Logout</a>
        <?php else: ?>
            <a href="../LoginRegister/FormRegister.html">Register</a>
            <a href="../LoginRegister/FormLogin.html">Login</a>
            <script>
                window.onload = function () {
                    closeModal(); // agar pop-up tidak muncul terus-terusan
                }
            </script>
        <?php endif; ?>
    </nav>

    <h1>Selamat Datang, Admin <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>

    <div class="menu-container">
        <a href="../Admin/KelolaMenu.php" class="menu-item">Kelola Menu</a>
        <a href="../Admin/KelolaUsers.php" class="menu-item">Kelola Pengguna</a>
        <a href="../Admin/KelolaJamOperasional.php" class="menu-item">Kelola Jam Operasional</a>
        <a href="../Admin/Laporan.php" class="menu-item">Lihat Laporan</a>
        <!-- <a href="../Admin/Pengaturan.php" class="menu-item">Pengaturan Sistem</a> -->
    </div>

</body>

</html>