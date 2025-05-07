<?php
    include 'FormRegister.html';

    $connect = mysqli_connect("localhost", "root", "", "foretubes");

    if (mysqli_connect_errno()) {
        echo (mysqli_connect_error());
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = md5($password); // ini di-hash

    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

    if (mysqli_stmt_execute($stmt)) {
        echo "Berhasil Daftar!";
    } else {
        echo "Error: " . mysqli_error($connect);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connect);
?>