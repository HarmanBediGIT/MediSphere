<?php
session_start();
require 'db_conn.php'; // Adjust path if needed

if (!isset($_SESSION['user_id'])) {
    header("Location: loginpage.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username']; // ✅ Corrected key

$final_total = isset($_POST['final_total']) ? floatval($_POST['final_total']) : 0;
// $coupon_code = isset($_POST['applied_coupon_code']) ? trim($_POST['applied_coupon_code']) : '';

// echo "Final total received: $final_total<br>";
// echo "Coupon code received: $coupon_code<br>";
// exit();

$_SESSION['final_total'] = $final_total;

// Check if the user already has an order
$checkQuery = "SELECT order_id FROM orders WHERE user_id = ?";
$stmtCheck = $conn->prepare($checkQuery);
$stmtCheck->bind_param("i", $user_id);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result && $result->num_rows > 0) {
    // Update existing order
    $updateQuery = "UPDATE orders SET total_price = ?, coupon_applied = ? WHERE user_id = ?";
    $stmtUpdate = $conn->prepare($updateQuery);
    $stmtUpdate->bind_param("dsi", $final_total, $coupon_code, $user_id);
    $stmtUpdate->execute();
    $stmtUpdate->close();
} else {
    // Insert new order
    $insertQuery = "INSERT INTO orders (user_id, user_name, total_price, coupon_applied) VALUES (?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($insertQuery);
    $stmtInsert->bind_param("isds", $user_id, $user_name, $final_total, $coupon_code);
    $stmtInsert->execute();
    $stmtInsert->close();
}

$stmtCheck->close();
$conn->close();

header("Location: add_address.php");
exit();
?>
