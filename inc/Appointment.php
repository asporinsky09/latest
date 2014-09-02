<?php 
	function storeAppointment($db, $member_id, $product_id, $address_id, $date, $time, $order_id) {
		if($stmt = prepareStatement($db, "INSERT INTO appointments (member_id, product_id, address_id, scheduled_date, scheduled_time) VALUES(?, ?, ?, ? ,?)")) {
			$stmt->bind_param('iiiss', $member_id, $product_id, $address_id, $date, $time);
			if (!($stmt->execute())) {
				error_log('Error inserting appointment '.$db->error);
				return false;
			} 
			if($db->affected_rows) {
				$appointment_id = $db->insert_id;
				if($stmt = prepareStatement($db, "INSERT INTO appointment_for_order (appointment_id, order_id) VALUES(?, ?)")) {
					$stmt->bind_param('ii', $appointment_id, $order_id);
					if (!($stmt->execute())) {
						error_log('Error inserting appointment to order relationship '.$db->error);
						return false;
					} else {
						return $db->affected_rows;
					}
				} else {
					error_log('Couldnt insert order assiciation due to '.$db->error);
					return false;
				}
			} else {
				error_log('Couldn\'t add appointment due to '.$db->error);
			}
		} else {
			error_log('Couldnt insert order');
			return false;
		}
	}

	function rescheduleAppointment($db, $member_id, $appointment_id, $address_id, $date, $time) {
		if($stmt = prepareStatement($db, "UPDATE appointments SET address_id=?, scheduled_date=?, scheduled_time=? WHERE id=? AND member_id=?")) {
			$stmt->bind_param('issii', $address_id, $date, $time, $appointment_id, $member_id);
			if ($stmt->execute()) {
				if($db->affected_rows) {
					return true;
				} else {
					return "No Change";
				}
			} else {
				error_log('Could not update appointment ' . $appointment_id .' for member_id ' . $member_id . ' due to '. $db->error);
			}
		}
		return false;
	}

	function scheduleAppointment($db, $member_id, $order_id, $product_id, $address_id, $date, $time) {
		return storeAppointment($db, $member_id, $product_id, $address_id, $date, $time, $order_id);
	}

	function cancelAppointment($db, $member_id, $appointment_id) {
		if($stmt = prepareStatement($db, "DELETE FROM appointment_for_order WHERE appointment_id=?")) {
			$stmt->bind_param('i', $appointment_id);
			if ($stmt->execute()) {
				if($db->affected_rows) {
					if($stmt = prepareStatement($db, "DELETE FROM appointments WHERE id=? AND member_id=?")) {
						$stmt->bind_param('ii', $appointment_id, $member_id);
						if ($stmt->execute()) {
							if($db->affected_rows) {
								error_log('delete of actual: '.$db->affected_rows);
								return true;
							} 
						} else {
							error_log('Could not delete appointment ' . $appointment_id .' for member_id ' . $member_id . ' due to '. $db->error);
						}
					}
				} 
			} else {
				error_log('Could not delete appointment ' . $appointment_id .' for member_id ' . $member_id . ' due to '. $db->error);
			}
		}
		return 0;
	}

	function getAppointmentsForMember($db, $member_id) {
		$result = array();
		if($stmt = prepareStatement($db, "SELECT a.id, p.product_name, ma.street_address, ma.apt_num, ma.city, ma.state, ma.zip, a.scheduled_date, a.scheduled_time"
			." FROM appointments a INNER JOIN products p ON a.product_id = p.product_id"
			." INNER JOIN member_address ma ON ma.address_id = a.address_id"
			." WHERE a.member_id=?"
			." AND a.scheduled_date >= CURDATE()"
			." ORDER BY a.scheduled_date ASC")) {
			$stmt->bind_param('s', $member_id);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows > 0) {
				$stmt->bind_result($appt_id, $product, $address, $aptNum, $city, $state, $zip, $date, $time);
				$i = 0;
				while ($row = $stmt->fetch()) {
					$appointment = array('appointment_id' => $appt_id, 'product' => $product, 'address' => $address, 'aptnum' => $aptNum, 'city' => $city,
					 'state' => $state, 'zip' => $zip, 'date' => $date, 'time' => $time);
					$result[$i] = $appointment;
					$i++;
				}
			}
		}
		return $result;	
	}

	function getUnscheduledForMember($db, $member_id) {
		$result = array();
		if($stmt = prepareStatement($db, "SELECT p.product_name, p.product_id, p.usages AS max_uses, COUNT(afo.id) AS used, o.id, o.submitted"
			." FROM orders o"
			." INNER JOIN appointment_for_order afo ON afo.order_id = o.id"
			." INNER JOIN products p ON p.product_id = o.product_id"
			." WHERE o.member_id = ?"
			." GROUP BY o.id"
			." HAVING used < max_uses")) {
			$stmt->bind_param('s', $member_id);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows > 0) {
				$stmt->bind_result($product_name, $product_id, $max_uses, $used, $order_id, $submitted);
				$i = 0;
				while ($row = $stmt->fetch()) {
					$unscheduled = array('product_name' => $product_name, 'product_id' => $product_id, 'max_uses' => $max_uses, 'used' => $used, 'order_id' => $order_id, 'submitted' => $submitted);
					$result[$i] = $unscheduled;
					$i++;
				}
			}
		}
		return $result;	
	}

	function formatAppointment($appointment) {
		$apt = ($appointment['aptnum'] ? 'Apt: '.$appointment['aptnum'] : '');
		$time = $appointment['time'];
		$time = ($time[0] == '0') ? substr($time, 1) : $time;
		return '<li class="appointment-entry">'.
			'<input type="hidden" value="'.$appointment['appointment_id'].'">'.
			'<h3>'.$appointment['date'].'</h3>'.
			'<span>'.$appointment['product'].' at '.$time.'</span>'.
			'<span>'.$appointment['address'].' '.$apt.'</span>'.
			'<span>'.$appointment['city'].', '.$appointment['state'].' '.$appointment['zip'].'</span>'.
		'</li>';
	}
?>