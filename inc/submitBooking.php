<?php 
	function validateInput() {
		$valid = (isset($_POST['email']) &&
		isset($_POST['cryptPass']) &&
		isset($_POST['streetAddress']) &&
		isset($_POST['city']) &&
		isset($_POST['state']) &&
		isset($_POST['zip']) &&
		isset($_POST['fname']) &&
		isset($_POST['lname']) &&
		isset($_POST['phone']) &&
		strlen($_POST['cryptPass'])==128);
		if (!$valid) {
			error_log('INVALID post data:'.strlen($_POST['cryptPass']));
		} 
		return $valid;
	}

	function prepareStatement($db, $stmt) {
		if ($statement = $db->prepare($stmt)) {
			return $statement;
		} else {
			error_log('Error preparing statement: '.$statement);
			return false;
		}
	}

	function generateSecurePass($pass) {
		$salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
        return hash('sha512', $pass . $salt);
	}

    function therest() {}
        if ($insert_stmt = prepareStatement($db, "INSERT INTO members (email, password, salt) VALUES (?, ?, ?)")) {
            $insert_stmt->bind_param('sss', $email, $password, $salt);
            if ($insert_stmt->execute()) {
            	if($stmt = prepareStatement($db, "SELECT id FROM members WHERE email=? LIMIT 1")) {
					$stmt->bind_param('s', $email);
					$stmt->execute();
					$stmt->store_result();
					if($stmt->num_rows == 1) {
						$stmt->bind_result($member_id);
						$stmt->fetch(); 
						if($stmt = prepareStatement($db, "INSERT INTO member_info(member_id, first_name, last_name, phone) VALUES(?, ?, ?, ?)")) {
							$stmt->bind_param('dsss', $member_id, $first_name, $last_name, $phone);
							if (!($stmt->execute())) {
								error_log('Error inserting member_info');
								return false;
							}
							return member_id;
						}
					}
				} else {
					error_log('Error executing statement '.$stmt);
					return false;
				}   
            } else {
            	error_log('Error executing statement '.$insert_stmt);
            	return false;
            }
        } else {
        	error_log('Error preparing statement');
        	return false;
        }
        return true;
	}

	function doBook() {
		include_once 'dbConnect.php';
		include_once 'Session.php';
		SecureSession::create();
		// $logged_in = login_check($db);
		if (validateInput()) {
			$first_name = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
			$last_name = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
			$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
			$addr = filter_input(INPUT_POST, 'streetAddress', FILTER_SANITIZE_STRING);
			$apt_num = filter_input(INPUT_POST, 'aptnum', FILTER_SANITIZE_STRING);
			$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
			$state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
			$zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
			$email = filter_var(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING), FILTER_VALIDATE_EMAIL);
			$pass = filter_input(INPUT_POST, 'cryptPass', FILTER_SANITIZE_STRING);

			if(storeMember($db, $email, generateSecurePass($pass), $first_name, $last_name, $phone, $addr, $apt_num, $city, $state, $zip)) {
				echo "Great Success";
				// Do booking I guess?
			}
		} 
	}

	

	doBook();
?>