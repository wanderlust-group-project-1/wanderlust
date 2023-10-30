-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Oct 30, 2023 at 06:10 AM
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
(1, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '4534646t435', '329473802343', NULL);

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
(2, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '2334543', '076024489', 'male', 32);

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
(3, 'NS', 'No 255, Neluwa RD', 'NS', '+94716024489', 28);

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
(1, 'ns@gmail.com', 'hfceflwefmew', '2023-09-17 15:29:46', 'customer'),
(2, 'hdcsde@sdcksd.com', 'jdcnkdjed', '2023-09-17 15:35:05', 'customer'),
(3, 'a@a.com', 'aaaaaaaa', '2023-09-18 13:49:20', 'customer'),
(4, 'a@a.com', 'aassaass', '2023-09-18 13:54:19', 'customer'),
(5, 'a@a.net', 'aaaaaaaa', '2023-09-18 13:55:10', 'customer'),
(6, 'abc@abc.abc', 'rjvMEs7N4gv3E5brnqA3vw==:b784cd7134e9e6452617960d1a4de2c8c825c1ade1ca5f5ef3b92828df1384cf', '2023-09-18 14:19:12', 'customer'),
(7, 'abc@a.a', 'nwoimcU/y9j9AvjIgD8Bcg==:4bc0d5bfe30e9c2cd2a00e3a2dfcc06f6ec71899e2213e0b5c14d0b17679d589', '2023-09-18 14:23:12', 'customer'),
(8, 'abc@a.a', '+Scf94h59syLQI3zBw3hrw==:7e67bb58ec0141b39f8c37956546f3627152b713daff1ec854e9f497f8db22c3', '2023-09-18 14:23:17', 'customer'),
(9, 'x@x.com', 'Esg5mI0aDPErZNiKFkxHSw==:231a5e0a57ff615ba645f7b52472e14706d8759bbef6e1d79925110e51a8ee01', '2023-09-18 15:38:56', 'customer'),
(10, 'as@as.a', 'RT/FDuCiAL9J7T+dDI3Vrw==:65cff108543005b10eed34a075a788ea16b9a2e1b424b0070b72ac79d00ed973', '2023-09-18 15:39:53', 'customer'),
(11, 'q@q.com', 'gK4nP9w967w0iWvVtqls4Q==:d373216ba5ff4310b5f539ad9043e337b16ccf44342b7836c86dc952706a6339', '2023-09-18 18:11:23', 'customer'),
(12, 'q@q.org', 'Wqpnabl2cgf/ajjSlzxRNA==:58186097659142198467f257cf3e360bb6424abfbdabcbfd38c5d544558b50b0', '2023-09-18 19:21:14', 'customer'),
(13, 'dd@d.com', 'PQCG6JZKXmvJ4rQcSzu44A==:e6f498fc6bf9e22dbffcec1771e24ce51cce389fa99e8bd2d0f95a7010d3870c', '2023-09-18 19:41:59', 'customer'),
(15, 'nirmal@ns.net', 'KUTJP6b+gZE5Ffxzv9Lkcw==:a7cd10935d45acdd9d63c48d75f72696f9fd821ceae83dbeb1d59792d6a73b66', '2023-10-27 03:13:14', 'customer'),
(16, 'nirmal@nirmal.net', 'jLCdxUx9jCFKAUIREpxZsg==:7ee3b87ffc2e740355543824a36fa421bfd3a8c0ed8a0fd49ef838ed16cb5a0f', '2023-10-30 02:26:40', 'customer'),
(17, 'nirmal@nirmal.net', 'ENwNLUPRrIXgXxOklO5jqw==:c89d5ca5c03a504948d96c1da32b025cd66ef9dd2b250b7f9be7340f988ae9a6', '2023-10-30 02:29:46', 'customer'),
(18, 'nirmal@nirmal.net', 'O8ndLiqw+ugJH1lPc7PF+w==:243363038a4bd26dc1ea93291c4f683777b88114137884f5a7beb7e737cad5bf', '2023-10-30 02:53:11', 'customer'),
(19, 'nirmal@nirmal.net', 'vyfN73eGrDwjlAud3KgnUQ==:0b3f3cd23a37c26f0663e5abb7287e73c452e26ce78d4cb94a80e728b6472b51', '2023-10-30 02:53:23', 'customer'),
(20, 'nirmal@nirmal.net', 'KCuDDsv0qc0dk0wr4dgtOA==:a1382ab48d79ac9d3131633dd0964c97e6bf0d41ef8c9d5f99cf00566701ca2b', '2023-10-30 02:53:54', 'customer'),
(21, 'nirmal@nirmal.net', 'LKJkEH9l40oeG1rlwJxUcA==:4668e886490011b9051efe7193e45bb374dc780140409cfc9a1dcd4046db26f4', '2023-10-30 02:54:10', 'customer'),
(22, 'nirmal@nirmal.net', 'wjhiqrNDZ8ldWIE5xsZomg==:c767b69f3791e8d1c305b9eb2248283ac260236c635b839db8ba9eae851017b2', '2023-10-30 02:54:45', 'customer'),
(23, 'nirmal@nirmal.net', 'VioJV2m9nT7A+jvvinQbdA==:6d517836c051028557f519b219ad0b1708e3f1139e61a4652c7f7618a2b7b550', '2023-10-30 02:57:30', 'customer'),
(24, 'admin@ns.com', 'JnSOavIYY2XHxxnRpiU01Q==:46179151c6e11c7ce9565a3bad992429d2a82afbc9308863bad0a6ba251a6e70', '2023-10-30 02:59:43', 'customer'),
(25, 'admin@ns.com', 'uSOk8wl6liOT8hk+UFhTwg==:d858baeabbebe51c4941e9d174303df006007f46fb0f3be29f2c549d87e3ed45', '2023-10-30 03:03:23', 'customer'),
(26, 'admin@ns.com', 'FIy1aXlTYUSCvSy4LoQXBg==:b3eb8a43d487b51f89ee9e5d20b10cd5a630e196fc6dae771e1ee0b234cd5314', '2023-10-30 03:07:06', 'customer'),
(27, 'ns@gmail.com', 'Cfd8iTgMKjp94OBPoI46aQ==:cdfc444708711da48169be9ee13986cf3a9f54042e743826398053869933f978', '2023-10-30 03:08:01', 'customer'),
(28, 'ns@rent.com', 'BoSTwn57mvT1a/TSEbMozQ==:3f80d02549f4d762c432fe677a03a2126021097684588cb02be140c8dc74b638', '2023-10-30 03:46:04', 'rentalservice'),
(29, 'nirmal@ns.nnnn', '4vacpY1dmsDOmSzQj3Bu5g==:4dd4390a8309a09128acb2c2bf30f2d1503b1ba347e5600f56eb2dfa304a1874', '2023-10-30 04:15:07', 'customer'),
(30, 'nirmal@ns.nnnn', 'RlpOMs9wlq+R6DCaL8sCMg==:00743b3a1b65a07f71062db4b11e23dc70a9a33f3584f73f39132817eba5f6c7', '2023-10-30 04:42:27', 'customer'),
(31, 'nirmal@ns.nnnnee', 'U1rKr8AGHukamgx0eWrz8g==:663b3aea437919cae4f9beeeb6f5e14e262fd79f3ec8e8ac5a38948829eebe4b', '2023-10-30 05:20:25', 'guide'),
(32, 'nirmal@ns.nnnnee', 'tnr2MFDpsp6XEyBCbtevWA==:af7e23db28f0ba2ee0214ceb3c2862b723f881f2b1704cff13b8a302a43a3d00', '2023-10-30 05:44:41', 'guide');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rental_services`
--
ALTER TABLE `rental_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
