<?php
$connect = mysqli_connect("localhost", "root", "", "foretubes");

if (!$connect) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    // validasi password cocok
    if ($newPassword !== $confirmPassword) {
        echo "<script>
                  alert('Password dan konfirmasi tidak sama!');
                window.history.back();
              </script>";
        exit();
    }

    // Cek apakah email ada dan ambil password lama
    $checkQuery = "SELECT password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($connect, $checkQuery);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        echo "<script>
                alert('Email tidak ditemukan!');
                window.history.back();
              </script>";
        exit();
    }

    $row = mysqli_fetch_assoc($result);
    $oldHashedPassword = $row["password"];

    // cek apakah password baru sama dengan yang lama 
    if (md5($newPassword) === $oldHashedPassword) {
        echo "<script>
                alert('Password tidak boleh sama seperti password yang lama!');
                window.history.back();
              </script>";
        exit();
    }

    // hash password baru biar konsisten dengan yang lama
    $hashedPassword = md5($newPassword);

    // Update password
    $updateQuery = "UPDATE users SET password = ? WHERE email = ?";
    $stmtUpdate = mysqli_prepare($connect, $updateQuery);
    mysqli_stmt_bind_param($stmtUpdate, "ss", $hashedPassword, $email);

    if (mysqli_stmt_execute($stmtUpdate)) {
        echo "<script>
                alert('Password berhasil direset!');
                window.location.href = 'FormLogin.html';
              </script>";
    } else {
        echo "<script>
                alert('Gagal reset password. Coba lagi!');
                window.history.back();
              </script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmtUpdate);
} else {
    echo "<script>
            alert('Request tidak valid.');
            window.history.back();
          </script>";
}

mysqli_close($connect);
?>
