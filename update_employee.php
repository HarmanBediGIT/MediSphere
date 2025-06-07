<?php
require 'db_conn.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = intval($_POST['user_id']);
    $name = $_POST['name'];
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $phn = $_POST['phn'];
    $role = strtolower($_POST['role']); // Ensure role is lowercase

    $stmt = $conn->prepare("UPDATE users SET name = ?, user_name = ?, email = ?, phn = ?, role = ? WHERE user_id = ?");
    $stmt->bind_param("sssisi", $name, $user_name, $email, $phn, $role, $userId);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
