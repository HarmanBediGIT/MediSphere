<?php
    // Database connection
    require 'db_conn.php';

    // Fetch all courses from the 'users' table
    $sql1 = "SELECT tickets.*, users.role 
         FROM tickets 
         JOIN users ON tickets.user_id = users.user_id";
    $result1 = $conn->query($sql1);

    session_start();

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
            <div class="logo">
                    <img src="images/medisphere.png" style="width:110px;"> </img>
                    <h5 style="font-size:25px; margin-left:10px;"> MediSphere </h5>
                    <?php if ($isLoggedIn): ?>
                        <!-- <br>  -->
                        <h5 style="font-size:15px;"> <br> <br> Welcome <?php echo $name; ?> </h5>
                    <?php endif; ?>
            </div>

            <input type="checkbox" id="click">
            <label for="click" class="menu-btn">
                <i class="fas fa-bars"></i>
            </label>

            <ul>
                <li><a class="active" href="home.php">Home</a></li>

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
                        <li><a href="ticketraisingpage.php"><i class="fas fa-bell"></i></a></li>
                    <?php endif; ?>

                <?php else: ?>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="loginpage.php">Login</a></li>
                <?php endif; ?>
            </ul>

        </nav>

        <div id="cartButton" class="cart-button" onclick="openCart()">
            <i class="fas fa-shopping-cart"></i>
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

    <div class="main-content">
        <section class="tickets">
            <br>
            <h2>Notifications</h2>
            <table>
                <thead>
                    <tr>
                        <th>Notification ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="userTable">
                    <?php if ($result1->num_rows > 0) { ?>
                    <?php while ($row = $result1->fetch_assoc()) { ?>
                    <tr id="enquiry-<?php echo $row['id']; ?>">
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td><?php echo $row['subject']; ?></td>
                        <td><?php echo $row['message']; ?></td>
                        <td>
                            <button class="reply-btn" data-id="<?php echo $row['id']; ?>">Reply</button>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php } else { ?>
                    <tr class="no-data">
                        <td colspan="6" style="text-align:center;">No pending enquiries</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <!-- Modal HTML -->
        <div id="replyModal" class="modal" style="display: none;">
            <div class="modal-content">
                <h2>Reply to Queries</h2>
                <br>
                <form id="replyForm">
                    <input type="hidden" id="enquiryId">
                    <div>
                        <label for="userName">Requester Name</label>
                        <input type="text" id="userName" readonly>
                    </div>
                    <div>
                        <label for="user_id">Requester ID</label>
                        <input type="number" id="user_id" readonly>
                    </div>
                    <div>
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" readonly>
                    </div>
                    <div>
                        <label for="message">Message</label>
                        <textarea id="message" readonly></textarea>
                    </div>
                    <div>
                        <label for="replyFromAdmin">Reply</label>
                        <textarea id="replyFromAdmin" required></textarea>
                    </div>
                    <button type="button" class="reply-btn" id="submitReply">Reply</button>
                    <button type="button" class="cancel-btn" id="cancelReply">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const replyModal = document.getElementById('replyModal');

        // Open modal with data fetched from the server
        document.querySelectorAll('.reply-btn').forEach(button => {
            button.addEventListener('click', function() {
                const enquiryId = this.getAttribute('data-id');

                // Fetch the enquiry data from the server using AJAX
                fetch(`get_ticket_data.php?id=${enquiryId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Fill the modal with the fetched data
                            document.getElementById('enquiryId').value = data.enquiry.id;
                            document.getElementById('user_id').value = data.enquiry.user_id;
                            document.getElementById('userName').value = data.enquiry.name;
                            document.getElementById('subject').value = data.enquiry.subject;
                            document.getElementById('message').value = data.enquiry.message;

                            // Show the modal
                            replyModal.style.display = 'block';
                        } else {
                            replyModal.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching enquiry data:', error);
                        alert('Error fetching data. Please try again.');
                    });
            });
        });

        // Handle "Reply" button inside the modal
        document.getElementById('submitReply').addEventListener('click', function() {
            const enquiryId = document.getElementById('enquiryId').value;
            const replyFromAdmin = document.getElementById('replyFromAdmin').value;

            // Check if the reply field is empty
            if (!replyFromAdmin.trim()) {
                alert("Please provide a reply before submitting.");
                return;
            }

            // AJAX call to save reply and delete the enquiry from pending_enquiries
            fetch('process_ticket.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        enquiryId: enquiryId,
                        userid: document.getElementById('user_id').value,
                        userName: document.getElementById('userName').value,
                        subject: document.getElementById('subject').value,
                        message: document.getElementById('message').value,
                        replyFromAdmin: replyFromAdmin
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // No need for alert, just remove the row and close the modal
                        document.getElementById('enquiry-' + enquiryId)
                            .remove(); // Remove the row from the table
                        replyModal.style.display = 'none'; // Close the modal
                    } else {
                        // Display the error only if something really goes wrong
                        console.error('Failed to send reply:', data.message);
                        alert("Failed to send reply. Please try again.");
                    }
                })
        });

        // Close the modal on "Cancel" button click
        document.getElementById('cancelReply').addEventListener('click', function() {
            replyModal.style.display = 'none';
        });
    </script>
</body>

</html>



<script>
    function openCart() {
        window.location.href = "cart.php"; // Redirect to cart page
    }
</script>