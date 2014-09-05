<?php 
	function storeOrder($db, $member_id, $product_id, $trans, $coupon_id, $price) {
		if(isset($trans['transId'])) {
			$transId = $trans['transId'];
		} else {
			$transId = 'GILT TRANS';
		}
		if($stmt = prepareStatement($db, "INSERT INTO orders (member_id, product_id, auth_transaction_id, coupon_id, billed_price, submitted) VALUES(?, ?, ?, ? ,?, CURDATE())")) {
			$stmt->bind_param('iiiid', $member_id, $product_id, $transId, $coupon_id, $price);
			if (!($stmt->execute())) {
				error_log('Error inserting order '.$db->error);
				return false;
			} 
			return $db->insert_id;
		} else {
			error_log('Couldnt insert order');
			return false;
		}
	}
?>