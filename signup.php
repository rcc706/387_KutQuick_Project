<?php

	// Start the session and require config_session.php
	session_start();
	require "config_session.php"; 

	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		// Grab the form input data
		$username = htmlspecialchars(trim($_POST["username"]));
		$email = htmlspecialchars(trim($_POST["email"]));
    		$password = htmlspecialchars(trim($_POST["password"]));
		$confirm_password = htmlspecialchars(trim($_POST["confirm_password"]));

		 try {

			// Connect to the db, include db interaction functions and included functions	
			require "connection.php";
			require "model.php";
			require "contr.php";

			// ERROR HANDLERS
			$errors = [];

			if (form_input_empty($username, $email, $password, $confirm_password)) {
				$errors["empty_input"] = "Please fill in all fields";
			}

			if (!valid_username($username, $errors)) {
				$errors["invalid_username"] = "Not a valid username";
			}

			if (!valid_email($email)) {
				$errors["invalid_email"] = "Not a valid email address";
			}

			// Check if the password is valid
			$check_password = valid_password($password);

			if (!empty($check_password)) {
				$errors["val_pass_arr"] = $check_password;
			}

			if (!valid_passes($confirm_password, $password)) {
				$errors["passes_dont_match"] = "Passwords do not match";
			}

			if (is_username_taken($pdo, $username)) {
				$errors["username_taken"] = "Username is already taken";
			}

			if (is_email_taken($pdo, $email)) {
				$errors["email_taken"] = "Email is already registered";
			}

			if (empty($errors)) {
				create_user($pdo, $username, $email, $password, "user");
				$_SESSION['access_signup'] = $username;
				header("Location: submit_signup.php");
				die();
			} else {
				// Add errors to the session variable
				$_SESSION["errors_signup"] = $errors;

				header("Location: index.php"); 
				die();
			}
		} catch (PDOException $e) {
			// db connection failed
			die("Connection failed: " . $e->getMessage());
		}
	} else {
		header("Location: index.php");
		die();
	}
?>
