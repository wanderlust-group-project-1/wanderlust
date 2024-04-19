-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Apr 18, 2024 at 03:24 AM
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

CREATE DEFINER=`root`@`%` PROCEDURE `GetAllMonthlyCompletedRentalCount` ()   BEGIN
    SELECT 
        MONTH(end_date) AS `Month`, 
        COUNT(*) AS `Count`
    FROM 
        `rent`
    WHERE 
       status = 'completed'
    GROUP BY 
        MONTH(end_date)
    ORDER BY 
        MONTH(end_date);
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetAllMonthlyRentedItemCount` ()   BEGIN
    SELECT 
        MONTH(r.end_date) AS `Month`, 
        COUNT(ri.item_id) AS `ItemCount`
    FROM 
        `rent_item` ri
    JOIN 
        `rent` r ON ri.rent_id = r.id
    WHERE 
        r.status IN ('rented', 'completed') -- Assuming you want to count items that were rented and those that completed the rental term
    GROUP BY 
        MONTH(r.end_date)
    ORDER BY 
        MONTH(r.end_date);
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
                      JOIN rent_request ON rent.id = rent_request.rent_id
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

    SET @SQL = CONCAT("SELECT rent.*, payment.status AS payment_status, rent_request.customer_req AS customer_req, rent_request.rentalservice_req AS rentalservice_req ", @baseQuery, @specificFilter);
    PREPARE stmt FROM @SQL;
    SET @rentalserviceID = rentalserviceID;
    EXECUTE stmt USING @rentalserviceID;
    DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetItemListbyRentID` (IN `rent_id` INT)   BEGIN
    SELECT 
        e.id AS `equipment_id`, 
        e.name AS `equipment_name`,
        i.item_number AS `item_number`,
        e.cost AS `equipment_cost`
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

CREATE DEFINER=`root`@`%` PROCEDURE `GetMonthlyCompletedRentalCount` (IN `service_id` INT)   BEGIN
    SELECT 
        MONTH(end_date) AS `Month`, 
        COUNT(*) AS `Count`
    FROM 
        `rent`
    WHERE 
       status = 'completed'
        AND rentalservice_id = service_id
    GROUP BY 
        MONTH(end_date)
    ORDER BY 
        MONTH(end_date);
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetMonthlyRentedItemCount` (IN `service_id` INT)   BEGIN
    SELECT 
        MONTH(r.end_date) AS `Month`, 
        COUNT(ri.item_id) AS `ItemCount`
    FROM 
        `rent_item` ri
    JOIN 
        `rent` r ON ri.rent_id = r.id
    WHERE 
        r.rentalservice_id = service_id
        AND r.status IN ('rented', 'completed') -- Assuming you want to count items that were rented and those that completed the rental term
    GROUP BY 
        MONTH(r.end_date)
    ORDER BY 
        MONTH(r.end_date);
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetRentalDetailsByID` (IN `rent_id_param` INT)   BEGIN
    SELECT 
        r.*,
        c.name AS `customer_name`, 
        u.email AS `customer_email`,
        c.number AS `customer_number`,
        rs.name AS `rental_service_name`,
        rs.id AS `rental_service_id`,
        -- Aggregate equipment names and their counts into a single column
        GROUP_CONCAT(DISTINCT CONCAT(sub.equipment_name, ' (', sub.equipment_count, ')') ORDER BY sub.equipment_name SEPARATOR ', ') AS `equipment_list`
    FROM 
        rent r
    INNER JOIN customers c ON r.customer_id = c.id
    INNER JOIN users u ON c.user_id = u.id
    INNER JOIN rental_services rs ON r.rentalservice_id = rs.id
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

CREATE DEFINER=`root`@`%` PROCEDURE `getRentalsByCustomer` (IN `customer_id_param` INT, IN `filterType` VARCHAR(255))   BEGIN


    DECLARE today DATE;
    SET today = CURDATE();
    
     SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));


    SET @baseQuery = "SELECT 
                        r.id, 
                        r.start_date AS `start`, 
                        r.end_date AS `end`, 
                        p.status AS `payment_status`,
                        r.status AS `rent_status`,
                        GROUP_CONCAT(e.name SEPARATOR ', ') AS `equipment_names`
                       
                    FROM 
                        rent r
                    INNER JOIN rent_item ri ON r.id = ri.rent_id
                    INNER JOIN item i ON ri.item_id = i.id
                    INNER JOIN equipment e ON i.equipment_id = e.id
                    INNER JOIN rent_pay rp ON r.id = rp.rent_id
                    INNER JOIN payment p ON rp.payment_id = p.id
                    WHERE 
                        r.customer_id = ?
                  ";

    SET @baseQueryEnd = "GROUP BY 
                        r.id
                    ORDER BY 
                        r.start_date;";
    
    CASE
        WHEN filterType = 'ALL' THEN
            SET @specificFilter = "AND p.status = 'completed'";
        
        WHEN filterType = 'pending' THEN
            SET @specificFilter = "AND r.status = 'pending' AND p.status = 'completed'";
        
        WHEN filterType = 'upcoming' THEN
            SET @specificFilter = "AND r.start_date >=  CURDATE() AND r.status = 'accepted' AND p.status = 'completed'";

        WHEN filterType = 'cancelled' THEN
            SET @specificFilter = "AND r.status = 'cancelled'";

        WHEN filterType = 'completed' THEN
            SET @specificFilter = "AND r.status = 'completed'";

        WHEN filterType = 'unpaid' THEN
            SET @specificFilter = "AND p.status = 'pending'";

        WHEN filterType = 'rented' THEN
            SET @specificFilter = "AND r.status = 'rented'";

        
        ELSE

            SET @specificFilter = "";
    END CASE;

    SET @SQL = CONCAT(@baseQuery, @specificFilter, @baseQueryEnd);
    PREPARE stmt FROM @SQL;
    SET @customer_id_param = customer_id_param;
    EXECUTE stmt USING @customer_id_param;
    DEALLOCATE PREPARE stmt;
    

    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetRentalStats` (IN `service_id` INT)   BEGIN
    -- Declare variables to hold the results
    DECLARE v_successful_rental_count INT;
    DECLARE v_total_earnings DECIMAL(10,2);
    DECLARE v_last_month_rental_count INT;
    DECLARE v_current_month_earnings DECIMAL(10,2);
    DECLARE v_equipment_count INT;
    
    -- Calculate Successful Rental Count
    SELECT COUNT(*) INTO v_successful_rental_count
    FROM rent
    WHERE status = 'completed' AND rentalservice_id = service_id;

    -- Calculate Total Earnings
    SELECT SUM(total) INTO v_total_earnings
    FROM rent
    WHERE status = 'completed' AND rentalservice_id = service_id;

    -- Calculate Last Month Rental Count
    SELECT COUNT(*) INTO v_last_month_rental_count
    FROM rent
    WHERE start_date >= DATE_FORMAT(CURRENT_DATE - INTERVAL 1 MONTH, '%Y-%m-01')
    AND start_date < DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')
    AND rentalservice_id = service_id;

    -- Calculate Current Month Earnings
    SELECT SUM(total) INTO v_current_month_earnings
    FROM rent
    WHERE status = 'completed'
    AND start_date >= DATE_FORMAT(CURRENT_DATE - INTERVAL 1 MONTH, '%Y-%m-01')
    AND start_date < DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')
    AND rentalservice_id = service_id;

    -- Calculate Equipment Count
    SELECT COUNT(*) INTO v_equipment_count
    FROM equipment
    WHERE rentalservice_id = service_id;

    -- Return all calculated data as a single row
    SELECT 
        v_successful_rental_count AS successful_rental_count,
        v_total_earnings AS total_earnings,
        v_last_month_rental_count AS last_month_rental_count,
        v_current_month_earnings AS current_month_earnings,
        v_equipment_count AS equipment_count;
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
(35, 'Nirmal', 'No 255, Neluwa RD\nGorakaduwa', '+94716024489', '200117829352', 167),
(36, 'Anderson Runte', '52556 Amara Mill', '+94716024489', '200176539077', 183);

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
(25, 25, 'Tent - 2 Persons', 3040.00, 'Tent for 2 Persons', 'Tent', 60, 1000.00, 0.00, '65b365fccf6dc.jpg'),
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
(49, 25, 'ABC AV', 606.00, 'Excepturi voluptates tenetur sit incidunt.', 'Tent', 0, 408.00, 383.00, '65e0650426b51.jpg'),
(53, 56, 'BBQ Grill', 5600.00, 'Large            ', 'Tent', 48, 300.00, 500.00, '65d8ae9491e5c.webp'),
(61, 56, 'Cooking Set', 11000.00, '5', 'Cooking', 11, 500.00, 400.00, '65d8b04792064.webp'),
(69, 25, 'Clare Ritchie', 74.00, 'Illum dolorem quas.', 'Footwear', 481, 6.00, 225.00, '65e0417c00298.jpg'),
(70, 25, 'Carlie Shields', 243.00, 'Beatae voluptatem maiores minus vel mollitia repellat quibusdam sint.', 'Cooking', 530, 606.00, 34.00, '65e041b26e300.png'),
(71, 25, 'Juana Barrows', 294.00, 'Ipsum pariatur dolores aliquam aspernatur doloremque sequi.', 'Cooking', 37, 200.00, 517.00, '65e0665e2b2bf.jpg'),
(72, 25, 'Dena Hirthe', 373.00, 'Nesciunt aspernatur aliquam.', 'Climbing', 40, 379.00, 96.00, ''),
(73, 25, 'Dane Schuster', 31.00, 'Sequi tempora consequatur explicabo maiores magni numquam adipisci.', 'Climbing', 366, 503.00, 655.00, ''),
(74, 25, 'Evangeline Vandervort', 357.00, 'Dolor eveniet ratione dolore fugiat.', 'Climbing', 6, 315.00, 249.00, '661cf80214b6c.png');

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
  `verification_document` text NOT NULL,
  `location_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guides`
--

INSERT INTO `guides` (`id`, `name`, `address`, `nic`, `mobile`, `gender`, `user_id`, `status`, `verification_document`, `location_id`) VALUES
(1, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '2334543', '076024489', 'male', NULL, 'waiting', '', 0),
(2, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '2334543', '076024489', 'male', 32, 'waiting', '', 0),
(3, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '435453636t345', '076024489', 'male', 33, 'waiting', '', 0),
(4, 'Sandali Gunawardhana', 'Colombo', '200117901832', '0716024489', 'female', 51, 'waiting', '', 0),
(5, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 105, 'waiting', '', 0),
(6, 'Nirmal Savinda', ' Matugama', '200117901838', '+94716024489', 'male', 106, 'waiting', '', 0),
(7, 'Nirmal Savinda', ' Colombo', '200167329831', '+94716024489', 'male', 108, 'waiting', '', 0),
(8, 'Nirmal Savinda', '  Colombo', '200167329831', '+94716024489', 'male', 109, 'waiting', '', 0),
(9, 'Nirmal Savinda', '  Colombo', '200167329832', '+94716024489', 'male', 110, 'waiting', '', 0),
(10, 'Nirmal Savinda', '  Colombo', '200167329831', '+94716024489', 'male', 111, 'waiting', '', 0),
(11, 'Nirmal Savinda', '  Colombo', '200167329831', '+94716024489', 'male', 112, 'waiting', '', 0),
(12, 'Nirmal Savinda', '  Colombo', '200167329831', '+94716024489', 'male', 113, 'waiting', '', 0),
(13, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 114, 'waiting', '', 0),
(14, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 115, 'waiting', '', 0),
(15, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 116, 'waiting', '', 0),
(16, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 117, 'waiting', '', 0),
(17, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 118, 'waiting', '', 0),
(18, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 119, 'waiting', '', 0),
(19, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 120, 'waiting', '', 0),
(20, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 121, 'waiting', '', 0),
(21, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 122, 'waiting', '', 0),
(22, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 123, 'waiting', '', 0),
(23, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 124, 'waiting', '', 0),
(24, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 125, 'waiting', '', 0),
(25, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 126, 'waiting', '', 0),
(26, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 127, 'waiting', '', 0),
(27, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 129, 'waiting', '', 0),
(28, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 130, 'waiting', '', 0),
(29, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 131, 'waiting', '', 0),
(30, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 133, 'waiting', '65684d08461a2.pdf', 0),
(31, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 134, 'waiting', '65684d3aaea5f.pdf', 0),
(32, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 135, 'waiting', '65684d544415b.pdf', 0),
(33, 'Nirmal Savinda', '  Colombo', '200167329831', '+94716024489', 'male', 136, 'waiting', '65684d7f53def.pdf', 0),
(34, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 137, 'waiting', '65685367464ac.pdf', 0),
(35, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 154, 'waiting', '656dd2f4b51a2.pdf', 0),
(36, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 156, 'waiting', '656dd482d5148.pdf', 0),
(37, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 157, 'waiting', '656dd4ad4d5cd.pdf', 0),
(38, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 158, 'waiting', '656dd4d3e4042.pdf', 0),
(39, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 159, 'waiting', '656dd5371c806.pdf', 0),
(40, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 160, 'waiting', '656ed2dd8fadd.pdf', 0),
(41, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 161, 'waiting', '656eddbc6e48d.pdf', 0),
(42, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 162, 'waiting', '656edf173246c.pdf', 0),
(43, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 163, 'waiting', '656edff2b5eda.pdf', 0),
(44, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 164, 'waiting', '656ee545b12fc.pdf', 0),
(45, 'nirmal', 'Address is required', '200156273849', '0713458323', 'male', 165, 'waiting', '656ee864db5fe.pdf', 0),
(46, 'Nirmal Savinda', '  Colombo', '200167329831', '+94716024489', 'male', 166, 'waiting', '6571be307d2f4.pdf', 0),
(47, 'HDUFIFISF', 'No 255, Neluwa RD', '098790987654', '+94716024489', 'male', 187, 'waiting', '', 6);

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
(1331, 25, 'I000254803', 'removed'),
(1332, 25, 'I000252679', 'available'),
(1333, 25, 'I000254617', 'removed'),
(1334, 25, 'I000254975', 'available'),
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
(2362, 53, 'I000533386', 'available'),
(2363, 25, 'I000254127', 'available'),
(2364, 25, 'I000251213', 'available'),
(2365, 25, 'I000251684', 'available'),
(2366, 25, 'I000253782', 'available'),
(2367, 25, 'I000255308', 'available'),
(2368, 25, 'I000255387', 'available'),
(2369, 25, 'I000251010', 'available'),
(2370, 25, 'I000255893', 'available'),
(2371, 25, 'I000259264', 'available'),
(2372, 25, 'I000251527', 'available'),
(2373, 25, 'I000257485', 'available'),
(2374, 25, 'I000255991', 'available'),
(2375, 25, 'I000255328', 'available'),
(2376, 25, 'I000258003', 'available'),
(2377, 25, 'I000255033', 'available'),
(2378, 25, 'I000259158', 'available'),
(2379, 25, 'I000256687', 'available'),
(2380, 25, 'I000256731', 'available'),
(2381, 25, 'I000253595', 'available'),
(2382, 25, 'I000255783', 'available'),
(2383, 25, 'I000256134', 'available'),
(2384, 25, 'I000253262', 'available'),
(2385, 25, 'I000252370', 'available'),
(2386, 25, 'I000255696', 'available'),
(2387, 25, 'I000251351', 'available'),
(2388, 25, 'I000254600', 'available'),
(2389, 25, 'I000257730', 'available'),
(2390, 25, 'I000254416', 'available'),
(2391, 25, 'I000259096', 'available'),
(2392, 25, 'I000251520', 'available'),
(2393, 25, 'I000253024', 'available'),
(2394, 25, 'I000252414', 'available'),
(2395, 25, 'I000258449', 'available'),
(2396, 25, 'I000255517', 'available'),
(2397, 25, 'I000256277', 'available'),
(2398, 25, 'I000257239', 'available'),
(2399, 25, 'I000252274', 'available'),
(2400, 25, 'I000254029', 'available'),
(2401, 25, 'I000258539', 'removed'),
(2402, 25, 'I000255130', 'available'),
(2403, 69, 'I000699704', 'available'),
(2404, 69, 'I000694389', 'available'),
(2405, 69, 'I000697225', 'available'),
(2406, 69, 'I000699952', 'available'),
(2407, 69, 'I000699238', 'available'),
(2408, 69, 'I000697129', 'available'),
(2409, 69, 'I000699481', 'available'),
(2410, 69, 'I000691144', 'available'),
(2411, 69, 'I000691578', 'available'),
(2412, 69, 'I000693869', 'available'),
(2413, 69, 'I000697473', 'available'),
(2414, 69, 'I000691332', 'available'),
(2415, 69, 'I000695235', 'available'),
(2416, 69, 'I000698646', 'available'),
(2417, 69, 'I000692329', 'available'),
(2418, 69, 'I000694618', 'available'),
(2419, 69, 'I000695949', 'available'),
(2420, 69, 'I000691648', 'available'),
(2421, 69, 'I000696940', 'available'),
(2422, 69, 'I000692195', 'available'),
(2423, 69, 'I000698421', 'available'),
(2424, 69, 'I000697420', 'available'),
(2425, 69, 'I000694355', 'available'),
(2426, 69, 'I000699139', 'available'),
(2427, 69, 'I000691617', 'available'),
(2428, 69, 'I000694654', 'available'),
(2429, 69, 'I000694036', 'available'),
(2430, 69, 'I000699969', 'available'),
(2431, 69, 'I000692267', 'available'),
(2432, 69, 'I000699525', 'available'),
(2433, 69, 'I000699749', 'available'),
(2434, 69, 'I000692684', 'available'),
(2435, 69, 'I000691417', 'available'),
(2436, 69, 'I000694934', 'available'),
(2437, 69, 'I000692174', 'available'),
(2438, 69, 'I000698070', 'available'),
(2439, 69, 'I000699933', 'available'),
(2440, 69, 'I000691269', 'available'),
(2441, 69, 'I000698887', 'available'),
(2442, 69, 'I000696607', 'available'),
(2443, 69, 'I000692727', 'available'),
(2444, 69, 'I000693524', 'available'),
(2445, 69, 'I000697395', 'available'),
(2446, 69, 'I000696442', 'available'),
(2447, 69, 'I000695752', 'available'),
(2448, 69, 'I000691223', 'available'),
(2449, 69, 'I000692376', 'available'),
(2450, 69, 'I000692055', 'available'),
(2451, 69, 'I000693566', 'available'),
(2452, 69, 'I000698184', 'available'),
(2453, 69, 'I000691319', 'available'),
(2454, 69, 'I000691507', 'available'),
(2455, 69, 'I000698191', 'available'),
(2456, 69, 'I000699238', 'available'),
(2457, 69, 'I000694971', 'available'),
(2458, 69, 'I000694491', 'available'),
(2459, 69, 'I000695562', 'available'),
(2460, 69, 'I000697920', 'available'),
(2461, 69, 'I000695330', 'available'),
(2462, 69, 'I000696914', 'available'),
(2463, 69, 'I000697500', 'available'),
(2464, 69, 'I000697521', 'available'),
(2465, 69, 'I000699041', 'available'),
(2466, 69, 'I000699015', 'available'),
(2467, 69, 'I000693826', 'available'),
(2468, 69, 'I000694470', 'available'),
(2469, 69, 'I000699403', 'available'),
(2470, 69, 'I000697346', 'available'),
(2471, 69, 'I000694867', 'available'),
(2472, 69, 'I000693269', 'available'),
(2473, 69, 'I000694222', 'available'),
(2474, 69, 'I000699707', 'available'),
(2475, 69, 'I000696355', 'available'),
(2476, 69, 'I000691301', 'available'),
(2477, 69, 'I000697431', 'available'),
(2478, 69, 'I000697396', 'available'),
(2479, 69, 'I000695565', 'available'),
(2480, 69, 'I000697072', 'available'),
(2481, 69, 'I000697201', 'available'),
(2482, 69, 'I000695087', 'available'),
(2483, 69, 'I000698758', 'available'),
(2484, 69, 'I000697532', 'available'),
(2485, 69, 'I000695190', 'available'),
(2486, 69, 'I000697744', 'available'),
(2487, 69, 'I000695335', 'available'),
(2488, 69, 'I000698578', 'available'),
(2489, 69, 'I000691878', 'available'),
(2490, 69, 'I000697387', 'available'),
(2491, 69, 'I000691375', 'available'),
(2492, 69, 'I000698553', 'available'),
(2493, 69, 'I000691042', 'available'),
(2494, 69, 'I000699729', 'available'),
(2495, 69, 'I000698382', 'available'),
(2496, 69, 'I000693621', 'available'),
(2497, 69, 'I000696270', 'available'),
(2498, 69, 'I000699447', 'available'),
(2499, 69, 'I000699987', 'available'),
(2500, 69, 'I000699639', 'available'),
(2501, 69, 'I000697179', 'available'),
(2502, 69, 'I000698561', 'available'),
(2503, 69, 'I000692960', 'available'),
(2504, 69, 'I000691098', 'available'),
(2505, 69, 'I000697144', 'available'),
(2506, 69, 'I000693764', 'available'),
(2507, 69, 'I000692137', 'available'),
(2508, 69, 'I000691646', 'available'),
(2509, 69, 'I000696959', 'available'),
(2510, 69, 'I000694316', 'available'),
(2511, 69, 'I000695292', 'available'),
(2512, 69, 'I000692620', 'available'),
(2513, 69, 'I000696056', 'available'),
(2514, 69, 'I000695408', 'available'),
(2515, 69, 'I000697152', 'available'),
(2516, 69, 'I000691132', 'available'),
(2517, 69, 'I000697254', 'available'),
(2518, 69, 'I000692059', 'available'),
(2519, 69, 'I000693020', 'available'),
(2520, 69, 'I000695010', 'available'),
(2521, 69, 'I000697186', 'available'),
(2522, 69, 'I000693625', 'available'),
(2523, 69, 'I000696370', 'available'),
(2524, 69, 'I000699587', 'available'),
(2525, 69, 'I000699986', 'available'),
(2526, 69, 'I000696632', 'available'),
(2527, 69, 'I000691118', 'available'),
(2528, 69, 'I000693199', 'available'),
(2529, 69, 'I000699640', 'available'),
(2530, 69, 'I000695286', 'available'),
(2531, 69, 'I000698593', 'available'),
(2532, 69, 'I000692606', 'available'),
(2533, 69, 'I000693094', 'available'),
(2534, 69, 'I000693238', 'available'),
(2535, 69, 'I000691601', 'available'),
(2536, 69, 'I000695180', 'available'),
(2537, 69, 'I000698121', 'available'),
(2538, 69, 'I000699113', 'available'),
(2539, 69, 'I000693239', 'available'),
(2540, 69, 'I000698688', 'available'),
(2541, 69, 'I000693087', 'available'),
(2542, 69, 'I000699629', 'available'),
(2543, 69, 'I000694341', 'available'),
(2544, 69, 'I000696998', 'available'),
(2545, 69, 'I000696056', 'available'),
(2546, 69, 'I000699262', 'available'),
(2547, 69, 'I000696640', 'available'),
(2548, 69, 'I000698983', 'available'),
(2549, 69, 'I000695091', 'available'),
(2550, 69, 'I000699059', 'available'),
(2551, 69, 'I000692466', 'available'),
(2552, 69, 'I000693498', 'available'),
(2553, 69, 'I000691444', 'available'),
(2554, 69, 'I000697130', 'available'),
(2555, 69, 'I000697649', 'available'),
(2556, 69, 'I000692340', 'available'),
(2557, 69, 'I000694789', 'available'),
(2558, 69, 'I000699101', 'available'),
(2559, 69, 'I000695996', 'available'),
(2560, 69, 'I000697181', 'available'),
(2561, 69, 'I000693651', 'available'),
(2562, 69, 'I000696912', 'available'),
(2563, 69, 'I000691642', 'available'),
(2564, 69, 'I000697163', 'available'),
(2565, 69, 'I000696500', 'available'),
(2566, 69, 'I000697198', 'available'),
(2567, 69, 'I000698444', 'available'),
(2568, 69, 'I000697821', 'available'),
(2569, 69, 'I000692768', 'available'),
(2570, 69, 'I000692381', 'available'),
(2571, 69, 'I000694502', 'available'),
(2572, 69, 'I000693280', 'available'),
(2573, 69, 'I000695970', 'available'),
(2574, 69, 'I000695092', 'available'),
(2575, 69, 'I000699191', 'available'),
(2576, 69, 'I000694970', 'available'),
(2577, 69, 'I000694882', 'available'),
(2578, 69, 'I000695196', 'available'),
(2579, 69, 'I000692961', 'available'),
(2580, 69, 'I000692662', 'available'),
(2581, 69, 'I000698108', 'available'),
(2582, 69, 'I000696274', 'available'),
(2583, 69, 'I000693397', 'available'),
(2584, 69, 'I000691244', 'available'),
(2585, 69, 'I000694245', 'available'),
(2586, 69, 'I000698629', 'available'),
(2587, 69, 'I000691926', 'available'),
(2588, 69, 'I000696102', 'available'),
(2589, 69, 'I000691776', 'available'),
(2590, 69, 'I000697991', 'available'),
(2591, 69, 'I000693027', 'available'),
(2592, 69, 'I000695770', 'available'),
(2593, 69, 'I000693565', 'available'),
(2594, 69, 'I000698976', 'available'),
(2595, 69, 'I000695631', 'available'),
(2596, 69, 'I000691376', 'available'),
(2597, 69, 'I000694639', 'available'),
(2598, 69, 'I000695931', 'available'),
(2599, 69, 'I000691107', 'available'),
(2600, 69, 'I000695820', 'available'),
(2601, 69, 'I000693798', 'available'),
(2602, 69, 'I000693950', 'available'),
(2603, 69, 'I000697046', 'available'),
(2604, 69, 'I000691488', 'available'),
(2605, 69, 'I000693029', 'available'),
(2606, 69, 'I000691329', 'available'),
(2607, 69, 'I000699174', 'available'),
(2608, 69, 'I000692516', 'available'),
(2609, 69, 'I000693418', 'available'),
(2610, 69, 'I000698472', 'available'),
(2611, 69, 'I000696382', 'available'),
(2612, 69, 'I000692188', 'available'),
(2613, 69, 'I000694299', 'available'),
(2614, 69, 'I000691157', 'available'),
(2615, 69, 'I000693476', 'available'),
(2616, 69, 'I000693790', 'available'),
(2617, 69, 'I000696912', 'available'),
(2618, 69, 'I000699388', 'available'),
(2619, 69, 'I000691340', 'available'),
(2620, 69, 'I000696147', 'available'),
(2621, 69, 'I000697902', 'available'),
(2622, 69, 'I000696979', 'available'),
(2623, 69, 'I000699652', 'available'),
(2624, 69, 'I000695748', 'available'),
(2625, 69, 'I000696519', 'available'),
(2626, 69, 'I000692310', 'available'),
(2627, 69, 'I000695389', 'available'),
(2628, 69, 'I000692765', 'available'),
(2629, 69, 'I000696202', 'available'),
(2630, 69, 'I000695135', 'available'),
(2631, 69, 'I000697922', 'available'),
(2632, 69, 'I000697476', 'available'),
(2633, 69, 'I000699918', 'available'),
(2634, 69, 'I000696330', 'available'),
(2635, 69, 'I000696181', 'available'),
(2636, 69, 'I000698633', 'available'),
(2637, 69, 'I000692582', 'available'),
(2638, 69, 'I000699528', 'available'),
(2639, 69, 'I000696972', 'available'),
(2640, 69, 'I000693889', 'available'),
(2641, 69, 'I000694320', 'available'),
(2642, 69, 'I000694253', 'available'),
(2643, 69, 'I000698099', 'available'),
(2644, 69, 'I000699776', 'available'),
(2645, 69, 'I000697034', 'available'),
(2646, 69, 'I000692831', 'available'),
(2647, 69, 'I000691657', 'available'),
(2648, 69, 'I000698172', 'available'),
(2649, 69, 'I000697527', 'available'),
(2650, 69, 'I000698635', 'available'),
(2651, 69, 'I000698499', 'available'),
(2652, 69, 'I000694946', 'available'),
(2653, 69, 'I000691644', 'available'),
(2654, 69, 'I000696906', 'available'),
(2655, 69, 'I000691586', 'available'),
(2656, 69, 'I000696156', 'available'),
(2657, 69, 'I000695862', 'available'),
(2658, 69, 'I000694152', 'available'),
(2659, 69, 'I000691305', 'available'),
(2660, 69, 'I000697596', 'available'),
(2661, 69, 'I000698759', 'available'),
(2662, 69, 'I000691235', 'available'),
(2663, 69, 'I000699803', 'available'),
(2664, 69, 'I000696314', 'available'),
(2665, 69, 'I000696906', 'available'),
(2666, 69, 'I000694572', 'available'),
(2667, 69, 'I000698879', 'available'),
(2668, 69, 'I000693720', 'available'),
(2669, 69, 'I000691937', 'available'),
(2670, 69, 'I000695503', 'available'),
(2671, 69, 'I000698292', 'available'),
(2672, 69, 'I000692191', 'available'),
(2673, 69, 'I000695185', 'available'),
(2674, 69, 'I000698959', 'available'),
(2675, 69, 'I000698723', 'available'),
(2676, 69, 'I000696291', 'available'),
(2677, 69, 'I000697192', 'available'),
(2678, 69, 'I000691201', 'available'),
(2679, 69, 'I000695258', 'available'),
(2680, 69, 'I000691274', 'available'),
(2681, 69, 'I000696054', 'available'),
(2682, 69, 'I000696741', 'available'),
(2683, 69, 'I000693759', 'available'),
(2684, 69, 'I000693924', 'available'),
(2685, 69, 'I000696900', 'available'),
(2686, 69, 'I000697954', 'available'),
(2687, 69, 'I000691196', 'available'),
(2688, 69, 'I000697304', 'available'),
(2689, 69, 'I000694197', 'available'),
(2690, 69, 'I000697180', 'available'),
(2691, 69, 'I000691811', 'available'),
(2692, 69, 'I000697965', 'available'),
(2693, 69, 'I000692214', 'available'),
(2694, 69, 'I000698558', 'available'),
(2695, 69, 'I000699837', 'available'),
(2696, 69, 'I000694066', 'available'),
(2697, 69, 'I000698897', 'available'),
(2698, 69, 'I000698623', 'available'),
(2699, 69, 'I000694929', 'available'),
(2700, 69, 'I000699221', 'available'),
(2701, 69, 'I000697010', 'available'),
(2702, 69, 'I000697364', 'available'),
(2703, 69, 'I000697009', 'available'),
(2704, 69, 'I000696073', 'available'),
(2705, 69, 'I000699478', 'available'),
(2706, 69, 'I000691593', 'available'),
(2707, 69, 'I000691132', 'available'),
(2708, 69, 'I000699338', 'available'),
(2709, 69, 'I000697413', 'available'),
(2710, 69, 'I000694974', 'available'),
(2711, 69, 'I000692959', 'available'),
(2712, 69, 'I000694636', 'available'),
(2713, 69, 'I000699615', 'available'),
(2714, 69, 'I000698131', 'available'),
(2715, 69, 'I000697133', 'available'),
(2716, 69, 'I000691511', 'available'),
(2717, 69, 'I000692688', 'available'),
(2718, 69, 'I000693646', 'available'),
(2719, 69, 'I000692255', 'available'),
(2720, 69, 'I000693200', 'available'),
(2721, 69, 'I000696683', 'available'),
(2722, 69, 'I000691344', 'available'),
(2723, 69, 'I000693556', 'available'),
(2724, 69, 'I000693652', 'available'),
(2725, 69, 'I000693544', 'available'),
(2726, 69, 'I000699639', 'available'),
(2727, 69, 'I000699420', 'available'),
(2728, 69, 'I000694404', 'available'),
(2729, 69, 'I000691059', 'available'),
(2730, 69, 'I000699657', 'available'),
(2731, 69, 'I000695135', 'available'),
(2732, 69, 'I000693815', 'available'),
(2733, 69, 'I000691450', 'available'),
(2734, 69, 'I000694383', 'available'),
(2735, 69, 'I000699408', 'available'),
(2736, 69, 'I000696475', 'available'),
(2737, 69, 'I000694531', 'available'),
(2738, 69, 'I000692986', 'available'),
(2739, 69, 'I000699123', 'available'),
(2740, 69, 'I000694635', 'available'),
(2741, 69, 'I000698125', 'available'),
(2742, 69, 'I000696841', 'available'),
(2743, 69, 'I000695033', 'available'),
(2744, 69, 'I000699232', 'available'),
(2745, 69, 'I000696286', 'available'),
(2746, 69, 'I000692514', 'available'),
(2747, 69, 'I000692369', 'available'),
(2748, 69, 'I000696553', 'available'),
(2749, 69, 'I000696367', 'available'),
(2750, 69, 'I000691161', 'available'),
(2751, 69, 'I000692923', 'available'),
(2752, 69, 'I000692264', 'available'),
(2753, 69, 'I000699882', 'available'),
(2754, 69, 'I000697560', 'available'),
(2755, 69, 'I000692876', 'available'),
(2756, 69, 'I000696778', 'available'),
(2757, 69, 'I000696074', 'available'),
(2758, 69, 'I000692578', 'available'),
(2759, 69, 'I000695480', 'available'),
(2760, 69, 'I000692103', 'available'),
(2761, 69, 'I000691802', 'available'),
(2762, 69, 'I000698491', 'available'),
(2763, 69, 'I000693280', 'available'),
(2764, 69, 'I000696068', 'available'),
(2765, 69, 'I000696946', 'available'),
(2766, 69, 'I000697528', 'available'),
(2767, 69, 'I000691685', 'available'),
(2768, 69, 'I000692438', 'available'),
(2769, 69, 'I000696337', 'available'),
(2770, 69, 'I000691624', 'available'),
(2771, 69, 'I000693003', 'available'),
(2772, 69, 'I000698613', 'available'),
(2773, 69, 'I000693788', 'available'),
(2774, 69, 'I000695624', 'available'),
(2775, 69, 'I000692944', 'available'),
(2776, 69, 'I000697482', 'available'),
(2777, 69, 'I000699846', 'available'),
(2778, 69, 'I000694910', 'available'),
(2779, 69, 'I000696854', 'available'),
(2780, 69, 'I000697200', 'available'),
(2781, 69, 'I000695294', 'available'),
(2782, 69, 'I000691955', 'available'),
(2783, 69, 'I000699671', 'available'),
(2784, 69, 'I000696328', 'available'),
(2785, 69, 'I000698372', 'available'),
(2786, 69, 'I000695292', 'available'),
(2787, 69, 'I000699369', 'available'),
(2788, 69, 'I000696559', 'available'),
(2789, 69, 'I000696472', 'available'),
(2790, 69, 'I000693113', 'available'),
(2791, 69, 'I000693069', 'available'),
(2792, 69, 'I000695462', 'available'),
(2793, 69, 'I000696328', 'available'),
(2794, 69, 'I000691252', 'available'),
(2795, 69, 'I000695000', 'available'),
(2796, 69, 'I000694900', 'available'),
(2797, 69, 'I000695285', 'available'),
(2798, 69, 'I000694491', 'available'),
(2799, 69, 'I000693938', 'available'),
(2800, 69, 'I000699572', 'available'),
(2801, 69, 'I000694603', 'available'),
(2802, 69, 'I000693480', 'available'),
(2803, 69, 'I000698865', 'available'),
(2804, 69, 'I000694281', 'available'),
(2805, 69, 'I000693736', 'available'),
(2806, 69, 'I000697620', 'available'),
(2807, 69, 'I000695329', 'available'),
(2808, 69, 'I000697841', 'available'),
(2809, 69, 'I000694188', 'available'),
(2810, 69, 'I000692173', 'available'),
(2811, 69, 'I000695771', 'available'),
(2812, 69, 'I000692256', 'available'),
(2813, 69, 'I000692107', 'available'),
(2814, 69, 'I000693893', 'available'),
(2815, 69, 'I000693174', 'available'),
(2816, 69, 'I000699436', 'available'),
(2817, 69, 'I000698900', 'available'),
(2818, 69, 'I000695350', 'available'),
(2819, 69, 'I000695728', 'available'),
(2820, 69, 'I000698847', 'available'),
(2821, 69, 'I000691711', 'available'),
(2822, 69, 'I000699062', 'available'),
(2823, 69, 'I000695945', 'available'),
(2824, 69, 'I000696577', 'available'),
(2825, 69, 'I000691621', 'available'),
(2826, 69, 'I000699784', 'available'),
(2827, 69, 'I000692481', 'available'),
(2828, 69, 'I000696248', 'available'),
(2829, 69, 'I000698563', 'available'),
(2830, 69, 'I000699630', 'available'),
(2831, 69, 'I000699082', 'available'),
(2832, 69, 'I000697066', 'available'),
(2833, 69, 'I000699685', 'available'),
(2834, 69, 'I000695587', 'available'),
(2835, 69, 'I000693169', 'available'),
(2836, 69, 'I000693097', 'available'),
(2837, 69, 'I000695486', 'available'),
(2838, 69, 'I000696536', 'available'),
(2839, 69, 'I000694984', 'available'),
(2840, 69, 'I000692038', 'available'),
(2841, 69, 'I000695050', 'available'),
(2842, 69, 'I000697777', 'available'),
(2843, 69, 'I000695478', 'available'),
(2844, 69, 'I000699271', 'available'),
(2845, 69, 'I000693010', 'available'),
(2846, 69, 'I000692923', 'available'),
(2847, 69, 'I000693626', 'available'),
(2848, 69, 'I000698749', 'available'),
(2849, 69, 'I000699767', 'available'),
(2850, 69, 'I000699999', 'available'),
(2851, 69, 'I000695249', 'available'),
(2852, 69, 'I000692907', 'available'),
(2853, 69, 'I000691170', 'available'),
(2854, 69, 'I000697232', 'available'),
(2855, 69, 'I000693059', 'available'),
(2856, 69, 'I000694209', 'available'),
(2857, 69, 'I000699609', 'available'),
(2858, 69, 'I000692869', 'available'),
(2859, 69, 'I000696191', 'available'),
(2860, 69, 'I000693993', 'available'),
(2861, 69, 'I000694312', 'available'),
(2862, 69, 'I000693174', 'available'),
(2863, 69, 'I000695661', 'available'),
(2864, 69, 'I000699174', 'available'),
(2865, 69, 'I000698534', 'available'),
(2866, 69, 'I000699753', 'available'),
(2867, 69, 'I000692667', 'available'),
(2868, 69, 'I000691077', 'available'),
(2869, 69, 'I000697632', 'available'),
(2870, 69, 'I000692494', 'available'),
(2871, 69, 'I000697872', 'available'),
(2872, 69, 'I000693287', 'available'),
(2873, 69, 'I000692786', 'available'),
(2874, 69, 'I000697528', 'available'),
(2875, 69, 'I000693136', 'available'),
(2876, 69, 'I000692996', 'available'),
(2877, 69, 'I000693871', 'available'),
(2878, 69, 'I000693751', 'available'),
(2879, 69, 'I000692605', 'available'),
(2880, 69, 'I000698905', 'available'),
(2881, 69, 'I000693919', 'available'),
(2882, 69, 'I000695496', 'available'),
(2883, 69, 'I000698332', 'available'),
(2884, 70, 'I000708501', 'available'),
(2885, 70, 'I000709513', 'available'),
(2886, 70, 'I000709138', 'available'),
(2887, 70, 'I000701919', 'available'),
(2888, 70, 'I000703142', 'available'),
(2889, 70, 'I000701341', 'available'),
(2890, 70, 'I000701106', 'available'),
(2891, 70, 'I000703860', 'available'),
(2892, 70, 'I000704309', 'available'),
(2893, 70, 'I000706041', 'available'),
(2894, 70, 'I000707903', 'available'),
(2895, 70, 'I000709707', 'available'),
(2896, 70, 'I000709213', 'available'),
(2897, 70, 'I000701606', 'available'),
(2898, 70, 'I000705504', 'available'),
(2899, 70, 'I000704040', 'available'),
(2900, 70, 'I000704136', 'available'),
(2901, 70, 'I000701134', 'available'),
(2902, 70, 'I000706233', 'available'),
(2903, 70, 'I000704134', 'available'),
(2904, 70, 'I000708368', 'available'),
(2905, 70, 'I000708704', 'available'),
(2906, 70, 'I000703965', 'available'),
(2907, 70, 'I000709916', 'available'),
(2908, 70, 'I000703624', 'available'),
(2909, 70, 'I000701537', 'available'),
(2910, 70, 'I000709493', 'available'),
(2911, 70, 'I000708455', 'available'),
(2912, 70, 'I000707117', 'available'),
(2913, 70, 'I000709041', 'available'),
(2914, 70, 'I000703641', 'available'),
(2915, 70, 'I000709716', 'available'),
(2916, 70, 'I000702746', 'available'),
(2917, 70, 'I000704645', 'available'),
(2918, 70, 'I000704451', 'available'),
(2919, 70, 'I000701672', 'available'),
(2920, 70, 'I000704633', 'available'),
(2921, 70, 'I000704886', 'available'),
(2922, 70, 'I000701052', 'available'),
(2923, 70, 'I000706925', 'available'),
(2924, 70, 'I000709016', 'available'),
(2925, 70, 'I000704899', 'available'),
(2926, 70, 'I000706842', 'available'),
(2927, 70, 'I000708814', 'available'),
(2928, 70, 'I000701814', 'available'),
(2929, 70, 'I000704052', 'available'),
(2930, 70, 'I000701566', 'available'),
(2931, 70, 'I000706203', 'available'),
(2932, 70, 'I000708668', 'available'),
(2933, 70, 'I000704782', 'available'),
(2934, 70, 'I000707759', 'available'),
(2935, 70, 'I000709297', 'available'),
(2936, 70, 'I000702729', 'available'),
(2937, 70, 'I000708087', 'available'),
(2938, 70, 'I000707011', 'available'),
(2939, 70, 'I000701331', 'available'),
(2940, 70, 'I000702115', 'available'),
(2941, 70, 'I000705122', 'available'),
(2942, 70, 'I000701199', 'available'),
(2943, 70, 'I000707588', 'available'),
(2944, 70, 'I000702299', 'available'),
(2945, 70, 'I000706295', 'available'),
(2946, 70, 'I000703828', 'available'),
(2947, 70, 'I000701993', 'available'),
(2948, 70, 'I000707331', 'available'),
(2949, 70, 'I000708270', 'available'),
(2950, 70, 'I000705060', 'available'),
(2951, 70, 'I000707615', 'available'),
(2952, 70, 'I000704063', 'available'),
(2953, 70, 'I000703573', 'available'),
(2954, 70, 'I000708238', 'available'),
(2955, 70, 'I000702274', 'available'),
(2956, 70, 'I000708975', 'available'),
(2957, 70, 'I000709965', 'available'),
(2958, 70, 'I000704478', 'available'),
(2959, 70, 'I000708311', 'available'),
(2960, 70, 'I000704521', 'available'),
(2961, 70, 'I000706017', 'available'),
(2962, 70, 'I000705540', 'available'),
(2963, 70, 'I000701024', 'available'),
(2964, 70, 'I000709510', 'available'),
(2965, 70, 'I000704386', 'available'),
(2966, 70, 'I000701665', 'available'),
(2967, 70, 'I000705064', 'available'),
(2968, 70, 'I000704156', 'available'),
(2969, 70, 'I000701300', 'available'),
(2970, 70, 'I000709215', 'available'),
(2971, 70, 'I000705303', 'available'),
(2972, 70, 'I000703732', 'available'),
(2973, 70, 'I000709089', 'available'),
(2974, 70, 'I000708021', 'available'),
(2975, 70, 'I000702514', 'available'),
(2976, 70, 'I000706485', 'available'),
(2977, 70, 'I000705505', 'available'),
(2978, 70, 'I000705135', 'available'),
(2979, 70, 'I000706930', 'available'),
(2980, 70, 'I000708365', 'available'),
(2981, 70, 'I000701481', 'available'),
(2982, 70, 'I000701761', 'available'),
(2983, 70, 'I000706039', 'available'),
(2984, 70, 'I000703818', 'available'),
(2985, 70, 'I000704092', 'available'),
(2986, 70, 'I000702295', 'available'),
(2987, 70, 'I000701703', 'available'),
(2988, 70, 'I000706611', 'available'),
(2989, 70, 'I000706199', 'available'),
(2990, 70, 'I000708190', 'available'),
(2991, 70, 'I000704742', 'available'),
(2992, 70, 'I000702302', 'available'),
(2993, 70, 'I000704977', 'available'),
(2994, 70, 'I000705752', 'available'),
(2995, 70, 'I000705405', 'available'),
(2996, 70, 'I000709846', 'available'),
(2997, 70, 'I000707349', 'available'),
(2998, 70, 'I000709926', 'available'),
(2999, 70, 'I000709369', 'available'),
(3000, 70, 'I000702588', 'available'),
(3001, 70, 'I000704769', 'available'),
(3002, 70, 'I000708223', 'available'),
(3003, 70, 'I000709235', 'available'),
(3004, 70, 'I000706363', 'available'),
(3005, 70, 'I000705704', 'available'),
(3006, 70, 'I000701026', 'available'),
(3007, 70, 'I000708071', 'available'),
(3008, 70, 'I000704024', 'available'),
(3009, 70, 'I000707732', 'available'),
(3010, 70, 'I000706598', 'available'),
(3011, 70, 'I000705698', 'available'),
(3012, 70, 'I000707795', 'available'),
(3013, 70, 'I000701740', 'available'),
(3014, 70, 'I000702821', 'available'),
(3015, 70, 'I000705857', 'available'),
(3016, 70, 'I000708803', 'available'),
(3017, 70, 'I000701453', 'available'),
(3018, 70, 'I000706302', 'available'),
(3019, 70, 'I000702574', 'available'),
(3020, 70, 'I000705102', 'available'),
(3021, 70, 'I000705687', 'available'),
(3022, 70, 'I000703056', 'available'),
(3023, 70, 'I000701791', 'available'),
(3024, 70, 'I000701436', 'available'),
(3025, 70, 'I000706086', 'available'),
(3026, 70, 'I000707613', 'available'),
(3027, 70, 'I000703785', 'available'),
(3028, 70, 'I000707321', 'available'),
(3029, 70, 'I000709941', 'available'),
(3030, 70, 'I000709912', 'available'),
(3031, 70, 'I000703766', 'available'),
(3032, 70, 'I000704264', 'available'),
(3033, 70, 'I000706040', 'available'),
(3034, 70, 'I000708287', 'available'),
(3035, 70, 'I000708015', 'available'),
(3036, 70, 'I000709605', 'available'),
(3037, 70, 'I000706278', 'available'),
(3038, 70, 'I000709757', 'available'),
(3039, 70, 'I000706795', 'available'),
(3040, 70, 'I000709196', 'available'),
(3041, 70, 'I000709490', 'available'),
(3042, 70, 'I000702463', 'available'),
(3043, 70, 'I000701305', 'available'),
(3044, 70, 'I000706545', 'available'),
(3045, 70, 'I000708524', 'available'),
(3046, 70, 'I000707961', 'available'),
(3047, 70, 'I000706461', 'available'),
(3048, 70, 'I000701018', 'available'),
(3049, 70, 'I000701113', 'available'),
(3050, 70, 'I000706894', 'available'),
(3051, 70, 'I000707885', 'available'),
(3052, 70, 'I000701683', 'available'),
(3053, 70, 'I000709925', 'available'),
(3054, 70, 'I000706345', 'available'),
(3055, 70, 'I000708141', 'available'),
(3056, 70, 'I000702495', 'available'),
(3057, 70, 'I000705037', 'available'),
(3058, 70, 'I000705171', 'available'),
(3059, 70, 'I000702835', 'available'),
(3060, 70, 'I000702244', 'available'),
(3061, 70, 'I000703035', 'available'),
(3062, 70, 'I000705382', 'available'),
(3063, 70, 'I000706858', 'available'),
(3064, 70, 'I000704778', 'available'),
(3065, 70, 'I000705972', 'available'),
(3066, 70, 'I000702383', 'available'),
(3067, 70, 'I000704440', 'available'),
(3068, 70, 'I000703825', 'available'),
(3069, 70, 'I000709632', 'available'),
(3070, 70, 'I000708491', 'available'),
(3071, 70, 'I000706550', 'available'),
(3072, 70, 'I000706948', 'available'),
(3073, 70, 'I000704967', 'available'),
(3074, 70, 'I000708762', 'available'),
(3075, 70, 'I000708684', 'available'),
(3076, 70, 'I000709047', 'available'),
(3077, 70, 'I000707649', 'available'),
(3078, 70, 'I000704815', 'available'),
(3079, 70, 'I000708565', 'available'),
(3080, 70, 'I000706318', 'available'),
(3081, 70, 'I000701871', 'available'),
(3082, 70, 'I000709605', 'available'),
(3083, 70, 'I000707564', 'available'),
(3084, 70, 'I000707179', 'available'),
(3085, 70, 'I000709233', 'available'),
(3086, 70, 'I000706742', 'available'),
(3087, 70, 'I000703915', 'available'),
(3088, 70, 'I000708609', 'available'),
(3089, 70, 'I000706248', 'available'),
(3090, 70, 'I000709145', 'available'),
(3091, 70, 'I000704441', 'available'),
(3092, 70, 'I000705990', 'available'),
(3093, 70, 'I000705754', 'available'),
(3094, 70, 'I000705327', 'available'),
(3095, 70, 'I000708342', 'available'),
(3096, 70, 'I000706475', 'available'),
(3097, 70, 'I000706852', 'available'),
(3098, 70, 'I000703828', 'available'),
(3099, 70, 'I000705974', 'available'),
(3100, 70, 'I000701977', 'available'),
(3101, 70, 'I000705710', 'available'),
(3102, 70, 'I000704608', 'available'),
(3103, 70, 'I000704067', 'available'),
(3104, 70, 'I000703712', 'available'),
(3105, 70, 'I000701850', 'available'),
(3106, 70, 'I000705653', 'available'),
(3107, 70, 'I000704663', 'available'),
(3108, 70, 'I000705641', 'available'),
(3109, 70, 'I000706126', 'available'),
(3110, 70, 'I000706540', 'available'),
(3111, 70, 'I000701034', 'available'),
(3112, 70, 'I000709692', 'available'),
(3113, 70, 'I000707767', 'available'),
(3114, 70, 'I000701261', 'available'),
(3115, 70, 'I000702969', 'available'),
(3116, 70, 'I000703979', 'available'),
(3117, 70, 'I000707789', 'available'),
(3118, 70, 'I000708089', 'available'),
(3119, 70, 'I000706498', 'available'),
(3120, 70, 'I000709583', 'available'),
(3121, 70, 'I000701499', 'available'),
(3122, 70, 'I000709159', 'available'),
(3123, 70, 'I000702434', 'available'),
(3124, 70, 'I000703736', 'available'),
(3125, 70, 'I000707994', 'available'),
(3126, 70, 'I000705178', 'available'),
(3127, 70, 'I000703354', 'available'),
(3128, 70, 'I000703027', 'available'),
(3129, 70, 'I000704241', 'available'),
(3130, 70, 'I000703255', 'available'),
(3131, 70, 'I000709593', 'available'),
(3132, 70, 'I000703887', 'available'),
(3133, 70, 'I000709435', 'available'),
(3134, 70, 'I000704184', 'available'),
(3135, 70, 'I000707494', 'available'),
(3136, 70, 'I000709641', 'available'),
(3137, 70, 'I000702651', 'available'),
(3138, 70, 'I000703507', 'available'),
(3139, 70, 'I000701682', 'available'),
(3140, 70, 'I000704317', 'available'),
(3141, 70, 'I000707802', 'available'),
(3142, 70, 'I000705288', 'available'),
(3143, 70, 'I000707163', 'available'),
(3144, 70, 'I000702419', 'available'),
(3145, 70, 'I000703414', 'available'),
(3146, 70, 'I000702005', 'available'),
(3147, 70, 'I000702285', 'available'),
(3148, 70, 'I000702282', 'available'),
(3149, 70, 'I000708698', 'available'),
(3150, 70, 'I000704635', 'available'),
(3151, 70, 'I000704691', 'available'),
(3152, 70, 'I000706326', 'available'),
(3153, 70, 'I000709884', 'available'),
(3154, 70, 'I000708778', 'available'),
(3155, 70, 'I000709735', 'available'),
(3156, 70, 'I000701883', 'available'),
(3157, 70, 'I000706080', 'available'),
(3158, 70, 'I000703477', 'available'),
(3159, 70, 'I000706261', 'available'),
(3160, 70, 'I000701577', 'available'),
(3161, 70, 'I000704521', 'available'),
(3162, 70, 'I000703627', 'available'),
(3163, 70, 'I000705142', 'available'),
(3164, 70, 'I000705959', 'available'),
(3165, 70, 'I000707850', 'available'),
(3166, 70, 'I000709444', 'available'),
(3167, 70, 'I000704700', 'available'),
(3168, 70, 'I000708215', 'available'),
(3169, 70, 'I000709703', 'available'),
(3170, 70, 'I000706330', 'available'),
(3171, 70, 'I000709864', 'available'),
(3172, 70, 'I000708347', 'available'),
(3173, 70, 'I000701737', 'available'),
(3174, 70, 'I000706793', 'available'),
(3175, 70, 'I000709341', 'available'),
(3176, 70, 'I000703262', 'available'),
(3177, 70, 'I000708817', 'available'),
(3178, 70, 'I000707071', 'available'),
(3179, 70, 'I000704908', 'available'),
(3180, 70, 'I000706537', 'available'),
(3181, 70, 'I000702947', 'available'),
(3182, 70, 'I000701067', 'available'),
(3183, 70, 'I000706656', 'available'),
(3184, 70, 'I000703786', 'available'),
(3185, 70, 'I000706943', 'available'),
(3186, 70, 'I000702748', 'available'),
(3187, 70, 'I000702641', 'available'),
(3188, 70, 'I000702358', 'available'),
(3189, 70, 'I000706953', 'available'),
(3190, 70, 'I000706791', 'available'),
(3191, 70, 'I000707360', 'available'),
(3192, 70, 'I000701635', 'available'),
(3193, 70, 'I000704738', 'available'),
(3194, 70, 'I000707225', 'available'),
(3195, 70, 'I000708547', 'available'),
(3196, 70, 'I000702281', 'available'),
(3197, 70, 'I000706330', 'available'),
(3198, 70, 'I000704648', 'available'),
(3199, 70, 'I000708255', 'available'),
(3200, 70, 'I000701935', 'available'),
(3201, 70, 'I000702382', 'available'),
(3202, 70, 'I000704759', 'available'),
(3203, 70, 'I000709892', 'available'),
(3204, 70, 'I000705031', 'available'),
(3205, 70, 'I000701474', 'available'),
(3206, 70, 'I000704647', 'available'),
(3207, 70, 'I000706943', 'available'),
(3208, 70, 'I000704009', 'available'),
(3209, 70, 'I000708590', 'available'),
(3210, 70, 'I000705039', 'available'),
(3211, 70, 'I000704352', 'available'),
(3212, 70, 'I000705252', 'available'),
(3213, 70, 'I000701531', 'available'),
(3214, 70, 'I000703734', 'available'),
(3215, 70, 'I000707073', 'available'),
(3216, 70, 'I000705695', 'available'),
(3217, 70, 'I000703471', 'available'),
(3218, 70, 'I000709596', 'available'),
(3219, 70, 'I000702004', 'available'),
(3220, 70, 'I000709633', 'available'),
(3221, 70, 'I000706891', 'available'),
(3222, 70, 'I000703159', 'available'),
(3223, 70, 'I000701902', 'available'),
(3224, 70, 'I000702625', 'available'),
(3225, 70, 'I000709663', 'available'),
(3226, 70, 'I000701894', 'available'),
(3227, 70, 'I000706931', 'available'),
(3228, 70, 'I000701549', 'available'),
(3229, 70, 'I000706966', 'available'),
(3230, 70, 'I000705333', 'available'),
(3231, 70, 'I000706553', 'available'),
(3232, 70, 'I000708410', 'available'),
(3233, 70, 'I000705735', 'available'),
(3234, 70, 'I000701177', 'available'),
(3235, 70, 'I000701664', 'available'),
(3236, 70, 'I000706932', 'available'),
(3237, 70, 'I000703640', 'available'),
(3238, 70, 'I000701690', 'available'),
(3239, 70, 'I000703265', 'available'),
(3240, 70, 'I000705729', 'available'),
(3241, 70, 'I000702381', 'available'),
(3242, 70, 'I000706595', 'available'),
(3243, 70, 'I000708794', 'available'),
(3244, 70, 'I000703635', 'available'),
(3245, 70, 'I000702828', 'available'),
(3246, 70, 'I000706928', 'available'),
(3247, 70, 'I000702094', 'available'),
(3248, 70, 'I000708348', 'available'),
(3249, 70, 'I000708675', 'available'),
(3250, 70, 'I000703604', 'available'),
(3251, 70, 'I000703312', 'available'),
(3252, 70, 'I000707409', 'available'),
(3253, 70, 'I000706879', 'available'),
(3254, 70, 'I000704927', 'available'),
(3255, 70, 'I000708612', 'available'),
(3256, 70, 'I000706206', 'available'),
(3257, 70, 'I000708319', 'available'),
(3258, 70, 'I000707127', 'available'),
(3259, 70, 'I000701444', 'available'),
(3260, 70, 'I000703207', 'available'),
(3261, 70, 'I000706843', 'available'),
(3262, 70, 'I000706074', 'available'),
(3263, 70, 'I000703728', 'available'),
(3264, 70, 'I000702077', 'available'),
(3265, 70, 'I000709827', 'available'),
(3266, 70, 'I000709936', 'available'),
(3267, 70, 'I000705254', 'available'),
(3268, 70, 'I000705312', 'available'),
(3269, 70, 'I000703554', 'available'),
(3270, 70, 'I000705634', 'available'),
(3271, 70, 'I000706980', 'available'),
(3272, 70, 'I000704817', 'available'),
(3273, 70, 'I000705619', 'available'),
(3274, 70, 'I000704330', 'available'),
(3275, 70, 'I000708600', 'available'),
(3276, 70, 'I000702349', 'available'),
(3277, 70, 'I000707151', 'available'),
(3278, 70, 'I000701635', 'available'),
(3279, 70, 'I000706625', 'available'),
(3280, 70, 'I000707064', 'available'),
(3281, 70, 'I000706513', 'available'),
(3282, 70, 'I000701089', 'available'),
(3283, 70, 'I000701979', 'available'),
(3284, 70, 'I000704716', 'available'),
(3285, 70, 'I000703708', 'available'),
(3286, 70, 'I000704929', 'available'),
(3287, 70, 'I000701191', 'available'),
(3288, 70, 'I000709182', 'available'),
(3289, 70, 'I000705654', 'available'),
(3290, 70, 'I000701702', 'available'),
(3291, 70, 'I000703373', 'available'),
(3292, 70, 'I000707674', 'available'),
(3293, 70, 'I000702363', 'available'),
(3294, 70, 'I000706745', 'available'),
(3295, 70, 'I000702800', 'available'),
(3296, 70, 'I000705409', 'available'),
(3297, 70, 'I000709481', 'available'),
(3298, 70, 'I000709367', 'available'),
(3299, 70, 'I000706559', 'available'),
(3300, 70, 'I000708237', 'available'),
(3301, 70, 'I000702374', 'available'),
(3302, 70, 'I000701888', 'available'),
(3303, 70, 'I000709416', 'available'),
(3304, 70, 'I000703827', 'available'),
(3305, 70, 'I000709874', 'available'),
(3306, 70, 'I000704073', 'available'),
(3307, 70, 'I000701444', 'available'),
(3308, 70, 'I000703696', 'available'),
(3309, 70, 'I000707143', 'available'),
(3310, 70, 'I000707365', 'available'),
(3311, 70, 'I000707559', 'available'),
(3312, 70, 'I000709241', 'available'),
(3313, 70, 'I000703473', 'available'),
(3314, 70, 'I000704724', 'available'),
(3315, 70, 'I000708407', 'available'),
(3316, 70, 'I000704140', 'available'),
(3317, 70, 'I000706898', 'available'),
(3318, 70, 'I000707717', 'available'),
(3319, 70, 'I000707456', 'available'),
(3320, 70, 'I000704132', 'available'),
(3321, 70, 'I000706304', 'available'),
(3322, 70, 'I000705508', 'available'),
(3323, 70, 'I000706825', 'available'),
(3324, 70, 'I000703022', 'available'),
(3325, 70, 'I000708898', 'available'),
(3326, 70, 'I000701784', 'available'),
(3327, 70, 'I000702730', 'available'),
(3328, 70, 'I000703722', 'available'),
(3329, 70, 'I000705071', 'available'),
(3330, 70, 'I000703758', 'available'),
(3331, 70, 'I000702676', 'available'),
(3332, 70, 'I000702931', 'available'),
(3333, 70, 'I000708527', 'available'),
(3334, 70, 'I000704537', 'available'),
(3335, 70, 'I000704370', 'available'),
(3336, 70, 'I000701112', 'available'),
(3337, 70, 'I000703719', 'available'),
(3338, 70, 'I000701556', 'available'),
(3339, 70, 'I000709008', 'available'),
(3340, 70, 'I000708065', 'available'),
(3341, 70, 'I000704069', 'available'),
(3342, 70, 'I000708646', 'available'),
(3343, 70, 'I000705566', 'available'),
(3344, 70, 'I000704856', 'available'),
(3345, 70, 'I000701078', 'available'),
(3346, 70, 'I000707686', 'available'),
(3347, 70, 'I000705254', 'available'),
(3348, 70, 'I000707571', 'available'),
(3349, 70, 'I000701474', 'available'),
(3350, 70, 'I000708776', 'available'),
(3351, 70, 'I000704390', 'available'),
(3352, 70, 'I000708340', 'available'),
(3353, 70, 'I000705865', 'available'),
(3354, 70, 'I000704288', 'available'),
(3355, 70, 'I000705603', 'available'),
(3356, 70, 'I000708774', 'available'),
(3357, 70, 'I000703195', 'available'),
(3358, 70, 'I000703654', 'available'),
(3359, 70, 'I000708584', 'available'),
(3360, 70, 'I000708123', 'available'),
(3361, 70, 'I000708995', 'available'),
(3362, 70, 'I000702706', 'available'),
(3363, 70, 'I000701378', 'available'),
(3364, 70, 'I000705742', 'available'),
(3365, 70, 'I000701600', 'available'),
(3366, 70, 'I000709635', 'available'),
(3367, 70, 'I000701577', 'available'),
(3368, 70, 'I000701162', 'available'),
(3369, 70, 'I000702799', 'available'),
(3370, 70, 'I000701637', 'available'),
(3371, 70, 'I000709825', 'available'),
(3372, 70, 'I000704847', 'available'),
(3373, 70, 'I000707580', 'available'),
(3374, 70, 'I000705316', 'available'),
(3375, 70, 'I000709697', 'available'),
(3376, 70, 'I000705305', 'available'),
(3377, 70, 'I000708441', 'available'),
(3378, 70, 'I000708540', 'available'),
(3379, 70, 'I000709213', 'available'),
(3380, 70, 'I000703094', 'available'),
(3381, 70, 'I000706790', 'available'),
(3382, 70, 'I000706066', 'available'),
(3383, 70, 'I000704064', 'available'),
(3384, 70, 'I000708538', 'available'),
(3385, 70, 'I000703560', 'available'),
(3386, 70, 'I000703714', 'available'),
(3387, 70, 'I000706439', 'available'),
(3388, 70, 'I000702834', 'available'),
(3389, 70, 'I000708800', 'available'),
(3390, 70, 'I000701608', 'available'),
(3391, 70, 'I000709258', 'available'),
(3392, 70, 'I000703354', 'available'),
(3393, 70, 'I000702322', 'available'),
(3394, 70, 'I000708838', 'available'),
(3395, 70, 'I000706931', 'available'),
(3396, 70, 'I000702066', 'available'),
(3397, 70, 'I000707794', 'available'),
(3398, 70, 'I000707402', 'available'),
(3399, 70, 'I000702405', 'available'),
(3400, 70, 'I000709982', 'available'),
(3401, 70, 'I000709930', 'available'),
(3402, 70, 'I000703741', 'available'),
(3403, 70, 'I000703615', 'available'),
(3404, 70, 'I000702928', 'available'),
(3405, 70, 'I000709022', 'available'),
(3406, 70, 'I000704965', 'available'),
(3407, 70, 'I000708062', 'available'),
(3408, 70, 'I000704409', 'available'),
(3409, 70, 'I000706115', 'available'),
(3410, 70, 'I000708421', 'available'),
(3411, 70, 'I000709874', 'available'),
(3412, 70, 'I000707731', 'available'),
(3413, 70, 'I000707942', 'available'),
(3414, 25, 'I000253375', 'available'),
(3415, 49, 'I000499316', 'unavailable'),
(3416, 71, 'I000718340', 'available'),
(3417, 71, 'I000718947', 'available'),
(3418, 71, 'I000717105', 'available'),
(3419, 71, 'I000712654', 'available'),
(3420, 71, 'I000716640', 'available'),
(3421, 71, 'I000715450', 'available'),
(3422, 71, 'I000719954', 'available'),
(3423, 71, 'I000717256', 'available'),
(3424, 71, 'I000715441', 'available'),
(3425, 71, 'I000714257', 'available'),
(3426, 71, 'I000717076', 'available'),
(3427, 71, 'I000718304', 'available'),
(3428, 71, 'I000716256', 'available'),
(3429, 71, 'I000711311', 'available'),
(3430, 71, 'I000716778', 'available'),
(3431, 71, 'I000712148', 'available'),
(3432, 71, 'I000718982', 'available'),
(3433, 71, 'I000719499', 'available'),
(3434, 71, 'I000712807', 'available'),
(3435, 71, 'I000716019', 'available'),
(3436, 71, 'I000711261', 'available'),
(3437, 71, 'I000717967', 'available'),
(3438, 71, 'I000717200', 'available'),
(3439, 71, 'I000714267', 'available'),
(3440, 71, 'I000719440', 'available'),
(3441, 71, 'I000718131', 'available'),
(3442, 71, 'I000718703', 'available'),
(3443, 71, 'I000714347', 'available'),
(3444, 71, 'I000716431', 'available'),
(3445, 71, 'I000714246', 'available'),
(3446, 71, 'I000715775', 'available'),
(3447, 71, 'I000715168', 'available'),
(3448, 71, 'I000715132', 'available'),
(3449, 71, 'I000719915', 'available'),
(3450, 71, 'I000712100', 'available'),
(3451, 71, 'I000718583', 'available'),
(3452, 71, 'I000714084', 'available'),
(3453, 72, 'I000721701', 'available'),
(3454, 72, 'I000723882', 'available'),
(3455, 72, 'I000727723', 'available'),
(3456, 72, 'I000722699', 'available'),
(3457, 72, 'I000724715', 'available'),
(3458, 72, 'I000725360', 'available'),
(3459, 72, 'I000729987', 'available'),
(3460, 72, 'I000728670', 'available'),
(3461, 72, 'I000725918', 'available'),
(3462, 72, 'I000722986', 'available'),
(3463, 72, 'I000725628', 'available'),
(3464, 72, 'I000722121', 'available'),
(3465, 72, 'I000723228', 'available'),
(3466, 72, 'I000728387', 'available'),
(3467, 72, 'I000727368', 'available'),
(3468, 72, 'I000726101', 'available'),
(3469, 72, 'I000726751', 'available'),
(3470, 72, 'I000724436', 'available'),
(3471, 72, 'I000721096', 'available'),
(3472, 72, 'I000721063', 'available'),
(3473, 72, 'I000724396', 'available'),
(3474, 72, 'I000726550', 'available'),
(3475, 72, 'I000726024', 'available'),
(3476, 72, 'I000724576', 'available'),
(3477, 72, 'I000721318', 'available'),
(3478, 72, 'I000724013', 'available'),
(3479, 72, 'I000724449', 'available'),
(3480, 72, 'I000728001', 'available'),
(3481, 72, 'I000726122', 'available'),
(3482, 72, 'I000722389', 'available'),
(3483, 72, 'I000723878', 'available'),
(3484, 72, 'I000729335', 'available'),
(3485, 72, 'I000721526', 'available'),
(3486, 72, 'I000723569', 'available'),
(3487, 72, 'I000722227', 'available'),
(3488, 72, 'I000724257', 'available'),
(3489, 72, 'I000722428', 'available'),
(3490, 72, 'I000729975', 'available'),
(3491, 72, 'I000728730', 'available'),
(3492, 72, 'I000728684', 'available'),
(3493, 73, 'I000737108', 'available'),
(3494, 73, 'I000733959', 'available'),
(3495, 73, 'I000739888', 'available'),
(3496, 73, 'I000732526', 'available'),
(3497, 73, 'I000732893', 'available'),
(3498, 73, 'I000733873', 'available'),
(3499, 73, 'I000735817', 'available'),
(3500, 73, 'I000732551', 'available'),
(3501, 73, 'I000733756', 'available'),
(3502, 73, 'I000736677', 'available'),
(3503, 73, 'I000731947', 'available'),
(3504, 73, 'I000732529', 'available'),
(3505, 73, 'I000736817', 'available'),
(3506, 73, 'I000736656', 'available'),
(3507, 73, 'I000739740', 'available'),
(3508, 73, 'I000732437', 'available'),
(3509, 73, 'I000732539', 'available'),
(3510, 73, 'I000736929', 'available'),
(3511, 73, 'I000734086', 'available'),
(3512, 73, 'I000735164', 'available'),
(3513, 73, 'I000735966', 'available'),
(3514, 73, 'I000737161', 'available'),
(3515, 73, 'I000739257', 'available'),
(3516, 73, 'I000732067', 'available'),
(3517, 73, 'I000734232', 'available'),
(3518, 73, 'I000736692', 'available'),
(3519, 73, 'I000736688', 'available'),
(3520, 73, 'I000737121', 'available'),
(3521, 73, 'I000732000', 'available'),
(3522, 73, 'I000738547', 'available'),
(3523, 73, 'I000733677', 'available'),
(3524, 73, 'I000731438', 'available'),
(3525, 73, 'I000737807', 'available'),
(3526, 73, 'I000732541', 'available'),
(3527, 73, 'I000738083', 'available'),
(3528, 73, 'I000739293', 'available'),
(3529, 73, 'I000736973', 'available'),
(3530, 73, 'I000736916', 'available'),
(3531, 73, 'I000731228', 'available'),
(3532, 73, 'I000738239', 'available'),
(3533, 73, 'I000734550', 'available'),
(3534, 73, 'I000737884', 'available'),
(3535, 73, 'I000733004', 'available'),
(3536, 73, 'I000737184', 'available'),
(3537, 73, 'I000736053', 'available'),
(3538, 73, 'I000737501', 'available'),
(3539, 73, 'I000731107', 'available'),
(3540, 73, 'I000733112', 'available'),
(3541, 73, 'I000736305', 'available'),
(3542, 73, 'I000734865', 'available'),
(3543, 73, 'I000734270', 'available'),
(3544, 73, 'I000733486', 'available');
INSERT INTO `item` (`id`, `equipment_id`, `item_number`, `status`) VALUES
(3545, 73, 'I000733886', 'available'),
(3546, 73, 'I000732042', 'available'),
(3547, 73, 'I000735964', 'available'),
(3548, 73, 'I000738015', 'available'),
(3549, 73, 'I000734198', 'available'),
(3550, 73, 'I000739774', 'available'),
(3551, 73, 'I000737000', 'available'),
(3552, 73, 'I000734613', 'available'),
(3553, 73, 'I000738806', 'available'),
(3554, 73, 'I000732855', 'available'),
(3555, 73, 'I000736568', 'available'),
(3556, 73, 'I000732259', 'available'),
(3557, 73, 'I000734881', 'available'),
(3558, 73, 'I000734988', 'available'),
(3559, 73, 'I000737999', 'available'),
(3560, 73, 'I000732163', 'available'),
(3561, 73, 'I000739693', 'available'),
(3562, 73, 'I000734844', 'available'),
(3563, 73, 'I000737874', 'available'),
(3564, 73, 'I000732590', 'available'),
(3565, 73, 'I000737488', 'available'),
(3566, 73, 'I000735041', 'available'),
(3567, 73, 'I000735967', 'available'),
(3568, 73, 'I000737147', 'available'),
(3569, 73, 'I000734168', 'available'),
(3570, 73, 'I000731442', 'available'),
(3571, 73, 'I000731048', 'available'),
(3572, 73, 'I000739992', 'available'),
(3573, 73, 'I000735002', 'available'),
(3574, 73, 'I000737257', 'available'),
(3575, 73, 'I000735927', 'available'),
(3576, 73, 'I000736893', 'available'),
(3577, 73, 'I000737770', 'available'),
(3578, 73, 'I000737660', 'available'),
(3579, 73, 'I000738189', 'available'),
(3580, 73, 'I000737802', 'available'),
(3581, 73, 'I000735607', 'available'),
(3582, 73, 'I000734905', 'available'),
(3583, 73, 'I000738885', 'available'),
(3584, 73, 'I000731742', 'available'),
(3585, 73, 'I000732709', 'available'),
(3586, 73, 'I000731946', 'available'),
(3587, 73, 'I000736664', 'available'),
(3588, 73, 'I000735691', 'available'),
(3589, 73, 'I000735296', 'available'),
(3590, 73, 'I000737468', 'available'),
(3591, 73, 'I000738820', 'available'),
(3592, 73, 'I000733521', 'available'),
(3593, 73, 'I000738586', 'available'),
(3594, 73, 'I000731027', 'available'),
(3595, 73, 'I000731069', 'available'),
(3596, 73, 'I000735359', 'available'),
(3597, 73, 'I000731091', 'available'),
(3598, 73, 'I000737136', 'available'),
(3599, 73, 'I000733044', 'available'),
(3600, 73, 'I000731493', 'available'),
(3601, 73, 'I000738614', 'available'),
(3602, 73, 'I000739499', 'available'),
(3603, 73, 'I000732841', 'available'),
(3604, 73, 'I000733123', 'available'),
(3605, 73, 'I000731052', 'available'),
(3606, 73, 'I000738771', 'available'),
(3607, 73, 'I000732810', 'available'),
(3608, 73, 'I000734738', 'available'),
(3609, 73, 'I000739612', 'available'),
(3610, 73, 'I000733617', 'available'),
(3611, 73, 'I000733504', 'available'),
(3612, 73, 'I000735915', 'available'),
(3613, 73, 'I000737477', 'available'),
(3614, 73, 'I000732117', 'available'),
(3615, 73, 'I000737813', 'available'),
(3616, 73, 'I000732021', 'available'),
(3617, 73, 'I000737357', 'available'),
(3618, 73, 'I000733490', 'available'),
(3619, 73, 'I000737728', 'available'),
(3620, 73, 'I000731709', 'available'),
(3621, 73, 'I000736809', 'available'),
(3622, 73, 'I000734623', 'available'),
(3623, 73, 'I000732836', 'available'),
(3624, 73, 'I000732168', 'available'),
(3625, 73, 'I000735298', 'available'),
(3626, 73, 'I000733439', 'available'),
(3627, 73, 'I000737539', 'available'),
(3628, 73, 'I000732930', 'available'),
(3629, 73, 'I000739097', 'available'),
(3630, 73, 'I000731368', 'available'),
(3631, 73, 'I000735749', 'available'),
(3632, 73, 'I000731194', 'available'),
(3633, 73, 'I000735842', 'available'),
(3634, 73, 'I000738874', 'available'),
(3635, 73, 'I000732304', 'available'),
(3636, 73, 'I000737800', 'available'),
(3637, 73, 'I000739098', 'available'),
(3638, 73, 'I000736946', 'available'),
(3639, 73, 'I000733973', 'available'),
(3640, 73, 'I000732611', 'available'),
(3641, 73, 'I000738127', 'available'),
(3642, 73, 'I000733803', 'available'),
(3643, 73, 'I000736753', 'available'),
(3644, 73, 'I000737354', 'available'),
(3645, 73, 'I000735833', 'available'),
(3646, 73, 'I000737272', 'available'),
(3647, 73, 'I000737949', 'available'),
(3648, 73, 'I000733480', 'available'),
(3649, 73, 'I000738512', 'available'),
(3650, 73, 'I000737480', 'available'),
(3651, 73, 'I000737095', 'available'),
(3652, 73, 'I000736643', 'available'),
(3653, 73, 'I000732428', 'available'),
(3654, 73, 'I000738935', 'available'),
(3655, 73, 'I000736060', 'available'),
(3656, 73, 'I000731801', 'available'),
(3657, 73, 'I000739178', 'available'),
(3658, 73, 'I000734060', 'available'),
(3659, 73, 'I000732175', 'available'),
(3660, 73, 'I000738065', 'available'),
(3661, 73, 'I000736553', 'available'),
(3662, 73, 'I000735551', 'available'),
(3663, 73, 'I000734771', 'available'),
(3664, 73, 'I000733576', 'available'),
(3665, 73, 'I000732942', 'available'),
(3666, 73, 'I000738793', 'available'),
(3667, 73, 'I000731173', 'available'),
(3668, 73, 'I000738300', 'available'),
(3669, 73, 'I000736212', 'available'),
(3670, 73, 'I000732903', 'available'),
(3671, 73, 'I000731407', 'available'),
(3672, 73, 'I000733680', 'available'),
(3673, 73, 'I000737840', 'available'),
(3674, 73, 'I000739980', 'available'),
(3675, 73, 'I000735126', 'available'),
(3676, 73, 'I000733165', 'available'),
(3677, 73, 'I000737152', 'available'),
(3678, 73, 'I000732476', 'available'),
(3679, 73, 'I000735254', 'available'),
(3680, 73, 'I000737690', 'available'),
(3681, 73, 'I000732893', 'available'),
(3682, 73, 'I000738333', 'available'),
(3683, 73, 'I000738439', 'available'),
(3684, 73, 'I000737410', 'available'),
(3685, 73, 'I000736430', 'available'),
(3686, 73, 'I000733618', 'available'),
(3687, 73, 'I000734337', 'available'),
(3688, 73, 'I000733486', 'available'),
(3689, 73, 'I000733483', 'available'),
(3690, 73, 'I000736693', 'available'),
(3691, 73, 'I000734449', 'available'),
(3692, 73, 'I000739578', 'available'),
(3693, 73, 'I000735828', 'available'),
(3694, 73, 'I000735157', 'available'),
(3695, 73, 'I000733818', 'available'),
(3696, 73, 'I000737010', 'available'),
(3697, 73, 'I000737468', 'available'),
(3698, 73, 'I000731079', 'available'),
(3699, 73, 'I000736182', 'available'),
(3700, 73, 'I000737372', 'available'),
(3701, 73, 'I000736713', 'available'),
(3702, 73, 'I000736084', 'available'),
(3703, 73, 'I000738822', 'available'),
(3704, 73, 'I000738130', 'available'),
(3705, 73, 'I000731974', 'available'),
(3706, 73, 'I000737958', 'available'),
(3707, 73, 'I000731716', 'available'),
(3708, 73, 'I000734875', 'available'),
(3709, 73, 'I000733814', 'available'),
(3710, 73, 'I000734861', 'available'),
(3711, 73, 'I000732933', 'available'),
(3712, 73, 'I000731612', 'available'),
(3713, 73, 'I000737502', 'available'),
(3714, 73, 'I000739917', 'available'),
(3715, 73, 'I000732546', 'available'),
(3716, 73, 'I000737389', 'available'),
(3717, 73, 'I000738377', 'available'),
(3718, 73, 'I000731297', 'available'),
(3719, 73, 'I000736520', 'available'),
(3720, 73, 'I000733103', 'available'),
(3721, 73, 'I000737914', 'available'),
(3722, 73, 'I000738713', 'available'),
(3723, 73, 'I000733212', 'available'),
(3724, 73, 'I000738251', 'available'),
(3725, 73, 'I000735139', 'available'),
(3726, 73, 'I000734278', 'available'),
(3727, 73, 'I000736659', 'available'),
(3728, 73, 'I000731295', 'available'),
(3729, 73, 'I000738563', 'available'),
(3730, 73, 'I000735683', 'available'),
(3731, 73, 'I000737180', 'available'),
(3732, 73, 'I000733173', 'available'),
(3733, 73, 'I000733295', 'available'),
(3734, 73, 'I000731320', 'available'),
(3735, 73, 'I000733318', 'available'),
(3736, 73, 'I000734798', 'available'),
(3737, 73, 'I000731575', 'available'),
(3738, 73, 'I000733634', 'available'),
(3739, 73, 'I000738506', 'available'),
(3740, 73, 'I000736186', 'available'),
(3741, 73, 'I000733877', 'available'),
(3742, 73, 'I000737614', 'available'),
(3743, 73, 'I000735055', 'available'),
(3744, 73, 'I000738405', 'available'),
(3745, 73, 'I000734842', 'available'),
(3746, 73, 'I000731636', 'available'),
(3747, 73, 'I000734401', 'available'),
(3748, 73, 'I000734530', 'available'),
(3749, 73, 'I000734734', 'available'),
(3750, 73, 'I000733071', 'available'),
(3751, 73, 'I000735368', 'available'),
(3752, 73, 'I000735644', 'available'),
(3753, 73, 'I000733808', 'available'),
(3754, 73, 'I000732189', 'available'),
(3755, 73, 'I000731594', 'available'),
(3756, 73, 'I000739480', 'available'),
(3757, 73, 'I000732406', 'available'),
(3758, 73, 'I000738666', 'available'),
(3759, 73, 'I000736896', 'available'),
(3760, 73, 'I000736667', 'available'),
(3761, 73, 'I000734879', 'available'),
(3762, 73, 'I000734592', 'available'),
(3763, 73, 'I000735963', 'available'),
(3764, 73, 'I000733254', 'available'),
(3765, 73, 'I000737040', 'available'),
(3766, 73, 'I000734105', 'available'),
(3767, 73, 'I000738912', 'available'),
(3768, 73, 'I000735273', 'available'),
(3769, 73, 'I000739617', 'available'),
(3770, 73, 'I000733544', 'available'),
(3771, 73, 'I000736668', 'available'),
(3772, 73, 'I000733427', 'available'),
(3773, 73, 'I000738382', 'available'),
(3774, 73, 'I000731312', 'available'),
(3775, 73, 'I000732112', 'available'),
(3776, 73, 'I000734608', 'available'),
(3777, 73, 'I000734431', 'available'),
(3778, 73, 'I000735341', 'available'),
(3779, 73, 'I000731888', 'available'),
(3780, 73, 'I000734694', 'available'),
(3781, 73, 'I000737817', 'available'),
(3782, 73, 'I000731562', 'available'),
(3783, 73, 'I000733223', 'available'),
(3784, 73, 'I000739798', 'available'),
(3785, 73, 'I000737444', 'available'),
(3786, 73, 'I000737148', 'available'),
(3787, 73, 'I000734449', 'available'),
(3788, 73, 'I000735478', 'available'),
(3789, 73, 'I000738924', 'available'),
(3790, 73, 'I000731895', 'available'),
(3791, 73, 'I000733961', 'available'),
(3792, 73, 'I000733104', 'available'),
(3793, 73, 'I000733947', 'available'),
(3794, 73, 'I000735339', 'available'),
(3795, 73, 'I000731804', 'available'),
(3796, 73, 'I000732856', 'available'),
(3797, 73, 'I000735310', 'available'),
(3798, 73, 'I000732073', 'available'),
(3799, 73, 'I000738236', 'available'),
(3800, 73, 'I000736110', 'available'),
(3801, 73, 'I000734859', 'available'),
(3802, 73, 'I000738960', 'available'),
(3803, 73, 'I000733795', 'available'),
(3804, 73, 'I000738357', 'available'),
(3805, 73, 'I000736264', 'available'),
(3806, 73, 'I000732076', 'available'),
(3807, 73, 'I000738954', 'available'),
(3808, 73, 'I000736080', 'available'),
(3809, 73, 'I000732203', 'available'),
(3810, 73, 'I000734026', 'available'),
(3811, 73, 'I000731303', 'available'),
(3812, 73, 'I000739794', 'available'),
(3813, 73, 'I000732249', 'available'),
(3814, 73, 'I000737224', 'available'),
(3815, 73, 'I000733286', 'available'),
(3816, 73, 'I000735307', 'available'),
(3817, 73, 'I000736897', 'available'),
(3818, 73, 'I000735564', 'available'),
(3819, 73, 'I000736525', 'available'),
(3820, 73, 'I000739540', 'available'),
(3821, 73, 'I000738805', 'available'),
(3822, 73, 'I000738325', 'available'),
(3823, 73, 'I000735569', 'available'),
(3824, 73, 'I000737158', 'available'),
(3825, 73, 'I000733764', 'available'),
(3826, 73, 'I000735739', 'available'),
(3827, 73, 'I000736434', 'available'),
(3828, 73, 'I000736493', 'available'),
(3829, 73, 'I000731086', 'available'),
(3830, 73, 'I000731482', 'available'),
(3831, 73, 'I000738280', 'available'),
(3832, 73, 'I000737608', 'available'),
(3833, 73, 'I000731309', 'available'),
(3834, 73, 'I000739282', 'available'),
(3835, 73, 'I000731214', 'available'),
(3836, 73, 'I000735813', 'available'),
(3837, 73, 'I000738416', 'available'),
(3838, 73, 'I000737218', 'available'),
(3839, 73, 'I000731670', 'available'),
(3840, 73, 'I000731656', 'available'),
(3841, 73, 'I000736063', 'available'),
(3842, 73, 'I000734583', 'available'),
(3843, 73, 'I000734400', 'available'),
(3844, 73, 'I000734976', 'available'),
(3845, 73, 'I000734225', 'available'),
(3846, 73, 'I000737386', 'available'),
(3847, 73, 'I000731945', 'available'),
(3848, 73, 'I000739748', 'available'),
(3849, 73, 'I000734610', 'available'),
(3850, 73, 'I000737485', 'available'),
(3851, 73, 'I000735010', 'available'),
(3852, 73, 'I000738721', 'available'),
(3853, 73, 'I000732241', 'available'),
(3854, 73, 'I000733364', 'available'),
(3855, 73, 'I000736644', 'available'),
(3856, 73, 'I000734583', 'available'),
(3857, 73, 'I000733569', 'available'),
(3858, 73, 'I000737450', 'available'),
(3859, 74, 'I000745025', 'available'),
(3860, 74, 'I000746466', 'available'),
(3861, 74, 'I000748710', 'available'),
(3862, 74, 'I000743871', 'available'),
(3863, 74, 'I000746314', 'available'),
(3864, 74, 'I000747193', 'available');

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
(4, 3.000000, 34.000000),
(5, 7.590006, 80.057686),
(6, 7.590006, 80.057686),
(7, 7.617231, 80.343330);

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
(42, '2024-02-25 10:28:02', 'completed', 1310.00, NULL, 'RNT00042'),
(43, '2024-02-27 09:16:40', 'completed', 13310.00, NULL, 'RNT00043'),
(44, '2024-02-27 09:21:57', 'completed', 3010.00, NULL, 'RNT00044'),
(45, '2024-04-05 05:39:54', 'pending', 211927.00, NULL, 'RNT00045'),
(46, '2024-04-09 04:48:55', 'pending', 19500.00, NULL, 'RNT00046'),
(47, '2024-04-09 04:52:04', 'completed', 2300.00, NULL, 'RNT00047'),
(48, '2024-04-13 18:14:23', 'completed', 6450.00, NULL, 'RNT00048'),
(49, '2024-04-14 10:58:51', 'pending', 27800.00, NULL, 'RNT00049'),
(50, '2024-04-14 10:58:54', 'pending', 0.00, NULL, 'RNT00050'),
(51, '2024-04-14 11:01:18', 'completed', 15400.00, NULL, 'RNT00051');

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
  `status` enum('pending','rented','completed','cancelled','accepted','return_reported','rent_reported') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'pending',
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
(63, 32, 56, '2024-02-25', '2024-02-29', 'completed', NULL, 4100.00, 0.00, '2024-02-27 10:19:40', '2024-02-25 07:31:42'),
(64, 32, 25, '2024-02-25', '2024-02-28', 'accepted', NULL, 3910.00, 0.00, '2024-02-25 10:25:23', '2024-02-25 10:24:39'),
(65, 32, 56, '2024-02-25', '2024-02-28', 'accepted', NULL, 1400.00, 0.00, '2024-02-27 04:19:54', '2024-02-25 10:24:39'),
(66, 32, 25, '2024-02-28', '2024-02-29', 'return_reported', NULL, 1310.00, 0.00, '2024-04-07 08:15:12', '2024-02-25 10:28:02'),
(67, 32, 56, '2024-02-21', '2024-02-29', 'accepted', NULL, 2900.00, 0.00, '2024-04-13 18:16:45', '2024-02-27 09:16:40'),
(68, 32, 25, '2024-02-21', '2024-02-29', 'completed', NULL, 10410.00, 0.00, '2024-04-15 15:28:16', '2024-02-27 09:16:40'),
(69, 32, 25, '2024-02-29', '2024-03-01', 'cancelled', NULL, 1310.00, 0.00, '2024-04-06 10:27:17', '2024-02-27 09:21:57'),
(70, 32, 56, '2024-02-29', '2024-03-01', 'accepted', NULL, 1700.00, 0.00, '2024-02-27 09:23:24', '2024-02-27 09:21:57'),
(71, 32, 25, '2024-04-11', '2024-04-26', 'pending', NULL, 69027.00, 0.00, '2024-04-05 05:39:54', '2024-04-05 05:39:54'),
(72, 32, 56, '2024-04-11', '2024-04-26', 'pending', NULL, 142900.00, 0.00, '2024-04-05 05:39:54', '2024-04-05 05:39:54'),
(73, 32, 56, '2024-04-10', '2024-04-17', 'pending', NULL, 19500.00, 0.00, '2024-04-09 04:48:55', '2024-04-09 04:48:55'),
(74, 32, 56, '2024-04-22', '2024-04-28', 'rented', NULL, 2300.00, 0.00, '2024-04-14 08:05:24', '2024-04-09 04:52:04'),
(75, 32, 25, '2024-04-26', '2024-04-30', 'completed', NULL, 6450.00, 0.00, '2024-04-15 15:52:45', '2024-04-13 18:14:23'),
(76, 32, 56, '2024-04-30', '2024-05-21', 'pending', NULL, 6800.00, 0.00, '2024-04-14 10:58:51', '2024-04-14 10:58:51'),
(77, 32, 25, '2024-04-30', '2024-05-21', 'pending', NULL, 21000.00, 0.00, '2024-04-14 10:58:51', '2024-04-14 10:58:51'),
(78, 32, 56, '2024-04-24', '2024-04-30', 'accepted', NULL, 3400.00, 0.00, '2024-04-14 11:02:20', '2024-04-14 11:01:18'),
(79, 32, 25, '2024-04-24', '2024-04-30', 'completed', NULL, 12000.00, 0.00, '2024-04-15 15:52:49', '2024-04-14 11:01:18');

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
(25, 'ABC Rent', ' Colombo 04', 'B873242343', '076024489', 87, 'waiting', '', 3, '661c05a16e2d0.jpg'),
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
(56, 'nirmal', 'Address is required', '200156273849', '0713458323', 182, 'waiting', '65d8ad691543f.pdf', 4, '1.webp'),
(57, 'NS yudufc', 'No 255, Neluwa RD', '200187674509', '+94716024489', 188, 'waiting', '6618f1fe4bdb3.pdf', 7, '1.webp');

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
(146, 66, 1360),
(147, 67, 2312),
(148, 68, 1367),
(149, 68, 1345),
(151, 69, 1333),
(152, 69, 1359),
(154, 70, 2314),
(155, 70, 2294),
(156, 71, 1357),
(157, 71, 38),
(158, 71, 2884),
(159, 71, 2406),
(160, 71, 2405),
(161, 71, 2404),
(162, 71, 2403),
(163, 71, 2884),
(164, 71, 2884),
(165, 71, 3416),
(166, 71, 3419),
(167, 71, 3418),
(168, 71, 3417),
(169, 71, 3416),
(171, 72, 1377),
(172, 72, 1373),
(173, 72, 1373),
(174, 72, 2339),
(175, 72, 2338),
(176, 72, 2337),
(177, 72, 2336),
(178, 72, 2335),
(179, 72, 2334),
(180, 72, 2333),
(181, 72, 2332),
(182, 72, 2331),
(183, 72, 2330),
(184, 72, 2329),
(185, 72, 2323),
(186, 72, 2322),
(187, 72, 2313),
(188, 72, 2312),
(189, 72, 2311),
(190, 72, 2310),
(191, 72, 2297),
(192, 72, 2296),
(193, 72, 2295),
(194, 72, 2294),
(195, 72, 1376),
(196, 72, 1375),
(197, 72, 1374),
(198, 72, 1373),
(202, 73, 1378),
(203, 73, 2314),
(204, 73, 1379),
(205, 73, 1378),
(206, 73, 1378),
(209, 74, 2340),
(210, 75, 3415),
(211, 75, 3415),
(212, 75, 1358),
(213, 75, 1358),
(217, 76, 1373),
(218, 77, 1329),
(219, 78, 1378),
(220, 79, 1323),
(221, 79, 1323);

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
(35, 66, 42, 1310.00),
(36, 67, 43, 2900.00),
(37, 68, 43, 10410.00),
(38, 69, 44, 1310.00),
(39, 70, 44, 1700.00),
(40, 71, 45, 69027.00),
(41, 72, 45, 142900.00),
(42, 73, 46, 19500.00),
(43, 74, 47, 2300.00),
(44, 75, 48, 6450.00),
(45, 76, 49, 6800.00),
(46, 77, 49, 21000.00),
(47, 78, 51, 3400.00),
(48, 79, 51, 12000.00);

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
(4, 63, 'rented', 'completed', '2024-02-27 10:19:40'),
(5, 64, NULL, NULL, '2024-04-06 10:22:37'),
(6, 65, NULL, 'rented', '2024-02-27 04:59:37'),
(7, 66, 'rented', 'rented', '2024-04-06 11:24:04'),
(8, 67, NULL, 'accepted', '2024-04-13 18:16:45'),
(9, 68, NULL, 'completed', '2024-04-11 08:08:37'),
(10, 69, NULL, 'cancelled', '2024-04-06 10:27:17'),
(11, 70, NULL, 'accepted', '2024-02-27 09:23:24'),
(12, 71, NULL, NULL, '2024-04-05 05:39:54'),
(13, 72, NULL, NULL, '2024-04-05 05:39:54'),
(14, 73, NULL, NULL, '2024-04-09 04:48:55'),
(15, 74, 'rented', 'rented', '2024-04-14 08:05:24'),
(16, 75, 'rented', 'completed', '2024-04-15 15:52:45'),
(17, 76, NULL, NULL, '2024-04-14 10:58:51'),
(18, 77, NULL, NULL, '2024-04-14 10:58:51'),
(19, 78, 'rented', 'accepted', '2024-04-15 15:52:18'),
(20, 79, 'rented', 'completed', '2024-04-15 15:52:49');

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
-- Table structure for table `rent_return_complaints`
--

CREATE TABLE `rent_return_complaints` (
  `id` int NOT NULL,
  `rent_id` int NOT NULL,
  `complains` json NOT NULL,
  `charge` decimal(10,2) NOT NULL,
  `description` text,
  `status` enum('pending','resolved','rejected','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rent_return_complaints`
--

INSERT INTO `rent_return_complaints` (`id`, `rent_id`, `complains`, `charge`, `description`, `status`, `created_at`) VALUES
(3, 68, '[{\"charge\": \"2830\", \"equipment_id\": \"25\", \"complaint_description\": \"Beer - Hyatt\"}, {\"charge\": \"2524\", \"equipment_id\": \"33\", \"complaint_description\": \"Schoen and Sons\"}]', 5354.00, NULL, 'cancelled', '2024-04-11 08:17:39');

--
-- Triggers `rent_return_complaints`
--
DELIMITER $$
CREATE TRIGGER `AfterRentReturnIssueInsert` AFTER INSERT ON `rent_return_complaints` FOR EACH ROW BEGIN
    UPDATE rent
    SET status = 'return_reported'
    WHERE id = NEW.rent_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_complaint_status_update` AFTER UPDATE ON `rent_return_complaints` FOR EACH ROW BEGIN
    IF NEW.status = 'cancelled' THEN
        UPDATE rent
        SET status = 'rented'
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
(182, 'rental1@wl.com', 'AKaIN2jKbPXe112x7OPTug==:ff7c4a9e2f20b620b50b98039d5ad9d8b28412ee4d3e55ec1329585c258456dd', '2024-02-23 14:36:17', 'rentalservice', 1),
(183, 'your.email+fakedata69121@gmail.com', '32vt3Hmtv1z9PONMwa2i/w==:9c03fae8dae6bd21c415ce4cbcb38bfa4b053db81ec1f8c1007f5f13e983796e', '2024-04-12 07:56:10', 'customer', 0),
(184, 'gvhgfhgfn@wl.com', '7eeJQXMYU8e6URwfMtEyXA==:24ae0bcb1a40a7d21ce7fb76695f4f600766efc33beccade9219e8c473bb9fdc', '2024-04-12 08:18:25', 'guide', 0),
(185, 'gvhgfhgfn@wl.com', 'RiqTc+3KLLghxM/nImpuvw==:fa1f5b0a9e6b24953dd1e42967e4b576b1f71f9e68ac094b1103bb5d65eaac14', '2024-04-12 08:19:25', 'guide', 0),
(186, 'guide@wl.com', 'aUyRjiSNKF/j0+hyVKdJOQ==:f1ce816abeb1aed405e4b3ea871d8fd39c2221f5a7968a46397973e0a086b002', '2024-04-12 08:25:33', 'guide', 0),
(187, 'guide@wl.com', 'yf2h2KpUljtl4cuYLUDWoA==:9129b512c68c8c7aa020494b2bbf7c590a9937374952ad705129e26c29042692', '2024-04-12 08:26:47', 'guide', 0),
(188, 'nirmalsavinda29@gmail.com', 'YwTBygsPyWuKYcxhhH2few==:e26d79a1159f24ff1b9d995681c0d8d92bc163dceb423345e25be1d5237b0145', '2024-04-12 08:33:58', 'rentalservice', 0);

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
(37, 182, '9c3853ac31bab21669937bc823a193be9fdd91a54657d4654d55a7451a5cc1e8'),
(38, 183, '972b68f449d92a7ac191e2c5da80ce07b17cc3cdc6a0a8d724dd4805df47f1d2'),
(39, 184, '915049bb93289d163f0c45d2117c70c6b812de13d07e4206cc57e3c72908c082'),
(40, 185, 'e7f8cb3f2d57da1348d4629cff4c3b6d09668f6863bac5c8ddf42b05e71e9b34'),
(41, 186, '35910b32293477e3e0c88e39a07dcb74e74be3e904068470603ea9c9383a5778'),
(42, 187, '1efc80fd832b09b6a13def963cb4dfcedee0fb48bad0e6c9c8df74085de27046'),
(43, 188, '90225ee472fb6633b6d2b2616b63d96ccf0961fa17f21fca772d914166c20d61');

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
-- Indexes for table `rent_return_complaints`
--
ALTER TABLE `rent_return_complaints`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=235;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3865;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `rent`
--
ALTER TABLE `rent`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `rental_services`
--
ALTER TABLE `rental_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `rent_item`
--
ALTER TABLE `rent_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `rent_pay`
--
ALTER TABLE `rent_pay`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `rent_request`
--
ALTER TABLE `rent_request`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `rent_return_complaints`
--
ALTER TABLE `rent_return_complaints`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tips`
--
ALTER TABLE `tips`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT for table `verification`
--
ALTER TABLE `verification`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
