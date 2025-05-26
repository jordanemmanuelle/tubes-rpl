<?php
    session_start();
    if (!isset($_SESSION['id_kurir'])) {
        header("Location: ../Login/FormLogin.html");
        exit();
    }
    echo "<h2>Selamat datang, Kurir " . htmlspecialchars($_SESSION['nama_kurir']) . "</h2>";
?>
