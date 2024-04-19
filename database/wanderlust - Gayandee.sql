-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Apr 19, 2024 at 08:01 AM
-- Server version: 8.2.0
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
-- Table structure for table `guide_availability`
--

CREATE TABLE `guide_availability` (
  `id` int NOT NULL,
  `guide_id` int DEFAULT NULL,
  `availability` tinyint(1) DEFAULT '0',
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guide_availability`
--

INSERT INTO `guide_availability` (`id`, `guide_id`, `availability`, `date`) VALUES
(1, 22, 1, '2024-04-25');

-- --------------------------------------------------------

--
-- Table structure for table `guide_profile`
--

CREATE TABLE `guide_profile` (
  `guide_id` int NOT NULL,
  `description` text,
  `languages` text,
  `certifications` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guide_profile`
--

INSERT INTO `guide_profile` (`guide_id`, `description`, `languages`, `certifications`) VALUES
(22, 'Hi, I\'m Nirmal, a professional tour guide with 5 years of experience. I have a passion for history and culture and love sharing my knowledge with others. I specialize in tours of ancient ruins, temples, and historical sites. I\'m also an expert in local cuisine and can recommend the best places to eat in town. Let me show you the beauty of my country and help you create memories that will last a lifetime.', 'English,Sinhala,Tamil', 'GOV certification');

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `id` int NOT NULL,
  `guide_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `max_group_size` int NOT NULL,
  `max_distance` int NOT NULL,
  `transport_needed` tinyint(1) NOT NULL,
  `places` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `package`
--

INSERT INTO `package` (`id`, `guide_id`, `price`, `max_group_size`, `max_distance`, `transport_needed`, `places`) VALUES
(1, 22, 15000.00, 30, 30, 1, 'Nuwara Eliya, Ella'),
(2, 23, 10000.00, 10, 20, 1, 'Kandy, Ella, Rathnapura'),
(3, 22, 5000.00, 10, 20, 1, 'Kandy, Ella'),
(30, 22, 10000.00, 5, 10, 0, 'Ella, Kandy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `guide_availability`
--
ALTER TABLE `guide_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guide_id` (`guide_id`);

--
-- Indexes for table `guide_profile`
--
ALTER TABLE `guide_profile`
  ADD PRIMARY KEY (`guide_id`);

--
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guide_id` (`guide_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `guide_availability`
--
ALTER TABLE `guide_availability`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `guide_availability`
--
ALTER TABLE `guide_availability`
  ADD CONSTRAINT `guide_availability_ibfk_1` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`id`);

--
-- Constraints for table `guide_profile`
--
ALTER TABLE `guide_profile`
  ADD CONSTRAINT `guide_profile_ibfk_1` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`id`);

--
-- Constraints for table `package`
--
ALTER TABLE `package`
  ADD CONSTRAINT `package_ibfk_1` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
