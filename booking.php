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
			case 'applyCoupon':
				$member_id = $_SESSION['member_id'];
				error_log('Recieved applyCoupon request for member_id: '.$member_id);
				$couponCode = filter_input(INPUT_POST, 'coupon', FILTER_SANITIZE_STRING);
				echo applyCoupon($db, $couponCode, 100, $member_id);
				break;
			case 'addAddress':
				$member_id = $_SESSION['member_id'];
				error_log('Recieved addAddress request for member_id: '.$member_id);
				$address_id = filter_input(INPUT_POST, 'savedAddress', FILTER_SANITIZE_STRING);
				if(!empty($address_id)) {
					error_log('We have an existing address_id of '.$address_id);
				} else {
					$instruction = filter_input(INPUT_POST, 'instruction', FILTER_SANITIZE_STRING);
					$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
					$aptnum = filter_input(INPUT_POST, 'aptnum', FILTER_SANITIZE_STRING);
					$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
					$state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
					$zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
					$instruction = filter_input(INPUT_POST, 'instruction', FILTER_SANITIZE_STRING);
					echo addAddress($db, $member_id, $address, $aptnum, $city, $state, $zip, $instruction);
				}
				break;
			default:
				break;
		}
	}

	function addAddress($db, $member_id, $address, $aptnum, $city, $state, $zip, $instruction) {
		echo storeAddress($db, $member_id, $address, $aptnum, $city, $state, $zip, $instruction);
	}

	function applyCoupon($db, $couponCode, $originalPrice, $member_id) {
		// TODO: Probably need to uppercase the code?
		if($stmt = prepareStatement($db, "SELECT id, value, discount_type, max_uses, max_uses_per_user FROM coupons WHERE coupon_code = ? AND begin_date <= CURDATE() AND end_date >= CURDATE()")) {
			$stmt->bind_param('s', $couponCode);
			$stmt->execute();
			$stmt->store_result();
			$num_coupons = $stmt->num_rows;
 	
			if($num_coupons == 1) {
				$stmt->bind_result($coupon_id, $value, $discount_type, $max_uses, $max_per_user);
				$stmt->fetch();

				$result;
				if($result = checkForOverMaxUses($db, $max_uses, $coupon_id, $couponCode) != null) {
					return $result;
				}
				if($result = checkOverMemberMaxUses($db, $max_per_user, $coupon_id, $couponCode, $member_id) != null) {
					return $result;
				}
				//TODO: Worry about coupon being applied more than once, handle that
				//Good to go
				return applyValidCoupon($couponCode, $coupon_id, $member_id, $originalPrice, $value, $discount_type);
			} else if($num_coupons < 1) {
				return "Sorry ".$couponCode." is not valid or expired";
			} else {
				error_log('More than one coupon returned for '.$couponCode);
				return false;
			}
		}
	}

	function applyValidCoupon($couponCode, $coupon_id, $member_id, $originalPrice, $value, $discount_type) {
		$resultPrice = $originalPrice;
		switch ($discount_type) {
			case 'DOLLAR':
				$resultPrice -= $value; 
			# Keep in mind negatives and things.  If big 100 dollar off and only 50 dollar cut, ask twice? update coupon amount for user?
				break;
			case 'PERCENT':
				$resultPrice -= (($value/100) * $originalPrice);
				break;
			default:
				error_log($member_id.'triggered incorrect coupon type, either db has typo, or something is wrong: '.$discount_type.', coupon_id: '.$coupon_id.', coupon code: '.$couponCode);
				return "Sorry that coupon is invalid";
		}

		//TODO: Style as a li and do something nice
		return "You have applied coupon code for ".$value." ".$discount_type." discount, your new total is ".$resultPrice;
	}

	function checkForOverMaxUses($db, $max_uses, $coupon_id, $couponCode) {
		$result = null;
		if($max_uses > 0) {
			if ($stmt = prepareStatement($db, "SELECT COUNT(*) FROM coupon_uses WHERE coupon_id = ?")) {
				$stmt->bind_param('s', $coupon_id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($uses);
				$stmt->fetch();
				if($uses >= $max_uses) {
					$result = "Sorry coupon: ".$couponCode." is expired ans no longer valid";
				}
			}
		}
		return $result;
	}

	function checkOverMemberMaxUses($db, $max_per_user, $coupon_id, $couponCode, $member_id) {
		$result = null;
		if($max_per_user > 0) {
			if ($stmt = prepareStatement($db, "SELECT COUNT(*) FROM coupon_uses WHERE coupon_id = ? AND member_id = ?")) {
				$stmt->bind_param('ss', $coupon_id, $member_id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($uses);
				$stmt->fetch();
				if($uses >= $max_uses) {
					$result = "Sorry coupon: ".$couponCode." can only be used ".$max_per_user." time".($uses==1 ? "" : "s")."per member";
				}
			}
		}
		return $result;
	}
?>