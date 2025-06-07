<?php
    require 'db_conn.php';
    header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1
    header("Pragma: no-cache"); // HTTP 1.0
    header("Expires: 0"); // Proxies
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediSphere - Login</title>
    <link rel="stylesheet" href="css\login.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <div class="login-left">
            <h2>Welcome to MediSphere</h2>
            <p>Login to access the best medical equipment platform</p>
            <form action="login_process.php" method="POST">
                <div class="input-box">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username or Email" required>
                </div>
                <div class="input-box">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="extra-options">
                    <a href="forgot_passwd.php">Forgot password?</a> 
                    <a href="signup.php">New User?Sign Up</a>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
        <div class="login-right">
            <img src="images/login.jpg" alt="Medical Illustration" />
        </div>
    </div>    
</body>
</html>
