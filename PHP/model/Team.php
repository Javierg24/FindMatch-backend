<?php

class Team
{
    private $conn;
    private $table_name = "TEAMS";

    public $user_id;
    public $handle;
    public $email;
    public $passwd;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getTeamsByUserId($user_id)
    {
        $sql = "SELECT DISTINCT
            T.TEAM_ID,
            T.TEAM_NAME,
            T.TEAM_ADDRESS,
            T.CREATED,
            T.TEAM_TYPE,
            T.CAPTAIN,
            T.PICTURE
        FROM TEAM_MEMBERS TM
        JOIN TEAMS T ON TM.TEAM_ID = T.TEAM_ID
        WHERE TM.USER_ID = :user_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeamsWhereUserIsCaptain($user_id)
    {
        $sql = "SELECT 
                TEAM_ID,
                TEAM_NAME,
                TEAM_ADDRESS,
                CREATED,
                TEAM_TYPE,
                CAPTAIN,
                PICTURE
            FROM " . $this->table_name . "
            WHERE CAPTAIN = :user_id
            ORDER BY CREATED DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getAllTeams()
    {
        $sql = "SELECT 
                    TEAM_ID,
                    TEAM_NAME,
                    TEAM_ADDRESS,
                    CREATED,
                    TEAM_TYPE,
                    CAPTAIN,
                    PICTURE
                FROM " . $this->table_name . "
                ORDER BY CREATED DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTeam($data)
    {
        // 1. Insertar en la tabla TEAMS
        $sql = "INSERT INTO TEAMS (TEAM_NAME, TEAM_ADDRESS, TEAM_TYPE, CAPTAIN)
                VALUES (:name, :address, :type, :captain)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':name', $data->TEAM_NAME);
        $stmt->bindParam(':address', $data->TEAM_ADDRESS);
        $stmt->bindParam(':type', $data->TEAM_TYPE);
        $stmt->bindParam(':captain', $data->CAPTAIN);

        if ($stmt->execute()) {
            // 2. Obtener el ID del equipo recién creado
            $teamId = $this->conn->lastInsertId();

            // 3. Insertar en TEAM_MEMBERS
            $sql_member = "INSERT INTO TEAM_MEMBERS (TEAM_ID, USER_ID) VALUES (:teamId, :userId)";
            $stmt_member = $this->conn->prepare($sql_member);
            $stmt_member->bindParam(':teamId', $teamId);
            $stmt_member->bindParam(':userId', $data->CAPTAIN);

            if ($stmt_member->execute()) {
                return true;
            } else {
                // Podrías loguear el error aquí si falla esta segunda inserción
                return false;
            }
        } else {
            return false;
        }
    }

public function getTeamDetailsById($team_id)
{
    // 1. Datos del equipo
    $sql_team = "SELECT 
                TEAM_ID,
                TEAM_NAME,
                TEAM_ADDRESS,
                CREATED,
                TEAM_TYPE,
                CAPTAIN,
                LEAGUE_ID,
                TOURNAMENT_ID,
                PICTURE
            FROM TEAMS
            WHERE TEAM_ID = :team_id";

    $stmt_team = $this->conn->prepare($sql_team);
    $stmt_team->bindParam(':team_id', $team_id);
    $stmt_team->execute();
    $equipo = $stmt_team->fetch(PDO::FETCH_ASSOC);

    if (!$equipo) {
        return null;
    }

    // Convertir la imagen del equipo a base64 si no está vacía
    if (!empty($equipo['PICTURE'])) {
        $equipo['PICTURE'] = 'data:image/jpeg;base64,' . base64_encode($equipo['PICTURE']);
    } else {
        $equipo['PICTURE'] = null;
    }

    // 2. Datos de los jugadores del equipo
    $sql_players = "SELECT 
                    U.USER_ID,
                    U.HANDLE,
                    U.EMAIL,
                    U.PICTURE,
                    P.USER_NAME,
                    P.SURNAME,
                    P.PRIMARY_POSITION,
                    P.SECONDARY_POSITION,
                    P.GOALS,
                    P.ASSISTS,
                    P.BIRTHDAY,
                    TIMESTAMPDIFF(YEAR, P.BIRTHDAY, CURDATE()) AS AGE
                FROM TEAM_MEMBERS TM
                JOIN USERS U ON TM.USER_ID = U.USER_ID
                JOIN PLAYERS P ON P.PLAYER_ID = U.USER_ID
                WHERE TM.TEAM_ID = :team_id";

    $stmt_players = $this->conn->prepare($sql_players);
    $stmt_players->bindParam(':team_id', $team_id);
    $stmt_players->execute();
    $jugadores = $stmt_players->fetchAll(PDO::FETCH_ASSOC);

    // Convertir las imágenes de los jugadores a base64
    foreach ($jugadores as &$jugador) {
        if (!empty($jugador['PICTURE'])) {
            $jugador['PICTURE'] = 'data:image/jpeg;base64,' . base64_encode($jugador['PICTURE']);
        } else {
            $jugador['PICTURE'] = null;
        }
    }

    return [
        'equipo' => $equipo,
        'jugadores' => $jugadores
    ];
}


}