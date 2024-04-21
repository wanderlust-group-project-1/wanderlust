DELIMITER $$

CREATE PROCEDURE `GetCurrentAcceptedRents`(IN equipmentID INT)
BEGIN
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

DELIMITER ;
