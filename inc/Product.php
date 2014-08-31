<?php
	function getIdForProduct($db, $product) {
		if($stmt = prepareStatement($db, "SELECT product_id FROM products WHERE product_name = ?")) {
			$stmt->bind_param('s', $product);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows == 1) {
				$stmt->bind_result($product_id);
				$stmt->fetch();
				$stmt->close();
				return $product_id;
			}
		}
		return false;
	}
?>