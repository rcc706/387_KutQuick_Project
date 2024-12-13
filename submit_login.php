<?php
    require "config_session.php";
    require "view.php";

    if (!isset($_SESSION['username']) && $_SESSION['username'] === NULL) {
        header("Location: logout.php");
        exit();
    }

    if (!isset($_SESSION['userrole']) && $_SESSION['userrole'] === NULL) {
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
    <title><?php echo $_SESSION['username']; ?>'s Homepage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <h1>Homepage: <?php echo $_SESSION['username']; ?></h1>
        <p>You're now logged in. Start uploading your videos below:</p>
        <form action="my_upload.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="videoUpload">Upload Video:</label>
                <input type="file" id="videoUpload" name="videoUpload" accept="video/mp4" required>
            </div>
	    <div>
	        <label for="videoLabel">Video Label:</label>
	        <input type="text" id=videoUpload" name="videoLabel" placeholder="Enter a Label" required>
	    </div>
            <div>
                <label for="toleranceSlider">Tolerance Threshold:</label>
                <input type="range" id="toleranceSlider" name="toleranceSlider" min="-40" max="-20" value="-20" required>
                <span id="toleranceValue">-20</span>dB
            </div>
            <div>
                <label for="silDuration">Silence Duration:</label>
                <input type="number" id="videoUpload" name="silDuration" min="1" step="0.01" placeholder="1" required>
            </div>
            <div>
                <label for="easeFactor">Ease Factor:</label>
                <input type="number" id="videoUpload" name="easeFactor" min="0.2" step="0.01" placeholder="0.2" required>
            </div>
            <button type="submit">Submit</button>
        </form>

	<?php 
		check_login_errors();
		success_message(); 
	?>

        <p><a href="video_library.php">Video Library</a></p>

	<br />

	<p><a href="logout.php">Log Out</a></p>
    </div>
    <script>
        const toleranceSlider = document.getElementById('toleranceSlider');
        const toleranceValue = document.getElementById('toleranceValue');
        toleranceSlider.oninput = function() {
            toleranceValue.innerText = this.value;
        };
    </script>

</body>
</html>

