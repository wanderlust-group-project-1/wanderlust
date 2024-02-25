DELIMITER $$

CREATE TRIGGER RentStatus
AFTER UPDATE ON rent_request
FOR EACH ROW
BEGIN
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
END$$

DELIMITER ;
