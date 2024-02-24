BEGIN
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
END