DELIMITER $$

CREATE PROCEDURE GetFirstUpcomingRent(IN customer_id INT)
BEGIN
    SELECT 
        r.id AS rent_id,
        r.start_date,
        r.end_date,
        e.image AS equipment_image,
        rs.name AS rental_service_name,
        rs.address AS rental_service_address,
        rs.mobile AS rental_service_mobile,
        rs.image AS rental_service_image
    FROM rent r
    JOIN rent_item ri ON r.id = ri.rent_id
    JOIN item i ON ri.item_id = i.id
    JOIN equipment e ON i.equipment_id = e.id
    JOIN rental_services rs ON e.rentalservice_id = rs.id
    WHERE r.customer_id = customer_id
      AND r.start_date > CURRENT_DATE()
    ORDER BY r.start_date ASC
    LIMIT 1;
END$$

DELIMITER ;
