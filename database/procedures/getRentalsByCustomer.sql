BEGIN
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
    INNER JOIN rent_pay rp ON r.id = rp.rent_id
    INNER JOIN payment p ON rp.payment_id = p.id
    WHERE 
        r.customer_id = customer_id_param
    GROUP BY 
        r.id
    ORDER BY 
        r.start_date;
END



BEGIN

    DECLARE today DATE;
    SET today = CURDATE();

    SET @baseQuery = "SELECT 
                        r.id, 
                        r.start_date AS `start`, 
                        r.end_date AS `end`, 
                        GROUP_CONCAT(e.name SEPARATOR ', ') AS `equipment_names`,
                        p.status AS `payment_status`,
                        r.status AS `rent_status`
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
    

    
END




-- BEGIN
--     -- Define variables for dynamic date filtering
--     DECLARE today DATE;
--     SET today = CURDATE();

--     -- Common base of the query
--     SET @baseQuery = "FROM rent 
--                       JOIN rent_pay ON rent.id = rent_pay.rent_id
--                       JOIN payment ON rent_pay.payment_id = payment.id
--                       JOIN rent_request ON rent.id = rent_request.rent_id
--                       WHERE rent.rentalservice_id = ? 
--                       AND payment.status = 'completed' ";

--     CASE 
        
--         WHEN filterType = 'ALL' THEN
--             SET @specificFilter = "";

--         WHEN filterType = 'pending' THEN
--             SET @specificFilter = "AND rent.status = 'pending'";

        
--         WHEN filterType = 'today' THEN
--             SET @specificFilter = "AND rent.start_date = CURDATE() AND rent.status = 'accepted'";
        
--         WHEN filterType = 'upcoming' THEN
--             SET @specificFilter = "AND rent.start_date > CURDATE() AND rent.status = 'accepted'";
        
--         WHEN filterType = 'not rented' THEN
--             SET @specificFilter = "AND rent.start_date < CURDATE() AND rent.status = 'accepted'";
        
--         WHEN filterType = 'Rented' THEN
--             SET @specificFilter = "AND rent.status = 'rented'";
        
--         WHEN filterType = 'completed' THEN
--             SET @specificFilter = "AND rent.status = 'completed'";
        
--         WHEN filterType = 'overdued' THEN
--             SET @specificFilter = "AND rent.end_date < CURDATE() AND rent.status = 'rented'";
        
--         WHEN filterType = 'cancelled' THEN
--             SET @specificFilter = "AND rent.status = 'cancelled'";
        
--         ELSE
--             SET @specificFilter = "";
--     END CASE;

--     SET @SQL = CONCAT("SELECT rent.*, payment.status AS payment_status, rent_request.customer_req AS customer_req, rent_request.rentalservice_req AS rentalservice_req ", @baseQuery, @specificFilter);
--     PREPARE stmt FROM @SQL;
--     SET @rentalserviceID = rentalserviceID;
--     EXECUTE stmt USING @rentalserviceID;
--     DEALLOCATE PREPARE stmt;
-- END