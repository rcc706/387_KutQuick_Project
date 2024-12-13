<?php
    // DESTROY SESSION AND LOGOUT FROM KUTQUICK

    // Configure the session
    require "config_session.php";

    // User needs to be logged in, in order to log out
    if (!isset($_SESSION['username']) && $_SESSION['username'] === NULL) {
        header("Location: loginpage.php");
        exit();
    } else {
        unset($_SESSION['username']);
	unset($_SESSION['userrole']);
    }

    // Completely destroy the session 
    $_SESSION = array();

    if (isset($_COOKIE[session_name()])) {
	setcookie(session_name(), '', time()-42000, '/');
    }

    session_destroy();
    

    // Redirect to the login page
    header("Location: loginpage.php");
    exit();
?>
