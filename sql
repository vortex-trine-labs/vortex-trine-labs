CREATE TABLE service_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    number VARCHAR(50),
    organization VARCHAR(255),
    need TEXT,
    description TEXT,
    referral VARCHAR(255),
    deadline DATE,
    budget DECIMAL(10, 2),
    app_ref VARCHAR(20) UNIQUE,
    status VARCHAR(50) DEFAULT 'Waiting for response',
    status_description TEXT DEFAULT 'Your request has been submitted, waiting for reply from Vortex Trine Lab Team',
    expected_delivery DATE DEFAULT NULL,
    final_price DECIMAL(10, 2) DEFAULT NULL,
    payment_status VARCHAR(50) DEFAULT 'Pending',
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status_number INT DEFAULT 0
);

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,     
    username VARCHAR(50) NOT NULL UNIQUE,   
    password VARCHAR(255) NOT NULL,         
    email VARCHAR(100) NOT NULL UNIQUE,     
    full_name VARCHAR(100) NOT NULL,       
    role VARCHAR(50) DEFAULT 'superadmin', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
