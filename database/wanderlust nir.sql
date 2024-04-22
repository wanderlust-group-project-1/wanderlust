-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Apr 22, 2024 at 01:53 PM
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

CREATE DEFINER=`root`@`%` PROCEDURE `GetCurrentAcceptedRents` (IN `equipmentID` INT)   BEGIN
    SELECT 
        r.id AS RentID, 
        r.customer_id AS CustomerID, 
        r.start_date AS StartDate, 
        r.end_date AS EndDate, 
        r.status AS Status
    FROM 
        rent r
    JOIN 
        rent_item ri ON r.id = ri.rent_id
    JOIN 
        item i ON ri.item_id = i.id
    WHERE 
        i.equipment_id = equipmentID AND 
        (r.status = 'accepted' OR r.status = 'rented') AND
        r.end_date > CURRENT_DATE()
    ORDER BY 
        r.start_date ASC;
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

CREATE DEFINER=`root`@`%` PROCEDURE `GetFirstUpcomingRent` (IN `customer_id` INT)   BEGIN
    SELECT 
        r.id AS rent_id,
        r.start_date,
        r.end_date,
        e.image AS equipment_image,
        rs.name AS rental_service_name,
        rs.address AS rental_service_address,
        rs.mobile AS rental_service_mobile,
        rs.image AS rental_service_image
    FROM rent r
    JOIN rent_item ri ON r.id = ri.rent_id
    JOIN item i ON ri.item_id = i.id
    JOIN equipment e ON i.equipment_id = e.id
    JOIN rental_services rs ON e.rentalservice_id = rs.id
    WHERE r.customer_id = customer_id
      AND r.start_date > CURRENT_DATE()
    ORDER BY r.start_date ASC
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetFirstUpcomingRentByRental` (IN `rentalservice_id` INT)   BEGIN
    SELECT 
        r.id AS rent_id,
        r.start_date,
        r.end_date,
        e.image AS equipment_image,
        c.name AS customer_name
        
 
    FROM rent r
    JOIN rent_item ri ON r.id = ri.rent_id
    JOIN item i ON ri.item_id = i.id
    JOIN equipment e ON i.equipment_id = e.id
    JOIN customers c ON r.customer_id = c.id
    WHERE r.rentalservice_id = rentalservice_id
      AND r.start_date >= CURRENT_DATE()
      AND r.status = 'accepted'
    ORDER BY r.start_date ASC
    LIMIT 1;
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

CREATE DEFINER=`root`@`%` PROCEDURE `GetMonthlyIncome` (IN `service_id` INT, IN `start_date` DATE, IN `end_date` DATE)   BEGIN
    SELECT 
        DATE_FORMAT(p.datetime, '%Y-%m') AS `Month`,
        SUM(p.amount) AS `MonthlyIncome`
    FROM 
        `payment` p
    JOIN 
        `rent_pay` rp ON p.id = rp.payment_id
    JOIN 
        `rent` r ON rp.rent_id = r.id
    WHERE 
        r.rentalservice_id = service_id
        AND p.status = 'completed'
        AND p.datetime BETWEEN start_date AND end_date
    GROUP BY 
        DATE_FORMAT(p.datetime, '%Y-%m')
    ORDER BY 
        DATE_FORMAT(p.datetime, '%Y-%m');
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
        AND r.status IN ('completed') -- Assuming you want to count items that were rented and those that completed the rental term
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
(43, 25, '2024-02-23', '2024-02-29'),
(95, 32, '2024-04-23', '2024-04-30');

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
(93, 40, 38),
(253, 95, 2317);

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
  `user_id` int DEFAULT NULL,
  `image` varchar(100) NOT NULL DEFAULT '1.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `address`, `number`, `nic`, `user_id`, `image`) VALUES
(1, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '4534646t435', '329473802343', NULL, '1.jpg'),
(2, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '239423423432', '235345345325', NULL, '1.jpg'),
(3, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '123124234', '3534534532', NULL, '1.jpg'),
(4, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '123124234', '3534534532', NULL, '1.jpg'),
(5, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '234423423', '32423053432', NULL, '1.jpg'),
(6, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '32354543', 'w309340324', 38, '1.jpg'),
(7, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '479238203', '43342834834', 39, '1.jpg'),
(8, 'd', 'fdede', 'fadeded', 'fedfef', 40, '1.jpg'),
(9, 'H W Nirmal Savinda', 'No 255 Neluwa Rd', '32535345', '4354354', 41, '1.jpg'),
(10, 'nsadsd', 'No 255, Neluwa RD', '32434', '2434234', 42, '1.jpg'),
(11, 'wqewe', 'fdes@s.com', 'dfsdf', 'dsfdf', 43, '1.jpg'),
(12, 'Arya', 'Colombo', '0716024489', '200177901838', 45, '1.jpg'),
(13, 'Nirmal', 'COlombo', '0716024489', '20011783929', 46, '1.jpg'),
(14, 'Nirmal', 'Colombo', '0716024489', '200117901838', 47, '1.jpg'),
(15, 'Admin', 'COlombo', '0716024489', '200117901838', 48, '1.jpg'),
(16, 'Savinda', 'colombo', '0713056777', '200117901838', 49, '1.jpg'),
(17, 'Nirmal savi', ' Colombo', '076024481', '200117901811', 74, '1.jpg'),
(18, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 75, '1.jpg'),
(19, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 76, '1.jpg'),
(20, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 77, '1.jpg'),
(21, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 78, '1.jpg'),
(22, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 79, '1.jpg'),
(23, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 80, '1.jpg'),
(24, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 81, '1.jpg'),
(25, 'Nirmal Savinda', 'No 255 Neluwa Rd\r\nGorakaduwa', '076024489', '20011786323', 82, '1.jpg'),
(26, 'nirmal', 'Address is required', '0713458323', '200156273849', 84, '1.jpg'),
(27, 'Nirmal', '  Colombo', '0716024489', '200118603720', 85, '1.jpg'),
(28, 'Nirmal', '  Colombo', '0716024489', '200118603720', 86, '1.jpg'),
(29, 'Nirmal', '  Colombo', '0716024489', '200118603720', 88, '1.jpg'),
(30, 'Nirmal', '  Colombo', '0716024489', '200118603720', 89, '1.jpg'),
(31, 'Nirmal', '  Colombo', '0716024489', '200118603720', 90, '1.jpg'),
(32, 'Customer ', ' Colombo 5', '+94716024499', '200117293604', 107, '6624855a66e48.jpg'),
(33, 'Nirmal', '  Colombo', '0716024489', '200118603720', 153, '1.jpg'),
(34, 'Nirmal', '  Colombo', '0716024489', '200118603720', 155, '1.jpg'),
(35, 'Nirmal', 'No 255, Neluwa RD\nGorakaduwa', '+94716024489', '200117829352', 167, '1.jpg'),
(36, 'Anderson Runte', '52556 Amara Mill', '+94716024489', '200176539077', 183, '1.jpg'),
(37, 'Brody Leannon', '1670 Effie Port', '+94726439870', '200117901838', 189, '1.jpg'),
(38, 'Nirmal', 'ABC', '+94726439870', '200117901838', 190, '1.jpg'),
(39, 'Nola Senger', '6451 Weber Island', '0948209393', '200117901838', 194, '1.jpg'),
(40, 'Nola Senger', '6451 Weber Island', '0948209393', '20011790183', 195, '1.jpg'),
(41, 'Nola Senger', '6451 Weber Island', '0948209393', '200117901Z', 196, '1.jpg'),
(42, 'Nola Senger', '6451 Weber Island', '0948209393', '200117901V', 197, '1.jpg'),
(43, 'Nola Senger', '6451 Weber Island', '0948209393', '200117901X', 198, '1.jpg'),
(44, 'Nola Senger', '6451 Weber Island', '0948209393', '200117901x', 199, '1.jpg'),
(45, 'Gabriella DuBuque', '35408 Hodkiewicz Roads', '0948209392', '20011790183', 200, '1.jpg'),
(46, 'Gabriella DuBuque', '35408 Hodkiewicz Roads', '0948209392', '20011790183', 201, '1.jpg'),
(47, 'Sylvester Walter', '583 Jacobs Fall', '0786579984', '200976880974', 210, '1.jpg'),
(48, 'Otho McKenzie', '118 Gust Parkway', '0786579984', '200976880974', 211, '1.jpg'),
(49, 'Eino Vandervort', '6951 Moriah Dam', '0786579984', '200976880970', 214, '1.jpg');

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
  `standard_fee` decimal(8,2) NOT NULL,
  `image` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `rentalservice_id`, `name`, `cost`, `description`, `type`, `count`, `fee`, `standard_fee`, `image`) VALUES
(25, 25, 'Tent - 2 Persons', 3040.00, 'Tent for 2 Persons', 'Tent', 3, 1000.00, 0.00, '65b365fccf6dc.jpg'),
(35, 25, 'Hiking Backpack', 14000.00, 'Backpack for hiking', 'Backpack', 2, 1000.00, 0.00, '65b3685fa38ae.jpg'),
(37, 25, 'Tent ABC', 13000.00, 'Tent for 4 ', 'Tent', 0, 1500.00, 0.00, '65bcb96e5870c.jpg'),
(38, 25, 'Abbot Jimenez', 85.00, 'Ea eiusmod id asper', 'Cooking', 70, 83.00, 0.00, '65bcc5d7c9299.jpg'),
(39, 25, 'Abbot Jimenez', 85.00, 'Ea eiusmod id asper', 'Cooking', 70, 83.00, 0.00, '65bcc5db96eb1.jpg'),
(41, 25, 'Baker Mueller', 69.00, 'Labore quis est veni', 'Footwear', 34, 6.00, 0.00, '65bcc65dcc3bf.jpg'),
(42, 25, 'Baker Mueller', 69.00, 'Labore quis est veni', 'Footwear', 34, 6.00, 0.00, '65bcc674ecbcb.jpg'),
(43, 25, 'BackPack - 80L', 25000.00, 'Black', 'Backpack', 4, 1200.00, 300.00, '65c38635992f2.jpg'),
(46, 25, 'ABC', 606.00, 'Excepturi voluptates tenetur sit incidunt.', 'Clothing', 10, 408.00, 363.00, '65d57b5ec9974.jpg'),
(47, 25, 'ABC', 606.00, 'Excepturi voluptates tenetur sit incidunt.', 'Clothing', 6, 408.00, 363.00, '65d57c6ec9297.jpg'),
(48, 25, 'ABC', 606.00, 'Excepturi voluptates tenetur sit incidunt.', 'Clothing', 6, 408.00, 363.00, '65d57d2f9de66.jpg'),
(53, 56, 'BBQ Grill', 5600.00, 'Large            ', 'Tent', 48, 300.00, 500.00, '65d8ae9491e5c.webp'),
(61, 56, 'Cooking Set', 11000.00, '5', 'Cooking', 11, 500.00, 400.00, '65d8b04792064.webp'),
(69, 25, 'Clare Ritchie', 74.00, 'Illum dolorem quas.', 'Footwear', 0, 6.00, 225.00, '65e0417c00298.jpg'),
(70, 25, 'Carlie Shields', 243.00, 'Beatae voluptatem maiores minus vel mollitia repellat quibusdam sint.', 'Cooking', 0, 606.00, 34.00, '65e041b26e300.png'),
(71, 25, 'Juana Barrows', 294.00, 'Ipsum pariatur dolores aliquam aspernatur doloremque sequi.', 'Cooking', 0, 200.00, 517.00, '65e0665e2b2bf.jpg'),
(72, 25, 'Dena Hirthe', 373.00, 'Nesciunt aspernatur aliquam.', 'Climbing', 0, 379.00, 96.00, ''),
(73, 25, 'Dane Schuster', 31.00, 'Sequi tempora consequatur explicabo maiores magni numquam adipisci.', 'Climbing', 0, 503.00, 655.00, ''),
(74, 25, 'Evangeline Vandervort', 357.00, 'Dolor eveniet ratione dolore fugiat.', 'Climbing', 0, 315.00, 249.00, '661cf80214b6c.png'),
(75, 25, 'Allie Schneider', 78.00, 'Molestias sapiente sunt pariatur consectetur soluta accusamus laudantium ut.', 'Cooking', 0, 999.00, 1000.00, '6625f90b7dc73.png'),
(76, 25, 'Easton Keefe', 184929.00, 'In temporibus unde nihil magnam.', 'Backpack', 0, 5714.00, 13535.99, '662602bbd1e12.png'),
(77, 25, 'Hiking Backpack (80L)', 17000.00, '80L waterproof Outdoor Sport Travel Camping Hiking Trekking Backpack, Capacity : 80 litre', 'Backpack', 5, 300.00, 600.00, '6626266196d59.png'),
(78, 25, 'Zempire Mono Hiking Tent', 25000.00, 'A soaringly waterproof floor, unbendable pegs, ripstop fly and tough zippers all come together to make the Zempire Mono a tent you can rely on for any lightweight adventure.', 'Tent', 7, 400.00, 1000.00, '662627a4b4829.jpeg'),
(79, 25, 'Camping Cookware Set', 11500.00, 'Camping Outdoor Cookware Set with cutlery, Package Size : 19 x 13 x 19 cm, Cook pot, pot lid, kettle, frying pan, carabiner, foldable fork', 'Cooking', 3, 400.00, 600.00, '66262824dc83c.jpeg');

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
(47, 'HDUFIFISF', 'No 255, Neluwa RD', '098790987654', '+94716024489', 'male', 187, 'waiting', '', 6),
(48, 'Braxton Rogahn', '368 Janice Ranch', '200976880974', '0983237761', 'male', 202, 'waiting', '6621e64a26927.pdf', 8),
(49, 'Terence Shields', '60304 Hills Forges', '200976880974', '0983237761', 'other', 203, 'waiting', '', 9),
(50, 'Gardner Feest', '18723 Buckridge Orchard', '200976880974', '0983237761', 'other', 206, 'waiting', '6621e963a0a7c.pdf', 10),
(51, 'Webster King', '53994 Dayna Estate', '200976880974', '0983237761', 'female', 207, 'waiting', '6621ea01ed5a8.pdf', 11),
(52, 'Wendy Waelchi', '15847 Kilback Cove', '200976880972', '0983237767', 'male', 215, 'waiting', '66237b795c7f4.pdf', 15);

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
(1311, 37, 'I000371584', 'removed'),
(1312, 37, 'I000378307', 'removed'),
(1313, 37, 'I000371419', 'removed'),
(1314, 37, 'I000377135', 'removed'),
(1315, 37, 'I000373418', 'removed'),
(1316, 37, 'I000373686', 'removed'),
(1317, 37, 'I000377175', 'removed'),
(1318, 37, 'I000371715', 'removed'),
(1319, 37, 'I000373619', 'removed'),
(1320, 37, 'I000372949', 'removed'),
(1321, 37, 'I000372887', 'removed'),
(1322, 37, 'I000374590', 'removed'),
(1323, 35, 'I000357657', 'available'),
(1324, 35, 'I000358215', 'unavailable'),
(1325, 35, 'I000359871', 'available'),
(1326, 35, 'I000356790', 'unavailable'),
(1327, 35, 'I000358809', 'unavailable'),
(1328, 35, 'I000352302', 'unavailable'),
(1329, 25, 'I000251527', 'unavailable'),
(1330, 25, 'I000259566', 'available'),
(1331, 25, 'I000254803', 'unavailable'),
(1332, 25, 'I000252679', 'unavailable'),
(1333, 25, 'I000254617', 'unavailable'),
(1334, 25, 'I000254975', 'unavailable'),
(1335, 25, 'I000259610', 'unavailable'),
(1336, 25, 'I000257921', 'unavailable'),
(1337, 25, 'I000254915', 'unavailable'),
(1338, 25, 'I000257653', 'unavailable'),
(1339, 25, 'I000254522', 'unavailable'),
(1340, 25, 'I000252431', 'unavailable'),
(1341, 25, 'I000254972', 'unavailable'),
(1342, 25, 'I000257569', 'unavailable'),
(1343, 25, 'I000258541', 'unavailable'),
(1344, 25, 'I000256111', 'unavailable'),
(1345, 25, 'I000254121', 'unavailable'),
(1346, 25, 'I000257307', 'unavailable'),
(1347, 25, 'I000258676', 'unavailable'),
(1348, 25, 'I000255603', 'unavailable'),
(1349, 25, 'I000253347', 'unavailable'),
(1350, 25, 'I000259992', 'unavailable'),
(1351, 25, 'I000252917', 'unavailable'),
(1352, 25, 'I000251613', 'unavailable'),
(1353, 25, 'I000253669', 'unavailable'),
(1354, 25, 'I000257983', 'unavailable'),
(1355, 25, 'I000259911', 'unavailable'),
(1356, 25, 'I000256605', 'unavailable'),
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
(2363, 25, 'I000254127', 'unavailable'),
(2364, 25, 'I000251213', 'unavailable'),
(2365, 25, 'I000251684', 'unavailable'),
(2366, 25, 'I000253782', 'unavailable'),
(2367, 25, 'I000255308', 'unavailable'),
(2368, 25, 'I000255387', 'unavailable'),
(2369, 25, 'I000251010', 'unavailable'),
(2370, 25, 'I000255893', 'unavailable'),
(2371, 25, 'I000259264', 'unavailable'),
(2372, 25, 'I000251527', 'unavailable'),
(2373, 25, 'I000257485', 'unavailable'),
(2374, 25, 'I000255991', 'unavailable'),
(2375, 25, 'I000255328', 'unavailable'),
(2376, 25, 'I000258003', 'unavailable'),
(2377, 25, 'I000255033', 'unavailable'),
(2378, 25, 'I000259158', 'unavailable'),
(2379, 25, 'I000256687', 'unavailable'),
(2380, 25, 'I000256731', 'unavailable'),
(2381, 25, 'I000253595', 'unavailable'),
(2382, 25, 'I000255783', 'unavailable'),
(2383, 25, 'I000256134', 'unavailable'),
(2384, 25, 'I000253262', 'unavailable'),
(2385, 25, 'I000252370', 'unavailable'),
(2386, 25, 'I000255696', 'unavailable'),
(2387, 25, 'I000251351', 'unavailable'),
(2388, 25, 'I000254600', 'unavailable'),
(2389, 25, 'I000257730', 'unavailable'),
(2390, 25, 'I000254416', 'unavailable'),
(2391, 25, 'I000259096', 'unavailable'),
(2392, 25, 'I000251520', 'unavailable'),
(2393, 25, 'I000253024', 'unavailable'),
(2394, 25, 'I000252414', 'unavailable'),
(2395, 25, 'I000258449', 'unavailable'),
(2396, 25, 'I000255517', 'unavailable'),
(2397, 25, 'I000256277', 'unavailable'),
(2398, 25, 'I000257239', 'available'),
(2399, 25, 'I000252274', 'unavailable'),
(2400, 25, 'I000254029', 'unavailable'),
(2401, 25, 'I000258539', 'unavailable'),
(2402, 25, 'I000255130', 'available'),
(2403, 69, 'I000699704', 'unavailable'),
(2404, 69, 'I000694389', 'unavailable'),
(2405, 69, 'I000697225', 'unavailable'),
(2406, 69, 'I000699952', 'unavailable'),
(2407, 69, 'I000699238', 'unavailable'),
(2408, 69, 'I000697129', 'unavailable'),
(2409, 69, 'I000699481', 'unavailable'),
(2410, 69, 'I000691144', 'unavailable'),
(2411, 69, 'I000691578', 'unavailable'),
(2412, 69, 'I000693869', 'unavailable'),
(2413, 69, 'I000697473', 'unavailable'),
(2414, 69, 'I000691332', 'unavailable'),
(2415, 69, 'I000695235', 'unavailable'),
(2416, 69, 'I000698646', 'unavailable'),
(2417, 69, 'I000692329', 'unavailable'),
(2418, 69, 'I000694618', 'unavailable'),
(2419, 69, 'I000695949', 'unavailable'),
(2420, 69, 'I000691648', 'unavailable'),
(2421, 69, 'I000696940', 'unavailable'),
(2422, 69, 'I000692195', 'unavailable'),
(2423, 69, 'I000698421', 'unavailable'),
(2424, 69, 'I000697420', 'unavailable'),
(2425, 69, 'I000694355', 'unavailable'),
(2426, 69, 'I000699139', 'unavailable'),
(2427, 69, 'I000691617', 'unavailable'),
(2428, 69, 'I000694654', 'unavailable'),
(2429, 69, 'I000694036', 'unavailable'),
(2430, 69, 'I000699969', 'unavailable'),
(2431, 69, 'I000692267', 'unavailable'),
(2432, 69, 'I000699525', 'unavailable'),
(2433, 69, 'I000699749', 'unavailable'),
(2434, 69, 'I000692684', 'unavailable'),
(2435, 69, 'I000691417', 'unavailable'),
(2436, 69, 'I000694934', 'unavailable'),
(2437, 69, 'I000692174', 'unavailable'),
(2438, 69, 'I000698070', 'unavailable'),
(2439, 69, 'I000699933', 'unavailable'),
(2440, 69, 'I000691269', 'unavailable'),
(2441, 69, 'I000698887', 'unavailable'),
(2442, 69, 'I000696607', 'unavailable'),
(2443, 69, 'I000692727', 'unavailable'),
(2444, 69, 'I000693524', 'unavailable'),
(2445, 69, 'I000697395', 'unavailable'),
(2446, 69, 'I000696442', 'unavailable'),
(2447, 69, 'I000695752', 'unavailable'),
(2448, 69, 'I000691223', 'unavailable'),
(2449, 69, 'I000692376', 'unavailable'),
(2450, 69, 'I000692055', 'unavailable'),
(2451, 69, 'I000693566', 'unavailable'),
(2452, 69, 'I000698184', 'unavailable'),
(2453, 69, 'I000691319', 'unavailable'),
(2454, 69, 'I000691507', 'unavailable'),
(2455, 69, 'I000698191', 'unavailable'),
(2456, 69, 'I000699238', 'unavailable'),
(2457, 69, 'I000694971', 'unavailable'),
(2458, 69, 'I000694491', 'unavailable'),
(2459, 69, 'I000695562', 'unavailable'),
(2460, 69, 'I000697920', 'unavailable'),
(2461, 69, 'I000695330', 'unavailable'),
(2462, 69, 'I000696914', 'unavailable'),
(2463, 69, 'I000697500', 'unavailable'),
(2464, 69, 'I000697521', 'unavailable'),
(2465, 69, 'I000699041', 'unavailable'),
(2466, 69, 'I000699015', 'unavailable'),
(2467, 69, 'I000693826', 'unavailable'),
(2468, 69, 'I000694470', 'unavailable'),
(2469, 69, 'I000699403', 'unavailable'),
(2470, 69, 'I000697346', 'unavailable'),
(2471, 69, 'I000694867', 'unavailable'),
(2472, 69, 'I000693269', 'unavailable'),
(2473, 69, 'I000694222', 'unavailable'),
(2474, 69, 'I000699707', 'unavailable'),
(2475, 69, 'I000696355', 'unavailable'),
(2476, 69, 'I000691301', 'unavailable'),
(2477, 69, 'I000697431', 'unavailable'),
(2478, 69, 'I000697396', 'unavailable'),
(2479, 69, 'I000695565', 'unavailable'),
(2480, 69, 'I000697072', 'unavailable'),
(2481, 69, 'I000697201', 'unavailable'),
(2482, 69, 'I000695087', 'unavailable'),
(2483, 69, 'I000698758', 'unavailable'),
(2484, 69, 'I000697532', 'unavailable'),
(2485, 69, 'I000695190', 'unavailable'),
(2486, 69, 'I000697744', 'unavailable'),
(2487, 69, 'I000695335', 'unavailable'),
(2488, 69, 'I000698578', 'unavailable'),
(2489, 69, 'I000691878', 'unavailable'),
(2490, 69, 'I000697387', 'unavailable'),
(2491, 69, 'I000691375', 'unavailable'),
(2492, 69, 'I000698553', 'unavailable'),
(2493, 69, 'I000691042', 'unavailable'),
(2494, 69, 'I000699729', 'unavailable'),
(2495, 69, 'I000698382', 'unavailable'),
(2496, 69, 'I000693621', 'unavailable'),
(2497, 69, 'I000696270', 'unavailable'),
(2498, 69, 'I000699447', 'unavailable'),
(2499, 69, 'I000699987', 'unavailable'),
(2500, 69, 'I000699639', 'unavailable'),
(2501, 69, 'I000697179', 'unavailable'),
(2502, 69, 'I000698561', 'unavailable'),
(2503, 69, 'I000692960', 'unavailable'),
(2504, 69, 'I000691098', 'unavailable'),
(2505, 69, 'I000697144', 'unavailable'),
(2506, 69, 'I000693764', 'unavailable'),
(2507, 69, 'I000692137', 'unavailable'),
(2508, 69, 'I000691646', 'unavailable'),
(2509, 69, 'I000696959', 'unavailable'),
(2510, 69, 'I000694316', 'unavailable'),
(2511, 69, 'I000695292', 'unavailable'),
(2512, 69, 'I000692620', 'unavailable'),
(2513, 69, 'I000696056', 'unavailable'),
(2514, 69, 'I000695408', 'unavailable'),
(2515, 69, 'I000697152', 'unavailable'),
(2516, 69, 'I000691132', 'unavailable'),
(2517, 69, 'I000697254', 'unavailable'),
(2518, 69, 'I000692059', 'unavailable'),
(2519, 69, 'I000693020', 'unavailable'),
(2520, 69, 'I000695010', 'unavailable'),
(2521, 69, 'I000697186', 'unavailable'),
(2522, 69, 'I000693625', 'unavailable'),
(2523, 69, 'I000696370', 'unavailable'),
(2524, 69, 'I000699587', 'unavailable'),
(2525, 69, 'I000699986', 'unavailable'),
(2526, 69, 'I000696632', 'unavailable'),
(2527, 69, 'I000691118', 'unavailable'),
(2528, 69, 'I000693199', 'unavailable'),
(2529, 69, 'I000699640', 'unavailable'),
(2530, 69, 'I000695286', 'unavailable'),
(2531, 69, 'I000698593', 'unavailable'),
(2532, 69, 'I000692606', 'unavailable'),
(2533, 69, 'I000693094', 'unavailable'),
(2534, 69, 'I000693238', 'unavailable'),
(2535, 69, 'I000691601', 'unavailable'),
(2536, 69, 'I000695180', 'unavailable'),
(2537, 69, 'I000698121', 'unavailable'),
(2538, 69, 'I000699113', 'unavailable'),
(2539, 69, 'I000693239', 'unavailable'),
(2540, 69, 'I000698688', 'unavailable'),
(2541, 69, 'I000693087', 'unavailable'),
(2542, 69, 'I000699629', 'unavailable'),
(2543, 69, 'I000694341', 'unavailable'),
(2544, 69, 'I000696998', 'unavailable'),
(2545, 69, 'I000696056', 'unavailable'),
(2546, 69, 'I000699262', 'unavailable'),
(2547, 69, 'I000696640', 'unavailable'),
(2548, 69, 'I000698983', 'unavailable'),
(2549, 69, 'I000695091', 'unavailable'),
(2550, 69, 'I000699059', 'unavailable'),
(2551, 69, 'I000692466', 'unavailable'),
(2552, 69, 'I000693498', 'unavailable'),
(2553, 69, 'I000691444', 'unavailable'),
(2554, 69, 'I000697130', 'unavailable'),
(2555, 69, 'I000697649', 'unavailable'),
(2556, 69, 'I000692340', 'unavailable'),
(2557, 69, 'I000694789', 'unavailable'),
(2558, 69, 'I000699101', 'unavailable'),
(2559, 69, 'I000695996', 'unavailable'),
(2560, 69, 'I000697181', 'unavailable'),
(2561, 69, 'I000693651', 'unavailable'),
(2562, 69, 'I000696912', 'unavailable'),
(2563, 69, 'I000691642', 'unavailable'),
(2564, 69, 'I000697163', 'unavailable'),
(2565, 69, 'I000696500', 'unavailable'),
(2566, 69, 'I000697198', 'unavailable'),
(2567, 69, 'I000698444', 'unavailable'),
(2568, 69, 'I000697821', 'unavailable'),
(2569, 69, 'I000692768', 'unavailable'),
(2570, 69, 'I000692381', 'unavailable'),
(2571, 69, 'I000694502', 'unavailable'),
(2572, 69, 'I000693280', 'unavailable'),
(2573, 69, 'I000695970', 'unavailable'),
(2574, 69, 'I000695092', 'unavailable'),
(2575, 69, 'I000699191', 'unavailable'),
(2576, 69, 'I000694970', 'unavailable'),
(2577, 69, 'I000694882', 'unavailable'),
(2578, 69, 'I000695196', 'unavailable'),
(2579, 69, 'I000692961', 'unavailable'),
(2580, 69, 'I000692662', 'unavailable'),
(2581, 69, 'I000698108', 'unavailable'),
(2582, 69, 'I000696274', 'unavailable'),
(2583, 69, 'I000693397', 'unavailable'),
(2584, 69, 'I000691244', 'unavailable'),
(2585, 69, 'I000694245', 'unavailable'),
(2586, 69, 'I000698629', 'unavailable'),
(2587, 69, 'I000691926', 'unavailable'),
(2588, 69, 'I000696102', 'unavailable'),
(2589, 69, 'I000691776', 'unavailable'),
(2590, 69, 'I000697991', 'unavailable'),
(2591, 69, 'I000693027', 'unavailable'),
(2592, 69, 'I000695770', 'unavailable'),
(2593, 69, 'I000693565', 'unavailable'),
(2594, 69, 'I000698976', 'unavailable'),
(2595, 69, 'I000695631', 'unavailable'),
(2596, 69, 'I000691376', 'unavailable'),
(2597, 69, 'I000694639', 'unavailable'),
(2598, 69, 'I000695931', 'unavailable'),
(2599, 69, 'I000691107', 'unavailable'),
(2600, 69, 'I000695820', 'unavailable'),
(2601, 69, 'I000693798', 'unavailable'),
(2602, 69, 'I000693950', 'unavailable'),
(2603, 69, 'I000697046', 'unavailable'),
(2604, 69, 'I000691488', 'unavailable'),
(2605, 69, 'I000693029', 'unavailable'),
(2606, 69, 'I000691329', 'unavailable'),
(2607, 69, 'I000699174', 'unavailable'),
(2608, 69, 'I000692516', 'unavailable'),
(2609, 69, 'I000693418', 'unavailable'),
(2610, 69, 'I000698472', 'unavailable'),
(2611, 69, 'I000696382', 'unavailable'),
(2612, 69, 'I000692188', 'unavailable'),
(2613, 69, 'I000694299', 'unavailable'),
(2614, 69, 'I000691157', 'unavailable'),
(2615, 69, 'I000693476', 'unavailable'),
(2616, 69, 'I000693790', 'unavailable'),
(2617, 69, 'I000696912', 'unavailable'),
(2618, 69, 'I000699388', 'unavailable'),
(2619, 69, 'I000691340', 'unavailable'),
(2620, 69, 'I000696147', 'unavailable'),
(2621, 69, 'I000697902', 'unavailable'),
(2622, 69, 'I000696979', 'unavailable'),
(2623, 69, 'I000699652', 'unavailable'),
(2624, 69, 'I000695748', 'unavailable'),
(2625, 69, 'I000696519', 'unavailable'),
(2626, 69, 'I000692310', 'unavailable'),
(2627, 69, 'I000695389', 'unavailable'),
(2628, 69, 'I000692765', 'unavailable'),
(2629, 69, 'I000696202', 'unavailable'),
(2630, 69, 'I000695135', 'unavailable'),
(2631, 69, 'I000697922', 'unavailable'),
(2632, 69, 'I000697476', 'unavailable'),
(2633, 69, 'I000699918', 'unavailable'),
(2634, 69, 'I000696330', 'unavailable'),
(2635, 69, 'I000696181', 'unavailable'),
(2636, 69, 'I000698633', 'unavailable'),
(2637, 69, 'I000692582', 'unavailable'),
(2638, 69, 'I000699528', 'unavailable'),
(2639, 69, 'I000696972', 'unavailable'),
(2640, 69, 'I000693889', 'unavailable'),
(2641, 69, 'I000694320', 'unavailable'),
(2642, 69, 'I000694253', 'unavailable'),
(2643, 69, 'I000698099', 'unavailable'),
(2644, 69, 'I000699776', 'unavailable'),
(2645, 69, 'I000697034', 'unavailable'),
(2646, 69, 'I000692831', 'unavailable'),
(2647, 69, 'I000691657', 'unavailable'),
(2648, 69, 'I000698172', 'unavailable'),
(2649, 69, 'I000697527', 'unavailable'),
(2650, 69, 'I000698635', 'unavailable'),
(2651, 69, 'I000698499', 'unavailable'),
(2652, 69, 'I000694946', 'unavailable'),
(2653, 69, 'I000691644', 'unavailable'),
(2654, 69, 'I000696906', 'unavailable'),
(2655, 69, 'I000691586', 'unavailable'),
(2656, 69, 'I000696156', 'unavailable'),
(2657, 69, 'I000695862', 'unavailable'),
(2658, 69, 'I000694152', 'unavailable'),
(2659, 69, 'I000691305', 'unavailable'),
(2660, 69, 'I000697596', 'unavailable'),
(2661, 69, 'I000698759', 'unavailable'),
(2662, 69, 'I000691235', 'unavailable'),
(2663, 69, 'I000699803', 'unavailable'),
(2664, 69, 'I000696314', 'unavailable'),
(2665, 69, 'I000696906', 'unavailable'),
(2666, 69, 'I000694572', 'unavailable'),
(2667, 69, 'I000698879', 'unavailable'),
(2668, 69, 'I000693720', 'unavailable'),
(2669, 69, 'I000691937', 'unavailable'),
(2670, 69, 'I000695503', 'unavailable'),
(2671, 69, 'I000698292', 'unavailable'),
(2672, 69, 'I000692191', 'unavailable'),
(2673, 69, 'I000695185', 'unavailable'),
(2674, 69, 'I000698959', 'unavailable'),
(2675, 69, 'I000698723', 'unavailable'),
(2676, 69, 'I000696291', 'unavailable'),
(2677, 69, 'I000697192', 'unavailable'),
(2678, 69, 'I000691201', 'unavailable'),
(2679, 69, 'I000695258', 'unavailable'),
(2680, 69, 'I000691274', 'unavailable'),
(2681, 69, 'I000696054', 'unavailable'),
(2682, 69, 'I000696741', 'unavailable'),
(2683, 69, 'I000693759', 'unavailable'),
(2684, 69, 'I000693924', 'unavailable'),
(2685, 69, 'I000696900', 'unavailable'),
(2686, 69, 'I000697954', 'unavailable'),
(2687, 69, 'I000691196', 'unavailable'),
(2688, 69, 'I000697304', 'unavailable'),
(2689, 69, 'I000694197', 'unavailable'),
(2690, 69, 'I000697180', 'unavailable'),
(2691, 69, 'I000691811', 'unavailable'),
(2692, 69, 'I000697965', 'unavailable'),
(2693, 69, 'I000692214', 'unavailable'),
(2694, 69, 'I000698558', 'unavailable'),
(2695, 69, 'I000699837', 'unavailable'),
(2696, 69, 'I000694066', 'unavailable'),
(2697, 69, 'I000698897', 'unavailable'),
(2698, 69, 'I000698623', 'unavailable'),
(2699, 69, 'I000694929', 'unavailable'),
(2700, 69, 'I000699221', 'unavailable'),
(2701, 69, 'I000697010', 'unavailable'),
(2702, 69, 'I000697364', 'unavailable'),
(2703, 69, 'I000697009', 'unavailable'),
(2704, 69, 'I000696073', 'unavailable'),
(2705, 69, 'I000699478', 'unavailable'),
(2706, 69, 'I000691593', 'unavailable'),
(2707, 69, 'I000691132', 'unavailable'),
(2708, 69, 'I000699338', 'unavailable'),
(2709, 69, 'I000697413', 'unavailable'),
(2710, 69, 'I000694974', 'unavailable'),
(2711, 69, 'I000692959', 'unavailable'),
(2712, 69, 'I000694636', 'unavailable'),
(2713, 69, 'I000699615', 'unavailable'),
(2714, 69, 'I000698131', 'unavailable'),
(2715, 69, 'I000697133', 'unavailable'),
(2716, 69, 'I000691511', 'unavailable'),
(2717, 69, 'I000692688', 'unavailable'),
(2718, 69, 'I000693646', 'unavailable'),
(2719, 69, 'I000692255', 'unavailable'),
(2720, 69, 'I000693200', 'unavailable'),
(2721, 69, 'I000696683', 'unavailable'),
(2722, 69, 'I000691344', 'unavailable'),
(2723, 69, 'I000693556', 'unavailable'),
(2724, 69, 'I000693652', 'unavailable'),
(2725, 69, 'I000693544', 'unavailable'),
(2726, 69, 'I000699639', 'unavailable'),
(2727, 69, 'I000699420', 'unavailable'),
(2728, 69, 'I000694404', 'unavailable'),
(2729, 69, 'I000691059', 'unavailable'),
(2730, 69, 'I000699657', 'unavailable'),
(2731, 69, 'I000695135', 'unavailable'),
(2732, 69, 'I000693815', 'unavailable'),
(2733, 69, 'I000691450', 'unavailable'),
(2734, 69, 'I000694383', 'unavailable'),
(2735, 69, 'I000699408', 'unavailable'),
(2736, 69, 'I000696475', 'unavailable'),
(2737, 69, 'I000694531', 'unavailable'),
(2738, 69, 'I000692986', 'unavailable'),
(2739, 69, 'I000699123', 'unavailable'),
(2740, 69, 'I000694635', 'unavailable'),
(2741, 69, 'I000698125', 'unavailable'),
(2742, 69, 'I000696841', 'unavailable'),
(2743, 69, 'I000695033', 'unavailable'),
(2744, 69, 'I000699232', 'unavailable'),
(2745, 69, 'I000696286', 'unavailable'),
(2746, 69, 'I000692514', 'unavailable'),
(2747, 69, 'I000692369', 'unavailable'),
(2748, 69, 'I000696553', 'unavailable'),
(2749, 69, 'I000696367', 'unavailable'),
(2750, 69, 'I000691161', 'unavailable'),
(2751, 69, 'I000692923', 'unavailable'),
(2752, 69, 'I000692264', 'unavailable'),
(2753, 69, 'I000699882', 'unavailable'),
(2754, 69, 'I000697560', 'unavailable'),
(2755, 69, 'I000692876', 'unavailable'),
(2756, 69, 'I000696778', 'unavailable'),
(2757, 69, 'I000696074', 'unavailable'),
(2758, 69, 'I000692578', 'unavailable'),
(2759, 69, 'I000695480', 'unavailable'),
(2760, 69, 'I000692103', 'unavailable'),
(2761, 69, 'I000691802', 'unavailable'),
(2762, 69, 'I000698491', 'unavailable'),
(2763, 69, 'I000693280', 'unavailable'),
(2764, 69, 'I000696068', 'unavailable'),
(2765, 69, 'I000696946', 'unavailable'),
(2766, 69, 'I000697528', 'unavailable'),
(2767, 69, 'I000691685', 'unavailable'),
(2768, 69, 'I000692438', 'unavailable'),
(2769, 69, 'I000696337', 'unavailable'),
(2770, 69, 'I000691624', 'unavailable'),
(2771, 69, 'I000693003', 'unavailable'),
(2772, 69, 'I000698613', 'unavailable'),
(2773, 69, 'I000693788', 'unavailable'),
(2774, 69, 'I000695624', 'unavailable'),
(2775, 69, 'I000692944', 'unavailable'),
(2776, 69, 'I000697482', 'unavailable'),
(2777, 69, 'I000699846', 'unavailable'),
(2778, 69, 'I000694910', 'unavailable'),
(2779, 69, 'I000696854', 'unavailable'),
(2780, 69, 'I000697200', 'unavailable'),
(2781, 69, 'I000695294', 'unavailable'),
(2782, 69, 'I000691955', 'unavailable'),
(2783, 69, 'I000699671', 'unavailable'),
(2784, 69, 'I000696328', 'unavailable'),
(2785, 69, 'I000698372', 'unavailable'),
(2786, 69, 'I000695292', 'unavailable'),
(2787, 69, 'I000699369', 'unavailable'),
(2788, 69, 'I000696559', 'unavailable'),
(2789, 69, 'I000696472', 'unavailable'),
(2790, 69, 'I000693113', 'unavailable'),
(2791, 69, 'I000693069', 'unavailable'),
(2792, 69, 'I000695462', 'unavailable'),
(2793, 69, 'I000696328', 'unavailable'),
(2794, 69, 'I000691252', 'unavailable'),
(2795, 69, 'I000695000', 'unavailable'),
(2796, 69, 'I000694900', 'unavailable'),
(2797, 69, 'I000695285', 'unavailable'),
(2798, 69, 'I000694491', 'unavailable'),
(2799, 69, 'I000693938', 'unavailable'),
(2800, 69, 'I000699572', 'unavailable'),
(2801, 69, 'I000694603', 'unavailable'),
(2802, 69, 'I000693480', 'unavailable'),
(2803, 69, 'I000698865', 'unavailable'),
(2804, 69, 'I000694281', 'unavailable'),
(2805, 69, 'I000693736', 'unavailable'),
(2806, 69, 'I000697620', 'unavailable'),
(2807, 69, 'I000695329', 'unavailable'),
(2808, 69, 'I000697841', 'unavailable'),
(2809, 69, 'I000694188', 'unavailable'),
(2810, 69, 'I000692173', 'unavailable'),
(2811, 69, 'I000695771', 'unavailable'),
(2812, 69, 'I000692256', 'unavailable'),
(2813, 69, 'I000692107', 'unavailable'),
(2814, 69, 'I000693893', 'unavailable'),
(2815, 69, 'I000693174', 'unavailable'),
(2816, 69, 'I000699436', 'unavailable'),
(2817, 69, 'I000698900', 'unavailable'),
(2818, 69, 'I000695350', 'unavailable'),
(2819, 69, 'I000695728', 'unavailable'),
(2820, 69, 'I000698847', 'unavailable'),
(2821, 69, 'I000691711', 'unavailable'),
(2822, 69, 'I000699062', 'unavailable'),
(2823, 69, 'I000695945', 'unavailable'),
(2824, 69, 'I000696577', 'unavailable'),
(2825, 69, 'I000691621', 'unavailable'),
(2826, 69, 'I000699784', 'unavailable'),
(2827, 69, 'I000692481', 'unavailable'),
(2828, 69, 'I000696248', 'unavailable'),
(2829, 69, 'I000698563', 'unavailable'),
(2830, 69, 'I000699630', 'unavailable'),
(2831, 69, 'I000699082', 'unavailable'),
(2832, 69, 'I000697066', 'unavailable'),
(2833, 69, 'I000699685', 'unavailable'),
(2834, 69, 'I000695587', 'unavailable'),
(2835, 69, 'I000693169', 'unavailable'),
(2836, 69, 'I000693097', 'unavailable'),
(2837, 69, 'I000695486', 'unavailable'),
(2838, 69, 'I000696536', 'unavailable'),
(2839, 69, 'I000694984', 'unavailable'),
(2840, 69, 'I000692038', 'unavailable'),
(2841, 69, 'I000695050', 'unavailable'),
(2842, 69, 'I000697777', 'unavailable'),
(2843, 69, 'I000695478', 'unavailable'),
(2844, 69, 'I000699271', 'unavailable'),
(2845, 69, 'I000693010', 'unavailable'),
(2846, 69, 'I000692923', 'unavailable'),
(2847, 69, 'I000693626', 'unavailable'),
(2848, 69, 'I000698749', 'unavailable'),
(2849, 69, 'I000699767', 'unavailable'),
(2850, 69, 'I000699999', 'unavailable'),
(2851, 69, 'I000695249', 'unavailable'),
(2852, 69, 'I000692907', 'unavailable'),
(2853, 69, 'I000691170', 'unavailable'),
(2854, 69, 'I000697232', 'unavailable'),
(2855, 69, 'I000693059', 'unavailable'),
(2856, 69, 'I000694209', 'unavailable'),
(2857, 69, 'I000699609', 'unavailable'),
(2858, 69, 'I000692869', 'unavailable'),
(2859, 69, 'I000696191', 'unavailable'),
(2860, 69, 'I000693993', 'unavailable'),
(2861, 69, 'I000694312', 'unavailable'),
(2862, 69, 'I000693174', 'unavailable'),
(2863, 69, 'I000695661', 'unavailable'),
(2864, 69, 'I000699174', 'unavailable'),
(2865, 69, 'I000698534', 'unavailable'),
(2866, 69, 'I000699753', 'unavailable'),
(2867, 69, 'I000692667', 'unavailable'),
(2868, 69, 'I000691077', 'unavailable'),
(2869, 69, 'I000697632', 'unavailable'),
(2870, 69, 'I000692494', 'unavailable'),
(2871, 69, 'I000697872', 'unavailable'),
(2872, 69, 'I000693287', 'unavailable'),
(2873, 69, 'I000692786', 'unavailable'),
(2874, 69, 'I000697528', 'unavailable'),
(2875, 69, 'I000693136', 'unavailable'),
(2876, 69, 'I000692996', 'unavailable'),
(2877, 69, 'I000693871', 'unavailable'),
(2878, 69, 'I000693751', 'unavailable'),
(2879, 69, 'I000692605', 'unavailable'),
(2880, 69, 'I000698905', 'unavailable'),
(2881, 69, 'I000693919', 'unavailable'),
(2882, 69, 'I000695496', 'unavailable'),
(2883, 69, 'I000698332', 'unavailable'),
(2884, 70, 'I000708501', 'unavailable'),
(2885, 70, 'I000709513', 'unavailable'),
(2886, 70, 'I000709138', 'unavailable'),
(2887, 70, 'I000701919', 'unavailable'),
(2888, 70, 'I000703142', 'unavailable'),
(2889, 70, 'I000701341', 'unavailable'),
(2890, 70, 'I000701106', 'unavailable'),
(2891, 70, 'I000703860', 'unavailable'),
(2892, 70, 'I000704309', 'unavailable'),
(2893, 70, 'I000706041', 'unavailable'),
(2894, 70, 'I000707903', 'unavailable'),
(2895, 70, 'I000709707', 'unavailable'),
(2896, 70, 'I000709213', 'unavailable'),
(2897, 70, 'I000701606', 'unavailable'),
(2898, 70, 'I000705504', 'unavailable'),
(2899, 70, 'I000704040', 'unavailable'),
(2900, 70, 'I000704136', 'unavailable'),
(2901, 70, 'I000701134', 'unavailable'),
(2902, 70, 'I000706233', 'unavailable'),
(2903, 70, 'I000704134', 'unavailable'),
(2904, 70, 'I000708368', 'unavailable'),
(2905, 70, 'I000708704', 'unavailable'),
(2906, 70, 'I000703965', 'unavailable'),
(2907, 70, 'I000709916', 'unavailable'),
(2908, 70, 'I000703624', 'unavailable'),
(2909, 70, 'I000701537', 'unavailable'),
(2910, 70, 'I000709493', 'unavailable'),
(2911, 70, 'I000708455', 'unavailable'),
(2912, 70, 'I000707117', 'unavailable'),
(2913, 70, 'I000709041', 'unavailable'),
(2914, 70, 'I000703641', 'unavailable'),
(2915, 70, 'I000709716', 'unavailable'),
(2916, 70, 'I000702746', 'unavailable'),
(2917, 70, 'I000704645', 'unavailable'),
(2918, 70, 'I000704451', 'unavailable'),
(2919, 70, 'I000701672', 'unavailable'),
(2920, 70, 'I000704633', 'unavailable'),
(2921, 70, 'I000704886', 'unavailable'),
(2922, 70, 'I000701052', 'unavailable'),
(2923, 70, 'I000706925', 'unavailable'),
(2924, 70, 'I000709016', 'unavailable'),
(2925, 70, 'I000704899', 'unavailable'),
(2926, 70, 'I000706842', 'unavailable'),
(2927, 70, 'I000708814', 'unavailable'),
(2928, 70, 'I000701814', 'unavailable'),
(2929, 70, 'I000704052', 'unavailable'),
(2930, 70, 'I000701566', 'unavailable'),
(2931, 70, 'I000706203', 'unavailable'),
(2932, 70, 'I000708668', 'unavailable'),
(2933, 70, 'I000704782', 'unavailable'),
(2934, 70, 'I000707759', 'unavailable'),
(2935, 70, 'I000709297', 'unavailable'),
(2936, 70, 'I000702729', 'unavailable'),
(2937, 70, 'I000708087', 'unavailable'),
(2938, 70, 'I000707011', 'unavailable'),
(2939, 70, 'I000701331', 'unavailable'),
(2940, 70, 'I000702115', 'unavailable'),
(2941, 70, 'I000705122', 'unavailable'),
(2942, 70, 'I000701199', 'unavailable'),
(2943, 70, 'I000707588', 'unavailable'),
(2944, 70, 'I000702299', 'unavailable'),
(2945, 70, 'I000706295', 'unavailable'),
(2946, 70, 'I000703828', 'unavailable'),
(2947, 70, 'I000701993', 'unavailable'),
(2948, 70, 'I000707331', 'unavailable'),
(2949, 70, 'I000708270', 'unavailable'),
(2950, 70, 'I000705060', 'unavailable'),
(2951, 70, 'I000707615', 'unavailable'),
(2952, 70, 'I000704063', 'unavailable'),
(2953, 70, 'I000703573', 'unavailable'),
(2954, 70, 'I000708238', 'unavailable'),
(2955, 70, 'I000702274', 'unavailable'),
(2956, 70, 'I000708975', 'unavailable'),
(2957, 70, 'I000709965', 'unavailable'),
(2958, 70, 'I000704478', 'unavailable'),
(2959, 70, 'I000708311', 'unavailable'),
(2960, 70, 'I000704521', 'unavailable'),
(2961, 70, 'I000706017', 'unavailable'),
(2962, 70, 'I000705540', 'unavailable'),
(2963, 70, 'I000701024', 'unavailable'),
(2964, 70, 'I000709510', 'unavailable'),
(2965, 70, 'I000704386', 'unavailable'),
(2966, 70, 'I000701665', 'unavailable'),
(2967, 70, 'I000705064', 'unavailable'),
(2968, 70, 'I000704156', 'unavailable'),
(2969, 70, 'I000701300', 'unavailable'),
(2970, 70, 'I000709215', 'unavailable'),
(2971, 70, 'I000705303', 'unavailable'),
(2972, 70, 'I000703732', 'unavailable'),
(2973, 70, 'I000709089', 'unavailable'),
(2974, 70, 'I000708021', 'unavailable'),
(2975, 70, 'I000702514', 'unavailable'),
(2976, 70, 'I000706485', 'unavailable'),
(2977, 70, 'I000705505', 'unavailable'),
(2978, 70, 'I000705135', 'unavailable'),
(2979, 70, 'I000706930', 'unavailable'),
(2980, 70, 'I000708365', 'unavailable'),
(2981, 70, 'I000701481', 'unavailable'),
(2982, 70, 'I000701761', 'unavailable'),
(2983, 70, 'I000706039', 'unavailable'),
(2984, 70, 'I000703818', 'unavailable'),
(2985, 70, 'I000704092', 'unavailable'),
(2986, 70, 'I000702295', 'unavailable'),
(2987, 70, 'I000701703', 'unavailable'),
(2988, 70, 'I000706611', 'unavailable'),
(2989, 70, 'I000706199', 'unavailable'),
(2990, 70, 'I000708190', 'unavailable'),
(2991, 70, 'I000704742', 'unavailable'),
(2992, 70, 'I000702302', 'unavailable'),
(2993, 70, 'I000704977', 'unavailable'),
(2994, 70, 'I000705752', 'unavailable'),
(2995, 70, 'I000705405', 'unavailable'),
(2996, 70, 'I000709846', 'unavailable'),
(2997, 70, 'I000707349', 'unavailable'),
(2998, 70, 'I000709926', 'unavailable'),
(2999, 70, 'I000709369', 'unavailable'),
(3000, 70, 'I000702588', 'unavailable'),
(3001, 70, 'I000704769', 'unavailable'),
(3002, 70, 'I000708223', 'unavailable'),
(3003, 70, 'I000709235', 'unavailable'),
(3004, 70, 'I000706363', 'unavailable'),
(3005, 70, 'I000705704', 'unavailable'),
(3006, 70, 'I000701026', 'unavailable'),
(3007, 70, 'I000708071', 'unavailable'),
(3008, 70, 'I000704024', 'unavailable'),
(3009, 70, 'I000707732', 'unavailable'),
(3010, 70, 'I000706598', 'unavailable'),
(3011, 70, 'I000705698', 'unavailable'),
(3012, 70, 'I000707795', 'unavailable'),
(3013, 70, 'I000701740', 'unavailable'),
(3014, 70, 'I000702821', 'unavailable'),
(3015, 70, 'I000705857', 'unavailable'),
(3016, 70, 'I000708803', 'unavailable'),
(3017, 70, 'I000701453', 'unavailable'),
(3018, 70, 'I000706302', 'unavailable'),
(3019, 70, 'I000702574', 'unavailable'),
(3020, 70, 'I000705102', 'unavailable'),
(3021, 70, 'I000705687', 'unavailable'),
(3022, 70, 'I000703056', 'unavailable'),
(3023, 70, 'I000701791', 'unavailable'),
(3024, 70, 'I000701436', 'unavailable'),
(3025, 70, 'I000706086', 'unavailable'),
(3026, 70, 'I000707613', 'unavailable'),
(3027, 70, 'I000703785', 'unavailable'),
(3028, 70, 'I000707321', 'unavailable'),
(3029, 70, 'I000709941', 'unavailable'),
(3030, 70, 'I000709912', 'unavailable'),
(3031, 70, 'I000703766', 'unavailable'),
(3032, 70, 'I000704264', 'unavailable'),
(3033, 70, 'I000706040', 'unavailable'),
(3034, 70, 'I000708287', 'unavailable'),
(3035, 70, 'I000708015', 'unavailable'),
(3036, 70, 'I000709605', 'unavailable'),
(3037, 70, 'I000706278', 'unavailable'),
(3038, 70, 'I000709757', 'unavailable'),
(3039, 70, 'I000706795', 'unavailable'),
(3040, 70, 'I000709196', 'unavailable'),
(3041, 70, 'I000709490', 'unavailable'),
(3042, 70, 'I000702463', 'unavailable'),
(3043, 70, 'I000701305', 'unavailable'),
(3044, 70, 'I000706545', 'unavailable'),
(3045, 70, 'I000708524', 'unavailable'),
(3046, 70, 'I000707961', 'unavailable'),
(3047, 70, 'I000706461', 'unavailable'),
(3048, 70, 'I000701018', 'unavailable'),
(3049, 70, 'I000701113', 'unavailable'),
(3050, 70, 'I000706894', 'unavailable'),
(3051, 70, 'I000707885', 'unavailable'),
(3052, 70, 'I000701683', 'unavailable'),
(3053, 70, 'I000709925', 'unavailable'),
(3054, 70, 'I000706345', 'unavailable'),
(3055, 70, 'I000708141', 'unavailable'),
(3056, 70, 'I000702495', 'unavailable'),
(3057, 70, 'I000705037', 'unavailable'),
(3058, 70, 'I000705171', 'unavailable'),
(3059, 70, 'I000702835', 'unavailable'),
(3060, 70, 'I000702244', 'unavailable'),
(3061, 70, 'I000703035', 'unavailable'),
(3062, 70, 'I000705382', 'unavailable'),
(3063, 70, 'I000706858', 'unavailable'),
(3064, 70, 'I000704778', 'unavailable'),
(3065, 70, 'I000705972', 'unavailable'),
(3066, 70, 'I000702383', 'unavailable'),
(3067, 70, 'I000704440', 'unavailable'),
(3068, 70, 'I000703825', 'unavailable'),
(3069, 70, 'I000709632', 'unavailable'),
(3070, 70, 'I000708491', 'unavailable'),
(3071, 70, 'I000706550', 'unavailable'),
(3072, 70, 'I000706948', 'unavailable'),
(3073, 70, 'I000704967', 'unavailable'),
(3074, 70, 'I000708762', 'unavailable'),
(3075, 70, 'I000708684', 'unavailable'),
(3076, 70, 'I000709047', 'unavailable'),
(3077, 70, 'I000707649', 'unavailable'),
(3078, 70, 'I000704815', 'unavailable'),
(3079, 70, 'I000708565', 'unavailable'),
(3080, 70, 'I000706318', 'unavailable'),
(3081, 70, 'I000701871', 'unavailable'),
(3082, 70, 'I000709605', 'unavailable'),
(3083, 70, 'I000707564', 'unavailable'),
(3084, 70, 'I000707179', 'unavailable'),
(3085, 70, 'I000709233', 'unavailable'),
(3086, 70, 'I000706742', 'unavailable'),
(3087, 70, 'I000703915', 'unavailable'),
(3088, 70, 'I000708609', 'unavailable'),
(3089, 70, 'I000706248', 'unavailable'),
(3090, 70, 'I000709145', 'unavailable'),
(3091, 70, 'I000704441', 'unavailable'),
(3092, 70, 'I000705990', 'unavailable'),
(3093, 70, 'I000705754', 'unavailable'),
(3094, 70, 'I000705327', 'unavailable'),
(3095, 70, 'I000708342', 'unavailable'),
(3096, 70, 'I000706475', 'unavailable'),
(3097, 70, 'I000706852', 'unavailable'),
(3098, 70, 'I000703828', 'unavailable'),
(3099, 70, 'I000705974', 'unavailable'),
(3100, 70, 'I000701977', 'unavailable'),
(3101, 70, 'I000705710', 'unavailable'),
(3102, 70, 'I000704608', 'unavailable'),
(3103, 70, 'I000704067', 'unavailable'),
(3104, 70, 'I000703712', 'unavailable'),
(3105, 70, 'I000701850', 'unavailable'),
(3106, 70, 'I000705653', 'unavailable'),
(3107, 70, 'I000704663', 'unavailable'),
(3108, 70, 'I000705641', 'unavailable'),
(3109, 70, 'I000706126', 'unavailable'),
(3110, 70, 'I000706540', 'unavailable'),
(3111, 70, 'I000701034', 'unavailable'),
(3112, 70, 'I000709692', 'unavailable'),
(3113, 70, 'I000707767', 'unavailable'),
(3114, 70, 'I000701261', 'unavailable'),
(3115, 70, 'I000702969', 'unavailable'),
(3116, 70, 'I000703979', 'unavailable'),
(3117, 70, 'I000707789', 'unavailable'),
(3118, 70, 'I000708089', 'unavailable'),
(3119, 70, 'I000706498', 'unavailable'),
(3120, 70, 'I000709583', 'unavailable'),
(3121, 70, 'I000701499', 'unavailable'),
(3122, 70, 'I000709159', 'unavailable'),
(3123, 70, 'I000702434', 'unavailable'),
(3124, 70, 'I000703736', 'unavailable'),
(3125, 70, 'I000707994', 'unavailable'),
(3126, 70, 'I000705178', 'unavailable'),
(3127, 70, 'I000703354', 'unavailable'),
(3128, 70, 'I000703027', 'unavailable'),
(3129, 70, 'I000704241', 'unavailable'),
(3130, 70, 'I000703255', 'unavailable'),
(3131, 70, 'I000709593', 'unavailable'),
(3132, 70, 'I000703887', 'unavailable'),
(3133, 70, 'I000709435', 'unavailable'),
(3134, 70, 'I000704184', 'unavailable'),
(3135, 70, 'I000707494', 'unavailable'),
(3136, 70, 'I000709641', 'unavailable'),
(3137, 70, 'I000702651', 'unavailable'),
(3138, 70, 'I000703507', 'unavailable'),
(3139, 70, 'I000701682', 'unavailable'),
(3140, 70, 'I000704317', 'unavailable'),
(3141, 70, 'I000707802', 'unavailable'),
(3142, 70, 'I000705288', 'unavailable'),
(3143, 70, 'I000707163', 'unavailable'),
(3144, 70, 'I000702419', 'unavailable'),
(3145, 70, 'I000703414', 'unavailable'),
(3146, 70, 'I000702005', 'unavailable'),
(3147, 70, 'I000702285', 'unavailable'),
(3148, 70, 'I000702282', 'unavailable'),
(3149, 70, 'I000708698', 'unavailable'),
(3150, 70, 'I000704635', 'unavailable'),
(3151, 70, 'I000704691', 'unavailable'),
(3152, 70, 'I000706326', 'unavailable'),
(3153, 70, 'I000709884', 'unavailable'),
(3154, 70, 'I000708778', 'unavailable'),
(3155, 70, 'I000709735', 'unavailable'),
(3156, 70, 'I000701883', 'unavailable'),
(3157, 70, 'I000706080', 'unavailable'),
(3158, 70, 'I000703477', 'unavailable'),
(3159, 70, 'I000706261', 'unavailable'),
(3160, 70, 'I000701577', 'unavailable'),
(3161, 70, 'I000704521', 'unavailable'),
(3162, 70, 'I000703627', 'unavailable'),
(3163, 70, 'I000705142', 'unavailable'),
(3164, 70, 'I000705959', 'unavailable'),
(3165, 70, 'I000707850', 'unavailable'),
(3166, 70, 'I000709444', 'unavailable'),
(3167, 70, 'I000704700', 'unavailable'),
(3168, 70, 'I000708215', 'unavailable'),
(3169, 70, 'I000709703', 'unavailable'),
(3170, 70, 'I000706330', 'unavailable'),
(3171, 70, 'I000709864', 'unavailable'),
(3172, 70, 'I000708347', 'unavailable'),
(3173, 70, 'I000701737', 'unavailable'),
(3174, 70, 'I000706793', 'unavailable'),
(3175, 70, 'I000709341', 'unavailable'),
(3176, 70, 'I000703262', 'unavailable'),
(3177, 70, 'I000708817', 'unavailable'),
(3178, 70, 'I000707071', 'unavailable'),
(3179, 70, 'I000704908', 'unavailable'),
(3180, 70, 'I000706537', 'unavailable'),
(3181, 70, 'I000702947', 'unavailable'),
(3182, 70, 'I000701067', 'unavailable'),
(3183, 70, 'I000706656', 'unavailable'),
(3184, 70, 'I000703786', 'unavailable'),
(3185, 70, 'I000706943', 'unavailable'),
(3186, 70, 'I000702748', 'unavailable'),
(3187, 70, 'I000702641', 'unavailable'),
(3188, 70, 'I000702358', 'unavailable'),
(3189, 70, 'I000706953', 'unavailable'),
(3190, 70, 'I000706791', 'unavailable'),
(3191, 70, 'I000707360', 'unavailable'),
(3192, 70, 'I000701635', 'unavailable'),
(3193, 70, 'I000704738', 'unavailable'),
(3194, 70, 'I000707225', 'unavailable'),
(3195, 70, 'I000708547', 'unavailable'),
(3196, 70, 'I000702281', 'unavailable'),
(3197, 70, 'I000706330', 'unavailable'),
(3198, 70, 'I000704648', 'unavailable'),
(3199, 70, 'I000708255', 'unavailable'),
(3200, 70, 'I000701935', 'unavailable'),
(3201, 70, 'I000702382', 'unavailable'),
(3202, 70, 'I000704759', 'unavailable'),
(3203, 70, 'I000709892', 'unavailable'),
(3204, 70, 'I000705031', 'unavailable'),
(3205, 70, 'I000701474', 'unavailable'),
(3206, 70, 'I000704647', 'unavailable'),
(3207, 70, 'I000706943', 'unavailable'),
(3208, 70, 'I000704009', 'unavailable'),
(3209, 70, 'I000708590', 'unavailable'),
(3210, 70, 'I000705039', 'unavailable'),
(3211, 70, 'I000704352', 'unavailable'),
(3212, 70, 'I000705252', 'unavailable'),
(3213, 70, 'I000701531', 'unavailable'),
(3214, 70, 'I000703734', 'unavailable'),
(3215, 70, 'I000707073', 'unavailable'),
(3216, 70, 'I000705695', 'unavailable'),
(3217, 70, 'I000703471', 'unavailable'),
(3218, 70, 'I000709596', 'unavailable'),
(3219, 70, 'I000702004', 'unavailable'),
(3220, 70, 'I000709633', 'unavailable'),
(3221, 70, 'I000706891', 'unavailable'),
(3222, 70, 'I000703159', 'unavailable'),
(3223, 70, 'I000701902', 'unavailable'),
(3224, 70, 'I000702625', 'unavailable'),
(3225, 70, 'I000709663', 'unavailable'),
(3226, 70, 'I000701894', 'unavailable'),
(3227, 70, 'I000706931', 'unavailable'),
(3228, 70, 'I000701549', 'unavailable'),
(3229, 70, 'I000706966', 'unavailable'),
(3230, 70, 'I000705333', 'unavailable'),
(3231, 70, 'I000706553', 'unavailable'),
(3232, 70, 'I000708410', 'unavailable'),
(3233, 70, 'I000705735', 'unavailable'),
(3234, 70, 'I000701177', 'unavailable'),
(3235, 70, 'I000701664', 'unavailable'),
(3236, 70, 'I000706932', 'unavailable'),
(3237, 70, 'I000703640', 'unavailable'),
(3238, 70, 'I000701690', 'unavailable'),
(3239, 70, 'I000703265', 'unavailable'),
(3240, 70, 'I000705729', 'unavailable'),
(3241, 70, 'I000702381', 'unavailable'),
(3242, 70, 'I000706595', 'unavailable'),
(3243, 70, 'I000708794', 'unavailable'),
(3244, 70, 'I000703635', 'unavailable'),
(3245, 70, 'I000702828', 'unavailable'),
(3246, 70, 'I000706928', 'unavailable'),
(3247, 70, 'I000702094', 'unavailable'),
(3248, 70, 'I000708348', 'unavailable'),
(3249, 70, 'I000708675', 'unavailable'),
(3250, 70, 'I000703604', 'unavailable'),
(3251, 70, 'I000703312', 'unavailable'),
(3252, 70, 'I000707409', 'unavailable'),
(3253, 70, 'I000706879', 'unavailable'),
(3254, 70, 'I000704927', 'unavailable'),
(3255, 70, 'I000708612', 'unavailable'),
(3256, 70, 'I000706206', 'unavailable'),
(3257, 70, 'I000708319', 'unavailable'),
(3258, 70, 'I000707127', 'unavailable'),
(3259, 70, 'I000701444', 'unavailable'),
(3260, 70, 'I000703207', 'unavailable'),
(3261, 70, 'I000706843', 'unavailable'),
(3262, 70, 'I000706074', 'unavailable'),
(3263, 70, 'I000703728', 'unavailable'),
(3264, 70, 'I000702077', 'unavailable'),
(3265, 70, 'I000709827', 'unavailable'),
(3266, 70, 'I000709936', 'unavailable'),
(3267, 70, 'I000705254', 'unavailable'),
(3268, 70, 'I000705312', 'unavailable'),
(3269, 70, 'I000703554', 'unavailable'),
(3270, 70, 'I000705634', 'unavailable'),
(3271, 70, 'I000706980', 'unavailable'),
(3272, 70, 'I000704817', 'unavailable'),
(3273, 70, 'I000705619', 'unavailable'),
(3274, 70, 'I000704330', 'unavailable'),
(3275, 70, 'I000708600', 'unavailable'),
(3276, 70, 'I000702349', 'unavailable'),
(3277, 70, 'I000707151', 'unavailable'),
(3278, 70, 'I000701635', 'unavailable'),
(3279, 70, 'I000706625', 'unavailable'),
(3280, 70, 'I000707064', 'unavailable'),
(3281, 70, 'I000706513', 'unavailable'),
(3282, 70, 'I000701089', 'unavailable'),
(3283, 70, 'I000701979', 'unavailable'),
(3284, 70, 'I000704716', 'unavailable'),
(3285, 70, 'I000703708', 'unavailable'),
(3286, 70, 'I000704929', 'unavailable'),
(3287, 70, 'I000701191', 'unavailable'),
(3288, 70, 'I000709182', 'unavailable'),
(3289, 70, 'I000705654', 'unavailable'),
(3290, 70, 'I000701702', 'unavailable'),
(3291, 70, 'I000703373', 'unavailable'),
(3292, 70, 'I000707674', 'unavailable'),
(3293, 70, 'I000702363', 'unavailable'),
(3294, 70, 'I000706745', 'unavailable'),
(3295, 70, 'I000702800', 'unavailable'),
(3296, 70, 'I000705409', 'unavailable'),
(3297, 70, 'I000709481', 'unavailable'),
(3298, 70, 'I000709367', 'unavailable'),
(3299, 70, 'I000706559', 'unavailable'),
(3300, 70, 'I000708237', 'unavailable'),
(3301, 70, 'I000702374', 'unavailable'),
(3302, 70, 'I000701888', 'unavailable'),
(3303, 70, 'I000709416', 'unavailable'),
(3304, 70, 'I000703827', 'unavailable'),
(3305, 70, 'I000709874', 'unavailable'),
(3306, 70, 'I000704073', 'unavailable'),
(3307, 70, 'I000701444', 'unavailable'),
(3308, 70, 'I000703696', 'unavailable'),
(3309, 70, 'I000707143', 'unavailable'),
(3310, 70, 'I000707365', 'unavailable'),
(3311, 70, 'I000707559', 'unavailable'),
(3312, 70, 'I000709241', 'unavailable'),
(3313, 70, 'I000703473', 'unavailable'),
(3314, 70, 'I000704724', 'unavailable'),
(3315, 70, 'I000708407', 'unavailable'),
(3316, 70, 'I000704140', 'unavailable'),
(3317, 70, 'I000706898', 'unavailable'),
(3318, 70, 'I000707717', 'unavailable'),
(3319, 70, 'I000707456', 'unavailable'),
(3320, 70, 'I000704132', 'unavailable'),
(3321, 70, 'I000706304', 'unavailable'),
(3322, 70, 'I000705508', 'unavailable'),
(3323, 70, 'I000706825', 'unavailable'),
(3324, 70, 'I000703022', 'unavailable'),
(3325, 70, 'I000708898', 'unavailable'),
(3326, 70, 'I000701784', 'unavailable'),
(3327, 70, 'I000702730', 'unavailable'),
(3328, 70, 'I000703722', 'unavailable'),
(3329, 70, 'I000705071', 'unavailable'),
(3330, 70, 'I000703758', 'unavailable'),
(3331, 70, 'I000702676', 'unavailable'),
(3332, 70, 'I000702931', 'unavailable'),
(3333, 70, 'I000708527', 'unavailable'),
(3334, 70, 'I000704537', 'unavailable'),
(3335, 70, 'I000704370', 'unavailable'),
(3336, 70, 'I000701112', 'unavailable'),
(3337, 70, 'I000703719', 'unavailable'),
(3338, 70, 'I000701556', 'unavailable'),
(3339, 70, 'I000709008', 'unavailable'),
(3340, 70, 'I000708065', 'unavailable'),
(3341, 70, 'I000704069', 'unavailable'),
(3342, 70, 'I000708646', 'unavailable'),
(3343, 70, 'I000705566', 'unavailable'),
(3344, 70, 'I000704856', 'unavailable'),
(3345, 70, 'I000701078', 'unavailable'),
(3346, 70, 'I000707686', 'unavailable'),
(3347, 70, 'I000705254', 'unavailable'),
(3348, 70, 'I000707571', 'unavailable'),
(3349, 70, 'I000701474', 'unavailable'),
(3350, 70, 'I000708776', 'unavailable'),
(3351, 70, 'I000704390', 'unavailable'),
(3352, 70, 'I000708340', 'unavailable'),
(3353, 70, 'I000705865', 'unavailable'),
(3354, 70, 'I000704288', 'unavailable'),
(3355, 70, 'I000705603', 'unavailable'),
(3356, 70, 'I000708774', 'unavailable'),
(3357, 70, 'I000703195', 'unavailable'),
(3358, 70, 'I000703654', 'unavailable'),
(3359, 70, 'I000708584', 'unavailable'),
(3360, 70, 'I000708123', 'unavailable'),
(3361, 70, 'I000708995', 'unavailable'),
(3362, 70, 'I000702706', 'unavailable'),
(3363, 70, 'I000701378', 'unavailable'),
(3364, 70, 'I000705742', 'unavailable'),
(3365, 70, 'I000701600', 'unavailable'),
(3366, 70, 'I000709635', 'unavailable'),
(3367, 70, 'I000701577', 'unavailable'),
(3368, 70, 'I000701162', 'unavailable'),
(3369, 70, 'I000702799', 'unavailable'),
(3370, 70, 'I000701637', 'unavailable'),
(3371, 70, 'I000709825', 'unavailable'),
(3372, 70, 'I000704847', 'unavailable'),
(3373, 70, 'I000707580', 'unavailable'),
(3374, 70, 'I000705316', 'unavailable'),
(3375, 70, 'I000709697', 'unavailable'),
(3376, 70, 'I000705305', 'unavailable'),
(3377, 70, 'I000708441', 'unavailable'),
(3378, 70, 'I000708540', 'unavailable'),
(3379, 70, 'I000709213', 'unavailable'),
(3380, 70, 'I000703094', 'unavailable'),
(3381, 70, 'I000706790', 'unavailable'),
(3382, 70, 'I000706066', 'unavailable'),
(3383, 70, 'I000704064', 'unavailable'),
(3384, 70, 'I000708538', 'unavailable'),
(3385, 70, 'I000703560', 'unavailable'),
(3386, 70, 'I000703714', 'unavailable'),
(3387, 70, 'I000706439', 'unavailable'),
(3388, 70, 'I000702834', 'unavailable'),
(3389, 70, 'I000708800', 'unavailable'),
(3390, 70, 'I000701608', 'unavailable'),
(3391, 70, 'I000709258', 'unavailable'),
(3392, 70, 'I000703354', 'unavailable'),
(3393, 70, 'I000702322', 'unavailable'),
(3394, 70, 'I000708838', 'unavailable'),
(3395, 70, 'I000706931', 'unavailable'),
(3396, 70, 'I000702066', 'unavailable'),
(3397, 70, 'I000707794', 'unavailable'),
(3398, 70, 'I000707402', 'unavailable'),
(3399, 70, 'I000702405', 'unavailable'),
(3400, 70, 'I000709982', 'unavailable'),
(3401, 70, 'I000709930', 'unavailable'),
(3402, 70, 'I000703741', 'unavailable'),
(3403, 70, 'I000703615', 'unavailable'),
(3404, 70, 'I000702928', 'unavailable'),
(3405, 70, 'I000709022', 'unavailable'),
(3406, 70, 'I000704965', 'unavailable'),
(3407, 70, 'I000708062', 'unavailable'),
(3408, 70, 'I000704409', 'unavailable'),
(3409, 70, 'I000706115', 'unavailable'),
(3410, 70, 'I000708421', 'unavailable'),
(3411, 70, 'I000709874', 'unavailable'),
(3412, 70, 'I000707731', 'unavailable'),
(3413, 70, 'I000707942', 'unavailable'),
(3414, 25, 'I000253375', 'unavailable'),
(3416, 71, 'I000718340', 'removed'),
(3417, 71, 'I000718947', 'removed'),
(3418, 71, 'I000717105', 'removed'),
(3419, 71, 'I000712654', 'removed'),
(3420, 71, 'I000716640', 'removed'),
(3421, 71, 'I000715450', 'removed'),
(3422, 71, 'I000719954', 'removed'),
(3423, 71, 'I000717256', 'removed'),
(3424, 71, 'I000715441', 'removed'),
(3425, 71, 'I000714257', 'removed'),
(3426, 71, 'I000717076', 'removed'),
(3427, 71, 'I000718304', 'removed'),
(3428, 71, 'I000716256', 'removed'),
(3429, 71, 'I000711311', 'removed'),
(3430, 71, 'I000716778', 'removed'),
(3431, 71, 'I000712148', 'removed'),
(3432, 71, 'I000718982', 'removed'),
(3433, 71, 'I000719499', 'removed'),
(3434, 71, 'I000712807', 'removed'),
(3435, 71, 'I000716019', 'removed'),
(3436, 71, 'I000711261', 'removed'),
(3437, 71, 'I000717967', 'removed'),
(3438, 71, 'I000717200', 'removed'),
(3439, 71, 'I000714267', 'removed'),
(3440, 71, 'I000719440', 'removed'),
(3441, 71, 'I000718131', 'removed'),
(3442, 71, 'I000718703', 'removed'),
(3443, 71, 'I000714347', 'removed'),
(3444, 71, 'I000716431', 'removed'),
(3445, 71, 'I000714246', 'removed'),
(3446, 71, 'I000715775', 'removed'),
(3447, 71, 'I000715168', 'removed'),
(3448, 71, 'I000715132', 'removed'),
(3449, 71, 'I000719915', 'removed'),
(3450, 71, 'I000712100', 'removed'),
(3451, 71, 'I000718583', 'removed'),
(3452, 71, 'I000714084', 'removed'),
(3453, 72, 'I000721701', 'removed'),
(3454, 72, 'I000723882', 'removed'),
(3455, 72, 'I000727723', 'removed'),
(3456, 72, 'I000722699', 'removed'),
(3457, 72, 'I000724715', 'removed'),
(3458, 72, 'I000725360', 'removed'),
(3459, 72, 'I000729987', 'removed'),
(3460, 72, 'I000728670', 'removed'),
(3461, 72, 'I000725918', 'removed'),
(3462, 72, 'I000722986', 'removed'),
(3463, 72, 'I000725628', 'removed'),
(3464, 72, 'I000722121', 'removed'),
(3465, 72, 'I000723228', 'removed'),
(3466, 72, 'I000728387', 'removed'),
(3467, 72, 'I000727368', 'removed'),
(3468, 72, 'I000726101', 'removed'),
(3469, 72, 'I000726751', 'removed'),
(3470, 72, 'I000724436', 'removed'),
(3471, 72, 'I000721096', 'removed'),
(3472, 72, 'I000721063', 'removed'),
(3473, 72, 'I000724396', 'removed'),
(3474, 72, 'I000726550', 'removed'),
(3475, 72, 'I000726024', 'removed'),
(3476, 72, 'I000724576', 'removed'),
(3477, 72, 'I000721318', 'removed'),
(3478, 72, 'I000724013', 'removed'),
(3479, 72, 'I000724449', 'removed'),
(3480, 72, 'I000728001', 'removed'),
(3481, 72, 'I000726122', 'removed'),
(3482, 72, 'I000722389', 'removed'),
(3483, 72, 'I000723878', 'removed'),
(3484, 72, 'I000729335', 'removed'),
(3485, 72, 'I000721526', 'removed'),
(3486, 72, 'I000723569', 'removed'),
(3487, 72, 'I000722227', 'removed'),
(3488, 72, 'I000724257', 'removed'),
(3489, 72, 'I000722428', 'removed'),
(3490, 72, 'I000729975', 'removed'),
(3491, 72, 'I000728730', 'removed'),
(3492, 72, 'I000728684', 'removed'),
(3493, 73, 'I000737108', 'removed'),
(3494, 73, 'I000733959', 'removed'),
(3495, 73, 'I000739888', 'removed'),
(3496, 73, 'I000732526', 'removed'),
(3497, 73, 'I000732893', 'removed'),
(3498, 73, 'I000733873', 'removed'),
(3499, 73, 'I000735817', 'removed'),
(3500, 73, 'I000732551', 'removed'),
(3501, 73, 'I000733756', 'removed'),
(3502, 73, 'I000736677', 'removed'),
(3503, 73, 'I000731947', 'removed'),
(3504, 73, 'I000732529', 'removed'),
(3505, 73, 'I000736817', 'removed'),
(3506, 73, 'I000736656', 'removed'),
(3507, 73, 'I000739740', 'removed'),
(3508, 73, 'I000732437', 'removed'),
(3509, 73, 'I000732539', 'removed');
INSERT INTO `item` (`id`, `equipment_id`, `item_number`, `status`) VALUES
(3510, 73, 'I000736929', 'removed'),
(3511, 73, 'I000734086', 'removed'),
(3512, 73, 'I000735164', 'removed'),
(3513, 73, 'I000735966', 'removed'),
(3514, 73, 'I000737161', 'removed'),
(3515, 73, 'I000739257', 'removed'),
(3516, 73, 'I000732067', 'removed'),
(3517, 73, 'I000734232', 'removed'),
(3518, 73, 'I000736692', 'removed'),
(3519, 73, 'I000736688', 'removed'),
(3520, 73, 'I000737121', 'removed'),
(3521, 73, 'I000732000', 'removed'),
(3522, 73, 'I000738547', 'removed'),
(3523, 73, 'I000733677', 'removed'),
(3524, 73, 'I000731438', 'removed'),
(3525, 73, 'I000737807', 'removed'),
(3526, 73, 'I000732541', 'removed'),
(3527, 73, 'I000738083', 'removed'),
(3528, 73, 'I000739293', 'removed'),
(3529, 73, 'I000736973', 'removed'),
(3530, 73, 'I000736916', 'removed'),
(3531, 73, 'I000731228', 'removed'),
(3532, 73, 'I000738239', 'removed'),
(3533, 73, 'I000734550', 'removed'),
(3534, 73, 'I000737884', 'removed'),
(3535, 73, 'I000733004', 'removed'),
(3536, 73, 'I000737184', 'removed'),
(3537, 73, 'I000736053', 'removed'),
(3538, 73, 'I000737501', 'removed'),
(3539, 73, 'I000731107', 'removed'),
(3540, 73, 'I000733112', 'removed'),
(3541, 73, 'I000736305', 'removed'),
(3542, 73, 'I000734865', 'removed'),
(3543, 73, 'I000734270', 'removed'),
(3544, 73, 'I000733486', 'removed'),
(3545, 73, 'I000733886', 'removed'),
(3546, 73, 'I000732042', 'removed'),
(3547, 73, 'I000735964', 'removed'),
(3548, 73, 'I000738015', 'removed'),
(3549, 73, 'I000734198', 'removed'),
(3550, 73, 'I000739774', 'removed'),
(3551, 73, 'I000737000', 'removed'),
(3552, 73, 'I000734613', 'removed'),
(3553, 73, 'I000738806', 'removed'),
(3554, 73, 'I000732855', 'removed'),
(3555, 73, 'I000736568', 'removed'),
(3556, 73, 'I000732259', 'removed'),
(3557, 73, 'I000734881', 'removed'),
(3558, 73, 'I000734988', 'removed'),
(3559, 73, 'I000737999', 'removed'),
(3560, 73, 'I000732163', 'removed'),
(3561, 73, 'I000739693', 'removed'),
(3562, 73, 'I000734844', 'removed'),
(3563, 73, 'I000737874', 'removed'),
(3564, 73, 'I000732590', 'removed'),
(3565, 73, 'I000737488', 'removed'),
(3566, 73, 'I000735041', 'removed'),
(3567, 73, 'I000735967', 'removed'),
(3568, 73, 'I000737147', 'removed'),
(3569, 73, 'I000734168', 'removed'),
(3570, 73, 'I000731442', 'removed'),
(3571, 73, 'I000731048', 'removed'),
(3572, 73, 'I000739992', 'removed'),
(3573, 73, 'I000735002', 'removed'),
(3574, 73, 'I000737257', 'removed'),
(3575, 73, 'I000735927', 'removed'),
(3576, 73, 'I000736893', 'removed'),
(3577, 73, 'I000737770', 'removed'),
(3578, 73, 'I000737660', 'removed'),
(3579, 73, 'I000738189', 'removed'),
(3580, 73, 'I000737802', 'removed'),
(3581, 73, 'I000735607', 'removed'),
(3582, 73, 'I000734905', 'removed'),
(3583, 73, 'I000738885', 'removed'),
(3584, 73, 'I000731742', 'removed'),
(3585, 73, 'I000732709', 'removed'),
(3586, 73, 'I000731946', 'removed'),
(3587, 73, 'I000736664', 'removed'),
(3588, 73, 'I000735691', 'removed'),
(3589, 73, 'I000735296', 'removed'),
(3590, 73, 'I000737468', 'removed'),
(3591, 73, 'I000738820', 'removed'),
(3592, 73, 'I000733521', 'removed'),
(3593, 73, 'I000738586', 'removed'),
(3594, 73, 'I000731027', 'removed'),
(3595, 73, 'I000731069', 'removed'),
(3596, 73, 'I000735359', 'removed'),
(3597, 73, 'I000731091', 'removed'),
(3598, 73, 'I000737136', 'removed'),
(3599, 73, 'I000733044', 'removed'),
(3600, 73, 'I000731493', 'removed'),
(3601, 73, 'I000738614', 'removed'),
(3602, 73, 'I000739499', 'removed'),
(3603, 73, 'I000732841', 'removed'),
(3604, 73, 'I000733123', 'removed'),
(3605, 73, 'I000731052', 'removed'),
(3606, 73, 'I000738771', 'removed'),
(3607, 73, 'I000732810', 'removed'),
(3608, 73, 'I000734738', 'removed'),
(3609, 73, 'I000739612', 'removed'),
(3610, 73, 'I000733617', 'removed'),
(3611, 73, 'I000733504', 'removed'),
(3612, 73, 'I000735915', 'removed'),
(3613, 73, 'I000737477', 'removed'),
(3614, 73, 'I000732117', 'removed'),
(3615, 73, 'I000737813', 'removed'),
(3616, 73, 'I000732021', 'removed'),
(3617, 73, 'I000737357', 'removed'),
(3618, 73, 'I000733490', 'removed'),
(3619, 73, 'I000737728', 'removed'),
(3620, 73, 'I000731709', 'removed'),
(3621, 73, 'I000736809', 'removed'),
(3622, 73, 'I000734623', 'removed'),
(3623, 73, 'I000732836', 'removed'),
(3624, 73, 'I000732168', 'removed'),
(3625, 73, 'I000735298', 'removed'),
(3626, 73, 'I000733439', 'removed'),
(3627, 73, 'I000737539', 'removed'),
(3628, 73, 'I000732930', 'removed'),
(3629, 73, 'I000739097', 'removed'),
(3630, 73, 'I000731368', 'removed'),
(3631, 73, 'I000735749', 'removed'),
(3632, 73, 'I000731194', 'removed'),
(3633, 73, 'I000735842', 'removed'),
(3634, 73, 'I000738874', 'removed'),
(3635, 73, 'I000732304', 'removed'),
(3636, 73, 'I000737800', 'removed'),
(3637, 73, 'I000739098', 'removed'),
(3638, 73, 'I000736946', 'removed'),
(3639, 73, 'I000733973', 'removed'),
(3640, 73, 'I000732611', 'removed'),
(3641, 73, 'I000738127', 'removed'),
(3642, 73, 'I000733803', 'removed'),
(3643, 73, 'I000736753', 'removed'),
(3644, 73, 'I000737354', 'removed'),
(3645, 73, 'I000735833', 'removed'),
(3646, 73, 'I000737272', 'removed'),
(3647, 73, 'I000737949', 'removed'),
(3648, 73, 'I000733480', 'removed'),
(3649, 73, 'I000738512', 'removed'),
(3650, 73, 'I000737480', 'removed'),
(3651, 73, 'I000737095', 'removed'),
(3652, 73, 'I000736643', 'removed'),
(3653, 73, 'I000732428', 'removed'),
(3654, 73, 'I000738935', 'removed'),
(3655, 73, 'I000736060', 'removed'),
(3656, 73, 'I000731801', 'removed'),
(3657, 73, 'I000739178', 'removed'),
(3658, 73, 'I000734060', 'removed'),
(3659, 73, 'I000732175', 'removed'),
(3660, 73, 'I000738065', 'removed'),
(3661, 73, 'I000736553', 'removed'),
(3662, 73, 'I000735551', 'removed'),
(3663, 73, 'I000734771', 'removed'),
(3664, 73, 'I000733576', 'removed'),
(3665, 73, 'I000732942', 'removed'),
(3666, 73, 'I000738793', 'removed'),
(3667, 73, 'I000731173', 'removed'),
(3668, 73, 'I000738300', 'removed'),
(3669, 73, 'I000736212', 'removed'),
(3670, 73, 'I000732903', 'removed'),
(3671, 73, 'I000731407', 'removed'),
(3672, 73, 'I000733680', 'removed'),
(3673, 73, 'I000737840', 'removed'),
(3674, 73, 'I000739980', 'removed'),
(3675, 73, 'I000735126', 'removed'),
(3676, 73, 'I000733165', 'removed'),
(3677, 73, 'I000737152', 'removed'),
(3678, 73, 'I000732476', 'removed'),
(3679, 73, 'I000735254', 'removed'),
(3680, 73, 'I000737690', 'removed'),
(3681, 73, 'I000732893', 'removed'),
(3682, 73, 'I000738333', 'removed'),
(3683, 73, 'I000738439', 'removed'),
(3684, 73, 'I000737410', 'removed'),
(3685, 73, 'I000736430', 'removed'),
(3686, 73, 'I000733618', 'removed'),
(3687, 73, 'I000734337', 'removed'),
(3688, 73, 'I000733486', 'removed'),
(3689, 73, 'I000733483', 'removed'),
(3690, 73, 'I000736693', 'removed'),
(3691, 73, 'I000734449', 'removed'),
(3692, 73, 'I000739578', 'removed'),
(3693, 73, 'I000735828', 'removed'),
(3694, 73, 'I000735157', 'removed'),
(3695, 73, 'I000733818', 'removed'),
(3696, 73, 'I000737010', 'removed'),
(3697, 73, 'I000737468', 'removed'),
(3698, 73, 'I000731079', 'removed'),
(3699, 73, 'I000736182', 'removed'),
(3700, 73, 'I000737372', 'removed'),
(3701, 73, 'I000736713', 'removed'),
(3702, 73, 'I000736084', 'removed'),
(3703, 73, 'I000738822', 'removed'),
(3704, 73, 'I000738130', 'removed'),
(3705, 73, 'I000731974', 'removed'),
(3706, 73, 'I000737958', 'removed'),
(3707, 73, 'I000731716', 'removed'),
(3708, 73, 'I000734875', 'removed'),
(3709, 73, 'I000733814', 'removed'),
(3710, 73, 'I000734861', 'removed'),
(3711, 73, 'I000732933', 'removed'),
(3712, 73, 'I000731612', 'removed'),
(3713, 73, 'I000737502', 'removed'),
(3714, 73, 'I000739917', 'removed'),
(3715, 73, 'I000732546', 'removed'),
(3716, 73, 'I000737389', 'removed'),
(3717, 73, 'I000738377', 'removed'),
(3718, 73, 'I000731297', 'removed'),
(3719, 73, 'I000736520', 'removed'),
(3720, 73, 'I000733103', 'removed'),
(3721, 73, 'I000737914', 'removed'),
(3722, 73, 'I000738713', 'removed'),
(3723, 73, 'I000733212', 'removed'),
(3724, 73, 'I000738251', 'removed'),
(3725, 73, 'I000735139', 'removed'),
(3726, 73, 'I000734278', 'removed'),
(3727, 73, 'I000736659', 'removed'),
(3728, 73, 'I000731295', 'removed'),
(3729, 73, 'I000738563', 'removed'),
(3730, 73, 'I000735683', 'removed'),
(3731, 73, 'I000737180', 'removed'),
(3732, 73, 'I000733173', 'removed'),
(3733, 73, 'I000733295', 'removed'),
(3734, 73, 'I000731320', 'removed'),
(3735, 73, 'I000733318', 'removed'),
(3736, 73, 'I000734798', 'removed'),
(3737, 73, 'I000731575', 'removed'),
(3738, 73, 'I000733634', 'removed'),
(3739, 73, 'I000738506', 'removed'),
(3740, 73, 'I000736186', 'removed'),
(3741, 73, 'I000733877', 'removed'),
(3742, 73, 'I000737614', 'removed'),
(3743, 73, 'I000735055', 'removed'),
(3744, 73, 'I000738405', 'removed'),
(3745, 73, 'I000734842', 'removed'),
(3746, 73, 'I000731636', 'removed'),
(3747, 73, 'I000734401', 'removed'),
(3748, 73, 'I000734530', 'removed'),
(3749, 73, 'I000734734', 'removed'),
(3750, 73, 'I000733071', 'removed'),
(3751, 73, 'I000735368', 'removed'),
(3752, 73, 'I000735644', 'removed'),
(3753, 73, 'I000733808', 'removed'),
(3754, 73, 'I000732189', 'removed'),
(3755, 73, 'I000731594', 'removed'),
(3756, 73, 'I000739480', 'removed'),
(3757, 73, 'I000732406', 'removed'),
(3758, 73, 'I000738666', 'removed'),
(3759, 73, 'I000736896', 'removed'),
(3760, 73, 'I000736667', 'removed'),
(3761, 73, 'I000734879', 'removed'),
(3762, 73, 'I000734592', 'removed'),
(3763, 73, 'I000735963', 'removed'),
(3764, 73, 'I000733254', 'removed'),
(3765, 73, 'I000737040', 'removed'),
(3766, 73, 'I000734105', 'removed'),
(3767, 73, 'I000738912', 'removed'),
(3768, 73, 'I000735273', 'removed'),
(3769, 73, 'I000739617', 'removed'),
(3770, 73, 'I000733544', 'removed'),
(3771, 73, 'I000736668', 'removed'),
(3772, 73, 'I000733427', 'removed'),
(3773, 73, 'I000738382', 'removed'),
(3774, 73, 'I000731312', 'removed'),
(3775, 73, 'I000732112', 'removed'),
(3776, 73, 'I000734608', 'removed'),
(3777, 73, 'I000734431', 'removed'),
(3778, 73, 'I000735341', 'removed'),
(3779, 73, 'I000731888', 'removed'),
(3780, 73, 'I000734694', 'removed'),
(3781, 73, 'I000737817', 'removed'),
(3782, 73, 'I000731562', 'removed'),
(3783, 73, 'I000733223', 'removed'),
(3784, 73, 'I000739798', 'removed'),
(3785, 73, 'I000737444', 'removed'),
(3786, 73, 'I000737148', 'removed'),
(3787, 73, 'I000734449', 'removed'),
(3788, 73, 'I000735478', 'removed'),
(3789, 73, 'I000738924', 'removed'),
(3790, 73, 'I000731895', 'removed'),
(3791, 73, 'I000733961', 'removed'),
(3792, 73, 'I000733104', 'removed'),
(3793, 73, 'I000733947', 'removed'),
(3794, 73, 'I000735339', 'removed'),
(3795, 73, 'I000731804', 'removed'),
(3796, 73, 'I000732856', 'removed'),
(3797, 73, 'I000735310', 'removed'),
(3798, 73, 'I000732073', 'removed'),
(3799, 73, 'I000738236', 'removed'),
(3800, 73, 'I000736110', 'removed'),
(3801, 73, 'I000734859', 'removed'),
(3802, 73, 'I000738960', 'removed'),
(3803, 73, 'I000733795', 'removed'),
(3804, 73, 'I000738357', 'removed'),
(3805, 73, 'I000736264', 'removed'),
(3806, 73, 'I000732076', 'removed'),
(3807, 73, 'I000738954', 'removed'),
(3808, 73, 'I000736080', 'removed'),
(3809, 73, 'I000732203', 'removed'),
(3810, 73, 'I000734026', 'removed'),
(3811, 73, 'I000731303', 'removed'),
(3812, 73, 'I000739794', 'removed'),
(3813, 73, 'I000732249', 'removed'),
(3814, 73, 'I000737224', 'removed'),
(3815, 73, 'I000733286', 'removed'),
(3816, 73, 'I000735307', 'removed'),
(3817, 73, 'I000736897', 'removed'),
(3818, 73, 'I000735564', 'removed'),
(3819, 73, 'I000736525', 'removed'),
(3820, 73, 'I000739540', 'removed'),
(3821, 73, 'I000738805', 'removed'),
(3822, 73, 'I000738325', 'removed'),
(3823, 73, 'I000735569', 'removed'),
(3824, 73, 'I000737158', 'removed'),
(3825, 73, 'I000733764', 'removed'),
(3826, 73, 'I000735739', 'removed'),
(3827, 73, 'I000736434', 'removed'),
(3828, 73, 'I000736493', 'removed'),
(3829, 73, 'I000731086', 'removed'),
(3830, 73, 'I000731482', 'removed'),
(3831, 73, 'I000738280', 'removed'),
(3832, 73, 'I000737608', 'removed'),
(3833, 73, 'I000731309', 'removed'),
(3834, 73, 'I000739282', 'removed'),
(3835, 73, 'I000731214', 'removed'),
(3836, 73, 'I000735813', 'removed'),
(3837, 73, 'I000738416', 'removed'),
(3838, 73, 'I000737218', 'removed'),
(3839, 73, 'I000731670', 'removed'),
(3840, 73, 'I000731656', 'removed'),
(3841, 73, 'I000736063', 'removed'),
(3842, 73, 'I000734583', 'removed'),
(3843, 73, 'I000734400', 'removed'),
(3844, 73, 'I000734976', 'removed'),
(3845, 73, 'I000734225', 'removed'),
(3846, 73, 'I000737386', 'removed'),
(3847, 73, 'I000731945', 'removed'),
(3848, 73, 'I000739748', 'removed'),
(3849, 73, 'I000734610', 'removed'),
(3850, 73, 'I000737485', 'removed'),
(3851, 73, 'I000735010', 'removed'),
(3852, 73, 'I000738721', 'removed'),
(3853, 73, 'I000732241', 'removed'),
(3854, 73, 'I000733364', 'removed'),
(3855, 73, 'I000736644', 'removed'),
(3856, 73, 'I000734583', 'removed'),
(3857, 73, 'I000733569', 'removed'),
(3858, 73, 'I000737450', 'removed'),
(3859, 74, 'I000745025', 'removed'),
(3860, 74, 'I000746466', 'removed'),
(3861, 74, 'I000748710', 'removed'),
(3862, 74, 'I000743871', 'removed'),
(3863, 74, 'I000746314', 'removed'),
(3864, 74, 'I000747193', 'removed'),
(3865, 25, 'I000253038', 'unavailable'),
(3866, 25, 'I000255840', 'unavailable'),
(3867, 25, 'I000256236', 'unavailable'),
(3868, 25, 'I000253657', 'unavailable'),
(3869, 75, 'I000755890', 'removed'),
(3870, 75, 'I000759484', 'removed'),
(3871, 75, 'I000759036', 'removed'),
(3872, 75, 'I000754169', 'removed'),
(3873, 75, 'I000755743', 'removed'),
(3874, 75, 'I000757437', 'removed'),
(3875, 75, 'I000757467', 'removed'),
(3876, 75, 'I000751777', 'removed'),
(3877, 75, 'I000751412', 'removed'),
(3878, 75, 'I000759673', 'removed'),
(3879, 75, 'I000757924', 'removed'),
(3880, 75, 'I000758342', 'removed'),
(3881, 75, 'I000755037', 'removed'),
(3882, 75, 'I000751465', 'removed'),
(3883, 75, 'I000758347', 'removed'),
(3884, 75, 'I000759253', 'removed'),
(3885, 75, 'I000759535', 'removed'),
(3886, 75, 'I000756736', 'removed'),
(3887, 75, 'I000755021', 'removed'),
(3888, 75, 'I000756611', 'removed'),
(3889, 75, 'I000757277', 'removed'),
(3890, 75, 'I000755609', 'removed'),
(3891, 75, 'I000753929', 'removed'),
(3892, 75, 'I000755018', 'removed'),
(3893, 75, 'I000754831', 'removed'),
(3894, 75, 'I000759469', 'removed'),
(3895, 75, 'I000759288', 'removed'),
(3896, 75, 'I000756309', 'removed'),
(3897, 75, 'I000754105', 'removed'),
(3898, 75, 'I000759044', 'removed'),
(3899, 75, 'I000755733', 'removed'),
(3900, 75, 'I000756370', 'removed'),
(3901, 75, 'I000753259', 'removed'),
(3902, 75, 'I000751427', 'removed'),
(3903, 75, 'I000753612', 'removed'),
(3904, 75, 'I000757626', 'removed'),
(3905, 75, 'I000751215', 'removed'),
(3906, 75, 'I000755289', 'removed'),
(3907, 75, 'I000755391', 'removed'),
(3908, 75, 'I000756661', 'removed'),
(3909, 75, 'I000752879', 'removed'),
(3910, 75, 'I000759581', 'removed'),
(3911, 75, 'I000758973', 'removed'),
(3912, 75, 'I000759091', 'removed'),
(3913, 75, 'I000757811', 'removed'),
(3914, 75, 'I000759420', 'removed'),
(3915, 75, 'I000756525', 'removed'),
(3916, 75, 'I000759036', 'removed'),
(3917, 75, 'I000751786', 'removed'),
(3918, 75, 'I000753446', 'removed'),
(3919, 75, 'I000751233', 'removed'),
(3920, 75, 'I000757251', 'removed'),
(3921, 75, 'I000756501', 'removed'),
(3922, 75, 'I000751101', 'removed'),
(3923, 75, 'I000754241', 'removed'),
(3924, 75, 'I000754502', 'removed'),
(3925, 75, 'I000758210', 'removed'),
(3926, 75, 'I000754306', 'removed'),
(3927, 75, 'I000754765', 'removed'),
(3928, 75, 'I000753606', 'removed'),
(3929, 75, 'I000754464', 'removed'),
(3930, 75, 'I000754206', 'removed'),
(3931, 75, 'I000759578', 'removed'),
(3932, 75, 'I000755963', 'removed'),
(3933, 75, 'I000755921', 'removed'),
(3934, 75, 'I000751667', 'removed'),
(3935, 75, 'I000755002', 'removed'),
(3936, 75, 'I000758887', 'removed'),
(3937, 75, 'I000755691', 'removed'),
(3938, 75, 'I000758985', 'removed'),
(3939, 75, 'I000751337', 'removed'),
(3940, 75, 'I000756381', 'removed'),
(3941, 75, 'I000759065', 'removed'),
(3942, 75, 'I000755653', 'removed'),
(3943, 75, 'I000756669', 'removed'),
(3944, 75, 'I000752687', 'removed'),
(3945, 75, 'I000757870', 'removed'),
(3946, 75, 'I000757196', 'removed'),
(3947, 75, 'I000751745', 'removed'),
(3948, 75, 'I000753228', 'removed'),
(3949, 75, 'I000758487', 'removed'),
(3950, 75, 'I000755046', 'removed'),
(3951, 75, 'I000759333', 'removed'),
(3952, 75, 'I000754762', 'removed'),
(3953, 75, 'I000755323', 'removed'),
(3954, 75, 'I000753123', 'removed'),
(3955, 75, 'I000751281', 'removed'),
(3956, 75, 'I000754084', 'removed'),
(3957, 75, 'I000751911', 'removed'),
(3958, 75, 'I000757406', 'removed'),
(3959, 75, 'I000755358', 'removed'),
(3960, 75, 'I000756994', 'removed'),
(3961, 75, 'I000758731', 'removed'),
(3962, 75, 'I000757760', 'removed'),
(3963, 75, 'I000752709', 'removed'),
(3964, 75, 'I000755468', 'removed'),
(3965, 75, 'I000757122', 'removed'),
(3966, 75, 'I000755971', 'removed'),
(3967, 75, 'I000753203', 'removed'),
(3968, 75, 'I000755965', 'removed'),
(3969, 75, 'I000752352', 'removed'),
(3970, 75, 'I000757845', 'removed'),
(3971, 75, 'I000759731', 'removed'),
(3972, 75, 'I000755134', 'removed'),
(3973, 75, 'I000758805', 'removed'),
(3974, 75, 'I000752937', 'removed'),
(3975, 75, 'I000752407', 'removed'),
(3976, 75, 'I000759145', 'removed'),
(3977, 75, 'I000756931', 'removed'),
(3978, 75, 'I000759453', 'removed'),
(3979, 75, 'I000754046', 'removed'),
(3980, 75, 'I000756646', 'removed'),
(3981, 75, 'I000755686', 'removed'),
(3982, 75, 'I000759053', 'removed'),
(3983, 75, 'I000759663', 'removed'),
(3984, 75, 'I000758964', 'removed'),
(3985, 75, 'I000758187', 'removed'),
(3986, 75, 'I000758702', 'removed'),
(3987, 75, 'I000757499', 'removed'),
(3988, 75, 'I000756996', 'removed'),
(3989, 75, 'I000758275', 'removed'),
(3990, 75, 'I000751094', 'removed'),
(3991, 75, 'I000756155', 'removed'),
(3992, 75, 'I000754929', 'removed'),
(3993, 75, 'I000758087', 'removed'),
(3994, 75, 'I000757125', 'removed'),
(3995, 75, 'I000755437', 'removed'),
(3996, 75, 'I000759010', 'removed'),
(3997, 75, 'I000752093', 'removed'),
(3998, 75, 'I000759508', 'removed'),
(3999, 75, 'I000755647', 'removed'),
(4000, 75, 'I000759552', 'removed'),
(4001, 75, 'I000751503', 'removed'),
(4002, 75, 'I000752432', 'removed'),
(4003, 75, 'I000752513', 'removed'),
(4004, 75, 'I000754694', 'removed'),
(4005, 75, 'I000759407', 'removed'),
(4006, 75, 'I000757330', 'removed'),
(4007, 75, 'I000756444', 'removed'),
(4008, 75, 'I000758307', 'removed'),
(4009, 75, 'I000756676', 'removed'),
(4010, 75, 'I000753905', 'removed'),
(4011, 75, 'I000751944', 'removed'),
(4012, 75, 'I000757637', 'removed'),
(4013, 75, 'I000751216', 'removed'),
(4014, 75, 'I000759236', 'removed'),
(4015, 75, 'I000757608', 'removed'),
(4016, 75, 'I000755019', 'removed'),
(4017, 75, 'I000759759', 'removed'),
(4018, 75, 'I000754102', 'removed'),
(4019, 75, 'I000756957', 'removed'),
(4020, 75, 'I000754117', 'removed'),
(4021, 75, 'I000755444', 'removed'),
(4022, 75, 'I000752237', 'removed'),
(4023, 75, 'I000751349', 'removed'),
(4024, 75, 'I000754353', 'removed'),
(4025, 75, 'I000756417', 'removed'),
(4026, 75, 'I000759590', 'removed'),
(4027, 75, 'I000756308', 'removed'),
(4028, 75, 'I000755387', 'removed'),
(4029, 75, 'I000758436', 'removed'),
(4030, 75, 'I000752652', 'removed'),
(4031, 75, 'I000759304', 'removed'),
(4032, 75, 'I000757755', 'removed'),
(4033, 75, 'I000758455', 'removed'),
(4034, 75, 'I000758703', 'removed'),
(4035, 75, 'I000754333', 'removed'),
(4036, 75, 'I000751601', 'removed'),
(4037, 75, 'I000757497', 'removed'),
(4038, 75, 'I000755859', 'removed'),
(4039, 75, 'I000757458', 'removed'),
(4040, 75, 'I000758833', 'removed'),
(4041, 75, 'I000759552', 'removed'),
(4042, 75, 'I000751843', 'removed'),
(4043, 75, 'I000758119', 'removed'),
(4044, 75, 'I000759268', 'removed'),
(4045, 75, 'I000754874', 'removed'),
(4046, 75, 'I000758045', 'removed'),
(4047, 75, 'I000758840', 'removed'),
(4048, 75, 'I000754407', 'removed'),
(4049, 75, 'I000753024', 'removed'),
(4050, 75, 'I000757645', 'removed'),
(4051, 75, 'I000758887', 'removed'),
(4052, 75, 'I000758895', 'removed'),
(4053, 75, 'I000756866', 'removed'),
(4054, 75, 'I000754239', 'removed'),
(4055, 75, 'I000758603', 'removed'),
(4056, 75, 'I000757148', 'removed'),
(4057, 75, 'I000753237', 'removed'),
(4058, 75, 'I000755015', 'removed'),
(4059, 75, 'I000752006', 'removed'),
(4060, 75, 'I000759824', 'removed'),
(4061, 75, 'I000752674', 'removed'),
(4062, 75, 'I000752216', 'removed'),
(4063, 75, 'I000755871', 'removed'),
(4064, 75, 'I000757444', 'removed'),
(4065, 75, 'I000754426', 'removed'),
(4066, 75, 'I000754940', 'removed'),
(4067, 75, 'I000755865', 'removed'),
(4068, 75, 'I000759809', 'removed'),
(4069, 75, 'I000754419', 'removed'),
(4070, 75, 'I000751518', 'removed'),
(4071, 75, 'I000753904', 'removed'),
(4072, 75, 'I000753351', 'removed'),
(4073, 75, 'I000757363', 'removed'),
(4074, 75, 'I000757575', 'removed'),
(4075, 75, 'I000752496', 'removed'),
(4076, 75, 'I000753083', 'removed'),
(4077, 75, 'I000755948', 'removed'),
(4078, 75, 'I000751109', 'removed'),
(4079, 75, 'I000759379', 'removed'),
(4080, 75, 'I000753051', 'removed'),
(4081, 75, 'I000756394', 'removed'),
(4082, 75, 'I000759861', 'removed'),
(4083, 75, 'I000759641', 'removed'),
(4084, 75, 'I000751266', 'removed'),
(4085, 75, 'I000756130', 'removed'),
(4086, 75, 'I000752910', 'removed'),
(4087, 75, 'I000755959', 'removed'),
(4088, 75, 'I000752857', 'removed'),
(4089, 75, 'I000756485', 'removed'),
(4090, 75, 'I000754344', 'removed'),
(4091, 75, 'I000753012', 'removed'),
(4092, 75, 'I000755969', 'removed'),
(4093, 75, 'I000752136', 'removed'),
(4094, 75, 'I000751707', 'removed'),
(4095, 75, 'I000751036', 'removed'),
(4096, 75, 'I000751158', 'removed'),
(4097, 75, 'I000759516', 'removed'),
(4098, 75, 'I000755214', 'removed'),
(4099, 75, 'I000751258', 'removed'),
(4100, 75, 'I000759816', 'removed'),
(4101, 75, 'I000751024', 'removed'),
(4102, 75, 'I000758944', 'removed'),
(4103, 75, 'I000754555', 'removed'),
(4104, 75, 'I000752892', 'removed'),
(4105, 75, 'I000751788', 'removed'),
(4106, 75, 'I000754773', 'removed'),
(4107, 75, 'I000758431', 'removed'),
(4108, 75, 'I000756060', 'removed'),
(4109, 75, 'I000759108', 'removed'),
(4110, 75, 'I000757345', 'removed'),
(4111, 75, 'I000759418', 'removed'),
(4112, 75, 'I000751674', 'removed'),
(4113, 75, 'I000756873', 'removed'),
(4114, 75, 'I000754364', 'removed'),
(4115, 75, 'I000758886', 'removed'),
(4116, 75, 'I000759397', 'removed'),
(4117, 75, 'I000755413', 'removed'),
(4118, 75, 'I000756106', 'removed'),
(4119, 75, 'I000751126', 'removed'),
(4120, 75, 'I000758276', 'removed'),
(4121, 75, 'I000758954', 'removed'),
(4122, 75, 'I000752469', 'removed'),
(4123, 75, 'I000758159', 'removed'),
(4124, 75, 'I000753638', 'removed'),
(4125, 75, 'I000751533', 'removed'),
(4126, 75, 'I000752223', 'removed'),
(4127, 75, 'I000757264', 'removed'),
(4128, 75, 'I000756842', 'removed'),
(4129, 75, 'I000757455', 'removed'),
(4130, 75, 'I000754911', 'removed'),
(4131, 75, 'I000755055', 'removed'),
(4132, 75, 'I000753670', 'removed'),
(4133, 75, 'I000753081', 'removed'),
(4134, 75, 'I000754340', 'removed'),
(4135, 75, 'I000756560', 'removed'),
(4136, 75, 'I000751421', 'removed'),
(4137, 75, 'I000759796', 'removed'),
(4138, 75, 'I000758167', 'removed'),
(4139, 75, 'I000759840', 'removed'),
(4140, 75, 'I000752302', 'removed'),
(4141, 75, 'I000756018', 'removed'),
(4142, 75, 'I000759456', 'removed'),
(4143, 75, 'I000755346', 'removed'),
(4144, 75, 'I000754298', 'removed'),
(4145, 75, 'I000753391', 'removed'),
(4146, 75, 'I000754580', 'removed'),
(4147, 75, 'I000758332', 'removed'),
(4148, 75, 'I000755919', 'removed'),
(4149, 75, 'I000757311', 'removed'),
(4150, 75, 'I000751465', 'removed'),
(4151, 75, 'I000753868', 'removed'),
(4152, 75, 'I000753149', 'removed'),
(4153, 75, 'I000753845', 'removed'),
(4154, 75, 'I000755548', 'removed'),
(4155, 75, 'I000754649', 'removed'),
(4156, 75, 'I000758229', 'removed'),
(4157, 75, 'I000753356', 'removed'),
(4158, 75, 'I000754534', 'removed'),
(4159, 75, 'I000757786', 'removed'),
(4160, 75, 'I000754957', 'removed'),
(4161, 75, 'I000752937', 'removed'),
(4162, 75, 'I000758034', 'removed'),
(4163, 75, 'I000756050', 'removed'),
(4164, 75, 'I000753484', 'removed'),
(4165, 75, 'I000758574', 'removed'),
(4166, 75, 'I000752383', 'removed'),
(4167, 75, 'I000759940', 'removed'),
(4168, 75, 'I000752255', 'removed'),
(4169, 75, 'I000754216', 'removed'),
(4170, 75, 'I000752638', 'removed'),
(4171, 75, 'I000759018', 'removed'),
(4172, 75, 'I000759433', 'removed'),
(4173, 75, 'I000754908', 'removed'),
(4174, 75, 'I000755190', 'removed'),
(4175, 75, 'I000756276', 'removed'),
(4176, 75, 'I000756363', 'removed'),
(4177, 75, 'I000757246', 'removed'),
(4178, 75, 'I000755249', 'removed'),
(4179, 75, 'I000754346', 'removed'),
(4180, 75, 'I000755173', 'removed'),
(4181, 75, 'I000752003', 'removed'),
(4182, 75, 'I000756338', 'removed'),
(4183, 75, 'I000759225', 'removed'),
(4184, 75, 'I000757063', 'removed'),
(4185, 75, 'I000753009', 'removed'),
(4186, 75, 'I000758440', 'removed'),
(4187, 75, 'I000754113', 'removed'),
(4188, 75, 'I000758017', 'removed'),
(4189, 75, 'I000754140', 'removed'),
(4190, 75, 'I000753354', 'removed'),
(4191, 75, 'I000755292', 'removed'),
(4192, 75, 'I000754191', 'removed'),
(4193, 75, 'I000751845', 'removed'),
(4194, 75, 'I000753161', 'removed'),
(4195, 75, 'I000754302', 'removed'),
(4196, 75, 'I000755129', 'removed'),
(4197, 75, 'I000751571', 'removed'),
(4198, 75, 'I000757511', 'removed'),
(4199, 75, 'I000758416', 'removed'),
(4200, 75, 'I000758143', 'removed'),
(4201, 75, 'I000753814', 'removed'),
(4202, 75, 'I000755011', 'removed'),
(4203, 75, 'I000753711', 'removed'),
(4204, 75, 'I000754256', 'removed'),
(4205, 75, 'I000758305', 'removed'),
(4206, 75, 'I000758314', 'removed'),
(4207, 75, 'I000758069', 'removed'),
(4208, 75, 'I000753314', 'removed'),
(4209, 75, 'I000755185', 'removed'),
(4210, 75, 'I000752528', 'removed'),
(4211, 75, 'I000757203', 'removed'),
(4212, 75, 'I000752933', 'removed'),
(4213, 75, 'I000755733', 'removed'),
(4214, 75, 'I000757360', 'removed'),
(4215, 75, 'I000754091', 'removed'),
(4216, 75, 'I000758323', 'removed'),
(4217, 75, 'I000751151', 'removed'),
(4218, 75, 'I000756804', 'removed'),
(4219, 75, 'I000757755', 'removed'),
(4220, 75, 'I000751939', 'removed'),
(4221, 75, 'I000758890', 'removed'),
(4222, 75, 'I000754282', 'removed'),
(4223, 75, 'I000757192', 'removed'),
(4224, 75, 'I000754943', 'removed'),
(4225, 75, 'I000758028', 'removed'),
(4226, 75, 'I000755878', 'removed'),
(4227, 75, 'I000759263', 'removed'),
(4228, 75, 'I000752936', 'removed'),
(4229, 75, 'I000755257', 'removed'),
(4230, 75, 'I000757283', 'removed'),
(4231, 75, 'I000756907', 'removed'),
(4232, 75, 'I000758517', 'removed'),
(4233, 75, 'I000756499', 'removed'),
(4234, 75, 'I000757670', 'removed'),
(4235, 75, 'I000753079', 'removed'),
(4236, 75, 'I000754305', 'removed'),
(4237, 75, 'I000755927', 'removed'),
(4238, 75, 'I000757697', 'removed'),
(4239, 75, 'I000758735', 'removed'),
(4240, 75, 'I000753533', 'removed'),
(4241, 75, 'I000753344', 'removed'),
(4242, 75, 'I000751583', 'removed'),
(4243, 75, 'I000755815', 'removed'),
(4244, 75, 'I000753063', 'removed'),
(4245, 75, 'I000752699', 'removed'),
(4246, 75, 'I000752558', 'removed'),
(4247, 75, 'I000753136', 'removed'),
(4248, 75, 'I000756397', 'removed'),
(4249, 75, 'I000759162', 'removed'),
(4250, 75, 'I000755332', 'removed'),
(4251, 75, 'I000756697', 'removed'),
(4252, 75, 'I000754077', 'removed'),
(4253, 75, 'I000755037', 'removed'),
(4254, 75, 'I000751849', 'removed'),
(4255, 75, 'I000754622', 'removed'),
(4256, 75, 'I000755016', 'removed'),
(4257, 75, 'I000755539', 'removed'),
(4258, 75, 'I000752929', 'removed'),
(4259, 75, 'I000752643', 'removed'),
(4260, 75, 'I000752682', 'removed'),
(4261, 75, 'I000756018', 'removed'),
(4262, 75, 'I000756043', 'removed'),
(4263, 75, 'I000753494', 'removed'),
(4264, 75, 'I000758839', 'removed'),
(4265, 75, 'I000757318', 'removed'),
(4266, 75, 'I000752329', 'removed'),
(4267, 75, 'I000752228', 'removed'),
(4268, 75, 'I000759198', 'removed'),
(4269, 75, 'I000757553', 'removed'),
(4270, 75, 'I000753556', 'removed'),
(4271, 75, 'I000755415', 'removed'),
(4272, 75, 'I000754725', 'removed'),
(4273, 75, 'I000751871', 'removed'),
(4274, 75, 'I000757864', 'removed'),
(4275, 75, 'I000753416', 'removed'),
(4276, 75, 'I000758960', 'removed'),
(4277, 75, 'I000751331', 'removed'),
(4278, 75, 'I000754479', 'removed'),
(4279, 75, 'I000756958', 'removed'),
(4280, 75, 'I000757731', 'removed'),
(4281, 75, 'I000759093', 'removed'),
(4282, 75, 'I000751027', 'removed'),
(4283, 75, 'I000756515', 'removed'),
(4284, 75, 'I000753377', 'removed'),
(4285, 75, 'I000752372', 'removed'),
(4286, 75, 'I000752497', 'removed'),
(4287, 75, 'I000754939', 'removed'),
(4288, 75, 'I000755207', 'removed'),
(4289, 75, 'I000756313', 'removed'),
(4290, 75, 'I000756915', 'removed'),
(4291, 75, 'I000758412', 'removed'),
(4292, 75, 'I000754417', 'removed'),
(4293, 75, 'I000755060', 'removed'),
(4294, 75, 'I000758226', 'removed'),
(4295, 75, 'I000758590', 'removed'),
(4296, 75, 'I000755011', 'removed'),
(4297, 75, 'I000755731', 'removed'),
(4298, 75, 'I000756629', 'removed'),
(4299, 75, 'I000758198', 'removed'),
(4300, 75, 'I000752947', 'removed'),
(4301, 75, 'I000756040', 'removed'),
(4302, 75, 'I000754074', 'removed'),
(4303, 75, 'I000751886', 'removed'),
(4304, 75, 'I000754998', 'removed'),
(4305, 75, 'I000752822', 'removed'),
(4306, 75, 'I000755469', 'removed'),
(4307, 75, 'I000753345', 'removed'),
(4308, 75, 'I000752172', 'removed'),
(4309, 75, 'I000758007', 'removed'),
(4310, 75, 'I000753420', 'removed'),
(4311, 75, 'I000757746', 'removed'),
(4312, 75, 'I000755234', 'removed'),
(4313, 75, 'I000758562', 'removed'),
(4314, 75, 'I000754408', 'removed'),
(4315, 75, 'I000753538', 'removed'),
(4316, 75, 'I000756384', 'removed'),
(4317, 75, 'I000755296', 'removed'),
(4318, 75, 'I000753998', 'removed'),
(4319, 75, 'I000757138', 'removed'),
(4320, 75, 'I000756477', 'removed'),
(4321, 75, 'I000751662', 'removed'),
(4322, 75, 'I000756115', 'removed'),
(4323, 75, 'I000758064', 'removed'),
(4324, 75, 'I000755017', 'removed'),
(4325, 75, 'I000751578', 'removed'),
(4326, 75, 'I000758673', 'removed'),
(4327, 75, 'I000753421', 'removed'),
(4328, 75, 'I000751775', 'removed'),
(4329, 75, 'I000758772', 'removed'),
(4330, 75, 'I000753535', 'removed'),
(4331, 75, 'I000753046', 'removed'),
(4332, 75, 'I000754895', 'removed'),
(4333, 75, 'I000758425', 'removed'),
(4334, 75, 'I000754850', 'removed'),
(4335, 75, 'I000754170', 'removed'),
(4336, 75, 'I000755403', 'removed'),
(4337, 75, 'I000757966', 'removed'),
(4338, 75, 'I000759560', 'removed'),
(4339, 75, 'I000757790', 'removed'),
(4340, 75, 'I000754793', 'removed'),
(4341, 75, 'I000758790', 'removed'),
(4342, 75, 'I000753709', 'removed'),
(4343, 75, 'I000756192', 'removed'),
(4344, 75, 'I000759111', 'removed'),
(4345, 75, 'I000756204', 'removed'),
(4346, 75, 'I000755723', 'removed'),
(4347, 75, 'I000754739', 'removed'),
(4348, 75, 'I000758363', 'removed'),
(4349, 75, 'I000755077', 'removed'),
(4350, 75, 'I000759028', 'removed'),
(4351, 75, 'I000755685', 'removed'),
(4352, 75, 'I000755598', 'removed'),
(4353, 75, 'I000757024', 'removed'),
(4354, 75, 'I000757906', 'removed'),
(4355, 75, 'I000752843', 'removed'),
(4356, 75, 'I000756700', 'removed'),
(4357, 75, 'I000758547', 'removed'),
(4358, 75, 'I000758936', 'removed'),
(4359, 75, 'I000758692', 'removed'),
(4360, 75, 'I000759559', 'removed'),
(4361, 75, 'I000751231', 'removed'),
(4362, 75, 'I000756192', 'removed'),
(4363, 75, 'I000754399', 'removed'),
(4364, 75, 'I000756697', 'removed'),
(4365, 75, 'I000753482', 'removed'),
(4366, 75, 'I000752717', 'removed'),
(4367, 75, 'I000758122', 'removed'),
(4368, 75, 'I000759705', 'removed'),
(4369, 75, 'I000759135', 'removed'),
(4370, 75, 'I000752053', 'removed'),
(4371, 75, 'I000757629', 'removed'),
(4372, 75, 'I000756303', 'removed'),
(4373, 75, 'I000752928', 'removed'),
(4374, 75, 'I000754738', 'removed'),
(4375, 75, 'I000756509', 'removed'),
(4376, 75, 'I000755871', 'removed'),
(4377, 75, 'I000754809', 'removed'),
(4378, 75, 'I000758361', 'removed'),
(4379, 75, 'I000754421', 'removed'),
(4380, 75, 'I000755591', 'removed'),
(4381, 75, 'I000759268', 'removed'),
(4382, 75, 'I000755608', 'removed'),
(4383, 75, 'I000751977', 'removed'),
(4384, 75, 'I000751918', 'removed'),
(4385, 75, 'I000756483', 'removed'),
(4386, 75, 'I000753758', 'removed'),
(4387, 75, 'I000759885', 'removed'),
(4388, 75, 'I000756833', 'removed'),
(4389, 75, 'I000751704', 'removed'),
(4390, 75, 'I000753710', 'removed'),
(4391, 75, 'I000756988', 'removed'),
(4392, 75, 'I000756211', 'removed'),
(4393, 75, 'I000756963', 'removed'),
(4394, 75, 'I000751228', 'removed'),
(4395, 75, 'I000752501', 'removed'),
(4396, 75, 'I000756312', 'removed'),
(4397, 75, 'I000754241', 'removed'),
(4398, 75, 'I000752610', 'removed'),
(4399, 75, 'I000758259', 'removed'),
(4400, 75, 'I000754271', 'removed'),
(4401, 75, 'I000753652', 'removed'),
(4402, 75, 'I000759792', 'removed'),
(4403, 75, 'I000756015', 'removed'),
(4404, 75, 'I000754987', 'removed'),
(4405, 75, 'I000751447', 'removed'),
(4406, 75, 'I000756450', 'removed'),
(4407, 75, 'I000757355', 'removed'),
(4408, 75, 'I000756541', 'removed'),
(4409, 75, 'I000759921', 'removed'),
(4410, 75, 'I000759463', 'removed'),
(4411, 75, 'I000756340', 'removed'),
(4412, 75, 'I000755127', 'removed'),
(4413, 75, 'I000751181', 'removed'),
(4414, 75, 'I000759133', 'removed'),
(4415, 75, 'I000756732', 'removed'),
(4416, 75, 'I000756545', 'removed'),
(4417, 75, 'I000756604', 'removed'),
(4418, 75, 'I000759403', 'removed'),
(4419, 75, 'I000758061', 'removed'),
(4420, 75, 'I000752370', 'removed'),
(4421, 75, 'I000755319', 'removed'),
(4422, 75, 'I000754559', 'removed'),
(4423, 75, 'I000758582', 'removed'),
(4424, 75, 'I000751856', 'removed'),
(4425, 75, 'I000758863', 'removed'),
(4426, 75, 'I000754530', 'removed'),
(4427, 75, 'I000753146', 'removed'),
(4428, 75, 'I000756840', 'removed'),
(4429, 75, 'I000758475', 'removed'),
(4430, 75, 'I000756297', 'removed'),
(4431, 75, 'I000754197', 'removed'),
(4432, 75, 'I000751918', 'removed'),
(4433, 75, 'I000754495', 'removed'),
(4434, 75, 'I000751980', 'removed'),
(4435, 75, 'I000752994', 'removed'),
(4436, 75, 'I000758910', 'removed'),
(4437, 75, 'I000754177', 'removed'),
(4438, 75, 'I000755007', 'removed'),
(4439, 75, 'I000759374', 'removed'),
(4440, 75, 'I000753872', 'removed'),
(4441, 75, 'I000754039', 'removed'),
(4442, 75, 'I000752727', 'removed'),
(4443, 75, 'I000753295', 'removed'),
(4444, 75, 'I000757907', 'removed'),
(4445, 75, 'I000759412', 'removed'),
(4446, 75, 'I000755505', 'removed'),
(4447, 75, 'I000757728', 'removed'),
(4448, 75, 'I000754114', 'removed'),
(4449, 75, 'I000755458', 'removed'),
(4450, 75, 'I000759509', 'removed'),
(4451, 75, 'I000752382', 'removed'),
(4452, 75, 'I000751507', 'removed'),
(4453, 75, 'I000754035', 'removed'),
(4454, 75, 'I000755943', 'removed'),
(4455, 75, 'I000751432', 'removed'),
(4456, 75, 'I000756560', 'removed'),
(4457, 75, 'I000759592', 'removed'),
(4458, 75, 'I000753045', 'removed'),
(4459, 75, 'I000754360', 'removed'),
(4460, 75, 'I000757277', 'removed'),
(4461, 75, 'I000755856', 'removed'),
(4462, 75, 'I000756607', 'removed'),
(4463, 75, 'I000756669', 'removed'),
(4464, 75, 'I000751079', 'removed'),
(4465, 75, 'I000752324', 'removed'),
(4466, 75, 'I000754275', 'removed'),
(4467, 75, 'I000753147', 'removed'),
(4468, 75, 'I000759151', 'removed'),
(4469, 75, 'I000757390', 'removed'),
(4470, 75, 'I000759880', 'removed'),
(4471, 75, 'I000751044', 'removed'),
(4472, 75, 'I000758747', 'removed'),
(4473, 75, 'I000759633', 'removed'),
(4474, 75, 'I000752144', 'removed'),
(4475, 75, 'I000754872', 'removed'),
(4476, 75, 'I000757114', 'removed'),
(4477, 75, 'I000757142', 'removed'),
(4478, 75, 'I000755509', 'removed'),
(4479, 75, 'I000751235', 'removed'),
(4480, 75, 'I000756709', 'removed'),
(4481, 75, 'I000755196', 'removed'),
(4482, 75, 'I000758182', 'removed'),
(4483, 75, 'I000756533', 'removed'),
(4484, 75, 'I000752325', 'removed'),
(4485, 75, 'I000759218', 'removed'),
(4486, 75, 'I000757057', 'removed'),
(4487, 75, 'I000752956', 'removed'),
(4488, 75, 'I000757485', 'removed'),
(4489, 75, 'I000754447', 'removed'),
(4490, 75, 'I000754957', 'removed'),
(4491, 75, 'I000756002', 'removed'),
(4492, 75, 'I000753257', 'removed'),
(4493, 75, 'I000754696', 'removed'),
(4494, 75, 'I000752749', 'removed'),
(4495, 75, 'I000756800', 'removed'),
(4496, 75, 'I000752291', 'removed'),
(4497, 75, 'I000756167', 'removed'),
(4498, 75, 'I000753381', 'removed'),
(4499, 75, 'I000755576', 'removed'),
(4500, 75, 'I000759352', 'removed'),
(4501, 75, 'I000757342', 'removed'),
(4502, 75, 'I000757161', 'removed'),
(4503, 75, 'I000753820', 'removed'),
(4504, 75, 'I000754970', 'removed'),
(4505, 75, 'I000752878', 'removed'),
(4506, 75, 'I000756796', 'removed'),
(4507, 75, 'I000751049', 'removed'),
(4508, 75, 'I000759614', 'removed'),
(4509, 75, 'I000758053', 'removed'),
(4510, 75, 'I000759315', 'removed'),
(4511, 75, 'I000752349', 'removed'),
(4512, 75, 'I000752635', 'removed'),
(4513, 75, 'I000757754', 'removed'),
(4514, 75, 'I000753443', 'removed'),
(4515, 75, 'I000751462', 'removed'),
(4516, 75, 'I000752628', 'removed'),
(4517, 75, 'I000756609', 'removed'),
(4518, 75, 'I000758758', 'removed'),
(4519, 75, 'I000754754', 'removed'),
(4520, 75, 'I000758343', 'removed'),
(4521, 75, 'I000754573', 'removed'),
(4522, 75, 'I000758635', 'removed'),
(4523, 75, 'I000752750', 'removed'),
(4524, 75, 'I000757366', 'removed'),
(4525, 75, 'I000754030', 'removed'),
(4526, 75, 'I000757116', 'removed'),
(4527, 75, 'I000755777', 'removed'),
(4528, 75, 'I000756559', 'removed'),
(4529, 75, 'I000756450', 'removed'),
(4530, 75, 'I000756364', 'removed'),
(4531, 75, 'I000755697', 'removed'),
(4532, 75, 'I000752807', 'removed'),
(4533, 75, 'I000757896', 'removed'),
(4534, 75, 'I000754591', 'removed'),
(4535, 75, 'I000753995', 'removed'),
(4536, 75, 'I000754417', 'removed'),
(4537, 75, 'I000758802', 'removed'),
(4538, 75, 'I000757329', 'removed'),
(4539, 75, 'I000752863', 'removed'),
(4540, 75, 'I000753269', 'removed'),
(4541, 75, 'I000756186', 'removed'),
(4542, 75, 'I000753944', 'removed'),
(4543, 75, 'I000757090', 'removed'),
(4544, 75, 'I000756051', 'removed'),
(4545, 75, 'I000752994', 'removed'),
(4546, 75, 'I000758266', 'removed'),
(4547, 75, 'I000759945', 'removed'),
(4548, 75, 'I000753399', 'removed'),
(4549, 75, 'I000757916', 'removed'),
(4550, 75, 'I000758652', 'removed'),
(4551, 75, 'I000758980', 'removed'),
(4552, 75, 'I000758592', 'removed'),
(4553, 75, 'I000757257', 'removed'),
(4554, 75, 'I000753384', 'removed'),
(4555, 75, 'I000754813', 'removed'),
(4556, 75, 'I000753826', 'removed'),
(4557, 75, 'I000758217', 'removed'),
(4558, 75, 'I000751530', 'removed'),
(4559, 75, 'I000755941', 'removed'),
(4560, 75, 'I000755951', 'removed'),
(4561, 75, 'I000752428', 'removed'),
(4562, 75, 'I000757410', 'removed'),
(4563, 75, 'I000757768', 'removed'),
(4564, 75, 'I000755733', 'removed'),
(4565, 75, 'I000758851', 'removed'),
(4566, 75, 'I000755415', 'removed'),
(4567, 75, 'I000752062', 'removed'),
(4568, 75, 'I000755269', 'removed'),
(4569, 75, 'I000756384', 'removed'),
(4570, 75, 'I000757705', 'removed'),
(4571, 75, 'I000754776', 'removed'),
(4572, 75, 'I000759240', 'removed'),
(4573, 75, 'I000753412', 'removed'),
(4574, 75, 'I000755507', 'removed'),
(4575, 75, 'I000757750', 'removed'),
(4576, 75, 'I000754530', 'removed'),
(4577, 75, 'I000754152', 'removed'),
(4578, 75, 'I000757951', 'removed'),
(4579, 75, 'I000752535', 'removed'),
(4580, 75, 'I000759445', 'removed'),
(4581, 75, 'I000759478', 'removed'),
(4582, 75, 'I000751902', 'removed'),
(4583, 75, 'I000751677', 'removed'),
(4584, 75, 'I000752584', 'removed'),
(4585, 75, 'I000753828', 'removed'),
(4586, 75, 'I000751307', 'removed'),
(4587, 75, 'I000755223', 'removed'),
(4588, 75, 'I000753299', 'removed'),
(4589, 75, 'I000753505', 'removed'),
(4590, 75, 'I000756745', 'removed'),
(4591, 75, 'I000753442', 'removed'),
(4592, 75, 'I000751529', 'removed'),
(4593, 75, 'I000753911', 'removed'),
(4594, 75, 'I000753381', 'removed'),
(4595, 75, 'I000757880', 'removed'),
(4596, 75, 'I000758128', 'removed'),
(4597, 75, 'I000758349', 'removed'),
(4598, 75, 'I000751314', 'removed'),
(4599, 75, 'I000759654', 'removed'),
(4600, 75, 'I000756440', 'removed'),
(4601, 75, 'I000755316', 'removed'),
(4602, 75, 'I000753879', 'removed'),
(4603, 75, 'I000754689', 'removed'),
(4604, 75, 'I000756022', 'removed'),
(4605, 75, 'I000756056', 'removed'),
(4606, 75, 'I000753690', 'removed'),
(4607, 75, 'I000753440', 'removed'),
(4608, 75, 'I000751992', 'removed'),
(4609, 75, 'I000753730', 'removed'),
(4610, 75, 'I000754778', 'removed'),
(4611, 75, 'I000759049', 'removed'),
(4612, 75, 'I000758754', 'removed'),
(4613, 75, 'I000759726', 'removed'),
(4614, 75, 'I000753839', 'removed'),
(4615, 75, 'I000759248', 'removed'),
(4616, 75, 'I000753001', 'removed'),
(4617, 75, 'I000756627', 'removed'),
(4618, 75, 'I000755736', 'removed'),
(4619, 75, 'I000751179', 'removed'),
(4620, 75, 'I000753616', 'removed'),
(4621, 75, 'I000759933', 'removed'),
(4622, 75, 'I000759020', 'removed'),
(4623, 75, 'I000756819', 'removed'),
(4624, 75, 'I000759215', 'removed'),
(4625, 75, 'I000752545', 'removed'),
(4626, 75, 'I000757249', 'removed'),
(4627, 75, 'I000753657', 'removed'),
(4628, 75, 'I000751086', 'removed'),
(4629, 75, 'I000752559', 'removed'),
(4630, 75, 'I000758684', 'removed'),
(4631, 75, 'I000753803', 'removed'),
(4632, 75, 'I000758940', 'removed'),
(4633, 75, 'I000756464', 'removed'),
(4634, 75, 'I000753192', 'removed'),
(4635, 75, 'I000758311', 'removed'),
(4636, 75, 'I000759016', 'removed'),
(4637, 75, 'I000753340', 'removed'),
(4638, 75, 'I000756155', 'removed'),
(4639, 75, 'I000752710', 'removed'),
(4640, 75, 'I000751933', 'removed'),
(4641, 75, 'I000759170', 'removed'),
(4642, 75, 'I000759676', 'removed'),
(4643, 75, 'I000758145', 'removed'),
(4644, 75, 'I000751502', 'removed'),
(4645, 75, 'I000756066', 'removed'),
(4646, 75, 'I000758576', 'removed'),
(4647, 75, 'I000756178', 'removed'),
(4648, 75, 'I000751029', 'removed'),
(4649, 75, 'I000755786', 'removed'),
(4650, 75, 'I000757512', 'removed'),
(4651, 75, 'I000756484', 'removed'),
(4652, 75, 'I000757422', 'removed'),
(4653, 75, 'I000757496', 'removed'),
(4654, 75, 'I000759458', 'removed'),
(4655, 75, 'I000751079', 'removed'),
(4656, 75, 'I000754216', 'removed'),
(4657, 75, 'I000754220', 'removed'),
(4658, 75, 'I000753075', 'removed'),
(4659, 75, 'I000758273', 'removed'),
(4660, 75, 'I000759359', 'removed'),
(4661, 75, 'I000751189', 'removed'),
(4662, 75, 'I000757197', 'removed'),
(4663, 75, 'I000755881', 'removed'),
(4664, 75, 'I000757804', 'removed'),
(4665, 75, 'I000752188', 'removed'),
(4666, 75, 'I000754165', 'removed'),
(4667, 75, 'I000752281', 'removed'),
(4668, 75, 'I000752689', 'removed'),
(4669, 75, 'I000759393', 'removed'),
(4670, 75, 'I000757095', 'removed'),
(4671, 75, 'I000752111', 'removed'),
(4672, 75, 'I000759084', 'removed'),
(4673, 75, 'I000756438', 'removed'),
(4674, 75, 'I000751402', 'removed'),
(4675, 75, 'I000751528', 'removed'),
(4676, 75, 'I000754244', 'removed'),
(4677, 75, 'I000759718', 'removed'),
(4678, 75, 'I000758289', 'removed'),
(4679, 75, 'I000753853', 'removed'),
(4680, 75, 'I000754446', 'removed'),
(4681, 75, 'I000751638', 'removed'),
(4682, 75, 'I000755932', 'removed'),
(4683, 75, 'I000754803', 'removed'),
(4684, 75, 'I000757709', 'removed'),
(4685, 75, 'I000751072', 'removed'),
(4686, 75, 'I000751824', 'removed'),
(4687, 75, 'I000753845', 'removed'),
(4688, 75, 'I000758478', 'removed'),
(4689, 75, 'I000756304', 'removed'),
(4690, 75, 'I000754317', 'removed'),
(4691, 75, 'I000754119', 'removed'),
(4692, 75, 'I000759251', 'removed'),
(4693, 75, 'I000759522', 'removed'),
(4694, 75, 'I000754501', 'removed'),
(4695, 75, 'I000755652', 'removed'),
(4696, 75, 'I000759709', 'removed'),
(4697, 75, 'I000754441', 'removed'),
(4698, 75, 'I000752831', 'removed'),
(4699, 75, 'I000751652', 'removed'),
(4700, 75, 'I000752740', 'removed'),
(4701, 75, 'I000757030', 'removed'),
(4702, 75, 'I000756742', 'removed'),
(4703, 75, 'I000757661', 'removed'),
(4704, 75, 'I000759723', 'removed'),
(4705, 75, 'I000754623', 'removed'),
(4706, 75, 'I000756165', 'removed'),
(4707, 75, 'I000759368', 'removed'),
(4708, 75, 'I000752338', 'removed'),
(4709, 75, 'I000751939', 'removed'),
(4710, 75, 'I000753641', 'removed'),
(4711, 75, 'I000753564', 'removed'),
(4712, 75, 'I000754786', 'removed'),
(4713, 75, 'I000751694', 'removed'),
(4714, 75, 'I000753939', 'removed'),
(4715, 75, 'I000752425', 'removed'),
(4716, 75, 'I000757470', 'removed'),
(4717, 75, 'I000758937', 'removed'),
(4718, 75, 'I000759414', 'removed'),
(4719, 75, 'I000755257', 'removed'),
(4720, 75, 'I000752998', 'removed'),
(4721, 75, 'I000756480', 'removed'),
(4722, 75, 'I000752978', 'removed'),
(4723, 75, 'I000754098', 'removed'),
(4724, 75, 'I000752893', 'removed'),
(4725, 75, 'I000755916', 'removed'),
(4726, 75, 'I000752197', 'removed'),
(4727, 75, 'I000753337', 'removed'),
(4728, 75, 'I000754468', 'removed'),
(4729, 75, 'I000756701', 'removed'),
(4730, 75, 'I000752937', 'removed'),
(4731, 75, 'I000751330', 'removed'),
(4732, 75, 'I000754678', 'removed'),
(4733, 75, 'I000751750', 'removed'),
(4734, 75, 'I000755985', 'removed'),
(4735, 75, 'I000754794', 'removed'),
(4736, 75, 'I000757051', 'removed'),
(4737, 75, 'I000756658', 'removed'),
(4738, 75, 'I000755888', 'removed'),
(4739, 75, 'I000753788', 'removed'),
(4740, 75, 'I000756810', 'removed'),
(4741, 75, 'I000752141', 'removed'),
(4742, 75, 'I000753225', 'removed'),
(4743, 75, 'I000752839', 'removed'),
(4744, 75, 'I000753758', 'removed'),
(4745, 75, 'I000756678', 'removed'),
(4746, 75, 'I000758904', 'removed'),
(4747, 75, 'I000756906', 'removed'),
(4748, 75, 'I000752914', 'removed'),
(4749, 75, 'I000758037', 'removed'),
(4750, 75, 'I000756325', 'removed'),
(4751, 75, 'I000758674', 'removed'),
(4752, 75, 'I000755712', 'removed'),
(4753, 75, 'I000759293', 'removed'),
(4754, 75, 'I000755002', 'removed'),
(4755, 75, 'I000758243', 'removed'),
(4756, 75, 'I000759424', 'removed'),
(4757, 75, 'I000752714', 'removed'),
(4758, 75, 'I000758575', 'removed'),
(4759, 75, 'I000759336', 'removed'),
(4760, 75, 'I000757048', 'removed'),
(4761, 75, 'I000751724', 'removed'),
(4762, 75, 'I000752166', 'removed'),
(4763, 75, 'I000754475', 'removed'),
(4764, 75, 'I000758372', 'removed'),
(4765, 75, 'I000757632', 'removed'),
(4766, 75, 'I000753486', 'removed'),
(4767, 75, 'I000753384', 'removed'),
(4768, 75, 'I000752762', 'removed'),
(4769, 75, 'I000759851', 'removed'),
(4770, 75, 'I000756140', 'removed'),
(4771, 75, 'I000756839', 'removed'),
(4772, 75, 'I000758521', 'removed'),
(4773, 75, 'I000757180', 'removed'),
(4774, 75, 'I000752559', 'removed'),
(4775, 75, 'I000757845', 'removed'),
(4776, 75, 'I000755863', 'removed'),
(4777, 75, 'I000751620', 'removed'),
(4778, 75, 'I000751853', 'removed'),
(4779, 75, 'I000758464', 'removed'),
(4780, 75, 'I000755975', 'removed'),
(4781, 75, 'I000757191', 'removed'),
(4782, 75, 'I000757683', 'removed'),
(4783, 75, 'I000756088', 'removed'),
(4784, 75, 'I000757362', 'removed'),
(4785, 75, 'I000759918', 'removed'),
(4786, 75, 'I000752020', 'removed'),
(4787, 75, 'I000758952', 'removed'),
(4788, 75, 'I000754738', 'removed'),
(4789, 75, 'I000756288', 'removed'),
(4790, 75, 'I000751659', 'removed'),
(4791, 75, 'I000757770', 'removed'),
(4792, 75, 'I000753525', 'removed'),
(4793, 75, 'I000752883', 'removed'),
(4794, 75, 'I000751891', 'removed'),
(4795, 75, 'I000756808', 'removed'),
(4796, 75, 'I000751163', 'removed'),
(4797, 75, 'I000752658', 'removed'),
(4798, 75, 'I000759869', 'removed'),
(4799, 75, 'I000757427', 'removed'),
(4800, 75, 'I000754118', 'removed'),
(4801, 75, 'I000758238', 'removed'),
(4802, 75, 'I000758296', 'removed'),
(4803, 75, 'I000758315', 'removed'),
(4804, 75, 'I000758149', 'removed'),
(4805, 75, 'I000754827', 'removed'),
(4806, 75, 'I000756218', 'removed'),
(4807, 75, 'I000758531', 'removed'),
(4808, 75, 'I000753958', 'removed'),
(4809, 75, 'I000754267', 'removed'),
(4810, 75, 'I000756291', 'removed'),
(4811, 75, 'I000755954', 'removed'),
(4812, 75, 'I000758335', 'removed'),
(4813, 75, 'I000752621', 'removed'),
(4814, 75, 'I000757610', 'removed'),
(4815, 75, 'I000759835', 'removed'),
(4816, 75, 'I000757210', 'removed'),
(4817, 75, 'I000759304', 'removed'),
(4818, 75, 'I000759724', 'removed'),
(4819, 75, 'I000757864', 'removed'),
(4820, 75, 'I000754722', 'removed'),
(4821, 75, 'I000756789', 'removed'),
(4822, 75, 'I000752334', 'removed'),
(4823, 75, 'I000757067', 'removed'),
(4824, 75, 'I000752111', 'removed'),
(4825, 75, 'I000759344', 'removed'),
(4826, 75, 'I000752367', 'removed'),
(4827, 75, 'I000752714', 'removed'),
(4828, 75, 'I000759093', 'removed'),
(4829, 75, 'I000751186', 'removed'),
(4830, 75, 'I000759525', 'removed'),
(4831, 75, 'I000755129', 'removed'),
(4832, 75, 'I000758568', 'removed'),
(4833, 75, 'I000755463', 'removed'),
(4834, 75, 'I000755512', 'removed'),
(4835, 75, 'I000757394', 'removed'),
(4836, 75, 'I000756719', 'removed'),
(4837, 75, 'I000753952', 'removed'),
(4838, 75, 'I000752456', 'removed'),
(4839, 75, 'I000757930', 'removed'),
(4840, 75, 'I000758393', 'removed'),
(4841, 75, 'I000753933', 'removed'),
(4842, 75, 'I000755025', 'removed'),
(4843, 75, 'I000752915', 'removed'),
(4844, 75, 'I000757002', 'removed'),
(4845, 75, 'I000754633', 'removed'),
(4846, 75, 'I000753844', 'removed'),
(4847, 75, 'I000751176', 'removed'),
(4848, 75, 'I000752575', 'removed'),
(4849, 75, 'I000758179', 'removed'),
(4850, 75, 'I000753052', 'removed'),
(4851, 75, 'I000758222', 'removed'),
(4852, 75, 'I000758586', 'removed'),
(4853, 75, 'I000754979', 'removed'),
(4854, 75, 'I000755149', 'removed'),
(4855, 75, 'I000754860', 'removed'),
(4856, 75, 'I000756827', 'removed'),
(4857, 75, 'I000751817', 'removed'),
(4858, 75, 'I000755918', 'removed'),
(4859, 75, 'I000752916', 'removed'),
(4860, 75, 'I000757979', 'removed'),
(4861, 75, 'I000755188', 'removed'),
(4862, 75, 'I000755594', 'removed'),
(4863, 75, 'I000752428', 'removed'),
(4864, 76, 'I000769605', 'removed'),
(4865, 76, 'I000761317', 'removed'),
(4866, 76, 'I000765928', 'removed'),
(4867, 76, 'I000763177', 'removed'),
(4868, 76, 'I000769804', 'removed'),
(4869, 76, 'I000767481', 'removed'),
(4870, 76, 'I000762708', 'removed'),
(4871, 76, 'I000764911', 'removed'),
(4872, 76, 'I000768740', 'removed'),
(4873, 76, 'I000767654', 'removed'),
(4874, 76, 'I000766558', 'removed'),
(4875, 76, 'I000764520', 'removed'),
(4876, 76, 'I000762646', 'removed'),
(4877, 76, 'I000763387', 'removed'),
(4878, 76, 'I000767340', 'removed'),
(4879, 76, 'I000763780', 'removed'),
(4880, 76, 'I000768544', 'removed'),
(4881, 76, 'I000765116', 'removed'),
(4882, 76, 'I000765095', 'removed'),
(4883, 76, 'I000768568', 'removed'),
(4884, 76, 'I000765172', 'removed'),
(4885, 76, 'I000767052', 'removed'),
(4886, 76, 'I000765280', 'removed'),
(4887, 76, 'I000768702', 'removed'),
(4888, 76, 'I000768670', 'removed'),
(4889, 76, 'I000764256', 'removed'),
(4890, 76, 'I000761688', 'removed'),
(4891, 76, 'I000761611', 'removed'),
(4892, 76, 'I000765269', 'removed'),
(4893, 76, 'I000763463', 'removed'),
(4894, 76, 'I000768211', 'removed'),
(4895, 76, 'I000766689', 'removed'),
(4896, 76, 'I000767044', 'removed'),
(4897, 76, 'I000769473', 'removed'),
(4898, 76, 'I000767438', 'removed'),
(4899, 76, 'I000769917', 'removed'),
(4900, 76, 'I000763327', 'removed'),
(4901, 77, 'I000775620', 'available'),
(4902, 77, 'I000775248', 'available'),
(4903, 77, 'I000776926', 'available'),
(4904, 77, 'I000776156', 'available'),
(4905, 77, 'I000773419', 'available'),
(4906, 78, 'I000785030', 'available'),
(4907, 78, 'I000781025', 'available'),
(4908, 78, 'I000783998', 'available'),
(4909, 78, 'I000786531', 'available'),
(4910, 78, 'I000783344', 'available'),
(4911, 78, 'I000788643', 'available'),
(4912, 78, 'I000786979', 'available'),
(4913, 79, 'I000799014', 'available'),
(4914, 79, 'I000791003', 'available'),
(4915, 79, 'I000793735', 'available');

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
(7, 7.617231, 80.343330),
(8, 7.660786, 80.173042),
(9, 7.502877, 80.255440),
(10, 7.791425, 80.299385),
(11, 7.720667, 80.282905),
(12, 7.622675, 80.288399),
(13, 7.633565, 80.398262),
(14, 7.688006, 80.227974),
(15, 7.742440, 80.310371);

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
(51, '2024-04-14 11:01:18', 'completed', 15400.00, NULL, 'RNT00051'),
(52, '2024-04-20 13:01:12', 'completed', 5255.00, NULL, 'RNT00052'),
(53, '2024-04-21 04:29:17', 'pending', 2900.00, NULL, 'RNT00053'),
(54, '2024-04-21 05:48:16', 'pending', 5000.00, NULL, 'RNT00054'),
(55, '2024-04-21 06:30:33', 'pending', 28900.00, NULL, 'RNT00055'),
(56, '2024-04-21 06:33:06', 'pending', 7400.00, NULL, 'RNT00056'),
(57, '2024-04-21 06:58:12', 'completed', 4400.00, NULL, 'RNT00057'),
(58, '2024-04-21 07:00:21', 'completed', 6500.00, NULL, 'RNT00058'),
(59, '2024-04-21 07:03:05', 'completed', 3900.00, NULL, 'RNT00059');

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
(79, 32, 25, '2024-04-24', '2024-04-30', 'completed', NULL, 12000.00, 0.00, '2024-04-15 15:52:49', '2024-04-14 11:01:18'),
(80, 32, 25, '2024-04-26', '2024-05-01', 'accepted', NULL, 5255.00, 0.00, '2024-04-20 13:07:44', '2024-04-20 13:01:12'),
(81, 32, 56, '2024-04-25', '2024-04-30', 'pending', NULL, 2900.00, 0.00, '2024-04-21 04:29:17', '2024-04-21 04:29:17'),
(82, 32, 25, '2024-04-25', '2024-04-30', 'pending', NULL, 5000.00, 0.00, '2024-04-21 05:48:16', '2024-04-21 05:48:16'),
(83, 32, 56, '2024-04-30', '2024-06-04', 'pending', NULL, 28900.00, 0.00, '2024-04-21 06:30:33', '2024-04-21 06:30:33'),
(84, 32, 56, '2024-10-22', '2024-11-05', 'pending', NULL, 7400.00, 0.00, '2024-04-21 06:33:06', '2024-04-21 06:33:06'),
(85, 32, 56, '2024-04-22', '2024-04-30', 'pending', NULL, 4400.00, 0.00, '2024-04-21 06:58:12', '2024-04-21 06:58:12'),
(86, 32, 56, '2024-04-23', '2024-04-30', 'pending', NULL, 6500.00, 0.00, '2024-04-21 07:00:21', '2024-04-21 07:00:21'),
(87, 32, 56, '2024-04-23', '2024-04-30', 'pending', NULL, 3900.00, 0.00, '2024-04-21 07:03:05', '2024-04-21 07:03:05');

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
(25, 'ACC Rent ', ' Colombo 04', 'B873242343', '076024489', 87, 'waiting', '', 3, '661df1d72bc83.png'),
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
(57, 'NS yudufc', 'No 255, Neluwa RD', '200187674509', '+94716024489', 188, 'waiting', '6618f1fe4bdb3.pdf', 7, '1.webp'),
(58, 'Donavon Carter', '178 Dooley Inlet', '0786579984', '0983237761', 208, 'waiting', '662202d002d3e.pdf', 12, '1.webp'),
(59, 'Assunta Upton', '237 Lessie Forest', '0786579984', '0983237761', 209, 'waiting', '6622065cd9035.pdf', 13, '1.webp'),
(60, 'Ivah Hilpert', '678 Jackeline Vista', '078657998456', '0983237761', 213, 'waiting', '66223236b2958.pdf', 14, '1.webp');

-- --------------------------------------------------------

--
-- Table structure for table `rental_settings`
--

CREATE TABLE `rental_settings` (
  `id` int NOT NULL,
  `rentalservice_id` int NOT NULL,
  `renting_state` tinyint(1) NOT NULL DEFAULT '1',
  `recovery_period` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rental_settings`
--

INSERT INTO `rental_settings` (`id`, `rentalservice_id`, `renting_state`, `recovery_period`) VALUES
(1, 25, 1, 3),
(1, 25, 1, 3);

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
(221, 79, 1323),
(222, 80, 1324),
(223, 80, 2407),
(225, 81, 1379),
(226, 82, 1325),
(227, 83, 1377),
(228, 83, 1374),
(230, 84, 1377),
(231, 85, 2314),
(232, 86, 2315),
(233, 86, 2341),
(235, 87, 2316);

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
(48, 79, 51, 12000.00),
(49, 80, 52, 5255.00),
(50, 81, 53, 2900.00),
(51, 82, 54, 5000.00),
(52, 83, 55, 28900.00),
(53, 84, 56, 7400.00),
(54, 85, 57, 4400.00),
(55, 86, 58, 6500.00),
(56, 87, 59, 3900.00);

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
(20, 79, 'rented', 'completed', '2024-04-15 15:52:49'),
(21, 80, NULL, NULL, '2024-04-22 04:17:29'),
(22, 81, NULL, NULL, '2024-04-21 04:29:17'),
(23, 82, NULL, NULL, '2024-04-21 05:48:16'),
(24, 83, NULL, NULL, '2024-04-21 06:30:33'),
(25, 84, NULL, NULL, '2024-04-21 06:33:06'),
(26, 85, NULL, NULL, '2024-04-21 06:58:12'),
(27, 86, NULL, NULL, '2024-04-21 07:00:21'),
(28, 87, NULL, NULL, '2024-04-21 07:03:05');

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
(87, 'rental@wl.com', 'e5Oir2upu0zHM6K99f2zKQ==:a3e3de47aea36e108b45934344c8563f02eb9eb2ab294113a487386cec6d6023', '2023-11-29 11:01:02', 'rentalservice', 1),
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
(167, 'customer@wl.com', 'x4xFTR2fX6PGBC1zi3absw==:5dd74d32e2abdb83ee770b30afa6ea74f00566c3f9af30028b898ae4636c2b05', '2023-12-11 10:13:22', 'customer', 1),
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
(188, 'nirmalsavinda29@gmail.com', 'YwTBygsPyWuKYcxhhH2few==:e26d79a1159f24ff1b9d995681c0d8d92bc163dceb423345e25be1d5237b0145', '2024-04-12 08:33:58', 'rentalservice', 0),
(189, 'your.email+fakedata12747@gmail.com', 'Z9gTbWthbpX3aH3SnT6kBQ==:78b39cc2f8399d92b3df9eb82d45549cb7750f1f14709fe0bf2282ac61f4ad99', '2024-04-18 04:52:14', 'customer', 0),
(190, 'admin@wl.com', 'Yt2RE52k8TxZHMS8uKX5sg==:04ce8f5c27e9ce8341e44b08770ff01002f36de3ab300fa4ab379905f9913269', '2024-04-18 14:15:26', 'customer', 0),
(191, 'your.email+fakedata61727@gmail.com', '3E3cLpeXRE4RK3SeQgiH1w==:5b1ab93837d6075c1a5f81bffd0da250fe6723bd835ef39bbdcf75e2abce6139', '2024-04-18 14:20:01', 'customer', 0),
(192, 'your.email+fakedata61727@gmail.com', 'eHxWLhppPGW2EXsZSEmLqg==:7890f3ed7702d6964dd32a686a155ab81160c5f3dbdf63a8d28ad3d0a9141311', '2024-04-18 14:20:07', 'customer', 0),
(193, 'your.email+fakedata46120@gmail.com', 'rtYweniEhU1MdOcq/UwnBQ==:673ce8a23872f5e8f11f1cc4e13459fb4d3ada0b4471c1fe31be728a66f96c81', '2024-04-18 14:21:50', 'customer', 0),
(194, 'your.email+fakedata46120@gmail.com', '7tTiRwtl3SGZUQnBbyyoMw==:5489dfc2472b05e112ce7a087d99fddc73c4be0507300598302ea06890895182', '2024-04-18 14:21:57', 'customer', 0),
(195, 'your.email+fakedata46120@gmail.com', 'MgOkoP+HbMkSxmj2YVwtwg==:aa92732d710efad51e490126b12b9b4b9b107518e10392bce772d1260bce8696', '2024-04-18 14:22:05', 'customer', 0),
(196, 'your.email+fakedata46120@gmail.com', 'Xuqf58Tz6clAqLWVcSxecw==:038af204b14434c7c84d731d2498dbfd4170810401202fe41ae6a0462f795d84', '2024-04-18 14:22:15', 'customer', 0),
(197, 'your.email+fakedata46120@gmail.com', '0tArCKFK5VZJRIuLpNhbfw==:36424049561ade3cfa42a3f7f2f79f0484579897296475bd84399c660f46322c', '2024-04-18 14:22:23', 'customer', 0),
(198, 'your.email+fakedata46120@gmail.com', 'flIxgPV6k3tqat/EKta5bg==:ffed18caa6f428f28960a1a818d7fa986a81f8870babc16a1db34c3b6fc4273b', '2024-04-18 14:22:30', 'customer', 0),
(199, 'your.email+fakedata46120@gmail.com', 'QTHmMAWHac+RoL/U2f1KzA==:c55d464ad74de85517ee8cce4f55a2009489f4c0e4cfcd253401115f746d64ac', '2024-04-18 14:22:37', 'customer', 0),
(200, 'your.emailfakedata85937@gmail.com', 'I8HW6LWpYoMkNJLPjlv2jg==:5ab67af1db087e7b29ab35f34705cd1a96e11015a294a2683d3b0c4cba16713f', '2024-04-18 14:28:13', 'customer', 0),
(201, 'your.emailfakedata85937@gmail.com', '+5UbIaoizspPGE1DKUC0CA==:d98f3229569f4c3a7a9437880e14446ff7e4fee65057ab3b352bf8ffd88f69d1', '2024-04-18 14:28:28', 'customer', 0),
(202, 'nirmal@gmail.com', '9YTSyogH1bGhboOS1rPtvg==:21a11e21f135b1a728dc937c4d16a07c88cf93f9957095dc1fc3c13cbe485748', '2024-04-19 03:34:28', 'guide', 0),
(203, 'yourta32669@gmail.com', 'Z6fGWQw9BxydWN3ij3NA4A==:b865d27560a0399ba019207f7b677dc4e59cf55b1f1f82c0ace4e35ab6aa62ad', '2024-04-19 03:39:01', 'guide', 0),
(204, 'yokedata20741@gmail.com', 'pgF01s9h1RJstpUy4BtJUQ==:6219885b17e60630eefd4579e073d03bda7a226693a6e992e67d1b9fc3a2f755', '2024-04-19 03:40:46', 'guide', 0),
(205, 'your045@gmail.com', 'XKbIECL783rhI97ZUwTb7g==:2af1a4ebdeeba3c14dd3f60bec626be191e19bd09596cdf745e953bf35501bb8', '2024-04-19 03:44:20', 'guide', 0),
(206, 'yokedata23376@gmail.com', 'f/bXm3WGr1JUpvqcsJUsgw==:bf77483f3bc12320e515c253529c5a63f46b34a7628aebe14f40f8080ae2eb60', '2024-04-19 03:47:39', 'guide', 0),
(207, 'youakedata51067@gmail.com', 'cbZuAJyCZmpIZNHDvuXnIw==:06a46c0e6ab3cf457ccf682998a704e046d6169cdf52cc0cdd700da181311af1', '2024-04-19 03:50:20', 'guide', 0),
(208, 'your.edata11970@gmail.com', 'AQ/lR0NHzqTv2T0ypR0Z/g==:2da8e36c695432f73df886c5a8808b18c7c2df28c27231dc96b0337a0eeaf2ab', '2024-04-19 05:36:07', 'rentalservice', 0),
(209, 'yoata73237@gmail.com', 'ImOUWFMMAL1qNNpQcdIxxQ==:02a6738e41f2724f6d1be86e21eb4a21d75ee1a75f46d3663c064929b79f551a', '2024-04-19 05:51:19', 'rentalservice', 0),
(210, 'data29524@gmail.com', '3LLLoIuwA1xUAuwMgY5aUg==:cb508129c1a94d720a57f2eee90065dfca8f80597d71b2a5a1f7d713ec0c9c7e', '2024-04-19 06:17:19', 'customer', 0),
(211, 'kedata78741@gmail.com', 's8hoDnThHWddmJr8xckrPA==:38e53469a854c28b3dbb88298d18f0fb224e40ec17836bfe280e6c855bde5181', '2024-04-19 06:23:39', 'customer', 0),
(212, 'your.emkedata59201@gmail.com', 'gU9/EquRizZpK4A6VIA6Sw==:fa5af7cca7974e49430644553f95daa26b63454fdd6abe67a95f63c0ec50924e', '2024-04-19 08:56:40', 'rentalservice', 0),
(213, 'your.ekedata98367@gmail.com', 'S1iU5yewZ3KfAJKuPl92Lw==:726b1a3af80f110ea4d1dc03dccdb7fb1ec51ce59ec76d558c58110971e12195', '2024-04-19 08:58:25', 'rentalservice', 0),
(214, 'custome@wl.com', 'ueLy+IVCjW0mYv8R/hQJGg==:aeef2cce9cedda04d6016acfa22383dde2b72162ed2375528c80459306f42995', '2024-04-20 07:07:45', 'customer', 0),
(215, 'rental3@wl.com', 's7lAGbhdUKrzeJLQykS7Wg==:6eedd6664b75a11a715fe5ac14387e4946c2dbbdc5808a22e7d0393c9f9182dd', '2024-04-20 08:23:15', 'guide', 0);

-- --------------------------------------------------------

--
-- Table structure for table `verification`
--

CREATE TABLE `verification` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `verification`
--

INSERT INTO `verification` (`id`, `user_id`, `token`, `email`) VALUES
(1, 145, '3497b51d91fa0d6e77c484c7c45123d7869a74efc93ab65edce935d21edb6e8c', NULL),
(2, 146, '61316b40745df5786873f4b0922b608e9a7d5bd00b2f7d1ab1ba3b6eeae15422', NULL),
(3, 147, '917453148aa76c114010b85452505f4c066fd4a8697fadf336a209996685dac6', NULL),
(4, 148, '6e88bcdea933dc413ab6e40361354e4313a72682930033ad92260604ad739efe', NULL),
(5, 149, 'c694a8063d49554c069b9c55fba92119f83b4de8ec706bdcd68f15654f85f081', NULL),
(6, 151, '587fd4304718d888543f7e030e5f392b63c1f827fc6d081a810266c405ce1187', NULL),
(7, 152, 'd546f6801b3923f7b7e15aeac7f3f3822ce706d86d34591fb380acfa64e05b27', NULL),
(8, 153, '9bcfbed773f2e031edf569d85f96f6c79ddefc379ba37cfad2d9df5ad5bd0619', NULL),
(10, 155, 'bc61a9f93eceba5c69df61e177a89b69c144eb4b765cc5e00c9781202b56d744', NULL),
(11, 156, 'fae797b5b593427c95fadd8a48fdcb44975611c0f97f3cc71dc54ea8ae787f32', NULL),
(12, 157, '2557ecdbaff820916f6914176e1435d1da581792d93987f2251a9192ae7de597', NULL),
(13, 158, '9f9abcaabf52b5f957dc872cbd56b8f30d87ba0514465837b759635240614a99', NULL),
(14, 159, '812b8bfc68e79270a5b5f6bd00658209939f4baed29ebf5ba8a41a714f9f5668', NULL),
(21, 166, '539ed68f05d8dc53e45dc2b6d75f0e4177ed06e07fcc7037765900cc831e6277', NULL),
(22, 167, 'a2e31b324f89ebb6d301e9184a54ab3e715303721328e4b7da000532c4ee72cf', NULL),
(23, 168, '867a77127f00ba4d60b02b92323c66c7d2071145d46c344d858a5053c2bfc808', NULL),
(24, 169, '8ea5ecef686595578c37a5c06bc296be38b60ee65f1157e503e59b1ecd4adb0f', NULL),
(25, 170, '434de12da0921c4093c43bfcff894235c499b1527b23949e7c956f888cf19c92', NULL),
(26, 171, '4017a52d3963467f613689fc1517db1d6358976f741636276efff7ee86e9c7f7', NULL),
(27, 172, 'e84b859d8fa654d0d5cac46b4d3ee2ee7d068d9783d84e911e37cd5edd947fcb', NULL),
(28, 173, '9972b6a3c801207287e2180bea1c39e24b3ff1858d75b891c13cd19f9d895105', NULL),
(29, 174, 'fb69d7fe658d2ef1bb643a44544831107dbe12097769a78fced32432c2d94e7d', NULL),
(30, 175, 'd95a34bb078967d011443cd1123f852705e8a1fd1cff8f4bd9adfc31b36fb4f1', NULL),
(31, 176, '70151ca2ffcc32f245896067f53fbe2e5bf03429fc64f5a705d53097be366ed3', NULL),
(32, 177, 'a4db3f8ff94b60f23f46bf59893796d70e3a122ca2817905461573e82ad98a12', NULL),
(33, 178, 'd4b507ad4a0b55bbb58e91c0495da9cf6acf63363cf9db80219249f7670cb6d6', NULL),
(34, 179, '1a24dc19f995a894a9edc9941d33bbddd76abd9396e38071efe7923709f7fd10', NULL),
(35, 180, '1991dbfb82d03f54acf8aecddb77e5629e4b460554678cde9a995c1b725d8975', NULL),
(36, 181, 'c6748fabe7b54060ccca007db651292b38209cc6f2474adb52b8539e54b5209a', NULL),
(37, 182, '9c3853ac31bab21669937bc823a193be9fdd91a54657d4654d55a7451a5cc1e8', NULL),
(38, 183, '972b68f449d92a7ac191e2c5da80ce07b17cc3cdc6a0a8d724dd4805df47f1d2', NULL),
(39, 184, '915049bb93289d163f0c45d2117c70c6b812de13d07e4206cc57e3c72908c082', NULL),
(40, 185, 'e7f8cb3f2d57da1348d4629cff4c3b6d09668f6863bac5c8ddf42b05e71e9b34', NULL),
(41, 186, '35910b32293477e3e0c88e39a07dcb74e74be3e904068470603ea9c9383a5778', NULL),
(42, 187, '1efc80fd832b09b6a13def963cb4dfcedee0fb48bad0e6c9c8df74085de27046', NULL),
(43, 188, '90225ee472fb6633b6d2b2616b63d96ccf0961fa17f21fca772d914166c20d61', NULL),
(44, 189, 'aff356157089bd7ad09b3c8bf041e0769f1cf9f7ea6f1ef4dcf275f50c67f2bf', 'your.email+fakedata12747@gmail.com'),
(45, 87, '92c67ae1285ff5987a5921f943696bdccc99c87451ac131dcf27933a4cbfa137', 'rental@wl.com'),
(47, 87, 'b8f7b338d1f8b352cf3c4392f8960c1fcfda1208f740991d7532a1ce6548cf48', 'rent@dk.com'),
(49, 87, 'cf39e6e51d2b1da98783aeade948a0340fca9d6bcdcfff4338f5c12f4342d624', ''),
(50, 87, '4992979577a24b845679ed23e92993cd7fc8b04fd6e4609fc01483f5293b537f', 'fsggg@fgdfg.com'),
(51, 87, '75feebba31806c7559d9d78c07d70c192483b667aad17855a9fe6cc8e3549bdf', 'fsggg@fgdfg.com'),
(52, 87, 'a266396123e8a6c02bebc3707466bc481e2a402a73bff3781fefd4c6817f67ac', 'fsggg@fgdfg.com'),
(53, 87, 'c6e91d0ff1bada4fd3491fb0aa38863f0190e5200e1d8ef93f8726658c488ad0', 'fdsafcsdf@dfsdf.cds'),
(54, 190, 'adfe4150e6b420df44769d8a7193fb062615f3fb7d1d1cdb36be44405a015227', 'admin@wl.com'),
(55, 191, '15bc9fdce819dac9fc8c7726694578b4dc2d6f8a9d8e733c056cf1fc343bc605', 'your.email+fakedata61727@gmail.com'),
(56, 192, '632163bb556f212cf42d716380d04ef461d9799972d2d942c40e108a38b309b2', 'your.email+fakedata61727@gmail.com'),
(57, 193, '8ec3d93a2056d0f22f1d15fe4d075de24346b1abd10ecd05e6ddc396db3c6a0d', 'your.email+fakedata46120@gmail.com'),
(58, 194, '3ff3ec6034bb8a591dfc1db1f8bf0162d7d31e08e35f15a6620509e83acdc44f', 'your.email+fakedata46120@gmail.com'),
(59, 195, 'c3b91cd3c1416a17a9d24a8024e4b444c71748d54c6f9d152c05608a1a89a7e2', 'your.email+fakedata46120@gmail.com'),
(60, 196, '6cba0cdd0b494eb06085619077eca63bbb3ca77fb0c726906de67c897a1249a5', 'your.email+fakedata46120@gmail.com'),
(61, 197, 'b969492171c68ce263a205bb3d2ce4961e07e635b95bf911867c6fc0c84e7730', 'your.email+fakedata46120@gmail.com'),
(62, 198, '00cd222d69123c911436e77aa5bf5101c4c82320bb3dd49e67fcbe36daca729f', 'your.email+fakedata46120@gmail.com'),
(63, 199, 'c0813d1a4449925d657ee8cb159ef9ca19278bf93abb6e38ff162cac046b80ff', 'your.email+fakedata46120@gmail.com'),
(64, 200, 'd04ed060c95ce6d3db0140756c56418a6a41d837cc9cb40024a5d31d418098e4', 'your.emailfakedata85937@gmail.com'),
(65, 201, 'ad67e59b0bd804b91f240a2bb0d32b2b73505cef66e966f042bd4a3fec6aca05', 'your.emailfakedata85937@gmail.com'),
(66, 202, '7b4211e11d817d62b6369608e6d3d46e5c9aa1346cb60eebcf3f73a635f6a02d', 'nirmal@gmail.com'),
(67, 203, '1a2fb482e40e2367f27b31b0acc95fc61abb6a08760a73c179d7d88707ae84d1', 'yourta32669@gmail.com'),
(68, 204, '557c58b1ac6550e12b6975e4b5a0f6aa470ff95175053834113b56285628de5a', 'yokedata20741@gmail.com'),
(69, 205, '46b4bb1dbf3a1a86d2a8a962b716a516b1d6287fc264a5079d170c8b4a9c5b4a', 'your045@gmail.com'),
(70, 206, 'b884f79152172a8b651c024b1465db4a4ad3655701c71ab88d898fcb1897de88', 'yokedata23376@gmail.com'),
(71, 207, 'dfa56f2c0df260f83971a91993cec85a79d8e3dd06192133349c9d93ec391b13', 'youakedata51067@gmail.com'),
(72, 208, '92ca225f4c2427a9229eebd4c43a6bd55b37504871839192762ffdb419f953d2', 'your.edata11970@gmail.com'),
(73, 209, '59d9e28d87100fd61743d896ff68d84a04d2c7010dc13ed4568dbeb32e0ba57c', 'yoata73237@gmail.com'),
(74, 210, 'e44a89bcc362856e7b89e1e11a2b6ec723bc904497e7ff3e0ab21ee80763570d', 'data29524@gmail.com'),
(75, 211, 'a38707e3a4b975a5aa80120edd0a71d1142f554c9711534f9b2ec9235388683a', 'kedata78741@gmail.com'),
(76, 212, '74d388152c33a663ab29e8540d01f84e2fb9fab358b44a51b10dee7a578d360a', 'your.emkedata59201@gmail.com'),
(77, 213, '516b51eb4497e7242c311613831a4f4ecb047bf3e95557e00e7bc557fce3a697', 'your.ekedata98367@gmail.com'),
(78, 214, 'a6cd25d7daefc34788926ee12b87737d5998426ed0f233658a2f6dfec177a39d', 'custome@wl.com'),
(79, 215, '2e014b3fc71f736644010a2f5a142b381241ab8ab74d17265700c9aefce83f2b', 'rental3@wl.com');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4916;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `rent`
--
ALTER TABLE `rent`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `rental_services`
--
ALTER TABLE `rental_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `rent_item`
--
ALTER TABLE `rent_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `rent_pay`
--
ALTER TABLE `rent_pay`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `rent_request`
--
ALTER TABLE `rent_request`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT for table `verification`
--
ALTER TABLE `verification`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

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
