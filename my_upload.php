<?php

require "config_session.php";  // Ensure session and authentication is set up

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tolslider = htmlspecialchars(trim($_POST["toleranceSlider"]));
    $videoLabel = htmlspecialchars(trim($_POST["videoLabel"]));
    $silduration = htmlspecialchars(trim($_POST["silDuration"]));
    $easefactor = htmlspecialchars(trim($_POST["easeFactor"]));

    try {
        // Database connection and model inclusion
        require "connection.php";
        require "model.php";
	require "contr.php";

        // ERROR HANDLING
        $errors = [];

	if (!valid_label($videoLabel)) {
		$errors["char_label"] = "Label must be between 5 and 25 alphanumeric characters";
	}

	if (!valid_factor(floatval($silduration), 1)) {
                $errors["invalid_sildur"] = "Silence Duration must be greater than or = 1";
	}

	if (!valid_factor(floatval($easefactor), 0.2)) {
                $errors["invalid_easefact"] = "Ease Factor must be greater than or = 0.2";
	}

        // Check if user has exceeded file capacity

	    $username = $_SESSION["username"];
	    
	    // Prepare and execute the query to find the user
	    $stmt = $pdo->prepare("SELECT USER_ID, USER_NAME FROM USERS WHERE USER_NAME = ?");
	    $stmt->execute([$username]);
	    
	    // Fetch the user record
	    $row = $stmt->fetch(PDO::FETCH_ASSOC);
	    if (!$row) {
	        die("User not found!");  // Exit if no user found
	    }
	    
	    // Prepare and execute the query to find user files
	    $stmt1 = $pdo->prepare("SELECT USER_ID, FILE_ID FROM USER_TO_FILES WHERE USER_ID = ?");
	    $stmt1->execute([$row["USER_ID"]]);
	    
	    // Fetch all rows and count the files
	    $file_rows = $stmt1->fetchAll(PDO::FETCH_ASSOC);
	    $file_count = count($file_rows);
	    
	    // Check file count condition
	   if (((int)$file_count) > 3 && ((int)$file_count) != 0) {
	        $errors["file_cap"] = "File limit reached. Delete a file in your video library. " . $file_count;
	   }

	   if (!empty($errors)) {
	        $_SESSION["errors_login"] = $errors;
	        header("Location: submit_login.php");
	        exit();
	   }

        // Validate file type (MP4 only)
        $allowed_types = ["video/mp4"];
        if (!in_array($_FILES['videoUpload']['type'], $allowed_types)) {
            $errors["invalid_type"] = "Invalid file type. Types Supported: .mp4";
        }

        // Max file size 5 GB (5 * 1024 * 1024 * 1024 bytes)
        $max_size = 5 * 1024 * 1024 * 1024;  // 5 GB
        if ($_FILES['videoUpload']['size'] > $max_size) {
            $errors['file_size_exceeded'] = 'File size exceeds the 5 GB limit';
        }

        // Min file size (1 KB)
        $min_size = 1 * 1024; // 1 KB
        if ($_FILES["videoUpload"]["size"] < $min_size) {
            $errors["file_size_small"] = 'File must be at least 1024 bytes';
        }

        // Handle file upload errors (PHP file upload constants)
        if ($_FILES["videoUpload"]["error"] !== UPLOAD_ERR_OK) {
            switch ($_FILES["videoUpload"]["error"]) {
                case UPLOAD_ERR_NO_FILE:
                    $errors["no_up_file"] = "No file was uploaded";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errors["partially_up"] = "File only partially uploaded";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errors["cant_write"] = "Failed to write file to disk";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errors["no_tmp_dir_ups"] = "No temporary directory detected"; 
                    break;
                default:
                    $errors["unknown_error"] = "Unknown upload error";
                    break;
            }
        }

        // If there are no errors, continue with file handling
        if (empty($errors)) {
            // Check if the file already exists
            $check_file_exists = "/var/www/html/public_html/uploads/" . $_SESSION["username"] . "_" . basename($_FILES["videoUpload"]["name"]);
            if (file_exists($check_file_exists)) {
                $errors["filename_exists"] = "Filename already exists";
                $_SESSION["errors_login"] = $errors;
                header("Location: submit_login.php");
                exit();
            }

	   $part_path = "uploads/" . $_SESSION["username"] . "_" . basename($_FILES["videoUpload"]["name"]);

            // Move the uploaded file to the desired directory
            $upload_dir = '/var/www/html/public_html/input/';
            $uploaded_file = $upload_dir . basename($_FILES['videoUpload']['name']);

            // Check if the move operation is successful
            if (!move_uploaded_file($_FILES['videoUpload']['tmp_name'], $uploaded_file)) {
                $errors["failed_move"] = "Move uploaded file failed";
                $_SESSION["errors_login"] = $errors;
                header("Location: submit_login.php");
                exit();
            }

            // Change file permissions to allow read/write
            chmod($uploaded_file, 0644);

            // Path to the Python script and virtual environment
            $scriptPath = "/var/www/html/public_html/hayden_python.py";
            $venvPath = "/var/www/html/public_html/venv/bin/activate";
            $outputPath = "/var/www/html/public_html/output/" . $_SESSION["username"] . "_" . basename($_FILES['videoUpload']['name']);

            // Check if output file already exists
            if (!file_exists($outputPath)) {
		$out_fp = fopen($outputPath, "wb");
		fclose($out_fp);
            }

            // Run the command using bash -i to activate the virtual environment and run the script
            $command = "bash -i -c 'source " . $venvPath . " && python3 " . $scriptPath . " " . $uploaded_file . " " . $outputPath . " " . $tolslider . " " . $silduration . " " . $easefactor  . "'";
            exec($command, $output, $exitCode);

            // Check if Python script execution was successful
            if ($exitCode !== 0) {
                $errors["failed_pythonscript"] = "Video failed to upload. Exit Code: " . $exitCode . ". Output: " . implode("\n", $output);
                $_SESSION["errors_login"] = $errors;
                header("Location: submit_login.php");
                exit();
            }

            // Rename output file if needed (to ensure the user doesn't exceed the max file limit)
	    $uploadsPath = "/var/www/html/public_html/uploads/" . $_SESSION["username"] . "_" . basename($_FILES["videoUpload"]["name"]);
	    chmod($outputPath, 0644);
	    chmod($uploadsPath, 0644);

            if (!rename($outputPath, $uploadsPath)) {
                $errors["user_max_file_cap"] = "Rename failed. File capacity reached. Delete a file from video library.";
                $_SESSION["errors_login"] = $errors;
                header("Location: submit_login.php");
                exit();
            }

            $new_filename = $_SESSION["username"] . "_" . basename($_FILES['videoUpload']['name']);
	    $new_filepath = "uploads/" . $new_filename;

	    if (filesize($uploaded_file) == 0) {
		$errors["filesize_zero"] = "No silence detected";                                                            
		$_SESSION["errors_login"] = $errors;                                                                                
 		header("Location: submit_login.php");                                                                                
		exit();
	    }

            // Insert file info into the database
            if (!insert_file($pdo, $_SESSION["username"], $new_filename, $videoLabel, $new_filepath)) {
                $errors["user_max_file_cap"] = "File name too long!";
                $_SESSION["errors_login"] = $errors;
                header("Location: submit_login.php");
                exit(); 
            }

	    // Delete uploaded file in the input directory
		if (file_exists($uploaded_file)) {
			if (!unlink($uploaded_file)) {
				$errors["file_exists"] = "File already exists in output";                                                            
				$_SESSION["errors_login"] = $errors;                                                                                
		 		header("Location: submit_login.php");                                                                                
				exit();
			}
		}

            // If everything went fine, proceed with a redirect to the login page or another page as needed
	    if (empty($errors)) {
	    	$sucs["good_message"] = "Video uploaded successfully!";
            	$_SESSION["upvid_success"] = $sucs;
            	header("Location: submit_login.php");
                exit();
	    } else {
		$_SESSION["errors_login"] = $errors; 
		header("Location: submit_login.php"); 
		exit();
	    }
        } else {
            // If there are errors, handle them and redirect back to the login page
            $_SESSION["errors_login"] = $errors;
            header("Location: submit_login.php");
            exit();
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        die("Connection failed: " . $e->getMessage());
    }
}
?>
