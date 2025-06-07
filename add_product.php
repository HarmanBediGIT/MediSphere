<?php
    error_reporting(0);
    ini_set('display_errors', '0');

    session_start();
    require 'db_conn.php';

    // Check if the user is an admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit();
    }

    // Get the JSON data from the request
    $data = json_decode(file_get_contents('php://input'), true);

    // Sanitize inputs
    $cat_code = $conn->real_escape_string($data['cat_code']);
    $prod_code = $conn->real_escape_string($data['prod_code']);
    $name = $conn->real_escape_string($data['name']);
    $description = $conn->real_escape_string($data['description']);
    $colors = $conn->real_escape_string($data['colors']);
    $sizes = $conn->real_escape_string($data['sizes']);
    $material = $conn->real_escape_string($data['material']);
    $manufacturer = $conn->real_escape_string($data['manufacturer']);
    $qty = $conn->real_escape_string($data['qty']);
    $price = $conn->real_escape_string($data['price']);

    // Insert into the database
    $sql = "INSERT INTO products (
                cat_code, prod_code, name, description, colors, sizes, material, manufacturer, qty, price
            ) VALUES (
                '$cat_code', '$prod_code', '$name', '$description', '$colors', '$sizes', '$material', '$manufacturer', '$qty', '$price'
            )";

    if ($conn->query($sql) === TRUE) {
        echo 'Product added successfully!';
    } else {
        echo 'Error: Product cannot be added!';
    }

    $conn->close();
?>
