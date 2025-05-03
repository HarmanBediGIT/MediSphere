<?php
session_start();
require 'db_conn.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['redirect' => 'signup.php']);
    exit();
}

// Get the logged-in user's ID and username
$user_id = $_SESSION['user_id'];
$name = $_SESSION['username'];

// Get the input data
$data = json_decode(file_get_contents("php://input"), true);

$product_id = $data['product_id'];
$product_name = $data['product_name'];
$product_size = $data['product_size'];
$product_color = $data['product_color'];
$product_material = $data['product_material'];
$product_manufacturer = $data['product_manufacturer'];
$product_price = (float)$data['product_price']; // Ensure the price is treated as a float
$product_qty = (int)$data['product_qty']; // Ensure the quantity is treated as an integer

// Check if the product exists and get its quantity
$sql = "SELECT qty FROM products WHERE prod_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $available_qty = $row['qty'];

    if ($available_qty > 0) {
        // Check if the user already has an entry in the cart
        $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If the product is already in the cart, update quantity
            echo json_encode(['message' => 'Product already in cart!']);
        } else {
            // Insert new cart entry
            $sql = "INSERT INTO cart (user_id, user_name, product_id, product_name, product_size, product_color, product_material, product_manufacturer, product_price, product_qty) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssssddi", $user_id, $name, $product_id, $product_name, $product_size, $product_color, $product_material, $product_manufacturer, $product_price, $product_qty);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'Product added to cart successfully!']);
            } else {
                echo json_encode(['message' => 'Error adding to cart: ' . $stmt->error]);
            }
        }
    } else {
        echo json_encode(['message' => 'Sorry, this product is out of stock.']);
    }
} else {
    echo json_encode(['message' => 'Product not found.']);
}

$stmt->close();
$conn->close();
?>
