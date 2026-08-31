-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 30, 2025 at 12:03 AM
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
-- Database: `student_clearance`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `ID` int(3) NOT NULL,
  `username` varchar(15) NOT NULL,
  `password` varchar(15) NOT NULL,
  `designation` varchar(25) NOT NULL,
  `fullname` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL,
  `status` varchar(10) NOT NULL,
  `photo` varchar(300) NOT NULL,
  `role` enum('department_head','library','bookstore','dormitory','cafeteria','sport','dean','police','registrar') NOT NULL DEFAULT 'department_head',
  `department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`ID`, `username`, `password`, `designation`, `fullname`, `email`, `status`, `photo`, `role`, `department`) VALUES
(4, 'admin', 'admin123', 'Admin', 'EKE, EMMANUEL EFA-EVAL', 'eva_2012@gmail.com', 'Active', 'uploads/default.jpg', 'department_head', NULL),
(5, 'nati', '04550455', 'Select Designation', 'ebisa', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', NULL),
(6, 'nahi', '04550455', 'Select Designation', 'ebisa', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', NULL),
(7, 'megersa', '04330433', 'Sport Director', 'gurmesa', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', NULL),
(8, 'nahiii', '04550455', 'Dormitory', 'nati ebis', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', NULL),
(9, 'heni', '09090909', '', 'heni', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', 'Computer Science'),
(10, 'nemoo', '12345678', '', 'nemoo', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', 'Business Management'),
(11, 'megi', '12121212', '', 'megi', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', 'Electrical Engineering'),
(12, 'buyo', '12341234', '', 'buyo', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'library', ''),
(13, 'oooo', '11111111', '', 'oooo', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'bookstore', ''),
(14, 'health', '21212121', '', 'health', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', 'Health Informatics'),
(15, 'cala', '12345656', '', 'cala tolosa beyesa', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', 'Medicine'),
(16, 'melke', '11111111', '', 'melke', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', 'Computer Science and Engineering'),
(17, 'bookstore', '11111111', '', 'bookstore', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'bookstore', ''),
(18, 'dormitory', '11111111', '', 'dormitory', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'dormitory', ''),
(19, 'cafteria', '11111111', '', 'cafteria', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'cafeteria', ''),
(20, 'sport', '11111111', '', 'sport', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'sport', ''),
(21, 'dean', '11111111', '', 'dean', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'dean', ''),
(22, 'police', '11111111', '', 'police', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'police', ''),
(23, 'registrar', '11111111', '', 'registrar', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'registrar', ''),
(0, 'meretu', '11111111', '', 'meretu', 'nationly623@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', 'Software Engineering'),
(0, 'library', '11111111', '', 'megersa', 'nahionly0455@gmail.com', 'Active', 'uploads/avatar_nick.png', 'library', ''),
(0, 'super_admin', '11111111', '', 'supr_administration', 'nahionly0455@gmail.com', 'Active', 'uploads/avatar_nick.png', '', ''),
(0, 'super_adminstra', '11111111', '', 'super_adminstration', 'nahionly0455@gmail.com', 'Active', 'uploads/avatar_nick.png', '', ''),
(0, 'system', '11111111', '', 'system', 'nahionly0455@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', 'Information System'),
(0, 'tecnology', '11111111', '', 'tecnology', 'nahionly0455@gmail.com', 'Active', 'uploads/avatar_nick.png', 'department_head', 'Information Technology');

-- --------------------------------------------------------

--
-- Table structure for table `clearance_day_control`
--

CREATE TABLE `clearance_day_control` (
  `date` date NOT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clearance_day_control`
--

INSERT INTO `clearance_day_control` (`date`, `is_open`, `updated_at`, `start_time`, `end_time`, `note`) VALUES
('2025-11-18', 0, '2025-11-29 18:15:09', NULL, NULL, NULL),
('2025-11-27', 1, '2025-11-30 05:12:31', NULL, NULL, NULL),
('2025-11-30', 1, '2025-11-30 11:50:36', '06:00:00', '11:00:00', 'please clearance request'),
('2025-12-01', 1, '2025-12-01 09:27:01', '01:00:00', '06:00:00', 'all student com to registrar'),
('2025-12-05', 1, '2025-11-30 07:45:09', NULL, NULL, NULL),
('2025-12-22', 1, '2025-12-22 18:15:17', '00:00:00', '00:00:00', ''),
('2025-12-26', 1, '2025-12-26 12:06:50', '00:00:00', '00:00:00', ''),
('2025-12-28', 1, '2025-12-28 08:45:16', '00:00:00', '00:00:00', ''),
('2025-12-29', 1, '2025-12-28 08:44:31', '00:00:00', '00:00:00', ''),
('2025-12-30', 1, '2025-12-28 08:44:17', '00:00:00', '00:00:00', ''),
('2025-12-31', 0, '2025-12-28 08:45:10', '11:45:00', '23:45:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `clearance_requests`
--

CREATE TABLE `clearance_requests` (
  `ID` int(3) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `matric_no` varchar(15) NOT NULL,
  `session` varchar(10) NOT NULL,
  `faculty` varchar(30) NOT NULL,
  `dept` varchar(44) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `photo` varchar(400) NOT NULL,
  `is_department_approved` tinyint(1) DEFAULT 0,
  `is_library_approved` tinyint(1) DEFAULT 0,
  `is_bookstore_approved` tinyint(1) DEFAULT 0,
  `is_dormitory_approved` tinyint(1) DEFAULT 0,
  `is_cafeteria_approved` tinyint(1) DEFAULT 0,
  `is_sport_approved` tinyint(1) DEFAULT 0,
  `is_dean_approved` tinyint(1) DEFAULT 0,
  `is_police_approved` tinyint(1) DEFAULT 0,
  `is_registrar_approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `ip_address`, `created_at`) VALUES
(1, 'nati', 'nahionly@gmail.com', 'afan oromo', 'good', '::1', '2025-11-29 11:57:45');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `dept` varchar(20) NOT NULL,
  `section` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `recipient_matric` varchar(32) DEFAULT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `recipient_matric`, `subject`, `message`, `created_at`) VALUES
(1, '0404', 'library', 'please come you', '2025-11-30 05:13:07'),
(2, '0404', 'library', 'please come you', '2025-11-30 05:20:06'),
(3, '0404', 'library', 'please come you', '2025-11-30 05:20:17'),
(4, '0404', 'library', 'please come you', '2025-11-30 05:20:38'),
(5, '0404', 'library', 'please come you', '2025-11-30 05:20:54'),
(6, '0404', 'library', 'please come you', '2025-11-30 05:21:03'),
(7, '0404', 'library', 'please come you', '2025-11-30 05:50:51'),
(8, NULL, 'Clearance Day Open (2025-11-30)', 'Clearance requests are open for 2025-11-30.', '2025-11-30 07:49:13'),
(9, NULL, 'Clearance Day Open (2025-11-30)', 'Clearance requests are open for 2025-11-30.', '2025-11-30 07:49:29'),
(10, NULL, 'Clearance Day Open (2025-12-01)', 'Clearance requests are open for 2025-12-01. Time: 00:00:00 - 00:00:00', '2025-11-30 07:50:06'),
(11, NULL, 'Clearance Day Open (2025-12-01)', 'Clearance requests are open for 2025-12-01. Time: 00:00:00 - 00:00:00', '2025-11-30 08:01:48'),
(12, NULL, 'Clearance Day Open (2025-11-30)', 'Clearance requests are open for 2025-11-30. Time: 00:00:00 - 00:00:00', '2025-11-30 08:06:47'),
(13, NULL, 'Clearance Day Open (2025-11-30)', 'Clearance requests are open for 2025-11-30. Time: 00:00:00 - 00:00:00', '2025-11-30 08:06:48'),
(14, NULL, 'Clearance Day Open (2025-11-30)', 'Clearance requests are open for 2025-11-30. Time: 00:00:00 - 00:00:00', '2025-11-30 08:07:34'),
(15, '7777', 'bookstore', 'camon', '2025-11-30 08:13:59'),
(16, '7777', 'bookstore', 'camon', '2025-11-30 08:14:17'),
(17, '7777', 'bookstore', 'camon', '2025-11-30 08:15:16'),
(18, NULL, 'Clearance Day Closed (2025-11-30)', 'Clearance requests are closed for 2025-11-30. Time: 00:00:00 - 00:00:00', '2025-11-30 11:47:39'),
(19, NULL, 'Clearance Day Closed (2025-11-30)', 'Clearance requests are closed for 2025-11-30. Time: 00:00:00 - 00:00:00', '2025-11-30 11:48:40'),
(20, NULL, 'Clearance Day Closed (2025-11-30)', 'Clearance requests are closed for 2025-11-30. Time: 00:00:00 - 00:00:00', '2025-11-30 11:49:04'),
(21, NULL, 'Clearance Day Open (2025-11-30)', 'Clearance requests are open for 2025-11-30. Time: 00:00:00 - 00:00:00', '2025-11-30 11:49:30'),
(22, NULL, 'Clearance Day Open (2025-11-30)', 'Clearance requests are open for 2025-11-30. Time: 06:00 - 11:00 please clearance request', '2025-11-30 11:50:36'),
(23, '', 'ALL', 'OPEN CLEARANCE', '2025-12-01 09:23:03'),
(24, '7777', 'DEAN', 'yeshewas love you', '2025-12-01 09:24:37'),
(25, '', 'sport', 'good morning student', '2025-12-01 09:25:29'),
(26, NULL, 'Clearance Day Open (2025-12-01)', 'Clearance requests are open for 2025-12-01. Time: 01:00 - 06:00 all student com to registrar', '2025-12-01 09:27:02'),
(27, '7777', 'Clearance Re‑request Allowed', 'You may submit a new clearance request. Day will be announced by Super Admin.', '2025-12-13 08:18:24'),
(28, NULL, 'Clearance Day Open (2025-12-22)', 'Clearance requests are open for 2025-12-22.', '2025-12-22 18:15:17'),
(29, NULL, 'Clearance Day Open (2025-12-26)', 'Clearance requests are open for 2025-12-26.', '2025-12-26 12:06:50'),
(30, NULL, 'Clearance Day Open (2025-12-30)', 'Clearance requests are open for 2025-12-30.', '2025-12-28 08:44:17'),
(31, NULL, 'Clearance Day Open (2025-12-29)', 'Clearance requests are open for 2025-12-29.', '2025-12-28 08:44:31'),
(32, NULL, 'Clearance Day Closed (2025-12-31)', 'Clearance requests are closed for 2025-12-31. Time: 11:45 - 23:45', '2025-12-28 08:45:11'),
(33, NULL, 'Clearance Day Closed (2025-12-28)', 'Clearance requests are closed for 2025-12-28.', '2025-12-28 08:45:12'),
(34, NULL, 'Clearance Day Open (2025-12-28)', 'Clearance requests are open for 2025-12-28. Time: 00:00:00 - 00:00:00', '2025-12-28 08:45:16');

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `ID` int(11) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `matric_no` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `session` varchar(10) NOT NULL,
  `faculty` varchar(30) NOT NULL,
  `dept` varchar(44) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `photo` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`ID`, `fullname`, `matric_no`, `password`, `session`, `faculty`, `dept`, `phone`, `photo`) VALUES
(0, 'nati ebisa feyisa', 'RU0455/14', '0455', '2019/2020', 'Informatics', 'Computer Science and Engineering', '0942435347', 'uploads/photo.jpg'),
(0, 'girma ', 'RU3732/14', 'jmlwrz', '2025/2026', 'Informatics', 'Computer Science and Engineering', '0910293442', 'uploads/avatar_nick.png'),
(0, 'CHRIS', 'RU123121', '2w98gt', '2025/2026', 'Informatics', 'Computer Science and Engineering', '0910293442', 'uploads/Screenshot 2025-10-22 134108.png'),
(0, 'feysel ibrahim', 'RU3732/13', '0455', '2019/2020', 'Informatics', 'Computer Science and Engineering', '091029344', 'uploads/Screenshot 2022-05-19 125542.png'),
(0, 'girma', '0990', 'bwx3ml', '2025/2026', 'Informatics', 'Computer Science and Engineering', '0942435347', 'uploads/avatar_nick.png');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `ID` int(3) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `matric_no` varchar(15) NOT NULL,
  `password` varchar(15) NOT NULL,
  `session` varchar(10) NOT NULL,
  `faculty` varchar(30) NOT NULL,
  `dept` varchar(44) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `photo` varchar(400) NOT NULL,
  `is_department_approved` tinyint(1) DEFAULT 0,
  `is_library_approved` tinyint(1) DEFAULT 0,
  `is_bookstore_approved` tinyint(1) DEFAULT 0,
  `is_dormitory_approved` tinyint(1) DEFAULT 0,
  `is_cafeteria_approved` tinyint(1) DEFAULT 0,
  `is_sport_approved` tinyint(1) DEFAULT 0,
  `is_dean_approved` tinyint(1) DEFAULT 0,
  `is_police_approved` tinyint(1) DEFAULT 0,
  `is_registrar_approved` tinyint(1) DEFAULT 0,
  `request_year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`ID`, `fullname`, `matric_no`, `password`, `session`, `faculty`, `dept`, `phone`, `photo`, `is_department_approved`, `is_library_approved`, `is_bookstore_approved`, `is_dormitory_approved`, `is_cafeteria_approved`, `is_sport_approved`, `is_dean_approved`, `is_police_approved`, `is_registrar_approved`, `request_year`) VALUES
(8, 'Eke Emmanuel Efa-eval', '18/132010', '11111111', '2021/2022', 'Science', 'Computer Science', '08067361023', 'uploads/eva.jpeg', 1, 1, 1, 1, 1, 1, 1, 0, 0, NULL),
(9, 'nati ebisa', '04550455', 'g7ut8e', '2021/2022', 'Engineering', 'Computer Science', '0942435347', 'uploads/avatar_nick.png', 1, 1, 1, 1, 0, 0, 0, 0, 0, NULL),
(10, 'nati ebisa', '12', 'yxrh9e', '2020/2021', 'Select faculty', 'Select Department', '0942435347', 'uploads/logo.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(11, 'pak', '16471647', '8bcun6', '2021/2022', 'Engineering', 'Computer Science', '09102934423', 'uploads/aaa.jpg', 1, 1, 1, 1, 0, 0, 0, 0, 0, NULL),
(12, 'chris', '09090909', 'j9l452', '2021/2022', 'Engineering', 'Computer Science', '0942435347', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(13, 'yehun', '12345678', '0433', '2021/2022', 'Engineering', 'Computer Science', '09102934423', 'uploads/logo.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(15, 'nati ebisa feyisa', '0455', '0455', '2021/2022', 'Social Science', 'Business Management', '09102934423', 'uploads/avatar_nick.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(16, 'nati ebis', '12321', 'tzme8w', '2020/2021', 'Engineering', 'Electrical Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 0, 0, 0, 0, NULL),
(17, 'feysel ibrahim', 'RU/0370', 'pj62en', '2020/2021', 'Engineering', 'Electrical Engineering', '09102934423', 'uploads/download.jpeg', 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(18, 'derje ', 'ru0770/14', '8wp5z2', '2021/2022', 'Science', 'Business Management', '09102934423', 'uploads/avatar_nick.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(19, 'derje ', 'ru0770', 'ewb23u', '2020/2021', 'Select faculty', 'Select Department', '09102934423', 'uploads/avatar_nick.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(20, 'derje ', 'RU/0770', 'zfmcpd', '2020/2021', 'Select faculty', 'Select Department', '09102934423', 'uploads/avatar_nick.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(21, 'nemo', 'RU0759/14', 'rnvflo', '2021/2022', 'Engineering', 'Business Management', '09102934423', 'uploads/avatar_nick.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(22, 'ooooo', '00000', '16v4g5', '2021/2022', 'Institutes', 'Health informatics ', '09102934423', 'uploads/avatar_nick.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(23, 'abdi', 'RU0413/14', 't5dwry', '2024/2025', 'Institutes', 'Medicine', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(24, 'nati ebisa feysa', 'RU0455/14', '0455', '2024/2025', 'Informatics', 'Computer Science and Engineering', '091029344', 'uploads/photo.jpg', 1, 1, 1, 1, 1, 1, 1, 1, 1, 2025),
(25, 'nati ebis', '1238', 'g80nud', '2025/2026', 'Informatics', 'Computer Science and Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(26, 'nati ebist', '87678', '201hs9', '2025/2026', 'Informatics', 'Computer Science and Engineering', '09102934423', 'uploads/photo_26_1763916788.png', 1, 1, 1, 1, 1, 0, 0, 0, 0, NULL),
(27, 'nati ebist', '8766778', 'euicvn', '2025/2026', 'Informatics', 'Computer Science and Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 0, 0, 0, 0, 0, 0, NULL),
(28, 'nemo ebist', '9808', 'noi8wg', '2025/2026', 'Informatics', 'Computer Science and Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(29, 'feyebist', '9111', 'pwuo7s', '2024/2025', 'Informatics', 'Computer Science and Engineering', '09102934423', 'uploads/Screenshot 2022-05-16 191223.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(30, 'feyebist', '09089', 'gv315q', '2025/2026', 'Informatics', 'Computer Science and Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(31, 'feye criss', '090232', 'k58nu6', '2025/2026', 'Informatics', 'Computer Science and Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(32, 'yehun', '0904', 'g8zcfl', '2025/2026', 'Informatics', 'Computer Science and Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(33, 'abel', '1111', 'tdnjm8', '2019/2020', 'Informatics', 'Software Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(34, 'fedhi', '2222', '0o7yjt', '2018/2019', 'Informatics', 'Software Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 0, 0, 0, 0, NULL),
(35, 'eba ', '3333', 'yd09b7', '2019/2020', 'Informatics', 'Software Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 0, 0, 0, 0, NULL),
(36, 'bini', '1010', 'guw21e', '2018/2019', 'Informatics', 'Software Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(37, 'bini', '4444', 'm5txqi', '2018/2019', 'Informatics', 'Computer Science and Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(38, 'nanbon', '5555', 'avw4n1', '2018/2019', 'Informatics', 'Software Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 0, 0, 0, 0, NULL),
(39, 'lami', '6666', '7it0ak', '2018/2019', 'Informatics', 'Software Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 0, 0, 0, 0, NULL),
(40, 'tola', '0009', '2c5kte', '2019/2020', 'Informatics', 'Computer Science', '09102934423', 'uploads/avatar_nick.png', 0, 1, 1, 1, 1, 0, 0, 0, 0, NULL),
(41, 'mohana', '7777', 'o028xg', '2019/2020', 'Informatics', 'Software Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 1, 1, 1, 1, 0, 0, 0, 0, NULL),
(42, 'dodo', '99990', 'zw6im1', '2019/2020', 'Informatics', 'Computer Science', '09102934423', 'uploads/avatar_nick.png', 0, 1, 1, 1, 1, 0, 0, 0, 0, NULL),
(43, 'ebisa', '0505', 'kw8ra6', '2018/2019', 'Informatics', 'Computer Science and Engineering', '09102934423', 'uploads/avatar_nick.png', 1, 0, 0, 0, 1, 0, 0, 0, 0, NULL),
(44, 'ebisa', '0404', 'j24o9d', '2018/2019', 'Informatics', 'Computer Science and Engineering', '09102934423', 'uploads/616A7408 (1).jpg', 1, 0, 0, 0, 1, 0, 0, 0, 0, NULL),
(45, 'nemomsa', '2323', '1212', '2018/2019', 'Informatics', 'Computer Science and Engineering', '09102934423', 'uploads/avatar_nick.png', 0, 0, 0, 0, 1, 0, 0, 0, 0, NULL),
(46, 'nati ebisa feyisa', 'RU04', 'ozqlj3', '2025/2026', 'Informatics', 'Computer Science and Engineering', '0942435347', 'uploads/avatar_nick.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(47, 'dean', 'RU3732/14', '11111111', '', '', '', '', 'uploads/avatar_nick.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, 2025),
(48, 'Super Administrator', '0990', 'bwx3ml', '2025/2026', 'Informatics', 'Computer Science and Engineering', '0942435347', 'uploads/avatar_nick.png', 0, 0, 0, 0, 0, 0, 0, 0, 0, 2025),
(49, 'feysel ibrahim', 'RU3732/13', '0455', '2019/2020', 'Informatics', 'Computer Science and Engineering', '091029344', 'uploads/Screenshot 2022-05-19 125542.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, 2025),
(50, 'CHRIS', 'RU123121', '2w98gt', '2025/2026', 'Informatics', 'Computer Science and Engineering', '0910293442', 'uploads/Screenshot 2025-10-22 134108.png', 1, 0, 0, 0, 0, 0, 0, 0, 0, 2025);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(64) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('clearance_open', '1', '2025-11-30 11:48:34');

-- --------------------------------------------------------

--
-- Table structure for table `tblsession`
--

CREATE TABLE `tblsession` (
  `ID` int(3) NOT NULL,
  `session` varchar(9) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblsession`
--

INSERT INTO `tblsession` (`ID`, `session`, `is_active`) VALUES
(1, '2025/2026', 0),
(2, '2019/2020', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clearance_day_control`
--
ALTER TABLE `clearance_day_control`
  ADD PRIMARY KEY (`date`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `ID` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
