<?php
    session_start();
    require 'db_conn.php';

    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['redirect' => 'loginpage.php']);
        exit();
    }

    // Get the logged-in user's ID and username
    $user_id = $_SESSION['user_id'];
    $name = $_SESSION['username'];

    // Get POST data
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate required fields
    if (!isset($data['product_id'], $data['product_name'], $data['product_size'], $data['product_color'], $data['product_price'], $data['product_qty'])) {
        echo json_encode(["message" => "Invalid input data."]);
        exit;
    }

    $prod_code = $conn->real_escape_string($data['product_id']);
    $product_name = $conn->real_escape_string($data['product_name']);
    $product_size = $conn->real_escape_string($data['product_size']);
    $product_color = $conn->real_escape_string($data['product_color']);
    $product_material = isset($data['product_material']) ? $conn->real_escape_string($data['product_material']) : null;
    $product_manufacturer = isset($data['product_manufacturer']) ? $conn->real_escape_string($data['product_manufacturer']) : null;
    $product_price = (float) $data['product_price'];
    $product_qty = 1;
    $created_at = date("Y-m-d H:i:s");

    // Insert into cart table
    $stmt = $conn->prepare("INSERT INTO cart (user_id, user_name, product_id, product_name, product_size, product_color, product_material, product_manufacturer, product_price, product_qty, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssdis", $user_id, $name, $prod_code, $product_name, $product_size, $product_color, $product_material, $product_manufacturer, $product_price, $product_qty, $created_at);

    if ($stmt->execute()) {
        // ✅ Add this block to update the session cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $_SESSION['cart'][] = $prod_code; // you could also push an associative array with more info

        echo json_encode(["status" => "success", "message" => "Product added to cart successfully."]);
    } 
    else {
        echo json_encode(["status" => "error", "message" => "Failed to add product to cart.", "error" => $stmt->error]);
    }

    exit;
    $stmt->close();
    $conn->close();
?>
