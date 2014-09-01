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

	function getAppointmentsForMember($db, $member_id) {
		$result = array();
		if($stmt = prepareStatement($db, "SELECT p.product_name, ma.street_address, ma.apt_num, ma.city, ma.state, ma.zip, a.scheduled_date, a.scheduled_time"
			." FROM appointments a INNER JOIN products p ON a.product_id = p.product_id"
			." INNER JOIN member_address ma ON ma.address_id = a.address_id"
			." WHERE a.member_id=?"
			." AND a.scheduled_date >= CURDATE()")) {
			$stmt->bind_param('s', $member_id);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows > 0) {
				$stmt->bind_result($product, $address, $aptNum, $city, $state, $zip, $date, $time);
				$i = 0;
				while ($row = $stmt->fetch()) {
					$appointment = array('product' => $product, 'address' => $address, 'aptnum' => $aptNum, 'city' => $city,
					 'state' => $state, 'zip' => $zip, 'date' => $date, 'time' => $time);
					$result[$i] = $appointment;
					$i++;
				}
			}
		}
		return $result;	
	}

	function formatAppointment($appointment) {
		$apt = ($appointment['aptnum'] ? 'Apt: '.$appointment['aptnum'] : '');
		return '<li class="appointment-entry">'.
			'<h3>'.$appointment['date'].'</h3>'.
			'<span>'.$appointment['product'].' at '.$appointment['time'].'</span>'.
			'<span>'.$appointment['address'].' '.$apt.'</span>'.
			'<span>'.$appointment['city'].', '.$appointment['state'].' '.$appointment['zip'].'</span>'.
		'</li>';
	}
?>