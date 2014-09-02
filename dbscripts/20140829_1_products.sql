CREATE TABLE products (
	product_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	product_name VARCHAR(25),
	price DECIMAL(7, 2),
	usages INT DEFAULT 1
)ENGINE=InnoDB;

INSERT INTO products(product_name, price, usages) 
VALUES('Blowout', 50.00, 1), 
('Braid', 50.00, 1), 
('Up-Do', 85.00, 1),
('4 Blowouts', 190.00, 4),
('8 Blowouts', 360.00), 8,
('12 Blowouts', 520.00, 12),
('16 Blowouts', 700.00, 16);