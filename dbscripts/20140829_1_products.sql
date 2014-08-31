CREATE TABLE products (
	product_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	product_name VARCHAR(25),
	price DECIMAL(4, 2)
)ENGINE=InnoDB;

INSERT INTO products(product_name, price) VALUES('Blowout', 50.00), ('Braid', 50.00), ('Up-Do', 85.00);