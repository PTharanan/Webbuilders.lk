-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 24, 2026 at 12:53 PM
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
-- Database: `webbuildersfinal`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `day_of_month` int(11) NOT NULL,
  `attendance` tinyint(1) DEFAULT 0,
  `in_time` time DEFAULT NULL,
  `out_time` time DEFAULT NULL,
  `tasklog_submitted` tinyint(1) DEFAULT 0,
  `working_hours` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `attendance_date`, `day_of_month`, `attendance`, `in_time`, `out_time`, `tasklog_submitted`, `working_hours`, `created_at`, `updated_at`) VALUES
(1, 3, '2026-01-01', 1, 1, '07:30:00', '21:24:00', 0, 13.90, '2026-01-24 08:13:28', '2026-01-24 10:30:25'),
(2, 4, '2026-01-02', 2, 1, '17:53:00', '19:45:00', 1, 1.87, '2026-01-24 08:15:29', '2026-01-24 10:05:33'),
(3, 3, '2026-01-02', 2, 1, '16:52:00', '21:52:00', 1, 5.00, '2026-01-24 08:22:16', '2026-01-24 08:22:16'),
(4, 6, '2026-01-02', 2, 1, '08:00:00', '17:49:00', 1, 9.82, '2026-01-24 08:26:27', '2026-01-24 10:10:08'),
(10, 5, '2026-01-02', 2, 1, '05:55:00', '19:54:00', 1, 13.98, '2026-01-24 10:10:17', '2026-01-24 10:29:54'),
(11, 6, '2026-01-03', 3, 1, '16:43:00', '18:43:00', 1, 2.00, '2026-01-24 10:13:24', '2026-01-24 10:13:24'),
(12, 3, '2026-01-06', 6, 1, '17:43:00', '22:43:00', 1, 5.00, '2026-01-24 10:13:50', '2026-01-24 10:13:50'),
(13, 6, '2026-01-06', 6, 1, '19:49:00', '20:45:00', 1, 0.93, '2026-01-24 10:15:10', '2026-01-24 10:15:10'),
(14, 5, '2026-01-05', 5, 1, '09:59:00', '10:59:00', 1, 1.00, '2026-01-24 10:18:00', '2026-01-24 10:18:00'),
(15, 4, '2026-01-08', 8, 1, '07:52:00', '18:52:00', 1, 11.00, '2026-01-24 10:18:40', '2026-01-24 10:18:40'),
(16, 6, '2026-01-08', 8, 0, NULL, NULL, 0, NULL, '2026-01-24 10:19:06', '2026-01-24 10:19:06'),
(17, 4, '2026-01-09', 9, 0, NULL, NULL, 0, NULL, '2026-01-24 10:20:37', '2026-01-24 10:20:37'),
(18, 6, '2026-01-09', 9, 0, NULL, NULL, 0, NULL, '2026-01-24 10:20:45', '2026-01-24 10:20:45'),
(21, 5, '2026-01-07', 7, 1, '21:00:00', '22:00:00', 1, 1.00, '2026-01-24 10:30:16', '2026-01-24 10:30:16'),
(23, 3, '2025-12-01', 1, 1, '19:00:00', '22:00:00', 1, 3.00, '2026-01-24 10:30:43', '2026-01-24 10:30:43'),
(24, 3, '2025-11-03', 3, 1, '18:01:00', '21:01:00', 0, 3.00, '2026-01-24 10:31:12', '2026-01-24 10:31:12'),
(25, 3, '2026-01-05', 5, 1, '17:12:00', '22:12:00', 1, 5.00, '2026-01-24 10:42:34', '2026-01-24 10:42:34'),
(26, 3, '2026-01-07', 7, 1, '19:21:00', '21:21:00', 1, 2.00, '2026-01-24 11:51:13', '2026-01-24 11:51:13');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `left_date` date DEFAULT NULL,
  `status` enum('working','left') DEFAULT 'working',
  `description` text DEFAULT NULL,
  `national_id_path` varchar(500) DEFAULT NULL,
  `character_certificate_path` varchar(500) DEFAULT NULL,
  `bank_proof_path` varchar(500) DEFAULT NULL,
  `cv_resume_path` varchar(500) DEFAULT NULL,
  `appointment_letter_path` varchar(500) DEFAULT NULL,
  `photograph_path` varchar(500) DEFAULT NULL,
  `folder_name` varchar(255) DEFAULT NULL,
  `interview_date` date DEFAULT NULL,
  `nic_number` varchar(20) DEFAULT NULL,
  `ol_result_path` varchar(500) DEFAULT NULL,
  `al_result_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verified_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `phone_number`, `address`, `email`, `designation`, `joining_date`, `left_date`, `status`, `description`, `national_id_path`, `character_certificate_path`, `bank_proof_path`, `cv_resume_path`, `appointment_letter_path`, `photograph_path`, `folder_name`, `interview_date`, `nic_number`, `ol_result_path`, `al_result_path`, `created_at`, `updated_at`, `verified_by`) VALUES
(3, 'Kamalanathan Thananchayan', '0740536517', 'Meesalai west,\r\nMeesalai', 'kamalanathanthananchayan04@gmail.com', 'Software Engineer', '2026-01-22', '2026-01-22', 'working', 'dsffffff', 'uploads/employees/Kamalanathan_Thananchayan_0740536517/national_id.pdf', 'uploads/employees/Kamalanathan_Thananchayan_0740536517/character_certificate.pdf', 'uploads/employees/Kamalanathan_Thananchayan_0740536517/bank_proof.pdf', './uploads/interviews/cv_1768985334_697092f6a76fb.pdf', 'uploads/employees/Kamalanathan_Thananchayan_0740536517/appointment_letter.pdf', 'uploads/employees/Kamalanathan_Thananchayan_0740536517/photograph.png', 'Kamalanathan_Thananchayan_0740536517', '2026-01-22', '200228002106', 'uploads/employees/Kamalanathan_Thananchayan_0740536517/ol_result.pdf', NULL, '2026-01-21 09:24:12', '2026-01-23 15:39:25', NULL),
(4, 'sadasd', '0740536544', 'Meesalai west,\r\nMeesalai', 'kamalanathanthananchayan04@gmail.com', 'Admin', '2026-01-20', NULL, 'working', 'Hello World', 'uploads/employees/sadasd_0740536544/national_id.png', 'uploads/employees/sadasd_0740536544/character_certificate.pdf', 'uploads/employees/sadasd_0740536544/bank_proof.png', './uploads/interviews/cv_1768990434_6970a6e21c04f.pdf', 'uploads/employees/sadasd_0740536544/appointment_letter.pdf', 'uploads/employees/sadasd_0740536544/photograph.png', 'sadasd_0740536544', '2026-01-20', '200228002134', 'uploads/employees/sadasd_0740536544/ol_result.png', 'uploads/employees/sadasd_0740536544/al_result.png', '2026-01-23 15:35:18', '2026-01-23 15:35:18', 'Dhanush'),
(5, 'Ulaganathan Chartheepan', '0740536517', 'Meesalai west,\r\nMeesalai', 'kamalanathanthananchayan04@gmail.com', 'Software Engineer', '2026-01-24', NULL, 'working', 'UO;IIIIIIII', 'uploads/employees/Ulaganathan_Chartheepan_0740536517/national_id.png', 'uploads/employees/Ulaganathan_Chartheepan_0740536517/character_certificate.png', '', './uploads/interviews/cv_1769183144_697397a89d9e0.pdf', '', '', 'Ulaganathan_Chartheepan_0740536517', '2026-01-24', '200228002132', '', '', '2026-01-23 16:07:15', '2026-01-23 16:25:42', 'sdffffff'),
(6, 'Thanu Thana', '0740536517', 'meesalai west\r\nMeesalai', 'kamalanathanthananchayan06@gmail.com', 'Software Engineer', '2026-01-01', NULL, 'working', 'DFFFFFFFFFFFFF', 'uploads/employees/Thanu_Thana_0740536517/national_id.png', '', '', 'uploads/employees/Thanu_Thana_0740536517/cv_resume.pdf', '', 'uploads/employees/Thanu_Thana_0740536517/photograph.png', 'Thanu_Thana_0740536517', '2026-01-09', '200228002132', '', '', '2026-01-23 16:25:17', '2026-01-23 16:31:46', 'Pallavi');

-- --------------------------------------------------------

--
-- Table structure for table `interviews`
--

CREATE TABLE `interviews` (
  `id` int(11) NOT NULL,
  `candidate_name` varchar(255) NOT NULL,
  `nic` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `interview_date` date NOT NULL,
  `status` enum('pending','passed','rejected') DEFAULT 'pending',
  `join_date` date DEFAULT NULL,
  `cv_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `checkout_url` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`id`, `plan_name`, `price`, `checkout_url`, `created_at`, `updated_at`) VALUES
(2, 'Domain Only', 10000.00, 'https://sandbox.payhere.lk/pay/obb535c9f', '2026-01-23 16:38:06', '2026-01-24 11:33:56'),
(3, 'Starter Package', 28000.00, 'https://sandbox.payhere.lk/pay/occ546c09', '2026-01-23 16:49:09', '2026-01-24 11:18:33'),
(4, 'Light Package', 46000.00, 'https://sandbox.payhere.lk/pay/o555d3db3', '2026-01-23 16:49:25', '2026-01-24 11:18:37'),
(5, 'Pro Package', 70000.00, 'https://sandbox.payhere.lk/pay/o225a0d25', '2026-01-23 16:49:46', '2026-01-24 11:18:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('admin','manager','staff') DEFAULT 'staff',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin@gmail.com', '$2y$10$vLvpxL.vkLetLukU8qNCC.jzSM.Qpj7MV63tjjIbKkk3KeF/AJH.m', 'Admin User', 'admin', 'active', '2026-01-21 04:38:04', '2026-01-21 04:38:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_date` (`employee_id`,`attendance_date`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_attendance_date` (`attendance_date`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_phone` (`phone_number`),
  ADD KEY `idx_nic_number` (`nic_number`),
  ADD KEY `idx_interview_date` (`interview_date`);

--
-- Indexes for table `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plan_name` (`plan_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
