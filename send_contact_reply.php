<?php
    require 'db_conn.php'; // Ensure this connects to your DB

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';
    require 'phpmailer/src/Exception.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $to = $data['email'];
        $replyMsg = $data['reply'];
        $contactId = $data['id']; // Get the ID of the message
        $subject = "Response from MediSphere";

        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'info.for.medisphere@gmail.com'; // your Gmail
            $mail->Password = 'wfyvzkljbghpohxl'; // App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email content
            $mail->setFrom('info.for.medisphere@gmail.com', 'MediSphere Admin');
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $replyMsg;

            $mail->send();

            $stmt = $conn->prepare("DELETE FROM contact WHERE id = ?");
            $stmt->bind_param("i", $contactId);
            $stmt->execute();
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $mail->ErrorInfo]);
        }
    }
?>
