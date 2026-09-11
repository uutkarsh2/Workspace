-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2026 at 02:57 PM
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
-- Database: `meeting_workspace`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `workspace_id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `meeting_title` varchar(150) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `booking_type` varchar(20) NOT NULL DEFAULT 'Time Slot',
  `recipient_email` varchar(150) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Booked',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `workspace_id`, `company_name`, `meeting_title`, `booking_date`, `start_time`, `end_time`, `booking_type`, `recipient_email`, `status`, `created_at`) VALUES
(1, NULL, 1, '', 'vvvvv', '2026-09-02', '10:44:00', '12:44:00', 'Time Slot', '', 'Booked', '2026-09-02 03:17:23'),
(2, NULL, 2, '', 'aaaa', '2026-09-02', '13:10:00', '17:16:00', 'Time Slot', '', 'Booked', '2026-09-02 03:41:18'),
(3, NULL, 1, 'adasdf', 'faad', '2026-09-11', '00:00:00', '23:59:59', 'Full Day', 'thakurus94520@gmail.com', 'Booked', '2026-09-11 03:11:35'),
(4, NULL, 2, 'adasdf', 'rvrvf', '2026-09-11', '00:00:00', '23:59:59', 'Full Day', 'thakurus94520@gmail.com', 'Booked', '2026-09-11 05:39:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '111111', 'admin', '2026-09-11 02:42:04');

-- --------------------------------------------------------

--
-- Table structure for table `workspaces`
--

CREATE TABLE `workspaces` (
  `id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `workspace_name` varchar(100) NOT NULL,
  `type` varchar(10) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Available',
  `time_slot` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workspaces`
--

INSERT INTO `workspaces` (`id`, `company_name`, `workspace_name`, `type`, `status`, `time_slot`) VALUES
(1, 'Believ-In-Tech', 'Meeting Room 1', 'M1', 'Available', '10:00 AM - 11:00 AM'),
(2, 'xyz Technology', 'Meeting Room 2', 'M2', 'Available', '11:00 AM - 12:00 PM'),
(3, 'ABC Technologies', 'Conference Room', 'CR', 'Available', '02:00 PM - 03:00 PM');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workspace_id` (`workspace_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `workspaces`
--
ALTER TABLE `workspaces`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `workspaces`
--
ALTER TABLE `workspaces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
