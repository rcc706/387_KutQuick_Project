<?php

	// starting the session and requiring the config_session.php file
	require 'config_session.php'; 

	// If the form was submitted correctly (using post method)
	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		// Get user input from the login form
		$username = htmlspecialchars(trim($_POST["username"]));
		$password = htmlspecialchars($_POST["password"]);

		try{
			// Connect to db, use model to interact with db, use contr to perform form checks
			require "connection.php";
			require_once "model.php";
			require_once "contr.php";

			// ERROR HANDLERS
			$errors = [];

			if (login_input_empty($username, $password)) {
				$errors["empty_input"] = "Please fill in all fields";
			}

			if (!valid_username($username, $errors)) {
				$errors["invalid_username"] = "Not a valid username";
			}

			// Get the user information from the db
			$got_user = get_user($pdo, $username);

			if (is_username_wrong($got_user)) {
				$errors["login_incorrect"] = "Incorrect login info";
			}

			if (!is_username_wrong($got_user) && is_password_wrong($password, $got_user['USER_PASSWORD'])) {
				$errors["login_incorrect"] = "Username and password do not match";
			}

			if (empty($errors) && !isset($_SESSION['username']) && $_SESSION['username'] === NULL) {
				// $_SESSION['username'] is used as a check for a successful login attempt
				$_SESSION['username'] = $got_user['USER_NAME'];
				$_SESSION['userrole'] = $got_user['USER_ROLE'];

				if ($got_user["USER_ROLE"] == "admin") {
					// User is an admin --> redirect to admin homepage
					header("Location: admin_homepage.php");
					exit();
				} else if ($got_user["USER_ROLE"] == "user") {
					// User is a normal user --> redirect to user homepage
					header("Location: submit_login.php");
					exit();
					
				} else {
					// USER_ROLE is invalid --> output error and redirect to loginpage
					$errors["invalid_account"] = "Invalid account role. Must be user or admin.";
					$_SESSION["errors_login"] = $errors;
					header("Location: loginpage.php");
					exit();
				}
			} else {
				// Add errors to the session variable (to be printed out on login page)
				$_SESSION["errors_login"] = $errors;
				header("Location: loginpage.php");
				exit();
			}
		}
		catch(PDOException $e) {
				// db connection failed
    			die("Connection failed: " . $e->getMessage());
		}
	} else {
		header("Location: loginpage.php");
		exit();
	} 
?>

