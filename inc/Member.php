<?php 
	include_once 'functions.php';

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
			$stmt->bind_param('dssssd', $member_id, $addr, $apt_num, $city, $state, $zip);
			if (!($stmt->execute())) {
				error_log('Error inserting member_address '.$db->error);
				return false;;
			}
		} else {
			error_log('Couldnt query');
		}
	}

	function insertMember($db, $email, $password, $salt) {
		error_log('in insert');
		if ($insert_stmt = prepareStatement($db, "INSERT INTO members (email, password, salt) VALUES (?, ?, ?)")) {
            $insert_stmt->bind_param('sss', $email, $password, $salt);	
            if ($insert_stmt->execute()) {
            	return getMemberId($db, $email);
            }
        }
        error_log('Error inserting member');
        return false;
	}

	function storeMember($db, $email, $password, $salt, $first_name, $last_name, $phone) {
		error_log('in with '.$email);
        if(!($member_id = getMemberId($db, $email))) {
        	if($result = insertMember($db, $email, $password, $salt)) {
        		return $result;
        	} else {
        		return false;
        	}
        } else {
        	return "Error: Member already exists for email address: ".$email;
        }
    }

?>