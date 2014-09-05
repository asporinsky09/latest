<?php 
	include_once 'functions.php';

	function getMemberDetails($db, $id) {
		if($stmt = prepareStatement($db, "SELECT i.first_name, i.last_name, m.email, i.phone FROM members m INNER JOIN member_info i ON m.id = i.member_id WHERE m.id=? LIMIT 1")) {
			$stmt->bind_param('s', $id);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows == 1) {
				$stmt->bind_result($firstName, $lastName, $email, $phone);
				$stmt->fetch(); 
				return array('fname' => $firstName, 'lname' => $lastName, 'email' => $email, 'phone' => format_telephone($phone));
			}
		}
		return false;	
	}

	function format_telephone($phone)
	{
	    $cleaned = preg_replace('/[^[:digit:]]/', '', $phone);
	    preg_match('/(\d{3})(\d{3})(\d{4})/', $cleaned, $matches);
	    return "({$matches[1]}) {$matches[2]}-{$matches[3]}";
	}

	function getMemberId($db, $email) {
		if($stmt = prepareStatement($db, "SELECT id FROM members WHERE email=? LIMIT 1")) {
			$stmt->bind_param('s', $email);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows == 1) {
				$stmt->bind_result($member_id);
				$stmt->fetch(); 
				return $member_id;
			}
		}
		return false;
	}

	function getMemberAddress($db, $member_id, $address_id) {
		if($stmt = prepareStatement($db, "SELECT street_address, apt_num, city, state, zip 
			FROM member_address WHERE member_id=? AND address_id = ? ORDER BY is_default DESC")) {
			$stmt->bind_param('ii', $member_id, $address_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($addr, $aptnum, $city, $state, $zip);
			if($stmt->num_rows < 1) {
				return false;
			}
			$result = '';
			$stmt->fetch();
			return array('address' => $addr, 'aptnum' => $aptnum, 'city' => $city, 'state' => $state, 'zip' => $zip);
		}
	}

	function getMemberAddressesAsOptions($db, $member_id) {
		if($stmt = prepareStatement($db, "SELECT address_id, street_address, apt_num, city, state, zip, is_default 
			FROM member_address WHERE member_id=? ORDER BY is_default DESC")) {
			$stmt->bind_param('s', $member_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($address_id, $addr, $aptnum, $city, $state, $zip, $is_default);
			if($stmt->num_rows < 1) {
				return false;
			}
			$result = '';
			$stmt->fetch();
			$result.=toSelectOption($address_id, $addr, $city, $state, true);
			while ($row = $stmt->fetch()) {
				$result.=toSelectOption($address_id, $addr, $city, $state);
			}
			return $result;
		}
	}

	function toSelectOption($address_id, $addr, $city, $state, $selected = false) {
		return "<option value=\"".$address_id."\"".($selected ? " selected" : "").">".$addr." ".$city.", ".$state."</option>\n";
	}

	//TODO: Add nickname
	function storeAddress($db, $member_id, $addr, $apt_num, $city, $state, $zip, $instruction) {
		if($stmt = prepareStatement($db, "INSERT INTO member_address (member_id, street_address, apt_num, city, state, zip) VALUES(?, ?, ?, ? ,? ,?)")) {
			$stmt->bind_param('issssi', $member_id, $addr, $apt_num, $city, $state, $zip);
			if (!($stmt->execute())) {
				error_log('Error inserting member_address '.$db->error);
				return false;
			} 
			return $db->insert_id;
		} else {
			error_log('Couldnt query');
		}
	}

	function insertMember($db, $email, $password, $salt) {
		if ($insert_stmt = prepareStatement($db, "INSERT INTO members (email, password, salt) VALUES (?, ?, ?)")) {
            $insert_stmt->bind_param('sss', $email, $password, $salt);	
            if ($insert_stmt->execute()) {
            	return $db->insert_id;
            }
        }
        error_log('Error inserting member');
        return false;
	}

	function storeMember($db, $email, $password, $salt, $first_name, $last_name, $phone) {
        if(!($member_id = getMemberId($db, $email))) {
        	if($result = insertMember($db, $email, $password, $salt)) {
        		storeMemberInfo($db, $result, $first_name, $last_name, $phone);
        		return $result;
        	} else {
        		return false;
        	}
        } else {
        	return "Error: Member already exists for email address: ".$email;
        }
    }

    function storeMemberInfo($db, $member_id, $first_name, $last_name, $phone) {
    	if ($insert_stmt = prepareStatement($db, "INSERT INTO member_info (member_id, first_name, last_name, phone) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('isss', $member_id, $first_name, $last_name, $phone);	
            if ($insert_stmt->execute()) {
            	return true;
            }
        }
        error_log('Error inserting member info');
        return false;
    }
?>