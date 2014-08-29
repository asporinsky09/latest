CREATE TABLE members (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	email VARCHAR(70) NOT NULL,
	password CHAR(128) NOT NULL,
	salt CHAR(128) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE login_attempts (
	member_id INT NOT NULL,
	time VARCHAR(30) NOT NULL,
	FOREIGN KEY (member_id) REFERENCES members(id)
) ENGINE=InnoDB;

CREATE TABLE member_info (
	member_id INT NOT NULL,
	first_name VARCHAR(30) NOT NULL,
	last_name VARCHAR(30) NOT NULL,
	phone VARCHAR(20) NOT NULL,
	FOREIGN KEY (member_id) REFERENCES members(id)
) ENGINE=InnoDB;

CREATE TABLE member_address (
	member_id INT NOT NULL,
	street_address VARCHAR(100) NOT NULL,
	apt_num VARCHAR(10),
	city VARCHAR(100) NOT NULL,
	state VARCHAR(2) NOT NULL,
	zip VARCHAR(10) NOT NULL,
	is_default BOOL NOT NULL DEFAULT 1,
	FOREIGN KEY (member_id) REFERENCES members(id)
) ENGINE=InnoDB;