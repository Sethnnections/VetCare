-- Veterinary Health Management System Database Schema
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database
CREATE DATABASE IF NOT EXISTS `veterinary_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `veterinary_system`;

-- --------------------------------------------------------
-- Table structure for users
-- --------------------------------------------------------

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','veterinary','client') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for clients
-- --------------------------------------------------------

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_clients_status` (`status`),
  KEY `idx_clients_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for animals
-- --------------------------------------------------------

CREATE TABLE `animals` (
  `animal_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `species` varchar(50) NOT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `gender` enum('male','female','unknown') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `microchip` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`animal_id`),
  KEY `fk_animals_client` (`client_id`),
  UNIQUE KEY `microchip` (`microchip`),
  KEY `idx_animals_species` (`species`),
  KEY `idx_animals_status` (`status`),
  CONSTRAINT `fk_animals_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for treatments
-- --------------------------------------------------------

CREATE TABLE `treatments` (
  `treatment_id` int(11) NOT NULL AUTO_INCREMENT,
  `animal_id` int(11) NOT NULL,
  `veterinary_id` int(11) NOT NULL,
  `diagnosis` text NOT NULL,
  `treatment_details` text NOT NULL,
  `medication_prescribed` text DEFAULT NULL,
  `treatment_date` date NOT NULL,
  `follow_up_date` date DEFAULT NULL,
  `status` enum('ongoing','completed','follow_up') NOT NULL DEFAULT 'ongoing',
  `cost` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`treatment_id`),
  KEY `fk_treatments_animal` (`animal_id`),
  KEY `fk_treatments_veterinary` (`veterinary_id`),
  KEY `idx_treatments_date` (`treatment_date`),
  KEY `idx_treatments_status` (`status`),
  KEY `idx_treatments_follow_up` (`follow_up_date`),
  CONSTRAINT `fk_treatments_animal` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_treatments_veterinary` FOREIGN KEY (`veterinary_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for medicines
-- --------------------------------------------------------

CREATE TABLE `medicines` (
  `medicine_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` enum('antibiotic','vaccine','supplement','anesthetic','other') NOT NULL,
  `description` text DEFAULT NULL,
  `dosage_form` varchar(50) DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `quantity_in_stock` int(11) NOT NULL DEFAULT 0,
  `unit_price` decimal(10,2) DEFAULT 0.00,
  `reorder_level` int(11) DEFAULT 10,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`medicine_id`),
  KEY `idx_medicines_type` (`type`),
  KEY `idx_medicines_status` (`status`),
  KEY `idx_medicines_expiry` (`expiry_date`),
  KEY `idx_medicines_stock` (`quantity_in_stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for vaccines
-- --------------------------------------------------------

CREATE TABLE `vaccines` (
  `vaccine_id` int(11) NOT NULL AUTO_INCREMENT,
  `animal_id` int(11) NOT NULL,
  `veterinary_id` int(11) NOT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `vaccine_name` varchar(100) NOT NULL,
  `vaccine_date` date NOT NULL,
  `next_due_date` date DEFAULT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `site_administered` varchar(50) DEFAULT NULL,
  `dosage` varchar(50) DEFAULT NULL,
  `reaction_notes` text DEFAULT NULL,
  `status` enum('administered','scheduled','overdue') NOT NULL DEFAULT 'administered',
  `cost` decimal(8,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`vaccine_id`),
  KEY `fk_vaccines_animal` (`animal_id`),
  KEY `fk_vaccines_veterinary` (`veterinary_id`),
  KEY `fk_vaccines_medicine` (`medicine_id`),
  KEY `idx_vaccines_date` (`vaccine_date`),
  KEY `idx_vaccines_due_date` (`next_due_date`),
  KEY `idx_vaccines_status` (`status`),
  CONSTRAINT `fk_vaccines_animal` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_vaccines_veterinary` FOREIGN KEY (`veterinary_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_vaccines_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for billings
-- --------------------------------------------------------

CREATE TABLE `billings` (
  `billing_id` int(11) NOT NULL AUTO_INCREMENT,
  `animal_id` int(11) NOT NULL,
  `treatment_id` int(11) DEFAULT NULL,
  `vaccine_id` int(11) DEFAULT NULL,
  `service_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `tax_amount` decimal(8,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','mobile_money','bank_transfer') DEFAULT NULL,
  `payment_status` enum('pending','paid','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `billing_date` date NOT NULL,
  `payment_date` date DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`billing_id`),
  KEY `fk_billings_animal` (`animal_id`),
  KEY `fk_billings_treatment` (`treatment_id`),
  KEY `fk_billings_vaccine` (`vaccine_id`),
  KEY `idx_billings_date` (`billing_date`),
  KEY `idx_billings_status` (`payment_status`),
  KEY `idx_billings_reference` (`reference_number`),
  CONSTRAINT `fk_billings_animal` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_billings_treatment` FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`treatment_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_billings_vaccine` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`vaccine_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for reminders
-- --------------------------------------------------------

CREATE TABLE `reminders` (
  `reminder_id` int(11) NOT NULL AUTO_INCREMENT,
  `animal_id` int(11) NOT NULL,
  `veterinary_id` int(11) DEFAULT NULL,
  `type` enum('vaccination','follow_up','appointment','medication','checkup') NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `reminder_date` date NOT NULL,
  `reminder_time` time DEFAULT NULL,
  `status` enum('pending','sent','completed','cancelled') NOT NULL DEFAULT 'pending',
  `send_sms` tinyint(1) DEFAULT 0,
  `send_email` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`reminder_id`),
  KEY `fk_reminders_animal` (`animal_id`),
  KEY `fk_reminders_veterinary` (`veterinary_id`),
  KEY `idx_reminders_date` (`reminder_date`),
  KEY `idx_reminders_status` (`status`),
  KEY `idx_reminders_type` (`type`),
  CONSTRAINT `fk_reminders_animal` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reminders_veterinary` FOREIGN KEY (`veterinary_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for feedback
-- --------------------------------------------------------

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `animal_id` int(11) DEFAULT NULL,
  `treatment_id` int(11) DEFAULT NULL,
  `rating` tinyint(1) DEFAULT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `response` text DEFAULT NULL,
  `responded_by` int(11) DEFAULT NULL,
  `status` enum('pending','reviewed','responded','closed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`feedback_id`),
  KEY `fk_feedback_client` (`client_id`),
  KEY `fk_feedback_animal` (`animal_id`),
  KEY `fk_feedback_treatment` (`treatment_id`),
  KEY `fk_feedback_responded_by` (`responded_by`),
  KEY `idx_feedback_status` (`status`),
  KEY `idx_feedback_rating` (`rating`),
  CONSTRAINT `fk_feedback_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_feedback_animal` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_feedback_treatment` FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`treatment_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_feedback_responded_by` FOREIGN KEY (`responded_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for appointments
-- --------------------------------------------------------

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `animal_id` int(11) DEFAULT NULL,
  `veterinary_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `duration` int(11) DEFAULT 30,
  `service_type` varchar(100) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('scheduled','confirmed','completed','cancelled','no_show') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `reminder_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`appointment_id`),
  KEY `fk_appointments_client` (`client_id`),
  KEY `fk_appointments_animal` (`animal_id`),
  KEY `fk_appointments_veterinary` (`veterinary_id`),
  KEY `idx_appointments_date` (`appointment_date`),
  KEY `idx_appointments_status` (`status`),
  CONSTRAINT `fk_appointments_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_appointments_animal` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_appointments_veterinary` FOREIGN KEY (`veterinary_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for medicine_inventory
-- --------------------------------------------------------

CREATE TABLE `medicine_inventory` (
  `inventory_id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) NOT NULL,
  `transaction_type` enum('purchase','usage','adjustment','return') NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`inventory_id`),
  KEY `fk_inventory_medicine` (`medicine_id`),
  KEY `fk_inventory_created_by` (`created_by`),
  KEY `idx_inventory_date` (`transaction_date`),
  KEY `idx_inventory_type` (`transaction_type`),
  CONSTRAINT `fk_inventory_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for treatment_medicines
-- --------------------------------------------------------

CREATE TABLE `treatment_medicines` (
  `treatment_medicine_id` int(11) NOT NULL AUTO_INCREMENT,
  `treatment_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity_used` int(11) NOT NULL DEFAULT 1,
  `dosage` varchar(100) DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`treatment_medicine_id`),
  KEY `fk_treatment_medicines_treatment` (`treatment_id`),
  KEY `fk_treatment_medicines_medicine` (`medicine_id`),
  CONSTRAINT `fk_treatment_medicines_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_treatment_medicines_treatment` FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`treatment_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for audit_log
-- --------------------------------------------------------

CREATE TABLE `audit_log` (
  `audit_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`audit_id`),
  KEY `fk_audit_user` (`user_id`),
  KEY `idx_audit_action` (`action`),
  KEY `idx_audit_table` (`table_name`),
  KEY `idx_audit_date` (`created_at`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for system_settings
-- --------------------------------------------------------

CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `idx_settings_public` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Insert default system settings
-- --------------------------------------------------------

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_description`, `is_public`) VALUES
('clinic_name', 'Veterinary Public Health Laboratory', 'Name of the veterinary clinic', 1),
('clinic_address', 'Ginnery Corner, Blantyre, Malawi', 'Physical address of the clinic', 1),
('clinic_phone', '+265 123 456 789', 'Primary contact phone number', 1),
('clinic_email', 'info@vetlab.mw', 'Primary contact email', 1),
('currency', 'MWK', 'Default currency for billing', 0),
('tax_rate', '16.5', 'Default tax rate percentage', 0),
('sms_enabled', '0', 'Whether SMS notifications are enabled', 0),
('email_enabled', '1', 'Whether email notifications are enabled', 0),
('appointment_reminder_days', '2', 'Days before appointment to send reminder', 0),
('vaccine_reminder_days', '7', 'Days before vaccine due date to send reminder', 0);

-- --------------------------------------------------------
-- Insert default admin user
-- --------------------------------------------------------

INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone`, `status`) VALUES
('System Administrator', 'admin@vetlab.mw', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '+265 999 888 777', 1);

-- --------------------------------------------------------
-- Insert sample medicine data
-- --------------------------------------------------------

INSERT INTO `medicines` (`name`, `type`, `description`, `dosage_form`, `strength`, `manufacturer`, `quantity_in_stock`, `unit_price`, `reorder_level`) VALUES
('Amoxicillin', 'antibiotic', 'Broad-spectrum antibiotic for bacterial infections', 'Tablet', '250mg', 'Pharma Ltd', 100, 1500.00, 20),
('Rabies Vaccine', 'vaccine', 'Vaccine for rabies prevention', 'Injectable', '1 dose vial', 'VetVax', 50, 2500.00, 10),
('Ivermectin', 'antibiotic', 'Antiparasitic medication', 'Injectable', '1% solution', 'Animal Health Co', 30, 1200.00, 5),
('Multivitamin Supplement', 'supplement', 'General vitamin supplement for animals', 'Liquid', '500ml', 'NutriPet', 25, 3500.00, 5),
('Ketamine', 'anesthetic', 'General anesthetic for surgical procedures', 'Injectable', '100mg/ml', 'MedSolutions', 15, 4500.00, 3);

COMMIT;