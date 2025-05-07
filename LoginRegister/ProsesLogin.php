<?php
    include 'FormLogin.html';
    session_start();

    $connect = mysqli_connect("localhost", "root", "", "foretubes");

    if (mysqli_connect_errno()) {
        echo (mysqli_connect_error());
    }

    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = md5($password);

    $sql = "SELECT * FROM users WHERE email ='$email' AND password ='$hashed_password' AND is_active = 1";
    $result = mysqli_query($connect, $sql);

    if (mysqli_num_rows($result) === 1) { // ngecek ada berapa data yang ada di table
        $logged_in_user = mysqli_fetch_assoc($result); // kalo cuma 1, get datanya

        // save ke session
        $_SESSION['id_user'] = $logged_in_user['id_user'];
        $_SESSION['name'] = $logged_in_user['name'];
        $_SESSION['email'] = $logged_in_user['email'];
        $_SESSION['role'] = $logged_in_user['role'];

        header("Location: ../Pages/Home.php");
        exit();
    } else {
        echo ("Email atau password salah!");
    }
?>