CREATE TABLE coupon_valid_product (
	coupon_id INT NOT NULL,
	product_id INT NOT NULL,
	FOREIGN KEY (product_id) REFERENCES products(product_id),
	FOREIGN KEY (coupon_id) REFERENCES coupons(id)
)ENGINE=InnoDB;

INSERT INTO coupon_valid_product(coupon_id, product_id)
SELECT c.id, p.product_id
FROM coupons c , products p 
WHERE c.product_id IS NULL
ORDER BY c.id, product_id;

INSERT INTO coupon_valid_product(coupon_id, product_id)
SELECT c.id, c.product_id
FROM coupons c 
WHERE c.product_id IS NOT NULL
ORDER BY c.id;

INSERT INTO coupon_valid_product(coupon_id, product_id)
SELECT c.id, 2
FROM coupons c 
WHERE c.product_id = 1
ORDER BY c.id;

ALTER TABLE coupons DROP FOREIGN KEY coupons_ibfk_1;
ALTER TABLE coupons DROP COLUMN product_id;