DELIMITER $$

CREATE PROCEDURE GetFirstUpcomingRentByRental(IN rentalservice_id INT)
BEGIN
    SELECT 
        r.id AS rent_id,
        r.start_date,
        r.end_date,
        e.image AS equipment_image,
        c.name AS customer_name,
        c.email AS customer_email

 
    FROM rent r
    JOIN rent_item ri ON r.id = ri.rent_id
    JOIN item i ON ri.item_id = i.id
    JOIN equipment e ON i.equipment_id = e.id
    JOIN customers c ON r.customer_id = c.id
    WHERE r.rentalservice_id = rentalservice_id
      AND r.start_date >= CURRENT_DATE()
      AND r.status = 'accepted'
    ORDER BY r.start_date ASC
    LIMIT 1;
END$$

DELIMITER ;
