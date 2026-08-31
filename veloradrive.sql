-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2026 at 09:14 AM
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
-- Database: `veloradrive`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `vehicle_name` varchar(100) NOT NULL,
  `vehicle_image` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `pickup_location` varchar(100) NOT NULL,
  `return_location` varchar(100) NOT NULL,
  `booking_date` date NOT NULL,
  `return_date` date NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(30) NOT NULL,
  `booking_status` varchar(30) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `fullname`, `email`, `phone`, `vehicle_name`, `vehicle_image`, `price`, `pickup_location`, `return_location`, `booking_date`, `return_date`, `payment_method`, `payment_status`, `booking_status`, `total_amount`, `created_at`) VALUES
(1, 'johngy', 'albin@gmail.com', '09497331659', 'Yamaha R15', '', 1200.00, 'Kochi', 'Kochi', '2026-08-04', '2026-08-06', 'Cash', 'Paid', 'Confirmed', 3832.00, '2026-08-02 14:55:30'),
(2, 'johngy', 'albin@gmail.com', '09497331659', 'TVS Ntorq', '', 850.00, 'Thrissur', 'Trivandrum', '2026-08-07', '2026-08-12', 'Cash', 'Paid', 'Confirmed', 6015.00, '2026-08-02 14:57:05'),
(3, 'johngy', 'albin@gmail.com', '09497331659', 'TVS Ntorq', '', 850.00, 'Thrissur', 'Trivandrum', '2026-08-07', '2026-08-12', 'Cash', 'Paid', 'Confirmed', 6015.00, '2026-08-02 15:16:17'),
(4, 'johngy', 'albin@gmail.com', '09497331659', 'Hyundai Verna', '', 3000.00, 'Kochi', 'Kochi', '2026-08-07', '2026-08-13', 'Cash', 'Paid', 'Confirmed', 22240.00, '2026-08-02 15:19:30'),
(5, 'john', 'alvin12@gmail.com', '09497331659', 'Hyundai Creta', '', 3500.00, 'Kochi', 'Kochi', '2026-08-07', '2026-08-08', 'Cash', 'Paid', 'Cancelled', 5130.00, '2026-08-05 13:14:06'),
(6, 'john', 'alvin12@gmail.com', '09497331659', 'Honda City', '', 2800.00, 'Trivandrum', 'Trivandrum', '2026-08-06', '2026-08-07', 'Cash', 'Paid', 'Confirmed', 4304.00, '2026-08-05 13:32:47'),
(7, 'john', 'johngy15jaison@gmail.com', '09497331659', 'Hyundai Creta', 'creta.jpg', 0.00, 'tiruvalla', 'tiruvalla', '2026-08-22', '2026-08-23', 'UPI', 'Paid', 'Confirmed', 7000.00, '2026-08-07 12:57:09'),
(8, 'john', 'johngy15jaison@gmail.com', '09497331659', 'Hyundai Creta', 'creta.jpg', 0.00, 'tiruvalla', 'tiruvalla', '2026-08-22', '2026-08-23', 'UPI', 'Paid', 'Confirmed', 7000.00, '2026-08-07 13:02:37'),
(9, 'john', 'johngy15jaison@gmail.com', '09497331659', 'Hyundai Creta', 'creta.jpg', 0.00, 'tiruvalla', 'tiruvalla', '2026-08-22', '2026-08-23', 'Cash on Pickup', 'Paid', 'Confirmed', 7000.00, '2026-08-07 13:02:54'),
(10, 'john', 'johngy15jaison@gmail.com', '09497331659', 'BMW 3 Series', 'bmw.jpg', 0.00, 'tiruvalla', 'tiruvalla', '2026-08-13', '2026-08-14', 'Cash on Pickup', 'Paid', 'Confirmed', 17000.00, '2026-08-08 11:28:52'),
(11, 'john', 'johngy15jaison@gmail.com', '09497331659', 'BMW 3 Series', 'bmw.jpg', 0.00, 'tiruvalla', 'tiruvalla', '2026-08-13', '2026-08-14', 'Cash on Pickup', 'Paid', 'Confirmed', 17000.00, '2026-08-08 11:45:02'),
(12, 'john', 'johngy15jaison@gmail.com', '09497331659', 'BMW 3 Series', 'bmw.jpg', 0.00, 'tiruvalla', 'tiruvalla', '2026-08-13', '2026-08-14', 'Cash on Pickup', 'Paid', 'Confirmed', 17000.00, '2026-08-08 11:45:23'),
(13, 'john', 'johngy15jaison@gmail.com', '9497331659', 'Honda City', 'city.jpg', 0.00, 'tiruvalla', 'tiruvalla', '2026-08-20', '2026-08-28', 'Cash on Pickup', 'Paid', 'Confirmed', 25200.00, '2026-08-08 14:20:41'),
(14, 'john', 'johngy15jaison@gmail.com', '9497331659', 'Honda City', 'city.jpg', 0.00, 'tiruvalla', 'tiruvalla', '2026-08-29', '2026-08-30', 'Credit Card', 'Paid', 'Confirmed', 5600.00, '2026-08-08 14:27:48'),
(15, 'john', 'johngy15jaison@gmail.com', '9497331659', 'Honda City', 'city.jpg', 0.00, 'tiruvalla', 'tiruvalla', '2026-08-29', '2026-08-30', 'Cash on Pickup', 'Paid', 'Confirmed', 5600.00, '2026-08-08 14:29:38'),
(16, 'john', 'johngy15jaison@gmail.com', '9497331659', 'Honda City', 'city.jpg', 2800.00, 'tiruvalla', '0', '2026-08-14', '2026-08-15', 'Cash', 'Paid', 'Confirmed', 2800.00, '2026-08-13 13:38:14'),
(17, 'john', 'alvin12@gmail.com', '9497331659', 'Honda City', 'city.jpg', 2800.00, 'Wayanad', '0', '2026-08-15', '2026-08-19', 'Cash', 'Paid', 'Cancelled', 11200.00, '2026-08-14 14:00:04'),
(18, 'john', 'alvin12@gmail.com', '9497331659', 'Hyundai Creta', 'creta.jpg', 3500.00, 'Wayanad', '0', '2026-08-15', '2026-08-16', 'Cash', 'Paid', 'Cancelled', 3500.00, '2026-08-14 14:48:18'),
(19, 'john', 'alvin12@gmail.com', '9497331659', 'KTM Duke', 'images/duke.jpg', 1400.00, 'Thiruvananthapuram', '0', '2026-08-21', '2026-08-22', 'Cash', 'Paid', 'Confirmed', 1400.00, '2026-08-17 00:51:05'),
(20, 'john', 'alvin12@gmail.com', '9497331659', 'KTM Duke', 'images/duke.jpg', 1400.00, 'Thiruvananthapuram', '0', '2026-08-21', '2026-08-22', 'Cash', 'Paid', 'Confirmed', 1400.00, '2026-08-17 00:51:24'),
(21, 'john', 'alvin12@gmail.com', '9497331659', 'KTM Duke', 'images/duke.jpg', 1400.00, 'Thiruvananthapuram', '0', '2026-08-21', '2026-08-22', 'UPI', 'Paid', 'Confirmed', 1400.00, '2026-08-17 00:54:53'),
(22, 'john', 'alvin12@gmail.com', '9497331659', 'KTM Duke', 'images/duke.jpg', 1400.00, 'Thiruvananthapuram', '0', '2026-08-21', '2026-08-22', 'UPI', 'Paid', 'Confirmed', 1400.00, '2026-08-17 01:02:53'),
(23, 'john', 'alvin12@gmail.com', '9497331659', 'KTM Duke', 'images/duke.jpg', 1400.00, 'Thiruvananthapuram', '0', '2026-08-21', '2026-08-22', 'UPI', 'Paid', 'Cancelled', 1400.00, '2026-08-17 01:03:15'),
(24, 'john', 'alvin12@gmail.com', '9497331659', 'Honda City', 'images/city.jpg', 2800.00, 'Thrissur', '0', '2026-08-21', '2026-08-23', 'Cash', 'Paid', 'Confirmed', 5600.00, '2026-08-17 15:01:08'),
(25, 'john', 'alvin12@gmail.com', '9497331659', 'Hyundai Creta', 'images/creta.jpg', 3500.00, 'Thrissur', '0', '2026-08-22', '2026-08-23', 'Cash', 'Paid', 'Confirmed', 3500.00, '2026-08-17 15:04:59');

-- --------------------------------------------------------

--
-- Table structure for table `booking_documents`
--

CREATE TABLE `booking_documents` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `verification_status` varchar(30) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_documents`
--

INSERT INTO `booking_documents` (`id`, `booking_id`, `email`, `document_type`, `file_name`, `file_path`, `upload_date`, `verification_status`) VALUES
(1, 15, 'johngy15jaison@gmail.com', 'Driving License', 'driving_license_20260808162754_a2c053c01c.jpeg', 'uploads/booking_documents/driving_license_20260808162754_a2c053c01c.jpeg', '2026-08-08 14:29:38', 'Pending'),
(2, 15, 'johngy15jaison@gmail.com', 'Government ID Proof', 'government_id_20260808162754_5b8aebf7fe.jpeg', 'uploads/booking_documents/government_id_20260808162754_5b8aebf7fe.jpeg', '2026-08-08 14:29:38', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `fullname`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'JOHNGY JAISON', 'johngy15jaison@gmail.com', 'nothing', 'nothing', '2026-08-04 13:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `type` enum('success','payment','booking','reminder','offer','review','error') DEFAULT 'success',
  `status` enum('Unread','Read') DEFAULT 'Unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `email`, `title`, `message`, `type`, `status`, `created_at`) VALUES
(1, 'johngy15jaison@gmail.com', 'Booking Confirmed', 'Your booking for Hyundai Creta has been confirmed successfully.', 'booking', 'Unread', '2026-08-07 12:57:10'),
(2, 'johngy15jaison@gmail.com', 'Booking Confirmed', 'Your booking for Hyundai Creta has been confirmed successfully.', 'booking', 'Unread', '2026-08-07 13:02:38'),
(3, 'johngy15jaison@gmail.com', 'Booking Confirmed', 'Your booking for Hyundai Creta has been confirmed successfully.', 'booking', 'Unread', '2026-08-07 13:02:54'),
(4, 'johngy15jaison@gmail.com', 'Booking Confirmed', 'Your booking for BMW 3 Series has been confirmed successfully.', 'booking', 'Unread', '2026-08-08 11:28:52'),
(5, 'johngy15jaison@gmail.com', 'Booking Confirmed', 'Your booking for BMW 3 Series has been confirmed successfully.', 'booking', 'Unread', '2026-08-08 11:45:02'),
(6, 'johngy15jaison@gmail.com', 'Booking Confirmed', 'Your booking for BMW 3 Series has been confirmed successfully.', 'booking', 'Unread', '2026-08-08 11:45:23'),
(7, 'johngy15jaison@gmail.com', 'Booking Confirmed', 'Your booking for Honda City has been confirmed successfully.', 'booking', 'Unread', '2026-08-08 14:20:41'),
(8, 'johngy15jaison@gmail.com', 'Booking Confirmed', 'Your booking for Honda City has been confirmed successfully.', 'booking', 'Unread', '2026-08-08 14:27:48'),
(9, 'johngy15jaison@gmail.com', 'Booking Confirmed', 'Your booking for Honda City has been confirmed successfully.', 'booking', 'Unread', '2026-08-08 14:29:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `phone`, `address`, `profile_image`, `created_at`) VALUES
(1, 'johngy', 'johngy15jaison@gmail.com', '$2y$10$SOLy4K7eAGVYpSD80taXMu4Lj9c/zjLXFFl6dRBYQpXns6bCtnkH6', '09497331659', 'kalapurackal house manjadi po tiruvalla', '', '2026-08-03 06:23:24'),
(2, 'johngy', 'avinash@gmail.com', '$2y$10$zS/v/tQNa1nbaY1I54OFQeRH1n4j03xo3lSsi/i40wxD0AeOgKpI.', NULL, NULL, NULL, '2026-08-03 14:37:42'),
(3, 'johngy', 'albin@gmail.com', '$2y$10$T7aozCPNCIRcLdZdl5uFXOkPi4S.z6LhZR6IMe5pvaYra8XDEVxh6', NULL, NULL, NULL, '2026-08-03 15:18:47'),
(4, 'johngy', 'avinash123@gmail.com', '$2y$10$jmpD5IsmmvGyj02dM6qW1uSGrRliMhpmA2XTXCHg0Fi3Ia.XGEHJG', NULL, NULL, NULL, '2026-08-03 15:22:40'),
(5, 'johngy', 'alvin@gmail.com', '$2y$10$EhogGNQFoUh6B4BAZq2tbOgx.KQjr2ADfxY3gINiAt.ALiK2iegUC', NULL, NULL, NULL, '2026-08-03 15:23:48'),
(6, 'john', 'alvin12@gmail.com', '$2y$10$URBPdCFMLt.MvIhSGus06OZt0Rcpy1SyYSuqXX/pnBQE5Mc1exsli', NULL, NULL, NULL, '2026-08-04 13:19:43'),
(7, 'johngy', 'albin12@gmail.com', '$2y$10$Ike0drL.oLDHlF1B.Vp8Te/BCfE7W/mJRPUTlHgl27wutWhKD7KrG', NULL, NULL, NULL, '2026-08-11 08:19:46');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vehicle_name` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_name`, `image`, `price`, `category`) VALUES
(1, 'Toyota Fortuner', 'fortuner.jpg', 4500.00, 'Car'),
(2, 'Hyundai Creta', 'creta.jpg', 3500.00, 'Car'),
(3, 'Honda City', 'city.jpg', 2800.00, 'Car'),
(4, 'Hyundai Verna', 'verna.jpg', 3000.00, 'Car'),
(5, 'Maruti Swift', 'swift.jpg', 1800.00, 'Car'),
(6, 'Toyota Innova', 'innova.jpg', 4200.00, 'Car'),
(7, 'Mahindra Thar', 'thar.jpg', 5000.00, 'Car'),
(8, 'BMW 3 Series', 'bmw.jpg', 8500.00, 'Car'),
(9, 'Yamaha R15', 'r15.jpg', 1200.00, 'Bike'),
(10, 'KTM Duke', 'duke.jpg', 1400.00, 'Bike'),
(11, 'Royal Enfield Classic 350', 'classic350.jpg', 1500.00, 'Bike'),
(12, 'Apache RTR 160', 'rtr160.jpg', 1100.00, 'Bike'),
(13, 'Honda Activa 6G', 'activa6g.jpg', 700.00, 'Scooter'),
(14, 'Suzuki Access 125', 'access125.jpg', 750.00, 'Scooter'),
(15, 'TVS Ntorq', 'ntorq.jpg', 850.00, 'Scooter'),
(16, 'Yamaha RayZR', 'rayzr.jpg', 800.00, 'Scooter');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_documents`
--
ALTER TABLE `booking_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_email`,`vehicle_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `booking_documents`
--
ALTER TABLE `booking_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
