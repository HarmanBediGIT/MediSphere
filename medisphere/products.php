<?php
    // Start a session
    session_start();

    require 'db_conn.php';

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
    $sql = "SELECT * FROM products"; // Adjust based on your table structure
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

    <head>
        <meta charset="utf-8">
        <title>Products</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind">
        <link rel="stylesheet" href="css/index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
            integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="js/script.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <div id="progressBarContainer">
            <div id="progressBar"></div>
        </div>
        
        <nav>
            <div class="logo">
                <!-- <div class="logoImage"> <img src="images/logo.png"> </div> -->
                <div class="logoName" style="font-size:30px;">
                    MediSphere
                    <?php if ($isLoggedIn): ?>
                        <h5 style="font-size:15px;"> Welcome <?php echo $name; ?> </h5>
                        <!-- <div id="cartButton" class="cart-button">
                            <a href="cart.php" style="text-decoration: none; color: inherit;">
                                <h2 style="color: white;">&#x1F6D2;</h2>
                            </a>
                        </div> -->
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
                    <li><a class="active" href="products.php">Products</a></li>
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

        <div id="cartButton" class="cart-button" onclick="openCart()">
            <i class="fas fa-shopping-cart"></i>
        </div>

        <!-- Search bar implementation -->
        <div class="search-container" style="position: relative;">  <!-- Add position: relative to the parent container -->
            <form action="" method="POST" id="searchForm" autocomplete="off">
                <input type="text" id="searchInput" placeholder="Search products..." name="search">
                <button type="submit">Search</button>
            </form>
            <div id="searchResults" class="search-results"></div> <!-- Results will show here -->
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

        <!-- Products Grid -->
        <div class="products-container">
            <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $colors = explode(',', $row["colors"]); 
                        // Construct the path to the product image
                        $imagePath = 'images/' . htmlspecialchars($row["name"]) . '.jpg';
                        
                        // Check if the file exists to avoid broken image links
                        if (!file_exists($imagePath)) {
                            $imagePath = 'images/default.jpg';
                        }
                        
                        // Product card layout with image on the left and product details on the right
                        echo '<div class="product-card">';
                        
                            // Product image
                            echo '<div class="product-image">';
                                echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($row["name"]) . '">';
                            echo '</div>';
                        
                            // Product information (name, price, description)
                            echo '<div class="product-info">';
                                echo '<div class="product-header">';
                                    echo '<div class="product-name">' . htmlspecialchars($row["name"]) . '</div>';
                                    echo '<div class="product-price">$' . htmlspecialchars($row["price"]) . '</div>';
                                echo '</div>';
                                echo '<div class="product-description">' . htmlspecialchars($row["description"]) . '</div>';
                                // Check if colors are available for the product
                                if (!empty($row["colors"])) {
                                    echo '<div class="product-colors">';
                                    
                                    // Split the colors into an array and render each as a colored circle
                                    $colors = explode(',', $row["colors"]); // Assuming colors are comma-separated
                                    foreach ($colors as $color) {
                                        $color = trim($color); // Remove any extra spaces
                                        echo '<span class="color-circle" style="background-color: ' . htmlspecialchars($color) . ';"></span>';
                                    }
                                    
                                    echo '</div>';
                                }

                                // Product buttons (aligned horizontally below description)
                                echo '<div class="product-actions">';
                                    echo '<button class="product-button" onclick="window.location.href=\'detailed_products.php?id=' . $row["id"] . '&code=' . $row["prod_code"] . '\'">View Details</button>';
                                    
                                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                                        // Split colors and format them correctly
                                        $colors = explode(', ', htmlspecialchars($row["colors"]));
                                        $formattedColors = array_map(function($color) {
                                            return "'" . trim($color) . "'"; // Surround each color with single quotes
                                        }, $colors);
                                        
                                        $colorsString = implode(', ', $formattedColors); // Join colors with commas
                                        
                                        $sizes = explode(', ', htmlspecialchars($row["sizes"]));
                                        $formattedSizes = array_map(function($size) {
                                            return "'" . trim($size) . "'"; // Surround each color with single quotes
                                        }, $sizes);
                                        
                                        $sizesString = implode(', ', $formattedSizes); // Join colors with commas
                                        
                                        echo '<button class="product-button" onclick="openEditModal(\'' . htmlspecialchars($row["prod_code"]) . '\', \'' . htmlspecialchars($row["name"]) . '\', \'' . htmlspecialchars($row["description"]) . '\', [' . $colorsString . '], [' . $sizesString . '], ' . htmlspecialchars($row["price"]) . ', ' . htmlspecialchars($row["qty"]) . ')">Edit Product</button>';
                                        echo '<button class="product-button remove-btn" onclick="deleteProduct(\'' . htmlspecialchars($row["prod_code"]) . '\')">Remove Product</button>';
                                    }
                                echo '</div>'; // Close product-actions
                            echo '</div>'; // Close product-info
                        echo '</div>'; // Close product-card
                    }
                } 
                else {
                    echo '<div class="product-card" style="margin-left:40%;">';
                        echo '<h1> No products found </h1>';
                    echo '</div>';
                }
            ?>
        </div>

        <!-- Modal for Editing Product -->
        <div id="editModal" class="modal" style="display:none;">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h2 class="modal-title">Edit Product</h2>
                    <button class="close-btn" onclick="closeEditModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="productCode">Product Code</label>
                        <input type="text" id="productCode" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label for="productName">Product Name</label>
                        <input type="text" id="productName" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="productDescription">Description</label>
                        <textarea id="productDescription" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="productColors">Available Colors</label>
                        <input type="text" id="productColors" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="productSize">Available Sizes</label>
                        <input type="text" id="productSize" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="productPrice">Price</label>
                        <input type="number" id="productPrice" step="0.01" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="productQty">Quantity</label>
                        <input type="number" id="productQty" min="0" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="saveChangesBtn" class="btn-secondary">Save Changes</button>
                    <button class="btn-secondary" onclick="closeEditModal()">Close</button>
                </div>
            </div>
        </div>

        <?php
            // Check if the logged-in user is an admin
            if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
            ?>
            <div class="login-container">
                <button type="submit" class="category-btn" id="openModalBtn">Add New Product</button>
            </div>
        <?php } ?>

        <!-- Modal structure -->
        <div id="productModal" class="modal">
            <div class="modal-content">
                <span class="close" style="font-weight:bolder; cursor:pointer;">&times;</span>
                <h2>Add New Product</h2>
                <div id="feedbackMessage" style="display: none; margin-bottom: 10px;"></div> <!-- Feedback message -->
                <form id="categoryForm">
                    <div class="form-group">
                        <label for="code">Unique Code :</label>
                        <input type="text" id="code" name="code" placeholder="eg: OXY, THM, GLV, MSK, etc." required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name :</label>
                        <input type="text" id="name" name="name" placeholder="eg: Digital Thermometers, etc." required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description :</label>
                        <input type="textarea" id="description" name="description" placeholder="Write description for this product" required>
                    </div>
                    <div class="form-group">
                        <label for="qty">Qty :</label>
                        <input type="number" id="qty" name="qty" placeholder="eg: 198" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price :</label>
                        <input type="number" id="price" name="price" placeholder="eg: 9 (price is in dollars)" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" id="addCategoryBtn" class="category-btn">Add</button>
                        <button type="button" class="cancel-btn">Cancel</button>
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
    window.addEventListener('scroll', function() {
        var nav = document.querySelector('nav');
        if (window.scrollY > 0) {
            nav.classList.add('scrolled'); // Apply gradient background
        } 
        else {
            nav.classList.remove('scrolled'); // Revert to transparent background
        }
    });
    
    document.getElementById('searchResults').style.display = 'none';
    document.getElementById('searchInput').addEventListener('input', function () {
        const query = this.value;

        if (query.length > 0) {
            fetch('search_products.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `search=${encodeURIComponent(query)}`
            })
            .then(response => response.json())
            .then(data => {
                const searchResults = document.getElementById('searchResults');
                searchResults.innerHTML = ''; // Clear previous results

                if (data.length > 0) {
                    // Calculate dynamic margin based on the number of results
                    const resultCount = data.length;
                    let dynamicMarginTop = 100 + (resultCount - 1) * 50; // Increment margin by 50px for each result
                    searchResults.style.marginTop = `${dynamicMarginTop}px`;

                    data.forEach(product => {
                        const resultDiv = document.createElement('div');
                        resultDiv.textContent = product.name;
                        resultDiv.style.cursor = 'pointer'; // Indicate that the result is clickable
                        
                        // Modify the click event to scroll to the product card
                        resultDiv.addEventListener('click', () => {
                            const productCard = document.getElementById(`product-${product.id}`);
                            if (productCard) {
                                // Scroll to the product card smoothly
                                productCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        });
                        searchResults.appendChild(resultDiv);
                    });
                    searchResults.style.display = 'block'; // Make sure it shows up
                } 
                else {
                    searchResults.innerHTML = '<div>No products found!</div>';
                    searchResults.style.marginTop = `100px`;
                    searchResults.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        } 
        else {
            document.getElementById('searchResults').innerHTML = ''; // Clear results if input is empty
            document.getElementById('searchResults').style.display = 'none'; // Hide container
        }
    });

    // Get modal elements
    const modal = document.getElementById("productModal");
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
        // Store the message in local storage
        localStorage.setItem('feedbackMessage', message);
        
        // Refresh the page
        location.reload();
    }

    // On page load, check for the feedback message
    $(document).ready(function() {
        const message = localStorage.getItem('feedbackMessage');
        if (message) {
            // Determine background color based on message content
            const isErrorMessage = message.includes("Error") || message.includes("All fields are required.");
            const backgroundColor = isErrorMessage ? '#f44336' : '#4caf50'; // Red for errors, green for success

            // Create a feedback message element
            const feedbackDiv = $('<div></div>').text(message).css({
                position: 'fixed',
                top: '20px',
                left: '50%',
                transform: 'translateX(-50%)',
                background: backgroundColor,
                color: '#fff',
                padding: '10px',
                borderRadius: '5px',
                zIndex: '1000',
                display: 'none'
            }).appendTo('body').fadeIn(400).delay(3000).fadeOut(400, function() {
                $(this).remove(); // Remove the element after fading out
            });

            // Remove the message from local storage
            localStorage.removeItem('feedbackMessage');
        }
    });

    // Add Category button action
    $('#addCategoryBtn').on('click', function() {
        // Collect form data
        const code = $('#code').val().trim();
        const name = $('#name').val();
        const qty = $('#qty').val().trim();
        const description = $('#description').val();
        const price = $('#price').val().trim();

        // Validate that all fields are filled
        if (!code || !name || !qty || !description || !price) {
            showFeedbackMessage('All fields are required.'); // Show error message
            return; // Exit the function to prevent further action
        }

        // Send data to the PHP script via jQuery AJAX
        $.ajax({
            url: 'add_product.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                code: code,
                name: name,
                qty: qty,
                description: description,
                price: price
            }),
            success: function(response) {
                showFeedbackMessage(response); // Show success or error message
                closeModal(); // Close the modal
                // Optionally, clear the form
                $('#categoryForm')[0].reset();
            },
            error: function(xhr, status, error) {
                // Log the error in case of network issue
                showFeedbackMessage('An error occurred while adding the product.'); // Show error message
            }
        });
    });

    function openEditModal(code, name, description, colors, sizes, price, qty) {
        // Set the values in the modal
        document.getElementById('productCode').value = code;
        document.getElementById('productName').value = name;
        document.getElementById('productDescription').value = description;
        document.getElementById('productColors').value = colors;
        document.getElementById('productSize').value = sizes;
        document.getElementById('productPrice').value = price;
        document.getElementById('productQty').value = qty; // Set qty in modal

        // Show the modal
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
        $('#editModal').css('display', 'none'); // Hide the modal
    }

    document.getElementById('saveChangesBtn').addEventListener('click', function() {
        const prod_code = document.getElementById('productCode').value;
        const name = document.getElementById('productName').value;
        const description = document.getElementById('productDescription').value;
        const colors = document.getElementById('productColors').value;
        const sizes = document.getElementById('productSize').value;
        const price = document.getElementById('productPrice').value;
        const qty = document.getElementById('productQty').value; // Get qty from modal

        // Send the updated details via AJAX
        $.ajax({
            url: 'update_product.php',
            type: 'POST',
            data: {
                prod_code: prod_code,
                name: name,
                description: description,
                colors: colors,
                sizes: sizes,
                price: price,
                qty: qty // Send qty to the server
            },
            success: function(response) {
                if (response === 'success') {
                    showFeedbackMessage('Product updated successfully.');
                    location.reload(); // Reload the page to see changes
                } else {
                    showFeedbackMessage('Error updating product: ' + response);
                }
            },
            error: function(xhr, status, error) {
                showFeedbackMessage('An error occurred while updating the product: ' + error);
            }
        });
    });


    function deleteProduct(code) {
        console.log("Deleting product with code:", code); // Log the code being deleted
        if (confirm("Are you sure you want to delete this product?")) {
            $.ajax({
                url: 'delete_product.php',
                type: 'POST',
                data: { code: code },
                success: function(response) {
                    if (response === 'success') {
                        $('#product-' + code).remove();
                        showFeedbackMessage('Product deleted successfully.');
                    } 
                    else {
                        showFeedbackMessage('Error deleting product: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    showFeedbackMessage('An error occurred while deleting the product: ' + error);
                }
            });
        }
    }

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