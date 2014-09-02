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

	function getProducts($db) {
		$result = array();
		if($stmt = prepareStatement($db, "SELECT product_id, product_name, price FROM products WHERE usages=1")) {
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows > 0) {
				$stmt->bind_result($product_id, $product_name, $price);
				$i = 0;
				while ($row = $stmt->fetch()) {
					$product = array('product_id' => $product_id, 'product' => $product_name, 'price' => $price);
					$result[$i] = $product;
					$i++;
				}
			}
		}
		return $result;
	}

	function getPackages($db) {
		$result = array();
		if($stmt = prepareStatement($db, "SELECT product_id, product_name, price, usages FROM products WHERE usages > 1")) {
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows > 0) {
				$stmt->bind_result($product_id, $product_name, $price, $usages);
				$i = 0;
				while ($row = $stmt->fetch()) {
					$product = array('product_id' => $product_id, 'product' => $product_name, 'price' => $price, 'usages' => $usages);
					$result[$i] = $product;
					$i++;
				}
			}
		}
		return $result;
	}
?>