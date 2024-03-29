-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Feb 25, 2024 at 02:53 PM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`%` PROCEDURE `CompleteRentProcess` (IN `customerID` INT)   BEGIN
    -- Variable to hold the last inserted rent ID
    DECLARE lastRentID INT;
    DECLARE lastPaymentID INT; 
    DECLARE reference_number VARCHAR(255);
    DECLARE total_amount DECIMAL(10, 2);

    -- Insert into rent table and capture the last inserted ID
    INSERT INTO rent (customer_id, start_date, end_date, status, total, paid_amount)
    SELECT customer_id, start_date, end_date, 'pending', SUM(equipment.fee), '0.00'
    FROM cart
    JOIN cart_item ON cart.id = cart_item.cart_id
    JOIN item ON cart_item.item_id = item.id
    JOIN equipment ON item.equipment_id = equipment.id
    WHERE cart.customer_id = customerID
    GROUP BY cart.id;

    -- Capture the last inserted ID
    SET lastRentID = LAST_INSERT_ID();

    -- Insert into rent_item for each item related to the cart
    INSERT INTO rent_item (rent_id, item_id)
    SELECT lastRentID, item.id
    FROM cart
    JOIN cart_item ON cart.id = cart_item.cart_id
    JOIN item ON cart_item.item_id = item.id
    WHERE cart.customer_id = customerID;
    
-- total amount of the rent
    SELECT total INTO total_amount
    FROM rent
    WHERE id = lastRentID;
    


    -- INSERT INTO payment (amount) 
    -- SELECT total
    -- FROM rent
    -- WHERE id = lastRentID;

    INSERT INTO payment (amount, status)
    VALUES (total_amount, 'pending');


    -- Generate Reference Number
    -- get the last inserted payment ID
    SET lastPaymentID = LAST_INSERT_ID();
    SET reference_number = CONCAT('RNT', LPAD(lastPaymentID, 5, '0'));

    UPDATE payment
    SET reference_number = reference_number
    WHERE id = lastPaymentID;

    INSERT INTO rent_pay (rent_id, payment_id) 
    SELECT lastRentID, lastPaymentID;




    -- Delete cart items associated with the customer's cart
    DELETE cart_item FROM cart_item
    JOIN cart ON cart_item.cart_id = cart.id
    WHERE cart.customer_id = customerID;


    -- Delete the cart associated with the customer
    DELETE FROM cart
    WHERE customer_id = customerID;

    -- Return the last inserted payment ID
    SELECT reference_number AS orderID , total_amount AS totalAmount;

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetAvailableEquipment` (IN `RentalServiceID` INT, IN `StartTime` DATETIME, IN `EndTime` DATETIME)   BEGIN
    SELECT e.id AS EquipmentID, 
           e.name AS EquipmentName, 
           COUNT(DISTINCT i.id) AS AvailableCount,
           e.count AS TotalCount
    FROM equipment e
    INNER JOIN item i ON e.id = i.equipment_id
    LEFT JOIN rent_item ri ON i.id = ri.item_id
    LEFT JOIN rent r ON ri.rent_id = r.id AND r.start_date <= EndTime AND r.end_date >= StartTime
    WHERE e.rentalservice_id = RentalServiceID
      AND i.id NOT IN (
          SELECT i.id
          FROM item i
          INNER JOIN rent_item ri ON i.id = ri.item_id
          INNER JOIN rent r ON ri.rent_id = r.id
          WHERE r.start_date <= EndTime AND r.end_date >= StartTime
      )
    GROUP BY e.id, e.name, e.count;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetAvailableEquipmentByRental` (IN `RentalServiceID` INT, IN `StartTime` DATETIME, IN `EndTime` DATETIME)   BEGIN
    SELECT e.*,
            
           COUNT(DISTINCT i.id) AS AvailableCount,
           e.count AS TotalCount
    FROM equipment e
    INNER JOIN item i ON e.id = i.equipment_id
    LEFT JOIN rent_item ri ON i.id = ri.item_id
    LEFT JOIN rent r ON ri.rent_id = r.id AND r.start_date <= EndTime AND r.end_date >= StartTime
    WHERE e.rentalservice_id = RentalServiceID
      AND i.id NOT IN (
          SELECT i.id
          FROM item i
          INNER JOIN rent_item ri ON i.id = ri.item_id
          INNER JOIN rent r ON ri.rent_id = r.id
          WHERE r.start_date <= EndTime AND r.end_date >= StartTime
      )
    GROUP BY e.id, e.name, e.count;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetAvailableItems` (IN `equipmentID` INT, IN `startDate` DATETIME, IN `endDate` DATETIME)   BEGIN
    SELECT i.*
    FROM item i
    LEFT JOIN rent_item ON i.id = rent_item.item_id
    LEFT JOIN rent ON rent_item.rent_id = rent.id
    WHERE i.equipment_id = equipmentID AND 
          i.id NOT IN (
              SELECT ri.item_id
              FROM rent_item ri
              JOIN rent r ON ri.rent_id = r.id
              WHERE r.start_date <= endDate AND r.end_date >= startDate
          )
    GROUP BY i.id;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetFilteredPaidOrders` (IN `rentalserviceID` INT, IN `filterType` VARCHAR(20))   BEGIN
    -- Define variables for dynamic date filtering
    DECLARE today DATE;
    SET today = CURDATE();

    -- Common base of the query
    SET @baseQuery = "FROM rent 
                      JOIN rent_pay ON rent.id = rent_pay.rent_id
                      JOIN payment ON rent_pay.payment_id = payment.id
                      WHERE rent.rentalservice_id = ? 
                      AND payment.status = 'completed' ";

    CASE 
        
        WHEN filterType = 'ALL' THEN
            SET @specificFilter = "";

        WHEN filterType = 'pending' THEN
            SET @specificFilter = "AND rent.status = 'pending'";

        
        WHEN filterType = 'today' THEN
            SET @specificFilter = "AND rent.start_date = CURDATE() AND rent.status = 'accepted'";
        
        WHEN filterType = 'upcoming' THEN
            SET @specificFilter = "AND rent.start_date > CURDATE() AND rent.status = 'accepted'";
        
        WHEN filterType = 'not rented' THEN
            SET @specificFilter = "AND rent.start_date < CURDATE() AND rent.status = 'accepted'";
        
        WHEN filterType = 'Rented' THEN
            SET @specificFilter = "AND rent.status = 'rented'";
        
        WHEN filterType = 'completed' THEN
            SET @specificFilter = "AND rent.status = 'completed'";
        
        WHEN filterType = 'overdued' THEN
            SET @specificFilter = "AND rent.end_date < CURDATE() AND rent.status = 'rented'";
        
        WHEN filterType = 'cancelled' THEN
            SET @specificFilter = "AND rent.status = 'cancelled'";
        
        ELSE
            SET @specificFilter = "";
    END CASE;

    SET @SQL = CONCAT("SELECT rent.*, payment.status AS payment_status ", @baseQuery, @specificFilter);
    PREPARE stmt FROM @SQL;
    SET @rentalserviceID = rentalserviceID;
    EXECUTE stmt USING @rentalserviceID;
    DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetItemListbyRentID` (IN `rent_id` INT)   BEGIN
    SELECT 
        e.id AS `equipment_id`, 
        e.name AS `equipment_name`,
        i.item_number AS `item_number`
    FROM 
        rent_item ri
    INNER JOIN item i ON ri.item_id = i.id
    INNER JOIN equipment e ON i.equipment_id = e.id
    WHERE 
        ri.rent_id = rent_id
    ORDER BY 
        e.name;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetItemsByEquipment` (IN `equipmentId` INT)   BEGIN
    SELECT 
        item.*, 
        equipment.name AS equipment_name, 
        equipment.image AS equipment_image,
        (SELECT COUNT(rent_item.rent_id)
         FROM rent_item
         JOIN rent ON rent_item.rent_id = rent.id
         WHERE rent_item.item_id = item.id
         AND rent.start_date > NOW()) AS upcoming_rent_count
         
    FROM 
        item
    JOIN 
        equipment ON item.equipment_id = equipment.id
    WHERE 
        item.equipment_id = equipmentId
    AND 
    	item.status IN  ('available','unavailable');
        
        
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetRentalDetailsByID` (IN `rent_id_param` INT)   BEGIN
    SELECT 
        r.*,
        c.name AS `customer_name`, 
        u.email AS `customer_email`,
        c.number AS `customer_number`,
        -- Aggregate equipment names and their counts into a single column
        GROUP_CONCAT(DISTINCT CONCAT(sub.equipment_name, ' (', sub.equipment_count, ')') ORDER BY sub.equipment_name SEPARATOR ', ') AS `equipment_list`
    FROM 
        rent r
    INNER JOIN customers c ON r.customer_id = c.id
    INNER JOIN users u ON c.user_id = u.id
    -- Subquery to calculate equipment counts
    INNER JOIN (
        SELECT 
            ri.rent_id, 
            e.name AS `equipment_name`, 
            COUNT(e.id) AS `equipment_count`
        FROM 
            rent_item ri
        INNER JOIN item i ON ri.item_id = i.id
        INNER JOIN equipment e ON i.equipment_id = e.id
        GROUP BY 
            ri.rent_id, e.name
    ) AS sub ON r.id = sub.rent_id
    WHERE 
        r.id = rent_id_param
    GROUP BY 
        r.id;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `getRentalsByCustomer` (IN `customer_id_param` INT)   BEGIN
    SELECT 
        r.id, 
        r.start_date AS `start`, 
        r.end_date AS `end`, 
        GROUP_CONCAT(e.name SEPARATOR ', ') AS `equipment_names`
    FROM 
        rent r
    INNER JOIN rent_item ri ON r.id = ri.rent_id
    INNER JOIN item i ON ri.item_id = i.id
    INNER JOIN equipment e ON i.equipment_id = e.id
    WHERE 
        r.customer_id = customer_id_param
    GROUP BY 
        r.id
    ORDER BY 
        r.start_date;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `IncreaseEquipmentCount` (IN `equipmentID` INT, IN `itemCount` INT)   BEGIN
    DECLARE i INT DEFAULT 0;
    
    -- Loop to insert new items into the item table
    WHILE i < itemCount DO
        INSERT INTO item (equipment_id, status) VALUES (equipmentID, 'available');
        SET i = i + 1;
    END WHILE;
    
    -- Update the total or available count in the equipment table
    UPDATE equipment
    SET count = count + itemCount
    WHERE id = equipmentID;
    
    -- Alternatively, if you're tracking available count specifically
    -- UPDATE equipment
    -- SET available_count = available_count + itemCount
    -- WHERE id = equipmentID;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `makeItemAvailable` (IN `id` INT)   BEGIN
    UPDATE item
    SET status = 'available'
    WHERE item.id = id;
    
    SELECT item.equipment_id
    FROM item
    WHERE item.id = id;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `makeItemUnavailablePermanently` (IN `id` INT)   BEGIN
    UPDATE item
    SET status = 'removed'
    WHERE item.id = id;
    
    SELECT item.equipment_id
    FROM item
    WHERE item.id = id;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `makeItemUnavailableTemporarily` (IN `id` INT)   BEGIN
    UPDATE item
    SET status = 'unavailable'
    WHERE item.id = id;
    
    SELECT item.equipment_id
    FROM item
    WHERE item.id = id;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `PayCartAndGenerateRentItems` (IN `customerID` INT)   BEGIN
    -- Variable to hold the last inserted rent ID
    DECLARE lastRentID INT;

    -- Insert into rent table and capture the last inserted ID
    INSERT INTO rent (customer_id, start_date, end_date, status, total, paid_amount)
    SELECT customer_id, start_date, end_date, 'pending', SUM(equipment.fee), '0.00'
    FROM cart
    JOIN cart_item ON cart.id = cart_item.cart_id
    JOIN item ON cart_item.item_id = item.id
    JOIN equipment ON item.equipment_id = equipment.id
    WHERE cart.customer_id = customerID
    GROUP BY cart.id;

    -- Capture the last inserted ID
    SET lastRentID = LAST_INSERT_ID();

    -- Insert into rent_item for each item related to the cart
    INSERT INTO rent_item (rent_id, item_id)
    SELECT lastRentID, item.id
    FROM cart
    JOIN cart_item ON cart.id = cart_item.cart_id
    JOIN item ON cart_item.item_id = item.id
    WHERE cart.customer_id = customerID;

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `PaymentComplete` (IN `reference_number_input` VARCHAR(255))   BEGIN
    UPDATE payment
    SET status = 'completed'
    WHERE reference_number = reference_number_input;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `ProcessCartToRentOrders` (IN `customerID` INT)   BEGIN
    DECLARE finished INT DEFAULT 0;
    DECLARE currentRentalServiceID INT;
    DECLARE totalSum DECIMAL(10, 2) DEFAULT 0.00;
    DECLARE lastRentID INT;
    DECLARE lastPaymentID INT;
    DECLARE reference_number VARCHAR(255);
    DECLARE rentAmount DECIMAL(10, 2);



    -- Cursor to select distinct rental service IDs from the cart items
    DECLARE curRentalService CURSOR FOR 
        SELECT DISTINCT equipment.rentalservice_id
        FROM cart
        JOIN cart_item ON cart.id = cart_item.cart_id
        JOIN item ON cart_item.item_id = item.id
        JOIN equipment ON item.equipment_id = equipment.id
        WHERE cart.customer_id = customerID;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished = 1;

    SET lastPaymentID = 0;

    OPEN curRentalService;

    -- Loop through each rental service ID
    rentalServiceLoop:LOOP
        FETCH curRentalService INTO currentRentalServiceID;
        IF finished = 1 THEN 
            LEAVE rentalServiceLoop;
        END IF;
        
    -- Insert a rent order for the current rental service and accumulate the total
    INSERT INTO rent (customer_id, rentalservice_id, start_date, end_date, status, total, paid_amount)
    SELECT 
        customer_id, 
        equipment.rentalservice_id, 
        MIN(start_date) AS start_date, 
        MAX(end_date) AS end_date, 
        'pending' AS status, 
        SUM(equipment.fee) * DATEDIFF(MAX(end_date), MIN(start_date)) + SUM(equipment.standard_fee) AS total, -- Add standard fee to the total amount
        0.00 AS paid_amount
    FROM cart
    JOIN cart_item ON cart.id = cart_item.cart_id
    JOIN item ON cart_item.item_id = item.id
    JOIN equipment ON item.equipment_id = equipment.id
    WHERE cart.customer_id = customerID AND equipment.rentalservice_id = currentRentalServiceID
    GROUP BY cart.customer_id, equipment.rentalservice_id;

        SET lastRentID = LAST_INSERT_ID();

        INSERT INTO rent_request (rent_id) VALUES (lastRentID);

        SELECT total INTO rentAmount FROM rent WHERE id = lastRentID;

        SET totalSum = totalSum + rentAmount;

        -- Insert rent items for the current rent order
        INSERT INTO rent_item (rent_id, item_id)
        SELECT lastRentID, item.id
        FROM cart_item
        JOIN item ON cart_item.item_id = item.id
        JOIN equipment ON item.equipment_id = equipment.id
        WHERE cart_item.cart_id IN (
            SELECT id FROM cart WHERE customer_id = customerID
        ) AND equipment.rentalservice_id = currentRentalServiceID;


         -- Insert into rent_pay for each rent order
        INSERT INTO rent_pay (rent_id, payment_id, amount)
        VALUES (lastRentID, lastPaymentID, rentAmount);

        
    END LOOP;

    CLOSE curRentalService;

    -- Create a single payment entry for the total sum of all rent orders
    INSERT INTO payment (amount, status) VALUES (totalSum, 'pending');
    SET lastPaymentID = LAST_INSERT_ID();
    SET reference_number = CONCAT('RNT', LPAD(lastPaymentID, 5, '0'));
    
    -- Update the payment with the generated reference number
    UPDATE payment SET reference_number = reference_number WHERE id = lastPaymentID;

    -- Update rent_pay with the payment_id after creating the payment
    UPDATE rent_pay SET payment_id = lastPaymentID WHERE payment_id = 0;

    -- Clean up cart items and the cart for the customer
    DELETE FROM cart_item WHERE cart_id IN (SELECT id FROM cart WHERE customer_id = customerID);
    DELETE FROM cart WHERE customer_id = customerID;

    -- Return the reference number and total amount for confirmation or further processing
    SELECT reference_number AS orderID, totalSum AS totalAmount;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `ProcessRentOrders` (`customerID` INT)   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE currentRentalServiceID INT;
    DECLARE curRentalService CURSOR FOR 
        SELECT DISTINCT equipment.rentalservice_id
        FROM cart
        JOIN cart_item ON cart.id = cart_item.cart_id
        JOIN item ON cart_item.item_id = item.id
        JOIN equipment ON item.equipment_id = equipment.id
        WHERE cart.customer_id = customerID;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN curRentalService;

    read_loop: LOOP
        FETCH curRentalService INTO currentRentalServiceID;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Insert into rent table and capture the last inserted ID for the current rentalservice_id
        INSERT INTO rent (customer_id, start_date, end_date, status, total, paid_amount)
        SELECT customer_id, MIN(start_date), MAX(end_date), 'pending', SUM(equipment.fee), '0.00'
        FROM cart
        JOIN cart_item ON cart.id = cart_item.cart_id
        JOIN item ON cart_item.item_id = item.id
        JOIN equipment ON item.equipment_id = equipment.id
        WHERE cart.customer_id = customerID AND equipment.rentalservice_id = currentRentalServiceID;

        -- Capture the last inserted ID for this batch
        SET @lastRentID = LAST_INSERT_ID();

        -- Insert into rent_item for each item related to the current rentalservice_id
        INSERT INTO rent_item (rent_id, item_id)
        SELECT @lastRentID, item.id
        FROM cart
        JOIN cart_item ON cart.id = cart_item.cart_id
        JOIN item ON cart_item.item_id = item.id
        JOIN equipment ON item.equipment_id = equipment.id
        WHERE cart.customer_id = customerID AND equipment.rentalservice_id = currentRentalServiceID;
    END LOOP;

    CLOSE curRentalService;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `customer_id`, `start_date`, `end_date`) VALUES
(43, 25, '2024-02-23', '2024-02-29');

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE `cart_item` (
  `id` int NOT NULL,
  `cart_id` int NOT NULL,
  `item_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart_item`
--

INSERT INTO `cart_item` (`id`, `cart_id`, `item_id`) VALUES
(90, 40, 38),
(91, 40, 4),
(92, 40, 38),
(93, 40, 38);

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
(13, 'Nirmal', 'COlombo', '0716024489', '20011783929', 46),
(14, 'Nirmal', 'Colombo', '0716024489', '200117901838', 47),
(15, 'Admin', 'COlombo', '0716024489', '200117901838', 48),
(16, 'Savinda', 'colombo', '0713056777', '200117901838', 49),
(17, 'Nirmal savi', ' Colombo', '076024481', '200117901811', 74),
(18, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 75),
(19, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 76),
(20, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 77),
(21, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 78),
(22, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 79),
(23, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 80),
(24, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 81),
(25, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 82),
(26, 'nirmal', 'Address is required', '0713458323', '200156273849', 84),
(27, 'Nirmal', '  Colombo', '0716024489', '200118603720', 85),
(28, 'Nirmal', '  Colombo', '0716024489', '200118603720', 86),
(29, 'Nirmal', '  Colombo', '0716024489', '200118603720', 88),
(30, 'Nirmal', '  Colombo', '0716024489', '200118603720', 89),
(31, 'Nirmal', '  Colombo', '0716024489', '200118603720', 90),
(32, 'Customer ', ' Colombo 5', '+94716024499', '200117293604', 107),
(33, 'Nirmal', '  Colombo', '0716024489', '200118603720', 153),
(34, 'Nirmal', '  Colombo', '0716024489', '200118603720', 155),
(35, 'Nirmal', 'No 255, Neluwa RD\nGorakaduwa', '+94716024489', '200117829352', 167);

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int NOT NULL,
  `rentalservice_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `description` text,
  `type` varchar(50) DEFAULT NULL,
  `count` int DEFAULT NULL,
  `fee` decimal(8,2) DEFAULT NULL,
  `standard_fee` decimal(6,2) NOT NULL,
  `image` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `rentalservice_id`, `name`, `cost`, `description`, `type`, `count`, `fee`, `standard_fee`, `image`) VALUES
(25, 25, 'Tent - 2 Persons', 3000.00, 'Tent for 2 Persons', 'Tent', 19, 1000.00, 0.00, '65b365fccf6dc.jpg'),
(33, 25, 'Torch 99', 4000.00, '                                                                                                            Torch            ABC                                                                                                ', 'Tent', 11, 300.00, 10.00, '65d5f3e045b7d.jpg'),
(35, 25, 'Hiking Backpack', 14000.00, 'Backpack for hiking', 'Backpack', 14, 1000.00, 0.00, '65b3685fa38ae.jpg'),
(37, 25, 'Tent', 13000.00, 'Tent for 4 ', 'Rent', 15, 1500.00, 0.00, '65bcb96e5870c.jpg'),
(38, 25, 'Abbot Jimenez', 85.00, 'Ea eiusmod id asper', 'Cooking', 70, 83.00, 0.00, '65bcc5d7c9299.jpg'),
(39, 25, 'Abbot Jimenez', 85.00, 'Ea eiusmod id asper', 'Cooking', 70, 83.00, 0.00, '65bcc5db96eb1.jpg'),
(41, 25, 'Baker Mueller', 69.00, 'Labore quis est veni', 'Footwear', 34, 6.00, 0.00, '65bcc65dcc3bf.jpg'),
(42, 25, 'Baker Mueller', 69.00, 'Labore quis est veni', 'Footwear', 34, 6.00, 0.00, '65bcc674ecbcb.jpg'),
(43, 25, 'BackPack - 80L', 25000.00, 'Black', 'Backpack', 4, 1200.00, 300.00, '65c38635992f2.jpg'),
(46, 25, 'ABC', 606.00, 'Excepturi voluptates tenetur sit incidunt.', 'Clothing', 10, 408.00, 363.00, '65d57b5ec9974.jpg'),
(47, 25, 'ABC', 606.00, 'Excepturi voluptates tenetur sit incidunt.', 'Clothing', 6, 408.00, 363.00, '65d57c6ec9297.jpg'),
(48, 25, 'ABC', 606.00, 'Excepturi voluptates tenetur sit incidunt.', 'Clothing', 6, 408.00, 363.00, '65d57d2f9de66.jpg'),
(49, 25, 'ABC', 606.00, 'Excepturi voluptates tenetur sit incidunt.', 'Clothing', 6, 408.00, 363.00, '65d57d8fedb7a.jpg'),
(50, 25, 'ABC', 606.00, 'Excepturi voluptates tenetur sit incidunt.', 'Clothing', 6, 408.00, 363.00, '65d57dc8b4232.jpg'),
(51, 25, 'ABC', 606.00, 'Excepturi voluptates tenetur sit incidunt.', 'Clothing', 6, 408.00, 363.00, '65d57ddf61565.jpg'),
(52, 25, 'ABC', 606.00, 'Excepturi voluptates tenetur sit incidunt.', 'Clothing', 9, 408.00, 363.00, '65d581590b685.jpg'),
(53, 56, 'BBQ Grill', 5600.00, 'Large            ', 'Tent', 48, 300.00, 500.00, '65d8ae9491e5c.webp'),
(61, 56, 'Cooking Set', 11000.00, '5', 'Cooking', 11, 500.00, 400.00, '65d8b04792064.webp');

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
  `user_id` int DEFAULT NULL,
  `status` enum('waiting','accepted','rejected','') NOT NULL DEFAULT 'waiting',
  `verification_document` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guides`
--

INSERT INTO `guides` (`id`, `name`, `address`, `nic`, `mobile`, `gender`, `user_id`, `status`, `verification_document`) VALUES
(1, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '2334543', '076024489', 'male', NULL, 'waiting', ''),
(2, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '2334543', '076024489', 'male', 32, 'waiting', ''),
(3, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '435453636t345', '076024489', 'male', 33, 'waiting', ''),
(4, 'Sandali Gunawardhana', 'Colombo', '200117901832', '0716024489', 'female', 51, 'waiting', ''),
(5, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 105, 'waiting', ''),
(6, 'Nirmal Savinda', ' Matugama', '200117901838', '+94716024489', 'male', 106, 'waiting', ''),
(7, 'Nirmal Savinda', ' Colombo', '200167329831', '+94716024489', 'male', 108, 'waiting', ''),
(8, 'Nirmal Savinda', '  Colombo', '200167329831', '+94716024489', 'male', 109, 'waiting', ''),
(9, 'Nirmal Savinda', '  Colombo', '200167329832', '+94716024489', 'male', 110, 'waiting', ''),
(10, 'Nirmal Savinda', '  Colombo', '200167329831', '+94716024489', 'male', 111, 'waiting', ''),
(11, 'Nirmal Savinda', '  Colombo', '200167329831', '+94716024489', 'male', 112, 'waiting', ''),
(12, 'Nirmal Savinda', '  Colombo', '200167329831', '+94716024489', 'male', 113, 'waiting', ''),
(13, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 114, 'waiting', ''),
(14, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 115, 'waiting', ''),
(15, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 116, 'waiting', ''),
(16, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 117, 'waiting', ''),
(17, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 118, 'waiting', ''),
(18, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 119, 'waiting', ''),
(19, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 120, 'waiting', ''),
(20, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 121, 'waiting', ''),
(21, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 122, 'waiting', ''),
(22, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 123, 'waiting', ''),
(23, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 124, 'waiting', ''),
(24, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 125, 'waiting', ''),
(25, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 126, 'waiting', ''),
(26, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 127, 'waiting', ''),
(27, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 129, 'waiting', ''),
(28, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 130, 'waiting', ''),
(29, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 131, 'waiting', ''),
(30, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 133, 'waiting', '65684d08461a2.pdf'),
(31, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 134, 'waiting', '65684d3aaea5f.pdf'),
(32, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 135, 'waiting', '65684d544415b.pdf'),
(33, 'Nirmal Savinda', '  Colombo', '200167329831', '+94716024489', 'male', 136, 'waiting', '65684d7f53def.pdf'),
(34, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 137, 'waiting', '65685367464ac.pdf'),
(35, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 154, 'waiting', '656dd2f4b51a2.pdf'),
(36, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 156, 'waiting', '656dd482d5148.pdf'),
(37, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 157, 'waiting', '656dd4ad4d5cd.pdf'),
(38, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 158, 'waiting', '656dd4d3e4042.pdf'),
(39, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 159, 'waiting', '656dd5371c806.pdf'),
(40, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 160, 'waiting', '656ed2dd8fadd.pdf'),
(41, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 161, 'waiting', '656eddbc6e48d.pdf'),
(42, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 162, 'waiting', '656edf173246c.pdf'),
(43, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 163, 'waiting', '656edff2b5eda.pdf'),
(44, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 164, 'waiting', '656ee545b12fc.pdf'),
(45, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 165, 'waiting', '656ee864db5fe.pdf'),
(46, 'Nirmal Savinda', '  Colombo', '200167329831', '+94716024489', 'male', 166, 'waiting', '6571be307d2f4.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `id` int NOT NULL,
  `equipment_id` int NOT NULL,
  `item_number` varchar(10) DEFAULT NULL,
  `status` enum('available','unavailable','removed','') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`id`, `equipment_id`, `item_number`, `status`) VALUES
(1, 42, 'I000258533', 'removed'),
(2, 42, 'I000258533', 'available'),
(3, 42, 'I000258533', 'available'),
(4, 42, 'I000258533', 'available'),
(5, 42, 'I000258533', 'available'),
(6, 42, 'I000258533', 'available'),
(7, 42, 'I000258533', 'available'),
(8, 42, 'I000258533', 'available'),
(9, 42, 'I000258533', 'available'),
(10, 42, 'I000258533', 'available'),
(11, 42, 'I000258533', 'available'),
(12, 42, 'I000258533', 'available'),
(13, 42, 'I000258533', 'available'),
(14, 42, 'I000258533', 'available'),
(15, 42, 'I000258533', 'available'),
(16, 42, 'I000258533', 'available'),
(17, 42, 'I000258533', 'available'),
(18, 42, 'I000258533', 'available'),
(19, 42, 'I000258533', 'available'),
(20, 42, 'I000258533', 'available'),
(21, 42, 'I000258533', 'available'),
(22, 42, 'I000258533', 'available'),
(23, 42, 'I000258533', 'available'),
(24, 42, 'I000258533', 'available'),
(25, 42, 'I000258533', 'available'),
(26, 42, 'I000258533', 'available'),
(27, 42, 'I000258533', 'available'),
(28, 42, 'I000258533', 'available'),
(29, 42, 'I000258533', 'available'),
(30, 42, 'I000258533', 'available'),
(31, 42, 'I000258533', 'available'),
(32, 42, 'I000258533', 'available'),
(33, 42, 'I000258533', 'available'),
(34, 42, 'I000258533', 'available'),
(35, 43, 'I000258533', 'available'),
(36, 43, 'I000258533', 'unavailable'),
(37, 43, 'I000258533', 'removed'),
(38, 43, 'I000258533', 'available'),
(1281, 46, 'I000000000', 'available'),
(1282, 46, 'I000000000', 'available'),
(1283, 46, 'I000000000', 'available'),
(1284, 46, 'I000000000', 'available'),
(1285, 46, 'I000000000', 'available'),
(1286, 46, 'I000000000', 'available'),
(1289, 51, 'I000000051', 'available'),
(1290, 51, 'I000000051', 'available'),
(1291, 51, 'I000000051', 'available'),
(1292, 51, 'I000000051', 'available'),
(1293, 51, 'I000000051', 'available'),
(1294, 51, 'I000000051', 'available'),
(1295, 52, 'I000522592', 'available'),
(1296, 52, 'I000521922', 'available'),
(1297, 52, 'I000526083', 'available'),
(1298, 52, 'I000527653', 'available'),
(1299, 52, 'I000526805', 'available'),
(1300, 52, 'I000524683', 'available'),
(1301, 52, 'I000525196', 'available'),
(1302, 52, 'I000524348', 'available'),
(1303, 52, 'I000525154', 'available'),
(1304, 52, 'I000526721', 'available'),
(1305, 52, 'I000525205', 'available'),
(1306, 52, 'I000524864', 'available'),
(1307, 46, 'I000464492', 'available'),
(1308, 46, 'I000461920', 'available'),
(1309, 46, 'I000464124', 'available'),
(1310, 46, 'I000467016', 'available'),
(1311, 37, 'I000371584', 'available'),
(1312, 37, 'I000378307', 'available'),
(1313, 37, 'I000371419', 'available'),
(1314, 37, 'I000377135', 'available'),
(1315, 37, 'I000373418', 'available'),
(1316, 37, 'I000373686', 'available'),
(1317, 37, 'I000377175', 'available'),
(1318, 37, 'I000371715', 'available'),
(1319, 37, 'I000373619', 'available'),
(1320, 37, 'I000372949', 'available'),
(1321, 37, 'I000372887', 'available'),
(1322, 37, 'I000374590', 'available'),
(1323, 35, 'I000357657', 'available'),
(1324, 35, 'I000358215', 'available'),
(1325, 35, 'I000359871', 'available'),
(1326, 35, 'I000356790', 'available'),
(1327, 35, 'I000358809', 'available'),
(1328, 35, 'I000352302', 'available'),
(1329, 25, 'I000251527', 'removed'),
(1330, 25, 'I000259566', 'removed'),
(1331, 25, 'I000254803', 'available'),
(1332, 25, 'I000252679', 'unavailable'),
(1333, 25, 'I000254617', 'removed'),
(1334, 25, 'I000254975', 'unavailable'),
(1335, 25, 'I000259610', 'removed'),
(1336, 25, 'I000257921', 'available'),
(1337, 25, 'I000254915', 'unavailable'),
(1338, 25, 'I000257653', 'available'),
(1339, 25, 'I000254522', 'available'),
(1340, 25, 'I000252431', 'available'),
(1341, 25, 'I000254972', 'available'),
(1342, 25, 'I000257569', 'available'),
(1343, 25, 'I000258541', 'available'),
(1344, 25, 'I000256111', 'available'),
(1345, 25, 'I000254121', 'available'),
(1346, 25, 'I000257307', 'available'),
(1347, 25, 'I000258676', 'available'),
(1348, 25, 'I000255603', 'available'),
(1349, 25, 'I000253347', 'unavailable'),
(1350, 25, 'I000259992', 'available'),
(1351, 25, 'I000252917', 'unavailable'),
(1352, 25, 'I000251613', 'available'),
(1353, 25, 'I000253669', 'available'),
(1354, 25, 'I000257983', 'available'),
(1355, 25, 'I000259911', 'available'),
(1356, 25, 'I000256605', 'available'),
(1357, 33, 'I000337009', 'removed'),
(1358, 33, 'I000331367', 'unavailable'),
(1359, 33, 'I000332808', 'unavailable'),
(1360, 33, 'I000338939', 'removed'),
(1361, 33, 'I000335379', 'unavailable'),
(1362, 33, 'I000336535', 'available'),
(1363, 33, 'I000336536', 'available'),
(1364, 33, 'I000333076', 'available'),
(1365, 33, 'I000339998', 'available'),
(1366, 33, 'I000335347', 'available'),
(1367, 33, 'I000334741', 'available'),
(1368, 33, 'I000336665', 'available'),
(1369, 33, 'I000339861', 'available'),
(1370, 33, 'I000336176', 'available'),
(1371, 33, 'I000339296', 'available'),
(1372, 33, 'I000338955', 'available'),
(1373, 53, 'I000536707', 'available'),
(1374, 53, 'I000535441', 'available'),
(1375, 53, 'I000539347', 'available'),
(1376, 53, 'I000532192', 'available'),
(1377, 61, 'I000619218', 'available'),
(1378, 61, 'I000615497', 'available'),
(1379, 61, 'I000616237', 'available'),
(2294, 53, 'I000534927', 'available'),
(2295, 53, 'I000538545', 'available'),
(2296, 53, 'I000533343', 'available'),
(2297, 53, 'I000533787', 'available'),
(2310, 53, 'I000531268', 'available'),
(2311, 53, 'I000539029', 'available'),
(2312, 53, 'I000532004', 'available'),
(2313, 53, 'I000535119', 'available'),
(2314, 61, 'I000614347', 'available'),
(2315, 61, 'I000615804', 'available'),
(2316, 61, 'I000619058', 'available'),
(2317, 61, 'I000619514', 'available'),
(2318, 61, 'I000614886', 'available'),
(2319, 61, 'I000614236', 'available'),
(2320, 61, 'I000616301', 'available'),
(2321, 61, 'I000612358', 'available'),
(2322, 53, 'I000538947', 'available'),
(2323, 53, 'I000536049', 'available'),
(2329, 53, 'I000531598', 'available'),
(2330, 53, 'I000538571', 'available'),
(2331, 53, 'I000534419', 'available'),
(2332, 53, 'I000538555', 'available'),
(2333, 53, 'I000539943', 'available'),
(2334, 53, 'I000535798', 'available'),
(2335, 53, 'I000539428', 'available'),
(2336, 53, 'I000534620', 'available'),
(2337, 53, 'I000531519', 'available'),
(2338, 53, 'I000538607', 'available'),
(2339, 53, 'I000534097', 'available'),
(2340, 53, 'I000536838', 'available'),
(2341, 53, 'I000537906', 'available'),
(2342, 53, 'I000539707', 'available'),
(2343, 53, 'I000534490', 'available'),
(2344, 53, 'I000531133', 'available'),
(2345, 53, 'I000536331', 'available'),
(2346, 53, 'I000532327', 'available'),
(2347, 53, 'I000539047', 'available'),
(2348, 53, 'I000533282', 'available'),
(2349, 53, 'I000533365', 'available'),
(2350, 53, 'I000531501', 'available'),
(2351, 53, 'I000535762', 'available'),
(2352, 53, 'I000531706', 'available'),
(2353, 53, 'I000537695', 'available'),
(2354, 53, 'I000539192', 'available'),
(2355, 53, 'I000534981', 'available'),
(2356, 53, 'I000539364', 'available'),
(2357, 53, 'I000534535', 'available'),
(2358, 53, 'I000536817', 'available'),
(2359, 53, 'I000539795', 'available'),
(2360, 53, 'I000536472', 'available'),
(2361, 53, 'I000531130', 'available'),
(2362, 53, 'I000533386', 'available');

--
-- Triggers `item`
--
DELIMITER $$
CREATE TRIGGER `UpdateEquipmentCountAfterStatusChange` AFTER UPDATE ON `item` FOR EACH ROW BEGIN
    -- Declare a variable to hold the count of available items
    DECLARE availableItemCount INT DEFAULT 0;

    -- Check if the item's status is updated to 'unavailable'
        IF (NEW.status = 'unavailable' AND OLD.status <> 'unavailable') OR (NEW.status = 'available' AND OLD.status <> 'available') OR (NEW.status = 'removed' AND OLD.status <> 'removed') THEN

        -- Calculate the new count of available items for the equipment
        SELECT COUNT(*)
        INTO availableItemCount
        FROM item
        WHERE item.equipment_id = OLD.equipment_id AND item.status = 'available';

        -- Update the equipment count with the new value
        UPDATE equipment
        SET count = availableItemCount
        WHERE id = OLD.equipment_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_item_insert` BEFORE INSERT ON `item` FOR EACH ROW BEGIN
  DECLARE random4Digit INT;
  SET random4Digit = FLOOR(RAND() * 9000) + 1000; -- Generates a random 4-digit number
  
  -- Sets the `item_number` for the new row
  SET NEW.item_number = CONCAT('I', LPAD(NEW.equipment_id, 5, '0'), random4Digit);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int NOT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `latitude`, `longitude`) VALUES
(2, 3.000000, 34.000000),
(3, 7.807752, 80.315864),
(4, 3.000000, 34.000000);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('completed','pending','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `payment_method` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `reference_number` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `datetime`, `status`, `amount`, `payment_method`, `reference_number`) VALUES
(1, '2024-02-11 14:29:28', 'pending', 1206.00, NULL, 'RNT00001'),
(2, '2024-02-11 14:54:52', 'pending', 1206.00, NULL, 'RNT00002'),
(3, '2024-02-11 14:57:20', 'pending', 1206.00, NULL, 'RNT00003'),
(4, '2024-02-11 14:58:35', 'pending', 1206.00, NULL, 'RNT00004'),
(5, '2024-02-11 14:59:19', 'completed', 1206.00, NULL, 'RNT00005'),
(6, '2024-02-11 15:01:59', 'pending', 2406.00, NULL, 'RNT00006'),
(7, '2024-02-11 15:03:32', 'pending', 1206.00, NULL, 'RNT00007'),
(8, '2024-02-11 15:05:58', 'pending', 1206.00, NULL, 'RNT00008'),
(9, '2024-02-12 12:19:42', 'pending', 1206.00, NULL, 'RNT00009'),
(10, '2024-02-12 12:27:14', 'pending', 1206.00, NULL, 'RNT00010'),
(11, '2024-02-12 12:31:52', 'pending', 1200.00, NULL, 'RNT00011'),
(12, '2024-02-12 12:48:13', 'pending', 1206.00, NULL, 'RNT00012'),
(13, '2024-02-12 12:51:24', 'pending', 1206.00, NULL, 'RNT00013'),
(14, '2024-02-12 12:54:35', 'completed', 1206.00, NULL, 'RNT00014'),
(15, '2024-02-13 10:07:17', 'pending', 1212.00, NULL, 'RNT00015'),
(16, '2024-02-14 10:26:43', 'completed', 1206.00, NULL, 'RNT00016'),
(17, '2024-02-14 10:35:18', 'completed', 1206.00, NULL, 'RNT00017'),
(18, '2024-02-16 10:27:53', 'completed', 1206.00, NULL, 'RNT00018'),
(19, '2024-02-22 08:10:30', 'pending', 408.00, NULL, 'RNT00019'),
(20, '2024-02-23 14:30:50', 'pending', 1000.00, NULL, 'RNT00020'),
(21, '2024-02-23 15:01:35', 'pending', 2100.00, NULL, 'RNT00021'),
(22, '2024-02-23 15:05:00', 'pending', 2100.00, NULL, 'RNT00022'),
(23, '2024-02-23 15:07:14', 'pending', 4000.00, NULL, 'RNT00023'),
(24, '2024-02-23 15:08:38', 'pending', 1000.00, NULL, 'RNT00024'),
(25, '2024-02-23 15:10:22', 'pending', 800.00, NULL, 'RNT00025'),
(26, '2024-02-23 15:12:48', 'pending', 2100.00, NULL, 'RNT00026'),
(27, '2024-02-23 15:16:54', 'pending', 2100.00, NULL, 'RNT00027'),
(28, '2024-02-23 15:21:21', 'pending', 1000.00, NULL, 'RNT00028'),
(29, '2024-02-23 15:22:02', 'pending', 0.00, NULL, 'RNT00029'),
(30, '2024-02-23 15:27:35', 'completed', 300.00, NULL, 'RNT00030'),
(31, '2024-02-23 15:30:00', 'pending', 1000.00, NULL, 'RNT00031'),
(32, '2024-02-23 15:34:26', 'completed', 2100.00, NULL, 'RNT00032'),
(33, '2024-02-24 05:50:13', 'pending', 1500.00, NULL, 'RNT00033'),
(34, '2024-02-24 06:51:46', 'completed', 1500.00, NULL, 'RNT00034'),
(35, '2024-02-24 07:10:10', 'completed', 27900.00, NULL, 'RNT00035'),
(36, '2024-02-24 07:13:35', 'completed', 5110.00, NULL, 'RNT00036'),
(37, '2024-02-24 09:57:53', 'pending', 2610.00, NULL, 'RNT00037'),
(38, '2024-02-24 10:00:22', 'completed', 2710.00, NULL, 'RNT00038'),
(39, '2024-02-24 18:37:48', 'completed', 22900.00, NULL, 'RNT00039'),
(40, '2024-02-25 07:31:42', 'completed', 9310.00, NULL, 'RNT00040'),
(41, '2024-02-25 10:24:39', 'completed', 5310.00, NULL, 'RNT00041'),
(42, '2024-02-25 10:28:02', 'completed', 1310.00, NULL, 'RNT00042');

-- --------------------------------------------------------

--
-- Table structure for table `rent`
--

CREATE TABLE `rent` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `rentalservice_id` int NOT NULL DEFAULT '25',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('pending','rented','completed','cancelled','accepted') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'pending',
  `sub_status` varchar(255) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rent`
--

INSERT INTO `rent` (`id`, `customer_id`, `rentalservice_id`, `start_date`, `end_date`, `status`, `sub_status`, `total`, `paid_amount`, `update_at`, `created_at`) VALUES
(5, 32, 25, '2024-02-05', '2025-02-27', 'pending', NULL, 2412.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(6, 32, 25, '2024-02-05', '2025-02-27', 'pending', NULL, 2412.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(7, 32, 25, '2024-06-11', '2024-07-17', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(8, 32, 25, '2024-02-13', '2024-03-26', 'pending', NULL, 1200.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(9, 32, 25, '2024-02-29', '2024-04-17', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(10, 32, 25, '2024-02-07', '2024-04-25', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(11, 32, 25, '2024-02-14', '2024-04-29', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(12, 32, 25, '2024-02-22', '2024-04-29', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(13, 32, 25, '2024-02-22', '2024-04-29', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(14, 32, 25, '2024-02-22', '2024-04-30', 'pending', NULL, 2406.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(15, 32, 25, '2024-02-06', '2024-02-28', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(16, 32, 25, '2024-02-12', '2024-02-29', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(17, 32, 25, '2024-02-06', '2024-02-29', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(18, 32, 25, '2024-02-13', '2024-02-28', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(19, 32, 25, '2024-02-13', '2024-02-28', 'pending', NULL, 1200.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(20, 32, 25, '2024-02-08', '2024-02-29', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(21, 32, 25, '2024-02-08', '2024-02-29', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(22, 32, 25, '2024-02-14', '2024-02-28', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(23, 32, 25, '2024-02-14', '2024-02-29', 'pending', NULL, 1212.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(24, 32, 25, '2024-02-14', '2024-02-29', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(25, 32, 25, '2024-02-13', '2024-02-29', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(26, 32, 25, '2024-02-01', '2025-02-19', 'pending', NULL, 1206.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(27, 32, 25, '2024-02-21', '2024-02-28', 'pending', NULL, 408.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(28, 32, 25, '2024-02-14', '2024-02-27', 'pending', NULL, 1000.00, 0.00, '2024-02-23 15:01:21', '2024-02-25 06:22:50'),
(29, 32, 25, '2024-02-14', '2024-02-29', 'pending', NULL, 800.00, 0.00, '2024-02-23 15:01:35', '2024-02-25 06:22:50'),
(30, 32, 25, '2024-02-14', '2024-02-29', 'pending', NULL, 1300.00, 0.00, '2024-02-23 15:01:35', '2024-02-25 06:22:50'),
(31, 32, 25, '2024-03-18', '2024-03-20', 'pending', NULL, 1300.00, 0.00, '2024-02-23 15:05:00', '2024-02-25 06:22:50'),
(32, 32, 25, '2024-03-18', '2024-03-20', 'pending', NULL, 800.00, 0.00, '2024-02-23 15:05:00', '2024-02-25 06:22:50'),
(33, 32, 25, '2024-02-21', '2024-02-29', 'pending', NULL, 3700.00, 0.00, '2024-02-23 15:07:14', '2024-02-25 06:22:50'),
(34, 32, 25, '2024-02-21', '2024-02-29', 'pending', NULL, 300.00, 0.00, '2024-02-23 15:07:14', '2024-02-25 06:22:50'),
(35, 32, 25, '2024-02-21', '2024-02-29', 'pending', NULL, 1000.00, 0.00, '2024-02-23 15:08:38', '2024-02-25 06:22:50'),
(36, 32, 25, '2024-02-20', '2024-02-29', 'pending', NULL, 800.00, 0.00, '2024-02-23 15:10:22', '2024-02-25 06:22:50'),
(37, 32, 25, '2024-02-11', '2024-02-29', 'pending', NULL, 800.00, 0.00, '2024-02-23 15:12:48', '2024-02-25 06:22:50'),
(38, 32, 25, '2024-02-11', '2024-02-29', 'pending', NULL, 1300.00, 0.00, '2024-02-23 15:12:48', '2024-02-25 06:22:50'),
(39, 32, 25, '2024-02-13', '2024-02-28', 'pending', NULL, 1300.00, 0.00, '2024-02-23 15:16:54', '2024-02-25 06:22:50'),
(40, 32, 25, '2024-02-13', '2024-02-28', 'pending', NULL, 800.00, 0.00, '2024-02-23 15:16:54', '2024-02-25 06:22:50'),
(41, 32, 25, '2024-02-21', '2024-02-29', 'pending', NULL, 1000.00, 0.00, '2024-02-23 15:21:21', '2024-02-25 06:22:50'),
(42, 32, 25, '2024-02-14', '2024-02-27', 'pending', NULL, 300.00, 0.00, '2024-02-23 15:27:35', '2024-02-25 06:22:50'),
(43, 32, 25, '2024-02-14', '2024-02-27', 'pending', NULL, 1000.00, 0.00, '2024-02-23 15:30:00', '2024-02-25 06:22:50'),
(44, 32, 25, '2024-02-12', '2024-02-28', 'pending', NULL, 1300.00, 0.00, '2024-02-23 15:34:25', '2024-02-25 06:22:50'),
(45, 32, 25, '2024-02-12', '2024-02-28', 'pending', NULL, 800.00, 0.00, '2024-02-23 15:34:26', '2024-02-25 06:22:50'),
(46, 32, 25, '2024-02-21', '2024-02-29', 'pending', NULL, 1000.00, 0.00, '2024-02-24 05:50:13', '2024-02-25 06:22:50'),
(47, 32, 56, '2024-02-21', '2024-02-29', 'pending', NULL, 500.00, 0.00, '2024-02-24 05:50:13', '2024-02-25 06:22:50'),
(48, 32, 25, '2024-02-21', '2024-02-29', 'pending', NULL, 1000.00, 0.00, '2024-02-24 06:40:06', '2024-02-25 06:22:50'),
(49, 32, 25, '2024-02-21', '2024-02-29', 'pending', NULL, 1000.00, 0.00, '2024-02-24 06:44:59', '2024-02-25 06:22:50'),
(50, 32, 25, '2024-02-21', '2024-02-29', 'pending', NULL, 1000.00, 0.00, '2024-02-24 06:51:46', '2024-02-25 06:22:50'),
(51, 32, 56, '2024-02-21', '2024-02-29', 'pending', NULL, 500.00, 0.00, '2024-02-24 06:51:46', '2024-02-25 06:22:50'),
(52, 32, 25, '2024-02-14', '2024-02-29', 'pending', NULL, 15000.00, 0.00, '2024-02-24 07:04:44', '2024-02-25 06:22:50'),
(53, 32, 25, '2024-02-14', '2024-02-29', 'pending', NULL, 15000.00, 0.00, '2024-02-24 07:10:10', '2024-02-25 06:22:50'),
(54, 32, 56, '2024-02-14', '2024-02-29', 'pending', NULL, 12900.00, 0.00, '2024-02-24 07:10:10', '2024-02-25 06:22:50'),
(55, 32, 25, '2024-02-27', '2024-02-29', 'pending', NULL, 2610.00, 0.00, '2024-02-24 07:13:35', '2024-02-25 06:22:50'),
(56, 32, 56, '2024-02-27', '2024-02-29', 'pending', NULL, 2500.00, 0.00, '2024-02-24 07:13:35', '2024-02-25 06:22:50'),
(57, 32, 25, '2024-02-27', '2024-02-29', 'pending', NULL, 2610.00, 0.00, '2024-02-24 09:57:53', '2024-02-25 06:22:50'),
(58, 32, 25, '2024-02-20', '2024-02-29', 'pending', NULL, 2710.00, 0.00, '2024-02-24 10:00:22', '2024-02-25 06:22:50'),
(59, 32, 25, '2024-02-14', '2024-02-29', 'pending', NULL, 15000.00, 0.00, '2024-02-24 18:36:21', '2024-02-25 06:22:50'),
(60, 32, 25, '2024-02-14', '2024-02-29', 'completed', NULL, 15000.00, 0.00, '2024-02-24 18:37:48', '2024-02-25 06:22:50'),
(61, 32, 56, '2024-02-14', '2024-02-29', 'pending', NULL, 7900.00, 0.00, '2024-02-24 18:37:48', '2024-02-25 06:22:50'),
(62, 32, 25, '2024-02-25', '2024-02-29', 'cancelled', NULL, 5210.00, 0.00, '2024-02-25 07:52:01', '2024-02-25 07:31:42'),
(63, 32, 56, '2024-02-25', '2024-02-29', 'pending', NULL, 4100.00, 0.00, '2024-02-25 07:31:42', '2024-02-25 07:31:42'),
(64, 32, 25, '2024-02-25', '2024-02-28', 'accepted', NULL, 3910.00, 0.00, '2024-02-25 10:25:23', '2024-02-25 10:24:39'),
(65, 32, 56, '2024-02-25', '2024-02-28', 'pending', NULL, 1400.00, 0.00, '2024-02-25 10:24:39', '2024-02-25 10:24:39'),
(66, 32, 25, '2024-02-28', '2024-02-29', 'accepted', NULL, 1310.00, 0.00, '2024-02-25 10:28:56', '2024-02-25 10:28:02');

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
  `user_id` int DEFAULT NULL,
  `status` enum('waiting','accepted','rejected','') NOT NULL DEFAULT 'waiting',
  `verification_document` text,
  `location_id` int DEFAULT NULL,
  `image` varchar(255) NOT NULL DEFAULT '1.webp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rental_services`
--

INSERT INTO `rental_services` (`id`, `name`, `address`, `regNo`, `mobile`, `user_id`, `status`, `verification_document`, `location_id`, `image`) VALUES
(1, 'Nirmal Savinda', 'No 255, Neluwa RD', '453453', '076024489', 26, 'waiting', NULL, NULL, '1.webp'),
(2, 'Sandali Gunawardena', 'Colombo', '353434', '+94716033484', 27, 'waiting', NULL, NULL, '1.webp'),
(3, 'Gayandee Rajapaksha', 'Colombo', 'NS', '0716039989', 28, 'waiting', NULL, NULL, '1.webp'),
(4, 'Sarani ', 'Hettiarachchi', '342332323', '0786023989', 44, 'accepted', NULL, NULL, '1.webp'),
(5, 'Rental SHop', 'Colombo', 'B092342343', '0716024489', 50, 'waiting', NULL, NULL, '1.webp'),
(6, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS', '076024489', 52, 'waiting', NULL, NULL, '1.webp'),
(7, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS', '+94716024489', 53, 'waiting', NULL, NULL, '1.webp'),
(8, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS', '+94716024489', 54, 'waiting', NULL, NULL, '1.webp'),
(9, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS675757', '+94716024489', 55, 'waiting', NULL, NULL, '1.webp'),
(10, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 56, 'waiting', NULL, NULL, '1.webp'),
(11, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 57, 'waiting', NULL, NULL, '1.webp'),
(12, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 58, 'waiting', NULL, NULL, '1.webp'),
(13, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 59, 'waiting', NULL, NULL, '1.webp'),
(14, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 60, 'waiting', NULL, NULL, '1.webp'),
(15, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 61, 'waiting', NULL, NULL, '1.webp'),
(16, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'NS765675', '+94716024489', 62, 'waiting', NULL, NULL, '1.webp'),
(17, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'sq32434234', '+94716024489', 66, 'waiting', NULL, NULL, '1.webp'),
(18, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'sq32434234', '+94716024489', 67, 'waiting', NULL, NULL, '1.webp'),
(19, 'NS', 'No 255, Neluwa RD\r\nGorakaduwa', 'sq32434234', '+94716024489', 68, 'rejected', NULL, NULL, '1.webp'),
(20, 'Sandali', ' ABC ABC', '200134245754', '076024489', 69, 'waiting', NULL, NULL, '1.webp'),
(21, 'NS', 'no 255 ', 'b2034534534', '076024489', 70, 'waiting', NULL, NULL, '1.webp'),
(22, 'NS', ' 255 Ns ', 'b048294873', '0832873293', 71, 'waiting', '', NULL, '1.webp'),
(23, 'ANDSD dad', 'No 255, Neluwa RD\r\nGorakaduwa', 'b43532423', '076024489', 72, 'accepted', '65435a34072e4.pdf', NULL, '1.webp'),
(24, 'Nirmal', ' ABC', 'B3243354', '082372434', 73, 'waiting', '65438a19444d3.pdf', NULL, '1.webp'),
(25, 'ABC Rent', ' Colombo 04', 'B873242343', '076024489', 87, 'waiting', '', NULL, '1.webp'),
(26, 'nirmal', 'Address is required', '200156273849', '0713458323', 91, 'waiting', '', NULL, '1.webp'),
(27, 'nirmal', 'Address is required', '200156273849', '0713458323', 92, 'waiting', '', NULL, '1.webp'),
(28, 'nirmal', 'Address is required', '200156273849', '0713458323', 93, 'waiting', '', NULL, '1.webp'),
(29, 'nirmal', 'Address is required', '200156273849', '0713458323', 94, 'waiting', '', NULL, '1.webp'),
(30, 'nirmal', 'Address is required', '200156273849', '0713458323', 95, 'waiting', '6567e605c9326.pdf', NULL, '1.webp'),
(31, 'nirmal', 'Address is required', '200156273849', '0713458323', 96, 'waiting', '', NULL, '1.webp'),
(32, 'nirmal', 'Address is required', '200156273849', '0713458323', 97, 'waiting', '6567e72d368c5.pdf', NULL, '1.webp'),
(33, 'New abc', ' Colombo 3', 'B7534804', '+94716024489', 98, 'waiting', '', NULL, '1.webp'),
(34, 'nirmal', 'Address is required', '200156273849', '0713458323', 99, 'waiting', '656816bb3db5d.pdf', NULL, '1.webp'),
(35, 'nirmal', 'Address is required', '200156273849', '0713458323', 100, 'waiting', '656816f01946f.pdf', NULL, '1.webp'),
(36, 'nirmal', 'Address is required', '200156273849', '0713458323', 101, 'waiting', '6568191366355.pdf', NULL, '1.webp'),
(37, 'nirmal', 'Address is required', '200156273849', '0713458323', 102, 'accepted', '65681ae928e5f.pdf', NULL, '1.webp'),
(38, 'nirmal', 'Address is required', '200156273849', '0713458323', 103, 'accepted', '65681afddbed4.pdf', NULL, '1.webp'),
(39, 'NS', 'No 255, Neluwa RD\nGorakaduwa', 'NS', '+94716024489', 104, 'waiting', '65681b3c104c8.pdf', NULL, '1.webp'),
(40, 'nirmal', 'Address is required', '200156273849', '0713458323', 128, 'waiting', '65684bdfa228f.pdf', NULL, '1.webp'),
(41, 'nirmal', 'Address is required', '200156273849', '0713458323', 132, 'rejected', '65684cb125d74.pdf', NULL, '1.webp'),
(42, 'nirmal', 'Address is required', '200156273849', '0713458323', 138, 'rejected', '6568539c85101.pdf', NULL, '1.webp'),
(43, 'Nirmal', 'No 255 Neluwa Rd\nGorakaduwa', 'B03279483409', '+94716024489', 168, 'waiting', '658ae5b7ee08d.pdf', NULL, '1.webp'),
(44, 'Jamey McClure', '390 Marco Mews', 'NS43454534', '+94716024489', 169, 'waiting', '65a3bbb3e45c5.webp', NULL, '1.webp'),
(45, 'Anahi Spinka', '47083 Homenick Run', 'NS', '+94716024489', 170, 'waiting', '65b88dde08df4.pdf', NULL, '1.webp'),
(46, 'Anahi Spinka', '47083 Homenick Run', 'NS', '+94716024489', 171, 'waiting', '65b88dee1b4ca.pdf', NULL, '1.webp'),
(47, 'Anahi Spinka', '47083 Homenick Run', 'NS', '+94716024489', 172, 'waiting', '65b88e27268f4.pdf', NULL, '1.webp'),
(48, 'Delaney Fadel', '87296 Keira Lock', 'NS', '+94716024489', 173, 'waiting', '65b8a94876b22.pdf', NULL, '1.webp'),
(49, 'Jeremy Schulist', '548 Katelyn Harbors', 'NS', '+94716024489', 174, 'waiting', '65b8a9a14ece7.pdf', NULL, '1.webp'),
(50, 'nirmal', 'Address is required', '200156273849', '0713458323', 175, 'waiting', '65b8aa5beea8c.pdf', NULL, '1.webp'),
(51, 'nirmal', 'Address is required', '200156273849', '0713458323', 176, 'waiting', '65b8aaac60fe6.pdf', NULL, '1.webp'),
(52, 'nirmal', 'Address is required', '200156273849', '0713458323', 177, 'waiting', '65b8aaeca63b3.pdf', NULL, '1.webp'),
(53, 'nirmal', 'Address is required', '200156273849', '0713458323', 179, 'waiting', '65b8ab6e2e9b5.pdf', NULL, '1.webp'),
(54, 'nirmal', 'Address is required', '200156273849', '0713458323', 180, 'waiting', '65b8abac9a310.pdf', 2, '1.webp'),
(55, 'Cruz Hills', '90826 Torphy Landing', 'NS', '+94716024489', 181, 'waiting', '65b8ac8050edf.pdf', 3, '1.webp'),
(56, 'nirmal', 'Address is required', '200156273849', '0713458323', 182, 'waiting', '65d8ad691543f.pdf', 4, '1.webp');

-- --------------------------------------------------------

--
-- Table structure for table `rent_item`
--

CREATE TABLE `rent_item` (
  `id` int NOT NULL,
  `rent_id` int NOT NULL,
  `item_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rent_item`
--

INSERT INTO `rent_item` (`id`, `rent_id`, `item_id`) VALUES
(1, 5, 1),
(2, 5, 1),
(3, 5, 35),
(4, 5, 35),
(8, 6, 1),
(9, 6, 1),
(10, 6, 35),
(11, 6, 35),
(15, 7, 36),
(16, 7, 2),
(18, 8, 36),
(19, 9, 36),
(20, 9, 2),
(22, 10, 36),
(23, 10, 2),
(25, 11, 36),
(26, 11, 2),
(28, 12, 36),
(29, 12, 2),
(31, 13, 36),
(32, 13, 2),
(34, 14, 36),
(35, 14, 2),
(36, 14, 36),
(37, 15, 2),
(38, 15, 36),
(40, 16, 36),
(41, 16, 2),
(43, 17, 2),
(44, 17, 36),
(46, 18, 36),
(47, 18, 2),
(49, 19, 36),
(50, 20, 2),
(51, 20, 36),
(53, 21, 36),
(54, 21, 2),
(56, 22, 36),
(57, 22, 2),
(58, 23, 2),
(59, 23, 2),
(60, 23, 36),
(61, 24, 2),
(62, 24, 36),
(64, 25, 36),
(65, 25, 2),
(67, 26, 3),
(68, 26, 37),
(69, 27, 1289),
(70, 28, 1329),
(71, 29, 1373),
(72, 29, 1377),
(74, 30, 1323),
(75, 30, 1357),
(77, 31, 1329),
(78, 31, 1357),
(80, 32, 1377),
(81, 32, 1373),
(83, 33, 1330),
(84, 33, 1311),
(85, 33, 38),
(86, 34, 1374),
(87, 35, 1331),
(88, 36, 1378),
(89, 36, 1375),
(91, 37, 1379),
(92, 37, 1376),
(94, 38, 1332),
(95, 38, 1358),
(97, 39, 1333),
(98, 39, 1359),
(100, 40, 2314),
(101, 40, 2294),
(103, 41, 1334),
(104, 42, 1360),
(105, 43, 1335),
(106, 44, 1336),
(107, 44, 1361),
(109, 45, 2295),
(110, 45, 2315),
(112, 46, 1337),
(113, 47, 2316),
(114, 48, 1338),
(115, 49, 1338),
(116, 50, 1338),
(117, 51, 2317),
(118, 52, 1339),
(119, 53, 1339),
(120, 54, 2296),
(121, 54, 2318),
(123, 55, 1340),
(124, 55, 1362),
(126, 56, 2319),
(127, 56, 2297),
(129, 57, 1341),
(130, 57, 1363),
(132, 58, 1364),
(133, 60, 1342),
(134, 61, 2320),
(135, 62, 1343),
(136, 62, 1365),
(138, 63, 2310),
(139, 63, 2321),
(141, 64, 1344),
(142, 64, 1366),
(144, 65, 2311),
(145, 66, 1329),
(146, 66, 1360);

-- --------------------------------------------------------

--
-- Table structure for table `rent_pay`
--

CREATE TABLE `rent_pay` (
  `id` int NOT NULL,
  `rent_id` int NOT NULL,
  `payment_id` int DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rent_pay`
--

INSERT INTO `rent_pay` (`id`, `rent_id`, `payment_id`, `amount`) VALUES
(5, 13, 5, NULL),
(6, 14, 6, NULL),
(7, 15, 7, NULL),
(8, 16, 8, NULL),
(9, 17, 9, NULL),
(10, 18, 10, NULL),
(11, 19, 11, NULL),
(12, 20, 12, NULL),
(13, 21, 13, NULL),
(14, 22, 14, NULL),
(15, 23, 15, NULL),
(16, 24, 16, NULL),
(17, 25, 17, NULL),
(18, 26, 18, NULL),
(19, 27, 19, NULL),
(21, 50, 34, 1000.00),
(22, 51, 34, 500.00),
(23, 53, 35, 15000.00),
(24, 54, 35, 12900.00),
(25, 55, 36, 2610.00),
(26, 56, 36, 2500.00),
(27, 57, 37, 2610.00),
(28, 58, 38, 2710.00),
(29, 60, 39, 15000.00),
(30, 61, 39, 7900.00),
(31, 62, 40, 5210.00),
(32, 63, 40, 4100.00),
(33, 64, 41, 3910.00),
(34, 65, 41, 1400.00),
(35, 66, 42, 1310.00);

-- --------------------------------------------------------

--
-- Table structure for table `rent_request`
--

CREATE TABLE `rent_request` (
  `id` int NOT NULL,
  `rent_id` int NOT NULL,
  `customer_req` enum('rented','cancelled','completed','accepted') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `rentalservice_req` enum('rented','cancelled','completed','accepted') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rent_request`
--

INSERT INTO `rent_request` (`id`, `rent_id`, `customer_req`, `rentalservice_req`, `update_at`) VALUES
(1, 60, 'rented', 'completed', '2024-02-25 05:45:21'),
(2, 61, NULL, NULL, '2024-02-24 18:37:48'),
(3, 62, NULL, 'cancelled', '2024-02-25 07:52:01'),
(4, 63, NULL, NULL, '2024-02-25 07:31:42'),
(5, 64, NULL, 'accepted', '2024-02-25 10:25:23'),
(6, 65, NULL, NULL, '2024-02-25 10:24:39'),
(7, 66, NULL, 'accepted', '2024-02-25 10:28:56');

--
-- Triggers `rent_request`
--
DELIMITER $$
CREATE TRIGGER `RentStatus` AFTER UPDATE ON `rent_request` FOR EACH ROW BEGIN
    -- Check if both columns have the same value and it's 'rented'
    IF NEW.customer_req = NEW.rentalservice_req AND NEW.customer_req = 'rented' THEN
        UPDATE rent
        SET status = 'rented'
        WHERE id = NEW.rent_id;
    -- Additionally, check if both columns have the same value and it's 'cancel'
    ELSEIF NEW.customer_req = NEW.rentalservice_req AND NEW.customer_req = 'cancel' THEN
        UPDATE rent
        SET status = 'cancelled'
        WHERE id = NEW.rent_id;


    -- Check if rentalservice_req is 'completed'
    ELSEIF NEW.rentalservice_req = 'completed' THEN
        UPDATE rent
        SET status = 'completed'
        WHERE id = NEW.rent_id;


    ELSEIF NEW.rentalservice_req = 'accepted' THEN
        UPDATE rent
        SET status = 'accepted'
        WHERE id = NEW.rent_id;

    ELSEIF NEW.rentalservice_req = 'cancelled' THEN
        UPDATE rent
        SET status = 'cancelled'
        WHERE id = NEW.rent_id;

    END IF;
END
$$
DELIMITER ;

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
(4, 'Hello', 'ABC', 'admin'),
(6, 'Camping? Read these before you plan!', 'Choose the Right Campsite:\r\n\r\nResearch and select a campsite that suits your preferences and needs, whether it\'s a developed campground with amenities or a remote backcountry site. Check for reservation requirements and availability.\r\n<br/>\r\nCheck the Weather Forecast:\r\n\r\nStay updated on the weather forecast for your camping destination and plan accordingly. Be prepared for changes in weather, and bring appropriate clothing and gear.\r\n<br/>\r\nPack Properly:\r\n\r\nCreate a checklist to ensure you bring all the necessary camping gear, including tents, sleeping bags, sleeping pads, cooking equipment, and clothing. Don\'t forget essentials like a first aid kit, insect repellent, and a multi-tool.\r\n<br/>\r\nSet Up Camp Early:\r\n\r\nArrive at your campsite with plenty of daylight left to set up your camp, so you\'re not struggling in the dark. Practice setting up your tent before you go camping to save time and frustration.\r\n<br/>\r\nCampfire Safety:\r\n\r\nIf campfires are allowed at your campsite, follow all fire safety rules. Use established fire rings or fire pans, keep the fire small, and always have water and a shovel nearby.\r\n<br/>\r\nRespect Nature:\r\n\r\nLeave no trace by following Leave No Trace principles. Pack out all trash and dispose of waste properly. Avoid disturbing wildlife and be mindful of your impact on the environment.\r\n<br/>\r\nWater Management:\r\n\r\nEnsure you have access to clean water or bring a reliable water purification system. Hydration is crucial, so drink plenty of water throughout your trip.\r\n<br/>\r\nNavigation:\r\n\r\nCarry a map and compass or GPS device, and know how to use them. Mark key waypoints and familiarize yourself with the area\'s topography and trail markers.', 'admin'),
(7, 'Here are ways to make camping more interesting', 'Hiking: Explore the surrounding wilderness by going on hikes. Many campsites offer hiking trails with varying levels of difficulty, from easy walks to challenging backcountry treks.\r\n<br/>\r\nCamping Games: Bring along board games, card games, or camp-friendly games like horseshoes or cornhole for entertainment during downtime.\r\n<br/>\r\nFishing: If your campsite is near a lake, river, or stream, fishing can be a relaxing and rewarding activity. Make sure to check local fishing regulations and obtain any necessary permits.\r\n<br/>\r\nWildlife Watching: Bring binoculars and a field guide to identify local wildlife. You might spot birds, deer, rabbits, and other creatures in their natural habitat.\r\n<br/>\r\nStar Gazing: Campsites away from city lights provide an excellent opportunity for stargazing. Bring a telescope or simply lay back and enjoy the night sky.\r\nPhotography: Capture the beauty of nature with your camera. Camping sites offer numerous opportunities for landscape and wildlife photography.\r\n<br/>\r\nNature Walks: Take leisurely walks around the campsite to observe local flora and fauna, learn about plants, or listen to the sounds of the forest.\r\n<br/>\r\nCampfire Cooking: Experiment with campfire cooking by roasting marshmallows, making foil packet meals, or baking campfire pies.\r\n<br/>\r\nGeocaching: Engage in geocaching, a treasure-hunting activity that uses GPS coordinates to find hidden caches in nature.\r\n<br/>\r\nBird Watching: If you\'re interested in ornithology, bring a pair of binoculars and a bird guide to identify and observe local bird species.\r\n<br/>\r\nRock Climbing: Some campsites offer opportunities for rock climbing or bouldering. Be sure to have the necessary equipment and skills.\r\n<br/>\r\nReading and Relaxing: Enjoy some quiet time with a good book, lying in a hammock, or simply sitting by the campfire.', 'admin'),
(9, 'hello', 'sadfsdfdf', ''),
(11, 'Going on a hike? Here\'s the must have medical kit', 'First Aid Kit:\r\n\r\nAlways carry a well-equipped first aid kit with items like bandages, antiseptic wipes, pain relievers, tweezers, and any necessary personal medications.\r\n\r\nKnow Basic First Aid:\r\n\r\nLearn basic first aid skills, such as how to treat minor injuries, manage blisters, and recognize signs of heat exhaustion, hypothermia, and altitude sickness.\r\n\r\n<br/>Sun Protection:\r\n\r\nUse sunscreen, wear a wide-brimmed hat, and cover exposed skin to protect against sunburn.\r\n\r\n<br/>Insect Repellent:\r\n\r\nUse insect repellent to prevent insect bites, and check for ticks regularly, especially in wooded areas.\r\n\r\n<br/>Foot Care:\r\n\r\nInvest in quality, moisture-wicking socks and well-fitting hiking boots to prevent blisters. Trim toenails to avoid ingrown nails.\r\n\r\n<br/>Proper Clothing:\r\n\r\nDress in layers, and choose moisture-wicking and breathable clothing to adapt to changing weather conditions. Don\'t forget to pack extra clothing in case of unexpected temperature drops.\r\n\r\n<br/>Stay Hydrated:\r\n\r\nDehydration can be a significant risk, especially in hot weather. Carry an adequate supply of clean water and drink regularly.', 'admin'),
(13, 'Hello', 'abc adsdasda', 'admin'),
(15, 'adsadasd', 'abc', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('customer','rentalservice','guide','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `date`, `role`, `is_verified`) VALUES
(26, 'admin@ns.com', 'FIy1aXlTYUSCvSy4LoQXBg==:b3eb8a43d487b51f89ee9e5d20b10cd5a630e196fc6dae771e1ee0b234cd5314', '2023-10-30 03:07:06', 'customer', 1),
(27, 'ns@gggggggggggggggggggggmail.com', 'Cfd8iTgMKjp94OBPoI46aQ==:cdfc444708711da48169be9ee13986cf3a9f54042e743826398053869933f978', '2023-10-30 03:08:01', 'customer', 1),
(28, 'ns@rent.com', 'BoSTwn57mvT1a/TSEbMozQ==:3f80d02549f4d762c432fe677a03a2126021097684588cb02be140c8dc74b638', '2023-10-30 03:46:04', 'rentalservice', 1),
(29, 'nirmal@ns.nnnn', '4vacpY1dmsDOmSzQj3Bu5g==:4dd4390a8309a09128acb2c2bf30f2d1503b1ba347e5600f56eb2dfa304a1874', '2023-10-30 04:15:07', 'customer', 1),
(30, 'nirmal@ns.nnnn', 'RlpOMs9wlq+R6DCaL8sCMg==:00743b3a1b65a07f71062db4b11e23dc70a9a33f3584f73f39132817eba5f6c7', '2023-10-30 04:42:27', 'customer', 1),
(31, 'nirmal@ns.nnnnee', 'U1rKr8AGHukamgx0eWrz8g==:663b3aea437919cae4f9beeeb6f5e14e262fd79f3ec8e8ac5a38948829eebe4b', '2023-10-30 05:20:25', 'guide', 1),
(32, 'nirmal@ns.nnnnee', 'tnr2MFDpsp6XEyBCbtevWA==:af7e23db28f0ba2ee0214ceb3c2862b723f881f2b1704cff13b8a302a43a3d00', '2023-10-30 05:44:41', 'guide', 1),
(33, 'g@g.com', 'DmMS2mJe9gvFeL+Q6mmZHg==:71aa818f5548cc7ad17efd7ef7ac13c760ebf22e49be62100795bf19c64600a9', '2023-10-30 08:21:11', 'guide', 1),
(34, 'n@D.com', '3sjrdG1UMhzC5Scl26eG2Q==:2b9174c561d77ba3804892d426a7742e84c1bc556e8da1ed329168ff6212d486', '2023-10-30 11:46:45', 'customer', 1),
(35, 'a@a.com', 'vPg/+nAnzMhcj74EDrZTxw==:bfb2c0b4e6646659c8aafbd3dde1c483cc45398c90b25aa16bdffa96156174b8', '2023-10-30 11:47:19', 'customer', 1),
(36, 'a@a.com', 'PcPNL2WnUcmQ56d7DrFdPQ==:ccbcfec20fe7e3e8a67115097d330739a50194dc72b02149452e768be9707346', '2023-10-30 11:47:59', 'customer', 1),
(37, 'nn@nn.nn', '0ystEKmQFArj4IfJWEfoVA==:310ff9f491adf2ff18247dae95f8ab3f79ac1a124c15fe4bc978f204d4bee2fe', '2023-10-30 11:48:49', 'customer', 1),
(38, 'nnn@nn.nn', 'kjIbHrqgStSSV2Tgu43tVQ==:9c2233c5cefd858ac05b5dc647cf5f08b66e6355a22c6ad1afad7d8643815778', '2023-10-30 11:54:41', 'admin', 1),
(39, 'c@nirmal.com', 'g5qTrsdyU9eJbo2dQLYHdg==:26d0e89604e81358904c4600c43b7aa87e4d681445ea980d04da020b151b7b5b', '2023-10-31 05:26:45', 'customer', 1),
(40, 'f@dfcd.com', 'IvVLuCu93/vzGcgRmSi9RA==:552bbbab43c387a39f80898a358585686b0e73f4ee46acc37657a83b10b19350', '2023-10-31 09:07:06', 'customer', 1),
(41, 'nnn@n8.nN', 'pkheelAH6BTjG6JA9I18Fg==:795ec2ef831af6a16f898171ab918f5eb6cd9636a70dcfa08aeabca846a2c2cc', '2023-10-31 10:06:28', 'customer', 1),
(42, 'nd@23.Com', 'blW+zFwGNKZ1mroVfajzkg==:a3339524e870511b5d2f20e5eb0edd532a5bcc2c1bec05aee785f8b3ebaefd17', '2023-10-31 10:07:08', 'customer', 1),
(43, 'sfdsd@d.com', 'XHc4jFzOyhf5atisiD6/rg==:0a5b8082bc5330a15be390e0ddb68231135e1251cc9b3321f2d548f6326d7d12', '2023-10-31 10:07:57', 'customer', 1),
(44, 'ab@a.COM', 'NHqcTQiJU/rGaLMTZgc2oA==:8fea6e4b0cd0ea3a49c687355f6d0aa9b9f795ccd54d4dacc3ab79af352f6806', '2023-10-31 10:26:36', 'rentalservice', 1),
(45, 'ABC@7c.com', 'YAQq+0K+WhfSF+YG+F2aBw==:98ac02df6083ae5c7de615bb87b31c408bf87d1992a1b2c835c630d31c49fc35', '2023-10-31 10:52:27', 'customer', 1),
(46, 'nirmal@ns.ns', 'zWHeEFYEICueOBsYvxv2bA==:d2329cfaf6050b01e752277ca546be3e009a05304c619edda54580a09535769f', '2023-10-31 11:28:13', 'customer', 1),
(47, 'nirmal@wl.com', 'H7D5921X7ZDnMSNQcpU9pQ==:d8a42902ba51d7cf2f78953287458c020a9e9d1f0a3f8e48978a17c06db21d3d', '2023-11-01 02:19:19', 'customer', 1),
(48, 'admin@wl.com', 'vPcpWPXHM3jvyNT+QepM0A==:d2c5395d2a4790fffdd2616ae313fb17357922073321257918e1853ad1f77328', '2023-11-01 02:27:13', 'admin', 1),
(49, 'savinda@wl.com', 'xymWM7k9s71JCoc+xFMPbw==:5887071fa59591b4aa03d6b1ca3bd37426c401089f08a26976d7ce9980ec7366', '2023-11-01 03:42:49', 'customer', 1),
(50, 'sarani@wl.com', 'UpoLSO5tiUINvIm5nS1ZHw==:716afc2dcfd23188b03936059a1a115b2db3654dc972f8c890805a704aaffc79', '2023-11-01 03:47:33', 'rentalservice', 1),
(51, 'sandali@wl.com', 'v/R24T5mJfH4o1/0i6b8QQ==:d05b3650d1eafdd3cdaa6ae11765c03af3b3cccf3dacfb6378231ca5572481ef', '2023-11-01 03:51:32', 'guide', 1),
(52, 'nnn@nn.nn', 'HC7OQ9o8q53ltLn/NIpPhw==:a8e4c92c2e88fe189c12014f5aaa8d2884fce3ac82871b1a6d3c4beaac2bc1bf', '2023-11-02 05:08:36', 'rentalservice', 1),
(53, 'nnn@nn.nn', 'fcNyvJK0xJ/A6qPJq7+WJw==:dc36c70caa3ece42e6eb201df720fd187586b010fa935d30c03237a535d9d27d', '2023-11-02 05:09:29', 'rentalservice', 1),
(54, 'nnn@nn.nn', 'y+YgH2xAzVniao1noAhQrg==:bfe2e6eab76d3c1b5ff67a7a9309e40b86456e77fe2d12ecbcf2767aaa1d562d', '2023-11-02 05:10:18', 'rentalservice', 1),
(55, 'nirmalsavinda29@gmail.com', 'aBP3b8/ylkRtxAvRSnBpAQ==:d59f7019d7b12235362b70449d7d19aac455de78a720c40967a8962608c76b51', '2023-11-02 06:29:24', 'rentalservice', 1),
(56, 'nirtttt@gmail.com', 'o91XfjkEtVDmnF6Z3pvKWA==:077770e583621e25caa3d620fae7ac47a33df4d2b86d11482ce5f033ef7086fc', '2023-11-02 06:30:25', 'rentalservice', 1),
(57, 'nirtttt@gmail.com', 'RswNXZp1UAMBQ0yo1iISdQ==:054b8741303dd3cb8aea5e74fa8109c6f12e4ee790f20a1daad0b30616af09e5', '2023-11-02 06:31:33', 'rentalservice', 1),
(58, 'nirtttt@gmail.com', 'tGrIqsyPnjP+mZ38lA5DPw==:92e0aaa91cec1f784e719cc46a3892238cb64bc480b576135b2b5bec7b542300', '2023-11-02 06:34:34', 'rentalservice', 1),
(59, 'nirtttt@gmail.com', 'biivZUEoDCAVl+tq7JUMQg==:85281f7a81ebf82186cc8be74886943a36934e98dbd016cfe9bb7b1699177674', '2023-11-02 06:34:46', 'rentalservice', 1),
(60, 'nirtttt@gmail.com', 'DyApCONKnDqIffPzopyIVA==:e4a4ccf66d195d251368d0f292ad86d98b1145a66b6bdaf2c353e505e476f408', '2023-11-02 06:35:06', 'rentalservice', 1),
(61, 'nirtttt@gmail.com', 'D324VYcW+3Jb85MvwB63yA==:d596baefc0608c2bce7f01ba307347ae898914f384e259efe536d78f47de8793', '2023-11-02 06:36:16', 'rentalservice', 1),
(62, 'nirtttt@gmail.com', 'YUZ5/TbOdcKxkVlKokclKw==:7afa7d584746a438cd3df38feafca12d56e19929dd22561f158978feafaa7702', '2023-11-02 06:37:13', 'rentalservice', 1),
(63, 'nirtttt@gmail.com', 'E91LxfqE27LobeIEwSbotg==:0a4b67dcee15289d8ced70508e67d786c451e2764d694ea24906f112ce6b7e02', '2023-11-02 06:38:29', 'rentalservice', 1),
(64, 'nirtttt@gmail.com', '6QMFNcSMVCjGFthGh88hxw==:ff5ce2bb3b552e908067682ce5104cb07d4f6b88f83ff631a848c31fe1daf4ff', '2023-11-02 06:40:01', 'rentalservice', 1),
(65, 'nirmalrrrrr9@gmail.com', 'pwaxSxERxQaQdAskxRtvTg==:c9e8ca39ddb01a6dbc616332b47aea27a67569802b7468b7a6979d6356c85d29', '2023-11-02 06:40:41', 'rentalservice', 1),
(66, 'nirmalrrrrr9@gmail.com', 'dQQ7cdppQw6KpjjJkCYJOg==:859e8d0091de555b9391718fae2817bbc644881262969c8fccd25ab8a25a3f7c', '2023-11-02 06:41:38', 'rentalservice', 1),
(67, 'nirmalrrrrr9@gmail.com', 'c4Ukk3dOfBgIeg3jGNZ7Pg==:e866b62de4f5cbf29e14b0664a1042fd153f8849e4b0bf25fd3bf142b71375e8', '2023-11-02 06:42:43', 'rentalservice', 1),
(68, 'nirmalrrrrr9@gmail.com', 'ft9dqX2BiyNkjKgXRPgYZQ==:ced4a9ffcedd910a0b6143204bad0b942f3593ba41afeb70f836cc7cfead4f80', '2023-11-02 06:43:48', 'rentalservice', 1),
(69, 'admin@abc.com', 'xjCLRKbZOwZ4/ky5aRFWyA==:c2d676bc990e7726ff9c45262930087c6e64c0230a7136f5953b7b7fbd2e5006', '2023-11-02 06:45:10', 'rentalservice', 1),
(70, 'nirmal@abc.net', 'KqgeiqjPzN5kaP85OQ0dfQ==:6299d235a4b6ba36ec6f9965f95f7f60615a2fabe740e4ccd7ed7edda51ca3c6', '2023-11-02 08:02:03', 'rentalservice', 1),
(71, 'nirmal@abc.wl', 'L5hlZkJYNEwYWBvGOoydig==:5f611787671e438ab1cc3f4fc90903d3b31be296efd8c45326bd4d8d3032c56d', '2023-11-02 08:10:47', 'rentalservice', 1),
(72, 'admin@ns.cohh', '+V900LsVeHnp4NmgD22tiw==:b488c9723b9fb2645309ec593984465a0047f2479f65c5883345c58fb5440695', '2023-11-02 08:13:40', 'rentalservice', 1),
(73, 'abc@asasd.com', '+s9VS6SnEiC9bKU7Pb/IMw==:5f78b636c137ddb27d23ac10ba50a8f1d1c744588649e26ec5b2a8821ee16cf4', '2023-11-02 11:38:01', 'rentalservice', 1),
(74, 'abc@wl.com', 'o8FiNp3xQHke1cMs6RykcQ==:eca348f031580ba682000f62e28222cf25010d8c1b018eaaa82ce9c1a29be7bf', '2023-11-03 02:06:12', 'customer', 1),
(75, 'nirmal@wl.net', 's3z+gPex7rctkYS73KHD0g==:68bfccc3edbb18de440cd823e6ba0f5950be3edb1430952581097af6e0fe4719', '2023-11-19 08:08:11', 'customer', 1),
(76, 'nirmal@wl.net', 'gqbiEutDguV03pdDVkTpqQ==:235f31d8b680d0c61339cb5b8b9e3ac7a7f992bfad0c56977d3aea8fe33f2a18', '2023-11-19 08:17:24', 'customer', 1),
(77, 'nirmal@wl.net', '+waXccsweB6m1l4/by6tXQ==:ea9fea464e4fca617c6d399edc1ac0d8175e51c4f1aa3c842ee2d554a6f91049', '2023-11-19 08:19:58', 'customer', 1),
(78, 'nirmal@wl.net', 'mGw4ielNyvp0+2eVjkJOyQ==:22a85c097c4a6a1fb6aae29dd82205e2b1402f57ac1198b6d41c2eaa24e3327b', '2023-11-19 08:22:15', 'customer', 1),
(79, 'nirmal@wl.net', 'HyjSQvaER445HvibDWi5ZA==:435949d0d6906c404b8aaab7f5515f2ae715dd909443d2037478d58892d4a1a9', '2023-11-19 08:22:32', 'customer', 1),
(80, 'nirmal@wl.net', 'QoF7ly9A+QAUpAErkqJITQ==:99502fdf11d03bbff28a6ebebe224932a3b94b32bfd8aba4bbf2c9b3690b46ab', '2023-11-19 08:22:34', 'customer', 1),
(81, 'nirmal@wl.net', 'aRD2pqKbI4T/6bQs6cb7Tw==:504efee3132c11d9dafebbfc653c43916a14d96b5a196b45d7b1109ec003d06d', '2023-11-19 08:22:51', 'customer', 1),
(82, 'nirmal@wl.net', 'wYre6gSi6xiuKBckll1tqQ==:98ef4f2baa713db2ce45c3f8a01bd79234a6e260f453c1845385ea60ccdb3204', '2023-11-19 08:27:23', 'customer', 1),
(83, 'abc@asdd.com', 'cS1USyU0QTG74Aptmpx08g==:9dc51a73427a08a979734c6cdaf3e897b8ecc6349a69fe9faaae7fb34cdfc77f', '2023-11-22 13:31:08', 'customer', 1),
(84, 'abc@asdd.com', '3VLOIeTso17pbIzDQ4F05A==:7c250e95db1a10f83a13b3c93b651a572f45b173b589cad5b3dde514ef29cc15', '2023-11-22 13:31:59', 'customer', 1),
(85, 'nirmalsavinda@wl.com', 'kSWhw3Tq4cF55fDW3vD5FA==:b73159b7b21bdb9c3fea0ab6709bd70d92494c425030724b039c1e8ba114c4d1', '2023-11-24 07:35:34', 'customer', 1),
(86, 'nirmalsavinda@wl.com', 'nhYrJukqn+On6UkfHNw0pA==:2cc62f0f74ea646ed868f85add14d2f0374842f24eed276a2be84920a94bcf8b', '2023-11-24 07:41:42', 'customer', 1),
(87, 'rental@wl.com', 'Qm7Es0OpMHsP/RvudPRQ7g==:25d10d1b8da93519a3dc798816a9e94829c8422556c051147cf9b67f8e8d19d3', '2023-11-29 11:01:02', 'rentalservice', 1),
(88, 'nirmalsavinda@wl.com', 'duJ3PJjO8St7TDK3KWoBWQ==:094c681c7408741543f824247ff1c64b98e9c21c0cb449600059482150d5c3c5', '2023-11-29 12:08:02', 'customer', 1),
(89, 'nirmalsavinda@wl.com', 'P9bbfftFtwMO7WN4AJNk6A==:db052c55b2ef064e78ed45051696d747f97f2cd7c93c0255a794e3a0bdc727ca', '2023-11-29 12:57:25', 'customer', 1),
(90, 'nirmalsavinda@wl.com', 'WO7FlAUqTCStwtWhnJFDJA==:1222db3aea87b54057ed162077bda128322a80f08f6b37ac11513744bcbacde2', '2023-11-29 12:58:51', 'customer', 1),
(91, 'abc@asdd.com', 'gcuY2KC+oV5GmL8bApCMGg==:bb1b41ce486c51d1fe2e6e5505a315d51985d53ec4c1bca9e401242145838193', '2023-11-30 01:27:39', 'rentalservice', 1),
(92, 'abc@asdd.com', 'cz9sEyZfBmog93vdaF0BUw==:51e094d7f334a005f118265bb04c6c94c87e0f1612056590e209d9c6daf6c1e7', '2023-11-30 01:28:27', 'rentalservice', 1),
(93, 'abc@asdd.com', '2avHcQqNntACF7QP/tgg+Q==:3d8957a38c3eec424eb6c0657ec26ec36ad77a14bb01cc9d9a53c4d71c7ad253', '2023-11-30 01:28:36', 'rentalservice', 1),
(94, 'abc@asdd.com', 'KRy9PBDlqmkVuxuRXGQL7Q==:e59257e50f2903d3df41a4e61da7c123322e0bf268924da23f1320c168b86bb5', '2023-11-30 01:29:54', 'rentalservice', 1),
(95, 'abc@asdd.com', 'k5gdhkpnOjdz5gg71CoHlg==:0ea64fa2cdbd3a7f890d1621cbe12b569f66010e0df661b9043f422e533f0a83', '2023-11-30 01:31:49', 'rentalservice', 1),
(96, 'abc@asdd.com', 'ZF5puas0977OW5SsSWcoEg==:77c73de480c15a6ec11872d65523401b664fa25fa87aa23083e26ed1d14ec0b7', '2023-11-30 01:36:34', 'rentalservice', 1),
(97, 'abc@asdd.com', '/5GikHNX2m6tKhWxOcbasw==:99b82dc66a603df41ff6019464bd4a813571e525308f0f7f31cda793af2a3446', '2023-11-30 01:36:45', 'rentalservice', 1),
(98, 'rentnow@gmail.com', 'qEdv1iuthAE7v12pU01uDA==:9a4124cd61d4d48d4c5fd346659c42d2575945c82616a9b28b40a17df568583d', '2023-11-30 01:51:07', 'rentalservice', 1),
(99, 'abc@asdd.com', '5b/H9SJg0n95Mu56BEDcIw==:60a6f8c149af9901aab58305624f1802bcbf49491ea51628032e743b4ab79d58', '2023-11-30 04:59:39', 'rentalservice', 1),
(100, 'abc@asdd.com', 'V+EdNPcdpHZD5n/86gOBag==:4a20de0eb03d236d2626be8248706a5d0e3a2653f23bae7a1e876e4dcaca9cac', '2023-11-30 05:00:32', 'rentalservice', 1),
(101, 'abc@asdd.com', 'baf8M9SyozBBZDaXvn2v/g==:d8c430cce9ad3c5a3094f994a4547e61f12a580e14d6e4638b58fc7f2f5d008d', '2023-11-30 05:09:39', 'rentalservice', 1),
(102, 'abc@asdd.com', 'jbsMk1qsZH52Szhz7wEkAg==:2222c3a51b6d3bcfbf35dcc4209ccd9c8170e6de8413631a672e23db39b3e2b4', '2023-11-30 05:17:29', 'rentalservice', 1),
(103, 'abc@asdd.com', 'S1orsYqULjCXazfcVgYDQw==:fe7f7dcc61584317377f55cd514afc17c6a08fb902b0ae1b403f286ec917117f', '2023-11-30 05:17:49', 'rentalservice', 1),
(104, 'nirmalsavinda29@gmail.com', 'ixEfpWdo98ezS1iOkq1v9w==:c0c09409d52d85f45500cd1865d5700d6d09d2fcba00cea29d1f42d205fb01f7', '2023-11-30 05:18:52', 'rentalservice', 1),
(105, 'abc@asdd.com', 't35F5/QeS3BDgCcMjW5L9w==:94bc5b050d74eeb3b748e651c5a632274e7690dfcd8a0d1774cd9936d7e641a4', '2023-11-30 06:43:24', 'guide', 1),
(106, 'nirmal@gmail.com', 'ili9yGuy2Lo+ILnlHQ5ceA==:5cecf8a2906c380f3a6d535400ee532af502cecdf23601912a909418af8ca6d5', '2023-11-30 06:57:07', 'guide', 1),
(107, 'customer@wl.com', 'K1RS1FZAZoD1Klu1gH5Tog==:9acde6add7e13c47bfc358c33babcddf960c6ac0eebbe3d4d264a220caee5a6a', '2023-11-30 07:05:09', 'customer', 1),
(108, 'guide@gmail.com', 'NpD7tjZcC9nL0EyV5sUOOA==:82e1b11863abf61b68520e25d40d6e7e1f0719c16dbd0afa6bc2679525eaf77f', '2023-11-30 07:07:55', 'guide', 1),
(109, 'guide@gmail.com', 'zbWzZOyP0VruACo1MPTOkA==:30feb86bd3107dfefc9540328d093c0bb42f5f14457bd279d47d7fdf0705f20b', '2023-11-30 08:13:20', 'guide', 1),
(110, 'guide@wl.com', 'Qm7Es0OpMHsP/RvudPRQ7g==:25d10d1b8da93519a3dc798816a9e94829c8422556c051147cf9b67f8e8d19d3', '2023-11-30 08:21:57', 'guide', 1),
(111, 'guide@gmail.com', 'Pc2gaDIp4XFaB9cH7iXE/Q==:16e5e60fa13363668877fce19eeeb2a5b2eda7ea7f9f8e68117fa54f9ee2f01e', '2023-11-30 08:22:37', 'guide', 1),
(112, 'guide1@gmail.com', 'MUVw4Q9MW8C6OUe8wKPc+g==:0665b411715252a4e2fc493a0176eda84375e83962c8bf4465ab84790b45de1c', '2023-11-30 08:22:58', 'guide', 1),
(113, 'guide1@gmail.com', 'Gu5XRGLANb0RwM9ta+G8KQ==:9af13d7d4f681be8188a306482a34911f0139ea06011889cb167921a80281994', '2023-11-30 08:29:46', 'guide', 1),
(114, 'abc@asdd.com', '0AuShxartQPONRxugJXfPw==:af126d5aa02f15889af9e47648d20221d7b61b97b67ab055202879e782700559', '2023-11-30 08:30:52', 'guide', 1),
(115, 'abc@asdd.com', 'EQpKeGviRks5oT3HH9GGgQ==:24a9c1907bb8672a1dcfb5cfd2de1601d973a524201b0109e493602961ec8b88', '2023-11-30 08:32:07', 'guide', 1),
(116, 'abc@asdd.com', 'cJGmqVyQRk0rTDw7EcmLzw==:a15d0e2ca56a890b55ade599fc4facaf6ec874029cda471f4a80ce59efd7412e', '2023-11-30 08:37:12', 'guide', 1),
(117, 'abc@asdd.com', 'QqzbZ3p0cGoc4Ev+bvKR7Q==:0c166d5a59afb74ee87b03d6ed22358ad93a956bb55978fad3640bd1941f3edd', '2023-11-30 08:37:34', 'guide', 1),
(118, 'abc@asdd.com', 'HgSPvcsCs+2F84vzgHMkbA==:728356031d290bb50782a3ed590208a36e8ebbd9f5dc64936dfc536ff9634d81', '2023-11-30 08:38:09', 'guide', 1),
(119, 'abc@asdd.com', 'FdTNOFqEuZPC+hUB1Yo/hg==:1995de0fb3d705d0afa1557323f77124982ae8fd68f5ced1442a839f62bfe70a', '2023-11-30 08:38:22', 'guide', 1),
(120, 'abc@asdd.com', 'v696KpqTRU9Dzp69PFN8pg==:8a254085f85ae44dee0518c16e771c4dcec7655a893b6ae8a29b7e74eee8b3d5', '2023-11-30 08:39:46', 'guide', 1),
(121, 'abc@asdd.com', '1JxFK9XqrMA9Qc/MAUwXkw==:e823afc5b772f8c29642c347a3462621374515c078fc8fb4577b83e32d5f1ee5', '2023-11-30 08:42:09', 'guide', 1),
(122, 'abc@asdd.com', 'Ly7Qba7HjAluQDI0E2Qpxg==:d5cbc2890f705dc3fdde798e998e71d28c49f7611ba598cb6f9beeb08ea5a291', '2023-11-30 08:42:52', 'guide', 1),
(123, 'abc@asdd.com', 'NGgiDhXhRjeu+qhYkFrCmg==:e51d75d0591c15d6262e589b52d4a5b643d828d1ddd9ab77c1b3903f07721cfa', '2023-11-30 08:43:27', 'guide', 1),
(124, 'abc@asdd.com', '88TcQIm2XZvPfhlC2LiF6Q==:71415f91099ccce20609c79d97c0ed3fd357a0025efbeff1e95658ae0c9d98ea', '2023-11-30 08:44:38', 'guide', 1),
(125, 'abc@asdd.com', 'YDMpacZq664HhXSxe+C0LQ==:baec3b6bcf764088829dd0a5b1c4916d7b8148251e84b63a9b5d9cadd1a615b4', '2023-11-30 08:44:57', 'guide', 1),
(126, 'abc@asdd.com', 'sWw1aMaV6m+LoLGeMKB+GQ==:46a8a57dedbf01f6d24612461d61172fc75c902a07bc979473271cf5e7a67590', '2023-11-30 08:45:27', 'guide', 1),
(127, 'abc@asdd.com', 'VqDJFXsDs7jJBQxuhs+K6w==:1d808844c8b523613ff773823314860e4d0e1a359b1e42cbd2799507b4a9b448', '2023-11-30 08:45:47', 'guide', 1),
(128, 'abc@asdd.com', 'DaMsntKGn0kyGk2rTSCnYA==:e5d7eaca12c06ad453e3f94ec44407e4529dae67de2f158ed2591ae3ce783f66', '2023-11-30 08:46:23', 'rentalservice', 1),
(129, 'abc@asdd.com', '9HHgJfM8pviBiYgpPopuPg==:99749c1f1e740f3c41486406c7faa45cd4f7a50d0a8a6336f979c17024e604fa', '2023-11-30 08:48:10', 'guide', 1),
(130, 'abc@asdd.com', 'yU/QeD2ldTXol4mGjx4hAQ==:535055b3426fb05d143e5a10ef730fec136c01287771a93541c112812809a0a9', '2023-11-30 08:49:10', 'guide', 1),
(131, 'abc@asdd.com', 'UjIEDbI62C3r9DmLwJbbIA==:5f703fe74a962f795a8e9c94bb8ed0d50b2246be88bf40a2eed38893104623db', '2023-11-30 08:49:45', 'guide', 1),
(132, 'abc@asdd.com', 'ndtFczwCCeakj9aNkLrD8g==:f9af08c1fd3d30edb3d70741279e08a1c2186aa624cdabb5d767d7958f72a742', '2023-11-30 08:49:53', 'rentalservice', 1),
(133, 'abc@asdd.com', '5pWqHYPtcVsTz1BXO3FckA==:ad80cd05e8c2033f4033a243d11911015bbf0db40b8ec3890a2b1ff3c3697a16', '2023-11-30 08:51:20', 'guide', 1),
(134, 'abc@asdd.com', 'R6PEv5MXupskejG6TjvGzg==:e334f014b154b74856edb3f000f8b33fcabb888300d7a75633f4288f1437c0b0', '2023-11-30 08:52:10', 'guide', 1),
(135, 'abc@asdd.com', 'utgn0WTzyg0mnh/tcOd3tg==:cfee14b6dca9516ed6817ddaf8533c47044eb0d4853237d413a600b53d9c4858', '2023-11-30 08:52:36', 'guide', 1),
(136, 'guide1@gmail.com', 'jxwM3wdn+1yLIDZtGi1wNA==:c0e6ab3192866dbd5070e1792014acf2f51757ac2c9efd09c4e572bce3dfe39c', '2023-11-30 08:53:19', 'guide', 1),
(137, 'abc@asdd.com', 'q5oRoheUt9tOKUSfB7fXtA==:0ccbd2639373d8caae4af36cfc212aff76c4d885dee36aa4f3efafa35400c195', '2023-11-30 09:18:31', 'guide', 1),
(138, 'abc@asdd.com', 'qKQ7KAvgGn0CoH6MSvToQQ==:b3aa021184a33830013709bc6c15a8b35e7ed6902c74a4172ad42d8f02f359f4', '2023-11-30 09:19:24', 'rentalservice', 1),
(139, 'nirmalsavinda@wl.com', 'm41NKb/IhjfCOJS2gcFzaA==:df1bb540351ed9bddcbef154f5b807501b3661381f15e2ce4adbeed6ea819027', '2023-12-04 13:10:09', 'customer', 0),
(140, 'nirmalsavinda@wl.com', 'iFelTJcT4g9hQvpvkc1YvA==:9fbe002eea109cffd5e8fbfc4da83a7d91019694b8a299d6d99870d22084f212', '2023-12-04 13:11:50', 'customer', 0),
(141, 'abc@asdd.com', 'dEq6Clx0AzgtrNFerw034A==:0145d1dce5daa9d1680ff380a6c50e652cf544b9d5c83706ed4f461303e3d9f6', '2023-12-04 13:12:14', 'guide', 0),
(142, 'nirmalsavinda@wl.com', 'lHGyjoIsaffgeytaP+XWJg==:44dbb970a57b55a6063924513bce954b9986fcc739faa82b27eb8cdb602eb9a8', '2023-12-04 13:12:59', 'customer', 0),
(143, 'abc@asdd.com', 'xk1IH7Z2q9aNupw7PC69jQ==:47aeecfd754e19c57d41bee3d8e34cf1d6164e6abca56e6872bdecc12538f77c', '2023-12-04 13:13:04', 'guide', 0),
(144, 'abc@asdd.com', 'iSdsD+nr1QAVWqtvhNGMNA==:8ac531602c3eff068d2f3a8b7fb9b148e83e9a09f1ac9596182587b14c9305b5', '2023-12-04 13:13:32', 'guide', 0),
(145, 'abc@asdd.com', 'thfkk/4y76YuFcHEfTNpsg==:863dfc992789cbd591179600e25ea5fc37569fa423dc5bd246bd890106860264', '2023-12-04 13:13:59', 'guide', 0),
(146, 'abc@asdd.com', 'jjUzfgx4cm//rgtvuc/qPQ==:47fca738e9436bca8eb2ffd73891ce085f8bde7927996b27aea9220fbdfe5b0f', '2023-12-04 13:15:10', 'guide', 0),
(147, 'abc@asdd.com', '+5tL+uZe9nFT4egIWon3IA==:b87a6caeeb536eaa8b3e181e49fdeaa9258243793c9f596fb24228d49b540e5a', '2023-12-04 13:18:56', 'guide', 0),
(148, 'abc@asdd.com', 've0lMkWhsn6YM8BroD6jQA==:0f95645313a0fdcccc76af25077b5706c2fec585c1503a0b2d2a8b4ec3ef30fa', '2023-12-04 13:19:08', 'guide', 0),
(149, 'abc@asdd.com', 'OMu7Pp2VXZ0yJ3sror3BsA==:59d21a524b02d0de7dee810e0134fd62432ae19cf3fc16c38b60147376f24cc1', '2023-12-04 13:19:25', 'guide', 0),
(150, 'abc@asdd.com', 'VyVj6seN6vkU9w0Q8uJTnA==:f010fda2ddae182ec3bfb4e9922f855b6abd8ee584af81c995b5feff614c2144', '2023-12-04 13:19:40', 'guide', 0),
(151, 'abc@asdd.com', 'WaX/mKeOnd6xyB4XMCBDbw==:738332645ec283a821a2e5a9b4d9b8f4e692e5dbc45b06c19498c2d5df0e5a69', '2023-12-04 13:20:01', 'guide', 0),
(152, 'abc@asdd.com', 'B4ZtqwwqIzRr3Dq7rLnPAQ==:745762afc1b04e5bdec9e936cc7111274fe966c3d7734c1947527c7facad984d', '2023-12-04 13:23:19', 'guide', 0),
(153, 'nirmalsavinda@wl.com', 'Eo04zQKXD26WYFVg0mzMHg==:f09c299e558bee260593c25536b18ced3c1baaa2b744370d9c047d75c07b6760', '2023-12-04 13:23:37', 'customer', 0),
(154, 'abc@asdd.com', '8r9rib3SvhptpqQFkad9GA==:6131956256e861bbd8778c6c38a92796170bb39c16c4b1ff2a1a3a89ca0484d5', '2023-12-04 13:23:58', 'guide', 0),
(155, 'nirmalsavinda@wl.com', '+LnbVRiFsNRpYk9am6kTfg==:c7c41ceae76089edc4bc9017cd026894829d4c7d0eb598aefd9cee5bcfa51a29', '2023-12-04 13:26:33', 'customer', 0),
(156, 'abc@asdd.com', 'q1iTwBs02WI29qLSc1+ByA==:677a49798c02e1d26b820970ec31ef566db02ee5befc9ffdb5e5ec56e67a71fa', '2023-12-04 13:30:42', 'guide', 0),
(157, 'abc@asdd.com', 'uD9S7o0olXh951073kOlvQ==:649c6ec36ae7e9f345d81dfc1ca4e094544df87cf570a7caf820d230d231ad85', '2023-12-04 13:31:25', 'guide', 0),
(158, 'abc@asdd.com', 'mbxIcVIXlqijn0FwUREIFg==:347624d5267aa7c4d59733e8cf3d443f20a74de30f3e6fdbee5259d6ebf74e02', '2023-12-04 13:32:03', 'guide', 0),
(159, 'abc@asdd.com', 'ND5QKh2ZvmjG8ZKRlpq6dg==:5ba2c23a3a342821659e63b12f9ac82cf30a6247156fa6e759a1945ee844af55', '2023-12-04 13:33:37', 'guide', 0),
(160, 'abc@asdd.com', 'tvPeZJFGPkqnq0NcuMRKlw==:45f9062f4be6632ab0aacd2bbb2fe475b1fffdda2f642e61d62ca70dd8ef6319', '2023-12-05 07:35:52', 'guide', 0),
(161, 'abc@asdd.com', '9NrWlaAp2q5vBs5jA/0kng==:eec0e511afff4b1938efbae131e5564fe28b5dd551ecdea582eec8e6f608ba24', '2023-12-05 08:22:12', 'guide', 0),
(162, 'abc@asdd.com', 'MgP0eJZexsep+CBeQ1R/Cg==:8ceea6eb3f37e20c3ea12baff7b055c8fb612451ec288edcbf303a5bcf5d9fcb', '2023-12-05 08:28:00', 'guide', 0),
(163, 'abc@asdd.com', 'TiNY0jTmgBrcH5qyHB8HLg==:3eb4da51bff964c6915335e9d172d8926488946c8bb809c35dcd6e39b4af7108', '2023-12-05 08:31:36', 'guide', 0),
(164, 'abc@asdd.com', 'G55aBd5eLL5MXT5gPjXZCg==:f5b80d2583aca17a6b2b8843756fb358cc049e88298804c5885fa4ed73f2d3d6', '2023-12-05 08:54:23', 'guide', 0),
(165, 'abc@asdd.com', 'X1Pn62lsL5TtQ4oN2id6AA==:03a0d4773544e1dc28771da3bfc24c630da6b134526d4e2517fcd263a061dde6', '2023-12-05 09:07:42', 'guide', 1),
(166, 'guide1@gmail.com', 'pERgYinRGuzxgLZU40r7gg==:e2e8aa4a2fcc230be65f212f400553e67a82e3eebb54cce66e09668fd8fd2f1e', '2023-12-07 12:44:26', 'guide', 0),
(167, 'customer@wl.com', 'x4xFTR2fX6PGBC1zi3absw==:5dd74d32e2abdb83ee770b30afa6ea74f00566c3f9af30028b898ae4636c2b05', '2023-12-11 10:13:22', 'customer', 0),
(168, 'rental@wl.com', 'RJz5Jof8yuIDu3TW20XJ8Q==:ffebf244573f3501f6566c9845bc4cfc3eae230761273c4652af26237abdc6e1', '2023-12-26 14:39:43', 'rentalservice', 0),
(169, 'nirmalsavinda29@gmail.com', 'zzMG6bgDCEi95zGl5kpDjA==:1c9ff43fcbff198a9d5519bad478b2b36470a12e3b8f875b3b0d7174cf614195', '2024-01-14 10:47:09', 'rentalservice', 0),
(170, 'nirmalsavinda29@gmail.com', '26dJwEZb3vEdEUpjpfVIcw==:b1b7f55d34caddd4b712cd41b2f948d66ce42af4c622709ff647509d5316e5f8', '2024-01-30 05:49:01', 'rentalservice', 0),
(171, 'nirmalsavinda29@gmail.com', 'qAEpJAd4Jpo9I0YON9Wpow==:a0a7c4aaf2fe9e281bf1697e33a2974c05daba6423e070bb81efe0299d24e7e8', '2024-01-30 05:49:18', 'rentalservice', 0),
(172, 'nirmalsavinda29@gmail.com', 'yKNq0LwIR7QNy8KFMGXPzw==:51bf30d6c5e0ebaa208a232a0cc7384ac55ca1802cb5b6f338c2ef65881153c5', '2024-01-30 05:50:15', 'rentalservice', 0),
(173, 'nirmalsavinda29@gmail.com', '2OLlyLGPzdVbniDDmAcCEw==:b1a332cbcaca612c4a1528c343945a37e7ecd3b54da53be60d35b0eeaae35d92', '2024-01-30 07:46:11', 'rentalservice', 0),
(174, 'nirmalsavinda29@gmail.com', 'aMtWZnPzUoktJNTQEK3uAA==:a7c8b68f08fd8d0b32db29b35b7b630726cfbad1e7314abfe6fac1cb8fb16102', '2024-01-30 07:47:41', 'rentalservice', 0),
(175, 'abc@asdd.com', 'jTWLETH3UmnfEsNGPu/F9Q==:47e64250c1f3048ad98deebc6c0d574c732565da9310a1d49b3072a4bc87bae9', '2024-01-30 07:50:47', 'rentalservice', 0),
(176, 'abc@asdd.com', 'g7ZkI3yjf+x7wb9zL/Dkrg==:c7190cf832be694c6ab84e21f3f9100b0dc8b60a1253b1e5e13e28e35584011e', '2024-01-30 07:52:08', 'rentalservice', 0),
(177, 'abc@asdd.com', 'KkoHpVltSrOHLztEzymWGQ==:e4fb6ccd5e29f2233868d7f8d15ef83fa554e1d60516dc63cbfc44190871b1d5', '2024-01-30 07:53:12', 'rentalservice', 0),
(178, 'abc@asdd.com', 'p6vvtpDy+duOUrFTieZLRQ==:90e0d5f869d4959cf8b1119c7f2edad10a8a4763071df3a72be7875837208e9b', '2024-01-30 07:54:21', 'rentalservice', 0),
(179, 'abc@asdd.com', 'VSRLTYOEX85FsXA4tkP/rA==:fe39c7d406873f029ce251f74252a35a66f7812cd333e1edffa10fb4942ac5f5', '2024-01-30 07:55:22', 'rentalservice', 0),
(180, 'abc@asdd.com', 'Xqpp06kMTa4vAfUubzukOg==:2664d8a289491dcc04de9dccd643bf93ee0f79f130ef4abdf904d3f91a4c0266', '2024-01-30 07:56:24', 'rentalservice', 0),
(181, 'nirmalsavinda29@gmail.com', 'keRiLseeT1IY1cCereA2Aw==:4952f318070ef202e0d7e9ab1791da426fce63638a0f8b573c231be1fdb6e3ec', '2024-01-30 07:59:56', 'rentalservice', 0),
(182, 'rental1@wl.com', 'AKaIN2jKbPXe112x7OPTug==:ff7c4a9e2f20b620b50b98039d5ad9d8b28412ee4d3e55ec1329585c258456dd', '2024-02-23 14:36:17', 'rentalservice', 1);

-- --------------------------------------------------------

--
-- Table structure for table `verification`
--

CREATE TABLE `verification` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `verification`
--

INSERT INTO `verification` (`id`, `user_id`, `token`) VALUES
(1, 145, '3497b51d91fa0d6e77c484c7c45123d7869a74efc93ab65edce935d21edb6e8c'),
(2, 146, '61316b40745df5786873f4b0922b608e9a7d5bd00b2f7d1ab1ba3b6eeae15422'),
(3, 147, '917453148aa76c114010b85452505f4c066fd4a8697fadf336a209996685dac6'),
(4, 148, '6e88bcdea933dc413ab6e40361354e4313a72682930033ad92260604ad739efe'),
(5, 149, 'c694a8063d49554c069b9c55fba92119f83b4de8ec706bdcd68f15654f85f081'),
(6, 151, '587fd4304718d888543f7e030e5f392b63c1f827fc6d081a810266c405ce1187'),
(7, 152, 'd546f6801b3923f7b7e15aeac7f3f3822ce706d86d34591fb380acfa64e05b27'),
(8, 153, '9bcfbed773f2e031edf569d85f96f6c79ddefc379ba37cfad2d9df5ad5bd0619'),
(10, 155, 'bc61a9f93eceba5c69df61e177a89b69c144eb4b765cc5e00c9781202b56d744'),
(11, 156, 'fae797b5b593427c95fadd8a48fdcb44975611c0f97f3cc71dc54ea8ae787f32'),
(12, 157, '2557ecdbaff820916f6914176e1435d1da581792d93987f2251a9192ae7de597'),
(13, 158, '9f9abcaabf52b5f957dc872cbd56b8f30d87ba0514465837b759635240614a99'),
(14, 159, '812b8bfc68e79270a5b5f6bd00658209939f4baed29ebf5ba8a41a714f9f5668'),
(21, 166, '539ed68f05d8dc53e45dc2b6d75f0e4177ed06e07fcc7037765900cc831e6277'),
(22, 167, 'a2e31b324f89ebb6d301e9184a54ab3e715303721328e4b7da000532c4ee72cf'),
(23, 168, '867a77127f00ba4d60b02b92323c66c7d2071145d46c344d858a5053c2bfc808'),
(24, 169, '8ea5ecef686595578c37a5c06bc296be38b60ee65f1157e503e59b1ecd4adb0f'),
(25, 170, '434de12da0921c4093c43bfcff894235c499b1527b23949e7c956f888cf19c92'),
(26, 171, '4017a52d3963467f613689fc1517db1d6358976f741636276efff7ee86e9c7f7'),
(27, 172, 'e84b859d8fa654d0d5cac46b4d3ee2ee7d068d9783d84e911e37cd5edd947fcb'),
(28, 173, '9972b6a3c801207287e2180bea1c39e24b3ff1858d75b891c13cd19f9d895105'),
(29, 174, 'fb69d7fe658d2ef1bb643a44544831107dbe12097769a78fced32432c2d94e7d'),
(30, 175, 'd95a34bb078967d011443cd1123f852705e8a1fd1cff8f4bd9adfc31b36fb4f1'),
(31, 176, '70151ca2ffcc32f245896067f53fbe2e5bf03429fc64f5a705d53097be366ed3'),
(32, 177, 'a4db3f8ff94b60f23f46bf59893796d70e3a122ca2817905461573e82ad98a12'),
(33, 178, 'd4b507ad4a0b55bbb58e91c0495da9cf6acf63363cf9db80219249f7670cb6d6'),
(34, 179, '1a24dc19f995a894a9edc9941d33bbddd76abd9396e38071efe7923709f7fd10'),
(35, 180, '1991dbfb82d03f54acf8aecddb77e5629e4b460554678cde9a995c1b725d8975'),
(36, 181, 'c6748fabe7b54060ccca007db651292b38209cc6f2474adb52b8539e54b5209a'),
(37, 182, '9c3853ac31bab21669937bc823a193be9fdd91a54657d4654d55a7451a5cc1e8');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rentalservice_id` (`rentalservice_id`);

--
-- Indexes for table `guides`
--
ALTER TABLE `guides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rent`
--
ALTER TABLE `rent`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `rental_services`
--
ALTER TABLE `rental_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_rental_services_location` (`location_id`);

--
-- Indexes for table `rent_item`
--
ALTER TABLE `rent_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rent_id` (`rent_id`);

--
-- Indexes for table `rent_pay`
--
ALTER TABLE `rent_pay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rent_id` (`rent_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `rent_request`
--
ALTER TABLE `rent_request`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `verification`
--
ALTER TABLE `verification`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2363;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `rent`
--
ALTER TABLE `rent`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `rental_services`
--
ALTER TABLE `rental_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `rent_item`
--
ALTER TABLE `rent_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `rent_pay`
--
ALTER TABLE `rent_pay`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `rent_request`
--
ALTER TABLE `rent_request`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tips`
--
ALTER TABLE `tips`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `verification`
--
ALTER TABLE `verification`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_item_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`),
  ADD CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`rentalservice_id`) REFERENCES `rental_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `guides`
--
ALTER TABLE `guides`
  ADD CONSTRAINT `guides_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rent`
--
ALTER TABLE `rent`
  ADD CONSTRAINT `rent_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `rental_services`
--
ALTER TABLE `rental_services`
  ADD CONSTRAINT `fk_rental_services_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `rental_services_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rent_item`
--
ALTER TABLE `rent_item`
  ADD CONSTRAINT `rent_item_ibfk_1` FOREIGN KEY (`rent_id`) REFERENCES `rent` (`id`);

--
-- Constraints for table `rent_pay`
--
ALTER TABLE `rent_pay`
  ADD CONSTRAINT `rent_pay_ibfk_1` FOREIGN KEY (`rent_id`) REFERENCES `rent` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
