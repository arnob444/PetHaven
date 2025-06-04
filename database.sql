CREATE DATABASE pethaven;
USE pethaven;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CREATE TABLE pets (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     user_id INT,
--     name VARCHAR(50) NOT NULL,
--     breed VARCHAR(50),
--     age INT,
--     category ENUM('dog', 'cat', 'other') NOT NULL,
--     photo VARCHAR(255),
--     location VARCHAR(100),
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (user_id) REFERENCES users(id)
-- );

CREATE TABLE adoption_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT,
    user_id INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pet_id) REFERENCES pets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT,
    vaccine_name VARCHAR(100),
    vaccine_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pet_id) REFERENCES pets(id)
);

CREATE TABLE pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    breed VARCHAR(100),
    age INT,
    category VARCHAR(50),
    photo VARCHAR(255),
    location VARCHAR(100),
    listing_type ENUM('adoption', 'buy_sell') DEFAULT 'adoption', -- New column
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    price DECIMAL(10, 2) DEFAULT NULL, -- New column for buy/sell price
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);