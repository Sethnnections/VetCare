SET FOREIGN_KEY_CHECKS=0;
DROP DATABASE IF EXISTS veterinary_ims;
CREATE DATABASE veterinary_ims;
USE veterinary_ims;

-- ==================== ENUM TABLES ====================

CREATE TABLE user_roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

INSERT INTO user_roles (role_name, description) VALUES
('admin', 'System administrator with full access'),
('veterinary', 'Veterinary staff with medical access'),
('client', 'Client access to own animals and appointments');

-- ==================== CORE TABLES ====================

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','veterinary','client') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  INDEX idx_role (`role`),
  INDEX idx_is_active (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Insert default users
INSERT INTO `users` (`username`, `email`, `password`, `role`, `is_active`, `first_name`, `last_name`, `phone`, `address`) VALUES
('admin', 'admin@vet.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'admin', 1, 'Patience', 'Manguluti', '0882279994', '1759 Blantyre'),
('sethpatience', 'sethpatiencemanguluti@outlook.com', '$2y$10$jf.8Oa9WM0JlPQJbGBM0gOwNfViTQp0IYHegfc/SpNLPaQlu4OJIy', 'client', 1, 'Seth', 'Patience', NULL, NULL),
('psmanguluti', 'admin@teampay.com', '$2y$10$ibjeulZJDCn.MQCM/PeTzubASwlWoxbTHH66jDopJGc9ImF5/wtvK', 'client', 1, NULL, NULL, NULL, NULL),
('seth', 'patmanseth@gmail.com', '$2y$10$n.F.7y1xPxakPku97NGHnOf/Q.CN0Tkl7Ce8bWAjBnUu9JxSqXfXG', 'veterinary', 1, 'Wanangwa', 'Manguluti', '0882279994', 'Area 18A'),
('wanagwa', 'sethpatiencemanguluti@outloo6k.com', '$2y$10$iEkcAfGO61u.6jhjaOCdceGSteysvSzw..U0lvX.cvlgKPpe3yUaK', 'veterinary', 1, NULL, NULL, NULL, NULL);

-- Clients table for additional client-specific information
CREATE TABLE clients (
    client_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    emergency_contact VARCHAR(20),
    preferred_contact_method ENUM('phone', 'email', 'sms') DEFAULT 'phone',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);

CREATE TABLE animals (
    animal_id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    species VARCHAR(50) NOT NULL,
    breed VARCHAR(100),
    gender ENUM('male', 'female', 'unknown') DEFAULT 'unknown',
    birth_date DATE,
    color VARCHAR(100),
    weight DECIMAL(5,2),
    microchip VARCHAR(100) UNIQUE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE RESTRICT,
    INDEX idx_client_id (client_id),
    INDEX idx_species (species),
    INDEX idx_status (status),
    INDEX idx_microchip (microchip),
    INDEX idx_name (name)
);

CREATE TABLE treatments (
    treatment_id INT PRIMARY KEY AUTO_INCREMENT,
    animal_id INT NOT NULL,
    veterinary_id INT NOT NULL,
    diagnosis TEXT NOT NULL,
    treatment_details TEXT NOT NULL,
    medication_prescribed TEXT,
    treatment_date DATE NOT NULL,
    follow_up_date DATE,
    status ENUM('ongoing', 'completed', 'follow_up') DEFAULT 'ongoing',
    notes TEXT,
    cost DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(animal_id) ON DELETE CASCADE,
    FOREIGN KEY (veterinary_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_animal_id (animal_id),
    INDEX idx_veterinary_id (veterinary_id),
    INDEX idx_treatment_date (treatment_date),
    INDEX idx_status (status),
    INDEX idx_follow_up_date (follow_up_date)
);

CREATE TABLE vaccines (
    vaccine_id INT PRIMARY KEY AUTO_INCREMENT,
    animal_id INT NOT NULL,
    vaccine_name VARCHAR(100) NOT NULL,
    vaccine_type VARCHAR(50),
    vaccine_date DATE NOT NULL,
    next_due_date DATE,
    administered_by INT NOT NULL,
    batch_number VARCHAR(100),
    manufacturer VARCHAR(100),
    notes TEXT,
    status ENUM('scheduled', 'completed', 'overdue') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(animal_id) ON DELETE CASCADE,
    FOREIGN KEY (administered_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_animal_id (animal_id),
    INDEX idx_vaccine_date (vaccine_date),
    INDEX idx_next_due_date (next_due_date),
    INDEX idx_status (status)
);

CREATE TABLE billings (
    billing_id INT PRIMARY KEY AUTO_INCREMENT,
    animal_id INT NOT NULL,
    treatment_id INT,
    billing_date DATE NOT NULL,
    due_date DATE,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    payment_status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_date DATE,
    notes TEXT,
    items JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(animal_id) ON DELETE CASCADE,
    FOREIGN KEY (treatment_id) REFERENCES treatments(treatment_id) ON DELETE SET NULL,
    INDEX idx_animal_id (animal_id),
    INDEX idx_billing_date (billing_date),
    INDEX idx_payment_status (payment_status),
    INDEX idx_due_date (due_date)
);

CREATE TABLE reminders (
    reminder_id INT PRIMARY KEY AUTO_INCREMENT,
    animal_id INT NOT NULL,
    reminder_type ENUM('vaccination', 'treatment_followup', 'appointment', 'billing', 'general') NOT NULL,
    reminder_date DATE NOT NULL,
    due_date DATE NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    assigned_to INT,
    notes TEXT,
    related_type VARCHAR(50),
    related_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(animal_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_animal_id (animal_id),
    INDEX idx_due_date (due_date),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_reminder_type (reminder_type),
    INDEX idx_assigned_to (assigned_to)
);

-- ==================== AUDIT TABLES ====================

CREATE TABLE audit_logs (
    audit_id INT PRIMARY KEY AUTO_INCREMENT,
    table_name VARCHAR(50) NOT NULL,
    record_id INT NOT NULL,
    action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    old_values JSON,
    new_values JSON,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id)
);

CREATE TABLE system_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    level ENUM('INFO', 'WARNING', 'ERROR', 'DEBUG') DEFAULT 'INFO',
    message TEXT NOT NULL,
    context JSON,
    user_id INT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_level (level),
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id)
);

-- ==================== TRIGGERS ====================

-- Trigger 1: Auto-create client record when user with client role is created
DELIMITER $$
CREATE TRIGGER after_user_client_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.role = 'client' THEN
        INSERT INTO clients (user_id) VALUES (NEW.user_id);
    END IF;
END$$
DELIMITER ;

-- Trigger 2: Auto-update total_amount in billings
DELIMITER $$
CREATE TRIGGER before_billing_insert
BEFORE INSERT ON billings
FOR EACH ROW
BEGIN
    IF NEW.total_amount = 0 THEN
        SET NEW.total_amount = NEW.amount + NEW.tax_amount - NEW.discount;
    END IF;
    
    IF NEW.due_date IS NULL THEN
        SET NEW.due_date = DATE_ADD(NEW.billing_date, INTERVAL 30 DAY);
    END IF;
END$$
DELIMITER ;

-- Trigger 3: Auto-update total_amount in billings on update
DELIMITER $$
CREATE TRIGGER before_billing_update
BEFORE UPDATE ON billings
FOR EACH ROW
BEGIN
    IF NEW.amount != OLD.amount OR NEW.tax_amount != OLD.tax_amount OR NEW.discount != OLD.discount THEN
        SET NEW.total_amount = NEW.amount + NEW.tax_amount - NEW.discount;
    END IF;
END$$
DELIMITER ;

-- Trigger 4: Auto-create billing when treatment is completed
DELIMITER $$
CREATE TRIGGER after_treatment_completed
AFTER UPDATE ON treatments
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' AND OLD.status != 'completed' AND NEW.cost > 0 THEN
        INSERT INTO billings (animal_id, treatment_id, billing_date, amount, total_amount, notes)
        VALUES (NEW.animal_id, NEW.treatment_id, CURDATE(), NEW.cost, NEW.cost, 
                CONCAT('Treatment: ', SUBSTRING(NEW.diagnosis, 1, 100)));
    END IF;
END$$
DELIMITER ;

-- Trigger 5: Auto-update vaccine status based on dates
DELIMITER $$
CREATE TRIGGER before_vaccine_insert
BEFORE INSERT ON vaccines
FOR EACH ROW
BEGIN
    IF NEW.next_due_date IS NOT NULL AND NEW.next_due_date < CURDATE() AND NEW.status = 'scheduled' THEN
        SET NEW.status = 'overdue';
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER before_vaccine_update
BEFORE UPDATE ON vaccines
FOR EACH ROW
BEGIN
    IF NEW.next_due_date IS NOT NULL AND NEW.next_due_date < CURDATE() AND NEW.status = 'scheduled' THEN
        SET NEW.status = 'overdue';
    END IF;
END$$
DELIMITER ;

-- Trigger 6: Auto-create vaccination reminder
DELIMITER $$
CREATE TRIGGER after_vaccine_insert
AFTER INSERT ON vaccines
FOR EACH ROW
BEGIN
    IF NEW.next_due_date IS NOT NULL THEN
        INSERT INTO reminders (animal_id, reminder_type, reminder_date, due_date, title, description, priority)
        VALUES (
            NEW.animal_id,
            'vaccination',
            CURDATE(),
            NEW.next_due_date,
            CONCAT('Vaccination Due: ', NEW.vaccine_name),
            CONCAT(NEW.vaccine_name, ' vaccination is due for animal ID: ', NEW.animal_id),
            'high'
        );
    END IF;
END$$
DELIMITER ;

-- Trigger 7: Auto-create treatment follow-up reminder
DELIMITER $$
CREATE TRIGGER after_treatment_followup
AFTER INSERT ON treatments
FOR EACH ROW
BEGIN
    IF NEW.follow_up_date IS NOT NULL THEN
        INSERT INTO reminders (animal_id, reminder_type, reminder_date, due_date, title, description, priority, related_type, related_id)
        VALUES (
            NEW.animal_id,
            'treatment_followup',
            CURDATE(),
            NEW.follow_up_date,
            CONCAT('Treatment Follow-up: ', SUBSTRING(NEW.diagnosis, 1, 50)),
            CONCAT('Follow-up required for treatment: ', SUBSTRING(NEW.diagnosis, 1, 100)),
            'medium',
            'treatment',
            NEW.treatment_id
        );
    END IF;
END$$
DELIMITER ;

-- Trigger 8: Log user login activity
DELIMITER $$
CREATE TRIGGER after_user_login
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF NEW.last_login != OLD.last_login THEN
        INSERT INTO system_logs (level, message, user_id, context)
        VALUES (
            'INFO',
            CONCAT('User login: ', COALESCE(CONCAT(NEW.first_name, ' ', NEW.last_name), NEW.username), ' (', NEW.email, ')'),
            NEW.user_id,
            JSON_OBJECT('last_login', NEW.last_login)
        );
    END IF;
END$$
DELIMITER ;

-- Trigger 9: Prevent deletion of clients with active animals
DELIMITER $$
CREATE TRIGGER before_client_delete
BEFORE DELETE ON clients
FOR EACH ROW
BEGIN
    DECLARE active_animals INT;
    
    SELECT COUNT(*) INTO active_animals 
    FROM animals 
    WHERE client_id = OLD.client_id AND status = 'active';
    
    IF active_animals > 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Cannot delete client with active animals. Deactivate animals first.';
    END IF;
END$$
DELIMITER ;

-- ==================== STORED PROCEDURES ====================

DELIMITER $$
CREATE PROCEDURE GetAnimalMedicalHistory(IN animal_id_param INT)
BEGIN
    -- Treatments
    SELECT 'treatment' as type, treatment_date as date, diagnosis as title, 
           treatment_details as description, cost as amount, status
    FROM treatments 
    WHERE animal_id = animal_id_param
    UNION ALL
    -- Vaccines
    SELECT 'vaccine' as type, vaccine_date as date, vaccine_name as title,
           CONCAT('Type: ', COALESCE(vaccine_type, 'N/A'), ' | Batch: ', COALESCE(batch_number, 'N/A')) as description,
           0 as amount, status
    FROM vaccines 
    WHERE animal_id = animal_id_param
    ORDER BY date DESC;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE GetClientFinancialSummary(IN client_id_param INT)
BEGIN
    SELECT 
        CONCAT(u.first_name, ' ', u.last_name) as client_name,
        u.email,
        u.phone,
        u.address,
        COUNT(DISTINCT a.animal_id) as total_animals,
        COUNT(DISTINCT t.treatment_id) as total_treatments,
        SUM(CASE WHEN b.payment_status = 'paid' THEN b.total_amount ELSE 0 END) as total_paid,
        SUM(CASE WHEN b.payment_status = 'pending' THEN b.total_amount ELSE 0 END) as total_pending,
        MAX(b.billing_date) as last_billing_date
    FROM clients c
    JOIN users u ON c.user_id = u.user_id
    LEFT JOIN animals a ON c.client_id = a.client_id
    LEFT JOIN treatments t ON a.animal_id = t.animal_id
    LEFT JOIN billings b ON a.animal_id = b.animal_id
    WHERE c.client_id = client_id_param
    GROUP BY c.client_id, u.first_name, u.last_name, u.email, u.phone, u.address;
END$$
DELIMITER ;

-- ==================== VIEWS ====================

CREATE VIEW animal_details AS
SELECT 
    a.animal_id,
    a.name as animal_name,
    a.species,
    a.breed,
    a.gender,
    a.birth_date,
    a.color,
    a.weight,
    a.microchip,
    a.status as animal_status,
    c.client_id,
    CONCAT(u.first_name, ' ', u.last_name) as client_name,
    u.phone as client_phone,
    u.email as client_email,
    u.address as client_address,
    TIMESTAMPDIFF(YEAR, a.birth_date, CURDATE()) as age_years,
    TIMESTAMPDIFF(MONTH, a.birth_date, CURDATE()) as age_months
FROM animals a
JOIN clients c ON a.client_id = c.client_id
JOIN users u ON c.user_id = u.user_id;

CREATE VIEW treatment_details AS
SELECT 
    t.treatment_id,
    t.animal_id,
    a.name as animal_name,
    a.species,
    CONCAT(u_client.first_name, ' ', u_client.last_name) as client_name,
    CONCAT(u_vet.first_name, ' ', u_vet.last_name) as veterinary_name,
    t.diagnosis,
    t.treatment_details,
    t.treatment_date,
    t.follow_up_date,
    t.status,
    t.cost,
    DATEDIFF(CURDATE(), t.treatment_date) as days_since_treatment
FROM treatments t
JOIN animals a ON t.animal_id = a.animal_id
JOIN clients c ON a.client_id = c.client_id
JOIN users u_client ON c.user_id = u_client.user_id
JOIN users u_vet ON t.veterinary_id = u_vet.user_id;

-- ==================== SAMPLE DATA ====================

-- Create client records for existing client users (trigger should handle new ones)
INSERT IGNORE INTO clients (user_id) 
SELECT user_id FROM users WHERE role = 'client';

-- Insert sample animals
INSERT INTO animals (client_id, name, species, breed, gender, birth_date, color, weight, microchip, status) VALUES
(1, 'Max', 'dog', 'German Shepherd', 'male', '2020-03-15', 'Black/Tan', 35.5, 'MICRO001', 'active'),
(1, 'Luna', 'cat', 'Siamese', 'female', '2021-06-20', 'Cream', 4.2, 'MICRO002', 'active'),
(2, 'Buddy', 'dog', 'Labrador Retriever', 'male', '2019-11-10', 'Yellow', 28.0, 'MICRO003', 'active'),
(3, 'Mittens', 'cat', 'Domestic Shorthair', 'female', '2022-01-05', 'Tabby', 3.8, NULL, 'active');

-- Insert sample treatments
INSERT INTO treatments (animal_id, veterinary_id, diagnosis, treatment_details, treatment_date, follow_up_date, status, cost) VALUES
(1, 4, 'Vaccination - Rabies', 'Administered rabies vaccine. No adverse reactions observed.', '2024-01-15', '2025-01-15', 'completed', 25.00),
(1, 4, 'Skin infection', 'Prescribed antibiotics for skin infection. Apply topical ointment twice daily.', '2024-02-20', '2024-03-05', 'completed', 45.50),
(2, 5, 'Spaying', 'Routine spaying procedure. Recovery normal.', '2024-01-10', '2024-01-24', 'completed', 120.00),
(3, 4, 'Dental cleaning', 'Professional dental cleaning. Minor tartar buildup removed.', '2024-03-01', NULL, 'completed', 85.00);

-- Insert sample vaccines
INSERT INTO vaccines (animal_id, vaccine_name, vaccine_type, vaccine_date, next_due_date, administered_by, batch_number, manufacturer, status) VALUES
(1, 'Rabies Vaccine', 'Rabies', '2024-01-15', '2025-01-15', 4, 'RB2024A1', 'VetPharm', 'completed'),
(1, 'DHPP Vaccine', 'Core', '2024-01-15', '2025-01-15', 4, 'DH2024B2', 'AnimalHealth', 'completed'),
(2, 'FVRCP Vaccine', 'Core', '2024-01-10', '2025-01-10', 5, 'FV2024C3', 'CatCare', 'completed');

-- Insert sample billings
INSERT INTO billings (animal_id, treatment_id, billing_date, due_date, amount, tax_amount, discount, total_amount, payment_status, payment_method) VALUES
(1, 1, '2024-01-15', '2024-02-14', 25.00, 2.00, 0, 27.00, 'paid', 'cash'),
(1, 2, '2024-02-20', '2024-03-21', 45.50, 3.64, 5.00, 44.14, 'paid', 'mobile_money'),
(2, 3, '2024-01-10', '2024-02-09', 120.00, 9.60, 0, 129.60, 'paid', 'cash'),
(3, 4, '2024-03-01', '2024-03-31', 85.00, 6.80, 0, 91.80, 'pending', NULL);

SET FOREIGN_KEY_CHECKS=1;

SELECT 'Veterinary IMS Database created successfully!' as message;
SELECT COUNT(*) as total_tables FROM information_schema.tables 
WHERE table_schema = 'veterinary_ims' AND table_type = 'BASE TABLE';