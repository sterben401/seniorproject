<?php

    $servername = "localhost";
    $username = "project";
    $password = "1234";
    $db_name = "PROJECT";
    $port = "3306";

    $conn = mysqli_connect($servername, $username, $password, $db_name, $port);

    if (!$conn) {

        echo "Connection failed!";
    }
?>
