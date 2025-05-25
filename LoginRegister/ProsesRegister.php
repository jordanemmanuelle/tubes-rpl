<?php
  include '../connection.php';
  
  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['name'], $_POST['email'], $_POST['password'])) {
      $name = $_POST['name'];
      $email = $_POST['email'];
      $password = $_POST['password'];
      $hashed_password = md5($password); 

      $sql = "INSERT INTO users (name, email, password)
      VALUES ('$name', '$email', '$hashed_password')";

      if (mysqli_query($connect, $sql)) {
        echo ("<script>
        alert('Registrasi Berhasil!');
        window.location.href='FormLogin.html';
        </script>");
      } else {
        echo ("<script>
        alert('Registrasi Gagal!');  
        window.location.href='FormRegister.html';
        </script>");
      }
    } else {
      echo ("Isi semua data!");
    }
  }
?>
