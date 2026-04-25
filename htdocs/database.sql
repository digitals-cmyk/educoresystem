CREATE TABLE schools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    address VARCHAR(255),
    email VARCHAR(100),
    principal_name VARCHAR(100),
    status ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE DEFAULT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('SUPER_ADMIN', 'SCHOOL_ADMIN', 'TEACHER', 'STUDENT', 'PARENT', 'STAFF') NOT NULL,
    school_id INT NULL,
    status ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_id INT NOT NULL,
    admission_number VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    dob DATE,
    date_of_admission DATE,
    assessment_number VARCHAR(100),
    grade VARCHAR(50),
    stream VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
);

CREATE TABLE exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    term VARCHAR(50) NOT NULL,
    year VARCHAR(4) NOT NULL,
    status ENUM('PUBLISHED', 'UNPUBLISHED') DEFAULT 'UNPUBLISHED',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
);

-- Insert Default Super Admin (Username: adminpro, Password: admin123)
-- Hash for admin123 is $2y$10$Y1/n1m48Z/C7rA8uC/Q/wOHB.fOOSZtZ2a/pIXV0nN3YJcO8B3b3O
INSERT INTO users (name, username, email, password, role, status) 
VALUES ('Super Admin', 'adminpro', 'admin@pro.com', '$2y$10$Y1/n1m48Z/C7rA8uC/Q/wOHB.fOOSZtZ2a/pIXV0nN3YJcO8B3b3O', 'SUPER_ADMIN', 'ACTIVE');

