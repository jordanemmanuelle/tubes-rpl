<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="StyleHome.css">
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
        <?php if (isset($_SESSION['id_user'])): ?>
            <a href="../LoginRegister/Logout.php">Logout</a>
        <?php else: ?>
            <a href="../LoginRegister/FormRegister.html">Register</a>
            <a href="../LoginRegister/FormLogin.html">Login</a>
        <?php endif; ?>
    </nav>

    <div class="about-container">
        <h2>About Fore Coffee</h2>
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Eveniet sapiente nam expedita. Nobis dolor amet similique, consectetur consequatur quo doloremque quam. Error, dicta! Vitae provident et accusantium voluptatibus ipsam ut?</p>
    </div>

    <footer>
        <p>&copy; 2025 Fore Coffee. All rights reserved.</p>
    </footer>

</body>
</html>