<?php
include '../connection.php';

function clean_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_menu = clean_input($_POST['nama_menu']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $jenis = clean_input($_POST['jenis']);
    $modal = intval($_POST['modal']);

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_name = basename($_FILES['gambar']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_ext, $allowed)) {
            die("Format gambar tidak didukung. Gunakan JPG, PNG, atau GIF.");
        }

        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $new_file_name = uniqid('menu_', true) . '.' . $file_ext;
        $upload_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            $stmt = $connect->prepare("INSERT INTO menu (nama_menu, deskripsi, harga, gambar, stok, jenis, modal) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisisi", $nama_menu, $deskripsi, $harga, $new_file_name, $stok, $jenis, $modal);

            if ($stmt->execute()) {
                echo "Menu berhasil ditambahkan! <a href='FormAddMenu.php'>Tambah menu lain</a>";
            } else {
                echo "Error saat menyimpan data: " . $stmt->error;
                if (file_exists($upload_path)) unlink($upload_path);
            }
            $stmt->close();
        } else {
            echo "Gagal meng-upload gambar.";
        }
    } else {
        echo "Gambar harus diupload.";
    }
} else {
    echo "Metode request tidak valid.";
    header("Location: ../Admin/FormAddMenu.php");
}

$connect->close();
?>
