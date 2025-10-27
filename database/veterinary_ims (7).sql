-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 27, 2025 at 10:19 PM
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
(6, 6, 4, 'Zigge', 'horse', 'Brazil', 'male', '2025-01-01', 'Black and White', 50.00, 'WEEF5567G', 'active', 'Nope', '2025-10-18 14:01:39', '2025-10-24 07:54:10'),
(7, 17, 4, 'Simba', 'dog', 'German Shepherd', 'male', '2021-05-10', 'Black/Tan', 38.20, 'MW2024001', 'active', 'Very protective, good with children', '2024-01-25 08:00:00', '2025-10-27 21:16:28'),
(8, 18, 5, 'Bella', 'dog', 'Labrador Mix', 'female', '2022-02-15', 'Golden Brown', 29.80, 'MW2024002', 'active', 'Energetic and playful', '2024-02-10 09:30:00', '2025-10-27 21:16:28'),
(9, 19, 6, 'Rocky', 'dog', 'Rottweiler', 'male', '2020-11-20', 'Black/Brown', 45.50, 'MW2024003', 'active', 'Well-trained guard dog', '2024-02-28 12:15:00', '2025-10-27 21:16:28'),
(10, 20, 4, 'Luna', 'dog', 'Mixed Breed', 'female', '2021-08-05', 'Brown/White', 23.40, 'MW2024004', 'active', 'Rescued from streets', '2024-03-05 07:45:00', '2025-10-27 21:16:28'),
(11, 21, 5, 'Charlie', 'dog', 'Terrier Mix', 'male', '2023-01-12', 'White/Brown', 12.30, 'MW2024005', 'active', 'Young and curious', '2024-03-20 14:20:00', '2025-10-27 21:16:28'),
(12, 22, 6, 'Whiskers', 'cat', 'Domestic Shorthair', 'male', '2022-04-18', 'Orange Tabby', 4.50, 'MW2024006', 'active', 'Indoor cat, very calm', '2024-04-02 11:10:00', '2025-10-27 21:16:28'),
(13, 23, 4, 'Misty', 'cat', 'Persian Mix', 'female', '2021-12-03', 'White/Grey', 3.80, 'MW2024007', 'active', 'Requires regular grooming', '2024-04-15 08:30:00', '2025-10-27 21:16:28'),
(14, 24, 5, 'Shadow', 'cat', 'Black Domestic', 'male', '2020-09-25', 'Black', 5.20, 'MW2024008', 'active', 'Outdoor hunter', '2024-05-01 09:45:00', '2025-10-27 21:16:28'),
(15, 25, 6, 'Princess', 'cat', 'Calico', 'female', '2023-03-08', 'Tri-color', 3.50, 'MW2024009', 'active', 'Very affectionate', '2024-05-12 13:20:00', '2025-10-27 21:16:28'),
(16, 26, 4, 'Coco', 'bird', 'African Grey Parrot', 'female', '2019-07-14', 'Grey/Red', 0.45, 'MW2024010', 'active', 'Can speak several words', '2024-05-20 06:15:00', '2025-10-27 21:16:28'),
(17, 17, 5, 'Fluffy', 'rabbit', 'Flemish Giant', 'male', '2023-02-28', 'Brown', 6.50, 'MW2024011', 'active', 'Very large breed', '2024-06-05 10:40:00', '2025-10-27 21:16:28'),
(18, 18, 6, 'Rex', 'dog', 'Bulldog Mix', 'male', '2022-11-11', 'Brindle', 28.00, 'MW2024012', 'active', 'Breathing monitored', '2024-06-18 12:55:00', '2025-10-27 21:16:28'),
(19, 19, 4, 'Daisy', 'dog', 'Beagle', 'female', '2023-05-22', 'Tri-color', 11.50, 'MW2024013', 'active', 'Excellent nose', '2024-07-01 08:10:00', '2025-10-27 21:16:28'),
(20, 20, 5, 'Max', 'dog', 'Boxer', 'male', '2022-06-30', 'Fawn', 32.00, 'MW2024014', 'active', 'Athletic and strong', '2024-07-15 14:30:00', '2025-10-27 21:16:28'),
(21, 21, 6, 'Molly', 'dog', 'Cocker Spaniel', 'female', '2021-04-12', 'Golden', 15.20, 'MW2024015', 'active', 'Loves water', '2024-08-02 07:25:00', '2025-10-27 21:16:28');

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
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `veterinary_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `duration` int(11) DEFAULT 30 COMMENT 'Duration in minutes',
  `appointment_type` enum('consultation','vaccination','surgery','checkup','emergency','grooming') DEFAULT 'consultation',
  `reason` text NOT NULL,
  `status` enum('scheduled','confirmed','in_progress','completed','cancelled','no_show') DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `reminder_sent` tinyint(1) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `animal_id`, `client_id`, `veterinary_id`, `appointment_date`, `appointment_time`, `duration`, `appointment_type`, `reason`, `status`, `notes`, `reminder_sent`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 4, '2025-10-28', '10:00:00', 30, 'consultation', 'Routine checkup', 'scheduled', NULL, 0, 1, '2025-10-27 08:54:42', '2025-10-27 08:54:42'),
(2, 2, 1, 4, '2025-10-28', '11:00:00', 45, 'vaccination', 'Annual vaccination', 'scheduled', NULL, 0, 1, '2025-10-27 08:54:42', '2025-10-27 08:54:42'),
(3, 3, 2, 5, '2025-10-29', '14:30:00', 60, 'grooming', 'Full grooming service', 'confirmed', NULL, 0, 1, '2025-10-27 08:54:42', '2025-10-27 08:54:42'),
(4, 6, 3, 4, '2025-10-31', '11:00:00', 30, 'consultation', 'Animal check up', 'scheduled', 'it looks strange', 0, 1, '2025-10-27 08:56:41', '2025-10-27 08:56:41'),
(5, 1, 1, 4, '2024-09-15', '09:00:00', 30, 'consultation', 'Annual checkup', 'completed', 'All vitals normal', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(6, 2, 1, 5, '2024-09-20', '10:30:00', 45, 'vaccination', 'Rabies booster', 'completed', 'No adverse reactions', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(7, 3, 2, 6, '2024-09-25', '14:00:00', 60, '', 'Teeth cleaning', 'completed', 'Minor tartar removed', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(8, 4, 2, 4, '2024-10-01', '11:00:00', 30, 'consultation', 'Skin irritation check', 'completed', 'Prescribed ointment', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(9, 5, 3, 5, '2024-10-05', '15:30:00', 45, 'vaccination', 'Annual vaccines', 'completed', 'All up to date', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(10, 6, 3, 6, '2024-10-23', '09:30:00', 30, 'consultation', 'Weight check', 'completed', 'Slight weight gain', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(11, 7, 1, 4, '2024-10-24', '13:00:00', 45, 'grooming', 'Full grooming service', 'completed', 'Coat in good condition', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(12, 8, 2, 5, '2024-10-25', '10:00:00', 30, 'consultation', 'Follow-up checkup', 'completed', 'Recovery progressing well', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(13, 9, 2, 6, '2024-10-28', '16:00:00', 30, 'consultation', 'Routine wellness check', 'scheduled', 'First appointment', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(14, 10, 3, 4, '2024-10-29', '09:30:00', 60, 'surgery', 'Spaying procedure', 'confirmed', 'Pre-surgery tests completed', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(15, 11, 1, 5, '2024-10-30', '14:30:00', 45, 'vaccination', 'Puppy shots series', 'scheduled', 'Second round of vaccines', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(16, 12, 2, 6, '2024-10-31', '11:00:00', 30, 'consultation', 'Behavioral consultation', 'confirmed', 'Excessive barking concerns', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(17, 13, 3, 4, '2024-11-01', '15:00:00', 30, 'consultation', 'Nutrition consultation', 'scheduled', 'Diet planning needed', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(18, 14, 1, 5, '2024-11-04', '10:30:00', 60, 'grooming', 'Nail trim and bath', 'scheduled', 'Regular grooming', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(19, 15, 2, 6, '2024-11-05', '13:30:00', 45, 'vaccination', 'Rabies vaccine', 'scheduled', 'Annual booster due', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(20, 16, 3, 4, '2024-11-06', '09:00:00', 30, 'checkup', 'Post-surgery follow-up', 'scheduled', '2 weeks post-op', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(21, 17, 1, 5, '2024-11-08', '14:00:00', 30, 'consultation', 'Senior wellness exam', 'scheduled', 'Geriatric screening', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(22, 18, 2, 6, '2024-11-11', '16:30:00', 45, 'emergency', 'Limping investigation', 'scheduled', 'Possible injury', 0, 1, '2025-10-27 21:16:28', '2025-10-27 21:16:28'),
(23, 1, 1, 4, '2024-11-12', '09:00:00', 30, 'consultation', 'Routine checkup', 'scheduled', NULL, 0, 1, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(24, 2, 1, 5, '2024-11-13', '10:30:00', 45, 'consultation', 'Allergy follow-up', 'scheduled', NULL, 0, 1, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(25, 3, 2, 6, '2024-11-14', '14:00:00', 30, 'checkup', 'Post-surgery final check', 'scheduled', NULL, 0, 1, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(26, 4, 2, 4, '2024-11-15', '11:00:00', 60, '', 'Teeth cleaning', 'scheduled', NULL, 0, 1, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(27, 5, 3, 5, '2024-11-18', '15:30:00', 30, 'consultation', 'General wellness', 'scheduled', NULL, 0, 1, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(28, 6, 6, 6, '2024-11-19', '09:30:00', 45, 'grooming', 'Full grooming service', 'scheduled', NULL, 0, 1, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(29, 7, 17, 4, '2024-11-20', '13:00:00', 30, 'consultation', 'Skin check', 'scheduled', NULL, 0, 1, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(30, 8, 18, 5, '2024-11-21', '10:00:00', 45, 'vaccination', 'Annual booster', 'scheduled', NULL, 0, 1, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(31, 9, 19, 6, '2024-11-22', '16:00:00', 30, 'consultation', 'Weight management check', 'scheduled', NULL, 0, 1, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(32, 10, 20, 4, '2024-11-25', '09:30:00', 30, 'checkup', 'Senior wellness exam', 'scheduled', NULL, 0, 1, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(33, 11, 21, 5, '2024-11-26', '14:30:00', 45, 'consultation', 'Digestive health check', 'scheduled', NULL, 0, 1, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(34, 12, 22, 6, '2024-11-27', '11:00:00', 30, 'consultation', 'Behavioral follow-up', 'scheduled', NULL, 0, 1, '2025-10-27 21:16:32', '2025-10-27 21:16:32');

-- --------------------------------------------------------

--
-- Stand-in structure for view `appointment_details_view`
-- (See below for the actual view)
--
CREATE TABLE `appointment_details_view` (
`appointment_id` int(11)
,`animal_id` int(11)
,`client_id` int(11)
,`veterinary_id` int(11)
,`appointment_date` date
,`appointment_time` time
,`duration` int(11)
,`appointment_type` enum('consultation','vaccination','surgery','checkup','emergency','grooming')
,`reason` text
,`status` enum('scheduled','confirmed','in_progress','completed','cancelled','no_show')
,`notes` text
,`reminder_sent` tinyint(1)
,`created_by` int(11)
,`created_at` timestamp
,`updated_at` timestamp
,`animal_name` varchar(100)
,`species` varchar(50)
,`breed` varchar(100)
,`gender` enum('male','female','unknown')
,`color` varchar(100)
,`client_first_name` varchar(50)
,`client_last_name` varchar(50)
,`client_full_name` varchar(101)
,`client_phone` varchar(20)
,`client_email` varchar(100)
,`vet_first_name` varchar(50)
,`vet_last_name` varchar(50)
,`vet_full_name` varchar(101)
,`vet_email` varchar(100)
,`vet_phone` varchar(20)
,`created_by_first_name` varchar(50)
,`created_by_last_name` varchar(50)
,`created_by_full_name` varchar(101)
,`formatted_date` varchar(139)
,`formatted_time` varchar(8)
,`end_time` time
,`formatted_end_time` varchar(8)
,`status_text` varchar(11)
,`priority` varchar(6)
,`days_until_appointment` int(7)
,`is_today` int(1)
,`is_upcoming` int(1)
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
(4, 3, 4, '2024-03-01', '2024-03-31', 85.00, 6.80, 0.00, 91.80, 'pending', NULL, NULL, NULL, NULL, '2025-10-13 14:07:26', '2025-10-13 14:07:26'),
(5, 1, 5, '2024-09-15', '2024-10-15', 12500.00, 0.00, 0.00, 12500.00, 'paid', 'mobile_money', '2024-09-15', NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(6, 2, 6, '2024-09-20', '2024-10-20', 8500.00, 0.00, 0.00, 8500.00, 'paid', 'cash', '2024-09-20', NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(7, 3, 7, '2024-09-25', '2024-10-25', 25000.00, 0.00, 2500.00, 22500.00, 'paid', 'mobile_money', '2024-09-25', NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(8, 4, 8, '2024-10-01', '2024-10-31', 15000.00, 0.00, 0.00, 15000.00, 'paid', 'bank_transfer', '2024-10-05', NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(9, 5, 9, '2024-10-05', '2024-11-04', 10500.00, 0.00, 0.00, 10500.00, 'paid', 'mobile_money', '2024-10-05', NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(10, 6, 10, '2024-10-23', '2024-11-22', 8000.00, 0.00, 0.00, 8000.00, 'paid', 'cash', '2024-10-23', NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(11, 7, 11, '2024-10-24', '2024-11-23', 6500.00, 0.00, 0.00, 6500.00, 'paid', 'mobile_money', '2024-10-24', NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(12, 8, 12, '2024-10-25', '2024-11-24', 5000.00, 0.00, 0.00, 5000.00, 'paid', 'cash', '2024-10-25', NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(13, 9, 13, '2024-10-20', '2024-11-19', 18000.00, 0.00, 0.00, 18000.00, 'pending', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(14, 10, 14, '2024-10-22', '2024-11-21', 12000.00, 0.00, 0.00, 12000.00, 'pending', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(15, 11, 15, '2024-10-10', '2024-11-09', 16500.00, 0.00, 1500.00, 15000.00, 'paid', 'mobile_money', '2024-10-10', NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(16, 12, 16, '2024-10-12', '2024-11-11', 22000.00, 0.00, 0.00, 22000.00, 'paid', 'bank_transfer', '2024-10-15', NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(17, 13, 17, '2024-10-15', '2024-11-14', 9500.00, 0.00, 0.00, 9500.00, 'paid', 'cash', '2024-10-15', NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(18, 14, 18, '2024-10-18', '2024-11-17', 28000.00, 0.00, 3000.00, 25000.00, 'pending', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(19, 15, 19, '2024-10-21', '2024-11-20', 11000.00, 0.00, 0.00, 11000.00, 'paid', 'mobile_money', '2024-10-21', NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(20, 1, 20, '2024-09-28', '2024-10-28', 7500.00, 0.00, 0.00, 7500.00, 'paid', 'cash', '2024-09-28', NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(21, 2, 21, '2024-10-03', '2024-11-02', 35000.00, 0.00, 5000.00, 30000.00, 'pending', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(22, 3, 22, '2024-10-08', '2024-11-07', 45000.00, 0.00, 0.00, 45000.00, 'paid', 'bank_transfer', '2024-10-08', NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(23, 4, 23, '2024-10-14', '2024-11-13', 32000.00, 0.00, 2000.00, 30000.00, 'paid', 'mobile_money', '2024-10-14', NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(24, 5, 24, '2024-10-16', '2024-11-15', 8500.00, 0.00, 0.00, 8500.00, 'paid', 'cash', '2024-10-16', NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32');

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
(6, 10, NULL, 'phone', NULL, '2025-10-18 13:59:56', '2025-10-18 13:59:56'),
(17, 35, '+265 881 234 567', 'phone', 'Prefers morning appointments', '2025-10-27 21:16:26', '2025-10-27 21:16:27'),
(18, 36, '+265 882 345 678', 'sms', 'Works full-time, evening appointments preferred', '2025-10-27 21:16:26', '2025-10-27 21:16:27'),
(19, 37, '+265 883 456 789', 'email', 'Prefers email communication', '2025-10-27 21:16:26', '2025-10-27 21:16:27'),
(20, 38, '+265 884 567 890', 'phone', 'Has multiple animals, regular customer', '2025-10-27 21:16:26', '2025-10-27 21:16:28'),
(21, 39, '+265 885 678 901', 'sms', 'First-time pet owner', '2025-10-27 21:16:26', '2025-10-27 21:16:28'),
(22, 40, '+265 886 789 012', 'phone', 'Experienced dog breeder', '2025-10-27 21:16:26', '2025-10-27 21:16:28'),
(23, 41, '+265 887 890 123', 'email', 'Travels frequently for work', '2025-10-27 21:16:26', '2025-10-27 21:16:28'),
(24, 42, '+265 888 901 234', 'phone', 'Very attentive pet parent', '2025-10-27 21:16:26', '2025-10-27 21:16:28'),
(25, 43, '+265 889 012 345', 'sms', 'Budget-conscious, asks about costs', '2025-10-27 21:16:26', '2025-10-27 21:16:28'),
(26, 44, '+265 880 123 456', 'phone', 'Rescues stray animals regularly', '2025-10-27 21:16:26', '2025-10-27 21:16:28');

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
-- Stand-in structure for view `client_activity_view`
-- (See below for the actual view)
--
CREATE TABLE `client_activity_view` (
`client_id` int(11)
,`client_name` varchar(101)
,`total_animals` bigint(21)
,`total_treatments` bigint(21)
,`total_spent` decimal(32,2)
,`last_visit` date
);

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
-- Stand-in structure for view `client_treatment_history`
-- (See below for the actual view)
--
CREATE TABLE `client_treatment_history` (
`client_id` int(11)
,`client_name` varchar(101)
,`animal_id` int(11)
,`animal_name` varchar(100)
,`species` varchar(50)
,`total_treatments` bigint(21)
,`completed_treatments` decimal(22,0)
,`ongoing_treatments` decimal(22,0)
,`total_treatment_cost` decimal(32,2)
,`last_treatment_date` date
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
(6, 2, 'vaccination', '2025-10-13', '2025-01-10', 'Vaccination Due: FVRCP Vaccine', 'FVRCP Vaccine vaccination is due for animal ID: 2', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-13 14:07:25', '2025-10-13 14:07:25'),
(7, 2, 'treatment_followup', '2025-10-27', '2025-09-20', 'Treatment Follow-up: Rabies vaccination', 'Follow-up required for treatment: Rabies vaccination', 'pending', 'medium', NULL, NULL, 'treatment', 6, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(8, 4, 'treatment_followup', '2025-10-27', '2024-10-15', 'Treatment Follow-up: Allergic dermatitis', 'Follow-up required for treatment: Allergic dermatitis', 'pending', 'medium', NULL, NULL, 'treatment', 8, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(9, 6, 'treatment_followup', '2025-10-27', '2024-11-23', 'Treatment Follow-up: Weight management', 'Follow-up required for treatment: Weight management', 'pending', 'medium', NULL, NULL, 'treatment', 10, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(10, 9, 'treatment_followup', '2025-10-27', '2024-11-05', 'Treatment Follow-up: Upper respiratory infection', 'Follow-up required for treatment: Upper respiratory infection', 'pending', 'medium', NULL, NULL, 'treatment', 13, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(11, 10, 'treatment_followup', '2025-10-27', '2024-11-06', 'Treatment Follow-up: Ear infection', 'Follow-up required for treatment: Ear infection', 'pending', 'medium', NULL, NULL, 'treatment', 14, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(12, 1, 'vaccination', '2025-10-27', '2025-09-15', 'Vaccination Due: Rabies Vaccine', 'Rabies Vaccine vaccination is due for animal ID: 1', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(13, 2, 'vaccination', '2025-10-27', '2025-09-20', 'Vaccination Due: Rabies Vaccine', 'Rabies Vaccine vaccination is due for animal ID: 2', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(14, 3, 'vaccination', '2025-10-27', '2025-08-10', 'Vaccination Due: Rabies Vaccine', 'Rabies Vaccine vaccination is due for animal ID: 3', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(15, 1, 'vaccination', '2025-10-27', '2025-09-15', 'Vaccination Due: DHPP Vaccine', 'DHPP Vaccine vaccination is due for animal ID: 1', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(16, 2, 'vaccination', '2025-10-27', '2025-09-20', 'Vaccination Due: DHPP Vaccine', 'DHPP Vaccine vaccination is due for animal ID: 2', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(17, 4, 'vaccination', '2025-10-27', '2025-07-15', 'Vaccination Due: DHPP Vaccine', 'DHPP Vaccine vaccination is due for animal ID: 4', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(18, 5, 'vaccination', '2025-10-27', '2025-10-05', 'Vaccination Due: FVRCP Vaccine', 'FVRCP Vaccine vaccination is due for animal ID: 5', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(19, 6, 'vaccination', '2025-10-27', '2025-08-20', 'Vaccination Due: FVRCP Vaccine', 'FVRCP Vaccine vaccination is due for animal ID: 6', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(20, 1, 'vaccination', '2025-10-27', '2025-09-15', 'Vaccination Due: Bordetella Vaccine', 'Bordetella Vaccine vaccination is due for animal ID: 1', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(21, 2, 'vaccination', '2025-10-27', '2025-09-20', 'Vaccination Due: Bordetella Vaccine', 'Bordetella Vaccine vaccination is due for animal ID: 2', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(22, 1, 'vaccination', '2025-08-15', '2025-09-15', 'Rabies Vaccine Due', 'Annual rabies vaccination is due for Simba', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(23, 2, 'vaccination', '2025-08-20', '2025-09-20', 'Rabies Vaccine Due', 'Annual rabies vaccination is due for Bella', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(24, 5, 'vaccination', '2025-09-05', '2025-10-05', 'FVRCP Vaccine Due', 'Annual FVRCP vaccination is due', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(25, 6, 'treatment_followup', '2024-11-15', '2024-11-23', 'Weight Check Follow-up', 'Follow-up weight check for diet monitoring', 'pending', 'medium', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(26, 9, 'treatment_followup', '2024-10-30', '2024-11-05', 'URI Treatment Check', 'Check respiratory infection recovery progress', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(27, 10, 'treatment_followup', '2024-11-01', '2024-11-06', 'Ear Infection Follow-up', 'Verify ear infection has cleared', 'pending', 'medium', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(28, 11, 'appointment', '2024-10-28', '2024-10-30', 'Upcoming Appointment', 'Vaccination appointment scheduled', 'pending', 'medium', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(29, 12, 'appointment', '2024-10-29', '2024-10-31', 'Upcoming Appointment', 'Behavioral consultation scheduled', 'pending', 'medium', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(30, 13, 'appointment', '2024-10-30', '2024-11-01', 'Upcoming Appointment', 'Nutrition consultation scheduled', 'pending', 'low', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(31, 9, 'billing', '2024-11-10', '2024-11-19', 'Payment Due', 'Treatment payment due for URI treatment', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(32, 10, 'billing', '2024-11-12', '2024-11-21', 'Payment Due', 'Treatment payment due for ear infection', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(33, 1, 'general', '2024-11-15', '2024-12-01', 'Heartworm Prevention', 'Monthly heartworm prevention medication due', 'pending', 'medium', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(34, 2, 'general', '2024-11-20', '2024-12-05', 'Flea Treatment', 'Monthly flea and tick prevention due', 'pending', 'low', NULL, NULL, NULL, NULL, '2025-10-27 21:16:31', '2025-10-27 21:16:31'),
(35, 11, 'treatment_followup', '2025-10-27', '2024-10-17', 'Treatment Follow-up: Gastrointestinal upset', 'Follow-up required for treatment: Gastrointestinal upset', 'pending', 'medium', NULL, NULL, 'treatment', 15, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(36, 12, 'treatment_followup', '2025-10-27', '2024-10-26', 'Treatment Follow-up: Laceration repair', 'Follow-up required for treatment: Laceration repair', 'pending', 'medium', NULL, NULL, 'treatment', 16, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(37, 13, 'treatment_followup', '2025-10-27', '2024-11-15', 'Treatment Follow-up: Parasite treatment', 'Follow-up required for treatment: Parasite treatment', 'pending', 'medium', NULL, NULL, 'treatment', 17, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(38, 14, 'treatment_followup', '2025-10-27', '2024-11-18', 'Treatment Follow-up: Arthritis management', 'Follow-up required for treatment: Arthritis management', 'pending', 'medium', NULL, NULL, 'treatment', 18, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(39, 15, 'treatment_followup', '2025-10-27', '2024-10-28', 'Treatment Follow-up: Eye infection', 'Follow-up required for treatment: Eye infection', 'pending', 'medium', NULL, NULL, 'treatment', 19, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(40, 2, 'treatment_followup', '2025-10-27', '2024-11-03', 'Treatment Follow-up: Skin allergy test', 'Follow-up required for treatment: Skin allergy test', 'pending', 'medium', NULL, NULL, 'treatment', 21, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(41, 3, 'treatment_followup', '2025-10-27', '2024-10-22', 'Treatment Follow-up: Spay surgery', 'Follow-up required for treatment: Spay surgery', 'pending', 'medium', NULL, NULL, 'treatment', 22, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(42, 4, 'vaccination', '2025-10-27', '2025-07-15', 'Vaccination Due: Rabies Vaccine', 'Rabies Vaccine vaccination is due for animal ID: 4', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(43, 7, 'vaccination', '2025-10-27', '2025-09-10', 'Vaccination Due: FVRCP Vaccine', 'FVRCP Vaccine vaccination is due for animal ID: 7', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(44, 8, 'vaccination', '2025-10-27', '2025-09-12', 'Vaccination Due: FVRCP Vaccine', 'FVRCP Vaccine vaccination is due for animal ID: 8', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(45, 9, 'vaccination', '2025-10-27', '2025-09-18', 'Vaccination Due: Rabies Vaccine', 'Rabies Vaccine vaccination is due for animal ID: 9', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(46, 10, 'vaccination', '2025-10-27', '2025-09-22', 'Vaccination Due: DHPP Vaccine', 'DHPP Vaccine vaccination is due for animal ID: 10', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(47, 11, 'vaccination', '2025-10-27', '2025-09-25', 'Vaccination Due: DHPP Vaccine', 'DHPP Vaccine vaccination is due for animal ID: 11', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(48, 12, 'vaccination', '2025-10-27', '2025-10-01', 'Vaccination Due: Rabies Vaccine', 'Rabies Vaccine vaccination is due for animal ID: 12', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(49, 13, 'vaccination', '2025-10-27', '2025-10-03', 'Vaccination Due: DHPP Vaccine', 'DHPP Vaccine vaccination is due for animal ID: 13', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(50, 14, 'vaccination', '2025-10-27', '2025-10-05', 'Vaccination Due: Bordetella Vaccine', 'Bordetella Vaccine vaccination is due for animal ID: 14', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(51, 15, 'vaccination', '2025-10-27', '2025-10-08', 'Vaccination Due: Leptospirosis Vaccine', 'Leptospirosis Vaccine vaccination is due for animal ID: 15', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(52, 1, 'vaccination', '2025-10-27', '2025-09-15', 'Vaccination Due: Leptospirosis Vaccine', 'Leptospirosis Vaccine vaccination is due for animal ID: 1', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(53, 2, 'vaccination', '2025-10-27', '2025-09-20', 'Vaccination Due: Canine Influenza Vaccine', 'Canine Influenza Vaccine vaccination is due for animal ID: 2', 'pending', 'high', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(54, 3, 'vaccination', '2025-07-10', '2025-08-10', 'Rabies Vaccine Due', 'Annual rabies vaccination due for Rocky', 'pending', 'high', NULL, NULL, 'vaccine', 3, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(55, 4, 'vaccination', '2025-06-15', '2025-07-15', 'DHPP Vaccine Due', 'Annual DHPP vaccination due for Luna', 'pending', 'high', NULL, NULL, 'vaccine', 6, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(56, 7, 'vaccination', '2025-08-10', '2025-09-10', 'FVRCP Vaccine Due', 'Annual FVRCP vaccination due for Misty', 'pending', 'high', NULL, NULL, 'vaccine', NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(57, 11, 'treatment_followup', '2024-11-10', '2024-11-15', 'Parasite Check', 'Follow-up fecal test for parasite treatment', 'pending', 'medium', NULL, NULL, 'treatment', NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(58, 14, 'treatment_followup', '2024-11-13', '2024-11-18', 'Arthritis Check', 'Assess pain management effectiveness', 'pending', 'medium', NULL, NULL, 'treatment', NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(59, 2, 'treatment_followup', '2024-10-28', '2024-11-03', 'Allergy Assessment', 'Review diet trial results', 'pending', 'medium', NULL, NULL, 'treatment', NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(60, 4, 'appointment', '2024-11-12', '2024-11-15', 'Dental Appointment', 'Teeth cleaning scheduled', 'pending', 'medium', NULL, NULL, 'appointment', NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(61, 8, 'appointment', '2024-11-18', '2024-11-21', 'Vaccination Appointment', 'Annual booster due', 'pending', 'high', NULL, NULL, 'appointment', NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(62, 14, 'billing', '2024-11-10', '2024-11-17', 'Payment Reminder', 'Arthritis treatment payment pending', 'pending', 'high', NULL, NULL, 'billing', NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(63, 2, 'billing', '2024-10-25', '2024-11-02', 'Payment Reminder', 'Allergy test payment pending', 'pending', 'high', NULL, NULL, 'billing', NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(64, 11, 'general', '2024-11-25', '2024-12-10', 'Diet Review', 'Schedule diet consultation', 'pending', 'low', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(65, 14, 'general', '2024-11-28', '2024-12-15', 'Joint Supplement', 'Reorder joint supplement medication', 'pending', 'medium', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(66, 3, 'general', '2024-11-05', '2024-11-20', 'Activity Monitor', 'Monitor activity post-surgery', 'pending', 'medium', NULL, NULL, NULL, NULL, '2025-10-27 21:16:32', '2025-10-27 21:16:32');

-- --------------------------------------------------------

--
-- Stand-in structure for view `revenue_analytics_view`
-- (See below for the actual view)
--
CREATE TABLE `revenue_analytics_view` (
`month` varchar(7)
,`total_bills` bigint(21)
,`total_revenue` decimal(32,2)
,`paid_revenue` decimal(32,2)
,`pending_revenue` decimal(32,2)
,`avg_bill_amount` decimal(14,6)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `species_analytics_view`
-- (See below for the actual view)
--
CREATE TABLE `species_analytics_view` (
`species` varchar(50)
,`animal_count` bigint(21)
,`percentage` decimal(26,2)
);

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
(35, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-24 09:51:44\"}', 1, NULL, '2025-10-24 07:51:44'),
(36, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-24 11:28:11\"}', 6, NULL, '2025-10-24 09:28:11'),
(37, 'INFO', 'User login: Seko Mwalwen (client@outlook.com)', '{\"last_login\": \"2025-10-24 11:29:22\"}', 7, NULL, '2025-10-24 09:29:22'),
(38, 'INFO', 'User login: Seko Mwalwen (client@outlook.com)', '{\"last_login\": \"2025-10-24 11:29:36\"}', 7, NULL, '2025-10-24 09:29:36'),
(39, 'INFO', 'User login: Seko Mwalwen (client@outlook.com)', '{\"last_login\": \"2025-10-27 09:29:43\"}', 7, NULL, '2025-10-27 07:29:43'),
(40, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-27 10:14:58\"}', 6, NULL, '2025-10-27 08:14:58'),
(41, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-27 10:21:25\"}', 1, NULL, '2025-10-27 08:21:25'),
(42, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-27 10:57:43\"}', 6, NULL, '2025-10-27 08:57:43'),
(43, 'INFO', 'User login: Seko Mwalwen (client@outlook.com)', '{\"last_login\": \"2025-10-27 10:59:21\"}', 7, NULL, '2025-10-27 08:59:21'),
(44, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-27 11:28:10\"}', 1, NULL, '2025-10-27 09:28:10'),
(45, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-27 11:55:29\"}', 6, NULL, '2025-10-27 09:55:29'),
(46, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-27 12:27:19\"}', 6, NULL, '2025-10-27 10:27:19'),
(47, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-27 12:52:00\"}', 1, NULL, '2025-10-27 10:52:00'),
(48, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-27 16:01:57\"}', 6, NULL, '2025-10-27 14:01:57'),
(49, 'INFO', 'User login: Seko Mwalwen (client@outlook.com)', '{\"last_login\": \"2025-10-27 16:05:48\"}', 7, NULL, '2025-10-27 14:05:48'),
(50, 'INFO', 'User login: Seko Mwalwen (client@outlook.com)', '{\"last_login\": \"2025-10-27 16:17:04\"}', 7, NULL, '2025-10-27 14:17:04'),
(51, 'INFO', 'User login: Steve Biko (sb@vetelinary.com)', '{\"last_login\": \"2025-10-27 16:17:13\"}', 6, NULL, '2025-10-27 14:17:13'),
(52, 'INFO', 'User login: Seko Mwalwen (client@outlook.com)', '{\"last_login\": \"2025-10-27 22:53:27\"}', 7, NULL, '2025-10-27 20:53:27'),
(53, 'INFO', 'User login: Patience Manguluti (admin@vet.com)', '{\"last_login\": \"2025-10-27 22:54:07\"}', 1, NULL, '2025-10-27 20:54:07'),
(54, 'INFO', 'User login: Chisomo Banda (dr.banda@vetclinic.mw)', NULL, 32, '::1', '2024-10-27 06:30:00'),
(55, 'INFO', 'User login: Kondwani Phiri (dr.phiri@vetclinic.mw)', NULL, 33, '::1', '2024-10-27 07:15:00'),
(56, 'INFO', 'New animal registered: Simba', NULL, 1, '::1', '2024-10-27 08:00:00'),
(57, 'INFO', 'Appointment scheduled for 2024-10-30', NULL, 1, '::1', '2024-10-27 08:30:00'),
(58, 'INFO', 'Treatment completed for animal ID: 8', NULL, 33, '::1', '2024-10-25 12:20:00'),
(59, 'INFO', 'Payment received: MWK 12,500.00 - Mobile Money', NULL, 1, '::1', '2024-10-27 09:00:00'),
(60, 'INFO', 'Vaccine administered: Rabies', NULL, 32, '::1', '2024-10-20 11:45:00');

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
(4, 3, 4, 'Dental cleaning', 'Professional dental cleaning. Minor tartar buildup removed.', NULL, '2024-03-01', NULL, 'completed', NULL, 85.00, '2025-10-13 14:07:25', '2025-10-13 14:07:25'),
(5, 1, 4, 'Annual wellness check', 'Complete physical examination, all systems normal', 'Multivitamin supplement', '2024-09-15', NULL, 'completed', NULL, 12500.00, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(6, 2, 5, 'Rabies vaccination', 'Administered rabies vaccine, no adverse reactions', 'Rabies vaccine 1mL', '2024-09-20', '2025-09-20', 'completed', NULL, 8500.00, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(7, 3, 6, 'Dental prophylaxis', 'Professional teeth cleaning, minor tartar removed', 'Dental chews, antibacterial rinse', '2024-09-25', NULL, 'completed', NULL, 25000.00, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(8, 4, 4, 'Allergic dermatitis', 'Skin irritation due to environmental allergies', 'Antihistamine tablets, medicated shampoo', '2024-10-01', '2024-10-15', 'completed', NULL, 15000.00, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(9, 5, 5, 'Vaccination update', 'Complete annual vaccination series', 'DHPP, Bordetella vaccines', '2024-10-05', NULL, 'completed', NULL, 10500.00, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(10, 6, 6, 'Weight management', 'Obesity assessment, diet plan provided', 'Prescription weight control food', '2024-10-23', '2024-11-23', 'ongoing', NULL, 8000.00, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(11, 7, 4, 'Grooming service', 'Full grooming including nail trim and bath', 'Flea prevention treatment', '2024-10-24', NULL, 'completed', NULL, 6500.00, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(12, 8, 5, 'Post-surgical check', 'Follow-up after previous procedure, healing well', 'Continue pain medication', '2024-10-25', NULL, 'completed', NULL, 5000.00, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(13, 9, 6, 'Upper respiratory infection', 'Diagnosed with URI, started antibiotics', 'Amoxicillin 250mg twice daily', '2024-10-20', '2024-11-05', 'ongoing', NULL, 18000.00, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(14, 10, 4, 'Ear infection', 'Bacterial ear infection, cleaning required', 'Ear drops, oral antibiotics', '2024-10-22', '2024-11-06', 'follow_up', NULL, 12000.00, '2025-10-27 21:16:30', '2025-10-27 21:16:30'),
(15, 11, 5, 'Gastrointestinal upset', 'Vomiting and diarrhea, likely dietary indiscretion', 'Metronidazole, probiotic supplement, bland diet', '2024-10-10', '2024-10-17', 'completed', NULL, 16500.00, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(16, 12, 6, 'Laceration repair', 'Minor wound from altercation, cleaned and sutured', 'Antibiotics, pain medication, E-collar', '2024-10-12', '2024-10-26', 'follow_up', NULL, 22000.00, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(17, 13, 4, 'Parasite treatment', 'Intestinal parasites detected in fecal exam', 'Deworming medication, follow-up fecal test', '2024-10-15', '2024-11-15', 'ongoing', NULL, 9500.00, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(18, 14, 5, 'Arthritis management', 'Joint pain in senior dog, started pain management', 'NSAIDs, joint supplement, weight management', '2024-10-18', '2024-11-18', 'ongoing', NULL, 28000.00, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(19, 15, 6, 'Eye infection', 'Conjunctivitis, bacterial origin', 'Antibiotic eye drops, warm compress', '2024-10-21', '2024-10-28', 'completed', NULL, 11000.00, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(20, 1, 4, 'Dental examination', 'Routine dental check, no major issues found', 'Dental chew toys recommended', '2024-09-28', NULL, 'completed', NULL, 7500.00, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(21, 2, 5, 'Skin allergy test', 'Environmental allergy testing performed', 'Antihistamine prescription, diet trial', '2024-10-03', '2024-11-03', 'ongoing', NULL, 35000.00, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(22, 3, 6, 'Spay surgery', 'Routine ovariohysterectomy procedure', 'Pain medication, antibiotics, rest', '2024-10-08', '2024-10-22', 'follow_up', NULL, 45000.00, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(23, 4, 4, 'Blood work panel', 'Annual senior wellness blood screening', 'All results within normal range', '2024-10-14', NULL, 'completed', NULL, 32000.00, '2025-10-27 21:16:32', '2025-10-27 21:16:32'),
(24, 5, 5, 'Microchip insertion', 'Permanent identification microchip implanted', 'No medication required', '2024-10-16', NULL, 'completed', NULL, 8500.00, '2025-10-27 21:16:32', '2025-10-27 21:16:32');

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
-- Stand-in structure for view `treatment_analytics_view`
-- (See below for the actual view)
--
CREATE TABLE `treatment_analytics_view` (
`month` varchar(7)
,`total_treatments` bigint(21)
,`completed_treatments` bigint(21)
,`ongoing_treatments` bigint(21)
,`follow_up_treatments` bigint(21)
,`total_revenue` decimal(32,2)
,`avg_treatment_cost` decimal(14,6)
);

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
-- Stand-in structure for view `treatment_progress_view`
-- (See below for the actual view)
--
CREATE TABLE `treatment_progress_view` (
`treatment_id` int(11)
,`animal_id` int(11)
,`animal_name` varchar(100)
,`species` varchar(50)
,`veterinary_id` int(11)
,`veterinary_name` varchar(101)
,`diagnosis` text
,`treatment_details` text
,`medication_prescribed` text
,`treatment_date` date
,`follow_up_date` date
,`treatment_status` enum('ongoing','completed','follow_up')
,`cost` decimal(10,2)
,`days_since_treatment` int(7)
,`progress_status` varchar(19)
,`client_id` int(11)
,`client_name` varchar(101)
,`client_phone` varchar(20)
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
(1, 'admin', 'admin@vet.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'admin', 1, '2025-10-13 14:07:16', 'Patience', 'Manguluti', '0882279994', '1759 Blantyre', NULL, '2025-10-27 20:54:07', '2025-10-27 22:54:07'),
(2, 'sethpatience', 'sethpatiencemanguluti@outlook.com', '$2y$10$jf.8Oa9WM0JlPQJbGBM0gOwNfViTQp0IYHegfc/SpNLPaQlu4OJIy', 'client', 1, '2025-10-13 14:07:16', 'Seth', 'Patience', NULL, NULL, NULL, '2025-10-13 14:07:16', NULL),
(3, 'psmanguluti', 'admin@teampay.com', '$2y$10$ibjeulZJDCn.MQCM/PeTzubASwlWoxbTHH66jDopJGc9ImF5/wtvK', 'client', 1, '2025-10-13 14:07:16', NULL, NULL, NULL, NULL, NULL, '2025-10-13 14:07:16', NULL),
(4, 'seth', 'patmanseth@gmail.com', '$2y$10$n.F.7y1xPxakPku97NGHnOf/Q.CN0Tkl7Ce8bWAjBnUu9JxSqXfXG', 'veterinary', 1, '2025-10-13 14:07:16', 'Wanangwa', 'Manguluti', '0882279994', 'Area 18A', NULL, '2025-10-13 14:07:16', NULL),
(5, 'wanagwa', 'sethpatiencemanguluti@outloo6k.com', '$2y$10$iEkcAfGO61u.6jhjaOCdceGSteysvSzw..U0lvX.cvlgKPpe3yUaK', 'veterinary', 1, '2025-10-13 14:07:16', NULL, NULL, NULL, NULL, NULL, '2025-10-13 14:07:16', NULL),
(6, 'Steve Biko', 'sb@vetelinary.com', '$2y$10$dkzY8/3w1B0E2/l4rRWSEOTttK97u.IxOmOTEEt0v/EttG5zaS6g2', 'veterinary', 1, '2025-10-13 14:42:47', NULL, NULL, NULL, NULL, NULL, '2025-10-27 14:17:13', '2025-10-27 16:17:13'),
(7, 'client q', 'client@outlook.com', '$2y$10$hza6ztQuQ9pgHMv1IbMb0.ygAjoUSXGWND3XRRx7rgiT7RM3tDb9G', 'client', 1, '2025-10-14 19:26:11', 'Seko', 'Mwalwen', '+265 882 279 996', 'Area 18A', NULL, '2025-10-27 20:53:27', '2025-10-27 22:53:27'),
(8, 'smanguluti', 'smanguluti@vims.com', '$2y$10$BXCn05AbIy2xIyUL/wylh.pTJdGSgXWLLxP.AQ8KrMp9wYpsEcVf2', 'client', 1, '2025-10-15 13:01:45', 'Sam', 'Manguluti', '+265992920181', 'Private Bag B411 Lilongwe 3', NULL, '2025-10-16 14:15:08', '2025-10-16 16:15:08'),
(9, 'Q banz', 'q@vims.com', '$2y$10$hko2YQy5bBMtgPe/mFwcweiQPY545bjGk3g7mFf8g8vkNSoM11rpi', 'client', 1, '2025-10-16 16:28:30', 'qbanz', 'Manguluti', '+265992920181', 'Private Bag B411 Lilongwe 3', NULL, '2025-10-16 16:29:14', '2025-10-16 18:29:14'),
(10, 'PSimama', 'sp@outlook.com', '$2y$10$F/01RfTz9AbkrVq/xDqkXuX04BodXLjemNY2phE0hIoO.T0MGEzIS', 'client', 1, '2025-10-18 13:59:56', 'Simama', 'Priscila', '+265882279994', 'Area 18A', NULL, '2025-10-24 06:45:54', '2025-10-24 08:45:54'),
(32, 'dr_banda', 'dr.banda@vetclinic.mw', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'veterinary', 1, '2024-01-15 07:00:00', 'Chisomo', 'Banda', '+265 991 234 567', '45 Chipembere Highway, Blantyre', NULL, '2025-10-27 21:16:26', NULL),
(33, 'dr_phiri', 'dr.phiri@vetclinic.mw', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'veterinary', 1, '2024-02-20 08:30:00', 'Kondwani', 'Phiri', '+265 992 345 678', '78 Convention Drive, Lilongwe', NULL, '2025-10-27 21:16:26', NULL),
(34, 'dr_mwale', 'dr.mwale@vetclinic.mw', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'veterinary', 1, '2024-03-10 12:15:00', 'Thandiwe', 'Mwale', '+265 993 456 789', '120 Orton Chirwa Ave, Mzuzu', NULL, '2025-10-27 21:16:26', NULL),
(35, 'client_mbewe', 'tmbewe@gmail.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'client', 1, '2024-01-20 09:00:00', 'Tawonga', 'Mbewe', '+265 994 567 890', 'Area 47, Sector 3, Lilongwe', NULL, '2025-10-27 21:16:26', NULL),
(36, 'client_kachali', 'pkachali@yahoo.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'client', 1, '2024-02-05 11:45:00', 'Pemphero', 'Kachali', '+265 995 678 901', 'Namiwawa, Blantyre', NULL, '2025-10-27 21:16:26', NULL),
(37, 'client_chirwa', 'gchirwa@outlook.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'client', 1, '2024-03-15 14:20:00', 'Grace', 'Chirwa', '+265 996 789 012', 'Chirunga, Zomba', NULL, '2025-10-27 21:16:26', NULL),
(38, 'client_nyirenda', 'mnyirenda@hotmail.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'client', 1, '2024-04-01 06:30:00', 'Mphatso', 'Nyirenda', '+265 997 890 123', 'Katoto, Mzuzu', NULL, '2025-10-27 21:16:26', NULL),
(39, 'client_kamanga', 'akamanga@gmail.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'client', 1, '2024-04-18 13:10:00', 'Alinafe', 'Kamanga', '+265 998 901 234', 'Area 25, Lilongwe', NULL, '2025-10-27 21:16:26', NULL),
(40, 'client_lungu', 'dlungu@gmail.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'client', 1, '2024-05-10 07:20:00', 'Dalitso', 'Lungu', '+265 999 012 345', 'Soche West, Blantyre', NULL, '2025-10-27 21:16:26', NULL),
(41, 'client_tembo', 'ytembo@yahoo.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'client', 1, '2024-05-25 08:45:00', 'Yamikani', 'Tembo', '+265 991 123 456', 'Ginnery Corner, Zomba', NULL, '2025-10-27 21:16:26', NULL),
(42, 'client_gondwe', 'cgondwe@outlook.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'client', 1, '2024-06-08 12:30:00', 'Chimwemwe', 'Gondwe', '+265 992 234 567', 'Area 18, Lilongwe', NULL, '2025-10-27 21:16:26', NULL),
(43, 'client_mvula', 'kmvula@gmail.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'client', 1, '2024-06-20 14:15:00', 'Kondwani', 'Mvula', '+265 993 345 678', 'Ndirande, Blantyre', NULL, '2025-10-27 21:16:26', NULL),
(44, 'client_zulu', 'lzulu@hotmail.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'client', 1, '2024-07-05 09:00:00', 'Limbikani', 'Zulu', '+265 994 456 789', 'Chibavi, Mzuzu', NULL, '2025-10-27 21:16:26', NULL);

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
-- Stand-in structure for view `user_analytics_view`
-- (See below for the actual view)
--
CREATE TABLE `user_analytics_view` (
`month` varchar(7)
,`total_users` bigint(21)
,`client_users` bigint(21)
,`veterinary_users` bigint(21)
,`admin_users` bigint(21)
,`active_users` bigint(21)
,`inactive_users` bigint(21)
);

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
  `status` enum('scheduled','completed','overdue','verified','reaction_reported') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `dosage` varchar(50) DEFAULT NULL,
  `route` enum('subcutaneous','intramuscular','oral','intranasal','intradermal') DEFAULT NULL,
  `site` varchar(50) DEFAULT NULL,
  `reaction_notes` text DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `verification_date` datetime DEFAULT NULL,
  `current_weight` decimal(5,2) DEFAULT NULL,
  `animal_temperature` decimal(3,1) DEFAULT NULL,
  `health_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `vaccines`
--

INSERT INTO `vaccines` (`vaccine_id`, `animal_id`, `vaccine_name`, `vaccine_type`, `vaccine_date`, `next_due_date`, `administered_by`, `batch_number`, `manufacturer`, `notes`, `status`, `created_at`, `updated_at`, `dosage`, `route`, `site`, `reaction_notes`, `verified_by`, `verification_date`, `current_weight`, `animal_temperature`, `health_notes`) VALUES
(1, 1, 'Rabies Vaccine', 'Rabies', '2024-01-15', '2025-01-15', 4, 'RB2024A1', 'VetPharm', NULL, 'completed', '2025-10-13 14:07:25', '2025-10-13 14:07:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 'DHPP Vaccine', 'Core', '2024-01-15', '2025-01-15', 4, 'DH2024B2', 'AnimalHealth', NULL, 'completed', '2025-10-13 14:07:25', '2025-10-13 14:07:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 2, 'FVRCP Vaccine', 'Core', '2024-01-10', '2025-01-10', 5, 'FV2024C3', 'CatCare', NULL, 'completed', '2025-10-13 14:07:25', '2025-10-13 14:07:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 1, 'Rabies Vaccine', 'Core', '2024-09-15', '2025-09-15', 4, 'RB-MW-2024-001', 'Zoetis', NULL, 'completed', '2025-10-27 21:16:30', '2025-10-27 21:16:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 2, 'Rabies Vaccine', 'Core', '2024-09-20', '2025-09-20', 5, 'RB-MW-2024-002', 'Merck Animal Health', NULL, 'completed', '2025-10-27 21:16:30', '2025-10-27 21:16:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 3, 'Rabies Vaccine', 'Core', '2024-08-10', '2025-08-10', 6, 'RB-MW-2024-003', 'Zoetis', NULL, 'completed', '2025-10-27 21:16:30', '2025-10-27 21:16:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 1, 'DHPP Vaccine', 'Core', '2024-09-15', '2025-09-15', 4, 'DH-MW-2024-001', 'Merck Animal Health', NULL, 'completed', '2025-10-27 21:16:30', '2025-10-27 21:16:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 2, 'DHPP Vaccine', 'Core', '2024-09-20', '2025-09-20', 5, 'DH-MW-2024-002', 'Boehringer Ingelheim', NULL, 'completed', '2025-10-27 21:16:30', '2025-10-27 21:16:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 4, 'DHPP Vaccine', 'Core', '2024-07-15', '2025-07-15', 4, 'DH-MW-2024-003', 'Zoetis', NULL, 'completed', '2025-10-27 21:16:30', '2025-10-27 21:16:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 5, 'FVRCP Vaccine', 'Core', '2024-10-05', '2025-10-05', 5, 'FV-MW-2024-001', 'Zoetis', NULL, 'completed', '2025-10-27 21:16:30', '2025-10-27 21:16:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 6, 'FVRCP Vaccine', 'Core', '2024-08-20', '2025-08-20', 6, 'FV-MW-2024-002', 'Merck Animal Health', NULL, 'completed', '2025-10-27 21:16:30', '2025-10-27 21:16:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 1, 'Bordetella Vaccine', 'Non-core', '2024-09-15', '2025-09-15', 4, 'BD-MW-2024-001', 'Zoetis', NULL, 'completed', '2025-10-27 21:16:30', '2025-10-27 21:16:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 2, 'Bordetella Vaccine', 'Non-core', '2024-09-20', '2025-09-20', 5, 'BD-MW-2024-002', 'Merck Animal Health', NULL, 'completed', '2025-10-27 21:16:30', '2025-10-27 21:16:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 4, 'Rabies Vaccine', 'Core', '2024-07-15', '2025-07-15', 4, 'RB-MW-2024-004', 'Zoetis', NULL, 'completed', '2025-10-27 21:16:32', '2025-10-27 21:16:32', '1.0 mL', 'intramuscular', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 7, 'FVRCP Vaccine', 'Core', '2024-09-10', '2025-09-10', 5, 'FV-MW-2024-003', 'Merck Animal Health', NULL, 'completed', '2025-10-27 21:16:32', '2025-10-27 21:16:32', '1.0 mL', 'subcutaneous', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 8, 'FVRCP Vaccine', 'Core', '2024-09-12', '2025-09-12', 6, 'FV-MW-2024-004', 'Zoetis', NULL, 'completed', '2025-10-27 21:16:32', '2025-10-27 21:16:32', '1.0 mL', 'subcutaneous', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 9, 'Rabies Vaccine', 'Core', '2024-09-18', '2025-09-18', 4, 'RB-MW-2024-005', 'Merck Animal Health', NULL, 'completed', '2025-10-27 21:16:32', '2025-10-27 21:16:32', '1.0 mL', 'intramuscular', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 10, 'DHPP Vaccine', 'Core', '2024-09-22', '2025-09-22', 5, 'DH-MW-2024-004', 'Boehringer Ingelheim', NULL, 'completed', '2025-10-27 21:16:32', '2025-10-27 21:16:32', '1.0 mL', 'subcutaneous', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 11, 'DHPP Vaccine', 'Core', '2024-09-25', '2025-09-25', 6, 'DH-MW-2024-005', 'Zoetis', NULL, 'completed', '2025-10-27 21:16:32', '2025-10-27 21:16:32', '1.0 mL', 'subcutaneous', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 12, 'Rabies Vaccine', 'Core', '2024-10-01', '2025-10-01', 4, 'RB-MW-2024-006', 'Zoetis', NULL, 'completed', '2025-10-27 21:16:32', '2025-10-27 21:16:32', '1.0 mL', 'intramuscular', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 13, 'DHPP Vaccine', 'Core', '2024-10-03', '2025-10-03', 5, 'DH-MW-2024-006', 'Merck Animal Health', NULL, 'completed', '2025-10-27 21:16:32', '2025-10-27 21:16:32', '1.0 mL', 'subcutaneous', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 14, 'Bordetella Vaccine', 'Non-core', '2024-10-05', '2025-10-05', 6, 'BD-MW-2024-003', 'Zoetis', NULL, 'completed', '2025-10-27 21:16:32', '2025-10-27 21:16:32', '1.0 mL', 'intranasal', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 15, 'Leptospirosis Vaccine', 'Non-core', '2024-10-08', '2025-10-08', 4, 'LP-MW-2024-001', 'Merck Animal Health', NULL, 'completed', '2025-10-27 21:16:32', '2025-10-27 21:16:32', '1.0 mL', 'subcutaneous', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 1, 'Leptospirosis Vaccine', 'Non-core', '2024-09-15', '2025-09-15', 4, 'LP-MW-2024-002', 'Zoetis', NULL, 'completed', '2025-10-27 21:16:32', '2025-10-27 21:16:32', '1.0 mL', 'subcutaneous', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 2, 'Canine Influenza Vaccine', 'Non-core', '2024-09-20', '2025-09-20', 5, 'CI-MW-2024-001', 'Merck Animal Health', NULL, 'completed', '2025-10-27 21:16:32', '2025-10-27 21:16:32', '1.0 mL', 'subcutaneous', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
,`vaccine_status` enum('scheduled','completed','overdue','verified','reaction_reported')
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
-- Stand-in structure for view `veterinary_performance_view`
-- (See below for the actual view)
--
CREATE TABLE `veterinary_performance_view` (
`user_id` int(11)
,`vet_name` varchar(101)
,`total_treatments` bigint(21)
,`completed_treatments` bigint(21)
,`ongoing_treatments` bigint(21)
,`total_revenue` decimal(32,2)
,`avg_treatment_cost` decimal(14,6)
,`unique_patients` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `veterinary_schedule_view`
-- (See below for the actual view)
--
CREATE TABLE `veterinary_schedule_view` (
`appointment_id` int(11)
,`veterinary_id` int(11)
,`appointment_date` date
,`appointment_time` time
,`duration` int(11)
,`appointment_type` enum('consultation','vaccination','surgery','checkup','emergency','grooming')
,`status` enum('scheduled','confirmed','in_progress','completed','cancelled','no_show')
,`vet_first_name` varchar(50)
,`vet_last_name` varchar(50)
,`vet_full_name` varchar(101)
,`animal_name` varchar(100)
,`species` varchar(50)
,`client_first_name` varchar(50)
,`client_last_name` varchar(50)
,`client_full_name` varchar(101)
,`reason` text
,`end_time` time
,`daily_appointment_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `veterinary_treatment_workload`
-- (See below for the actual view)
--
CREATE TABLE `veterinary_treatment_workload` (
`veterinary_id` int(11)
,`veterinary_name` varchar(101)
,`email` varchar(100)
,`total_treatments` bigint(21)
,`ongoing_treatments` decimal(22,0)
,`follow_up_treatments` decimal(22,0)
,`completed_treatments` decimal(22,0)
,`unique_animals` bigint(21)
,`avg_treatment_cost` decimal(14,6)
,`last_treatment_date` date
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
-- Structure for view `appointment_details_view`
--
DROP TABLE IF EXISTS `appointment_details_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `appointment_details_view`  AS SELECT `a`.`appointment_id` AS `appointment_id`, `a`.`animal_id` AS `animal_id`, `a`.`client_id` AS `client_id`, `a`.`veterinary_id` AS `veterinary_id`, `a`.`appointment_date` AS `appointment_date`, `a`.`appointment_time` AS `appointment_time`, `a`.`duration` AS `duration`, `a`.`appointment_type` AS `appointment_type`, `a`.`reason` AS `reason`, `a`.`status` AS `status`, `a`.`notes` AS `notes`, `a`.`reminder_sent` AS `reminder_sent`, `a`.`created_by` AS `created_by`, `a`.`created_at` AS `created_at`, `a`.`updated_at` AS `updated_at`, `an`.`name` AS `animal_name`, `an`.`species` AS `species`, `an`.`breed` AS `breed`, `an`.`gender` AS `gender`, `an`.`color` AS `color`, `u_client`.`first_name` AS `client_first_name`, `u_client`.`last_name` AS `client_last_name`, concat(`u_client`.`first_name`,' ',`u_client`.`last_name`) AS `client_full_name`, `u_client`.`phone` AS `client_phone`, `u_client`.`email` AS `client_email`, `u_vet`.`first_name` AS `vet_first_name`, `u_vet`.`last_name` AS `vet_last_name`, concat(`u_vet`.`first_name`,' ',`u_vet`.`last_name`) AS `vet_full_name`, `u_vet`.`email` AS `vet_email`, `u_vet`.`phone` AS `vet_phone`, `u_creator`.`first_name` AS `created_by_first_name`, `u_creator`.`last_name` AS `created_by_last_name`, concat(`u_creator`.`first_name`,' ',`u_creator`.`last_name`) AS `created_by_full_name`, date_format(`a`.`appointment_date`,'%W, %M %e, %Y') AS `formatted_date`, date_format(`a`.`appointment_time`,'%h:%i %p') AS `formatted_time`, `a`.`appointment_time`+ interval `a`.`duration` minute AS `end_time`, date_format(`a`.`appointment_time` + interval `a`.`duration` minute,'%h:%i %p') AS `formatted_end_time`, CASE WHEN `a`.`status` = 'scheduled' THEN 'Scheduled' WHEN `a`.`status` = 'confirmed' THEN 'Confirmed' WHEN `a`.`status` = 'in_progress' THEN 'In Progress' WHEN `a`.`status` = 'completed' THEN 'Completed' WHEN `a`.`status` = 'cancelled' THEN 'Cancelled' WHEN `a`.`status` = 'no_show' THEN 'No Show' END AS `status_text`, CASE WHEN `a`.`appointment_type` = 'emergency' THEN 'high' WHEN to_days(`a`.`appointment_date`) - to_days(curdate()) <= 1 THEN 'medium' ELSE 'low' END AS `priority`, to_days(`a`.`appointment_date`) - to_days(curdate()) AS `days_until_appointment`, CASE WHEN `a`.`appointment_date` = curdate() THEN 1 ELSE 0 END AS `is_today`, CASE WHEN `a`.`appointment_date` >= curdate() AND `a`.`status` in ('scheduled','confirmed') THEN 1 ELSE 0 END AS `is_upcoming` FROM (((((`appointments` `a` join `animals` `an` on(`a`.`animal_id` = `an`.`animal_id`)) join `clients` `c` on(`a`.`client_id` = `c`.`client_id`)) join `users` `u_client` on(`c`.`user_id` = `u_client`.`user_id`)) left join `users` `u_vet` on(`a`.`veterinary_id` = `u_vet`.`user_id`)) join `users` `u_creator` on(`a`.`created_by` = `u_creator`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `client_activity_view`
--
DROP TABLE IF EXISTS `client_activity_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `client_activity_view`  AS SELECT `c`.`client_id` AS `client_id`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `client_name`, count(distinct `a`.`animal_id`) AS `total_animals`, count(`t`.`treatment_id`) AS `total_treatments`, sum(`b`.`total_amount`) AS `total_spent`, max(`t`.`treatment_date`) AS `last_visit` FROM ((((`clients` `c` join `users` `u` on(`c`.`user_id` = `u`.`user_id`)) left join `animals` `a` on(`c`.`client_id` = `a`.`client_id`)) left join `treatments` `t` on(`a`.`animal_id` = `t`.`animal_id`)) left join `billings` `b` on(`a`.`animal_id` = `b`.`animal_id`)) WHERE `u`.`is_active` = 1 GROUP BY `c`.`client_id`, `u`.`first_name`, `u`.`last_name` ;

-- --------------------------------------------------------

--
-- Structure for view `client_details_view`
--
DROP TABLE IF EXISTS `client_details_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `client_details_view`  AS SELECT `c`.`client_id` AS `client_id`, `c`.`emergency_contact` AS `emergency_contact`, `c`.`preferred_contact_method` AS `preferred_contact_method`, `c`.`notes` AS `client_notes`, `c`.`created_at` AS `client_created_at`, `c`.`updated_at` AS `client_updated_at`, `u`.`user_id` AS `user_id`, `u`.`username` AS `username`, `u`.`email` AS `email`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `full_name`, `u`.`phone` AS `phone`, `u`.`address` AS `address`, `u`.`profile_picture` AS `profile_picture`, `u`.`is_active` AS `is_active`, `u`.`last_login` AS `last_login`, (select count(0) from `animals` where `animals`.`client_id` = `c`.`client_id` and `animals`.`status` = 'active') AS `active_animals_count`, (select count(0) from `animals` where `animals`.`client_id` = `c`.`client_id`) AS `total_animals_count` FROM (`clients` `c` join `users` `u` on(`c`.`user_id` = `u`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `client_treatment_history`
--
DROP TABLE IF EXISTS `client_treatment_history`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `client_treatment_history`  AS SELECT `c`.`client_id` AS `client_id`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `client_name`, `a`.`animal_id` AS `animal_id`, `a`.`name` AS `animal_name`, `a`.`species` AS `species`, count(`t`.`treatment_id`) AS `total_treatments`, sum(case when `t`.`status` = 'completed' then 1 else 0 end) AS `completed_treatments`, sum(case when `t`.`status` = 'ongoing' then 1 else 0 end) AS `ongoing_treatments`, sum(`t`.`cost`) AS `total_treatment_cost`, max(`t`.`treatment_date`) AS `last_treatment_date` FROM (((`clients` `c` join `users` `u` on(`c`.`user_id` = `u`.`user_id`)) join `animals` `a` on(`c`.`client_id` = `a`.`client_id`)) left join `treatments` `t` on(`a`.`animal_id` = `t`.`animal_id`)) GROUP BY `c`.`client_id`, `u`.`first_name`, `u`.`last_name`, `a`.`animal_id`, `a`.`name`, `a`.`species` ;

-- --------------------------------------------------------

--
-- Structure for view `revenue_analytics_view`
--
DROP TABLE IF EXISTS `revenue_analytics_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `revenue_analytics_view`  AS SELECT date_format(`billings`.`billing_date`,'%Y-%m') AS `month`, count(0) AS `total_bills`, sum(`billings`.`total_amount`) AS `total_revenue`, sum(case when `billings`.`payment_status` = 'paid' then `billings`.`total_amount` else 0 end) AS `paid_revenue`, sum(case when `billings`.`payment_status` = 'pending' then `billings`.`total_amount` else 0 end) AS `pending_revenue`, avg(`billings`.`total_amount`) AS `avg_bill_amount` FROM `billings` WHERE `billings`.`billing_date` >= current_timestamp() - interval 12 month GROUP BY date_format(`billings`.`billing_date`,'%Y-%m') ORDER BY date_format(`billings`.`billing_date`,'%Y-%m') ASC ;

-- --------------------------------------------------------

--
-- Structure for view `species_analytics_view`
--
DROP TABLE IF EXISTS `species_analytics_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `species_analytics_view`  AS SELECT `animals`.`species` AS `species`, count(0) AS `animal_count`, round(count(0) * 100.0 / (select count(0) from `animals` where `animals`.`status` = 'active'),2) AS `percentage` FROM `animals` WHERE `animals`.`status` = 'active' GROUP BY `animals`.`species` ORDER BY count(0) DESC ;

-- --------------------------------------------------------

--
-- Structure for view `treatment_analytics_view`
--
DROP TABLE IF EXISTS `treatment_analytics_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `treatment_analytics_view`  AS SELECT date_format(`treatments`.`treatment_date`,'%Y-%m') AS `month`, count(0) AS `total_treatments`, count(case when `treatments`.`status` = 'completed' then 1 end) AS `completed_treatments`, count(case when `treatments`.`status` = 'ongoing' then 1 end) AS `ongoing_treatments`, count(case when `treatments`.`status` = 'follow_up' then 1 end) AS `follow_up_treatments`, sum(`treatments`.`cost`) AS `total_revenue`, avg(`treatments`.`cost`) AS `avg_treatment_cost` FROM `treatments` WHERE `treatments`.`treatment_date` >= current_timestamp() - interval 12 month GROUP BY date_format(`treatments`.`treatment_date`,'%Y-%m') ORDER BY date_format(`treatments`.`treatment_date`,'%Y-%m') ASC ;

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
-- Structure for view `treatment_progress_view`
--
DROP TABLE IF EXISTS `treatment_progress_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `treatment_progress_view`  AS SELECT `t`.`treatment_id` AS `treatment_id`, `t`.`animal_id` AS `animal_id`, `a`.`name` AS `animal_name`, `a`.`species` AS `species`, `t`.`veterinary_id` AS `veterinary_id`, concat(`u_vet`.`first_name`,' ',`u_vet`.`last_name`) AS `veterinary_name`, `t`.`diagnosis` AS `diagnosis`, `t`.`treatment_details` AS `treatment_details`, `t`.`medication_prescribed` AS `medication_prescribed`, `t`.`treatment_date` AS `treatment_date`, `t`.`follow_up_date` AS `follow_up_date`, `t`.`status` AS `treatment_status`, `t`.`cost` AS `cost`, to_days(curdate()) - to_days(`t`.`treatment_date`) AS `days_since_treatment`, CASE WHEN `t`.`status` = 'completed' THEN 'Completed' WHEN `t`.`follow_up_date` is null AND `t`.`status` <> 'completed' THEN 'In Progress' WHEN `t`.`follow_up_date` < curdate() THEN 'Follow-up Overdue' WHEN `t`.`follow_up_date` >= curdate() THEN 'Follow-up Scheduled' ELSE 'Active' END AS `progress_status`, `c`.`client_id` AS `client_id`, concat(`u_client`.`first_name`,' ',`u_client`.`last_name`) AS `client_name`, `u_client`.`phone` AS `client_phone` FROM ((((`treatments` `t` join `animals` `a` on(`t`.`animal_id` = `a`.`animal_id`)) join `users` `u_vet` on(`t`.`veterinary_id` = `u_vet`.`user_id`)) join `clients` `c` on(`a`.`client_id` = `c`.`client_id`)) join `users` `u_client` on(`c`.`user_id` = `u_client`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `user_analytics_view`
--
DROP TABLE IF EXISTS `user_analytics_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_analytics_view`  AS SELECT date_format(`users`.`created_at`,'%Y-%m') AS `month`, count(0) AS `total_users`, count(case when `users`.`role` = 'client' then 1 end) AS `client_users`, count(case when `users`.`role` = 'veterinary' then 1 end) AS `veterinary_users`, count(case when `users`.`role` = 'admin' then 1 end) AS `admin_users`, count(case when `users`.`is_active` = 1 then 1 end) AS `active_users`, count(case when `users`.`is_active` = 0 then 1 end) AS `inactive_users` FROM `users` WHERE `users`.`created_at` >= current_timestamp() - interval 12 month GROUP BY date_format(`users`.`created_at`,'%Y-%m') ORDER BY date_format(`users`.`created_at`,'%Y-%m') ASC ;

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

-- --------------------------------------------------------

--
-- Structure for view `veterinary_performance_view`
--
DROP TABLE IF EXISTS `veterinary_performance_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `veterinary_performance_view`  AS SELECT `u`.`user_id` AS `user_id`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `vet_name`, count(`t`.`treatment_id`) AS `total_treatments`, count(case when `t`.`status` = 'completed' then 1 end) AS `completed_treatments`, count(case when `t`.`status` = 'ongoing' then 1 end) AS `ongoing_treatments`, sum(`t`.`cost`) AS `total_revenue`, avg(`t`.`cost`) AS `avg_treatment_cost`, count(distinct `t`.`animal_id`) AS `unique_patients` FROM (`users` `u` left join `treatments` `t` on(`u`.`user_id` = `t`.`veterinary_id`)) WHERE `u`.`role` = 'veterinary' AND `u`.`is_active` = 1 GROUP BY `u`.`user_id`, `u`.`first_name`, `u`.`last_name` ;

-- --------------------------------------------------------

--
-- Structure for view `veterinary_schedule_view`
--
DROP TABLE IF EXISTS `veterinary_schedule_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `veterinary_schedule_view`  AS SELECT `a`.`appointment_id` AS `appointment_id`, `a`.`veterinary_id` AS `veterinary_id`, `a`.`appointment_date` AS `appointment_date`, `a`.`appointment_time` AS `appointment_time`, `a`.`duration` AS `duration`, `a`.`appointment_type` AS `appointment_type`, `a`.`status` AS `status`, `u_vet`.`first_name` AS `vet_first_name`, `u_vet`.`last_name` AS `vet_last_name`, concat(`u_vet`.`first_name`,' ',`u_vet`.`last_name`) AS `vet_full_name`, `an`.`name` AS `animal_name`, `an`.`species` AS `species`, `u_client`.`first_name` AS `client_first_name`, `u_client`.`last_name` AS `client_last_name`, concat(`u_client`.`first_name`,' ',`u_client`.`last_name`) AS `client_full_name`, `a`.`reason` AS `reason`, `a`.`appointment_time`+ interval `a`.`duration` minute AS `end_time`, (select count(0) from `appointments` `a2` where `a2`.`veterinary_id` = `a`.`veterinary_id` and `a2`.`appointment_date` = `a`.`appointment_date` and `a2`.`status` in ('scheduled','confirmed')) AS `daily_appointment_count` FROM ((((`appointments` `a` join `users` `u_vet` on(`a`.`veterinary_id` = `u_vet`.`user_id`)) join `animals` `an` on(`a`.`animal_id` = `an`.`animal_id`)) join `clients` `c` on(`a`.`client_id` = `c`.`client_id`)) join `users` `u_client` on(`c`.`user_id` = `u_client`.`user_id`)) WHERE `a`.`veterinary_id` is not null ORDER BY `a`.`appointment_date` ASC, `a`.`appointment_time` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `veterinary_treatment_workload`
--
DROP TABLE IF EXISTS `veterinary_treatment_workload`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `veterinary_treatment_workload`  AS SELECT `u`.`user_id` AS `veterinary_id`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `veterinary_name`, `u`.`email` AS `email`, count(`t`.`treatment_id`) AS `total_treatments`, sum(case when `t`.`status` = 'ongoing' then 1 else 0 end) AS `ongoing_treatments`, sum(case when `t`.`status` = 'follow_up' then 1 else 0 end) AS `follow_up_treatments`, sum(case when `t`.`status` = 'completed' then 1 else 0 end) AS `completed_treatments`, count(distinct `a`.`animal_id`) AS `unique_animals`, avg(`t`.`cost`) AS `avg_treatment_cost`, max(`t`.`treatment_date`) AS `last_treatment_date` FROM ((`users` `u` left join `treatments` `t` on(`u`.`user_id` = `t`.`veterinary_id`)) left join `animals` `a` on(`t`.`animal_id` = `a`.`animal_id`)) WHERE `u`.`role` = 'veterinary' AND `u`.`is_active` = 1 GROUP BY `u`.`user_id`, `u`.`first_name`, `u`.`last_name`, `u`.`email` ;

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
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `idx_animal_id` (`animal_id`),
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `idx_veterinary_id` (`veterinary_id`),
  ADD KEY `idx_appointment_date` (`appointment_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_type` (`appointment_type`),
  ADD KEY `created_by` (`created_by`);

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
  ADD KEY `idx_status` (`status`),
  ADD KEY `verified_by` (`verified_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `animals`
--
ALTER TABLE `animals`
  MODIFY `animal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `animal_assignments_history`
--
ALTER TABLE `animal_assignments_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billings`
--
ALTER TABLE `billings`
  MODIFY `billing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `treatments`
--
ALTER TABLE `treatments`
  MODIFY `treatment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vaccines`
--
ALTER TABLE `vaccines`
  MODIFY `vaccine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`veterinary_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_vaccines_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `vaccines_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`animal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vaccines_ibfk_2` FOREIGN KEY (`administered_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `vaccines_ibfk_3` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
