<?php
session_start();

// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = ""; // Ganti jika pakai password
$db   = "foretubes"; // Nama database kamu

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fore Coffee - Home</title>
    <link rel="stylesheet" href="StyleHome.css" />
</head>
<body>

    <header>
        <h1>Fore Coffee</h1>
        <p>Your favorite coffee shop</p>
    </header>

    <nav>
        <a href="home.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
        <?php if (isset($_SESSION['id_user'])): ?>
            <a href="../LoginRegister/Logout.php">Logout</a>
        <?php else: ?>
            <a href="../LoginRegister/FormRegister.html">Register</a>
            <a href="../LoginRegister/FormLogin.html">Login</a>
        <?php endif; ?>
    </nav>

    <main>
        <section class="welcome-message">
            <h2>Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Guest'); ?>!</h2>
            <p>Explore our delicious coffee collection and more!</p>
        </section>

        <section class="products">
            <?php
            $sql = "SELECT * FROM menu ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                <div class="product-card">
                    <img src="../Menu/uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama_menu']); ?>">
                    <div class="product-info">
                        <h3><?= htmlspecialchars($row['nama_menu']); ?></h3>
                        <p><?= htmlspecialchars($row['deskripsi']); ?></p>
                        <p><strong>Jenis:</strong> <?= ucfirst($row['jenis']); ?></p>
                        <p>Rp<?= number_format($row['harga'], 0, ',', '.'); ?></p>
                
                    </div>
                    <button <?= $row['stok'] == 0 ? 'disabled' : ''; ?>>
                        <?= $row['stok'] == 0 ? 'Out of Stock' : 'Order Now'; ?>
                    </button>
                </div>
            <?php
                endwhile;
            else:
                echo "<p>Belum ada menu tersedia.</p>";
            endif;
            ?>
        </section>
    </main>

    <footer>&copy; 2025 Fore Coffee. All rights reserved.</footer>

</body>
</html>

<?php $conn->close(); ?>
