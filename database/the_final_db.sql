-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2025 at 05:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `the_final_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `admin_username` varchar(50) NOT NULL,
  `admin_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_username`, `admin_password`) VALUES
(1, 'admin', '$2y$10$x9fU5VNGS8fSXyfZfYd/QORmBhz5vHNwDrw36RYDha8'),
(2, 'chan', '$2y$10$DAlTUACz9CbGeoRl06voX.YT3Jkd0y9Nw5xsIZT0KoJ'),
(3, 'chan', '$2y$10$PmXXT0b9BIdnouU4qcf4xOZnMqijJVEYMrI9S6KCsTR'),
(4, 'jimmy', '$2y$10$cOeBOT1XhBYBK1A.iEqnDuGjWepFTNigp1Ldg9Yq6Ov');

-- --------------------------------------------------------

--
-- Table structure for table `bookingstatus`
--

CREATE TABLE `bookingstatus` (
  `bookingStatus_id` int(11) NOT NULL,
  `bookingStatus_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookingstatus`
--

INSERT INTO `bookingstatus` (`bookingStatus_id`, `bookingStatus_name`) VALUES
(1, 'Pending'),
(2, 'Approved'),
(3, 'Cancelled'),
(4, 'Rejected'),
(5, 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `branch_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `branch_image` varchar(150) DEFAULT NULL,
  `branch_name` varchar(100) NOT NULL,
  `branch_address` varchar(150) DEFAULT NULL,
  `branch_number` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`branch_id`, `owner_id`, `branch_image`, `branch_name`, `branch_address`, `branch_number`, `created_at`, `updated_at`) VALUES
(1, 1, 'branch_6825d4bf0c9414.40532990.jpg', 'OwnerBranch', 'San Jose Malaybalay City', '09908778655', '2025-05-21 10:58:26', '2025-05-21 10:58:26'),
(2, 2, 'branch_6825d87d214a55.13707347.jpg', 'Jobert Rental', 'Kalasungay Malaybalay City', '09112543678', '2025-05-21 10:58:26', '2025-05-21 10:58:26'),
(3, 3, 'branch_6825dd585e8585.11517641.jpg', 'Jandyll Rental', 'Sumpong Malaybalay City', '09675473854', '2025-05-21 10:58:26', '2025-05-21 10:58:26'),
(4, 4, 'branch_6825e14a0b7111.76005285.jpg', 'Joseph Rental', 'Laguitas Malaybalay City', '09997567346', '2025-05-21 10:58:26', '2025-05-21 10:58:26'),
(5, 5, 'branch_6825e5115d32b2.26996631.jpg', 'Jane Rental', 'San Jose Malaybalay City', '09123657845', '2025-05-21 10:58:26', '2025-05-21 10:58:26'),
(6, 6, NULL, 'Julian Branch', NULL, NULL, '2025-05-21 10:58:26', '2025-05-21 10:58:26'),
(7, 7, NULL, 'Abay', NULL, NULL, '2025-05-21 10:58:26', '2025-05-21 10:58:26'),
(8, 8, 'branch_682ae39f936456.24177059.png', 'Wenwen Rental', 'Sumpong Malaybalay City', '09987864364', '2025-05-21 10:58:26', '2025-05-21 10:58:26'),
(9, 9, 'branch_682ae4141030c4.72121771.png', 'Jacob Rental', 'Casisang Malaybalay City', '09474653273', '2025-05-21 10:58:26', '2025-05-21 10:58:26'),
(10, 10, 'branch_682ae49d0de8c1.34100486.png', 'Dave Rental', 'Barangay 2, Malaybalay City', '09688745344', '2025-05-21 10:58:26', '2025-05-21 10:58:26'),
(11, 11, 'branch_682ae56c5ef179.74427882.png', 'Coco Rental', 'San Jose Malaybalay City', '09878664534', '2025-05-21 10:58:26', '2025-05-21 10:58:26'),
(12, 12, 'branch_682ae5c158d6a4.39150653.png', 'Mocha Rentals', 'Aglayan Malaybalay City', '09865746375', '2025-05-21 10:58:26', '2025-05-21 10:58:26'),
(13, 13, NULL, 'Prexs Rental', NULL, NULL, '2025-05-21 10:58:26', '2025-05-21 10:58:26');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `car_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `car_description` varchar(150) DEFAULT NULL,
  `carType_id` int(11) NOT NULL,
  `car_model` varchar(100) NOT NULL,
  `transmissionType_id` int(11) NOT NULL,
  `AC` enum('Yes','No') DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `fuelType_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `car_status` enum('Available','Not Available') DEFAULT 'Available',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`car_id`, `branch_id`, `car_description`, `carType_id`, `car_model`, `transmissionType_id`, `AC`, `capacity`, `fuelType_id`, `price`, `car_status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Nice car for traveling.', 1, 'Kia Soluto', 1, 'Yes', 5, 1, 1500.00, 'Not Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(2, 1, 'Nice car with affordable price.', 1, 'Nissan Almera', 1, 'Yes', 4, 1, 1500.00, 'Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(3, 1, 'A car that is both good in style and functionality.', 1, 'Suzuki Dzire', 1, 'Yes', 5, 1, 1200.00, 'Not Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(4, 1, 'A car that is flashy.', 1, 'Toyota Vios', 1, 'Yes', 5, 2, 1300.00, 'Not Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(5, 1, 'Well maintain car.', 1, 'Honda City', 1, 'Yes', 4, 1, 1500.00, 'Not Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(6, 2, 'A nice car for travelers. ', 1, 'MG 5', 1, 'Yes', 4, 1, 1500.00, 'Not Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(7, 2, 'A grey flashy car for travelling.', 1, 'Mitsubishi Mirage G4', 1, 'Yes', 4, 1, 1400.00, 'Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(8, 2, 'Want a car that can cater your needs?', 1, 'Toyota Corolla Altis', 1, 'Yes', 4, 1, 1300.00, 'Not Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(9, 2, 'A car that good for family vacation.', 2, 'Honda BR-V', 1, 'Yes', 5, 2, 1400.00, 'Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(10, 2, 'Good for outing/traveling. ', 2, 'Isuzu mu-X', 1, 'Yes', 5, 2, 1500.00, 'Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(11, 3, 'A car that is good for adventure.', 2, 'Mitsubishi Montero Sport', 1, 'Yes', 5, 2, 1600.00, 'Not Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(12, 3, 'A reliable and fuel-efficient vehicle, perfect for city drives and long trips. Features a comfortable interior, smooth handling, and modern design.', 2, 'Audi Q3', 1, 'Yes', 5, 1, 1300.00, 'Not Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(13, 3, 'A compact and stylish car ideal for daily commuting, offering great mileage and easy maneuverability.', 2, 'Audi Q8 e-Tron SUV', 1, 'Yes', 5, 2, 1500.00, 'Not Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(14, 3, 'Spacious SUV with advanced safety features, powerful performance, and comfortable seating for the whole family.', 2, 'Nissan Terra', 1, 'Yes', 5, 1, 1500.00, 'Not Available', '2025-05-21 10:58:55', '2025-05-21 11:04:11'),
(15, 3, 'Family-friendly van with flexible seating, ample storage, and smooth highway performance.', 2, 'Toyota Fortuner', 1, 'Yes', 5, 1, 1600.00, 'Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(16, 4, 'Elegant and aerodynamic, this car blends efficiency with a touch of class.', 2, 'Toyota Rush Promos', 1, 'Yes', 5, 1, 1600.00, 'Not Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(17, 4, 'Spacious van perfect for family trips, offering comfort and room for everyone.', 3, 'Honda Odyssey', 2, 'Yes', 6, 2, 1700.00, 'Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(18, 5, 'Reliable passenger van with flexible seating and smooth highway performance.', 3, 'Hyundai Grand Starex', 2, 'Yes', 6, 1, 1700.00, 'Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(19, 5, 'Designed for long drives, this van combines storage space with comfortable interiors.', 3, 'Toyota Hiace', 2, 'Yes', 10, 1, 1700.00, 'Available', '2025-05-21 10:58:55', '2025-05-21 10:58:55'),
(20, 3, 'A car that is suited for travelling with family.', 3, 'Nissan Urvan', 2, 'Yes', 8, 2, 1300.00, 'Available', '2025-05-21 11:08:57', '2025-05-21 11:08:57');

-- --------------------------------------------------------

--
-- Table structure for table `cartype`
--

CREATE TABLE `cartype` (
  `carType_id` int(11) NOT NULL,
  `carType_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cartype`
--

INSERT INTO `cartype` (`carType_id`, `carType_name`) VALUES
(1, 'Sedan'),
(2, 'SUV'),
(3, 'Van');

-- --------------------------------------------------------

--
-- Table structure for table `car_images`
--

CREATE TABLE `car_images` (
  `carImages_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `carImages` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_images`
--

INSERT INTO `car_images` (`carImages_id`, `car_id`, `carImages`, `uploaded_at`) VALUES
(9, 2, '12.webp', '2025-05-15 13:49:36'),
(10, 2, '22.webp', '2025-05-15 13:49:36'),
(11, 2, '32.webp', '2025-05-15 13:49:36'),
(12, 2, '42.webp', '2025-05-15 13:49:36'),
(13, 1, '1.webp', '2025-05-15 13:51:00'),
(14, 1, '2.webp', '2025-05-15 13:51:00'),
(15, 1, '3.webp', '2025-05-15 13:51:00'),
(16, 1, '4.webp', '2025-05-15 13:51:00'),
(17, 3, '13.webp', '2025-05-15 13:53:50'),
(18, 3, '23.webp', '2025-05-15 13:53:50'),
(19, 3, '33.webp', '2025-05-15 13:53:50'),
(20, 3, '43.webp', '2025-05-15 13:53:50'),
(21, 4, '14.webp', '2025-05-15 13:55:29'),
(22, 4, '24.webp', '2025-05-15 13:55:29'),
(23, 4, '44.webp', '2025-05-15 13:55:29'),
(24, 4, '34.webp', '2025-05-15 13:55:29'),
(25, 5, '15.webp', '2025-05-15 13:58:02'),
(26, 5, '25.webp', '2025-05-15 13:58:02'),
(27, 5, '35.webp', '2025-05-15 13:58:02'),
(28, 5, '45.webp', '2025-05-15 13:58:02'),
(29, 6, '16.webp', '2025-05-15 14:03:53'),
(30, 6, '26.webp', '2025-05-15 14:03:53'),
(31, 6, '36.webp', '2025-05-15 14:03:53'),
(32, 6, '46.webp', '2025-05-15 14:03:53'),
(33, 7, '17.webp', '2025-05-15 14:08:38'),
(34, 7, '27.webp', '2025-05-15 14:08:38'),
(35, 7, '37.webp', '2025-05-15 14:08:38'),
(36, 7, '47.webp', '2025-05-15 14:08:38'),
(37, 8, '18.webp', '2025-05-15 14:10:43'),
(38, 8, '28.webp', '2025-05-15 14:10:43'),
(39, 8, '38.webp', '2025-05-15 14:10:43'),
(40, 8, '48.webp', '2025-05-15 14:10:43'),
(41, 9, '19.avif', '2025-05-15 14:13:42'),
(42, 9, '29.avif', '2025-05-15 14:13:42'),
(43, 9, '49.avif', '2025-05-15 14:13:42'),
(44, 9, '39.avif', '2025-05-15 14:13:42'),
(45, 10, '110.webp', '2025-05-15 14:16:57'),
(46, 10, '210.webp', '2025-05-15 14:16:57'),
(47, 10, '310.webp', '2025-05-15 14:16:57'),
(48, 10, '410.webp', '2025-05-15 14:16:57'),
(49, 11, '111.webp', '2025-05-15 14:21:42'),
(50, 11, '211.webp', '2025-05-15 14:21:42'),
(51, 11, '311.webp', '2025-05-15 14:21:42'),
(52, 11, '411.webp', '2025-05-15 14:21:42'),
(53, 12, '112.webp', '2025-05-15 14:24:17'),
(54, 12, '212.webp', '2025-05-15 14:24:17'),
(55, 12, '312.webp', '2025-05-15 14:24:17'),
(56, 12, '412.webp', '2025-05-15 14:24:17'),
(57, 13, '113.webp', '2025-05-15 14:26:04'),
(58, 13, '213.webp', '2025-05-15 14:26:04'),
(59, 13, '313.webp', '2025-05-15 14:26:04'),
(60, 13, '413.webp', '2025-05-15 14:26:04'),
(61, 14, '114.webp', '2025-05-15 14:27:34'),
(62, 14, '314.webp', '2025-05-15 14:27:34'),
(63, 14, '414.webp', '2025-05-15 14:27:34'),
(64, 14, '214.webp', '2025-05-15 14:27:34'),
(65, 15, '115.webp', '2025-05-15 14:29:13'),
(66, 15, '215.webp', '2025-05-15 14:29:13'),
(67, 15, '315.webp', '2025-05-15 14:29:13'),
(68, 15, '415.webp', '2025-05-15 14:29:13'),
(69, 16, '116.avif', '2025-05-15 14:33:58'),
(70, 16, '216.avif', '2025-05-15 14:33:58'),
(71, 16, '316.avif', '2025-05-15 14:33:58'),
(72, 16, '416.avif', '2025-05-15 14:33:58'),
(73, 17, '117.avif', '2025-05-15 14:41:59'),
(74, 17, '217.avif', '2025-05-15 14:41:59'),
(75, 17, '317.avif', '2025-05-15 14:41:59'),
(76, 17, '417.avif', '2025-05-15 14:41:59'),
(77, 18, '118.webp', '2025-05-15 14:44:22'),
(78, 18, '218.avif', '2025-05-15 14:44:22'),
(79, 18, '318.avif', '2025-05-15 14:44:22'),
(80, 18, '418.avif', '2025-05-15 14:44:22'),
(81, 19, '119.avif', '2025-05-15 14:46:12'),
(82, 19, '219.avif', '2025-05-15 14:46:12'),
(83, 19, '319.avif', '2025-05-15 14:46:12'),
(84, 19, '419.avif', '2025-05-15 14:46:12'),
(85, 20, '1n.avif', '2025-05-21 03:08:57'),
(86, 20, '2n.avif', '2025-05-21 03:08:57'),
(87, 20, '3n.avif', '2025-05-21 03:08:57'),
(88, 20, '4n.avif', '2025-05-21 03:08:57');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `customer_image` varchar(255) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_username` varchar(50) NOT NULL,
  `customer_emailAddress` varchar(255) NOT NULL,
  `customer_password` varchar(255) NOT NULL,
  `customer_age` int(11) DEFAULT NULL,
  `customer_gender` enum('Male','Female') DEFAULT NULL,
  `customer_birthdate` date DEFAULT NULL,
  `customer_contactNumber` varchar(20) DEFAULT NULL,
  `customer_address` varchar(150) DEFAULT NULL,
  `reset_code` varchar(6) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_image`, `customer_name`, `customer_username`, `customer_emailAddress`, `customer_password`, `customer_age`, `customer_gender`, `customer_birthdate`, `customer_contactNumber`, `customer_address`, `reset_code`, `zip_code`, `created_at`, `updated_at`) VALUES
(1, '6825d58cca494_userprofile.jpeg', 'User Name', 'user', 'user81@gamil.com', '$2y$10$2WVC3sXWOtkO23C/JVQhLOTgfK0jLlPlhuUvNc4pKyehwUexg5Vyu', 20, 'Male', '2004-01-01', '09758336768', 'Casisang Malaybalay City', NULL, '8700', '2025-05-21 10:56:45', '2025-05-21 10:56:45'),
(2, '682d412a9474a_WIN_20250307_18_27_33_Pro.jpg', 'Christian Principe', 'Chanchan', 'cprincipe83@gmail.com', '$2y$10$BHxbwoqOLliaw6A4QS0YKeD0pLauCMUkt2zPVrohn9F5GHWLc37dW', 21, 'Male', '2004-05-21', '09758336768', 'San Jose Malaybalay CIty', NULL, '8700', '2025-05-21 10:56:45', '2025-05-21 10:57:46'),
(3, '6825ead742618_jimmy.jpg', 'Jimmy Pingcas', 'Jim', 'jimmy12@gmail.com', '$2y$10$685D41qscG9Lb6NzgvrLv.KJR/WkYi0TEKdv3UOcXOf6fNAC2TuEm', 21, 'Male', '2004-01-01', '09104340099', 'Dalwangan Malaybalay City', NULL, '8700', '2025-05-21 10:56:45', '2025-05-21 10:56:45'),
(4, '6825eb74e5290_jib.jpg', 'Jhib Lourence', 'jhib', 'jhib10@gmail.com', '$2y$10$sCbWBzFyeVgHn2IQolnMDeZ5kwOIne93fh5Z16H8kn0YR9manIpCO', 22, 'Male', '2004-01-01', '', 'Dalwangan Malaybalay City', NULL, '', '2025-05-21 10:56:45', '2025-05-21 10:56:45'),
(5, '6825e805e1359_girl.jpg', 'Natalie Amoc', 'nat', 'natalie55@gmail.com', '$2y$10$sVpN2g/j3O8kGTJrvN.Buefat2fcT4miln.s3GiqoUdnzsrvetwmO', 25, 'Female', '2004-01-01', '09104340456', 'Casisang Malaybalay City', NULL, '8700', '2025-05-21 10:56:45', '2025-05-21 10:56:45'),
(8, '682adbdd16651_chanprof.jpg', 'Chan Chan', '', 'chantoy192004@gmail.com', '', 24, 'Male', '1999-02-18', NULL, 'Casisang Malaybalay City', NULL, NULL, '2025-05-21 10:56:45', '2025-05-21 10:56:45'),
(9, 'https://lh3.googleusercontent.com/a/ACg8ocJTAe2lG4FH0feejd16zeWSA06fuCYIbOFmyIUmIYQ-lFbnGw=s96-c', 'DOMIcheytac NOBLEGOLD', '', 'dominoblegold@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-21 10:56:45', '2025-05-21 10:56:45'),
(10, 'https://lh3.googleusercontent.com/a/ACg8ocKSrjoFe6ud71hid9Be3lUIYlGhOxtodWuspMe70yxjKlWG7A=s96-c', 'Z shadow Shadow', '', 'zshadowshadow00@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-21 10:56:45', '2025-05-21 10:56:45'),
(11, 'https://lh3.googleusercontent.com/a/ACg8ocLpJ4rs5SKk1M91E7t2Zka0UNUA_8Wwy4D_66UJXyIZSl0Npg=s96-c', 'Rosalina Rendon', '', 'jhibjhib7@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-21 10:56:45', '2025-05-21 10:56:45'),
(12, 'https://lh3.googleusercontent.com/a/ACg8ocLdzvLSpN53zf9lEGoRFyKHfqdhdKdMi_TfwMlyBDwTUg4rOQ=s96-c', 'Jhib Rendon', '', 'jhibrendon@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-21 10:56:45', '2025-05-21 10:56:45'),
(13, 'https://lh3.googleusercontent.com/a/ACg8ocJdh0K99QSqooClrc4Ft8E_D-QLAvjGDVg0E40bQAwsAq8fjw=s96-c', 'Christian Jay Principe', '', '2301104738@student.buksu.edu.ph', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-21 10:56:45', '2025-05-21 10:56:45');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `drivers_id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `driver_name` varchar(100) NOT NULL,
  `drivers_age` int(11) DEFAULT NULL,
  `driverslicense_number` varchar(30) DEFAULT NULL,
  `driverlicense_image` varchar(150) DEFAULT NULL,
  `drivers_contactNumber` varchar(20) DEFAULT NULL,
  `proofOfResidency_id` int(11) DEFAULT NULL,
  `driverType_id` int(11) DEFAULT NULL,
  `drivers_price` decimal(10,2) DEFAULT NULL,
  `drivers_picture` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`drivers_id`, `owner_id`, `customer_id`, `driver_name`, `drivers_age`, `driverslicense_number`, `driverlicense_image`, `drivers_contactNumber`, `proofOfResidency_id`, `driverType_id`, `drivers_price`, `drivers_picture`, `created_at`, `updated_at`) VALUES
(2, 1, NULL, 'Jhon Loyd', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825d107afba6_driver 1.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(3, 1, NULL, 'Joey', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825d13de5c4c_driver 2.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(4, 1, NULL, 'Stephen', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825d15084c81_driver 3.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(5, 1, NULL, 'Mark', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825d1a26c8a6_driver 4.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(6, 1, NULL, 'Florence', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825d1b49a294_driver 5.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(7, 2, NULL, 'James', NULL, NULL, NULL, NULL, NULL, 1, 999.00, '6825d9aee0820_ddriver 1.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(8, 2, NULL, 'Bryan', NULL, NULL, NULL, NULL, NULL, 1, 999.00, '6825d9c6561ea_ddriver 2.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(9, 2, NULL, 'Gordon', NULL, NULL, NULL, NULL, NULL, 1, 999.00, '6825d9ef4b0ad_ddriver 3.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(10, 2, NULL, 'Juan', NULL, NULL, NULL, NULL, NULL, 1, 999.00, '6825da0a3a7eb_ddriver 4.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(11, 2, NULL, 'Kyle', NULL, NULL, NULL, NULL, NULL, 1, 999.00, '6825da3118e11_ddriver 5.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(12, 3, NULL, 'Jimboy', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825dec0dd38d_1driver1.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(13, 3, NULL, 'Cris', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825ded276f56_2driver2.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(14, 3, NULL, 'Fretz', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825dee558604_3driver3.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(15, 3, NULL, 'Mike', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825def820e64_4driver4.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(16, 3, NULL, 'Mico', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825df094cc10_5driver5.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(17, 4, NULL, 'Ruel', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825e2bda7ab5_b3driver1.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(18, 4, NULL, 'Lourie', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825e2d8000d6_b3driver2.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(19, 4, NULL, 'Lonei', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825e2ea00025_b3driver3.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(20, 4, NULL, 'Belly', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825e309d1488_b3driver4.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(21, 4, NULL, 'Nel', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825e31a83258_b3driver5.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(22, 5, NULL, 'Jomar', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825e68f5d776_ldriver1.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(23, 5, NULL, 'Leo', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825e6a006b99_ldriver2.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(24, 5, NULL, 'Randy', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825e6b1d0961_ldriver3.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(25, 5, NULL, 'Carlos', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825e6c09007f_ldriver4.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(26, 5, NULL, 'Michael', NULL, NULL, NULL, NULL, NULL, 1, 1000.00, '6825e6fa91f02_ldriver5.jpg', '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(28, NULL, 1, 'Johny Bless', 21, 'S123-4567-8901', '../uploads/driverli.jpg', '09758336768', 2, 2, NULL, NULL, '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(29, NULL, 4, 'Drake Bless', 0, 'S123-4567-9090', '../uploads/driverli.jpg', '09758336768', 3, 2, NULL, NULL, '2025-05-21 10:59:18', '2025-05-21 10:59:18'),
(30, NULL, 2, 'Drake Bless', 21, 'S123-4567-9090', '../uploads/driverli.jpg', '09758336768', 4, 2, NULL, NULL, '2025-05-21 10:59:18', '2025-05-21 10:59:18');

-- --------------------------------------------------------

--
-- Table structure for table `driverstype`
--

CREATE TABLE `driverstype` (
  `driversType_id` int(11) NOT NULL,
  `driversType_name` enum('Self-Drive','Acting Driver') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driverstype`
--

INSERT INTO `driverstype` (`driversType_id`, `driversType_name`) VALUES
(1, 'Acting Driver'),
(2, 'Self-Drive');

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `fee_id` int(11) NOT NULL,
  `fee_name` varchar(100) NOT NULL,
  `fee_amount` decimal(10,2) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`fee_id`, `fee_name`, `fee_amount`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 'Delivery Fee', 100.00, 1, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(2, 'Damage Fee', 100.00, 1, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(3, 'Late Return Fee', 100.00, 1, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(4, 'Cleaning Fee', 100.00, 1, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(5, 'Fuel Charge', 100.00, 1, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(6, 'Late fee', 100.00, 2, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(7, 'Delivery Fee', 100.00, 2, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(8, 'Insurance Fee', 100.00, 2, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(9, 'Fuel Charges', 100.00, 2, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(10, 'Mileage Fee', 100.00, 2, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(11, 'Late Return Fee', 100.00, 3, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(12, 'Delivery Fee', 100.00, 3, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(13, 'Smoking Fee', 100.00, 3, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(14, 'Damage Fee', 100.00, 3, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(15, 'Refueling Fee', 100.00, 3, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(16, 'Young Driver Fee', 100.00, 4, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(17, 'Delivery Fee', 100.00, 4, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(18, 'Cleaning Fee', 100.00, 4, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(19, 'Mileage Fee', 100.00, 4, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(20, 'Smoking Fee', 100.00, 4, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(21, 'Late Return Fee', 100.00, 5, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(22, 'Delivery Fee', 100.00, 5, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(23, 'Damage Fee', 100.00, 5, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(24, 'Extra Mileage Fee', 100.00, 5, '2025-05-21 10:59:35', '2025-05-21 10:59:35'),
(25, 'Smoking Fee', 100.00, 5, '2025-05-21 10:59:35', '2025-05-21 10:59:35');

-- --------------------------------------------------------

--
-- Table structure for table `fueltype`
--

CREATE TABLE `fueltype` (
  `fuelType_id` int(11) NOT NULL,
  `fuelType_name` enum('Gasoline','Diesel') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fueltype`
--

INSERT INTO `fueltype` (`fuelType_id`, `fuelType_name`) VALUES
(1, 'Diesel'),
(2, 'Gasoline');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL,
  `location_delivery` varchar(100) DEFAULT NULL,
  `location_return` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `location_delivery`, `location_return`) VALUES
(1, 'San Jose Malaybalay City', 'Casisang Malaybalay City'),
(2, 'Kalasungay Malaybalay City', 'Kalasungay Malaybalay City'),
(3, 'Sumpong Malaybalay City', 'Sumpong Malaybalay City'),
(4, 'Laguitas Malaybalay City', 'Laguitas Malaybalay City'),
(5, 'San Jose Malaybalay City', 'San Jose Malaybalay City');

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `owner_id` int(11) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `owner_username` varchar(50) NOT NULL,
  `owner_emailAddress` varchar(100) NOT NULL,
  `owner_password` varchar(255) NOT NULL,
  `owner_businessPermit` varchar(100) DEFAULT NULL,
  `reset_code` varchar(6) DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`owner_id`, `owner_name`, `owner_username`, `owner_emailAddress`, `owner_password`, `owner_businessPermit`, `reset_code`, `approval_status`, `admin_id`, `created_at`, `updated_at`) VALUES
(1, 'Owner Name', 'owner', 'user81@gamil.com', '$2y$10$Gh15mvuR9Y8mHFCzfU6LNO2LCvlPhNMmShAEjLCwB/79w3OLw0LjO', '202412345', NULL, 'approved', 3, '2025-05-21 10:59:59', '2025-05-21 10:59:59'),
(2, 'Jobert Cruz', 'Jobert', 'unodos2354@gmail.com', '$2y$10$dbFfspZS5sxIc.YM1gLEFOwfvxCKa32TtnaZjzXnjsPhHnhf3xmVi', '12345', NULL, 'approved', 2, '2025-05-21 10:59:59', '2025-05-21 10:59:59'),
(3, 'Jandyll valdez', 'janjan', 'chanchan83@gmail.com', '$2y$10$pe5A9wnFCCrAx./z6rtRNuHkpZTH61K9TOfr7/5ailb.Gfbg5KVDC', '09876', NULL, 'approved', 2, '2025-05-21 10:59:59', '2025-05-21 10:59:59'),
(4, 'Joseph Denver', 'seph', 'joseph12@gmail.com', '$2y$10$zXjtFMTlSp/jtLPE1uNe4O5cEtprx46/j7Iqa6j2F3uL9JcYb5kqS', '56743', NULL, 'approved', 4, '2025-05-21 10:59:59', '2025-05-21 10:59:59'),
(5, 'Jane Lastimosa', 'jane', 'jane66@gmail.com', '$2y$10$URi4sC6pB71UA2mCc3EIXOmZwT90GUHjr/WW8Xm5tGQj.0lqQoygy', '90087', NULL, 'approved', 4, '2025-05-21 10:59:59', '2025-05-21 10:59:59'),
(6, 'Julian  Reyes', 'Juls', 'julian19@gmail.com', '$2y$10$XHzUfCHEfKLQGH12JZVuOOBB2j8qeMxh0Uo/9kxCDkMGfDCJdrgnO', '98675', NULL, 'rejected', 2, '2025-05-21 10:59:59', '2025-05-21 10:59:59'),
(7, 'Abay Abay', 'Abay', 'Abay83@gmail.com', '$2y$10$RZoYuvebEsshlSVwu93DAey7/f4tCBn5lV6oiZcd0HoyoeF.HCw92', '09675', NULL, 'rejected', 1, '2025-05-21 10:59:59', '2025-05-21 10:59:59'),
(8, 'Wen Wen', 'Wenwensapsap', 'wenwensapsap@gmail.com', '$2y$10$8DLu7Yw/cKwtvjtrOB0Kc.x/pbNl3ufN9gCZM7uHPaT1vINWh5tzy', '59324', NULL, 'approved', 1, '2025-05-21 10:59:59', '2025-05-21 10:59:59'),
(9, 'Jacob Bryan', 'jacob', 'jacobjimimah@gmail.com', '$2y$10$c1S3.VL71jEV5b2ldYTdj.RIgMDoI/EQpB2PQPI1PqXODz4us/apO', '12745', NULL, 'approved', 1, '2025-05-21 10:59:59', '2025-05-21 10:59:59'),
(10, 'Dave Rhenzo', 'dave', 'daverhenzo@gmail.com', '$2y$10$/6evKMJF5N2u.bevVX5Xc.zwHAhKlyqCBzO7ZPHM90SzTavINoMKS', '03147', NULL, 'approved', 4, '2025-05-21 10:59:59', '2025-05-21 10:59:59'),
(11, 'Coco Martin', 'cocoys', 'hachachakdog@gmail.com', '$2y$10$z8QWvl0bYbfp7WJbJmaTDu9SJ8Ce7tg98kEY5WdzBSlpD6iauMEsO', '81267', NULL, 'approved', 1, '2025-05-21 10:59:59', '2025-05-21 10:59:59'),
(12, 'Mocha Reyes', 'mocha', 'zshadowshadow00@gmail.com', '$2y$10$ksEGmxj1f6Mbdt4ra5ngU.9O7.CjLEijH5kbvM1REhkzQ1YdpsFaC', '03258', NULL, 'approved', 3, '2025-05-21 10:59:59', '2025-05-21 10:59:59'),
(13, 'Jan Prexs', 'prexs', 'prexs76@gmail.com', '$2y$10$PcyqvsET08CK1qcZl72q4.jPfTtzBak3sIDgRcvwPOIK5KbH1Rx/W', '03228', NULL, 'rejected', 1, '2025-05-21 10:59:59', '2025-05-21 10:59:59');

-- --------------------------------------------------------

--
-- Table structure for table `proofofresidency`
--

CREATE TABLE `proofofresidency` (
  `proofOfResidency_id` int(11) NOT NULL,
  `proofOfResidency_image` varchar(255) NOT NULL,
  `proofOfResidency_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proofofresidency`
--

INSERT INTO `proofofresidency` (`proofOfResidency_id`, `proofOfResidency_image`, `proofOfResidency_name`) VALUES
(1, '../uploads/waterb.png', 'Water Bill'),
(2, '../uploads/waterb.png', 'Water Bill'),
(3, '../uploads/waterb.png', 'Water Bill'),
(4, '../uploads/waterb.png', 'Water Bill');

-- --------------------------------------------------------

--
-- Table structure for table `rentalperiods`
--

CREATE TABLE `rentalperiods` (
  `rentalPeriod_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `return_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentalperiods`
--

INSERT INTO `rentalperiods` (`rentalPeriod_id`, `start_date`, `return_date`, `start_time`, `return_time`) VALUES
(1, '2025-05-15', '2025-05-16', '22:50:00', '22:50:00'),
(2, '2025-05-15', '2025-05-16', '23:01:00', '23:01:00'),
(3, '2025-05-15', '2025-05-17', '23:16:00', '23:16:00'),
(4, '2025-05-15', '2025-05-23', '23:21:00', '23:21:00'),
(5, '2025-05-15', '2025-05-29', '23:24:00', '23:24:00'),
(6, '2025-05-16', '2025-05-17', '09:08:00', '09:08:00'),
(7, '2025-05-16', '2025-05-17', '09:30:00', '09:30:00'),
(8, '2025-05-16', '2025-05-17', '10:01:00', '10:01:00'),
(9, '2025-05-16', '2025-05-17', '21:23:00', '21:23:00'),
(10, '2025-05-16', '2025-05-19', '21:26:00', '21:26:00'),
(11, '2025-05-15', '2025-05-29', '21:35:00', '21:35:00'),
(12, '2025-05-16', '2025-05-17', '21:39:00', '21:39:00'),
(13, '2025-05-16', '2025-05-17', '21:46:00', '21:46:00'),
(14, '2025-05-18', '2025-05-19', '09:00:00', '09:00:00'),
(15, '2025-05-18', '2025-05-19', '15:22:00', '15:22:00'),
(16, '2025-05-18', '2025-05-19', '16:20:00', '17:20:00'),
(17, '2025-05-19', '2025-05-20', '17:37:00', '17:37:00'),
(18, '2025-05-19', '2025-05-20', '17:50:00', '17:50:00'),
(19, '2025-05-19', '2025-05-20', '18:15:00', '18:15:00'),
(20, '2025-05-19', '2025-05-20', '19:34:00', '19:34:00'),
(21, '2025-05-19', '2025-05-20', '09:01:00', '09:01:00'),
(22, '2025-05-21', '2025-05-22', '11:03:00', '11:03:00');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `rental_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `drivers_id` int(11) DEFAULT NULL,
  `rentalType_id` int(11) NOT NULL,
  `locations_id` int(11) DEFAULT NULL,
  `rentalPeriod_id` int(11) NOT NULL,
  `estimated_total` decimal(10,2) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `bookingStatus_id` int(11) NOT NULL,
  `number_person` int(11) DEFAULT NULL,
  `reviewed` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`rental_id`, `customer_id`, `car_id`, `drivers_id`, `rentalType_id`, `locations_id`, `rentalPeriod_id`, `estimated_total`, `booking_date`, `booking_time`, `bookingStatus_id`, `number_person`, `reviewed`, `created_at`, `updated_at`) VALUES
(2, 2, 5, 22, 1, 1, 1, 2700.00, '2025-05-15', '22:58:50', 5, 5, 1, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(3, 1, 9, 28, 2, 2, 2, 1500.00, '2025-05-15', '23:08:57', 5, 6, 1, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(4, 3, 13, 12, 2, 3, 3, 2700.00, '2025-05-15', '23:18:41', 5, 6, 1, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(5, 4, 16, 29, 2, 4, 4, 2400.00, '2025-05-15', '23:23:05', 5, 6, 1, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(6, 5, 18, 22, 1, 1, 5, 4200.00, '2025-05-15', '23:25:42', 5, 7, 1, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(7, 5, 19, 22, 2, 5, 6, 2800.00, '2025-05-16', '09:09:36', 1, 7, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(8, 5, 1, 2, 2, 5, 7, 2600.00, '2025-05-16', '09:31:19', 5, 7, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(9, 5, 5, 2, 2, 5, 8, 2600.00, '2025-05-16', '10:02:18', 3, 7, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(10, 2, 4, 2, 2, 5, 9, 4400.00, '2025-05-16', '21:25:03', 1, 6, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(11, 2, 1, 2, 2, 5, 10, 2800.00, '2025-05-16', '21:34:40', 1, 7, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(12, 2, 19, 22, 1, 1, 11, 5200.00, '2025-05-16', '21:39:13', 5, 7, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(13, 2, 16, 17, 2, 4, 12, 3700.00, '2025-05-16', '21:46:25', 1, 10, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(14, 2, 6, 9, 2, 2, 13, 3598.00, '2025-05-16', '21:59:07', 5, 10, 1, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(15, 2, 6, 11, 1, 1, 14, 2699.00, '2025-05-18', '09:01:11', 1, 5, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(16, 4, 11, 14, 2, 3, 15, 2700.00, '2025-05-18', '15:23:57', 1, 5, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(17, 4, 12, 12, 1, 1, 16, 3520.00, '2025-05-18', '16:27:28', 3, 0, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(18, 2, 8, 30, 2, 2, 17, 1400.00, '2025-05-19', '17:38:46', 2, 5, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(19, 1, 3, 4, 2, 5, 18, 2300.00, '2025-05-19', '17:50:31', 1, 5, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(20, 1, 13, 16, 1, 1, 19, 3700.00, '2025-05-19', '18:15:56', 1, 6, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(21, 1, 5, 3, 2, 5, 20, 2600.00, '2025-05-19', '19:35:49', 1, 6, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(22, 2, 5, 2, 1, 1, 21, 2700.00, '2025-05-20', '09:03:30', 3, 6, 0, '2025-05-21 11:02:41', '2025-05-21 11:02:41'),
(23, 2, 14, 15, 2, 3, 22, 2600.00, '2025-05-21', '11:04:11', 3, 7, 0, '2025-05-21 11:04:11', '2025-05-21 11:05:04');

-- --------------------------------------------------------

--
-- Table structure for table `rentaltype`
--

CREATE TABLE `rentaltype` (
  `rentalType_id` int(11) NOT NULL,
  `rentalType_name` enum('Delivery','Self-Pickup') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentaltype`
--

INSERT INTO `rentaltype` (`rentalType_id`, `rentalType_name`) VALUES
(1, 'Delivery'),
(2, 'Self-Pickup');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `rental_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `rental_id`, `customer_id`, `rating`, `review_text`, `review_date`) VALUES
(1, 2, 2, 4, 'Nice car, exceed my expectations.', '2025-05-15 18:54:44'),
(2, 3, 1, 4, 'Nice car so comfy for traveling.', '2025-05-15 18:57:16'),
(3, 4, 3, 5, 'It so smooth to drive.', '2025-05-15 19:00:14'),
(4, 5, 4, 5, 'Great car for travelling, nice.', '2025-05-15 19:02:52'),
(5, 6, 5, 5, 'Super nice car, it is suitable for family outing.', '2025-05-15 19:04:33'),
(6, 14, 2, 5, 'Super nice car, i am very satisfied with the car.', '2025-05-16 08:22:26');

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

CREATE TABLE `rules` (
  `rule_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `rule_name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rules`
--

INSERT INTO `rules` (`rule_id`, `branch_id`, `rule_name`, `created_at`, `updated_at`) VALUES
(2, 1, 'Valid Driver’s License Required', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(3, 1, 'Age Requirement', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(4, 1, 'No Smoking or Pets Allowed', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(5, 1, 'Fuel Policy Must Be Followed', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(6, 1, 'Return the Vehicle on Time', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(7, 2, 'Insurance Coverage', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(8, 2, 'Mileage Limits', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(9, 2, 'Prohibited Uses', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(10, 2, 'Deposit and Payment', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(11, 2, 'Damage Reporting', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(12, 3, 'Valid License Required', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(13, 3, 'No Smoking in the Vehicle', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(14, 3, 'Return with Same Fuel Level', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(15, 3, 'Only Registered Drivers May Operate', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(16, 3, 'Report Damages Immediately', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(17, 4, 'Valid License Required', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(18, 4, 'Only Registered Drivers May Operate', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(19, 4, 'Report Damages Immediately', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(20, 4, 'Deposit and Payment', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(21, 4, 'Return with Same Fuel Level', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(22, 5, 'No Smoking in the Vehicle', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(23, 5, 'Return with Same Fuel Level', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(24, 5, 'Only Registered Drivers May Operate', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(25, 5, 'Valid License Required', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(26, 5, 'Report Damages Immediately', '2025-05-21 11:03:10', '2025-05-21 11:03:10'),
(27, 3, 'No Pets Allowed', '2025-05-21 11:03:10', '2025-05-21 11:03:10');

-- --------------------------------------------------------

--
-- Table structure for table `transmissiontype`
--

CREATE TABLE `transmissiontype` (
  `transmissionType_id` int(11) NOT NULL,
  `transmissionType_name` enum('Manual','Automatic') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transmissiontype`
--

INSERT INTO `transmissiontype` (`transmissionType_id`, `transmissionType_name`) VALUES
(1, 'Automatic'),
(2, 'Manual');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `bookingstatus`
--
ALTER TABLE `bookingstatus`
  ADD PRIMARY KEY (`bookingStatus_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`branch_id`),
  ADD KEY `idx_branches_owner_id` (`owner_id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`car_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `carType_id` (`carType_id`),
  ADD KEY `transmissionType_id` (`transmissionType_id`),
  ADD KEY `fuelType_id` (`fuelType_id`);

--
-- Indexes for table `cartype`
--
ALTER TABLE `cartype`
  ADD PRIMARY KEY (`carType_id`);

--
-- Indexes for table `car_images`
--
ALTER TABLE `car_images`
  ADD PRIMARY KEY (`carImages_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_emailAddress` (`customer_emailAddress`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`drivers_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `proofOfResidency_id` (`proofOfResidency_id`),
  ADD KEY `driverType_id` (`driverType_id`);

--
-- Indexes for table `driverstype`
--
ALTER TABLE `driverstype`
  ADD PRIMARY KEY (`driversType_id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`fee_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `fueltype`
--
ALTER TABLE `fueltype`
  ADD PRIMARY KEY (`fuelType_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`owner_id`),
  ADD UNIQUE KEY `owner_emailAddress` (`owner_emailAddress`),
  ADD KEY `fk_admin` (`admin_id`);

--
-- Indexes for table `proofofresidency`
--
ALTER TABLE `proofofresidency`
  ADD PRIMARY KEY (`proofOfResidency_id`);

--
-- Indexes for table `rentalperiods`
--
ALTER TABLE `rentalperiods`
  ADD PRIMARY KEY (`rentalPeriod_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `drivers_id` (`drivers_id`),
  ADD KEY `rentalType_id` (`rentalType_id`),
  ADD KEY `locations_id` (`locations_id`),
  ADD KEY `rentalPeriod_id` (`rentalPeriod_id`),
  ADD KEY `bookingStatus_id` (`bookingStatus_id`),
  ADD KEY `idx_rentals_car_id` (`car_id`);

--
-- Indexes for table `rentaltype`
--
ALTER TABLE `rentaltype`
  ADD PRIMARY KEY (`rentalType_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `rental_id` (`rental_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `rules`
--
ALTER TABLE `rules`
  ADD PRIMARY KEY (`rule_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `transmissiontype`
--
ALTER TABLE `transmissiontype`
  ADD PRIMARY KEY (`transmissionType_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bookingstatus`
--
ALTER TABLE `bookingstatus`
  MODIFY `bookingStatus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `car_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `cartype`
--
ALTER TABLE `cartype`
  MODIFY `carType_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `car_images`
--
ALTER TABLE `car_images`
  MODIFY `carImages_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `drivers_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `driverstype`
--
ALTER TABLE `driverstype`
  MODIFY `driversType_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `fueltype`
--
ALTER TABLE `fueltype`
  MODIFY `fuelType_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `owner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `proofofresidency`
--
ALTER TABLE `proofofresidency`
  MODIFY `proofOfResidency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rentalperiods`
--
ALTER TABLE `rentalperiods`
  MODIFY `rentalPeriod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `rental_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `rentaltype`
--
ALTER TABLE `rentaltype`
  MODIFY `rentalType_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rules`
--
ALTER TABLE `rules`
  MODIFY `rule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `transmissiontype`
--
ALTER TABLE `transmissiontype`
  MODIFY `transmissionType_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`owner_id`);

--
-- Constraints for table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`),
  ADD CONSTRAINT `cars_ibfk_2` FOREIGN KEY (`carType_id`) REFERENCES `cartype` (`carType_id`),
  ADD CONSTRAINT `cars_ibfk_3` FOREIGN KEY (`transmissionType_id`) REFERENCES `transmissiontype` (`transmissionType_id`),
  ADD CONSTRAINT `cars_ibfk_4` FOREIGN KEY (`fuelType_id`) REFERENCES `fueltype` (`fuelType_id`);

--
-- Constraints for table `car_images`
--
ALTER TABLE `car_images`
  ADD CONSTRAINT `car_images_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`);

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`owner_id`),
  ADD CONSTRAINT `drivers_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `drivers_ibfk_3` FOREIGN KEY (`proofOfResidency_id`) REFERENCES `proofofresidency` (`proofOfResidency_id`),
  ADD CONSTRAINT `drivers_ibfk_4` FOREIGN KEY (`driverType_id`) REFERENCES `driverstype` (`driversType_id`);

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);

--
-- Constraints for table `owners`
--
ALTER TABLE `owners`
  ADD CONSTRAINT `fk_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`);

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `rentals_ibfk_3` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`),
  ADD CONSTRAINT `rentals_ibfk_4` FOREIGN KEY (`drivers_id`) REFERENCES `drivers` (`drivers_id`),
  ADD CONSTRAINT `rentals_ibfk_5` FOREIGN KEY (`rentalType_id`) REFERENCES `rentaltype` (`rentalType_id`),
  ADD CONSTRAINT `rentals_ibfk_6` FOREIGN KEY (`locations_id`) REFERENCES `locations` (`location_id`),
  ADD CONSTRAINT `rentals_ibfk_7` FOREIGN KEY (`rentalPeriod_id`) REFERENCES `rentalperiods` (`rentalPeriod_id`),
  ADD CONSTRAINT `rentals_ibfk_8` FOREIGN KEY (`bookingStatus_id`) REFERENCES `bookingstatus` (`bookingStatus_id`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`rental_id`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `rules`
--
ALTER TABLE `rules`
  ADD CONSTRAINT `rules_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
