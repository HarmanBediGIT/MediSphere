<?php
    // Start a session
    session_start();

    require 'db_conn.php';

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // User is not logged in
        $isLoggedIn = false;
    } else {
        // User is logged in
        $isLoggedIn = true;
        // Access the user_id
        $user_id = $_SESSION['user_id'];
        $name = $_SESSION['username'];
        $role = $_SESSION['role'];
    }

    $sql = "SELECT * FROM orders";
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Orders Made</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
      crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
</head>

    <body>
        <div id="progressBarContainer">
            <div id="progressBar"></div>
        </div>
        
        <nav>
            <div class="logo">
                <div class="logoName" style="font-size:30px;">
                    MediSphere
                    <?php if ($isLoggedIn): ?>
                        <h5 style="font-size:15px;"> Welcome <?php echo $name; ?> </h5>
                    <?php endif; ?>
                </div>
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
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <?php
                        // Check if the logged-in user is an admin
                        if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                    ?>
                        <li><a href="orders.php">Order Status</a></li>
                    <?php } ?>
                <?php else: ?>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="loginpage.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Chatbot Button -->
        <!-- <div id="chatbotButton" class="chatbot-button">
            <h2>&#129302;</h2>
        </div> -->

        <!-- Chatbot Interface -->
        <!-- <div id="chatbot" class="chatbot-window" style="display: none;">
            <div class="chatbot-header">
                <h3>MediSphere Chatbot</h3>
                <span id="closeChatbot" class="close-btn">&times;</span>
            </div>
            <div id="chatWindow" class="chat-window">
                <div id="chatHistory" class="chat-history"> -->
                    <!-- Chat history will appear here -->
                <!-- </div>
                <input type="text" id="userInput" class="user-input" placeholder="Type your message...">
                <button id="sendBtn">Send</button>
            </div>
        </div> -->

        <table>
            <thead>
                <th colspan="6"> ORDERS MADE TILL NOW </th>
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Items</th>
                    <th>Total Amount</th>
                    <th>Coupons Applied</th>
                </tr>
            </thead>
            <tbody id="userTable">
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr data-role="<?php echo strtolower($row['role']); ?>" id="user-<?php echo $row['user_id']; ?>">
                        <td><?php echo $row['order_id']; ?></td>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['items']; ?></td>
                        <td><?php echo $row['total_price']; ?></td>
                        <td><?php echo $row['coupon_applied']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
            
        <div class="footer1">
            <div class="social">
                <a href="#">
                    <i class="fa-brands fa-square-instagram"></i>
                </a>
                <a href="#">
                    <i class="fa-brands fa-facebook"></i>
                </a>
            </div>
            <div class="copyright">
            © Copyright 2023, All rights reserved
            </div>
        </div>
    </body>
    
</html>

<script>
    // JavaScript for handling Edit and Delete operations
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');

                if (confirm("Are you sure you want to cancel the order for this user?")) {
                    // AJAX request to delete the user
                    fetch('cancel_order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ user_id: userId }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Order cancelled by Admin successfully!");
                            document.getElementById('user-' + userId).remove();  // Remove the row from the table
                        }
                        else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            });
        });
    });    
</script>