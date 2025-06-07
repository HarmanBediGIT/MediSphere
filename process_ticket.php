<?php
    require 'db_conn.php';

    // Get the posted data
    $data = json_decode(file_get_contents('php://input'), true);

    $enquiryId = $data['enquiryId'];
    $user_id = $data['userid'];
    $userName = $data['userName'];
    $subject = $data['subject'];
    $message = $data['message'];
    $replyFromAdmin = $data['replyFromAdmin'];

    // Insert reply into the replied_enquiries table
    $sql = "INSERT INTO replied_tickets (name, subject, message, reply_from_admin, user_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $userName, $subject, $message, $replyFromAdmin, $user_id);

    if ($stmt->execute()) {
        // Delete the enquiry from pending_enquiries
        $deleteSql = "DELETE FROM tickets WHERE user_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $user_id);
        $deleteStmt->execute();

        // Return success response
        echo json_encode(['success' => true]);
    } else {
        // Return failure response
        echo json_encode(['success' => false, 'message' => 'Failed to process reply.']);
    }

    $stmt->close();
    $conn->close();
?>
