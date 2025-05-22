<?php
include '../connection.php';

function clean_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan bersihkan data form
    $nama_menu = clean_input($_POST['nama_menu']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $jenis = clean_input($_POST['jenis']);

    // Cek jika file gambar diupload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_name = basename($_FILES['gambar']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        // Validasi ekstensi file
        if (!in_array($file_ext, $allowed)) {
            die("Format gambar tidak didukung. Gunakan JPG, PNG, atau GIF.");
        }

        // Folder tujuan upload
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Buat nama file unik untuk menghindari overwrite
        $new_file_name = uniqid('menu_', true) . '.' . $file_ext;
        $upload_path = $upload_dir . $new_file_name;

        // Pindahkan file ke folder upload
        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Simpan data ke database
            $stmt = $conn->prepare("INSERT INTO menu (nama_menu, deskripsi, harga, gambar, stok, jenis) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisis", $nama_menu, $deskripsi, $harga, $new_file_name, $stok, $jenis);

            if ($stmt->execute()) {
                echo "Menu berhasil ditambahkan! <a href='ProsesAddMenu.php'>Tambah menu lain</a>";
            } else {
                echo "Error saat menyimpan data: " . $stmt->error;
                // Jika gagal, hapus file yang sudah terupload
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
    header("Location: ../Menu/FormAddMenu.php");
}

$conn->close();
?>
