-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Oct 31, 2023 at 02:53 PM
-- Server version: 8.1.0
-- PHP Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wanderlust`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `number` varchar(15) NOT NULL,
  `nic` varchar(15) NOT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `address`, `number`, `nic`, `user_id`) VALUES
(1, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '4534646t435', '329473802343', NULL),
(2, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '239423423432', '235345345325', NULL),
(3, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '123124234', '3534534532', NULL),
(4, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '123124234', '3534534532', NULL),
(5, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '234423423', '32423053432', NULL),
(6, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '32354543', 'w309340324', 38),
(7, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '479238203', '43342834834', 39),
(8, 'd', 'fdede', 'fadeded', 'fedfef', 40),
(9, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '32535345', '4354354', 41),
(10, 'nsadsd', 'No 255, Neluwa RD', '32434', '2434234', 42),
(11, 'wqewe', 'fdes@s.com', 'dfsdf', 'dsfdf', 43),
(12, 'Arya', 'Colombo', '0716024489', '200177901838', 45),
(13, 'Nirmal', 'COlombo', '0716024489', '20011783929', 46);

-- --------------------------------------------------------

--
-- Table structure for table `guides`
--

CREATE TABLE `guides` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `nic` varchar(15) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guides`
--

INSERT INTO `guides` (`id`, `name`, `address`, `nic`, `mobile`, `gender`, `user_id`) VALUES
(1, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '2334543', '076024489', 'male', NULL),
(2, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '2334543', '076024489', 'male', 32),
(3, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '435453636t345', '076024489', 'male', 33);

-- --------------------------------------------------------

--
-- Table structure for table `rental_services`
--

CREATE TABLE `rental_services` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `regNo` varchar(50) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rental_services`
--

INSERT INTO `rental_services` (`id`, `name`, `address`, `regNo`, `mobile`, `user_id`) VALUES
(1, 'NS', 'No 255, Neluwa RD', '453453', '076024489', 26),
(2, 'NS', 'No 255, Neluwa RD', '353434', '+94716024489', 27),
(3, 'NS', 'No 255, Neluwa RD', 'NS', '+94716024489', 28),
(4, 'ABC', 'ABC a', '342332323', '+94716024489', 44);

-- --------------------------------------------------------

--
-- Table structure for table `tips`
--

CREATE TABLE `tips` (
  `id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `author` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tips`
--

INSERT INTO `tips` (`id`, `title`, `description`, `author`) VALUES
(1, 'hello', 'sadfsdfdf', ''),
(4, 'Hello', 'ABC', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('customer','rentalservice','guide','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `date`, `role`) VALUES
(26, 'admin@ns.com', 'FIy1aXlTYUSCvSy4LoQXBg==:b3eb8a43d487b51f89ee9e5d20b10cd5a630e196fc6dae771e1ee0b234cd5314', '2023-10-30 03:07:06', 'customer'),
(27, 'ns@gmail.com', 'Cfd8iTgMKjp94OBPoI46aQ==:cdfc444708711da48169be9ee13986cf3a9f54042e743826398053869933f978', '2023-10-30 03:08:01', 'customer'),
(28, 'ns@rent.com', 'BoSTwn57mvT1a/TSEbMozQ==:3f80d02549f4d762c432fe677a03a2126021097684588cb02be140c8dc74b638', '2023-10-30 03:46:04', 'rentalservice'),
(29, 'nirmal@ns.nnnn', '4vacpY1dmsDOmSzQj3Bu5g==:4dd4390a8309a09128acb2c2bf30f2d1503b1ba347e5600f56eb2dfa304a1874', '2023-10-30 04:15:07', 'customer'),
(30, 'nirmal@ns.nnnn', 'RlpOMs9wlq+R6DCaL8sCMg==:00743b3a1b65a07f71062db4b11e23dc70a9a33f3584f73f39132817eba5f6c7', '2023-10-30 04:42:27', 'customer'),
(31, 'nirmal@ns.nnnnee', 'U1rKr8AGHukamgx0eWrz8g==:663b3aea437919cae4f9beeeb6f5e14e262fd79f3ec8e8ac5a38948829eebe4b', '2023-10-30 05:20:25', 'guide'),
(32, 'nirmal@ns.nnnnee', 'tnr2MFDpsp6XEyBCbtevWA==:af7e23db28f0ba2ee0214ceb3c2862b723f881f2b1704cff13b8a302a43a3d00', '2023-10-30 05:44:41', 'guide'),
(33, 'g@g.com', 'DmMS2mJe9gvFeL+Q6mmZHg==:71aa818f5548cc7ad17efd7ef7ac13c760ebf22e49be62100795bf19c64600a9', '2023-10-30 08:21:11', 'guide'),
(34, 'n@D.com', '3sjrdG1UMhzC5Scl26eG2Q==:2b9174c561d77ba3804892d426a7742e84c1bc556e8da1ed329168ff6212d486', '2023-10-30 11:46:45', 'customer'),
(35, 'a@a.com', 'vPg/+nAnzMhcj74EDrZTxw==:bfb2c0b4e6646659c8aafbd3dde1c483cc45398c90b25aa16bdffa96156174b8', '2023-10-30 11:47:19', 'customer'),
(36, 'a@a.com', 'PcPNL2WnUcmQ56d7DrFdPQ==:ccbcfec20fe7e3e8a67115097d330739a50194dc72b02149452e768be9707346', '2023-10-30 11:47:59', 'customer'),
(37, 'nn@nn.nn', '0ystEKmQFArj4IfJWEfoVA==:310ff9f491adf2ff18247dae95f8ab3f79ac1a124c15fe4bc978f204d4bee2fe', '2023-10-30 11:48:49', 'customer'),
(38, 'nnn@nn.nn', 'kjIbHrqgStSSV2Tgu43tVQ==:9c2233c5cefd858ac05b5dc647cf5f08b66e6355a22c6ad1afad7d8643815778', '2023-10-30 11:54:41', 'admin'),
(39, 'c@nirmal.com', 'g5qTrsdyU9eJbo2dQLYHdg==:26d0e89604e81358904c4600c43b7aa87e4d681445ea980d04da020b151b7b5b', '2023-10-31 05:26:45', 'customer'),
(40, 'f@dfcd.com', 'IvVLuCu93/vzGcgRmSi9RA==:552bbbab43c387a39f80898a358585686b0e73f4ee46acc37657a83b10b19350', '2023-10-31 09:07:06', 'customer'),
(41, 'nnn@n8.nN', 'pkheelAH6BTjG6JA9I18Fg==:795ec2ef831af6a16f898171ab918f5eb6cd9636a70dcfa08aeabca846a2c2cc', '2023-10-31 10:06:28', 'customer'),
(42, 'nd@23.Com', 'blW+zFwGNKZ1mroVfajzkg==:a3339524e870511b5d2f20e5eb0edd532a5bcc2c1bec05aee785f8b3ebaefd17', '2023-10-31 10:07:08', 'customer'),
(43, 'sfdsd@d.com', 'XHc4jFzOyhf5atisiD6/rg==:0a5b8082bc5330a15be390e0ddb68231135e1251cc9b3321f2d548f6326d7d12', '2023-10-31 10:07:57', 'customer'),
(44, 'ab@a.COM', 'NHqcTQiJU/rGaLMTZgc2oA==:8fea6e4b0cd0ea3a49c687355f6d0aa9b9f795ccd54d4dacc3ab79af352f6806', '2023-10-31 10:26:36', 'rentalservice'),
(45, 'ABC@7c.com', 'YAQq+0K+WhfSF+YG+F2aBw==:98ac02df6083ae5c7de615bb87b31c408bf87d1992a1b2c835c630d31c49fc35', '2023-10-31 10:52:27', 'customer'),
(46, 'nirmal@ns.ns', 'zWHeEFYEICueOBsYvxv2bA==:d2329cfaf6050b01e752277ca546be3e009a05304c619edda54580a09535769f', '2023-10-31 11:28:13', 'customer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `guides`
--
ALTER TABLE `guides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rental_services`
--
ALTER TABLE `rental_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tips`
--
ALTER TABLE `tips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rental_services`
--
ALTER TABLE `rental_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tips`
--
ALTER TABLE `tips`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `guides`
--
ALTER TABLE `guides`
  ADD CONSTRAINT `guides_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rental_services`
--
ALTER TABLE `rental_services`
  ADD CONSTRAINT `rental_services_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
