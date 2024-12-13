<?php 

    // FILE IS USED TO OUTPUT INFO/ERRORS TO THE SIGNUP/LOGIN PAGES
    session_start();

    function check_signup_errors() {
        if (isset($_SESSION["errors_signup"])) {
            $errs = $_SESSION["errors_signup"];

            echo "<br>";
            echo "<ul style='list-style-type: none;'>";

            foreach ($errs as $an_err) {
                if (is_array($an_err)) {
                    foreach ($an_err as $arr_err) {
                        if (!empty($an_err)) {
                            echo '<li class="form-error">' . $arr_err . '</li>';
                        }
                    }
                } else {
                    echo '<li class="form-error">' . $an_err . '</li><br>';
                }
            }

            echo "</ul>";

            unset($_SESSION["errors_signup"]);
        } 
    }

    function check_login_errors() {
        if (isset($_SESSION["errors_login"])) {
            $errs = $_SESSION["errors_login"];

            echo "<br>";
            echo "<ul style='list-style-type: none;'>";

            foreach ($errs as $an_err) {
                if (is_array($an_err)) {
                    foreach ($an_err as $arr_err) {
                        if (!empty($an_err)) {
                            echo '<li class="form-error">' . $arr_err . '</li><br>';
                        }
                    }
                } else {
                    echo '<li class="form-error">' . $an_err . '</li><br>';
                }
            }

            echo "</ul>";

            unset($_SESSION["errors_login"]);
        } 
    }

    function success_message() {
        if (isset($_SESSION["upvid_success"])) {
            $suc = $_SESSION["upvid_success"];

            echo "<br>";
            echo "<ul style='list-style-type: none;'>";

            foreach ($suc as $a_suc) {
                    echo '<li class="form-error" style="color: green;">' . $a_suc . '</li><br>';
            }

            echo "</ul>";

            unset($_SESSION["upvid_success"]);
        } 
    }

?>
