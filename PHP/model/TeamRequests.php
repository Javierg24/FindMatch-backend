<?php
class TeamRequests
{

    private $conn;
    private $table_name = "TEAM_REQUESTS";
    private $team_members_table = "TEAM_MEMBERS";

    public $user_id;
    public $team_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function obtenerSolicitudes($team_id)
    {
        $query = "
        SELECT 
            r.REQUEST_ID,
            r.TEAM_ID,
            r.USER_ID,
            p.USER_NAME AS nombre,
            p.SURNAME AS apellido,
            u.HANDLE AS nombre_usuario,
            u.EMAIL AS correo,
            u.PICTURE AS user_picture,
            r.REQUEST_DATE
        FROM 
            " . $this->table_name . " r
        JOIN 
            users u ON r.USER_ID = u.USER_ID
        JOIN 
            players p ON u.USER_ID = p.PLAYER_ID
        WHERE 
            r.TEAM_ID = :team_id AND r.REQUEST_STATUS = 'PENDING'
        ORDER BY 
            r.REQUEST_DATE DESC
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    // Aceptar solicitud: insertar en miembros del equipo y eliminar la solicitud
    public function aceptarSolicitud($user_id, $team_id)
    {
        $this->conn->beginTransaction();

        // Insertar en la tabla TEAM_MEMBERS
        $queryInsert = "INSERT INTO " . $this->team_members_table . " (USER_ID, TEAM_ID) VALUES (:user_id, :team_id)";
        $stmtInsert = $this->conn->prepare($queryInsert);
        $stmtInsert->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmtInsert->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $stmtInsert->execute();

        // Eliminar de las solicitudes
        $queryDelete = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id AND team_id = :team_id";
        $stmtDelete = $this->conn->prepare($queryDelete);
        $stmtDelete->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmtDelete->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $stmtDelete->execute();

        $this->conn->commit();
        return true;
    }

    // Rechazar solicitud: solo eliminar la solicitud
    public function rechazarSolicitud($user_id, $team_id)
    {
        $queryUpdate = "UPDATE " . $this->table_name . " 
                    SET REQUEST_STATUS = 'REJECTED' 
                    WHERE USER_ID = :user_id AND TEAM_ID = :team_id AND REQUEST_STATUS = 'PENDING'";
        $stmtUpdate = $this->conn->prepare($queryUpdate);
        $stmtUpdate->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        return $stmtUpdate->execute();
    }


    // En TeamRequests.php

    public function eliminarJugadorDelEquipo($user_id, $team_id)
    {
        $queryDelete = "DELETE FROM " . $this->team_members_table . " WHERE USER_ID = :user_id AND TEAM_ID = :team_id";
        $stmtDelete = $this->conn->prepare($queryDelete);
        $stmtDelete->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmtDelete->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        return $stmtDelete->execute();
    }


}
