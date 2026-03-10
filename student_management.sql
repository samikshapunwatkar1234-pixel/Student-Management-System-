/* =========================
   DATABASE
========================= */
CREATE DATABASE IF NOT EXISTS student_management 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;
USE student_management;


/* =========================
   COURSES TABLE
========================= */
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    duration INT NOT NULL COMMENT 'Duration in months',
    fee DECIMAL(10,2) NOT NULL,
    description TEXT,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_course_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* =========================
   BATCHES TABLE
========================= */
CREATE TABLE IF NOT EXISTS batches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    course_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    capacity INT NOT NULL,
    enrolled INT DEFAULT 0,
    status ENUM('Open', 'Full', 'Closed', 'Cancelled') DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_batch_course
        FOREIGN KEY (course_id)
        REFERENCES courses(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    
    INDEX idx_batch_course (course_id),
    INDEX idx_batch_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* =========================
   ID DOCUMENT TYPES TABLE
========================= */
CREATE TABLE IF NOT EXISTS id_document_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(100) NOT NULL UNIQUE,
    type_code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default ID document types
INSERT INTO id_document_types (type_name, type_code, description) VALUES
('Passport', 'passport', 'International travel document'),
('National ID Card', 'national_id', 'Government issued national identity card'),
('Driver\'s License', 'drivers_license', 'Official driving permit with photo ID'),
('Student ID Card', 'student_id', 'Educational institution student identification'),
('Birth Certificate', 'birth_certificate', 'Official birth registration document'),
('Social Security Card', 'ssn_card', 'Social security number card'),
('Voter ID Card', 'voter_id', 'Electoral commission voter identification'),
('Other', 'other', 'Other valid identification document')
ON DUPLICATE KEY UPDATE type_name = type_name;


/* =========================
   STUDENTS TABLE
========================= */
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    course_id INT NOT NULL,
    batch_id INT NOT NULL,
    address TEXT NOT NULL,
    
    -- Fee fields
    total_fee DECIMAL(10,2) NOT NULL,
    paid_amount DECIMAL(10,2) DEFAULT 0.00,
    balance DECIMAL(10,2) NOT NULL,
    
    -- Photo field (stores file path)
    photo VARCHAR(255) DEFAULT NULL,
    
    -- ID Document fields
    id_document_type_id INT DEFAULT NULL,
    id_document_number VARCHAR(100) DEFAULT NULL,
    id_document_file VARCHAR(255) DEFAULT NULL COMMENT 'File path to stored ID document',
    id_document_verified BOOLEAN DEFAULT FALSE,
    id_document_uploaded_at TIMESTAMP NULL DEFAULT NULL,
    
    admission_date DATETIME NOT NULL,
    status ENUM('Active', 'Inactive', 'Graduated', 'Dropped', 'Suspended') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_student_course
        FOREIGN KEY (course_id)
        REFERENCES courses(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_student_batch
        FOREIGN KEY (batch_id)
        REFERENCES batches(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_student_id_type
        FOREIGN KEY (id_document_type_id)
        REFERENCES id_document_types(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    
    INDEX idx_student_email (email),
    INDEX idx_student_status (status),
    INDEX idx_student_course (course_id),
    INDEX idx_student_batch (batch_id),
    INDEX idx_id_document_number (id_document_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* =========================
   ID DOCUMENT HISTORY TABLE
   (For tracking document changes/updates)
========================= */
CREATE TABLE IF NOT EXISTS id_document_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    document_type_id INT NOT NULL,
    document_number VARCHAR(100) NOT NULL,
    document_file VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    uploaded_by INT DEFAULT NULL COMMENT 'Admin user ID who uploaded',
    reason_for_change TEXT,
    
    CONSTRAINT fk_idhist_student
        FOREIGN KEY (student_id)
        REFERENCES students(id)
        ON DELETE CASCADE,
        
    CONSTRAINT fk_idhist_type
        FOREIGN KEY (document_type_id)
        REFERENCES id_document_types(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* =========================
   ATTENDANCE TABLE
========================= */
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    batch_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Present', 'Absent', 'Late', 'Excused') DEFAULT 'Present',
    remarks VARCHAR(255) DEFAULT NULL,
    marked_by INT DEFAULT NULL COMMENT 'Admin/Instructor ID',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uniq_attendance (student_id, date),

    CONSTRAINT fk_attendance_student
        FOREIGN KEY (student_id) 
        REFERENCES students(id)
        ON DELETE CASCADE,
        
    CONSTRAINT fk_attendance_batch
        FOREIGN KEY (batch_id) 
        REFERENCES batches(id)
        ON DELETE CASCADE,
    
    INDEX idx_attendance_date (date),
    INDEX idx_attendance_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* =========================
   PAYMENTS TABLE
========================= */
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_reference VARCHAR(100) UNIQUE DEFAULT NULL,
    student_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    method ENUM('Cash', 'Card', 'Bank Transfer', 'Online', 'Cheque', 'Other') DEFAULT 'Cash',
    transaction_id VARCHAR(255) DEFAULT NULL,
    notes TEXT,
    recorded_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_payment_student
        FOREIGN KEY (student_id) 
        REFERENCES students(id)
        ON DELETE RESTRICT,
    
    INDEX idx_payment_date (payment_date),
    INDEX idx_payment_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* =========================
   USERS/ADMINS TABLE
   (For login and audit trails)
========================= */
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('Super Admin', 'Admin', 'Accountant', 'Instructor', 'Viewer') DEFAULT 'Viewer',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_email (email),
    INDEX idx_user_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* =========================
   ACTIVITY LOGS TABLE
   (For audit trails)
========================= */
CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_logs_user (user_id),
    INDEX idx_logs_action (action),
    INDEX idx_logs_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* =========================
   INSERT DEFAULT COURSES
========================= */
INSERT INTO courses (name, duration, fee, description) VALUES
('Computer Science', 24, 5000.00, 'Bachelor of Computer Science - Comprehensive program covering programming, algorithms, and software engineering'),
('Business Administration', 18, 4500.00, 'MBA Program - Advanced business management and leadership skills'),
('Data Science', 12, 6000.00, 'Data Science Certification - Machine learning, statistics, and data analytics')
ON DUPLICATE KEY UPDATE 
    duration = VALUES(duration),
    fee = VALUES(fee),
    description = VALUES(description);


/* =========================
   INSERT DEFAULT BATCHES
========================= */
INSERT INTO batches (name, course_id, start_date, end_date, capacity) VALUES
('CS-2024-A', 1, '2024-01-15', '2025-01-15', 30),
('CS-2024-B', 1, '2024-06-01', '2025-06-01', 25),
('MBA-2024-B', 2, '2024-02-01', '2025-08-01', 25),
('DS-2024-A', 3, '2024-03-01', '2025-03-01', 20)
ON DUPLICATE KEY UPDATE 
    start_date = VALUES(start_date),
    end_date = VALUES(end_date),
    capacity = VALUES(capacity);


/* =========================
   INSERT DEFAULT ADMIN USER
   (Password: admin123 - Change immediately after first login)
========================= */
INSERT INTO users (username, email, password_hash, full_name, role) VALUES
('admin', 'admin@edumanager.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'Super Admin')
ON DUPLICATE KEY UPDATE email = email;


/* =========================
   VIEWS FOR EASY REPORTING
========================= */

-- View: Student Details with Course and Batch Info
CREATE OR REPLACE VIEW view_student_details AS
SELECT 
    s.id,
    s.student_id,
    CONCAT(s.first_name, ' ', s.last_name) AS full_name,
    s.email,
    s.phone,
    s.gender,
    s.dob,/* =========================
   DATABASE
========================= */
CREATE DATABASE IF NOT EXISTS student_management
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE student_management;

/* =========================
   COURSES
========================= */
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    duration INT NOT NULL,
    fee DECIMAL(10,2) NOT NULL,
    description TEXT,
    status ENUM('Active','Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX(status)
) ENGINE=InnoDB;

/* =========================
   BATCHES
========================= */
CREATE TABLE IF NOT EXISTS batches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    course_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    capacity INT NOT NULL,
    enrolled INT DEFAULT 0,
    status ENUM('Open','Full','Closed','Cancelled') DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id)
) ENGINE=InnoDB;

/* =========================
   ID TYPES
========================= */
CREATE TABLE IF NOT EXISTS id_document_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(100) UNIQUE,
    type_code VARCHAR(50) UNIQUE,
    description VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

/* =========================
   STUDENTS
========================= */
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) UNIQUE,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    dob DATE,
    gender ENUM('Male','Female','Other'),
    course_id INT,
    batch_id INT,
    address TEXT,
    total_fee DECIMAL(10,2),
    paid_amount DECIMAL(10,2) DEFAULT 0,
    balance DECIMAL(10,2),
    photo VARCHAR(255),
    id_document_type_id INT,
    id_document_number VARCHAR(100),
    id_document_file VARCHAR(255),
    admission_date DATETIME,
    status ENUM('Active','Inactive','Graduated','Dropped') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (batch_id) REFERENCES batches(id)
) ENGINE=InnoDB;

/* =========================
   PAYMENTS
========================= */
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    amount DECIMAL(10,2),
    method VARCHAR(50),
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (student_id) REFERENCES students(id)
) ENGINE=InnoDB;

/* =========================
   USERS
========================= */
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE,
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    full_name VARCHAR(255),
    role VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

/* =========================
   DEFAULT DATA
========================= */

INSERT IGNORE INTO courses(name,duration,fee,description) VALUES
('Computer Science',24,5000,'CS Program'),
('Business Administration',18,4500,'MBA'),
('Data Science',12,6000,'DS Program');

INSERT IGNORE INTO id_document_types(type_name,type_code) VALUES
('Passport','passport'),
('National ID','national_id'),
('Drivers License','drivers_license');

/* =========================
   SAFE VIEW
========================= */
CREATE OR REPLACE VIEW view_student_details AS
SELECT
s.id,
s.student_id,
CONCAT(s.first_name,' ',s.last_name) full_name,
c.name course,
b.name batch,
s.total_fee,
s.paid_amount,
s.balance
FROM students s
LEFT JOIN courses c ON s.course_id=c.id
LEFT JOIN batches b ON s.batch_id=b.id;

    s.address,
    c.name AS course_name,
    b.name AS batch_name,
    s.total_fee,
    s.paid_amount,
    s.balance,
    s.status AS student_status,
    idt.type_name AS id_document_type,
    s.id_document_number,
    s.id_document_verified,
    s.admission_date,
    s.photo,
    s.id_document_file
FROM students s
LEFT JOIN courses c ON s.course_id = c.id
LEFT JOIN batches b ON s.batch_id = b.id
LEFT JOIN id_document_types idt ON s.id_document_type_id = idt.id;


-- View: Fee Collection Summary
CREATE OR REPLACE VIEW view_fee_summary AS
SELECT 
    c.name AS course_name,
    COUNT(s.id) AS total_students,
    SUM(s.total_fee) AS total_expected,
    SUM(s.paid_amount) AS total_collected,
    SUM(s.balance) AS total_pending,
    ROUND((SUM(s.paid_amount) / SUM(s.total_fee)) * 100, 2) AS collection_rate
FROM students s
JOIN courses c ON s.course_id = c.id
WHERE s.status = 'Active'
GROUP BY c.id, c.name;


-- View: Batch Capacity Status
CREATE OR REPLACE VIEW view_batch_status AS
SELECT 
    b.name AS batch_name,
    c.name AS course_name,
    b.capacity,
    b.enrolled,
    (b.capacity - b.enrolled) AS available_seats,
    b.status,
    b.start_date,
    b.end_date,
    ROUND((b.enrolled / b.capacity) * 100, 2) AS occupancy_rate
FROM batches b
JOIN courses c ON b.course_id = c.id;


/* =========================
   STORED PROCEDURES
========================= */

DELIMITER //

-- Procedure: Generate Student ID
CREATE PROCEDURE IF NOT EXISTS sp_generate_student_id(
    OUT new_student_id VARCHAR(50)
)
BEGIN
    DECLARE year_prefix VARCHAR(2);
    DECLARE next_number INT;
    DECLARE last_id VARCHAR(50);
    
    SET year_prefix = RIGHT(YEAR(CURDATE()), 2);
    
    SELECT student_id INTO last_id 
    FROM students 
    WHERE student_id LIKE CONCAT('STD', year_prefix, '%')
    ORDER BY id DESC 
    LIMIT 1;
    
    IF last_id IS NULL THEN
        SET next_number = 1;
    ELSE
        SET next_number = CAST(SUBSTRING(last_id, 6) AS UNSIGNED) + 1;
    END IF;
    
    SET new_student_id = CONCAT('STD', year_prefix, '-', LPAD(next_number, 4, '0'));
END //

-- Procedure: Record Payment and Update Balance
CREATE PROCEDURE IF NOT EXISTS sp_record_payment(
    IN p_student_id INT,
    IN p_amount DECIMAL(10,2),
    IN p_method VARCHAR(50),
    IN p_notes TEXT,
    IN p_recorded_by INT,
    OUT p_payment_id INT
)
BEGIN
    DECLARE v_balance DECIMAL(10,2);
    DECLARE v_reference VARCHAR(100);
    
    START TRANSACTION;
    
    -- Generate payment reference
    SET v_reference = CONCAT('PAY-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));
    
    -- Insert payment
    INSERT INTO payments (
        payment_reference, 
        student_id, 
        amount, 
        method, 
        notes, 
        recorded_by
    ) VALUES (
        v_reference,
        p_student_id,
        p_amount,
        p_method,
        p_notes,
        p_recorded_by
    );
    
    SET p_payment_id = LAST_INSERT_ID();
    
    -- Update student balance
    UPDATE students 
    SET 
        paid_amount = paid_amount + p_amount,
        balance = balance - p_amount,
        updated_at = NOW()
    WHERE id = p_student_id;
    
    COMMIT;
END //

-- Procedure: Update Batch Enrolled Count
CREATE PROCEDURE IF NOT EXISTS sp_update_batch_count(
    IN p_batch_id INT
)
BEGIN
    DECLARE v_count INT;
    
    SELECT COUNT(*) INTO v_count 
    FROM students 
    WHERE batch_id = p_batch_id AND status = 'Active';
    
    UPDATE batches 
    SET 
        enrolled = v_count,
        status = CASE 
            WHEN v_count >= capacity THEN 'Full'
            WHEN v_count < capacity AND status = 'Full' THEN 'Open'
            ELSE status
        END,
        updated_at = NOW()
    WHERE id = p_batch_id;
END //

DELIMITER ;


/* =========================
   TRIGGERS
========================= */

DELIMITER //

-- Trigger: Auto-update batch count after student insert
CREATE TRIGGER IF NOT EXISTS trg_after_student_insert
AFTER INSERT ON students
FOR EACH ROW
BEGIN
    CALL sp_update_batch_count(NEW.batch_id);
END //

-- Trigger: Auto-update batch count after student update (if batch changed)
CREATE TRIGGER IF NOT EXISTS trg_after_student_update
AFTER UPDATE ON students
FOR EACH ROW
BEGIN
    IF OLD.batch_id != NEW.batch_id THEN
        CALL sp_update_batch_count(OLD.batch_id);
        CALL sp_update_batch_count(NEW.batch_id);
    ELSEIF OLD.status != NEW.status THEN
        CALL sp_update_batch_count(NEW.batch_id);
    END IF;
END //

-- Trigger: Prevent over-enrollment
CREATE TRIGGER IF NOT EXISTS trg_before_student_insert
BEFORE INSERT ON students
FOR EACH ROW
BEGIN
    DECLARE v_enrolled INT;
    DECLARE v_capacity INT;
    
    SELECT enrolled, capacity INTO v_enrolled, v_capacity
    FROM batches WHERE id = NEW.batch_id;
    
    IF v_enrolled >= v_capacity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Batch is full. Cannot enroll new student.';
    END IF;
END //

DELIMITER ;