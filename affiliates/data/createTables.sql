use affiliates;

CREATE TABLE affiliate (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(100) NOT NULL,
    address VARCHAR(100) NOT NULL,
    employee_count INT NOT NULL
);

CREATE TABLE employee (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    affiliate_id INT UNSIGNED NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    phone_number VARCHAR(100),
    email VARCHAR(100) NOT NULL ,
    job_title VARCHAR(100) NOT NULL,
    gender TINYINT NOT NULL,
    birth_date DATE NOT NULL ,
    hire_date DATE NOT NULL,
    administrator_comment VARCHAR(1500),
    avatar VARCHAR(1000),
    FOREIGN KEY (affiliate_id) REFERENCES affiliate(id)
);

SHOW DATABASES;
SHOW TABLES FROM affiliates;
SELECT * FROM affiliate;
SELECT * FROM employee;
DROP TABLE affiliate;
DROP TABLE employee;

