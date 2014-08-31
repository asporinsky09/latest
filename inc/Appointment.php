<?php 
	function storeAppointment($db, $member_id, $product_id, $address_id, $date, $time, $order_id) {
		if($stmt = prepareStatement($db, "INSERT INTO appointments (member_id, product_id, address_id, scheduled_date, scheduled_time) VALUES(?, ?, ?, ? ,?)")) {
			$stmt->bind_param('iiiss', $member_id, $product_id, $address_id, $date, $time);
			if (!($stmt->execute())) {
				error_log('Error inserting appointment '.$db->error);
				return false;
			} 
			$appointment_id = $db->insert_id;
			if($stmt = prepareStatement($db, "INSERT INTO appointment_for_order (appointment_id, order_id) VALUES(?, ?)")) {
				$stmt->bind_param('ii', $appointment_id, $order_id);
				if (!($stmt->execute())) {
					error_log('Error inserting appointment to order relationship '.$db->error);
					return false;
				} 
			} else {
				error_log('Couldnt inset order assiciation');
			}
		} else {
			error_log('Couldnt insert order');
			return false;
		}
	}
?>