<?php
session_start();
include '../connection.php';

date_default_timezone_set('Asia/Jakarta'); 

$email = $_POST['email'];
$password = $_POST['password'];
$hashed_password = md5($password); 

// Ambil jam operasional dari database
$jam_operasional_sql = "SELECT jam_buka, jam_tutup FROM jam_operasional LIMIT 1";
$jam_operasional_result = mysqli_query($connect, $jam_operasional_sql);

if ($jam_operasional_result && mysqli_num_rows($jam_operasional_result) === 1) {
    $jam_operasional = mysqli_fetch_assoc($jam_operasional_result);
    $jam_buka = $jam_operasional['jam_buka'];
    $jam_tutup = $jam_operasional['jam_tutup'];

    $now = date('H:i:s');

    if ($now < $jam_buka || $now > $jam_tutup) {
        echo ("<script>
            alert('Toko saat ini tutup. Silakan login pada jam operasional ($jam_buka - $jam_tutup)');
            window.location.href='FormLogin.html';
        </script>");
        exit();
    }
}

// Proses login
$sql = "SELECT * FROM users WHERE email = '$email' AND password = '$hashed_password'";
$result = mysqli_query($connect, $sql);

if (mysqli_num_rows($result) === 1) {
    $logged_in_user = mysqli_fetch_assoc($result);
    $_SESSION['id_user'] = $logged_in_user['id_user'];
    $_SESSION['name'] = $logged_in_user['name'];
    $_SESSION['role'] = $logged_in_user['role'];

    if ($logged_in_user['role'] == 'admin') {
        header("Location: ../Pages/AdminMenu.php");
        exit();
    }

    header("Location: ../Pages/Home.php");
    exit();
} else {
    echo ("<script>
        alert('Email atau password salah!');
        window.location.href='FormLogin.html';
    </script>");
}
?>
