<?php 

    // FILE USED TO INTERACT WITH THE DB

    declare(strict_types=1);

    // Gets the username from the database and returns it
    function get_username(object $pdo, string $username) {
        $ueStmt = $pdo->prepare("SELECT USER_NAME FROM USERS WHERE USER_NAME = :uName");
		$ueStmt->bindParam(':uName', $username);
		$ueStmt->execute();

        $row = $ueStmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    // Gets the email from the database and returns it
    function get_email(object $pdo, string $email) {
        $ueStmt = $pdo->prepare("SELECT USER_EMAIL FROM USERS WHERE USER_EMAIL = :uEmail");
		$ueStmt->bindParam(':uEmail', $email);
		$ueStmt->execute();

        $row = $ueStmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    // Gets the user information from the db
    function get_user(object $pdo, string $username) {
        $stmt = $pdo->prepare("SELECT USER_NAME, USER_ROLE, USER_PASSWORD FROM USERS WHERE USER_NAME = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user; 
    }

    // Creates a new user and inserts that data into the db
    function create_user(object $pdo, string $username, string $email, string $password, string $account) {
        $stmt = $pdo->prepare("INSERT INTO USERS (USER_NAME, USER_EMAIL, USER_ROLE, USER_PASSWORD) VALUES (:username, :email, :acc, :pass)");
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(":pass", $hashed_password);
	$stmt->bindParam(":acc", $account);
        $stmt->execute();
    }

    // User files from User_to_Files
    function valid_user_files_count(object $pdo, string $username, array $errory) {
        // Prepare and execute the query to find the user
        $stmt = $pdo->prepare("SELECT USER_ID, USER_NAME FROM USERS WHERE USER_NAME = ?");
        if (!($stmt->execute([$username]))) {
            $errory["vufc_finduser"] = "Valid user file count: No user found!";
        }

        // Fetch the user record
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists
        if (!$row) {
	    $errory["vufc_row1"] = "Valid user file count: Row didn't run! No user found!";
        }

        // Prepare and execute the query to find user files
        $stmt1 = $pdo->prepare("SELECT USER_ID, FILE_ID FROM USER_TO_FILES WHERE USER_ID = ?");
        if (!$stmt1->execute([$row["USER_ID"]])) {
            // Handle execution failure for file query
            $errory["vufc_finduserfiles"] = "Valid user file count: Find user files stmt failed!";
        }

        // Fetch all rows and count the files
        $file_rows = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $file_count = count($file_rows);

        // Return true if the user has fewer than 3 files
        return $file_count < 3;
    }

    // Insert edited file into the database
    function insert_file(object $pdo, string $username, string $file_name, string $file_label, string $file_path) {
        // Prepare and execute the query to find the user
        $stmt = $pdo->prepare("SELECT USER_ID, USER_NAME FROM USERS WHERE USER_NAME = ?");
        if ($stmt->execute([$username])) {
            // Fetch the user record
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if user exists
            if (!$row) {
                return false; // User not found
            }

            // Find the current FILE_ID and +1
            $max_fileID = "SELECT FILE_ID FROM FILES WHERE FILE_ID = (SELECT MAX(FILE_ID) FROM FILES)";
			$max_stmt = $pdo->prepare($max_fileID);
			$max_stmt->execute();
			$table_row = $max_stmt->fetch(PDO::FETCH_ASSOC);
			$insert_max_fileID = $table_row["FILE_ID"] + 1; 


            // Prepare and execute the query to 
            //$full_filename = $username . "_" . $file_name;
            $stmt1 = $pdo->prepare("INSERT INTO FILES VALUES (?, ?, ?, ?)");
            if ($stmt1->execute([$insert_max_fileID, $file_name, $file_label, $file_path])) {
                $stmt2 = $pdo->prepare("INSERT INTO USER_TO_FILES VALUES (?, ?)");
                if ($stmt2->execute([$row["USER_ID"], $insert_max_fileID])) {
                    return true; 
                } 
            } 
        } 
        return false; 
    }


    function del_db_file_data(object $pdo, string $file_name) {
        // Step 1: Fetch the file ID from the FILES table
        $fid_query = "SELECT FILE_ID, FILE_NAME FROM FILES WHERE FILE_NAME = ?";
        $fid_stmt = $pdo->prepare($fid_query);
        
        // Execute the SELECT query and fetch the result
        if ($fid_stmt->execute([$file_name])) {
            $row = $fid_stmt->fetch(PDO::FETCH_ASSOC);
    
            // If no file is found, return false
            if (!$row) {
                return false; // File not found
            }
    
            // File ID
            $file_id = $row['FILE_ID'];
    
            // Step 2: Delete the corresponding entry in USER_TO_FILES
            $del_u2f_query = "DELETE FROM USER_TO_FILES WHERE FILE_ID = ?";
            $del_u2f_stmt = $pdo->prepare($del_u2f_query);
    
            if (!$del_u2f_stmt->execute([$file_id])) {
                return false; // Deletion failed
            }
    
            // Step 3: Delete the file record from the FILES table
            $del_f_query = "DELETE FROM FILES WHERE FILE_ID = ?";
            $del_f_stmt = $pdo->prepare($del_f_query);
    
            if (!$del_f_stmt->execute([$file_id])) {
                return false; // Deletion failed
            }
    
            // If all deletions are successful, return true
            return true;
        }
    
        return false; // Query execution failed
    }
    

    function grab_user_files_data(object $pdo, string $username) {
        // Step 1: Get the user ID from the username
        $get_user_id = "SELECT USER_ID, USER_NAME FROM USERS WHERE USER_NAME = ?";
        $get_user_id_stmt = $pdo->prepare($get_user_id);
    
        // Execute and check for valid result
        if ($get_user_id_stmt->execute([$username])) {
            $row = $get_user_id_stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return false; // No user found
            }
    
            // Step 2: Get files associated with the user
            $uid = $row["USER_ID"];
            $get_files_query = "SELECT USER_ID, FILE_ID FROM USER_TO_FILES WHERE USER_ID = ?";
            $get_files_stmt = $pdo->prepare($get_files_query);
    
            if ($get_files_stmt->execute([$uid])) {
                $file_data = [];
    
                // Step 3: Loop through each file associated with the user
                while ($row = $get_files_stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (!$row) {
                        return false; // No file found
                    }
    
                    // Get file details
                    $get_fl = "SELECT FILE_ID, FILE_NAME, FILE_LABEL, PARTIAL_PATH FROM FILES WHERE FILE_ID = ?";
                    $get_fl_stmt = $pdo->prepare($get_fl);
    
                    if ($get_fl_stmt->execute([$row["FILE_ID"]])) {
                        $file_row = $get_fl_stmt->fetch(PDO::FETCH_ASSOC);
                        if ($file_row) {
                            // Append file data to the array
                            $file_data[] = [
                                $file_row["FILE_ID"],
                                $file_row["FILE_NAME"],
                                $file_row["FILE_LABEL"],
                                $file_row["PARTIAL_PATH"]
                            ];
                        }
                    }
                }
    
                // Return the collected file data or an empty array if no files found
                return !empty($file_data) ? $file_data : [];
            }
        }
        return false; // User or files not found
    }

	function del_user(object $pdo, string $username) {
		$got_user = get_user($pdo, $username);
		$del_query = "DELETE FROM USERS WHERE USER_NAME = ?";
		$del_stmt = $pdo->prepare($del_query);
		if ($del_stmt->execute([$got_user["USER_NAME"]])) {
			return true;
		} else {
			return false;
		}
	}

?>
