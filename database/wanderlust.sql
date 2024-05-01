-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: May 01, 2024 at 12:04 PM
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
CREATE DATABASE IF NOT EXISTS `wanderlust` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `wanderlust`;

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

CREATE DEFINER=`root`@`%` PROCEDURE `CreatePaymentForGuide` (IN `package_id` INT)   BEGIN
    DECLARE lastPaymentID INT;
    DECLARE packagePrice DECIMAL(10, 2);
    DECLARE reference_number VARCHAR(20);
    DECLARE paymentID INT;

    -- Inserting a new payment with status 'completed'
    -- INSERT INTO payment (status) VALUES ('pending');

    -- Retrieve price from package table
    SELECT price INTO packagePrice FROM package WHERE package.id = package_id;
    
    -- Inserting into booking_pay table
    INSERT INTO payment (amount, status) VALUES (packagePrice, 'pending');

	SET lastPaymentID = LAST_INSERT_ID();

    -- Generating reference number
    SET reference_number = CONCAT('GD', LPAD(lastPaymentID, 5, '0'));
	UPDATE payment SET reference_number = reference_number where id=lastPaymentID;

    -- Retrieve the newly inserted payment ID
    SET paymentID = lastPaymentID;
    
    -- Return reference number and payment ID
    SELECT reference_number AS bookingID, paymentID AS payment_id, packagePrice AS amount;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `CreatePaymentForRent` (IN `rent_id` INT)   BEGIN
    DECLARE lastPaymentID INT;
    DECLARE reference_number VARCHAR(10);
    DECLARE amount DECIMAL(10, 2);

    -- Fetching the outstanding amount for the given rent ID
    SELECT total - paid_amount INTO amount FROM rent WHERE id = rent_id;

    -- Insert a new payment entry
    INSERT INTO payment (amount, status) VALUES (amount, 'pending');

    -- Get the ID of the last inserted payment
    SET lastPaymentID = LAST_INSERT_ID();

    -- Create a reference number using the last payment ID
    SET reference_number = CONCAT('RNT', LPAD(lastPaymentID, 5, '0'));

    -- Update the payment with the reference number
    UPDATE payment SET reference_number = reference_number WHERE id = lastPaymentID;

    -- Insert into rent_pay table
    INSERT INTO rent_pay (rent_id, payment_id, amount) VALUES (rent_id, lastPaymentID, amount);

    -- Select the reference number and total amount for the output
    SELECT reference_number AS orderID, amount AS totalAmount;
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

CREATE DEFINER=`root`@`%` PROCEDURE `GetAllMonthlyIncome` (IN `start_date` DATE, IN `end_date` DATE)   BEGIN
    SELECT 
        DATE_FORMAT(rent.end_date, '%Y-%m') AS `Month`,
        SUM(total) AS `MonthlyIncome`
    FROM 
        `rent` 
    WHERE 
    	status = 'completed'
        AND rent.end_date BETWEEN start_date AND end_date
    GROUP BY 
        DATE_FORMAT(rent.end_date, '%Y-%m')
    ORDER BY 
        DATE_FORMAT(rent.end_date, '%Y-%m');
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
          i.status = 'available'  AND
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

CREATE DEFINER=`root`@`%` PROCEDURE `GetCustomerDetailsByBookingID` (IN `booking_id` INT)   BEGIN
    SELECT
        c.name AS customer_name,
        c.number AS customer_number
    FROM
        guide_booking gb
    INNER JOIN
        customers c ON gb.customer_id = c.id
    WHERE
        gb.id = booking_id;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetEquipmentRentalCountByRental` (IN `start_date` DATE, IN `end_date` DATE, IN `rentalservice_id` INT)   BEGIN
    SELECT 
        e.name AS equipment_name,
        e.id AS equipment_id,
        COUNT(r.id) AS rental_count
    FROM rent r
    JOIN rent_item ri ON r.id = ri.rent_id
    JOIN item i ON ri.item_id = i.id
    JOIN equipment e ON i.equipment_id = e.id
    WHERE r.rentalservice_id = rentalservice_id 
      AND r.start_date >= start_date
      AND r.end_date <= end_date
      AND r.status IN ('rented', 'completed') -- Assumes these statuses mean the equipment was in use
    GROUP BY e.id, e.name
    ORDER BY rental_count DESC;
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
            SET @specificFilter = "AND rent.start_date <= CURDATE() AND rent.status = 'accepted'";
        
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

CREATE DEFINER=`root`@`%` PROCEDURE `GetGuideIdByPackageId` (IN `p_package_id` INT)   BEGIN
    SELECT
        g.id AS guide_id, p.price AS totalAmount
    FROM
        guides g
    INNER JOIN
        package p ON g.id = p.guide_id
    WHERE
        p.id = p_package_id;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetGuideMonthlyIncome` (IN `guide_id` INT, IN `start_date` DATE, IN `end_date` DATE)   BEGIN
    SELECT 
        DATE_FORMAT(p.datetime, '%Y-%m') AS `Month`,
        SUM(p.amount) AS `MonthlyIncome`
    FROM 
        `payment` p
    JOIN
        `guide_booking` gb ON p.id = gb.payment_id -- Assuming payment_id is the foreign key in guide_booking referencing payment.id
    WHERE 
        gb.guide_id = guide_id
        AND p.status = 'completed'
        AND p.datetime BETWEEN start_date AND end_date
    GROUP BY 
        DATE_FORMAT(p.datetime, '%Y-%m')
    ORDER BY 
        DATE_FORMAT(p.datetime, '%Y-%m');
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetGuidePackages` (IN `packageID` INT)   BEGIN
    SELECT * FROM package WHERE package.id = packageID;
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

CREATE DEFINER=`root`@`%` PROCEDURE `GetMonthlyCompletedBookings` (IN `guide_id` INT)   BEGIN
    -- Create temporary table to store monthly completed bookings
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_monthly_completed_bookings (
        month_year VARCHAR(7),
        num_completed_bookings INT
    );

    -- Insert monthly completed bookings data into temporary table
    INSERT INTO temp_monthly_completed_bookings
    SELECT DATE_FORMAT(date, '%Y-%m') AS month_year, COUNT(*) AS num_completed_bookings
    FROM guide_booking
    WHERE guide_id = guide_id AND status = 'completed'
    GROUP BY DATE_FORMAT(date, '%Y-%m');

    -- Create temporary table to store all bookings
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_all_bookings (
        month_year VARCHAR(7),
        num_bookings INT
    );

    -- Insert all bookings data into temporary table
    INSERT INTO temp_all_bookings
    SELECT DATE_FORMAT(date, '%Y-%m') AS month_year, COUNT(*) AS num_bookings
    FROM guide_booking
    WHERE guide_id = guide_id
    GROUP BY DATE_FORMAT(date, '%Y-%m');

    -- Select data from both temporary tables
    SELECT tcb.month_year, tcb.num_completed_bookings, tab.num_bookings
    FROM temp_monthly_completed_bookings tcb
    LEFT JOIN temp_all_bookings tab ON tcb.month_year = tab.month_year;

    -- Drop temporary tables
    DROP TEMPORARY TABLE IF EXISTS temp_monthly_completed_bookings;
    DROP TEMPORARY TABLE IF EXISTS temp_all_bookings;
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
                        p.reference_number AS `reference_number`,
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

CREATE DEFINER=`root`@`%` PROCEDURE `GetSuitableGuides` (IN `max_group_size` INT, IN `date` DATE, IN `places` VARCHAR(255), IN `transport_needed` BOOLEAN)   BEGIN
    -- Retrieve available guides and their corresponding packages
    SELECT g.id AS guide_id,
           g.name AS guide_name,
           GROUP_CONCAT(DISTINCT p.id) AS package_ids,
           GROUP_CONCAT(DISTINCT p.places) AS places,
           GROUP_CONCAT(DISTINCT gp.languages) AS languages
    FROM package p
    INNER JOIN guides g ON p.guide_id = g.id
    INNER JOIN guide_profile gp ON g.id = gp.guide_id
    INNER JOIN guide_availability ga ON g.id = ga.guide_id
    WHERE p.max_group_size >= max_group_size
      AND p.transport_needed = transport_needed
      AND FIND_IN_SET(places, REPLACE(p.places, ', ', ',')) > 0
      AND ga.date = date 
      AND ga.availability = 1
    GROUP BY g.id, g.name;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `GetUserBookingDetails` (IN `user_id` INT)   BEGIN
    DECLARE num_completed_tours INT;
    DECLARE total_income DECIMAL(10, 2);
    DECLARE upcoming_booking_date DATE;
    DECLARE upcoming_booking_location VARCHAR(255);
    DECLARE recent_booking_date DATE;
    DECLARE recent_booking_location VARCHAR(255);

    -- No of Tours (Completed)
    SELECT COUNT(*) INTO num_completed_tours
    FROM guide_booking
    WHERE status = 'completed' AND guide_id = user_id;

    -- Income from completed tours
    SELECT IFNULL(SUM(p.amount), 0) INTO total_income
    FROM guide_booking gb
    INNER JOIN payment p ON gb.payment_id = p.id
    WHERE gb.status = 'completed' AND gb.guide_id = user_id;

    -- Upcoming Booking
    SELECT MIN(date) INTO upcoming_booking_date
    FROM guide_booking
    WHERE status = 'pending' AND guide_id = user_id AND date > current_date
    ORDER BY date ASC
    LIMIT 1;

    SELECT location INTO upcoming_booking_location
    FROM guide_booking
    WHERE status = 'pending' AND guide_id = user_id AND date = upcoming_booking_date
    LIMIT 1;

    -- Recent Booking
    SELECT MAX(date) INTO recent_booking_date
    FROM guide_booking
    WHERE status = 'completed' AND guide_id = user_id AND date < current_date
    ORDER BY date DESC
    LIMIT 1;

    SELECT location INTO recent_booking_location
    FROM guide_booking
    WHERE status = 'completed' AND guide_id = user_id AND date = recent_booking_date
    LIMIT 1;

    -- Return results
    SELECT num_completed_tours, total_income, upcoming_booking_date, upcoming_booking_location, recent_booking_date, recent_booking_location;
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
    DECLARE bookingFee DECIMAL(10, 2) DEFAULT 0.00;
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


        SET rentAmount = rentAmount * 0.20; -- 20% booking fee
         -- Insert into rent_pay for each rent order
        INSERT INTO rent_pay (rent_id, payment_id, amount)
        VALUES (lastRentID, lastPaymentID, rentAmount);

        
    END LOOP;

    CLOSE curRentalService;


    SET bookingFee = totalSum * 0.20; -- 20% booking fee
    -- Create a single payment entry for the total sum of all rent orders
    INSERT INTO payment (amount, status) VALUES (bookingFee, 'pending');
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
    SELECT reference_number AS orderID, bookingFee AS totalAmount;
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

CREATE DEFINER=`root`@`%` PROCEDURE `RetrieveAvailableDays` (IN `p_guide_id` INT, IN `p_month` INT, IN `p_year` INT)   BEGIN
    SELECT DATE_FORMAT(date, '%d') AS available_day
    FROM guide_availability
    WHERE guide_id = p_guide_id
        AND MONTH(date) = p_month
        AND YEAR(date) = p_year
        AND availability = 1;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `RetrieveDaysByGuideIdMonthYear` (IN `p_guide_id` INT, IN `p_month` INT, IN `p_year` INT)   BEGIN
    SELECT DISTINCT DATE_FORMAT(date, '%d') AS booked_day
    FROM guide_booking
    WHERE guide_id = p_guide_id
        AND MONTH(date) = p_month
        AND YEAR(date) = p_year;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `ViewGuideProfile` (IN `guide_id` INT)   BEGIN
    SELECT cp.*, g.name AS guide_name
    FROM guide_profile cp
    JOIN guides g ON cp.guide_id = g.id
    WHERE cp.guide_id = guide_id;
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
(9, 3, '2024-05-01', '2024-05-29'),
(15, 2, '2024-05-01', '2024-05-03');

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
(40, 15, 2);

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
(2, 'Kamal Wijesiri', '33/10, Mawathgama Rd, Homagama', '0762260663', '200072903238', 2, '1.jpg'),
(3, 'Nirmal Savinda', 'Colombo', '0714499550', '200167329832', 11, '1.jpg');

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
  `image` varchar(200) NOT NULL,
  `advance_fee` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `rentalservice_id`, `name`, `cost`, `description`, `type`, `count`, `fee`, `standard_fee`, `image`, `advance_fee`) VALUES
(1, 1, 'Tent with Rainfly', 23000.00, 'Camping Tent 2 Person, Waterproof Windproof Tent with Rainfly Easy Set up-Portable Dome Tents for Camping', 'Tent', 2, 500.00, 300.00, '662f8ebb9ad85.jpg', 2500.00),
(2, 1, 'QUECHUA Camping Tent ', 45000.00, 'QUECHUA Camping Tent MH100 – 3-Person', 'Tent', 3, 1200.00, 2000.00, '662f8f36daa72.jpg', 1000.00),
(3, 2, '4 Man Tent - MH100', 6999.00, 'Hire Dometic Camping Equipment ', 'Tent', 5, 399.00, 3599.00, '662fb427c4a59.jpg', 0.00),
(4, 2, 'Tent ', 6999.00, 'Hire Dometic Camping Equipment ', 'Tent', 2, 399.00, 3599.00, '662fb4df7940e.jpg', 0.00),
(5, 2, 'Rucksack Backpack Small 20L', 5400.00, 'Rucksack Backpack Small 20L Folding Waterproof Light Bag Camping Hiking Walking', 'Backpack', 7, 199.00, 3990.00, '662fb57becd8e.jpg', 0.00),
(6, 2, 'Sleeping Bag', 7890.00, 'Lightweight 3 Season Weather Sleep Bags for Kids Adults Girls Women, Microfiber Filled', 'Sleeping', 3, 500.00, 6500.00, '662fb640d7889.jpg', 0.00),
(7, 2, 'Trail Kids Sleeping Bag', 8500.00, 'Trail Kids Sleeping Bag Mummy Hooded 3 Season Soft Warm 2 Way Zip Boys Girls', 'Sleeping', 8, 490.00, 4000.00, '662fb6f416678.jpg', 0.00),
(8, 2, 'Sleeping Bag for Adults', 10900.00, 'Sleeping Bags for Adults Backpacking Lightweight', 'Tent', 5, 600.00, 8900.00, '662fb76b5837c.jpg', 0.00),
(9, 2, 'Camping Cookware Set', 5600.00, 'Camping Outdoor Cookware Set with cutlery\nPackage Size : 19 x 13 x 19 cm\nCook pot, pot lid, kettle, frying pan, carabiner, foldable fork, foldable spoon & knife', 'Cooking', 2, 200.00, 4000.00, '662fb7cca0880.jpeg', 0.00),
(10, 2, 'Cookware Kit', 15000.00, 'Fire-Maple Feast 4 Piece Camping Cookware Kit Outdoor Cookware Set with Pots, Kettle, Saucepans and Spatula for Hiking Fishing Picnic\n', 'Cooking', 6, 900.00, 8000.00, '662fb8662d174.jpg', 0.00),
(11, 2, 'Titanium Coffee Maker ', 7000.00, 'Valtcan Titanium Percolator Coffee Maker Pot 1.5L Filter Brew Ultralight Weight Camping Kettle 50 fl oz 6 Cup Capacity Glass Knob 395g Compact Kettle\n', 'Cooking', 1, 1090.00, 5000.00, '662fb8e56975e.jpg', 0.00),
(12, 2, 'Camping Coffee Pot', 1500.00, 'widesea Camping Coffee Pot 750ML, French Press Coffee Maker,Lightweight Backpacking Pot with Collapsible Handle For Camping,Hiking,Fire Cooking\n', 'Cooking', 4, 200.00, 1000.00, '662fb93176d73.jpg', 0.00),
(13, 3, 'Stainless Steel Cooking Set', 1000.00, 'LIghtweight Stackable Pots with bags', 'Cooking', 4, 600.00, 400.00, '66305259e7188.jpg', 0.00),
(14, 3, 'Trekking Poles', 500.00, 'Lightweight high grip durable dual poles', 'Climbing', 3, 300.00, 500.00, '663052fbe9c76.jpg', 0.00),
(15, 3, 'Jackets', 800.00, 'Polyester high quality jackets for men and women', 'Clothing', 6, 200.00, 400.00, '66305376674d9.jpg', 0.00),
(16, 2, 'Green Tent', 40000.00, 'Tent for 4 people', 'Tent', 2, 1000.00, 2000.00, '66306a27ccaa3.jpg', 0.00),
(17, 1, 'Paul Wyman', 657.00, 'Repellat ab ratione veniam dolorum ipsum.', 'Tent', 78, 786.00, 786.00, '66307ce6330ad.jpg', 10000.00);

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
(1, 'Nimal Perera', 'Badulla', '200167329832', '0716024489', 'male', 4, 'accepted', '662fa5f022b98.pdf', 2),
(5, 'Visal Silva', '348, Parakrama Mawatha,Town Road, Homagama', '200250901555', '0779100000', 'male', 9, 'accepted', '662fb820f01dc.pdf', 7),
(6, 'Kamal Perera', 'Colombo', '200129997800', '0760244843', 'male', 10, 'accepted', '6630335b6ee98.pdf', 8);

--
-- Triggers `guides`
--
DELIMITER $$
CREATE TRIGGER `AfterGuideInsert` AFTER INSERT ON `guides` FOR EACH ROW BEGIN
    INSERT INTO `guide_profile` (`guide_id`, `description`, `languages`,`certifications`)
    VALUES (NEW.id, ' ', ' ', ' ');  -- Assuming default values 
END
$$
DELIMITER ;

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
(1, 5, 0, '2024-05-16'),
(2, 5, 1, '2024-05-07'),
(3, 5, 0, '2024-05-20'),
(4, 5, 0, '2024-05-11'),
(5, 5, 1, '2024-06-11'),
(6, 5, 0, '2024-05-22'),
(7, 5, 1, '2024-05-28'),
(8, 5, 0, '2024-05-24'),
(9, 1, 1, '2024-05-09'),
(10, 1, 1, '2024-05-23');

-- --------------------------------------------------------

--
-- Table structure for table `guide_booking`
--

CREATE TABLE `guide_booking` (
  `id` int NOT NULL,
  `guide_id` int DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `package_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date` date DEFAULT NULL,
  `no_of_people` int DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `transport_supply` tinyint(1) DEFAULT NULL,
  `payment_id` int DEFAULT NULL,
  `status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guide_booking`
--

INSERT INTO `guide_booking` (`id`, `guide_id`, `customer_id`, `package_id`, `created_at`, `date`, `no_of_people`, `location`, `transport_supply`, `payment_id`, `status`) VALUES
(1, 5, 3, 1, '2024-04-30 02:49:19', '2024-05-16', 10, 'Kandy', 1, 9, 'completed'),
(2, 5, 3, 1, '2024-04-30 02:58:41', '2024-05-20', 15, 'Kandy', 1, 10, 'pending'),
(3, 5, 3, 1, '2024-04-30 03:02:10', '2024-04-22', 5, 'Kandy', 1, 11, 'completed'),
(4, 5, 3, 1, '2024-04-30 03:07:02', '2024-03-11', 5, 'Kandy', 1, 12, 'completed'),
(5, 5, 3, 1, '2024-04-30 03:08:44', '2024-04-17', 5, 'Kandy', 1, 13, 'completed');

--
-- Triggers `guide_booking`
--
DELIMITER $$
CREATE TRIGGER `after_guide_booking_delete` AFTER DELETE ON `guide_booking` FOR EACH ROW BEGIN
    UPDATE guide_availability
    SET availability = 1
    WHERE guide_id = OLD.guide_id
    AND date = OLD.date;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_guide_booking_insert` AFTER INSERT ON `guide_booking` FOR EACH ROW BEGIN
    -- Update availability in guide_availability table to 0
    UPDATE guide_availability
    SET availability = 0
    WHERE guide_id = NEW.guide_id AND date = NEW.date;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_guide_booking_insert` BEFORE INSERT ON `guide_booking` FOR EACH ROW BEGIN
    DECLARE guide_count INT;

    -- Check if the combination of guide_id and date already exists
    SELECT COUNT(*) INTO guide_count
    FROM guide_booking
    WHERE guide_id = NEW.guide_id AND date = NEW.date;

    -- If the count is greater than 0, it means there is already an entry for this guide on the same date
    IF guide_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Only one booking is allowed per guide on the same date.';
    END IF;
END
$$
DELIMITER ;

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
(1, ' hbjhbjhbjhbj', 'English ,SInhala', ' hhbjbhjk'),
(5, '  ', ' ', ' '),
(6, ' ', ' ', ' ');

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
(1, 1, 'I000011719', 'available'),
(2, 1, 'I000013499', 'available'),
(3, 2, 'I000023471', 'available'),
(4, 2, 'I000027224', 'available'),
(5, 2, 'I000024707', 'available'),
(6, 3, 'I000036248', 'available'),
(7, 3, 'I000031738', 'available'),
(8, 3, 'I000032932', 'available'),
(9, 3, 'I000034266', 'available'),
(10, 3, 'I000036939', 'available'),
(11, 4, 'I000044270', 'available'),
(12, 4, 'I000041709', 'available'),
(13, 5, 'I000052409', 'available'),
(14, 5, 'I000053777', 'available'),
(15, 5, 'I000054219', 'available'),
(16, 5, 'I000051372', 'available'),
(17, 5, 'I000053379', 'available'),
(18, 5, 'I000055989', 'available'),
(19, 5, 'I000052581', 'available'),
(20, 6, 'I000068709', 'available'),
(21, 6, 'I000066459', 'available'),
(22, 6, 'I000068527', 'available'),
(23, 7, 'I000071945', 'available'),
(24, 7, 'I000073274', 'available'),
(25, 7, 'I000078894', 'available'),
(26, 7, 'I000071805', 'available'),
(27, 7, 'I000077384', 'available'),
(28, 7, 'I000077254', 'available'),
(29, 7, 'I000075647', 'available'),
(30, 7, 'I000075148', 'available'),
(31, 8, 'I000085217', 'available'),
(32, 8, 'I000089552', 'available'),
(33, 8, 'I000087679', 'available'),
(34, 8, 'I000086134', 'available'),
(35, 8, 'I000088713', 'available'),
(36, 9, 'I000095982', 'available'),
(37, 9, 'I000095893', 'available'),
(38, 10, 'I000108912', 'available'),
(39, 10, 'I000101510', 'available'),
(40, 10, 'I000108756', 'available'),
(41, 10, 'I000107912', 'available'),
(42, 10, 'I000108719', 'available'),
(43, 10, 'I000101741', 'available'),
(44, 11, 'I000119867', 'available'),
(45, 12, 'I000126243', 'available'),
(46, 12, 'I000129561', 'available'),
(47, 12, 'I000121362', 'available'),
(48, 12, 'I000126633', 'available'),
(49, 13, 'I000131080', 'available'),
(50, 13, 'I000136237', 'available'),
(51, 13, 'I000139980', 'available'),
(52, 13, 'I000134766', 'available'),
(53, 14, 'I000144349', 'available'),
(54, 14, 'I000148919', 'available'),
(55, 14, 'I000149973', 'available'),
(56, 15, 'I000153630', 'available'),
(57, 15, 'I000152287', 'available'),
(58, 15, 'I000159912', 'available'),
(59, 15, 'I000155942', 'available'),
(60, 15, 'I000152965', 'available'),
(61, 15, 'I000151993', 'available'),
(62, 16, 'I000162358', 'removed'),
(63, 16, 'I000168743', 'unavailable'),
(64, 16, 'I000161429', 'available'),
(65, 16, 'I000169042', 'available'),
(66, 17, 'I000178126', 'available'),
(67, 17, 'I000173002', 'available'),
(68, 17, 'I000179122', 'available'),
(69, 17, 'I000179510', 'available'),
(70, 17, 'I000176799', 'available'),
(71, 17, 'I000175796', 'available'),
(72, 17, 'I000172143', 'available'),
(73, 17, 'I000174773', 'available'),
(74, 17, 'I000176599', 'available'),
(75, 17, 'I000178635', 'available'),
(76, 17, 'I000173882', 'available'),
(77, 17, 'I000173250', 'available'),
(78, 17, 'I000177033', 'available'),
(79, 17, 'I000173588', 'available'),
(80, 17, 'I000173075', 'available'),
(81, 17, 'I000176340', 'available'),
(82, 17, 'I000179999', 'available'),
(83, 17, 'I000175136', 'available'),
(84, 17, 'I000175799', 'available'),
(85, 17, 'I000178169', 'available'),
(86, 17, 'I000172229', 'available'),
(87, 17, 'I000173034', 'available'),
(88, 17, 'I000178804', 'available'),
(89, 17, 'I000173179', 'available'),
(90, 17, 'I000176379', 'available'),
(91, 17, 'I000179799', 'available'),
(92, 17, 'I000179975', 'available'),
(93, 17, 'I000179552', 'available'),
(94, 17, 'I000178920', 'available'),
(95, 17, 'I000179719', 'available'),
(96, 17, 'I000173588', 'available'),
(97, 17, 'I000175923', 'available'),
(98, 17, 'I000176443', 'available'),
(99, 17, 'I000174320', 'available'),
(100, 17, 'I000174303', 'available'),
(101, 17, 'I000175087', 'available'),
(102, 17, 'I000172132', 'available'),
(103, 17, 'I000171946', 'available'),
(104, 17, 'I000176985', 'available'),
(105, 17, 'I000175414', 'available'),
(106, 17, 'I000172212', 'available'),
(107, 17, 'I000179510', 'available'),
(108, 17, 'I000175995', 'available'),
(109, 17, 'I000178520', 'available'),
(110, 17, 'I000177145', 'available'),
(111, 17, 'I000173280', 'available'),
(112, 17, 'I000175221', 'available'),
(113, 17, 'I000174895', 'available'),
(114, 17, 'I000178233', 'available'),
(115, 17, 'I000172570', 'available'),
(116, 17, 'I000178947', 'available'),
(117, 17, 'I000171068', 'available'),
(118, 17, 'I000179978', 'available'),
(119, 17, 'I000177178', 'available'),
(120, 17, 'I000178786', 'available'),
(121, 17, 'I000171545', 'available'),
(122, 17, 'I000172487', 'available'),
(123, 17, 'I000174562', 'available'),
(124, 17, 'I000178496', 'available'),
(125, 17, 'I000171584', 'available'),
(126, 17, 'I000175845', 'available'),
(127, 17, 'I000175006', 'available'),
(128, 17, 'I000174729', 'available'),
(129, 17, 'I000177006', 'available'),
(130, 17, 'I000177767', 'available'),
(131, 17, 'I000171732', 'available'),
(132, 17, 'I000176210', 'available'),
(133, 17, 'I000171618', 'available'),
(134, 17, 'I000179064', 'available'),
(135, 17, 'I000172869', 'available'),
(136, 17, 'I000177135', 'available'),
(137, 17, 'I000178956', 'available'),
(138, 17, 'I000175165', 'available'),
(139, 17, 'I000176735', 'available'),
(140, 17, 'I000177690', 'available'),
(141, 17, 'I000172700', 'available'),
(142, 17, 'I000177298', 'available'),
(143, 17, 'I000174583', 'available');

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
(1, 6.993401, 81.054982),
(2, 6.993401, 81.054982),
(3, 6.032895, 80.216791),
(4, 7.873054, 80.771797),
(5, 7.873054, 80.771797),
(6, 7.873054, 80.771797),
(7, 7.873054, 80.771797),
(8, 7.753326, 80.848701),
(9, 6.585395, 79.960740);

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
  `places` text NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `package`
--

INSERT INTO `package` (`id`, `guide_id`, `price`, `max_group_size`, `max_distance`, `transport_needed`, `places`, `name`) VALUES
(1, 5, 30000.00, 20, 20, 1, 'Kandy', 'Silver'),
(2, 6, 30000.00, 15, 6, 0, 'Ella', 'Rawana Ella'),
(3, 1, 10000.00, 10, 20, 0, 'Kandy', 'Silver Package'),
(4, 1, 5000.00, 10, 10, 1, 'Ella', 'Regular Package'),
(6, 5, 30000.00, 20, 20, 0, 'Kandy', 'Gold Package'),
(7, 5, 10000.00, 15, 20, 0, 'Kandy', 'Regular Package');

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
(1, '2024-04-30 01:02:52', 'completed', 2675.40, NULL, 'RNT00001'),
(2, '2024-04-30 01:10:07', 'completed', 3780.00, NULL, 'RNT00002'),
(3, '2024-04-30 01:12:54', 'completed', 4008.00, NULL, 'RNT00003'),
(4, '2024-04-30 01:16:00', 'completed', 8988.00, NULL, 'RNT00004'),
(5, '2024-04-30 01:18:12', 'completed', 1378.00, NULL, 'RNT00005'),
(6, '2024-04-30 02:08:33', 'completed', 3180.00, NULL, 'RNT00006'),
(7, '2024-04-30 02:16:10', 'completed', 2838.40, NULL, 'RNT00007'),
(8, '2024-04-30 02:30:09', 'completed', 240.00, NULL, 'RNT00008'),
(9, '2024-04-30 02:49:18', 'pending', 30000.00, NULL, 'GD00009'),
(10, '2024-04-30 02:58:41', 'pending', 30000.00, NULL, 'GD00010'),
(11, '2024-04-30 03:02:10', 'pending', 30000.00, NULL, 'GD00011'),
(12, '2024-04-30 03:07:02', 'pending', 30000.00, NULL, 'GD00012'),
(13, '2024-04-30 03:08:44', 'pending', 30000.00, NULL, 'GD00013'),
(14, '2024-04-30 03:57:32', 'completed', 5239.60, NULL, 'RNT00014'),
(15, '2024-04-30 04:02:08', 'completed', 10000.00, NULL, 'GD00015'),
(16, '2024-04-30 04:07:13', 'completed', 3038.40, NULL, 'RNT00016'),
(17, '2024-04-30 04:51:37', 'completed', 4215.60, NULL, 'RNT00017'),
(18, '2024-04-30 05:58:47', 'pending', 160.00, NULL, 'RNT00018');

--
-- Triggers `payment`
--
DELIMITER $$
CREATE TRIGGER `AfterPaymentStatusCompleted` AFTER UPDATE ON `payment` FOR EACH ROW BEGIN
    -- Declare variables and cursor at the beginning of the block
    DECLARE total_paid DECIMAL(10,2);
    DECLARE done INT DEFAULT FALSE;
    DECLARE rent_id_var INT;
    DECLARE rent_cursor CURSOR FOR 
        SELECT rent_id FROM rent_pay WHERE payment_id = NEW.id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Check if the payment status has been updated to 'completed'
    IF NEW.status = 'completed' AND OLD.status <> 'completed' THEN

        -- Open the cursor
        OPEN rent_cursor;

        -- Loop through all rent IDs fetched by the cursor
        rent_loop: LOOP
            FETCH rent_cursor INTO rent_id_var;
            IF done THEN
                LEAVE rent_loop;
            END IF;
            
            -- Calculate the total paid amount for the specific rent
            SELECT SUM(rp.amount) INTO total_paid
            FROM rent_pay rp
            JOIN payment p ON rp.payment_id = p.id
            WHERE rp.rent_id = rent_id_var AND p.status = 'completed';

            -- Update the paid_amount in the rent table for each affected rent
            UPDATE rent
            SET paid_amount = total_paid
            WHERE id = rent_id_var;
        END LOOP;
        
        -- Close the cursor
        CLOSE rent_cursor;
    END IF;
END
$$
DELIMITER ;

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
(1, 2, 2, '2024-03-01', '2024-03-05', 'return_reported', NULL, 13377.00, 2675.40, '2024-04-30 02:06:55', '2024-04-30 01:02:52'),
(2, 2, 2, '2024-02-01', '2024-02-03', 'completed', NULL, 14500.00, 2900.00, '2024-04-30 01:28:36', '2024-04-30 01:10:07'),
(3, 2, 1, '2024-03-01', '2024-03-03', 'accepted', NULL, 4400.00, 880.00, '2024-04-30 02:55:20', '2024-04-30 01:10:07'),
(4, 2, 2, '2024-01-02', '2024-01-08', 'completed', NULL, 16740.00, 3348.00, '2024-04-30 01:28:44', '2024-04-30 01:12:53'),
(5, 2, 1, '2024-04-02', '2024-04-08', 'accepted', NULL, 3300.00, 660.00, '2024-04-30 02:55:22', '2024-04-30 01:12:54'),
(6, 2, 2, '2024-03-16', '2024-03-22', 'rented', NULL, 35740.00, 7148.00, '2024-04-30 03:50:39', '2024-04-30 01:15:59'),
(7, 2, 1, '2024-05-16', '2024-05-22', 'accepted', NULL, 9200.00, 1840.00, '2024-04-30 04:49:55', '2024-04-30 01:16:00'),
(8, 2, 2, '2024-05-28', '2024-05-29', 'accepted', NULL, 6090.00, 1218.00, '2024-04-30 01:18:54', '2024-04-30 01:18:12'),
(9, 2, 1, '2024-04-30', '2024-05-01', 'accepted', NULL, 800.00, 160.00, '2024-04-30 02:55:30', '2024-04-30 01:18:12'),
(10, 2, 2, '2024-05-01', '2024-05-02', 'accepted', NULL, 15900.00, 3180.00, '2024-04-30 03:50:14', '2024-04-30 02:08:33'),
(11, 2, 2, '2024-05-05', '2024-05-08', 'pending', NULL, 14192.00, 2838.40, '2024-04-30 02:16:10', '2024-04-30 02:16:10'),
(12, 2, 3, '2024-05-02', '2024-05-03', 'pending', NULL, 1200.00, 240.00, '2024-04-30 02:30:09', '2024-04-30 02:30:09'),
(13, 2, 2, '2024-05-09', '2024-05-10', 'accepted', NULL, 26198.00, 5239.60, '2024-04-30 03:59:21', '2024-04-30 03:57:32'),
(14, 2, 2, '2024-05-01', '2024-05-04', 'pending', NULL, 9592.00, 1918.40, '2024-04-30 04:07:13', '2024-04-30 04:07:13'),
(15, 2, 1, '2024-05-01', '2024-05-04', 'pending', NULL, 5600.00, 1120.00, '2024-04-30 04:07:13', '2024-04-30 04:07:13'),
(16, 2, 1, '2024-05-02', '2024-05-03', 'pending', NULL, 3200.00, 640.00, '2024-04-30 04:51:37', '2024-04-30 04:51:37'),
(17, 2, 2, '2024-05-02', '2024-05-03', 'pending', NULL, 17878.00, 3575.60, '2024-04-30 04:51:37', '2024-04-30 04:51:37'),
(18, 2, 1, '2024-05-03', '2024-05-04', 'pending', NULL, 800.00, 160.00, '2024-04-30 05:58:47', '2024-04-30 05:58:47');

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
(1, 'Camp Here', 'Badulla ', '200189128890', '0760244897', 3, 'accepted', '662f8a5c5d974.pdf', 1, '1.webp'),
(2, 'Tent Master', 'Galle', '200174996619', '0716024482', 5, 'accepted', '662fa6695959d.pdf', 3, '1.webp'),
(3, 'Kalu Rentals', 'kalutara', '210073596233', '0760245489', 12, 'accepted', '66305166060e6.pdf', 9, '1.webp');

--
-- Triggers `rental_services`
--
DELIMITER $$
CREATE TRIGGER `AfterRentalServiceInsert` AFTER INSERT ON `rental_services` FOR EACH ROW BEGIN
    INSERT INTO `rental_settings` (`rentalservice_id`, `renting_status`, `recovery_period`)
    VALUES (NEW.id, 1, 1);  -- Assuming default values for `renting_state` and `recovery_period`
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `rental_settings`
--

CREATE TABLE `rental_settings` (
  `id` int NOT NULL,
  `rentalservice_id` int NOT NULL,
  `renting_status` tinyint(1) NOT NULL DEFAULT '1',
  `recovery_period` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rental_settings`
--

INSERT INTO `rental_settings` (`id`, `rentalservice_id`, `renting_status`, `recovery_period`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 1),
(3, 3, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `rent_complaint`
--

CREATE TABLE `rent_complaint` (
  `id` int NOT NULL,
  `complaint_no` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'CC000001',
  `rent_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','cancelled','resolved') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rent_complaint`
--

INSERT INTO `rent_complaint` (`id`, `complaint_no`, `rent_id`, `title`, `description`, `created_at`, `status`) VALUES
(1, 'CC000001', 6, 'Not Responding', 'Rental Service Not Responding', '2024-04-30 01:30:19', 'pending'),
(2, 'CC000002', 8, 'Damaged', 'THE item is damaged', '2024-04-30 02:18:30', 'pending'),
(3, 'CC000003', 8, 'Rental Services is not respoding', 'I tried to contact the rental services more than 5 times but they are not answering the call. please solve my problem', '2024-04-30 02:21:35', 'pending');

--
-- Triggers `rent_complaint`
--
DELIMITER $$
CREATE TRIGGER `generate_complaint_no` BEFORE INSERT ON `rent_complaint` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    DECLARE padded_id VARCHAR(6);
    DECLARE new_complaint_no VARCHAR(12);

    -- Get the next available ID
    SELECT IFNULL(MAX(id) + 1, 1) INTO next_id FROM rent_complaint;

    -- Pad the ID with zeros to ensure it's six digits long
    SET padded_id = LPAD(next_id, 6, '0');

    -- Combine 'CC' with the padded ID
    SET new_complaint_no = CONCAT('CC', padded_id);

    -- Set the new complaint_no value for the new row
    SET NEW.complaint_no = new_complaint_no;
END
$$
DELIMITER ;

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
(1, 1, 11),
(2, 1, 13),
(3, 1, 45),
(4, 2, 36),
(5, 2, 31),
(7, 3, 3),
(8, 4, 44),
(9, 4, 37),
(11, 5, 1),
(12, 6, 44),
(13, 6, 21),
(14, 6, 20),
(15, 6, 36),
(19, 7, 3),
(20, 8, 44),
(21, 9, 1),
(22, 10, 38),
(23, 10, 20),
(25, 11, 7),
(26, 11, 6),
(27, 11, 36),
(28, 12, 57),
(29, 12, 56),
(30, 13, 11),
(31, 13, 22),
(32, 13, 21),
(33, 13, 20),
(34, 13, 45),
(37, 14, 11),
(38, 14, 6),
(40, 15, 3),
(41, 16, 4),
(42, 17, 14),
(43, 17, 13),
(44, 17, 31),
(45, 18, 1);

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
(1, 1, 1, 2675.40),
(2, 2, 2, 2900.00),
(3, 3, 2, 880.00),
(4, 4, 3, 3348.00),
(5, 5, 3, 660.00),
(6, 6, 4, 7148.00),
(7, 7, 4, 1840.00),
(8, 8, 5, 1218.00),
(9, 9, 5, 160.00),
(10, 10, 6, 3180.00),
(11, 11, 7, 2838.40),
(12, 12, 8, 240.00),
(13, 13, 14, 5239.60),
(14, 14, 16, 1918.40),
(15, 15, 16, 1120.00),
(16, 16, 17, 640.00),
(17, 17, 17, 3575.60),
(18, 18, 18, 160.00);

--
-- Triggers `rent_pay`
--
DELIMITER $$
CREATE TRIGGER `AfterPaymentInsert` AFTER INSERT ON `rent_pay` FOR EACH ROW BEGIN
    -- Declare a variable to hold the sum of payments for the rent
    DECLARE total_paid DECIMAL(10,2);

    -- Calculate the total paid amount for the specific rent
    SELECT SUM(amount) INTO total_paid
    FROM rent_pay
    WHERE rent_id = NEW.rent_id;

    -- Update the paid_amount in the rent table
    UPDATE rent
    SET paid_amount = total_paid
    WHERE id = NEW.rent_id;
END
$$
DELIMITER ;

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
(1, 1, NULL, 'completed', '2024-04-30 01:28:28'),
(2, 2, NULL, 'completed', '2024-04-30 01:28:36'),
(3, 3, NULL, 'accepted', '2024-04-30 02:55:20'),
(4, 4, NULL, 'completed', '2024-04-30 01:28:44'),
(5, 5, NULL, 'accepted', '2024-04-30 02:55:22'),
(6, 6, NULL, 'rented', '2024-04-30 03:50:39'),
(7, 7, NULL, 'accepted', '2024-04-30 04:49:55'),
(8, 8, NULL, 'accepted', '2024-04-30 01:18:54'),
(9, 9, NULL, 'accepted', '2024-04-30 02:55:30'),
(10, 10, NULL, 'accepted', '2024-04-30 03:50:14'),
(11, 11, NULL, NULL, '2024-04-30 02:16:10'),
(12, 12, NULL, NULL, '2024-04-30 02:30:09'),
(13, 13, NULL, 'accepted', '2024-04-30 03:59:21'),
(14, 14, NULL, NULL, '2024-04-30 04:07:13'),
(15, 15, NULL, NULL, '2024-04-30 04:07:13'),
(16, 16, NULL, NULL, '2024-04-30 04:51:37'),
(17, 17, NULL, NULL, '2024-04-30 04:51:37'),
(18, 18, NULL, NULL, '2024-04-30 05:58:47');

--
-- Triggers `rent_request`
--
DELIMITER $$
CREATE TRIGGER `RentStatus` AFTER UPDATE ON `rent_request` FOR EACH ROW BEGIN
    -- Check if both columns have the same value and it's 'rented'
IF NEW.rentalservice_req = 'rented' THEN
        UPDATE rent
        SET status = 'rented'
        WHERE id = NEW.rent_id;
    -- Additionally, check if both columns have the same value and it's 'cancel'
    -- ELSEIF NEW.customer_req = NEW.rentalservice_req AND NEW.customer_req = 'cancel' THEN
    ELSEIF NEW.customer_req = 'cancelled' THEN
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `complaint_no` varchar(12) NOT NULL DEFAULT 'RC000001'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rent_return_complaints`
--

INSERT INTO `rent_return_complaints` (`id`, `rent_id`, `complains`, `charge`, `description`, `status`, `created_at`, `complaint_no`) VALUES
(1, 1, '[{\"charge\": \"1000\", \"equipment_id\": \"12\", \"complaint_description\": \"damaged\"}, {\"charge\": \"4400\", \"equipment_id\": \"5\", \"complaint_description\": \"Lost\"}]', 5400.00, NULL, 'cancelled', '2024-04-30 01:24:38', 'RC00001'),
(2, 1, '[{\"charge\": \"13000\", \"equipment_id\": \"4\", \"complaint_description\": \"Damaged\"}]', 13000.00, NULL, 'pending', '2024-04-30 02:06:55', 'RC00001'),
(3, 1, '[{\"charge\": \"15000\", \"equipment_id\": \"5\", \"complaint_description\": \"Lost\"}]', 15000.00, NULL, 'pending', '2024-04-30 02:07:20', 'RC00001');

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
DELIMITER $$
CREATE TRIGGER `complaint_no` BEFORE INSERT ON `rent_return_complaints` FOR EACH ROW BEGIN
    DECLARE next_id INT;

    -- Get the next complaint ID
    SELECT IFNULL(MAX(SUBSTRING(id, 3) + 1), 1) INTO next_id
    FROM rent_return_complaints;

    -- Generate the complaint number
    SET NEW.complaint_no = CONCAT('RC', LPAD(next_id, 5, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `reset_tokens`
--

CREATE TABLE `reset_tokens` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, ' Check Weather Patterns Before You Camp', ' Sri Lanka\'s weather can be unpredictable. Always check the local forecast to avoid monsoon rains during your camping trip.', 'admin'),
(2, 'Respect Wildlife and Natural Habitats', 'When camping near national parks like Yala or Wilpattu, keep a safe distance from wildlife and follow all local conservation rules.', 'admin'),
(3, ' Discover Eco-Friendly Camping Sites', 'Opt for established eco-campsites in Sri Lanka that support sustainable tourism and minimize environmental impact.', 'admin');

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
(1, 'admin@wl.com', '6Hb7MywnhQOXA9kQSGADiQ==:cbd5770ec32eeb02dcde119b9099cabaef681b24150510f2b50453864efa2f79', '2024-04-29 10:08:10', 'admin', 1),
(2, 'customer@wl.com', 'bNAYlng/1cRIXCNj5HRpwA==:e17653b71e47f416b605d7c3032146027fa6e20c1c5486895f46aa8eeb131d13', '2024-04-29 11:42:19', 'customer', 1),
(3, 'rental@wl.com', 'DYPMS/Rho/zFakp+JKGrLQ==:d3ba06defd2e94da6a67edd12d0b522a4fad506499de7c19b55cb3158859f711', '2024-04-29 11:53:58', 'rentalservice', 1),
(4, 'guide@wl.com', 'j2rQfSk+Hc++b0YHNd956Q==:5a34ea7568d7bd1db16dce1b1bc3d0737c8e81d29944e64212135654e8cf3726', '2024-04-29 13:51:38', 'guide', 1),
(5, 'rental2@wl.com', 'q/4meq33/S45baddWLbFAw==:ed67f2690f09dad0a8d584fe322c9be4d28a0e84d3f8dee99da4eaed91719799', '2024-04-29 13:53:39', 'rentalservice', 1),
(6, 'gayandeerajapaksha110@gmail.com', '3aeHK2ke1GpljQWrmHZGDg==:30952e6791c3e577b80b00b3460bfb03cc06c7a4a2ee69dc57399fd1bc725106', '2024-04-29 14:57:38', 'guide', 0),
(7, 'gayandeerajapaksha10@gmail.com', 'fqDVeZeLQWf6NPv9yaZSYQ==:8b07876d92274be36af6d5a6dab8b8ce62116b671b060b6211337f988455a25b', '2024-04-29 14:58:22', 'guide', 0),
(8, 'guide2@wl.com', 'GG5yQh1FNYn2ckfSj3UYmg==:53c29293cd758c6a5e494a87831910f7fba0168b18573e9c8b0b834b2591eb35', '2024-04-29 14:59:58', 'guide', 0),
(9, 'guide7@wl.com', 'R8BrwCEGgCI1u8fO2yW+PA==:dc86974da51a13398bb13a33adac8509cfe1a7985db49850fba75efbaa70df07', '2024-04-29 15:09:15', 'guide', 1),
(10, 'guide10@wl.com', 'IgIsJCVbbnQync7+AcWRsg==:c1e860b8f5197a1348ca1e9162a699ad611d325a074711f0b8f2e84494fa8565', '2024-04-29 23:55:01', 'guide', 1),
(11, 'customer2@wl.com', 'AGhh282J3YZFlsrKkDKbfw==:7c48c547b6fb59cb9079e84cc56049870778df079453b3afa7bd6fe07c7d35bb', '2024-04-30 01:46:26', 'customer', 1),
(12, 'rental5@wl.com', '0Jel0kiWrx9cHFsEpdv27A==:22b9c2eebd6bcc7dbdab1517101a4764f11433735b0443d2073fbc3b3c7bbcf9', '2024-04-30 02:03:13', 'rentalservice', 1);

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
(1, 1, '87f4b34ee2c19678a6fd126a1f7f4a25044bddc73ab6af9bf6971b1023606527', 'admin@wl.com'),
(6, 6, '21c42a4e6d219f8fbf359b1e1f3a17c3f400e59afb37debc2a6ef7657fbf2360', 'gayandeerajapaksha110@gmail.com'),
(7, 7, '81aacadf20560f16d8e06d1c57167e2f797fbf0231de25249daec9a6013238d2', 'gayandeerajapaksha10@gmail.com'),
(8, 8, '763bfc6e7eb28a1e80dc02004619e48aa71ca1eaeb7d248bb1999d2249df6264', 'guide2@wl.com'),
(9, 9, '79143737c3d172aa5fb10689d603f5cef31cb874d9c214faada4d72411c463dc', 'guide7@wl.com');

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
-- Indexes for table `guide_availability`
--
ALTER TABLE `guide_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guide_id` (`guide_id`);

--
-- Indexes for table `guide_booking`
--
ALTER TABLE `guide_booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guide_id` (`guide_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `guide_profile`
--
ALTER TABLE `guide_profile`
  ADD PRIMARY KEY (`guide_id`);

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
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guide_id` (`guide_id`);

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
-- Indexes for table `rental_settings`
--
ALTER TABLE `rental_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rent_complaint`
--
ALTER TABLE `rent_complaint`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `reset_tokens`
--
ALTER TABLE `reset_tokens`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `guide_availability`
--
ALTER TABLE `guide_availability`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `guide_booking`
--
ALTER TABLE `guide_booking`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rent`
--
ALTER TABLE `rent`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rental_services`
--
ALTER TABLE `rental_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rental_settings`
--
ALTER TABLE `rental_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rent_complaint`
--
ALTER TABLE `rent_complaint`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rent_item`
--
ALTER TABLE `rent_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `rent_pay`
--
ALTER TABLE `rent_pay`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rent_request`
--
ALTER TABLE `rent_request`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rent_return_complaints`
--
ALTER TABLE `rent_return_complaints`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reset_tokens`
--
ALTER TABLE `reset_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tips`
--
ALTER TABLE `tips`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `verification`
--
ALTER TABLE `verification`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
