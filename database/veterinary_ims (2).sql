-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2025 at 11:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `veterinary_ims`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAnimalMedicalHistory` (IN `animal_id_param` INT)   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetClientFinancialSummary` (IN `client_id_param` INT)   BEGIN
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

-- --------------------------------------------------------

--
-- Table structure for table `animals`
--

CREATE TABLE `animals` (
  `animal_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `assigned_veterinary` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `species` varchar(50) NOT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `gender` enum('male','female','unknown') DEFAULT 'unknown',
  `birth_date` date DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `microchip` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `animals`
--

INSERT INTO `animals` (`animal_id`, `client_id`, `assigned_veterinary`, `name`, `species`, `breed`, `gender`, `birth_date`, `color`, `weight`, `microchip`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'Max', 'dog', 'German Shepherd', 'male', '2020-03-15', 'Black/Tan', 35.50, 'MICRO001', 'active', NULL, '2025-10-13 14:07:25', '2025-10-17 14:00:23'),
(2, 1, 4, 'Luna', 'cat', 'Siamese', 'female', '2021-06-20', 'Cream', 4.20, 'MICRO002', 'active', NULL, '2025-10-13 14:07:25', '2025-10-17 14:00:23'),
(3, 2, 5, 'Buddy', 'dog', 'Labrador Retriever', 'male', '2019-11-10', 'Yellow', 28.00, 'MICRO003', 'active', NULL, '2025-10-13 14:07:25', '2025-10-17 14:00:25'),
(4, 2, 5, 'Mittens', 'cat', 'Domestic Shorthair', 'female', '2022-01-05', 'Tabby', 3.80, NULL, 'active', NULL, '2025-10-13 14:07:25', '2025-10-17 14:00:25'),
(5, 3, 6, 'Bruce', 'dog', 'German Shaperd', 'female', '2025-01-01', 'Black and White', 50.00, '1000973478489', 'active', 'Clean', '2025-10-17 12:52:04', '2025-10-17 14:47:36'),
(6, 6, 4, 'Zigge', 'horse', 'Brazil', 'male', '2025-01-01', 'Black and White', 50.00, 'WEEF5567G', 'active', 'Nope', '2025-10-18 14:01:39', '2025-10-24 07:54:10');

-- --------------------------------------------------------

--
-- Table structure for table `animal_assignments_history`
--

CREATE TABLE `animal_assignments_history` (
  `history_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `veterinary_id` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `action` enum('assigned','unassigned') NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `animal_assignments_history`
--

INSERT INTO `animal_assignments_history` (`history_id`, `animal_id`, `veterinary_id`, `assigned_by`, `action`, `assigned_at`) VALUES
(1, 6, 4, 1, 'assigned', '2025-10-24 07:54:10');

-- --------------------------------------------------------

--
-- Stand-in structure for view `animal_client_view`
-- (See below for the actual view)
--
CREATE TABLE `animal_client_view` (
`animal_id` int(11)
,`client_id` int(11)
,`assigned_veterinary` int(11)
,`animal_name` varchar(100)
,`species` varchar(50)
,`breed` varchar(100)
,`gender` enum('male','female','unknown')
,`birth_date` date
,`color` varchar(100)
,`weight` decimal(5,2)
,`microchip` varchar(100)
,`animal_status` enum('active','inactive')
,`animal_notes` text
,`animal_created_at` timestamp
,`animal_updated_at` timestamp
,`client_user_id` int(11)
,`client_first_name` varchar(50)
,`client_last_name` varchar(50)
,`client_full_name` varchar(101)
,`client_email` varchar(100)
,`client_phone` varchar(20)
,`client_address` text
,`vet_first_name` varchar(50)
,`vet_last_name` varchar(50)
,`vet_full_name` varchar(101)
,`vet_email` varchar(100)
,`age_years` bigint(21)
,`age_months` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `animal_details`
-- (See below for the actual view)
--
CREATE TABLE `animal_details` (
`animal_id` int(11)
,`animal_name` varchar(100)
,`species` varchar(50)
,`breed` varchar(100)
,`gender` enum('male','female','unknown')
,`birth_date` date
,`color` varchar(100)
,`weight` decimal(5,2)
,`microchip` varchar(100)
,`animal_status` enum('active','inactive')
,`client_id` int(11)
,`client_name` varchar(101)
,`client_phone` varchar(20)
,`client_email` varchar(100)
,`client_address` text
,`age_years` bigint(21)
,`age_months` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `audit_id` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL,
  `action` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billings`
--

CREATE TABLE `billings` (
  `billing_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `treatment_id` int(11) DEFAULT NULL,
  `billing_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `billings`
--

INSERT INTO `billings` (`billing_id`, `animal_id`, `treatment_id`, `billing_date`, `due_date`, `amount`, `tax_amount`, `discount`, `total_amount`, `payment_status`, `payment_method`, `payment_date`, `notes`, `items`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-01-15', '2024-02-14', 25.00, 2.00, 0.00, 27.00, 'paid', 'cash', NULL, NULL, NULL, '2025-10-13 14:07:26', '2025-10-13 14:07:26'),
(2, 1, 2, '2024-02-20', '2024-03-21', 45.50, 3.64, 5.00, 44.14, 'paid', 'mobile_money', NULL, NULL, NULL, '2025-10-13 14:07:26', '2025-10-13 14:07:26'),
(3, 2, 3, '2024-01-10', '2024-02-09', 120.00, 9.60, 0.00, 129.60, 'paid', 'cash', NULL, NULL, NULL, '2025-10-13 14:07:26', '2025-10-13 14:07:26'),
(4, 3, 4, '2024-03-01', '2024-03-31', 85.00, 6.80, 0.00, 91.80, 'pending', NULL, NULL, NULL, NULL, '2025-10-13 14:07:26', '2025-10-13 14:07:26');

--
-- Triggers `billings`
--
DELIMITER $$
CREATE TRIGGER `before_billing_insert` BEFORE INSERT ON `billings` FOR EACH ROW BEGIN
    IF NEW.total_amount = 0 THEN
        SET NEW.total_amount = NEW.amount + NEW.tax_amount - NEW.discount;
    END IF;
    
    IF NEW.due_date IS NULL THEN
        SET NEW.due_date = DATE_ADD(NEW.billing_date, INTERVAL 30 DAY);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_billing_update` BEFORE UPDATE ON `billings` FOR EACH ROW BEGIN
    IF NEW.amount != OLD.amount OR NEW.tax_amount != OLD.tax_amount OR NEW.discount != OLD.discount THEN
        SET NEW.total_amount = NEW.amount + NEW.tax_amount - NEW.discount;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `preferred_contact_method` enum('phone','email','sms') DEFAULT 'phone',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `user_id`, `emergency_contact`, `preferred_contact_method`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, 'phone', NULL, '2025-10-13 14:07:24', '2025-10-13 14:07:24'),
(2, 3, NULL, 'phone', NULL, '2025-10-13 14:07:24', '2025-10-13 14:07:24'),
(3, 7, '+265 882 279 994', 'phone', 'Im happy', '2025-10-14 19:26:11', '2025-10-17 08:05:50'),
(4, 8, NULL, 'phone', NULL, '2025-10-15 13:01:45', '2025-10-15 13:01:45'),
(5, 9, NULL, 'phone', NULL, '2025-10-16 16:28:30', '2025-10-16 16:28:30'),
(6, 10, NULL, 'phone', NULL, '2025-10-18 13:59:56', '2025-10-18 13:59:56');

--
-- Triggers `clients`
--
DELIMITER $$
CREATE TRIGGER `before_client_delete` BEFORE DELETE ON `clients` FOR EACH ROW BEGIN
    DECLARE active_animals INT;
    
    SELECT COUNT(*) INTO active_animals 
    FROM animals 
    WHERE client_id = OLD.client_id AND status = 'active';
    
    IF active_animals > 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Cannot delete client with active animals. Deactivate animals first.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `client_details_view`
-- (See below for the actual view)
--
CREATE TABLE `client_details_view` (
`client_id` int(11)
,`emergency_contact` varchar(20)
,`preferred_contact_method` enum('phone','email','sms')
,`client_notes` text
,`client_created_at` timestamp
,`client_updated_at` timestamp
,`user_id` int(11)
,`username` varchar(50)
,`email` varchar(100)
,`first_name` varchar(50)
,`last_name` varchar(50)
,`full_name` varchar(101)
,`phone` varchar(20)
,`address` text
,`profile_picture` varchar(255)
,`is_active` tinyint(1)
,`last_login` datetime
,`active_animals_count` bigint(21)
,`total_animals_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `reminder_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `reminder_type` enum('vaccination','treatment_followup','appointment','billing','general') NOT NULL,
  `reminder_date` date NOT NULL,
  `due_date` date NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `assigned_to` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `related_type` varchar(50) DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `reminders`
--

INSERT INTO `reminders` (`reminder_id`, `animal_id`, `reminder_type`, `reminder_date`, `due_date`, `title`, `description`, `status`, `priority`, `assigned_to`, `notes`, `related_type`, `related_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'treatment_followup', '2025-10-13', '2025-01-15', 'Treatment Follow-up: Vaccination - Rabies', 'Follow-up required for treatment: Vaccination - Rabies', 'pending', 'medium', NULL, NULL, 'treatment', 1, '2025-10-13 14:07:25', '2025-10-13 14:07:25'),
(2, 1, 'treatment_followup', '2025-10-13', '2024-03-05', 'Treatment Follow-up: Skin infection', 'Follow-up required for treatment: Skin infection', 'pending', 'medium', NULL, NULL, 'treatment', 2, '2025-10-13 14:07:25', '2025-10-13 14:07:25'),
(3, 2, 'treatment_followup', '2025-10-13', '2024-01-24', 'Treatment Follow-up: Spaying', 'Follow-up required for treatment: Spaying', 'pending', 'medium', NULL, NULL, 'treatment', 3, '2025-10-13 14:07:25', '2025-10-13 14:07:25'),
(4, 1, 'vaccination', '2025-10-13', '2025-01-15', 'Vaccination Due: Rabies Vaccine', 'Rabies Vaccine vaccination is due for animal ID: 1', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-13 14:07:25', '2025-10-13 14:07:25'),
(5, 1, 'vaccination', '2025-10-13', '2025-01-15', 'Vaccination Due: DHPP Vaccine', 'DHPP Vaccine vaccination is due for animal ID: 1', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-13 14:07:25', '2025-10-13 14:07:25'),
(6, 2, 'vaccination', '2025-10-13', '2025-01-10', 'Vaccination Due: FVRCP Vaccine', 'FVRCP Vaccine vaccination is due for animal ID: 2', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-13 14:07:25', '2025-10-13 14:07:25');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `level` enum('INFO','WARNING','ERROR','DEBUG') DEFAULT 'INFO',
  `message` text NOT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`log_id`, `level`, `message`, `context`, `user_id`, `ip_address`, `created_at`) VALUES
(1, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-15 15:24:06\"}', 1, NULL, '2025-10-15 13:24:06'),
(2, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-15 15:36:01\"}', 6, NULL, '2025-10-15 13:36:01'),
(3, 'INFO', 'User login: Sam Manguluti (smanguluti@vims.com)', '{\"last_login\": \"2025-10-15 15:45:46\"}', 8, NULL, '2025-10-15 13:45:46'),
(4, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-15 15:46:50\"}', 6, NULL, '2025-10-15 13:46:50'),
(5, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-15 15:47:11\"}', 6, NULL, '2025-10-15 13:47:11'),
(6, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-15 15:49:58\"}', 6, NULL, '2025-10-15 13:49:58'),
(7, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-15 15:50:07\"}', 6, NULL, '2025-10-15 13:50:07'),
(8, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-15 15:50:35\"}', 1, NULL, '2025-10-15 13:50:35'),
(9, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-15 16:33:02\"}', 6, NULL, '2025-10-15 14:33:02'),
(10, 'INFO', 'User login: client q (client@outlook.com)', '{\"last_login\": \"2025-10-15 16:33:54\"}', 7, NULL, '2025-10-15 14:33:54'),
(11, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-15 17:00:25\"}', 6, NULL, '2025-10-15 15:00:25'),
(12, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-15 17:01:36\"}', 1, NULL, '2025-10-15 15:01:36'),
(13, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-15 17:08:27\"}', 1, NULL, '2025-10-15 15:08:27'),
(14, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-15 17:11:06\"}', 6, NULL, '2025-10-15 15:11:06'),
(15, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-15 17:12:32\"}', 1, NULL, '2025-10-15 15:12:32'),
(16, 'INFO', 'User login: client q (client@outlook.com)', '{\"last_login\": \"2025-10-15 17:15:26\"}', 7, NULL, '2025-10-15 15:15:26'),
(17, 'INFO', 'User login: client q (client@outlook.com)', '{\"last_login\": \"2025-10-16 16:01:07\"}', 7, NULL, '2025-10-16 14:01:07'),
(18, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-16 16:14:50\"}', 6, NULL, '2025-10-16 14:14:50'),
(19, 'INFO', 'User login: Sam Manguluti (smanguluti@vims.com)', '{\"last_login\": \"2025-10-16 16:15:08\"}', 8, NULL, '2025-10-16 14:15:08'),
(20, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-16 17:04:12\"}', 1, NULL, '2025-10-16 15:04:12'),
(21, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-16 17:04:32\"}', 6, NULL, '2025-10-16 15:04:32'),
(22, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-16 17:07:28\"}', 1, NULL, '2025-10-16 15:07:28'),
(23, 'INFO', 'User login: client q (client@outlook.com)', '{\"last_login\": \"2025-10-16 17:10:23\"}', 7, NULL, '2025-10-16 15:10:23'),
(24, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-16 17:30:41\"}', 6, NULL, '2025-10-16 15:30:41'),
(25, 'INFO', 'User login: client q (client@outlook.com)', '{\"last_login\": \"2025-10-16 18:22:52\"}', 7, NULL, '2025-10-16 16:22:52'),
(26, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-16 18:49:01\"}', 1, NULL, '2025-10-16 16:49:01'),
(27, 'INFO', 'User login: client q (client@outlook.com)', '{\"last_login\": \"2025-10-16 20:37:30\"}', 7, NULL, '2025-10-16 18:37:30'),
(28, 'INFO', 'User login: Seko Mwalwen (client@outlook.com)', '{\"last_login\": \"2025-10-17 10:08:06\"}', 7, NULL, '2025-10-17 08:08:06'),
(29, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-17 14:58:44\"}', 6, NULL, '2025-10-17 12:58:44'),
(30, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-17 16:06:08\"}', 1, NULL, '2025-10-17 14:06:08'),
(31, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-17 16:51:32\"}', 6, NULL, '2025-10-17 14:51:32'),
(32, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-18 09:23:01\"}', 1, NULL, '2025-10-18 07:23:01'),
(33, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-18 15:57:46\"}', 6, NULL, '2025-10-18 13:57:46'),
(34, 'INFO', 'User login: Simama Priscila (sp@outlook.com)', '{\"last_login\": \"2025-10-24 08:45:54\"}', 10, NULL, '2025-10-24 06:45:54'),
(35, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-24 09:51:44\"}', 1, NULL, '2025-10-24 07:51:44');

-- --------------------------------------------------------

--
-- Table structure for table `treatments`
--

CREATE TABLE `treatments` (
  `treatment_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `veterinary_id` int(11) NOT NULL,
  `diagnosis` text NOT NULL,
  `treatment_details` text NOT NULL,
  `medication_prescribed` text DEFAULT NULL,
  `treatment_date` date NOT NULL,
  `follow_up_date` date DEFAULT NULL,
  `status` enum('ongoing','completed','follow_up') DEFAULT 'ongoing',
  `notes` text DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `treatments`
--

INSERT INTO `treatments` (`treatment_id`, `animal_id`, `veterinary_id`, `diagnosis`, `treatment_details`, `medication_prescribed`, `treatment_date`, `follow_up_date`, `status`, `notes`, `cost`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'Vaccination - Rabies', 'Administered rabies vaccine. No adverse reactions observed.', NULL, '2024-01-15', '2025-01-15', 'completed', NULL, 25.00, '2025-10-13 14:07:25', '2025-10-13 14:07:25'),
(2, 1, 4, 'Skin infection', 'Prescribed antibiotics for skin infection. Apply topical ointment twice daily.', NULL, '2024-02-20', '2024-03-05', 'completed', NULL, 45.50, '2025-10-13 14:07:25', '2025-10-13 14:07:25'),
(3, 2, 5, 'Spaying', 'Routine spaying procedure. Recovery normal.', NULL, '2024-01-10', '2024-01-24', 'completed', NULL, 120.00, '2025-10-13 14:07:25', '2025-10-13 14:07:25'),
(4, 3, 4, 'Dental cleaning', 'Professional dental cleaning. Minor tartar buildup removed.', NULL, '2024-03-01', NULL, 'completed', NULL, 85.00, '2025-10-13 14:07:25', '2025-10-13 14:07:25');

--
-- Triggers `treatments`
--
DELIMITER $$
CREATE TRIGGER `after_treatment_completed` AFTER UPDATE ON `treatments` FOR EACH ROW BEGIN
    IF NEW.status = 'completed' AND OLD.status != 'completed' AND NEW.cost > 0 THEN
        INSERT INTO billings (animal_id, treatment_id, billing_date, amount, total_amount, notes)
        VALUES (NEW.animal_id, NEW.treatment_id, CURDATE(), NEW.cost, NEW.cost, 
                CONCAT('Treatment: ', SUBSTRING(NEW.diagnosis, 1, 100)));
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_treatment_followup` AFTER INSERT ON `treatments` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `treatment_details`
-- (See below for the actual view)
--
CREATE TABLE `treatment_details` (
`treatment_id` int(11)
,`animal_id` int(11)
,`animal_name` varchar(100)
,`species` varchar(50)
,`client_name` varchar(101)
,`veterinary_name` varchar(101)
,`diagnosis` text
,`treatment_details` text
,`treatment_date` date
,`follow_up_date` date
,`status` enum('ongoing','completed','follow_up')
,`cost` decimal(10,2)
,`days_since_treatment` int(7)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `treatment_details_view`
-- (See below for the actual view)
--
CREATE TABLE `treatment_details_view` (
`treatment_id` int(11)
,`animal_id` int(11)
,`veterinary_id` int(11)
,`diagnosis` text
,`treatment_details` text
,`medication_prescribed` text
,`treatment_date` date
,`follow_up_date` date
,`treatment_status` enum('ongoing','completed','follow_up')
,`treatment_notes` text
,`cost` decimal(10,2)
,`treatment_created_at` timestamp
,`treatment_updated_at` timestamp
,`animal_name` varchar(100)
,`species` varchar(50)
,`vet_first_name` varchar(50)
,`vet_last_name` varchar(50)
,`vet_full_name` varchar(101)
,`vet_email` varchar(100)
,`client_first_name` varchar(50)
,`client_last_name` varchar(50)
,`client_full_name` varchar(101)
,`days_since_treatment` int(7)
,`follow_up_status` varchar(7)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
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
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `is_active`, `created_at`, `first_name`, `last_name`, `phone`, `address`, `profile_picture`, `updated_at`, `last_login`) VALUES
(1, 'admin', 'admin@vet.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'admin', 1, '2025-10-13 14:07:16', 'Patience', 'Manguluti', '0882279994', '1759 Blantyre', NULL, '2025-10-24 07:51:44', '2025-10-24 09:51:44'),
(2, 'sethpatience', 'sethpatiencemanguluti@outlook.com', '$2y$10$jf.8Oa9WM0JlPQJbGBM0gOwNfViTQp0IYHegfc/SpNLPaQlu4OJIy', 'client', 1, '2025-10-13 14:07:16', 'Seth', 'Patience', NULL, NULL, NULL, '2025-10-13 14:07:16', NULL),
(3, 'psmanguluti', 'admin@teampay.com', '$2y$10$ibjeulZJDCn.MQCM/PeTzubASwlWoxbTHH66jDopJGc9ImF5/wtvK', 'client', 1, '2025-10-13 14:07:16', NULL, NULL, NULL, NULL, NULL, '2025-10-13 14:07:16', NULL),
(4, 'seth', 'patmanseth@gmail.com', '$2y$10$n.F.7y1xPxakPku97NGHnOf/Q.CN0Tkl7Ce8bWAjBnUu9JxSqXfXG', 'veterinary', 1, '2025-10-13 14:07:16', 'Wanangwa', 'Manguluti', '0882279994', 'Area 18A', NULL, '2025-10-13 14:07:16', NULL),
(5, 'wanagwa', 'sethpatiencemanguluti@outloo6k.com', '$2y$10$iEkcAfGO61u.6jhjaOCdceGSteysvSzw..U0lvX.cvlgKPpe3yUaK', 'veterinary', 1, '2025-10-13 14:07:16', NULL, NULL, NULL, NULL, NULL, '2025-10-13 14:07:16', NULL),
(6, 'Steve Biko', 'sb@vetelinary.com', '$2y$10$dkzY8/3w1B0E2/l4rRWSEOTttK97u.IxOmOTEEt0v/EttG5zaS6g2', 'veterinary', 1, '2025-10-13 14:42:47', NULL, NULL, NULL, NULL, NULL, '2025-10-18 13:57:46', '2025-10-18 15:57:46'),
(7, 'client q', 'client@outlook.com', '$2y$10$hza6ztQuQ9pgHMv1IbMb0.ygAjoUSXGWND3XRRx7rgiT7RM3tDb9G', 'client', 1, '2025-10-14 19:26:11', 'Seko', 'Mwalwen', '+265 882 279 996', 'Area 18A', NULL, '2025-10-17 08:08:06', '2025-10-17 10:08:06'),
(8, 'smanguluti', 'smanguluti@vims.com', '$2y$10$BXCn05AbIy2xIyUL/wylh.pTJdGSgXWLLxP.AQ8KrMp9wYpsEcVf2', 'client', 1, '2025-10-15 13:01:45', 'Sam', 'Manguluti', '+265992920181', 'Private Bag B411 Lilongwe 3', NULL, '2025-10-16 14:15:08', '2025-10-16 16:15:08'),
(9, 'Q banz', 'q@vims.com', '$2y$10$hko2YQy5bBMtgPe/mFwcweiQPY545bjGk3g7mFf8g8vkNSoM11rpi', 'client', 1, '2025-10-16 16:28:30', 'qbanz', 'Manguluti', '+265992920181', 'Private Bag B411 Lilongwe 3', NULL, '2025-10-16 16:29:14', '2025-10-16 18:29:14'),
(10, 'PSimama', 'sp@outlook.com', '$2y$10$F/01RfTz9AbkrVq/xDqkXuX04BodXLjemNY2phE0hIoO.T0MGEzIS', 'client', 1, '2025-10-18 13:59:56', 'Simama', 'Priscila', '+265882279994', 'Area 18A', NULL, '2025-10-24 06:45:54', '2025-10-24 08:45:54');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `after_user_client_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    IF NEW.role = 'client' THEN
        INSERT INTO clients (user_id) VALUES (NEW.user_id);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_user_login` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    IF NEW.last_login != OLD.last_login THEN
        INSERT INTO system_logs (level, message, user_id, context)
        VALUES (
            'INFO',
            CONCAT('User login: ', COALESCE(CONCAT(NEW.first_name, ' ', NEW.last_name), NEW.username), ' (', NEW.email, ')'),
            NEW.user_id,
            JSON_OBJECT('last_login', NEW.last_login)
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'admin', 'System administrator with full access'),
(2, 'veterinary', 'Veterinary staff with medical access'),
(3, 'client', 'Client access to own animals and appointments');

-- --------------------------------------------------------

--
-- Table structure for table `vaccines`
--

CREATE TABLE `vaccines` (
  `vaccine_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `vaccine_name` varchar(100) NOT NULL,
  `vaccine_type` varchar(50) DEFAULT NULL,
  `vaccine_date` date NOT NULL,
  `next_due_date` date DEFAULT NULL,
  `administered_by` int(11) NOT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('scheduled','completed','overdue') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `vaccines`
--

INSERT INTO `vaccines` (`vaccine_id`, `animal_id`, `vaccine_name`, `vaccine_type`, `vaccine_date`, `next_due_date`, `administered_by`, `batch_number`, `manufacturer`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Rabies Vaccine', 'Rabies', '2024-01-15', '2025-01-15', 4, 'RB2024A1', 'VetPharm', NULL, 'completed', '2025-10-13 14:07:25', '2025-10-13 14:07:25'),
(2, 1, 'DHPP Vaccine', 'Core', '2024-01-15', '2025-01-15', 4, 'DH2024B2', 'AnimalHealth', NULL, 'completed', '2025-10-13 14:07:25', '2025-10-13 14:07:25'),
(3, 2, 'FVRCP Vaccine', 'Core', '2024-01-10', '2025-01-10', 5, 'FV2024C3', 'CatCare', NULL, 'completed', '2025-10-13 14:07:25', '2025-10-13 14:07:25');

--
-- Triggers `vaccines`
--
DELIMITER $$
CREATE TRIGGER `after_vaccine_insert` AFTER INSERT ON `vaccines` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_vaccine_insert` BEFORE INSERT ON `vaccines` FOR EACH ROW BEGIN
    IF NEW.next_due_date IS NOT NULL AND NEW.next_due_date < CURDATE() AND NEW.status = 'scheduled' THEN
        SET NEW.status = 'overdue';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_vaccine_update` BEFORE UPDATE ON `vaccines` FOR EACH ROW BEGIN
    IF NEW.next_due_date IS NOT NULL AND NEW.next_due_date < CURDATE() AND NEW.status = 'scheduled' THEN
        SET NEW.status = 'overdue';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `vaccine_details_view`
-- (See below for the actual view)
--
CREATE TABLE `vaccine_details_view` (
`vaccine_id` int(11)
,`animal_id` int(11)
,`vaccine_name` varchar(100)
,`vaccine_type` varchar(50)
,`vaccine_date` date
,`next_due_date` date
,`administered_by` int(11)
,`batch_number` varchar(100)
,`manufacturer` varchar(100)
,`vaccine_notes` text
,`vaccine_status` enum('scheduled','completed','overdue')
,`vaccine_created_at` timestamp
,`vaccine_updated_at` timestamp
,`administered_by_first_name` varchar(50)
,`administered_by_last_name` varchar(50)
,`administered_by_name` varchar(101)
,`animal_name` varchar(100)
,`species` varchar(50)
,`days_until_due` int(7)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `veterinary_assignments_view`
-- (See below for the actual view)
--
CREATE TABLE `veterinary_assignments_view` (
`animal_id` int(11)
,`animal_name` varchar(100)
,`species` varchar(50)
,`breed` varchar(100)
,`animal_status` enum('active','inactive')
,`client_first_name` varchar(50)
,`client_last_name` varchar(50)
,`client_full_name` varchar(101)
,`client_phone` varchar(20)
,`assigned_veterinary` int(11)
,`vet_first_name` varchar(50)
,`vet_last_name` varchar(50)
,`vet_full_name` varchar(101)
,`vet_email` varchar(100)
,`assignment_status` varchar(10)
);

-- --------------------------------------------------------

--
-- Structure for view `animal_client_view`
--
DROP TABLE IF EXISTS `animal_client_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `animal_client_view`  AS SELECT `a`.`animal_id` AS `animal_id`, `a`.`client_id` AS `client_id`, `a`.`assigned_veterinary` AS `assigned_veterinary`, `a`.`name` AS `animal_name`, `a`.`species` AS `species`, `a`.`breed` AS `breed`, `a`.`gender` AS `gender`, `a`.`birth_date` AS `birth_date`, `a`.`color` AS `color`, `a`.`weight` AS `weight`, `a`.`microchip` AS `microchip`, `a`.`status` AS `animal_status`, `a`.`notes` AS `animal_notes`, `a`.`created_at` AS `animal_created_at`, `a`.`updated_at` AS `animal_updated_at`, `u`.`user_id` AS `client_user_id`, `u`.`first_name` AS `client_first_name`, `u`.`last_name` AS `client_last_name`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `client_full_name`, `u`.`email` AS `client_email`, `u`.`phone` AS `client_phone`, `u`.`address` AS `client_address`, `vet`.`first_name` AS `vet_first_name`, `vet`.`last_name` AS `vet_last_name`, concat(`vet`.`first_name`,' ',`vet`.`last_name`) AS `vet_full_name`, `vet`.`email` AS `vet_email`, timestampdiff(YEAR,`a`.`birth_date`,curdate()) AS `age_years`, timestampdiff(MONTH,`a`.`birth_date`,curdate()) AS `age_months` FROM (((`animals` `a` join `clients` `c` on(`a`.`client_id` = `c`.`client_id`)) join `users` `u` on(`c`.`user_id` = `u`.`user_id`)) left join `users` `vet` on(`a`.`assigned_veterinary` = `vet`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `animal_details`
--
DROP TABLE IF EXISTS `animal_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `animal_details`  AS SELECT `a`.`animal_id` AS `animal_id`, `a`.`name` AS `animal_name`, `a`.`species` AS `species`, `a`.`breed` AS `breed`, `a`.`gender` AS `gender`, `a`.`birth_date` AS `birth_date`, `a`.`color` AS `color`, `a`.`weight` AS `weight`, `a`.`microchip` AS `microchip`, `a`.`status` AS `animal_status`, `c`.`client_id` AS `client_id`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `client_name`, `u`.`phone` AS `client_phone`, `u`.`email` AS `client_email`, `u`.`address` AS `client_address`, timestampdiff(YEAR,`a`.`birth_date`,curdate()) AS `age_years`, timestampdiff(MONTH,`a`.`birth_date`,curdate()) AS `age_months` FROM ((`animals` `a` join `clients` `c` on(`a`.`client_id` = `c`.`client_id`)) join `users` `u` on(`c`.`user_id` = `u`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `client_details_view`
--
DROP TABLE IF EXISTS `client_details_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `client_details_view`  AS SELECT `c`.`client_id` AS `client_id`, `c`.`emergency_contact` AS `emergency_contact`, `c`.`preferred_contact_method` AS `preferred_contact_method`, `c`.`notes` AS `client_notes`, `c`.`created_at` AS `client_created_at`, `c`.`updated_at` AS `client_updated_at`, `u`.`user_id` AS `user_id`, `u`.`username` AS `username`, `u`.`email` AS `email`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `full_name`, `u`.`phone` AS `phone`, `u`.`address` AS `address`, `u`.`profile_picture` AS `profile_picture`, `u`.`is_active` AS `is_active`, `u`.`last_login` AS `last_login`, (select count(0) from `animals` where `animals`.`client_id` = `c`.`client_id` and `animals`.`status` = 'active') AS `active_animals_count`, (select count(0) from `animals` where `animals`.`client_id` = `c`.`client_id`) AS `total_animals_count` FROM (`clients` `c` join `users` `u` on(`c`.`user_id` = `u`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `treatment_details`
--
DROP TABLE IF EXISTS `treatment_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `treatment_details`  AS SELECT `t`.`treatment_id` AS `treatment_id`, `t`.`animal_id` AS `animal_id`, `a`.`name` AS `animal_name`, `a`.`species` AS `species`, concat(`u_client`.`first_name`,' ',`u_client`.`last_name`) AS `client_name`, concat(`u_vet`.`first_name`,' ',`u_vet`.`last_name`) AS `veterinary_name`, `t`.`diagnosis` AS `diagnosis`, `t`.`treatment_details` AS `treatment_details`, `t`.`treatment_date` AS `treatment_date`, `t`.`follow_up_date` AS `follow_up_date`, `t`.`status` AS `status`, `t`.`cost` AS `cost`, to_days(curdate()) - to_days(`t`.`treatment_date`) AS `days_since_treatment` FROM ((((`treatments` `t` join `animals` `a` on(`t`.`animal_id` = `a`.`animal_id`)) join `clients` `c` on(`a`.`client_id` = `c`.`client_id`)) join `users` `u_client` on(`c`.`user_id` = `u_client`.`user_id`)) join `users` `u_vet` on(`t`.`veterinary_id` = `u_vet`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `treatment_details_view`
--
DROP TABLE IF EXISTS `treatment_details_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `treatment_details_view`  AS SELECT `t`.`treatment_id` AS `treatment_id`, `t`.`animal_id` AS `animal_id`, `t`.`veterinary_id` AS `veterinary_id`, `t`.`diagnosis` AS `diagnosis`, `t`.`treatment_details` AS `treatment_details`, `t`.`medication_prescribed` AS `medication_prescribed`, `t`.`treatment_date` AS `treatment_date`, `t`.`follow_up_date` AS `follow_up_date`, `t`.`status` AS `treatment_status`, `t`.`notes` AS `treatment_notes`, `t`.`cost` AS `cost`, `t`.`created_at` AS `treatment_created_at`, `t`.`updated_at` AS `treatment_updated_at`, `a`.`name` AS `animal_name`, `a`.`species` AS `species`, `u_vet`.`first_name` AS `vet_first_name`, `u_vet`.`last_name` AS `vet_last_name`, concat(`u_vet`.`first_name`,' ',`u_vet`.`last_name`) AS `vet_full_name`, `u_vet`.`email` AS `vet_email`, `u_client`.`first_name` AS `client_first_name`, `u_client`.`last_name` AS `client_last_name`, concat(`u_client`.`first_name`,' ',`u_client`.`last_name`) AS `client_full_name`, to_days(curdate()) - to_days(`t`.`treatment_date`) AS `days_since_treatment`, CASE WHEN `t`.`follow_up_date` is null THEN 'none' WHEN `t`.`follow_up_date` > curdate() THEN 'pending' ELSE 'overdue' END AS `follow_up_status` FROM ((((`treatments` `t` join `animals` `a` on(`t`.`animal_id` = `a`.`animal_id`)) join `users` `u_vet` on(`t`.`veterinary_id` = `u_vet`.`user_id`)) join `clients` `c` on(`a`.`client_id` = `c`.`client_id`)) join `users` `u_client` on(`c`.`user_id` = `u_client`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vaccine_details_view`
--
DROP TABLE IF EXISTS `vaccine_details_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vaccine_details_view`  AS SELECT `v`.`vaccine_id` AS `vaccine_id`, `v`.`animal_id` AS `animal_id`, `v`.`vaccine_name` AS `vaccine_name`, `v`.`vaccine_type` AS `vaccine_type`, `v`.`vaccine_date` AS `vaccine_date`, `v`.`next_due_date` AS `next_due_date`, `v`.`administered_by` AS `administered_by`, `v`.`batch_number` AS `batch_number`, `v`.`manufacturer` AS `manufacturer`, `v`.`notes` AS `vaccine_notes`, `v`.`status` AS `vaccine_status`, `v`.`created_at` AS `vaccine_created_at`, `v`.`updated_at` AS `vaccine_updated_at`, `u`.`first_name` AS `administered_by_first_name`, `u`.`last_name` AS `administered_by_last_name`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `administered_by_name`, `a`.`name` AS `animal_name`, `a`.`species` AS `species`, to_days(`v`.`next_due_date`) - to_days(curdate()) AS `days_until_due` FROM ((`vaccines` `v` join `users` `u` on(`v`.`administered_by` = `u`.`user_id`)) join `animals` `a` on(`v`.`animal_id` = `a`.`animal_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `veterinary_assignments_view`
--
DROP TABLE IF EXISTS `veterinary_assignments_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `veterinary_assignments_view`  AS SELECT `a`.`animal_id` AS `animal_id`, `a`.`name` AS `animal_name`, `a`.`species` AS `species`, `a`.`breed` AS `breed`, `a`.`status` AS `animal_status`, `u_client`.`first_name` AS `client_first_name`, `u_client`.`last_name` AS `client_last_name`, concat(`u_client`.`first_name`,' ',`u_client`.`last_name`) AS `client_full_name`, `u_client`.`phone` AS `client_phone`, `a`.`assigned_veterinary` AS `assigned_veterinary`, `u_vet`.`first_name` AS `vet_first_name`, `u_vet`.`last_name` AS `vet_last_name`, concat(`u_vet`.`first_name`,' ',`u_vet`.`last_name`) AS `vet_full_name`, `u_vet`.`email` AS `vet_email`, CASE WHEN `a`.`assigned_veterinary` is null THEN 'unassigned' ELSE 'assigned' END AS `assignment_status` FROM (((`animals` `a` join `clients` `c` on(`a`.`client_id` = `c`.`client_id`)) join `users` `u_client` on(`c`.`user_id` = `u_client`.`user_id`)) left join `users` `u_vet` on(`a`.`assigned_veterinary` = `u_vet`.`user_id`)) WHERE `a`.`status` = 'active' ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `animals`
--
ALTER TABLE `animals`
  ADD PRIMARY KEY (`animal_id`),
  ADD UNIQUE KEY `microchip` (`microchip`),
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `idx_species` (`species`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_microchip` (`microchip`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `assigned_veterinary` (`assigned_veterinary`);

--
-- Indexes for table `animal_assignments_history`
--
ALTER TABLE `animal_assignments_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `assigned_by` (`assigned_by`),
  ADD KEY `idx_animal_id` (`animal_id`),
  ADD KEY `idx_veterinary_id` (`veterinary_id`),
  ADD KEY `idx_assigned_at` (`assigned_at`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `idx_table_record` (`table_name`,`record_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `billings`
--
ALTER TABLE `billings`
  ADD PRIMARY KEY (`billing_id`),
  ADD KEY `treatment_id` (`treatment_id`),
  ADD KEY `idx_animal_id` (`animal_id`),
  ADD KEY `idx_billing_date` (`billing_date`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `idx_animal_id` (`animal_id`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_reminder_type` (`reminder_type`),
  ADD KEY `idx_assigned_to` (`assigned_to`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_level` (`level`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `treatments`
--
ALTER TABLE `treatments`
  ADD PRIMARY KEY (`treatment_id`),
  ADD KEY `idx_animal_id` (`animal_id`),
  ADD KEY `idx_veterinary_id` (`veterinary_id`),
  ADD KEY `idx_treatment_date` (`treatment_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_follow_up_date` (`follow_up_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `vaccines`
--
ALTER TABLE `vaccines`
  ADD PRIMARY KEY (`vaccine_id`),
  ADD KEY `administered_by` (`administered_by`),
  ADD KEY `idx_animal_id` (`animal_id`),
  ADD KEY `idx_vaccine_date` (`vaccine_date`),
  ADD KEY `idx_next_due_date` (`next_due_date`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `animals`
--
ALTER TABLE `animals`
  MODIFY `animal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `animal_assignments_history`
--
ALTER TABLE `animal_assignments_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billings`
--
ALTER TABLE `billings`
  MODIFY `billing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `treatments`
--
ALTER TABLE `treatments`
  MODIFY `treatment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vaccines`
--
ALTER TABLE `vaccines`
  MODIFY `vaccine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `animals`
--
ALTER TABLE `animals`
  ADD CONSTRAINT `animals_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `animals_ibfk_2` FOREIGN KEY (`assigned_veterinary`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `animal_assignments_history`
--
ALTER TABLE `animal_assignments_history`
  ADD CONSTRAINT `animal_assignments_history_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `animal_assignments_history_ibfk_2` FOREIGN KEY (`veterinary_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `animal_assignments_history_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `billings`
--
ALTER TABLE `billings`
  ADD CONSTRAINT `billings_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `billings_ibfk_2` FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`treatment_id`) ON DELETE SET NULL;

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reminders_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `treatments`
--
ALTER TABLE `treatments`
  ADD CONSTRAINT `treatments_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `treatments_ibfk_2` FOREIGN KEY (`veterinary_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `vaccines`
--
ALTER TABLE `vaccines`
  ADD CONSTRAINT `vaccines_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vaccines_ibfk_2` FOREIGN KEY (`administered_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
