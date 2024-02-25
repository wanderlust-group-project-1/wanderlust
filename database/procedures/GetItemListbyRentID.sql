DELIMITER $$

CREATE PROCEDURE GetItemListbyRentID(IN rent_id INT)
BEGIN
    SELECT 
        e.id AS `equipment_id`, 
        e.name AS `equipment_name`,
        i.item_number AS `item_number`
    FROM 
        rent_item ri
    INNER JOIN item i ON ri.item_id = i.id
    INNER JOIN equipment e ON i.equipment_id = e.id
    WHERE 
        ri.rent_id = rent_id
    ORDER BY 
        e.name;
END$$

DELIMITER ;
