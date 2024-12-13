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

		if (file_exists($full_getting_file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: video/mp4');
			header('Content-Disposition: attachment; filename="' . $getting_file . '"');
			header('Content-Length: ' . filesize($full_getting_file));
			header('Cache-Control: no-cache, no-store, must-revalidate');
			header('Pragma: no-cache');
			header('Expires: 0');

			flush();
			readfile($full_getting_file);
		}


		header("Location: video_library.php");
		exit();
	}
   }
?>


