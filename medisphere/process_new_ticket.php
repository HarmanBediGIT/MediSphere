<?php
    require 'db_conn.php'; // Database connection

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 
    catch (PDOException $e) {
        die("Error: Could not connect. " . $e->getMessage());
    }

    // Get form data
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // File upload handling (only if a file is uploaded)
    $file_name = '';
    $file_tmp = '';
    $file_destination = '';

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $upload_directory = "uploads/";

        // Ensure the upload directory exists
        if (!file_exists($upload_directory)) {
            mkdir($upload_directory, 0777, true);
        }

        // Move the file to the uploads directory
        $file_destination = $upload_directory . basename($file_name);
        move_uploaded_file($file_tmp, $file_destination);
    }

    // Insert the new ticket into the database
    try {
        session_start();
        $user_id = $_SESSION['user_id']; // Ensure user_id is in session

        // Insert into tickets (excluding file details first)
        $sql = "INSERT INTO tickets (name, subject, message, user_id) VALUES (:name, :subject, :message, :user_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            $ticket_id = $pdo->lastInsertId();

            // If file uploaded, update the record with file details
            if ($file_name && $file_destination) {
                $sql2 = "UPDATE tickets SET file_name = :file_name, file_path = :file_path WHERE id = :ticket_id";
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->bindParam(':file_name', $file_name);
                $stmt2->bindParam(':file_path', $file_destination);
                $stmt2->bindParam(':ticket_id', $ticket_id);
                $stmt2->execute();
            }

            // Redirect back to the ticket raising page with success
            header("Location: ticketraisingpage.php?success=1");
            exit();
        }
    } 
    catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>