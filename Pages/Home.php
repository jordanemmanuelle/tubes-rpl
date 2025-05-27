<?php
session_start();

// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = ""; // Ganti jika ada password
$db = "foretubes";

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
            padding-top: 30px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 20px;
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

        #modalImage {
            width: 300px;
            /* atau ukuran yang kamu inginkan */
            height: 300px;
            object-fit: cover;
            /* cover = gambar di-crop untuk pas, contain = gambar utuh tapi bisa ada ruang kosong */
            display: block;
            margin: 0 auto 20px auto;
            /* tengah + jarak bawah */
            border-radius: 10px;
        }

        .modal-content button[type="submit"] {
            background-color: #4CAF50;
            /* warna hijau segar */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            width: 100%;
        }

        .modal-content button[type="submit"]:hover {
            background-color: #45a049;
            transform: scale(1.02);
        }

        .modal-content button[type="submit"]:active {
            transform: scale(0.98);
        }

        /* Keranjang */
        .cart-container {
            max-width: 500px;
            margin: 20px auto;
            padding: 15px;
            border: 2px solid #4CAF50;
            border-radius: 12px;
            background: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .cart-container h2 {
            margin-bottom: 15px;
            color: #2e7d32;
            text-align: center;
        }

        #cartItems {
            list-style: none;
            padding: 0;
            margin: 0 0 15px 0;
            max-height: 250px;
            overflow-y: auto;
        }

        #cartItems li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
            color: #333;
        }

        #cartItems li:last-child {
            border-bottom: none;
        }

        .cart-item-info {
            flex-grow: 1;
        }

        .cart-item-qty {
            margin: 0 10px;
            font-weight: 600;
            color: #4caf50;
        }

        .cart-item-price {
            font-weight: 700;
            color: #1b5e20;
            min-width: 90px;
            text-align: right;
        }

        .remove-btn {
            background-color: transparent;
            border: none;
            color: #e53935;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            padding: 0 8px;
            transition: color 0.3s ease;
        }

        .remove-btn:hover {
            color: #b71c1c;
        }

        .cart-total {
            text-align: right;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #2e7d32;
        }

        #buyButton {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 0;
            width: 100%;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        #buyButton:hover {
            background-color: #388e3c;
            transform: scale(1.05);
        }

        #buyButton:active {
            transform: scale(0.98);
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
        <a href="HistoryPesanan.php">History Pesanan</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
        <?php if (isset($_SESSION['id_user'])): ?>
            <a href="../LoginRegister/Logout.php">Logout</a>
        <?php else: ?>
            <a href="../LoginRegister/FormRegister.html">Register</a>
            <a href="../LoginRegister/FormLogin.html">Login</a>
            <script>
                window.onload = function () {
                    closeModal(); // niar kalau direfresh ga terus2an muncul popout productnya
                }
            </script>
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
                        <img src="../Admin/uploads/<?= htmlspecialchars($row['gambar']); ?>"
                            alt="<?= htmlspecialchars($row['nama_menu']); ?>" style="max-width: 100%; height: auto;">
                        <div class="product-info">
                            <h3><?= htmlspecialchars($row['nama_menu']); ?></h3>
                            <p><strong>Jenis:</strong> <?= ucfirst($row['jenis']); ?></p>
                            <p>Rp<?= number_format($row['harga'], 0, ',', '.'); ?></p>
                        </div>
                        <button <?= $row['stok'] == 0 ? 'disabled' : ''; ?> onclick='openModal(<?= json_encode([
                                     "id_menu" => $row["id_menu"],
                                     "nama_menu" => $row["nama_menu"],
                                     "deskripsi" => $row["deskripsi"],
                                     "jenis" => $row["jenis"],
                                     "harga" => $row["harga"],
                                     "stok" => $row["stok"],
                                     "gambar" => $row["gambar"]
                                 ]); ?>)'>
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
    <?php if (isset($_SESSION['id_user'])): ?>
        <section class="cart-container" id="cartContainer">
            <h2>Keranjang Saya</h2>
            <ul id="cartItems">
                <li>Keranjang kosong.</li>
            </ul>
            <div class="cart-total" id="cartTotal" style="display:none;">
                Total: Rp0
            </div>
            <button id="buyButton" style="display:none;">Beli</button>
        </section>
    <?php endif; ?>
    <footer>&copy; 2025 Fore Coffee. All rights reserved.</footer>

    <!-- Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <img id="modalImage" src="" alt="Product Image">
            <h2 id="modalName"></h2>
            <p id="modalDescription"></p>
            <p><strong>Jenis:</strong> <span id="modalJenis"></span></p>
            <p><strong>Harga:</strong> Rp<span id="modalHarga"></span></p>
            <p><strong>Stok tersedia:</strong> <span id="modalStok"></span></p>

            <form id="addToCartForm">
                <label for="jumlah">Jumlah:</label>
                <div class="quantity-control">
                    <button type="button" onclick="decreaseQty()">−</button>
                    <input type="text" id="jumlah" name="jumlah" value="1" readonly>
                    <button type="button" onclick="increaseQty()">+</button>
                </div>

                <input type="hidden" id="modalIdMenu" name="id_menu" />
                <br><br>
                <button type="submit">Tambah ke Keranjang</button>
            </form>
        </div>
    </div>

    <!-- Form tersembunyi untuk checkout -->
    <form id="checkoutForm" action="checkout.php" method="POST" style="display:none;">
        <input type="hidden" name="cart_data" id="cartData" value="" />
    </form>

    <script>
        function increaseQty() {
            const qtyInput = document.getElementById("jumlah");
            const max = parseInt(qtyInput.max);
            let current = parseInt(qtyInput.value);
            if (current < max) {
                qtyInput.value = current + 1;
            }
        }

        function decreaseQty() {
            const qtyInput = document.getElementById("jumlah");
            let current = parseInt(qtyInput.value);
            if (current > 1) {
                qtyInput.value = current - 1;
            }
        }
        function openModal(menu) {
            console.log("openModal called", menu); // debug
            document.getElementById("modalImage").src = "../Admin/uploads/" + menu.gambar;
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

        document.getElementById("addToCartForm").addEventListener("submit", function (e) {
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
            saveCart();
            updateCartUI();
            closeModal();
            alert("Berhasil ditambahkan ke keranjang!");
        });

        let cart = JSON.parse(localStorage.getItem("cart")) || [];

        function saveCart() {
            localStorage.setItem("cart", JSON.stringify(cart));
        }


        function updateCartUI() {
            const cartItemsEl = document.getElementById("cartItems");
            const buyButton = document.getElementById("buyButton");
            const cartTotalEl = document.getElementById("cartTotal");
            cartItemsEl.innerHTML = "";

            if (cart.length === 0) {
                cartItemsEl.innerHTML = "<li>Keranjang kosong.</li>";
                buyButton.style.display = "none";
                cartTotalEl.style.display = "none";
                return;
            }

            // Render setiap item dengan tombol hapus
            cart.forEach((item, index) => {
                const li = document.createElement("li");

                const infoDiv = document.createElement("div");
                infoDiv.classList.add("cart-item-info");
                infoDiv.textContent = item.nama_menu;

                const qtySpan = document.createElement("span");
                qtySpan.classList.add("cart-item-qty");
                qtySpan.textContent = `x${item.jumlah}`;

                const priceSpan = document.createElement("span");
                priceSpan.classList.add("cart-item-price");
                priceSpan.textContent = `Rp${(item.harga * item.jumlah).toLocaleString('id-ID')}`;

                const removeBtn = document.createElement("button");
                removeBtn.classList.add("remove-btn");
                removeBtn.textContent = "×";
                removeBtn.title = "Hapus item";
                removeBtn.addEventListener("click", () => {
                    cart.splice(index, 1);
                    saveCart();
                    updateCartUI();
                });

                li.appendChild(infoDiv);
                li.appendChild(qtySpan);
                li.appendChild(priceSpan);
                li.appendChild(removeBtn);

                cartItemsEl.appendChild(li);
            });

            // Hitung total harga
            const totalHarga = cart.reduce((sum, item) => sum + item.harga * item.jumlah, 0);
            cartTotalEl.textContent = `Total: Rp${totalHarga.toLocaleString('id-ID')}`;
            cartTotalEl.style.display = "block";
            buyButton.style.display = "block";
        }


        // Event klik tombol Beli untuk submit data keranjang ke checkout.php
        document.getElementById("buyButton").addEventListener("click", function () {
            if (cart.length === 0) {
                alert("Keranjang kosong, tidak bisa melanjutkan pembelian.");
                return;
            }

            // Kirim data cart dalam bentuk JSON string ke input hidden
            document.getElementById("cartData").value = JSON.stringify(cart);

            // Submit form
            document.getElementById("checkoutForm").submit();
        });

        window.onload = function () {
            closeModal();
        } // biar setiap kali refresh, popoutnya nutup

        updateCartUI(); // Biar keranjang langsung tampil saat halaman dimuat

    </script>

</body>

</html>

<?php $conn->close(); ?>