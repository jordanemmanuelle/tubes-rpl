<?php
session_start();

include '../connection.php';

$email = $_POST['email'];
$password = $_POST['password'];
$hashed_password = md5($password);

$sql = "SELECT * FROM users WHERE email ='$email' AND password ='$hashed_password'";
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
