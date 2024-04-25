DELIMITER $$

CREATE TRIGGER `AfterPaymentStatusCompleted` 
AFTER UPDATE ON `payment` 
FOR EACH ROW 
BEGIN
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
END$$

DELIMITER ;
