<?php
    include '../connection.php';

    // NANTI PASSWORD DI-HASH
    $sql = "CREATE TABLE users (
    id_user int AUTO_INCREMENT PRIMARY KEY,
    name varchar (50) NOT NULL,
    email varchar(100) NOT NULL UNIQUE,
    password varchar(255) NOT NULL, 
    role ENUM('user', 'admin') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if (mysqli_query($connect, $sql)) {
        echo ("Table created");
    } else {
        echo ("Error while creating table");
    }

?>