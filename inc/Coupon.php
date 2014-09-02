<?php 
	function storeCouponUse($db, $member_id, $coupon_id) {
		if($coupon_id) {
			if($stmt = prepareStatement($db, "INSERT INTO coupon_use(member_id, coupon_id) VALUES(?, ?)")) {
				$stmt->bind_param('ii', $member_id, $coupon_id);
				if (!($stmt->execute())) {
					error_log('Error inserting coupon use '.$db->error);
					return false;
				} 
				return $db->insert_id;
			} else {
				error_log('Couldnt log coupon use');
				return false;
			}
		}
	}
?>