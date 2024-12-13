<?php
    require "config_session.php";
    require "connection.php";
    require "model.php";
    require "contr.php";

   if (!isset($_SESSION['username']) && $_SESSION['username'] === NULL) {
        header("Location: loginpage.php");
        exit();
   }

   if ($_SERVER["REQUEST_METHOD"] === "GET") {
	if (isset($_GET["filename"])) {
		$getting_file = basename($_GET['filename']);
		$full_getting_file = '/var/www/html/public_html/uploads/' . $getting_file;
		if (!(del_db_file_data($pdo, $getting_file) && del_file($full_getting_file))) {
			$_SESSION["delete_errors"] = "Error deleting user data";
		}

		// In all cases, redirect to video library
		header("Location: video_library.php");
		exit();
	}
   }
?>


