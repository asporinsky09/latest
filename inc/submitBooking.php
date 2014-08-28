<?php 

include_once 'functions.php';
include_once 'dbConnect.php';
include_once 'Session.php';

	function validateInput() {
		return isset($_POST['email']) &&
		isset($_POST['streetAddress']) &&
		isset($_POST['city']) &&
		isset($_POST['state']) &&
		isset($_POST['zip']) &&
		isset($_POST['fname']) &&
		isset($_POST['lname']) &&
		isset($_POST['phone']);
	}

	SecureSession::create();
	// $logged_in = login_check($db);
	if (validateInput()) {
		$first_name = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
		$last_name = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
		$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
		$arrd = filter_input(INPUT_POST, 'streetAddress', FILTER_SANITIZE_STRING);$
		$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
		$state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
		$zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
		$email = filter_var(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING), FILTER_VALIDATE_EMAIL);

		
	}
?>