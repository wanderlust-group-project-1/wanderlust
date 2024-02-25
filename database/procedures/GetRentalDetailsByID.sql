DELIMITER $$

CREATE PROCEDURE GetRentalDetailsByID(IN rent_id_param INT)
BEGIN
    SELECT 
        r.*,
        c.name AS `customer_name`, 
        u.email AS `customer_email`,
        c.number AS `customer_number`,
        -- Aggregate equipment names and their counts into a single column
        GROUP_CONCAT(DISTINCT CONCAT(sub.equipment_name, ' (', sub.equipment_count, ')') ORDER BY sub.equipment_name SEPARATOR ', ') AS `equipment_list`
    FROM 
        rent r
    INNER JOIN customers c ON r.customer_id = c.id
    INNER JOIN users u ON c.user_id = u.id
    -- Subquery to calculate equipment counts
    INNER JOIN (
        SELECT 
            ri.rent_id, 
            e.name AS `equipment_name`, 
            COUNT(e.id) AS `equipment_count`
        FROM 
            rent_item ri
        INNER JOIN item i ON ri.item_id = i.id
        INNER JOIN equipment e ON i.equipment_id = e.id
        GROUP BY 
            ri.rent_id, e.name
    ) AS sub ON r.id = sub.rent_id
    WHERE 
        r.id = rent_id_param
    GROUP BY 
        r.id;
END$$

DELIMITER ;
