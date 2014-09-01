<?php 
include_once 'bh-config.php';
include_once 'Member.php';

function prepareStatement($db, $stmt) {
        if ($statement = $db->prepare($stmt)) {
            return $statement;
        } else {
            error_log('Error preparing statement: '.$stmt);
            return false;
        }
    }
    
function login($email, $password, $db) {
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $db->prepare("SELECT id, password, salt 
        FROM members
       WHERE email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($member_id, $db_password, $salt);
        $stmt->fetch();

        if ($stmt->num_rows == 1) {
            // hash the password with the unique salt.
            $password = hash('sha512', $password . $salt);
            // If the user exists we check if the account is locked
            // from too many login attempts 
            if (checkbrute($member_id, $db) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked
                error_log('too many attempts');
                return false;
            } else {
                // Check if the password in the database matches
                // the password the user submitted.
                if ($db_password == $password) {
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $member_id = preg_replace("/[^0-9]+/", "", $member_id);

                    $_SESSION['member_id'] = $member_id;
                    $member = getMemberDetails($db, $member_id);
                    $_SESSION['member'] = $member;
                    error_log('member is '.print_r($member, true));
                    $_SESSION['login_verification_string'] = hash('sha512', $password . $user_browser);
                    return true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    error_log('Invalid password provided for '.$member_id);
                    $db->query("INSERT INTO login_attempts(member_id) VALUES ('$member_id')");
                    return false;
                }
            }
        } else {
            // No user exists.
            error_log('no user exists with email '.$email);
            return false;
        }
    }
}

function checkbrute($member_id, $db) {
    // Get timestamp of current time 
    $now = time();

    // All login attempts are counted from the past 2 hours. 
    $valid_attempts_timeframe = $now - (2 * 60 * 60);

    if ($stmt = $db->prepare("SELECT time 
                                  FROM login_attempts 
                                  WHERE member_id = ? AND time > '$valid_attempts_timeframe'")) {
        $stmt->bind_param('i', $member_id);

        // Execute the prepared query. 
        $stmt->execute();
        $stmt->store_result();

        // If there have been more than 5 failed logins 
        return ($stmt->num_rows > 5);
    } else {
        // Could not create a prepared statement
        header("Location: ../error.php?err=Database error: cannot prepare statement");
        exit();
    }
}

function login_check($db) {
    // Check if all session variables are set 
    if (isset($_SESSION['member_id'], $_SESSION['login_verification_string'])) {
        $member_id = $_SESSION['member_id'];
        $login_string = $_SESSION['login_verification_string'];
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        if ($stmt = $db->prepare("SELECT password 
                      FROM members 
                      WHERE id = ? LIMIT 1")) {
            // Bind "$member_id" to parameter. 
            $stmt->bind_param('i', $member_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
                return ($login_check == $login_string);
            } else {
                // Not logged in 
                return false;
            }
        } else {
            // Could not prepare statement
            header("Location: ../error.php?err=Database error: cannot prepare statement");
            exit();
        }
    } else {
        // Not logged in 
        return false;
    }
}

function esc_url($url) {
    if ('' == $url) {
        return $url;
    }
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;

    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
    
    $url = str_replace(';//', '://', $url);
    $url = htmlentities($url);
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}