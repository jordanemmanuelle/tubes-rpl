<?php
    $connect = mysqli_connect("localhost", "root", "");

    if (mysqli_connect_errno()) {
        echo (mysqli_connect_error());
    } 

    $sql = "CREATE DATABASE foretubes";

    if (mysqli_query($connect, $sql)) {
        echo ("Database created");
    } else {
        echo ("Error while creating database");
    }
?>