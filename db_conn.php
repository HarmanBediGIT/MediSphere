<?php
    $servername = "localhost";
    $username = "root"; // database username
    $password = ""; // database password
    $dbname = "medisphere"; // name of the database

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>