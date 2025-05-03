<?php
    require 'db_conn.php'; // Database Connection File

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $message = strtolower(trim($_POST['message']));

        // Split the message into individual words
        $words = explode(" ", $message);

        $responses = [];
        foreach ($words as $word) {
            $stmt = $conn->prepare("SELECT response FROM chatbot WHERE question LIKE ?");
            $searchQuery = "%" . $word . "%";
            $stmt->bind_param("s", $searchQuery);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $responses[] = $row['response'];
                }
            }
        }

        // If there are matching responses, send the first one
        if (!empty($responses)) {
            echo json_encode(["response" => $responses[0]]);
        } else {
            echo json_encode(["response" => "I'm sorry, I don't understand that question."]);
        }
    }
?>
