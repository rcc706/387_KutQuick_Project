<?php
    require "config_session.php";
    require "view.php";
    require "connection.php";
    require "model.php";
    require "contr.php";

    if (!isset($_SESION["username"]) && $_SESSION["username"] === NULL) {
	header("Location: logout.php");
	exit();
    }

    if (!isset($_SESION["userrole"]) && $_SESSION["userrole"] === NULL) {
	header("Location: logout.php");
	exit();
    }

    if ($_SESSION['userrole'] !== "user") {
	header("Location: logout.php");
	exit();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $_SESSION['username']; ?>'s Video Library</title>
    <link rel="stylesheet" href="style.css">
    <script>
	function confirmDelete(infilename, revfilename) {
		if (confirm("Are you sure you want to delete this file?")) {
			passFilename(infilename, revfilename);
		}
	}


        function passFilename(filename, receiverFile) {

            // Construct the URL with the filename as a query parameter
            var url = receiverFile + '?filename=' + encodeURIComponent(filename);

            // Redirect the browser to the constructed URL
            window.location.href = url;
        }
    </script>
</head>
<body>
    <div class="wrapper" style="width: 80%">
        <h1><?php echo $_SESSION['username']; ?> Videos</h1>
        <p>This is your video library. Download or Delete a file below:</p>
	<?php
		$file_data = grab_user_files_data($pdo, $_SESSION["username"]);
		if ($file_data) {
			echo "<br>";
			echo "<ul style='list-style-type: none; text-align: left;'>";

			foreach ($file_data as $row) {
				echo '<li>Label: ' . $row[2] . '</li>';
				echo '<li>Filename: ' . $row[1] . '</li>';
                		echo '<li><button onclick="passFilename(\'' . $row[1] . '\', \'download.php\')">Download</button>';
                		echo ' | <button onclick="confirmDelete(\'' . $row[1] . '\', \'delete.php\')">Delete</button></li><br>';
                }
			echo "<br><br><br>";
			echo "</ul>";

		}  else {
			echo "<p>Upload a video to see it here</p>";
		}


	?>

        <p><a href="submit_login.php">Upload a Video</a></p>

	<br />

	<p><a href="logout.php">Log Out</a></p>

	<br />

	<!-- <p><a style="color: red;" href="" onclick="return confirm('Are you sure you want to delete your account?')">Delete Account</a></p> -->
    </div>
</body>
</html>

