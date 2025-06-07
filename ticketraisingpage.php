<?php
    // Start a session
    session_start();

    // $cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

    // Database connection
    require "db_conn.php";

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // User is not logged in
        $isLoggedIn = false;
    } 
    else {
        // User is logged in
        $isLoggedIn = true;
        // Access the user_id
        $user_id = $_SESSION['user_id'];
        $name = $_SESSION['username'];
        $role = $_SESSION['role'];
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];   // Assuming this field is in your form
        $subject = $_POST['subject']; // Assuming this field is in your form
        $message = $_POST['message']; // Assuming this field is in your form
        $file_name = $_POST['file_name']; // Optional file input

        // Insert the ticket into the database, including the user_id
        $sql = "INSERT INTO tickets (user_id, name, subject, message, file_name) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Error with prepare: ' . $conn->error);
        }
        $stmt->bind_param("issss", $user_id, $name, $subject, $message, $file_name);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Ticket raised successfully!";
        } else {
            echo "Failed to raise ticket.";
        }
        $stmt->close();
    }
    // Retrieve tickets for the logged-in user based on their user ID
    $sql1 = "SELECT * FROM tickets WHERE user_id = ?";
    $stmt1 = $conn->prepare($sql1);
    if (!$stmt1) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    // Retrieve replied tickets for the logged-in user
    $sql2 = "SELECT * FROM replied_tickets WHERE user_id = ?";
    $stmt2 = $conn->prepare($sql2);
    if (!$stmt2) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $cartCount = 0;
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $countStmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
        $countStmt->bind_param("i", $user_id);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        if ($countRow = $countResult->fetch_assoc()) {
            $cartCount = $countRow['count'];
        }
    }
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Notifications</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
        <link rel="stylesheet" type="text/css" href="css\index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="js/script.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        <style>
            .no-data {
                text-align: center;
                font-style: italic;
                color: #888;
            }

            /* Modal Styles */
            .modal {
                display: none;
                position: fixed;
                z-index: 1;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
            }

            .modal-content {
                background-color: white;
                margin: 15% auto;
                padding: 20px;
                width: 40%;
            }

            #replyForm input[type="text"],
            #replyForm input[type="number"],
            #replyForm textarea {
                width: 100%;
                padding: 8px 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 14px;
                box-sizing: border-box;
                resize: vertical;
            }
        </style>
    </head>

    <body>
        <div id="progressBarContainer">
            <div id="progressBar"></div>
        </div>
        
        <nav>
                <div class="logo" style="text-align: center;">
                    <a href="home.php">
                        <img src="images/medisphere.png" style="width: 60px; display: block; margin: 0 auto;">
                    </a>
                    <div style="color: white; font-size: 16px; margin-top: 5px;">MediSphere</div>
                </div>

            <input type="checkbox" id="click">
            <label for="click" class="menu-btn">
                <i class="fas fa-bars"></i>
            </label>

            <ul>
                <li><a href="home.php">Home</a></li>

                <?php if ($isLoggedIn): ?>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="categories.php">Categories</a></li>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="orders.php">Order Status</a></li>
                    <?php endif; ?>

                    <li><a href="profile.php"><i class="fas fa-user"></i></a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="pending_tickets.php"><i class="fas fa-bell"></i></a></li>
                    <?php else: ?>
                        <li><a class="active" href="ticketraisingpage.php"><i class="fas fa-bell"></i></a></li>
                    <?php endif; ?>

                <?php else: ?>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="loginpage.php">Login</a></li>
                <?php endif; ?>
            </ul>

        </nav>

        <div id="cartButton" class="cart-button" onclick="openCart()" style="position: relative;">
            <i class="fas fa-shopping-cart"></i>
            <span id="cartCountBadge" style="
                position: absolute;
                top: -8px;
                right: -10px;
                background: red;
                color: white;
                border-radius: 50%;
                padding: 3px 10px;
                font-size: 12px;
                font-weight: bold;
                display: <?php echo ($cartCount > 0 ? 'inline-block' : '0'); ?>;
            ">
                <?php echo $cartCount; ?>
            </span>
        </div>

        <!-- Chatbot Button -->
        <div id="chatbotButton" class="chatbot-button" onclick="toggleChatbot()">
            <i class="fas fa-robot"></i>
        </div>

        <!-- Chatbot Interface -->
        <div id="chatbot" class="chatbot-window">
            <div class="chatbot-header">
                <h3>MediSphere Chatbot</h3>
                <span class="close-btn" onclick="toggleChatbot()">&times;</span>
            </div>
            <div class="chat-window" id="chatWindow">
                <div id="chatHistory" class="chat-history"></div>
                <div class="chat-input">
                    <input type="text" id="userInput" placeholder="Type your message...">
                    <button id="sendBtn" onclick="sendMessage()">Send</button>
                </div>
            </div>
        </div>
    <div class="container">
        <section class="tickets">
            <br>
            <h2>Your Queries</h2>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Your Name</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Files</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result1->num_rows > 0) { ?>
                        <?php while ($row = $result1->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td><?php echo htmlspecialchars($row['message']); ?></td>
                                <?php if (empty($row['file_name']) || empty($row['file_path'])) { ?>
                                    <td style="color:grey;"><i>No file uploaded</i></td>
                                <?php } else { ?>
                                    <td><?php echo htmlspecialchars($row['file_name']); ?></td>
                                <?php } ?>
                                <td>Sent to Admin</td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr class="no-data">
                            <td colspan="6" style="text-align:center;">No new queries made yet !</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="raise-new-ticket" style="display:flex; justify-content:right; text-align:right;">
                <form action="raise-new-ticket.php" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <button type="submit" class="new-btn">Raise a new Query</button>
                </form>
            </div>
        </section>

        <section class="replies">
            <h2>Replies to your Queries</h2>
            <table>
                <thead>
                    <tr>
                        <th>Reply ID</th>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Reply From Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result2->num_rows > 0) { ?>
                        <?php while ($row = $result2->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td><?php echo htmlspecialchars($row['message']); ?></td>
                                <td><?php echo htmlspecialchars($row['reply_from_admin']); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr class="no-data">
                            <td colspan="5" style="text-align:center;">No replies to your tickets</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </div>
    <div class="footer1">
            <div class="social">
                <a href="#">
                    <i class="fa-brands fa-square-instagram"></i>
                </a>
                <a href="#">
                    <i class="fa-brands fa-square-facebook"></i>
                </a>
            </div>
            <div class="copyright">
                © Copyright MediSphere 2025, All rights reserved
            </div>
        </div>
</body>

</html>

<script>
    function openCart() {
        window.location.href = "cart.php"; // Redirect to cart page
    }
</script>