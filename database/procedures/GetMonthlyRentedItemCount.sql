DELIMITER $$

CREATE PROCEDURE `GetMonthlyRentedItemCount`(
    IN service_id INT
)
BEGIN
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

DELIMITER ;
