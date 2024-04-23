DELIMITER $$

CREATE PROCEDURE GetEquipmentRentalCountByRental(IN start_date DATE, IN end_date DATE, IN rentalservice_id INT)
BEGIN
    SELECT 
        e.name AS equipment_name,
        e.id AS equipment_id,
        COUNT(r.id) AS rental_count
    FROM rent r
    JOIN rent_item ri ON r.id = ri.rent_id
    JOIN item i ON ri.item_id = i.id
    JOIN equipment e ON i.equipment_id = e.id
    WHERE r.rentalservice_id = rentalservice_id 
      AND r.start_date >= start_date
      AND r.end_date <= end_date
      AND r.status IN ('rented', 'completed') -- Assumes these statuses mean the equipment was in use
    GROUP BY e.id, e.name
    ORDER BY rental_count DESC;
END$$

DELIMITER ;
