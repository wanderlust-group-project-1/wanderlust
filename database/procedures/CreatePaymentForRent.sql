DELIMITER $$

CREATE PROCEDURE `CreatePaymentForRent`(
    IN rent_id INT
)
BEGIN
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
END $$

DELIMITER ;
