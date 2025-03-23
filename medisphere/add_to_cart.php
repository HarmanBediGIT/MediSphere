<?php
session_start();
require 'db_conn.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['message' => 'You must be logged in to add items to the cart.']);
    exit();
}

// Get the logged-in user's ID and username
$user_id = $_SESSION['user_id'];
$name = $_SESSION['username'];

// Get the input data
$data = json_decode(file_get_contents("php://input"), true);
$product_name = $data['product_name'];
$product_price = (float)$data['product_price']; // Ensure the price is treated as a float

// Check if the product exists and get its quantity
$sql = "SELECT qty FROM products WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $qty = $row['qty'];

    if ($qty > 0) {
        // Check if the user has any entries in the cart
        $sql = "SELECT * FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If the user already has a cart entry, update it
            $cart_item = $result->fetch_assoc();
            $existing_items = $cart_item['items'];
            $existing_price = (float)$cart_item['price']; // Ensure existing price is treated as a float
            
            // Check if the item is already in the cart's items
            if (strpos($existing_items, $product_name) === false) {
                // Append the new product name if it's not already in the items
                $updated_items = $existing_items . ', ' . $product_name;
            } else {
                // Keep existing items if it's already there
                $updated_items = $existing_items; // Do not change existing items
            }
            
            // Update the total price
            $updated_price = $existing_price + $product_price;

            // Update items and price for the existing cart entry
            $sql = "UPDATE cart SET items = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $updated_items, $user_id);
            if ($stmt->execute()) {
                echo json_encode(['message' => 'Product updated in cart successfully!']);
            } else {
                echo json_encode(['message' => 'Error updating the cart: ' . $stmt->error]);
            }
        } else {
            // If no cart entry exists, create a new entry
            $sql = "INSERT INTO cart (user_id, user_name, items) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $user_id, $name, $product_name);
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
