<?php
session_start();
include '../connection.php';

date_default_timezone_set('Asia/Jakarta'); 

$email = $_POST['email'];
$password = $_POST['password'];
$hashed_password = md5($password); // Saran: ganti ke password_hash() untuk keamanan lebih baik

// Ambil jam operasional toko
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

// Login sebagai user (customer atau admin)
$sql_user = "SELECT * FROM users WHERE email = '$email' AND password = '$hashed_password'";
$result_user = mysqli_query($connect, $sql_user);

if (mysqli_num_rows($result_user) === 1) {
    $user = mysqli_fetch_assoc($result_user);
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] === 'admin') {
        header("Location: ../Admin/AdminMenu.php");
    } else {
        header("Location: ../Pages/Home.php");
    }
    exit();
}

// Login sebagai kurir
$sql_kurir = "SELECT * FROM kurir WHERE email = '$email' AND password = '$hashed_password'";
$result_kurir = mysqli_query($connect, $sql_kurir);

if (mysqli_num_rows($result_kurir) === 1) {
    $kurir = mysqli_fetch_assoc($result_kurir);
    $_SESSION['id_kurir'] = $kurir['id_kurir'];
    $_SESSION['nama_kurir'] = $kurir['nama_kurir'];
    $_SESSION['role'] = 'kurir';

    header("Location: ../Pages/OrderanKurir.php"); // ⬅ Halaman khusus kurir
    exit();
}

// Jika tidak ditemukan
echo ("<script>
    alert('Email atau password salah!');
    window.location.href='FormLogin.html';
</script>");
?>
