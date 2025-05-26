<?php
    $connect = mysqli_connect("localhost", "root", "", "foretubes");

    if (mysqli_connect_errno()) {
        echo (mysqli_connect_error());
    }
    /* test */
?>