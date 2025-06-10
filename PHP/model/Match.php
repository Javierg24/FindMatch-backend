<?php

// models/Match.php
class Matches
{

    private $conn;

    private $table_name = "MATCHES";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Historial de partidos (pasados) del equipo $teamId, validando que el usuario pertenece a ese equipo
    public function getPastMatches($userId, $teamId)
    {
        $query = "SELECT 
                    m.MATCH_ID,
                    m.MATCH_DATE_TIME,
                    m.PRICE,
                    m.HOME_TEAM,
                    m.AWAY_TEAM,
                    t.TEAM_NAME AS HOME_TEAM_NAME,
                    tr.TEAM_NAME AS AWAY_TEAM_NAME,
                    m.HOME_GOALS,
                    m.AWAY_GOALS,
                    p.PITCH_NAME,
                    t.TEAM_TYPE,
                    sc.LOCATION
                FROM MATCHES m
                JOIN TEAMS t ON m.HOME_TEAM = t.TEAM_ID
                LEFT JOIN TEAMS tr ON m.AWAY_TEAM = tr.TEAM_ID
                JOIN PITCHES p ON m.PITCH_ID = p.PITCH_ID
                JOIN SPORT_CENTRES sc ON p.SPORT_CENTRES_ID = sc.SPORT_CENTRE_ID
                JOIN TEAM_MEMBERS tm ON (tm.TEAM_ID = m.HOME_TEAM OR tm.TEAM_ID = m.AWAY_TEAM)
                WHERE tm.USER_ID = :userId
                AND (m.HOME_TEAM = :teamId OR m.AWAY_TEAM = :teamId)
                AND m.MATCH_DATE_TIME < NOW()
                ORDER BY m.MATCH_DATE_TIME DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as &$match) {
            $dt = new DateTime($match['MATCH_DATE_TIME']);
            $match['fecha'] = $dt->format('Y-m-d');
            $match['hora'] = $dt->format('H:i');
            $match['precio'] = number_format($match['PRICE'], 2);
        }

        return $results;
    }

    // Próximos partidos del equipo $teamId, validando que el usuario pertenece a ese equipo
    public function getUpcomingMatches($userId, $teamId)
    {
        $query = "SELECT 
            DISTINCT m.MATCH_ID,
            m.MATCH_DATE_TIME,
            m.PRICE,
            m.HOME_TEAM,
            m.AWAY_TEAM,
            t.TEAM_NAME AS HOME_TEAM_NAME,
            tr.TEAM_NAME AS AWAY_TEAM_NAME,                   
            p.PITCH_NAME,
            t.TEAM_TYPE,
            sc.LOCATION
        FROM MATCHES m
        JOIN TEAMS t ON m.HOME_TEAM = t.TEAM_ID
        LEFT JOIN TEAMS tr ON m.AWAY_TEAM = tr.TEAM_ID
        JOIN PITCHES p ON m.PITCH_ID = p.PITCH_ID
        JOIN SPORT_CENTRES sc ON p.SPORT_CENTRES_ID = sc.SPORT_CENTRE_ID
        JOIN TEAM_MEMBERS tm ON tm.USER_ID = :userId AND (tm.TEAM_ID = m.HOME_TEAM OR tm.TEAM_ID = m.AWAY_TEAM)
        WHERE (m.HOME_TEAM = :teamId OR m.AWAY_TEAM = :teamId)
        AND m.MATCH_DATE_TIME >= NOW()
        ORDER BY m.MATCH_DATE_TIME ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as &$match) {
            $dt = new DateTime($match['MATCH_DATE_TIME']);
            $match['fecha'] = $dt->format('Y-m-d');
            $match['hora'] = $dt->format('H:i');
            $match['precio'] = number_format($match['PRICE'], 2);
        }

        return $results;
    }

    // Obtener partidos disponibles para unirse con el equipo $teamId (que el usuario capitanea)
    public function getAvailableMatchesToJoin($userId, $teamId)
    {
        // 1. Obtener el TEAM_TYPE del equipo que intenta unirse
        $captainCheckQuery = "SELECT TEAM_TYPE FROM TEAMS WHERE TEAM_ID = :teamId";
        $stmtCheck = $this->conn->prepare($captainCheckQuery);
        $stmtCheck->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() === 0) {
            throw new Exception("Error al mostrar partidos para unirse");
        }

        $teamData = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        $teamType = $teamData['TEAM_TYPE'];

        // 2. Buscar partidos donde:
        // - No haya equipo visitante aún
        // - El equipo no sea el local
        // - Coincida el tipo de equipo
        // - La hora sea al menos 4 horas después de la actual
        $query = "SELECT 
                m.MATCH_ID,
                m.MATCH_DATE_TIME,
                m.PRICE,
                m.HOME_TEAM,
                t.TEAM_NAME AS HOME_TEAM_NAME,
                p.PITCH_NAME,
                t.TEAM_TYPE,
                sc.LOCATION
            FROM MATCHES m
            JOIN TEAMS t ON m.HOME_TEAM = t.TEAM_ID
            JOIN PITCHES p ON m.PITCH_ID = p.PITCH_ID
            JOIN SPORT_CENTRES sc ON p.SPORT_CENTRES_ID = sc.SPORT_CENTRE_ID
            WHERE m.AWAY_TEAM IS NULL
            AND m.HOME_TEAM != :teamId
            AND t.TEAM_TYPE = :teamType
            AND m.MATCH_DATE_TIME > DATE_ADD(NOW(), INTERVAL 4 HOUR)
            ORDER BY m.MATCH_DATE_TIME ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $stmt->bindParam(':teamType', $teamType, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as &$match) {
            $dt = new DateTime($match['MATCH_DATE_TIME']);
            $match['fecha'] = $dt->format('Y-m-d');
            $match['hora'] = $dt->format('H:i');
            $match['precio'] = number_format($match['PRICE'], 2);
        }

        return $results;
    }

    // Unirse a un partido como equipo visitante, usando el equipo $teamId, validando que el usuario es capitán de ese equipo
    public function joinMatchAsAwayTeam($matchId, $userId, $teamId)
    {
        // Validamos que el usuario es capitán del equipo $teamId
        $teamQuery = "SELECT TEAM_ID FROM TEAMS WHERE TEAM_ID = :teamId AND CAPTAIN = :userId";
        $stmtTeam = $this->conn->prepare($teamQuery);
        $stmtTeam->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $stmtTeam->bindParam(':userId', $userId);
        $stmtTeam->execute();

        if ($stmtTeam->rowCount() === 0) {
            throw new Exception("El usuario no es capitán del equipo que quiere usar para unirse al partido.");
        }

        // Validamos que el equipo no sea ya el local en ese partido
        $homeCheckQuery = "SELECT MATCH_ID FROM MATCHES WHERE MATCH_ID = :matchId AND HOME_TEAM = :teamId";
        $stmtHomeCheck = $this->conn->prepare($homeCheckQuery);
        $stmtHomeCheck->bindParam(':matchId', $matchId, PDO::PARAM_INT);
        $stmtHomeCheck->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $stmtHomeCheck->execute();

        if ($stmtHomeCheck->rowCount() > 0) {
            throw new Exception("El equipo no puede unirse como visitante a un partido donde ya es local.");
        }

        // Actualizamos el partido si está disponible
        $updateQuery = "UPDATE MATCHES 
                        SET AWAY_TEAM = :awayTeamId
                        WHERE MATCH_ID = :matchId
                        AND AWAY_TEAM IS NULL
                        AND MATCH_DATE_TIME > DATE_ADD(NOW(), INTERVAL 4 HOUR)";

        $stmtUpdate = $this->conn->prepare($updateQuery);
        $stmtUpdate->bindParam(':awayTeamId', $teamId, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':matchId', $matchId, PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            if ($stmtUpdate->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    // Crear partido con equipo local $teamId, validando que el usuario es capitán de ese equipo
    public function createMatch($data, $userId, $teamId)
    {
        // Validamos que el usuario es capitán
        $checkQuery = "SELECT TEAM_ID FROM TEAMS WHERE TEAM_ID = :teamId AND CAPTAIN = :userId";
        $stmtCheck = $this->conn->prepare($checkQuery);
        $stmtCheck->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $stmtCheck->bindParam(':userId', $userId);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() === 0) {
            throw new Exception("No eres el capitán del equipo seleccionado.");
        }

        $matchDateTime = $data['match_date'] . ' ' . $data['match_time']; // Ej: 2025-06-02 18:00
        $pitchId = $data['pitch_id'];
        $awayTeam = $data['away_team'] ?? null;
        $price = $data['price'];

        $dateTime = DateTime::createFromFormat('Y-m-d H:i', $matchDateTime);
        $now = new DateTime();

        // Validación: mínimo 4 horas desde ahora
        $minDateTime = (clone $now)->modify('+4 hours');
        if ($dateTime < $minDateTime) {
            throw new Exception("El partido debe programarse con al menos 4 horas de antelación.");
        }

        // Validación: hora exacta
        if ((int) $dateTime->format('i') !== 0) {
            throw new Exception("El partido debe comenzar en punto (ej. 14:00, 15:00).");
        }

        // Validación: comprobar si ya hay partido a esa hora en esa pista
        $conflictQuery = "SELECT 1 FROM MATCHES 
                      WHERE PITCH_ID = :pitchId 
                      AND MATCH_DATE_TIME = :matchDateTime";
        $stmtConflict = $this->conn->prepare($conflictQuery);
        $stmtConflict->bindParam(':pitchId', $pitchId, PDO::PARAM_INT);
        $stmtConflict->bindParam(':matchDateTime', $matchDateTime);
        $stmtConflict->execute();

        if ($stmtConflict->rowCount() > 0) {
            throw new Exception("Ya existe un partido en esta pista a esa hora.");
        }

        // Insertar si todo está correcto
        $insertQuery = "INSERT INTO MATCHES 
            (MATCH_DATE_TIME, PITCH_ID, HOME_TEAM, AWAY_TEAM, REFEREE_ID, HOME_GOALS, AWAY_GOALS, PRICE)
            VALUES
            (:matchDateTime, :pitchId, :homeTeam, :awayTeam, NULL, NULL, NULL, :price)";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bindParam(':matchDateTime', $matchDateTime);
        $stmt->bindParam(':pitchId', $pitchId, PDO::PARAM_INT);
        $stmt->bindParam(':homeTeam', $teamId, PDO::PARAM_INT);
        $stmt->bindParam(':awayTeam', $awayTeam, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);

        return $stmt->execute();
    }

    public function cancelMatch($teamId, $matchId)
    {
        // Comprobar si el equipo participa en el partido
        $query = "SELECT HOME_TEAM, AWAY_TEAM FROM MATCHES WHERE MATCH_ID = :matchId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matchId', $matchId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new Exception("El partido no existe.");
        }

        $match = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($match['HOME_TEAM'] == $teamId) {
            // El equipo que cancela es el local -> eliminar partido completamente
            $deleteQuery = "DELETE FROM MATCHES WHERE MATCH_ID = :matchId AND HOME_TEAM = :teamId";
            $stmtDelete = $this->conn->prepare($deleteQuery);
            $stmtDelete->bindParam(':matchId', $matchId, PDO::PARAM_INT);
            $stmtDelete->bindParam(':teamId', $teamId, PDO::PARAM_INT);
            $stmtDelete->execute();

            if ($stmtDelete->rowCount() === 0) {
                throw new Exception("No se pudo eliminar el partido. Verifica que eres el equipo local.");
            }

            return 'deleted';

        } elseif ($match['AWAY_TEAM'] == $teamId) {
            // El equipo que cancela es el visitante -> poner AWAY_TEAM en NULL
            $updateQuery = "UPDATE MATCHES SET AWAY_TEAM = NULL WHERE MATCH_ID = :matchId AND AWAY_TEAM = :teamId";
            $stmtUpdate = $this->conn->prepare($updateQuery);
            $stmtUpdate->bindParam(':matchId', $matchId, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':teamId', $teamId, PDO::PARAM_INT);
            $stmtUpdate->execute();

            if ($stmtUpdate->rowCount() === 0) {
                throw new Exception("No se pudo cancelar la participación. Verifica que eres el equipo visitante.");
            }

            return 'left';

        } else {
            throw new Exception("El equipo no participa en este partido.");
        }
    }


}

?>