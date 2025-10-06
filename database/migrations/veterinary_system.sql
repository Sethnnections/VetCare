-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2025 at 08:47 PM
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
-- Database: `veterinary_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `is_active`, `created_at`, `first_name`, `last_name`, `phone`, `address`, `profile_picture`, `updated_at`) VALUES
(1, 'admin', 'admin@vet.com', '$2y$10$qG80YeMrk4kw1kZAzwA62eHkTjjxd2NoVnU.UBHaDqqn.BNDBTmkq', 'admin', 1, '2025-10-05 08:06:00', 'Patience', 'Manguluti', '0882279994', '1759 Blantyre', NULL, '2025-10-05 13:53:37'),
(2, 'sethpatience', 'sethpatiencemanguluti@outlook.com', '$2y$10$jf.8Oa9WM0JlPQJbGBM0gOwNfViTQp0IYHegfc/SpNLPaQlu4OJIy', 'client', 1, '2025-10-05 08:07:02', NULL, NULL, NULL, NULL, NULL, '2025-10-05 11:02:37'),
(3, 'psmanguluti@escom.mw', 'admin@teampay.com', '$2y$10$ibjeulZJDCn.MQCM/PeTzubASwlWoxbTHH66jDopJGc9ImF5/wtvK', 'client', 1, '2025-10-05 08:32:18', NULL, NULL, NULL, NULL, NULL, '2025-10-05 11:02:37'),
(4, 'Seth', 'patmanseth@gmail.com', '$2y$10$n.F.7y1xPxakPku97NGHnOf/Q.CN0Tkl7Ce8bWAjBnUu9JxSqXfXG', 'veterinary', 1, '2025-10-05 10:06:46', 'Wanangwa', 'Manguluti', '0882279994', 'Area 18A', NULL, '2025-10-05 14:19:41'),
(5, 'Wanagwa', 'sethpatiencemanguluti@outloo6k.com', '$2y$10$iEkcAfGO61u.6jhjaOCdceGSteysvSzw..U0lvX.cvlgKPpe3yUaK', 'veterinary', 1, '2025-10-05 14:48:32', NULL, NULL, NULL, NULL, NULL, '2025-10-05 14:48:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
