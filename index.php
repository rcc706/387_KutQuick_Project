<?php
    // FILE IS FOR SIGNUP PAGE 
    
    // Configure the session and include functions to output errors (if any)
    require "config_session.php";
    require "view.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sign Up - Kut Quick</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <h1>Create Your Account</h1>
        <p>Join Kut Quick by filling out the form below:</p>

        <form action="signup.php" method="post">
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            <button type="submit">Sign Up</button>
        </form>

        <?php 
            // Function from view.php --> outputs errors (if any)
            check_signup_errors();
        ?>

        <p>Already have an account? <a href="loginpage.php">Log in here</a>.</p>
    </div>
</body>
</html>
