DELIMITER $$

CREATE TRIGGER AfterRentReturnIssueInsert
AFTER INSERT ON rent_return_issues
FOR EACH ROW
BEGIN
    UPDATE rent
    SET status = 'return_reported'
    WHERE id = NEW.rent_id;
END$$

DELIMITER ;
