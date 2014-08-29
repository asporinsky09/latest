CREATE TABLE coupons(
	id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	coupon_code VARCHAR(15) NOT NULL,
	value INT NOT NULL,
	discount_type VARCHAR(15) NOT NULL,
	begin_date DATE NOT NULL,
	end_date DATE DEFAULT NULL,
	max_uses INT DEFAULT 0,
	max_uses_per_user INT DEFAULT 1
)ENGINE=InnoDB;

CREATE TABLE coupon_use (
	member_id INT NOT NULL,
	coupon_id INT NOT NULL,
	use_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (member_id) REFERENCES members(id),
	FOREIGN KEY (coupon_id) REFERENCES coupons(id)
)ENGINE=InnoDB;