DELIMITER $$

CREATE TRIGGER delete_old_messages
BEFORE INSERT ON MESSAGES
FOR EACH ROW
BEGIN
    DELETE FROM MESSAGES 
    WHERE MESSAGE_DATE <= NOW() - INTERVAL 30 DAY;
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER delete_message_after
AFTER DELETE ON MESSAGES
FOR EACH ROW
BEGIN
    INSERT INTO AVAILABLE_MESSAGE_IDS (ID)
    VALUES (OLD.MESSAGE_ID);
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER check_match_assignment_league
BEFORE INSERT ON LEAGUE_MATCHES
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM TOURNAMENT_MATCHES
        WHERE MATCH_ID = NEW.MATCH_ID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El partido ya está asignado a un torneo y no puede ser asignado a una liga.';
    END IF;
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER check_match_assignment_tournament
BEFORE INSERT ON TOURNAMENT_MATCHES
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM LEAGUE_MATCHES
        WHERE MATCH_ID = NEW.MATCH_ID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El partido ya está asignado a una liga y no puede ser asignado a un torneo.';
    END IF;
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER ensure_referee_assigned_league
BEFORE INSERT ON LEAGUE_MATCHES
FOR EACH ROW
BEGIN
    DECLARE referee_id VARCHAR(6);

    SELECT REFEREE_ID INTO referee_id
    FROM MATCHES
    WHERE MATCH_ID = NEW.MATCH_ID;

    IF referee_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Un partido de una liga debe tener asignado un árbitro.';
    END IF;
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER ensure_referee_assigned_tournament
BEFORE INSERT ON TOURNAMENT_MATCHES
FOR EACH ROW
BEGIN
    DECLARE referee_id VARCHAR(6);

    SELECT REFEREE_ID INTO referee_id
    FROM MATCHES
    WHERE MATCH_ID = NEW.MATCH_ID;

    IF referee_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Un partido de un torneo debe tener asignado un árbitro.';
    END IF;
END$$

DELIMITER ;



