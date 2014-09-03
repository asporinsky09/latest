CREATE TABLE orders (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	member_id INT NOT NULL,
	product_id INT NOT NULL,
	auth_transaction_id VARCHAR(100) NOT NULL,
	coupon_id INT, 
	billed_price DECIMAL(7, 2),
	submitted DATE NOT NULL,
	FOREIGN KEY (member_id) REFERENCES members(id),
	FOREIGN KEY (product_id) REFERENCES products(product_id),
	FOREIGN KEY (coupon_id) REFERENCES coupons(id)
)ENGINE=InnoDB;

CREATE TABLE appointments (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	member_id INT NOT NULL,
	product_id INT NOT NULL,
	address_id INT NOT NULL,
	scheduled_date DATE NOT NULL,
	scheduled_time VARCHAR(10) NOT NULL,
	FOREIGN KEY (member_id) REFERENCES members(id),
	FOREIGN KEY (product_id) REFERENCES products(product_id),
	FOREIGN KEY (address_id) REFERENCES member_address(address_id)
)ENGINE=InnoDB;

CREATE TABLE appointment_for_order (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	appointment_id INT NOT NULL,
	order_id INT NOT NULL,
	FOREIGN KEY (appointment_id) REFERENCES appointments(id),
	FOREIGN KEY (order_id) REFERENCES orders(id)
)ENGINE=InnoDB;