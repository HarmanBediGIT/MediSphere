<?php
session_start();
require 'db_conn.php';

header('Content-Type: application/json');

// Decode JSON data
$data = json_decode(file_get_contents("php://input"), true);

$order_id = $data['orderID'] ?? '';
$payer_id = $data['payerID'] ?? '';
$amount = $data['amount'] ?? 0;
$payer_email = $data['payerEmail'] ?? '';
$payer_name = $data['payerName'] ?? '';
$product_id = $data['productId'] ?? null;
$is_single_buy = $data['singleBuy'] ?? false;

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "User not authenticated."]);
    exit();
}

// Save payment
$stmt = $conn->prepare("INSERT INTO payments (user_id, order_id, payer_id, amount, payer_email, payer_name) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issdss", $user_id, $order_id, $payer_id, $amount, $payer_email, $payer_name);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Payment insert failed", "error" => $stmt->error]);
    exit();
}

// Delete from cart
if ($is_single_buy && $product_id) {
    // Delete only one item
    $deleteOne = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $deleteOne->bind_param("is", $user_id, $product_id);
    if ($deleteOne->execute()) {
        echo json_encode(["success" => true, "message" => "Payment successful. One product removed from cart."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to remove product from cart.", "error" => $deleteOne->error]);
    }
} else {
    // Delete all items from cart
    $deleteAll = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $deleteAll->bind_param("i", $user_id);
    if ($deleteAll->execute()) {
        echo json_encode(["success" => true, "message" => "Payment successful. Full cart cleared."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to clear cart.", "error" => $deleteAll->error]);
    }
}
?>
