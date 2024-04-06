DELIMITER $$

CREATE PROCEDURE GetFilteredPaidOrders(
    IN rentalserviceID INT,
    IN filterType VARCHAR(20)
)
BEGIN
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

DELIMITER ;
