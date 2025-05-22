<?php
session_start();

// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = ""; // Ganti jika ada password
$db   = "foretubes";

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
    <style>
        /* Tambahan untuk modal */
        .modal {
            position: fixed;
            z-index: 999;
            padding-top: 60px;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            display: none;
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            width: 90%;
            max-width: 500px;
            border-radius: 10px;
            position: relative;
        }

        .close-btn {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
        }

        .product-card button {
            margin-top: 10px;
            padding: 8px 12px;
        }
    </style>
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
                    <img src="../Menu/uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama_menu']); ?>" style="max-width: 100%; height: auto;">
                    <div class="product-info">
                        <h3><?= htmlspecialchars($row['nama_menu']); ?></h3>
                        <p><strong>Jenis:</strong> <?= ucfirst($row['jenis']); ?></p>
                        <p>Rp<?= number_format($row['harga'], 0, ',', '.'); ?></p>
                    </div>
                    <button 
                        <?= $row['stok'] == 0 ? 'disabled' : ''; ?>
                        onclick='openModal(<?= json_encode([
                            "id_menu" => $row["id_menu"],
                            "nama_menu" => $row["nama_menu"],
                            "deskripsi" => $row["deskripsi"],
                            "jenis" => $row["jenis"],
                            "harga" => $row["harga"],
                            "stok" => $row["stok"],
                            "gambar" => $row["gambar"]
                        ]); ?>)'
                    >
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

    <section class="cart-container" id="cartContainer">
    <h2>Keranjang Saya</h2>
    <ul id="cartItems">
        <li>Keranjang kosong.</li>
    </ul>
</section>


    <footer>&copy; 2025 Fore Coffee. All rights reserved.</footer>

    <!-- Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <img id="modalImage" src="" alt="Product Image" style="max-width: 100%; border-radius: 8px;">
            <h2 id="modalName"></h2>
            <p id="modalDescription"></p>
            <p><strong>Jenis:</strong> <span id="modalJenis"></span></p>
            <p><strong>Harga:</strong> Rp<span id="modalHarga"></span></p>
            <p><strong>Stok tersedia:</strong> <span id="modalStok"></span></p>

            <form id="addToCartForm">
                <label for="jumlah">Jumlah:</label>
                <input type="number" id="jumlah" name="jumlah" value="1" min="1" max="1">
                <input type="hidden" id="modalIdMenu" name="id_menu" />
                <br><br>
                <button type="submit">Tambah ke Keranjang</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(menu) {
            document.getElementById("modalImage").src = "../Menu/uploads/" + menu.gambar;
            document.getElementById("modalName").textContent = menu.nama_menu;
            document.getElementById("modalDescription").textContent = menu.deskripsi;
            document.getElementById("modalJenis").textContent = menu.jenis;
            document.getElementById("modalHarga").textContent = Number(menu.harga).toLocaleString('id-ID');
            document.getElementById("modalStok").textContent = menu.stok;
            document.getElementById("jumlah").max = menu.stok;
            document.getElementById("jumlah").value = 1;
            document.getElementById("modalIdMenu").value = menu.id_menu;

            document.getElementById("orderModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("orderModal").style.display = "none";
        }

        document.getElementById("addToCartForm").addEventListener("submit", function(e) {
            e.preventDefault();
            // Simulasi penambahan ke keranjang
            alert("Berhasil ditambahkan ke keranjang!");
            closeModal();

            // Atau kirim ke file PHP: keranjang.php dengan AJAX jika ingin sungguhan
        });
    </script>


<script>
    let cart = [];

    function updateCartUI() {
        const cartItemsEl = document.getElementById("cartItems");
        cartItemsEl.innerHTML = "";

        if (cart.length === 0) {
            cartItemsEl.innerHTML = "<li>Keranjang kosong.</li>";
            return;
        }

        cart.forEach(item => {
            const li = document.createElement("li");
            li.textContent = `${item.nama_menu} (${item.jumlah}x) - Rp${(item.harga * item.jumlah).toLocaleString('id-ID')}`;
            cartItemsEl.appendChild(li);
        });
    }

    document.getElementById("addToCartForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const id_menu = document.getElementById("modalIdMenu").value;
        const nama_menu = document.getElementById("modalName").textContent;
        const harga = parseInt(document.getElementById("modalHarga").textContent.replace(/\./g, ""));
        const jumlah = parseInt(document.getElementById("jumlah").value);

        // Cek apakah item sudah ada
        const existingItem = cart.find(item => item.id_menu === id_menu);
        if (existingItem) {
            existingItem.jumlah += jumlah;
        } else {
            cart.push({
                id_menu,
                nama_menu,
                harga,
                jumlah
            });
        }

        updateCartUI();
        closeModal();
        alert("Berhasil ditambahkan ke keranjang!");
    });
</script>



</body>
</html>

<?php $conn->close(); ?>
