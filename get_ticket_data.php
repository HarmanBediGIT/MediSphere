<?php
    require 'db_conn.php';

    $enquiryId = $_GET['id'];

    // Fetch the enquiry data from the database
    $sql = "SELECT * FROM tickets WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $enquiryId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $enquiry = $result->fetch_assoc();
        // Return JSON response with success flag
        echo json_encode(['success' => true, 'enquiry' => $enquiry]);
    } else {
        // Return JSON response indicating failure
        echo json_encode(['success' => false, 'message' => 'Ticket not found.']);
    }

    $stmt->close();
    $conn->close();
?>
