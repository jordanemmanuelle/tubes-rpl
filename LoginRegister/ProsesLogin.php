<?php
session_start();

$connect = mysqli_connect("localhost", "root", "", "foretubes");

if (mysqli_connect_errno()) {
    echo "<script>
            alert('Gagal koneksi ke database!');
            window.location.href = 'FormLogin.html';
          </script>";
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];
$hashed_password = md5($password);

$sql = "SELECT * FROM users WHERE email = ? AND password = ? AND is_active = 1";
$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, "ss", $email, $hashed_password);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 1) {
    $logged_in_user = mysqli_fetch_assoc($result);

    $_SESSION['id_user'] = $logged_in_user['id_user'];
    $_SESSION['name'] = $logged_in_user['name'];
    $_SESSION['email'] = $logged_in_user['email'];
    $_SESSION['role'] = $logged_in_user['role'];

    echo "<script>
            alert('Login berhasil!');
            window.location.href = '../Pages/Home.php';
          </script>";
    exit();
} else {
    echo "<script>
            alert('Email atau password salah!');
            window.location.href = 'FormLogin.html';
          </script>";
}

mysqli_stmt_close($stmt);
mysqli_close($connect);
?>
