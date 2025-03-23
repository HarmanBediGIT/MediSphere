<?php
    require 'db_conn.php';

    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->user_id)) {
        $user_id = $data->user_id;

        // Perform deletion query
        $sql = "DELETE FROM orders WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $sql2 = "DELETE FROM cart WHERE user_id = ?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("i", $user_id);
            $stmt2->execute();
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to cancel order.']);
        }
        $stmt->close();
        $conn->close();
    } 
    else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    }
?>
