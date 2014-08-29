<?php
	include_once 'inc/Member.php';
	include_once 'inc/Session.php';
	include_once 'inc/dbConnect.php';
	SecureSession::create();

	if(isset($_POST['function']) && !empty($_POST['function'])) {
		switch ($_POST['function']) {
			case 'storeMember':
				$first_name = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
				$last_name = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
				$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
				$email = filter_var(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING), FILTER_VALIDATE_EMAIL);
				$pass = filter_input(INPUT_POST, 'cryptPass', FILTER_SANITIZE_STRING);
				$salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
        		$password = hash('sha512', $pass . $salt);
				echo storeMember($db, $email, $password, $salt, $first_name, $last_name, $phone);;
				break;
			default:
				# code...
				break;
		}
	}
?>