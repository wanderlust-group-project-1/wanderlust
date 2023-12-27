-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Nov 07, 2023 at 11:42 AM
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
CREATE DATABASE IF NOT EXISTS `wanderlust` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `wanderlust`;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
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
(13, 'Nirmal', 'COlombo', '0716024489', '20011783929', 46),
(14, 'Nirmal', 'Colombo', '0716024489', '200117901838', 47),
(15, 'Admin', 'COlombo', '0716024489', '200117901838', 48),
(16, 'Savinda', 'colombo', '0713056777', '200117901838', 49),
(17, 'Nirmal savi', ' Colombo', '076024481', '200117901811', 74);

-- --------------------------------------------------------

--
-- Table structure for table `guides`
--

DROP TABLE IF EXISTS `guides`;
CREATE TABLE `guides` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `nic` varchar(15) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `user_id` int DEFAULT NULL,
  `status` enum('waiting','accepted','rejected','') NOT NULL DEFAULT 'waiting'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guides`
--

INSERT INTO `guides` (`id`, `name`, `address`, `nic`, `mobile`, `gender`, `user_id`, `status`) VALUES
(1, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '2334543', '076024489', 'male', NULL, 'waiting'),
(2, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '2334543', '076024489', 'male', 32, 'waiting'),
(3, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '435453636t345', '076024489', 'male', 33, 'waiting'),
(4, 'Sandali Gunawardhana', 'Colombo', '200117901832', '0716024489', 'female', 51, 'waiting');

-- --------------------------------------------------------

--
-- Table structure for table `rental_services`
--

DROP TABLE IF EXISTS `rental_services`;
CREATE TABLE `rental_services` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `regNo` varchar(50) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `user_id` int DEFAULT NULL,
  `status` enum('waiting','accepted','rejected','') NOT NULL DEFAULT 'waiting',
  `verification_document` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rental_services`
--

INSERT INTO `rental_services` (`id`, `name`, `address`, `regNo`, `mobile`, `user_id`, `status`, `verification_document`) VALUES
(1, 'Nirmal Savinda', 'No 255, Neluwa RD', '453453', '076024489', 26, 'waiting', NULL),
(2, 'Sandali Gunawardena', 'Colombo', '353434', '+94716033484', 27, 'waiting', NULL),
(3, 'Gayandee Rajapaksha', 'Colombo', 'NS', '0716039989', 28, 'waiting', NULL),
(4, 'Sarani ', 'Hettiarachchi', '342332323', '0786023989', 44, 'accepted', NULL),
(5, 'Rental SHop', 'Colombo', 'B092342343', '0716024489', 50, 'waiting', NULL),
(6, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS', '076024489', 52, 'waiting', NULL),
(7, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS', '+94716024489', 53, 'waiting', NULL),
(8, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS', '+94716024489', 54, 'waiting', NULL),
(9, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS675757', '+94716024489', 55, 'waiting', NULL),
(10, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 56, 'waiting', NULL),
(11, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 57, 'waiting', NULL),
(12, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 58, 'waiting', NULL),
(13, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 59, 'waiting', NULL),
(14, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 60, 'waiting', NULL),
(15, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 61, 'waiting', NULL),
(16, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 62, 'waiting', NULL),
(17, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'sq32434234', '+94716024489', 66, 'waiting', NULL),
(18, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'sq32434234', '+94716024489', 67, 'waiting', NULL),
(19, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'sq32434234', '+94716024489', 68, 'rejected', NULL),
(20, 'Sandali', ' ABC ABC', '200134245754', '076024489', 69, 'waiting', NULL),
(21, 'NS', 'no 255 ', 'b2034534534', '076024489', 70, 'waiting', NULL),
(22, 'NS', ' 255 Ns ', 'b048294873', '0832873293', 71, 'waiting', ''),
(23, 'ANDSD dad', 'No 255, Neluwa RD\r\nGorakaduwa', 'b43532423', '076024489', 72, 'accepted', '65435a34072e4.pdf'),
(24, 'Nirmal', ' ABC', 'B3243354', '082372434', 73, 'waiting', '65438a19444d3.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `tips`
--

DROP TABLE IF EXISTS `tips`;
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
(4, 'Hello', 'ABC', 'admin'),
(6, 'Camping? Read these before you plan!', 'Choose the Right Campsite:\r\n\r\nResearch and select a campsite that suits your preferences and needs, whether it\'s a developed campground with amenities or a remote backcountry site. Check for reservation requirements and availability.\r\n<br/>\r\nCheck the Weather Forecast:\r\n\r\nStay updated on the weather forecast for your camping destination and plan accordingly. Be prepared for changes in weather, and bring appropriate clothing and gear.\r\n<br/>\r\nPack Properly:\r\n\r\nCreate a checklist to ensure you bring all the necessary camping gear, including tents, sleeping bags, sleeping pads, cooking equipment, and clothing. Don\'t forget essentials like a first aid kit, insect repellent, and a multi-tool.\r\n<br/>\r\nSet Up Camp Early:\r\n\r\nArrive at your campsite with plenty of daylight left to set up your camp, so you\'re not struggling in the dark. Practice setting up your tent before you go camping to save time and frustration.\r\n<br/>\r\nCampfire Safety:\r\n\r\nIf campfires are allowed at your campsite, follow all fire safety rules. Use established fire rings or fire pans, keep the fire small, and always have water and a shovel nearby.\r\n<br/>\r\nRespect Nature:\r\n\r\nLeave no trace by following Leave No Trace principles. Pack out all trash and dispose of waste properly. Avoid disturbing wildlife and be mindful of your impact on the environment.\r\n<br/>\r\nWater Management:\r\n\r\nEnsure you have access to clean water or bring a reliable water purification system. Hydration is crucial, so drink plenty of water throughout your trip.\r\n<br/>\r\nNavigation:\r\n\r\nCarry a map and compass or GPS device, and know how to use them. Mark key waypoints and familiarize yourself with the area\'s topography and trail markers.', 'admin'),
(7, 'Here are ways to make camping more interesting', 'Hiking: Explore the surrounding wilderness by going on hikes. Many campsites offer hiking trails with varying levels of difficulty, from easy walks to challenging backcountry treks.\r\n<br/>\r\nCamping Games: Bring along board games, card games, or camp-friendly games like horseshoes or cornhole for entertainment during downtime.\r\n<br/>\r\nFishing: If your campsite is near a lake, river, or stream, fishing can be a relaxing and rewarding activity. Make sure to check local fishing regulations and obtain any necessary permits.\r\n<br/>\r\nWildlife Watching: Bring binoculars and a field guide to identify local wildlife. You might spot birds, deer, rabbits, and other creatures in their natural habitat.\r\n<br/>\r\nStar Gazing: Campsites away from city lights provide an excellent opportunity for stargazing. Bring a telescope or simply lay back and enjoy the night sky.\r\nPhotography: Capture the beauty of nature with your camera. Camping sites offer numerous opportunities for landscape and wildlife photography.\r\n<br/>\r\nNature Walks: Take leisurely walks around the campsite to observe local flora and fauna, learn about plants, or listen to the sounds of the forest.\r\n<br/>\r\nCampfire Cooking: Experiment with campfire cooking by roasting marshmallows, making foil packet meals, or baking campfire pies.\r\n<br/>\r\nGeocaching: Engage in geocaching, a treasure-hunting activity that uses GPS coordinates to find hidden caches in nature.\r\n<br/>\r\nBird Watching: If you\'re interested in ornithology, bring a pair of binoculars and a bird guide to identify and observe local bird species.\r\n<br/>\r\nRock Climbing: Some campsites offer opportunities for rock climbing or bouldering. Be sure to have the necessary equipment and skills.\r\n<br/>\r\nReading and Relaxing: Enjoy some quiet time with a good book, lying in a hammock, or simply sitting by the campfire.', 'admin'),
(9, 'hello', 'sadfsdfdf', ''),
(11, 'Going on a hike? Here\'s the must have medical kit', 'First Aid Kit:\r\n\r\nAlways carry a well-equipped first aid kit with items like bandages, antiseptic wipes, pain relievers, tweezers, and any necessary personal medications.\r\n\r\nKnow Basic First Aid:\r\n\r\nLearn basic first aid skills, such as how to treat minor injuries, manage blisters, and recognize signs of heat exhaustion, hypothermia, and altitude sickness.\r\n\r\n<br/>Sun Protection:\r\n\r\nUse sunscreen, wear a wide-brimmed hat, and cover exposed skin to protect against sunburn.\r\n\r\n<br/>Insect Repellent:\r\n\r\nUse insect repellent to prevent insect bites, and check for ticks regularly, especially in wooded areas.\r\n\r\n<br/>Foot Care:\r\n\r\nInvest in quality, moisture-wicking socks and well-fitting hiking boots to prevent blisters. Trim toenails to avoid ingrown nails.\r\n\r\n<br/>Proper Clothing:\r\n\r\nDress in layers, and choose moisture-wicking and breathable clothing to adapt to changing weather conditions. Don\'t forget to pack extra clothing in case of unexpected temperature drops.\r\n\r\n<br/>Stay Hydrated:\r\n\r\nDehydration can be a significant risk, especially in hot weather. Carry an adequate supply of clean water and drink regularly.', 'admin'),
(13, 'Hello', 'abc adsdasda', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
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
(27, 'ns@gggggggggggggggggggggmail.com', 'Cfd8iTgMKjp94OBPoI46aQ==:cdfc444708711da48169be9ee13986cf3a9f54042e743826398053869933f978', '2023-10-30 03:08:01', 'customer'),
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
(46, 'nirmal@ns.ns', 'zWHeEFYEICueOBsYvxv2bA==:d2329cfaf6050b01e752277ca546be3e009a05304c619edda54580a09535769f', '2023-10-31 11:28:13', 'customer'),
(47, 'nirmal@wl.com', 'H7D5921X7ZDnMSNQcpU9pQ==:d8a42902ba51d7cf2f78953287458c020a9e9d1f0a3f8e48978a17c06db21d3d', '2023-11-01 02:19:19', 'customer'),
(48, 'admin@wl.com', 'vPcpWPXHM3jvyNT+QepM0A==:d2c5395d2a4790fffdd2616ae313fb17357922073321257918e1853ad1f77328', '2023-11-01 02:27:13', 'admin'),
(49, 'savinda@wl.com', 'xymWM7k9s71JCoc+xFMPbw==:5887071fa59591b4aa03d6b1ca3bd37426c401089f08a26976d7ce9980ec7366', '2023-11-01 03:42:49', 'customer'),
(50, 'sarani@wl.com', 'UpoLSO5tiUINvIm5nS1ZHw==:716afc2dcfd23188b03936059a1a115b2db3654dc972f8c890805a704aaffc79', '2023-11-01 03:47:33', 'rentalservice'),
(51, 'sandali@wl.com', 'v/R24T5mJfH4o1/0i6b8QQ==:d05b3650d1eafdd3cdaa6ae11765c03af3b3cccf3dacfb6378231ca5572481ef', '2023-11-01 03:51:32', 'guide'),
(52, 'nnn@nn.nn', 'HC7OQ9o8q53ltLn/NIpPhw==:a8e4c92c2e88fe189c12014f5aaa8d2884fce3ac82871b1a6d3c4beaac2bc1bf', '2023-11-02 05:08:36', 'rentalservice'),
(53, 'nnn@nn.nn', 'fcNyvJK0xJ/A6qPJq7+WJw==:dc36c70caa3ece42e6eb201df720fd187586b010fa935d30c03237a535d9d27d', '2023-11-02 05:09:29', 'rentalservice'),
(54, 'nnn@nn.nn', 'y+YgH2xAzVniao1noAhQrg==:bfe2e6eab76d3c1b5ff67a7a9309e40b86456e77fe2d12ecbcf2767aaa1d562d', '2023-11-02 05:10:18', 'rentalservice'),
(55, 'nirmalsavinda29@gmail.com', 'aBP3b8/ylkRtxAvRSnBpAQ==:d59f7019d7b12235362b70449d7d19aac455de78a720c40967a8962608c76b51', '2023-11-02 06:29:24', 'rentalservice'),
(56, 'nirtttt@gmail.com', 'o91XfjkEtVDmnF6Z3pvKWA==:077770e583621e25caa3d620fae7ac47a33df4d2b86d11482ce5f033ef7086fc', '2023-11-02 06:30:25', 'rentalservice'),
(57, 'nirtttt@gmail.com', 'RswNXZp1UAMBQ0yo1iISdQ==:054b8741303dd3cb8aea5e74fa8109c6f12e4ee790f20a1daad0b30616af09e5', '2023-11-02 06:31:33', 'rentalservice'),
(58, 'nirtttt@gmail.com', 'tGrIqsyPnjP+mZ38lA5DPw==:92e0aaa91cec1f784e719cc46a3892238cb64bc480b576135b2b5bec7b542300', '2023-11-02 06:34:34', 'rentalservice'),
(59, 'nirtttt@gmail.com', 'biivZUEoDCAVl+tq7JUMQg==:85281f7a81ebf82186cc8be74886943a36934e98dbd016cfe9bb7b1699177674', '2023-11-02 06:34:46', 'rentalservice'),
(60, 'nirtttt@gmail.com', 'DyApCONKnDqIffPzopyIVA==:e4a4ccf66d195d251368d0f292ad86d98b1145a66b6bdaf2c353e505e476f408', '2023-11-02 06:35:06', 'rentalservice'),
(61, 'nirtttt@gmail.com', 'D324VYcW+3Jb85MvwB63yA==:d596baefc0608c2bce7f01ba307347ae898914f384e259efe536d78f47de8793', '2023-11-02 06:36:16', 'rentalservice'),
(62, 'nirtttt@gmail.com', 'YUZ5/TbOdcKxkVlKokclKw==:7afa7d584746a438cd3df38feafca12d56e19929dd22561f158978feafaa7702', '2023-11-02 06:37:13', 'rentalservice'),
(63, 'nirtttt@gmail.com', 'E91LxfqE27LobeIEwSbotg==:0a4b67dcee15289d8ced70508e67d786c451e2764d694ea24906f112ce6b7e02', '2023-11-02 06:38:29', 'rentalservice'),
(64, 'nirtttt@gmail.com', '6QMFNcSMVCjGFthGh88hxw==:ff5ce2bb3b552e908067682ce5104cb07d4f6b88f83ff631a848c31fe1daf4ff', '2023-11-02 06:40:01', 'rentalservice'),
(65, 'nirmalrrrrr9@gmail.com', 'pwaxSxERxQaQdAskxRtvTg==:c9e8ca39ddb01a6dbc616332b47aea27a67569802b7468b7a6979d6356c85d29', '2023-11-02 06:40:41', 'rentalservice'),
(66, 'nirmalrrrrr9@gmail.com', 'dQQ7cdppQw6KpjjJkCYJOg==:859e8d0091de555b9391718fae2817bbc644881262969c8fccd25ab8a25a3f7c', '2023-11-02 06:41:38', 'rentalservice'),
(67, 'nirmalrrrrr9@gmail.com', 'c4Ukk3dOfBgIeg3jGNZ7Pg==:e866b62de4f5cbf29e14b0664a1042fd153f8849e4b0bf25fd3bf142b71375e8', '2023-11-02 06:42:43', 'rentalservice'),
(68, 'nirmalrrrrr9@gmail.com', 'ft9dqX2BiyNkjKgXRPgYZQ==:ced4a9ffcedd910a0b6143204bad0b942f3593ba41afeb70f836cc7cfead4f80', '2023-11-02 06:43:48', 'rentalservice'),
(69, 'admin@abc.com', 'xjCLRKbZOwZ4/ky5aRFWyA==:c2d676bc990e7726ff9c45262930087c6e64c0230a7136f5953b7b7fbd2e5006', '2023-11-02 06:45:10', 'rentalservice'),
(70, 'nirmal@abc.net', 'KqgeiqjPzN5kaP85OQ0dfQ==:6299d235a4b6ba36ec6f9965f95f7f60615a2fabe740e4ccd7ed7edda51ca3c6', '2023-11-02 08:02:03', 'rentalservice'),
(71, 'nirmal@abc.wl', 'L5hlZkJYNEwYWBvGOoydig==:5f611787671e438ab1cc3f4fc90903d3b31be296efd8c45326bd4d8d3032c56d', '2023-11-02 08:10:47', 'rentalservice'),
(72, 'admin@ns.cohh', '+V900LsVeHnp4NmgD22tiw==:b488c9723b9fb2645309ec593984465a0047f2479f65c5883345c58fb5440695', '2023-11-02 08:13:40', 'rentalservice'),
(73, 'abc@asasd.com', '+s9VS6SnEiC9bKU7Pb/IMw==:5f78b636c137ddb27d23ac10ba50a8f1d1c744588649e26ec5b2a8821ee16cf4', '2023-11-02 11:38:01', 'rentalservice'),
(74, 'abc@wl.com', 'o8FiNp3xQHke1cMs6RykcQ==:eca348f031580ba682000f62e28222cf25010d8c1b018eaaa82ce9c1a29be7bf', '2023-11-03 02:06:12', 'customer');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rental_services`
--
ALTER TABLE `rental_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tips`
--
ALTER TABLE `tips`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

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
