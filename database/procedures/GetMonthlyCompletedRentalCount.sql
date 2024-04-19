DELIMITER $$

CREATE PROCEDURE `GetMonthlyCompletedRentalCount`(
    IN service_id INT
)
BEGIN
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

DELIMITER ;
