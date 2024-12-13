<?php
    // FILE IS THE LOGIN PAGE
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
    <title>Login - Kut Quick</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <h1>Welcome to Kut Quick</h1>
        <p>Please log in with your username to access your account.</p>

        <form action="login.php" method="post">
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit">Log In</button>
        </form>

        <?php
            // Function from view.php --> output login errors (from session variable)
            check_login_errors();
        ?> 

        <p>Don't have an account? <a href="index.php">Sign up here</a>.</p>
    </div>
</body>
</html>
