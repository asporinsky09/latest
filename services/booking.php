<?php
	include_once '../inc/Member.php';
	include_once '../inc/Session.php';
	include_once '../inc/dbConnect.php';
	include_once '../inc/Authorize.php';
	include_once '../inc/Product.php';
	include_once '../inc/Order.php';
	include_once '../inc/Appointment.php';
	include_once '../inc/Coupon.php';
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
        		$member_id = storeMember($db, $email, $password, $salt, $first_name, $last_name, $phone);
        		$_SESSION['member_id'] = $member_id;
				echo $member_id;
				break;
			case 'applyCoupon':
				$member_id = $_SESSION['member_id'];
				$couponCode = filter_input(INPUT_POST, 'coupon', FILTER_SANITIZE_STRING);
				$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
				echo applyCoupon($db, $couponCode, $price, $member_id);
				break;
			case 'addAddress':
				$member_id = $_SESSION['member_id'];
				error_log('Recieved addAddress request for member_id: '.$member_id);
				$instruction = filter_input(INPUT_POST, 'instruction', FILTER_SANITIZE_STRING);
				$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
				$aptnum = filter_input(INPUT_POST, 'aptnum', FILTER_SANITIZE_STRING);
				$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
				$state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
				$zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
				$instruction = filter_input(INPUT_POST, 'instruction', FILTER_SANITIZE_STRING);
				echo addAddress($db, $member_id, $address, $aptnum, $city, $state, $zip, $instruction);
				break;
			case 'getPrice': //TODO: Move this stuff to a product class or product dao class
				$product = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_STRING);
				echo getPrice($db, $product);
				break;
			case 'doBooking':
				$member_id = $_SESSION['member_id'];
				$address_id = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
				$coupon = filter_input(INPUT_POST, 'coupon_id', FILTER_SANITIZE_STRING);		
				$date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
				$time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING);
				$ccnum = filter_input(INPUT_POST, 'ccnum', FILTER_SANITIZE_STRING);
				$ccexp = filter_input(INPUT_POST, 'ccexp', FILTER_SANITIZE_STRING);
				$cvv = filter_input(INPUT_POST, 'cvv', FILTER_SANITIZE_STRING);
				$product = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_STRING);
				$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
				echo doBooking($db, $member_id, $address_id, $coupon, $product, $price, $date, $time, $ccnum, $ccexp, $cvv);
				break;
			default:
				break;
		}
	}

	function doBooking($db, $member_id, $address_id, $coupon_id, $product, $price, $date, $time, $ccnum, $ccexp, $cvv) {
		//TODO: get member name, get product name
        $member = getMemberDetails($db, $member_id);
        if($member) {
        	$transResult = processTransaction($member['fname'], $member['lname'], $product, $price, $ccnum, $ccexp, $cvv);
        	if($transResult==0 || $transResult) {  //HARDCODE
        		if ($transResult == 'Declined') {
        			error_log('Transaction declined for member_id ' . $member_id);
        			return $transResult;
        		} else {
    				$product_id = getIdForProduct($db, $product);
    				$order_id = storeOrder($db, $member_id, $product_id, $transResult, $coupon_id, $price);
    				if($order_id) {
    					storeAppointment($db, $member_id, $product_id, $address_id, $date, $time, $order_id);
    					storeCouponUse($db, $member_id, $coupon_id);
    					return 'Success';
    				} else {
    					error_log('Could not insert order, cannot book for member_id ' . $member_id);
    				}
        		}
        	} else {
        		error_log('Transaction returned an error cannot book for member_id ' . $member_id);
        	}
        } else {
        	error_log('Member does not exist with id ' . $member_id . ' cannot process transaction');
        }        
	}

	function getPrice($db, $productName) {
		if($stmt = prepareStatement($db, "SELECT product_id, price FROM products WHERE product_name = ?")) {
			$stmt->bind_param('s', $productName);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows == 1) {
				$stmt->bind_result($product_id, $price);
				$stmt->fetch();
				$result['product_id'] = $product_id;
				$result['price'] = $price;
				return json_encode($result);
			}
		}
		return false;
	}

	function addAddress($db, $member_id, $address, $aptnum, $city, $state, $zip, $instruction) {
		$address_result = storeAddress($db, $member_id, $address, $aptnum, $city, $state, $zip, $instruction);
		if ($address_result) {

		} else {
			echo 'Error storing address';
		}
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
				if($result = checkForOverMaxUses($db, $max_uses, $coupon_id, $couponCode)) {
					return json_encode(array('id' => '', 'adjust' => '', 'error' => $result));
				}
				if($result = checkOverMemberMaxUses($db, $max_per_user, $coupon_id, $couponCode, $member_id)) {
					return json_encode(array('id' => '', 'adjust' => '', 'error' => $result));
				}
				//TODO: Worry about coupon being applied more than once, handle that
				//Good to go
				return applyValidCoupon($couponCode, $coupon_id, $member_id, $originalPrice, $value, $discount_type);
			} else if($num_coupons < 1) {
				return json_encode(array('id' => '', 'adjust' => '', 'error' => "Sorry, ".$couponCode." is not valid or expired"));
			} else {
				error_log('More than one coupon returned for '.$couponCode);
				return false;
			}
		}
	}

	function applyValidCoupon($couponCode, $coupon_id, $member_id, $originalPrice, $value, $discount_type) {
		$priceAdjust;
		switch ($discount_type) {
			case 'DOLLAR':
				$priceAdjust = $value; 
			# Keep in mind negatives and things.  If big 100 dollar off and only 50 dollar cut, ask twice? update coupon amount for user?
				break;
			case 'PERCENT':
				$priceAdjust = (($value/100) * $originalPrice);
				break;
			default:
				error_log($member_id.'triggered incorrect coupon type, either db has typo, or something is wrong: '.$discount_type.', coupon_id: '.$coupon_id.', coupon code: '.$couponCode);
				return "Sorry, that coupon is invalid";
		}
		return json_encode(array('id' => $coupon_id, 'adjust' => $priceAdjust, 'error' => '')); 
	}

	function checkForOverMaxUses($db, $max_uses, $coupon_id, $couponCode) {
		$result = null;
		if($max_uses > 0) {
			if ($stmt = prepareStatement($db, "SELECT COUNT(*) FROM coupon_use WHERE coupon_id = ?")) {
				$stmt->bind_param('i', $coupon_id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($uses);
				$stmt->fetch();
				if($uses >= $max_uses) {
					$result = "Sorry, coupon ".$couponCode." is expired and no longer valid";
				}
			}
		}
		return $result;
	}

	function checkOverMemberMaxUses($db, $max_per_user, $coupon_id, $couponCode, $member_id) {
		$result = null;
		if($max_per_user > 0) {
			if ($stmt = prepareStatement($db, "SELECT COUNT(*) FROM coupon_use WHERE coupon_id = ? AND member_id = ?")) {
				$stmt->bind_param('ii', $coupon_id, $member_id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($uses);
				$stmt->fetch();
				if($uses >= $max_per_user) {
					$result = "Sorry, coupon ".$couponCode." can only be used ".$max_per_user." time".($uses==1 ? "" : "s");
				}
			}
		}
		return $result;
	}
?>