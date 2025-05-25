<?php
session_start();
include '../connection.php'; // Pastikan ini file koneksi ke database kamu

// Ambil jam operasional dari database
$sql_jam = "SELECT jam_buka, jam_tutup FROM jam_operasional WHERE id = 1 LIMIT 1";
$result_jam = mysqli_query($connect, $sql_jam);

if ($result_jam && mysqli_num_rows($result_jam) > 0) {
    $row_jam = mysqli_fetch_assoc($result_jam);
    $jam_buka = $row_jam['jam_buka'];     // format: '07:00:00'
    $jam_tutup = $row_jam['jam_tutup'];   // format: '21:00:00'
} else {
    // Jika tidak ada data jam operasional, atur default (boleh juga diarahkan ke error page)
    $jam_buka = '07:00:00';
    $jam_tutup = '21:00:00';
}

// Waktu sekarang
date_default_timezone_set("Asia/Jakarta"); // Atur zona waktu ke WIB
$jam_sekarang = date('H:i:s');

// Cek apakah di luar jam operasional
if ($jam_sekarang < $jam_buka || $jam_sekarang >= $jam_tutup) {
    echo "<script>
        alert('Toko hanya buka dari jam $jam_buka sampai $jam_tutup. Silakan login saat jam operasional.');
        window.location.href = 'FormLogin.html';
    </script>";
    exit;
}

// Jika di jam operasional, proses login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek apakah user dengan email & password ada
    $sql = "SELECT id_user, nama FROM users WHERE email = ? AND password = ?";
    $stmt = mysqli_prepare($connect, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            // Login berhasil
            mysqli_stmt_bind_result($stmt, $id_user, $nama);
            mysqli_stmt_fetch($stmt);

            $_SESSION['id_user'] = $id_user;
            $_SESSION['nama'] = $nama;
            $_SESSION['logged_in'] = true;

            echo "<script>
                alert('Login berhasil! Selamat datang, $nama.');
                window.location.href = '../Pages/Home.php';
            </script>";
        } else {
            // Login gagal
            echo "<script>
                alert('Email atau password salah.');
                window.location.href = 'FormLogin.html';
            </script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Terjadi kesalahan pada query login.";
    }
}
?>
