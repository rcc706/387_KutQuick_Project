<?php
    // FILE IS USED TO REDIRECT NEW CREATED ACCOUNTS TO THE LOGIN PAGE

    require "config_session.php";

    // Will redirect user to signup page if they haven't successfully signed up yet
    // $_SESSION['access_signup'] is set in signup.php after a successful singup has been achieved
    if (!isset($_SESSION['access_signup']) && $_SESSION['access_signup'] === NULL) {
        header("Location: sign_up.php");
        exit();
    } else {
        unset($_SESSION['access_signup']);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sign-Up Confirmation - Kut Quick</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <h1>Thank You for Signing Up!</h1>
        <p>Your account has been successfully created. Welcome to Kut Quick!</p>
        <p>You can now <a href="loginpage.php">log in</a> and start using our services.</p>
        
        <form action="loginpage.php" method="get">
            <button type="submit">Go to Login</button>
        </form>
    </div>
</body>
</html>
