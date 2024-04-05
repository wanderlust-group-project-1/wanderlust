BEGIN
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

END

-- Path: test.sql

DELIMITER $$

CREATE PROCEDURE GetAvailableEquipmentByRental(
    IN RentalServiceID INT,
    IN StartTime DATETIME,
    IN EndTime DATETIME)
BEGIN
    SELECT e.EquipmentID, e.Name
    FROM Equipment e
    INNER JOIN item i ON e.id = i.equipment_id
    INNER JOIN rent_item ri ON i.id = ri.item_id
    INNER JOIN rent r ON ri.rent_id = r.id
    WHERE e.rentalservice_id = RentalServiceID
    AND r.start_date >= StartTime AND r.end_date <= EndTime
    AND NOT EXISTS (
        SELECT 1 FROM rent_item ri2
        INNER JOIN rent r2 ON ri2.rent_id = r2.id
        WHERE ri2.item_id = i.id
        AND ((r2.start_date < EndTime AND r2.end_date > StartTime))
    );
END$$

DELIMITER ;


-- sample call to the stored procedure
EXEC GetAvailableEquipmentByRental 1, '2021-01-01 00:00:00', '2021-01-01 23:59:59';



-- BEGIN
--     SELECT e.id AS EquipmentID, 
--            e.name AS EquipmentName, 
--           GROUP_CONCAT( i.id) AS itemID,
--            COUNT(DISTINCT i.id) AS AvailableCount,
--            e.count
--     FROM equipment e
--     JOIN item i ON e.id = i.equipment_id
--     LEFT JOIN rent_item ri ON i.id = ri.item_id
--     JOIN rent r ON ri.rent_id = r.id
    
--     WHERE (r.start_date > EndTime OR r.end_date < StartTime)
--       AND e.rentalservice_id = RentalServiceID
--     GROUP BY e.id, e.name;
-- END

BEGIN
    SELECT e.id AS EquipmentID, 
           e.name AS EquipmentName, 
          GROUP_CONCAT( i.id) AS itemID,
           COUNT(DISTINCT i.id) AS AvailableCount,
           e.count
    FROM equipment e
    JOIN item i ON e.id = i.equipment_id
    LEFT JOIN rent_item ri ON i.id = ri.item_id
    JOIN rent r ON ri.rent_id = r.id
    
    WHERE i.id NOT IN (
        SELECT i.id
        FROM item i
        JOIN rent_item ri ON i.id = ri.item_id
        JOIN rent r ON ri.rent_id = r.id
        WHERE r.start_date <= EndTime AND r.end_date >= StartTime
    )
      AND e.rentalservice_id = RentalServiceID

END


-- BEGIN
--     SELECT item.*
--     FROM item
--     LEFT JOIN rent_item ON item.id = rent_item.item_id
--     LEFT JOIN rent ON rent_item.rent_id = rent.id
--     WHERE item.equipment_id = equipmentID AND (
--         rent.start_date > endDate OR
--         rent.end_date < startDate OR
--         rent.id IS NULL
--     );
-- END

BEGIN
    SELECT i.*
    FROM item i
    LEFT JOIN rent_item ON i.id = rent_item.item_id
    LEFT JOIN rent ON rent_item.rent_id = rent.id
    WHERE i.equipment_id = equipmentID AND 
          i.id NOT IN (
              SELECT ri.item_id
              FROM rent_item ri
              JOIN rent r ON ri.rent_id = r.id
              WHERE r.start_date <= EndTime AND r.end_date >= StartTime
          )
    GROUP BY i.id;
END





BEGIN
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
END