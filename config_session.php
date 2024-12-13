<?php
    // FILE USED TO CONFIGURE THE SESSION

    if (session_status() !== PHP_SESSION_ACTIVE || session_status() === PHP_SESSION_NONE) {
        session_start();
    } else {
        if (!isset($_SESSION["last_regeneration"])) {
            session_regenerate_id();
            $_SESSION["last_regeneration"] = time();
        } else {
            // interval to update the session id generated (30 minutes)
            $interval = 60 * 30; 
            if (time() - $_SESSION["last_regeneration"] >= $interval) {
                session_regenerate_id();
                $_SESSION["last_regeneration"] = time();    
            }
        }
    }
?>
