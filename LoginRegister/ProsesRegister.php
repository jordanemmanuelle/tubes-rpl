<?php
$connect = mysqli_connect("localhost", "root", "", "foretubes");

if (mysqli_connect_errno()) {
    echo "<script>
            alert('Gagal koneksi ke database!');
            window.location.href = 'FormRegister.html';
          </script>";
    exit();
}

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$hashed_password = md5($password); // disarankan pakai password_hash()

// Cek apakah email sudah terdaftar
$checkQuery = "SELECT email FROM users WHERE email = ?";
$stmtCheck = mysqli_prepare($connect, $checkQuery);
mysqli_stmt_bind_param($stmtCheck, "s", $email);
mysqli_stmt_execute($stmtCheck);
mysqli_stmt_store_result($stmtCheck);

if (mysqli_stmt_num_rows($stmtCheck) > 0) {
    echo "<script>
            alert('Email sudah digunakan!');
            window.location.href = 'FormRegister.html';
          </script>";
    exit();
}
mysqli_stmt_close($stmtCheck);

// Lanjut insert data baru
$sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>
            alert('Berhasil Daftar!');
            window.location.href = 'FormLogin.html';
          </script>";
} else {
    echo "<script>
            alert('Gagal mendaftar: " . addslashes(mysqli_error($connect)) . "');
            window.location.href = 'FormRegister.html';
          </script>";
}

mysqli_stmt_close($stmt);
mysqli_close($connect);
?>
