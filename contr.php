<?php

	// FILE USED TO INCLUDE FUNCTIONS AND HANDLE FORM INPUTS

    declare(strict_types=1);

	// Checks if the string passed is not empty
    function non_empty_str(string $val) {
        return is_string($val) && $val !== '' && !empty($val);
    }

	// Checks if the signup form is empty or not
    function form_input_empty(string $username, string $email, string $password, string $confirm_password) {
        if (non_empty_str($username) || non_empty_str($email) || non_empty_str($password) || non_empty_str($confirm_password)) {
            return false; 
        } else {
            return true;  
        }
    }

    // Checks for valid username passed in a form
    function valid_username(string $in_username, array $errors) {
		// min length: 5 chars - max length: 25 chars - only alphanumeric chars
		if (preg_match('/^\w{5,25}$/', $in_username)) {
            		return true; 
		} else {
            		if (strlen($in_username) < 5 || strlen($in_username) > 25) {
				$errors["length_username"] = "Username must be between 5 and 25 characters ";
			}

			if (ctype_alnum($in_username) === false) {
				$errors["username_not_alphanum"] = "Username must be letters (a-zA-Z) or numbers";
			}
            		return false; 
        	}
    }

	// Checks for valid email from the signup form
    function valid_email(string $in_email) {
		if (filter_var($in_email, FILTER_VALIDATE_EMAIL)) {
            return true; 
		} else {
            return false; 
        }
	}

	// Checks the db if the username is already taken (get_username is from model.php)
    function is_username_taken(object $pdo, string $username) {
        if (get_username($pdo, $username)) {
            return true; 
        } else {
            return false; 
        }
    }

	// Checks the db if the email is already taken (get_email is from model.php)
    function is_email_taken(object $pdo, string $email) {
        if (get_email($pdo, $email)) {
            return true; 
        } else {
            return false; 
        }
    }

	// Checks if the password passed is valid
	function valid_password(string $password) {
		$pass_errors = [];

		// Check for minimum or maximum length (between 8 and 30 chars)
		if (strlen($password) < 8 || strlen($password) > 30) {
			$pass_errors["length_password"] = "Password must be between 8 and 30 characters";
		}
	
		// Check for at least one uppercase letter
		if (!preg_match('/[A-Z]/', $password)) {
			$pass_errors["cont_up_password"] = "Password must contain at least one uppercase letter.";
		}
	
		// Check for at least one lowercase letter
		if (!preg_match('/[a-z]/', $password)) {
			$pass_errors["cont_low_password"] = "Password must contain at least one lowercase letter.";
		}
	
		// Check for at least one digit
		if (!preg_match('/[0-9]/', $password)) {
			$pass_errors["cont_num_password"] = "Password must contain at least one number.";
		}
	
		// Check for at least one special character
		if (!preg_match('/[#?!@$%^&*-]/', $password)) {
			$pass_errors["cont_spec_password"] = "Password must contain at least one special character (e.g., #?!@$%^&*-).";
		}

		return $pass_errors;
	}

	// Checks if the password and repeated password (used for signup) are the same
	function valid_passes(string $in_conpassword, string $in_password) {
		if ($in_password === $in_conpassword) {
            return true; 
        } else {
            return false; 
        }
	}

	// Checks if the username is incorrect (login)
	function is_username_wrong(bool|array $user) {
		if (!$user) {
			return true; 
		} else {
			return false; 
		}
	}

	// Checks if the password passed is incorrect (login)
	function is_password_wrong(string $password, string $hashed_password) {
		if (!password_verify($password, $hashed_password)) {
			return true; 
		} else {
			return false; 
		}
	}

	// Checks if all the form inputs (login) are empty or not
	function login_input_empty(string $username, string $password) {
		if (non_empty_str($username) || non_empty_str($password)) {
            		return false; 
        	} else {
            		return true;  
        	}
	}

	// deletes specified file
	function del_file($file_name) {
		if (file_exists($file_name)) {
			unlink($file_name);
			return true;
		} else {
			return false; 
		}
	}

	function valid_label(string $label) {
		// min length: 5 chars - max length: 25 chars - only alphanumeric chars
		if (preg_match('/^\w{5,25}$/', $label)) {
	    		return true; 
		} else {
	    		return false; 
		}
	}

	// can be used to check if the silenceduration or easefactor are valid
	function valid_factor(float $factor, string $range_min) {
		if (is_numeric($factor) && $factor >= $range_min) {
			return true;
		} else {
			return false; 
		}
	}
?>
