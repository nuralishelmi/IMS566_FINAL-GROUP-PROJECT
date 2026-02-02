-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2026 at 02:23 PM
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
-- Database: `clinic_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_name` varchar(100) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `symptoms` text DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `clinical_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_name`, `appointment_date`, `appointment_time`, `symptoms`, `status`, `clinical_notes`) VALUES
(1, 1, 'Dr. Ahmad (General Physician)', '2026-02-08', '15:00:00', 'Pening and demam', 'confirmed', 'done'),
(2, 1, 'Dr. Ahmad (General Physician)', '2026-03-10', '10:00:00', 'fatigue and fever', 'confirmed', 'already prescribed with paracetamol'),
(3, 6, 'Dr. Anisah (Pediatrician)', '2026-04-06', '08:30:00', 'Stomach ache', 'cancelled', NULL),
(4, 8, 'Dr. Kim (Dentist)', '2026-03-07', '10:00:00', 'Sakit gigi geraham', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `unit_price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item_name`, `category`, `stock_quantity`, `unit_price`) VALUES
(1, 'Paracetamol 500mg', 'Medicine', 50, 5.50),
(2, 'Amoxicillin', 'Antibiotics', 9, 12.00),
(3, 'Medical Face Mask', 'Supplies', 120, 0.50),
(4, 'Hand Sanitizer', 'Supplies', 2, 15.00),
(5, 'Metformin 500mg', 'Medicine', 100, 12.50),
(6, 'Amlodipine 5mg', 'Medicine', 80, 15.00),
(7, 'Salbutamol Inhaler', 'Medicine', 20, 25.00),
(8, 'Loratadine 10mg', 'Medicine', 60, 9.00),
(9, 'Augmentin 625mg', 'Antibiotics', 15, 45.00),
(10, 'Azithromycin 250mg', 'Antibiotics', 12, 38.00),
(11, 'Sterile Gauze Pads (10s)', 'Supplies', 50, 4.50),
(12, 'Surgical Spirit 100ml', 'Supplies', 10, 7.80),
(13, 'Adhesive Bandages (Box)', 'Supplies', 30, 12.00),
(14, 'Disposable Syringe 5ml', 'Supplies', 200, 0.30),
(15, 'Latex Gloves (Box 100s)', 'Supplies', 25, 28.00),
(16, 'Blood Glucose Test Strips', 'Supplies', 40, 65.00),
(17, 'Digital Thermometer', 'Equipment', 5, 18.00),
(18, 'Manual Blood Pressure Cuff', 'Equipment', 2, 85.00);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `change_amount` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`id`, `inventory_id`, `staff_id`, `change_amount`, `reason`, `created_at`) VALUES
(1, 13, 4, 0, 'Clinic Use', '2026-02-01 10:34:46'),
(2, 2, 4, 1, 'Restock', '2026-02-01 10:35:11');

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `staff_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`id`, `appointment_id`, `staff_id`, `diagnosis`, `prescription`, `staff_notes`) VALUES
(1, 1, NULL, 'This patient has denggi fever and already been admitted to ward.', 'paracetamol 500mg\r\naspirin 20mg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('staff','patient') DEFAULT 'patient',
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `staff_id` varchar(50) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `profile_pic`, `created_at`, `staff_id`, `username`, `phone`, `address`) VALUES
(1, 'Nurul Aqilah', 'qyha517@gmail.com', '1234', 'patient', NULL, '2026-02-01 08:03:17', NULL, 'sna', '01133354019', 'Sungai Udang,06900 Yan Kedah'),
(2, 'Ahmad Izuddin', 'izuddin11@gmail.com', '7777', 'staff', NULL, '2026-02-01 08:11:42', 'PMC-1106', 'Ahmad', '0175654504', 'Bagan Serai'),
(4, 'Nur Anisah', 'anisah56@gmail.com', '2222', 'staff', 'staff_4_1769941595.JPG', '2026-02-01 09:12:22', 'PMC-1109', 'Anisah', '0198765456', 'Penang'),
(6, 'Aqil Izu', 'aqil77@gmail.com', '6565', 'patient', NULL, '2026-02-01 10:40:21', NULL, 'Aqil', '018-23456789', 'Bagan Serai, Perak'),
(7, 'Kim Taehyung', 'kim67@gmail.com', '9999', 'staff', 'staff_7_1769943714.avif', '2026-02-01 10:55:15', 'PMC-1107', 'Kim', '018-23456789', 'Yan, Kedah'),
(8, 'Alis Helmi', 'alis78@gmail.com', '8888', 'patient', NULL, '2026-02-01 11:06:41', NULL, 'Alis', '0198765456', 'Batu Kawan Penang');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_id` (`inventory_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `fk_staff_record` (`staff_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`),
  ADD CONSTRAINT `inventory_logs_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `fk_staff_record` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
