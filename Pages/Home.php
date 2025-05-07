<?php
    session_start(); // Mulai session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fore Coffee - Home</title>
    <link rel="stylesheet" href="style.css">
    </head>
<body>

    <header>
        <h1>Welcome to Fore Coffee</h1>
        <p>Your favorite coffee shop</p>
    </header>

    <nav>
        <a href="Home.php">Home</a>
        <a href="About.php">About Us</a>
        <a href="Contact.php">Contact</a>
        <a href="../LoginRegister/FormLogin.html">Login</a>
        <a href="../LoginRegister/FormRegister.html">Register</a>
    </nav>

    <div class="welcome-message">
        <h2>Welcome, <?= $_SESSION['name'] ?? 'Guest'; ?>!</h2>
        <p>Explore our delicious coffee collection and more!</p>
    </div>

    <div class="products">
        <div class="product-card">
            <img src="coffee1.jpg" alt="Coffee 1">
            <h3>Espresso</h3>
            <p>Rich and bold flavor with a smooth finish</p>
            <button>Order Now</button>
        </div>
        <div class="product-card">
            <img src="coffee2.jpg" alt="Coffee 2">
            <h3>Cappuccino</h3>
            <p>Classic espresso with steamed milk and foam</p>
            <button>Order Now</button>
        </div>
        <div class="product-card">
            <img src="coffee3.jpg" alt="Coffee 3">
            <h3>Latte</h3>
            <p>Espresso mixed with steamed milk and a creamy taste</p>
            <button>Order Now</button>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Fore Coffee. All rights reserved.</p>
    </footer>

</body>
</html>
