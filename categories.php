<?php
    // Start a session
    session_start();

    // $cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

    // Database connection
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

    // Fetch categories from the database
    $sql = "SELECT * FROM categories";
    $result = mysqli_query($conn, $sql);

    // Check if query was successful
    if (!$result) {
        die("Database query failed: " . mysqli_error($conn));
    }

    // Array of images
    $images = [
        "images/diag_category.png",
        "images/surg_category.png",
        "images/care_category.png",
        "images/ppe_category.png",
        "images/lab_category.png",
        "images/home_category.png",
        "images/rehab_category.png",
        "images/img_category.png",
        "images/cons_category.png",
        "images/emer_category.png",
        "images/dent_category.png",
        "images/neuro_category.png",
        "images/card_category.png",
        "images/ortho_category.png",
        "images/derm_category.png",
        // Add more images as needed
    ];

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

    $notify_count = 0;
    // Count messages in contact table
    $contactCountResult = $conn->query("SELECT COUNT(*) AS total FROM contact");
    $contactCountRow = $contactCountResult->fetch_assoc();
    $contactCount = $contactCountRow['total'] ?? 0;

    // Count pending tickets in tickets table (assuming a 'status' column exists)
    $ticketsCountResult = $conn->query("SELECT COUNT(*) AS total FROM tickets");
    $ticketsCountRow = $ticketsCountResult->fetch_assoc();
    $pendingTicketCount = $ticketsCountRow['total'] ?? 0;

    // Combined count
    $notify_count = $contactCount + $pendingTicketCount;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">

        <title>Categories</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind">
        <link rel="stylesheet" href="css/index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
            integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"/>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="js/script.js"></script>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <li><a href="products.php">Products</a></li>

                <?php if ($isLoggedIn): ?>
                    <li><a href="categories.php">Categories</a></li>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="orders.php">Order Status</a></li>
                    <?php endif; ?>

                    <li><a href="profile.php"><i class="fas fa-user"></i></a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="pending_tickets.php"><i class="fas fa-bell"></i><span id="cartCountBadge" style="
                            position: absolute;
                            top: -8px;
                            right: -10px;
                            background: red;
                            color: white;
                            border-radius: 50%;
                            padding: 3px 10px;
                            font-size: 12px;
                            font-weight: bold;
                            display: <?php echo ($notify_count > 0 ? 'inline-block' : '1'); ?>;
                        ">
                            <?php echo $notify_count; ?>
                        </span></a></li>
                    <?php else: ?>
                        <li><a href="ticketraisingpage.php"><i class="fas fa-bell"></i></a></li>
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

        <?php if ($isLoggedIn): ?>
            <!-- search bar implementation -->
            <div class="search-container" style="position: relative;">  <!-- Add position: relative to the parent container -->
                <form action="" method="POST" id="searchForm" autocomplete="off">
                    <input type="text" id="searchInput" placeholder="Search products..." name="search">
                    <button type="submit">Search</button>
                </form>
                <div id="searchResults" class="search-results"></div> <!-- Results will show here -->
            </div>
        <?php endif; ?>

        <?php
            // Check if the logged-in user is an admin
            if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
            ?>
            <div class="login-container">
                    <button type="submit" class="category-btn" id="openModalBtn">Add New Category</button>
                    <button type="button" class="category-btn" id="bulkUploadBtn">Bulk Upload Categories</button>
                    <input type="file" id="csvFileInput" name="csv_file" accept=".csv" style="display: none;">
            </div>
        <?php } ?>

        <br><br>

        <div class="featured-products">
            <h2 style="margin-top:-50px; margin-bottom:-50px;">Explore through Categories</h2>
        </div>
        
        <!-- Category Cards Section -->
        <div class="card-container">
            <?php 
            // Initialize a counter for images
            $index = 0;

            // Loop through the fetched categories
            while ($row = mysqli_fetch_assoc($result)): 
                // Select an image based on the current index
                $imagePath = $images[$index % count($images)];
            ?>
                <div class="card" onclick="window.location.href='category_products.php?code=<?php echo urlencode($row['code']); ?>'">
                    <div class="card-inner">
                        <div class="card-front" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                            <img src="<?php echo $imagePath; ?>" style="width: 50%; height: 50%; object-fit: cover;" alt="Product Image" />
                            <div class="category-name" style="margin-top: 10px; font-size: 18px; font-weight: bold;">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </div>
                        </div>
                        <div class="card-back">
                            <div class="category-details">
                                <div class="category-code">Unique Code: <?php echo htmlspecialchars($row['code']); ?></div>
                                <div class="category-name"><?php echo htmlspecialchars($row['name']); ?></div>
                                <div class="category-code">Price Range: <?php echo htmlspecialchars($row['price_range']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php 
                // Increment the index for the next image
                $index++;
            endwhile; 
            ?>
        </div>

        <!-- Modal structure -->
        <div id="categoryModal" class="modal">
            <div class="modal-content">
                <span class="close" style="font-weight:bolder; cursor:pointer;">&times;</span>
                <h2>Add New Category</h2>
                <div id="feedbackMessage" style="display: none; margin-bottom: 10px;"></div> <!-- Feedback message -->
                <form id="categoryForm">
                    <div class="form-group">
                        <label for="code">Code :</label>
                        <input type="text" id="code" name="code" placeholder="eg: DIAG, SURG, LAB, etc." required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name :</label>
                        <input type="text" id="name" name="name" placeholder="eg: Surgical Gloves, etc." required>
                    </div>
                    <div class="form-group">
                        <label for="priceRange">Price Range :</label>
                        <input type="text" id="priceRange" name="priceRange" placeholder="$9 - $199" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" id="addCategoryBtn" class="category-btn">Add</button>
                        <button type="button" class="category-btn cancel-btn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="footer1">
            <div class="social">
                <a href="#">
                    <i class="fa-brands fa-square-instagram"></i>
                </a>
                <a target="_blank" href="#">
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
    document.getElementById('bulkUploadBtn').addEventListener('click', () => {
        document.getElementById('csvFileInput').click(); // trigger file input
    });

    document.getElementById('csvFileInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('csvFile', file);

        fetch('upload_csv_categories.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                location.reload(); // Reload the page on success
            } else {
                alert('Error: ' + result.message);
                console.error(result.details);
            }
        })
        .then(data => {
            console.log(data);
            alert(data.message);
        })
        .catch(err => console.error('Upload error:', err));
    });

    // Get modal elements
    const modal = document.getElementById("categoryModal");
    const openModalBtn = document.getElementById("openModalBtn");
    const closeModalSpan = document.querySelector(".close");
    const cancelBtn = document.querySelector(".cancel-btn");
    const feedbackMessage = document.getElementById("feedbackMessage");

    // Open modal
    openModalBtn.onclick = function() {
        modal.style.display = "flex";
    };

    // Close modal when "X" is clicked or "Cancel" button is clicked
    closeModalSpan.onclick = closeModal;
    cancelBtn.onclick = closeModal;

    // Function to close the modal
    function closeModal() {
        modal.style.display = "none"; // Hide the modal
        feedbackMessage.style.display = "none"; // Hide feedback message on close
    }

    // Function to show feedback message
    function showFeedbackMessage(message) {
        // Determine background color based on message content
        const isErrorMessage = message.includes("Error");
        const backgroundColor = isErrorMessage ? '#f44336' : '#4caf50'; // Red for errors, green for success

        // Create a feedback message element
        const feedbackDiv = $('<div></div>').text(message).css({
            position: 'fixed',
            top: '20px',
            left: '50%',
            transform: 'translateX(-50%)',
            background: backgroundColor, // Set background color based on the message
            color: '#fff',
            padding: '10px',
            borderRadius: '5px',
            zIndex: '1000',
            display: 'none'
        }).appendTo('body').fadeIn(400).delay(3000).fadeOut(400, function() {
            $(this).remove(); // Remove the element after fading out
        });
    }

    // Add Category button action
    $('#addCategoryBtn').on('click', function() {
        // Collect form data
        const code = $('#code').val();
        const name = $('#name').val();
        const priceRange = $('#priceRange').val();

        // Send data to the PHP script via jQuery AJAX
        $.ajax({
            url: 'add_category.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                code: code,
                name: name,
                priceRange: priceRange
            }),
            success: function(response) {
                showFeedbackMessage(response); // Show success or error message
                closeModal(); // Close the modal
                // Optionally, clear the form
                $('#categoryForm')[0].reset();
            },
            error: function(xhr, status, error) {
                // Log the error in case of network issue
                showFeedbackMessage('An error occurred while adding the category.'); // Show error message
            }
        });
    });

    document.getElementById('searchResults').style.display = 'none';
    document.getElementById('searchInput').addEventListener('input', function () {
        const query = this.value;

        if (query.length > 0) {
            fetch('search_categories.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `search=${encodeURIComponent(query)}`
            })
            .then(response => response.json())
            .then(data => {
                const searchResults = document.getElementById('searchResults');
                document.getElementById('searchResults').style.display = 'flex';
                searchResults.innerHTML = ''; // Clear previous results

                if (data.length > 0) {
                    // Calculate dynamic margin based on the number of results
                    const resultCount = data.length;
                    let dynamicMarginTop = 100 + (resultCount - 1) * 50; // Increment margin by 50px for each result
                    searchResults.style.marginTop = `${dynamicMarginTop}px`;

                    data.forEach(product => {
                        const resultDiv = document.createElement('div');
                        resultDiv.textContent = product.name;
                        resultDiv.addEventListener('click', () => {
                            window.location.href = `category_products.php?code=${product.code}`;
                        });
                        searchResults.appendChild(resultDiv);
                    });
                    searchResults.style.display = 'block'; // Make sure it shows up
                } else {
                    searchResults.innerHTML = '<div>No products found !</div>';
                    searchResults.style.marginTop = `100px`;
                    searchResults.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        } else {
            document.getElementById('searchResults').innerHTML = ''; // Clear results if input is empty
            document.getElementById('searchResults').style.display = 'none'; // Hide container
        }
    });

    document.querySelectorAll('.add-to-cart-button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default anchor behavior
            const productName = this.getAttribute('data-product-name');
            const productPrice = parseFloat(this.getAttribute('data-product-price'));       
            // Send AJAX request to add to cart
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_name: productName,
                    product_price: productPrice
                })
            })
            .then(response => response.json())
            .then(data => {
                // Handle response (success or error)
                alert(data.message);
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        });
    });

    function openCart() {
        window.location.href = "cart.php"; // Redirect to cart page
    }

    function toggleChatbot() {
        const chatbot = document.getElementById("chatbot");
        chatbot.style.display = chatbot.style.display === "block" ? "none" : "block";
    }

    function sendMessage() {
        const userInput = document.getElementById("userInput").value;
        const chatHistory = document.getElementById("chatHistory");

        if (userInput.trim() !== "") {
            // Show User Message
            const userDiv = document.createElement("div");
            userDiv.classList.add("user-message");
            userDiv.textContent = userInput;
            chatHistory.appendChild(userDiv);

            // Show Loading Dots
            const loadingDiv = document.createElement("div");
            loadingDiv.classList.add("bot-message");
            loadingDiv.innerHTML = "Typing...";
            chatHistory.appendChild(loadingDiv);
            chatHistory.scrollTop = chatHistory.scrollHeight;

            // Clear Input
            document.getElementById("userInput").value = "";

            // Send AJAX Request
            fetch("chatbot_response.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `message=${encodeURIComponent(userInput)}`
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.remove(); // Remove loading message

                const botDiv = document.createElement("div");
                botDiv.classList.add("bot-message");
                botDiv.textContent = data.response;
                chatHistory.appendChild(botDiv);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            })
            .catch(error => {
                console.error("Error:", error);
            });
        }
    }

</script>