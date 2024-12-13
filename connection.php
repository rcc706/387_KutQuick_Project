<?php
    // Information for db connection
    $servername = "localhost";
    $db_username = "group4_f24";
    $db_password = "387-kutquickproject-2024";
    $db = "group4_f24";

    // Create PDO object to connect to the db
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$db", $db_username, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOExcpetion $e) {
        echo "Connection failed: " . $e->getMessage();
    }

?>
