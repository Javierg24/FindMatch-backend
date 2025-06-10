<?php
// models/User.php
// models/User.php

class User
{
    // models/User.php

    private $conn;
    private $table_name = "USERS";

    public $user_id;
    public $handle;
    public $email;
    public $passwd;



    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Obtener usuario por email y contraseña
    public function getUserByEmailAndPassword($email, $password)
    {
        try {
            $query = "SELECT 
                        u.USER_ID, u.HANDLE, u.EMAIL, u.PASSWD, u.PICTURE,
                        p.USER_NAME, p.SURNAME, p.PRIMARY_POSITION, p.SECONDARY_POSITION, 
                        p.BIRTHDAY, p.GOALS, p.ASSISTS
                    FROM USERS u
                    LEFT JOIN PLAYERS p ON u.USER_ID = p.PLAYER_ID
                    WHERE u.EMAIL = :email AND u.PASSWD = :password";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si se encuentra un usuario
            if ($user) {

                if (!empty($user['PICTURE'])) {
                    $user['PICTURE'] = 'data:image/jpeg;base64,' . base64_encode($user['PICTURE']);
                }
                // Obtener el tipo de usuario
                $userType = $this->getUserType($user['USER_ID']);

                // Crear el objeto de datos del usuario
                $userData = [
                    'handle' => $user['HANDLE'],
                    'name' => $user['USER_NAME'],
                    'surname' => $user['SURNAME'],
                    'picture' => $user['PICTURE'],
                    'primary_position' => $user['PRIMARY_POSITION'],
                    'secondary_position' => $user['SECONDARY_POSITION'],
                    'goals' => $user['GOALS'],
                    'assists' => $user['ASSISTS'],
                    'user_type' => $userType,
                    'email' => $email,
                    'userId' => $user['USER_ID'],
                ];

                return $userData;
            } else {
                return null; // Si no se encuentra el usuario
            }

        } catch (PDOException $e) {
            // Captura cualquier excepción de PDO
            echo "Error en la consulta: " . $e->getMessage();
        }
    }

    public function updateUserProfile($user_id, $user_data)
    {
        try {
            $this->conn->beginTransaction();

            // Consulta para USERS, ahora con PICTURE opcional
            $query_user = "UPDATE USERS 
                       SET HANDLE = :handle, EMAIL = :email";

            if (!empty($user_data['passwd'])) {
                $query_user .= ", PASSWD = :passwd";
            }

            if (!empty($user_data['picture'])) {
                $query_user .= ", PICTURE = :picture";
            }

            $query_user .= " WHERE USER_ID = :user_id";

            $stmt_user = $this->conn->prepare($query_user);

            // Bind obligatorios
            $stmt_user->bindParam(':handle', $user_data['handle']);
            $stmt_user->bindParam(':email', $user_data['email']);
            $stmt_user->bindParam(':user_id', $user_id);

            // Bind opcionales
            if (!empty($user_data['passwd'])) {
                $stmt_user->bindParam(':passwd', $user_data['passwd']);
            }
            if (!empty($user_data['picture'])) {
                $stmt_user->bindParam(':picture', $user_data['picture'], PDO::PARAM_LOB);
            }

            $stmt_user->execute();

            // Actualizar tabla PLAYERS (sin cambios)
            $query_player = "UPDATE PLAYERS 
                         SET USER_NAME = :user_name, SURNAME = :surname, 
                             PRIMARY_POSITION = :primary_position, 
                             SECONDARY_POSITION = :secondary_position, 
                             GOALS = :goals, ASSISTS = :assists 
                         WHERE PLAYER_ID = :user_id";

            $stmt_player = $this->conn->prepare($query_player);
            $stmt_player->bindParam(':user_name', $user_data['user_name']);
            $stmt_player->bindParam(':surname', $user_data['surname']);
            $stmt_player->bindParam(':primary_position', $user_data['primary_position']);
            $stmt_player->bindParam(':secondary_position', $user_data['secondary_position']);
            $stmt_player->bindParam(':goals', $user_data['goals'], PDO::PARAM_INT);
            $stmt_player->bindParam(':assists', $user_data['assists'], PDO::PARAM_INT);
            $stmt_player->bindParam(':user_id', $user_id);
            $stmt_player->execute();

            $this->conn->commit();

            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al actualizar el perfil: " . $e->getMessage());
            return false;
        }
    }

    // Obtener tipo de usuario
    public function getUserType($userId)
    {
        $query = "SELECT CASE 
                    WHEN EXISTS (SELECT 1 FROM REFEREES WHERE REFEREE_ID = :user_id) THEN 'REFEREE'
                    WHEN EXISTS (SELECT 1 FROM PLAYERS WHERE PLAYER_ID = :user_id) THEN 'PLAYER'
                    WHEN EXISTS (SELECT 1 FROM SPORT_CENTRES WHERE SPORT_CENTRE_ID = :user_id) THEN 'SPORT_CENTRE'
                    ELSE 'UNKNOWN' 
                   END AS USER_TYPE";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['USER_TYPE'];
    }


    public function requestJoinTeam($teamId, $userId)
    {
        try {
            // Verifica si ya hay una solicitud pendiente
            $checkQuery = "SELECT * FROM TEAM_REQUESTS 
                        WHERE TEAM_ID = :team_id AND USER_ID = :user_id AND REQUEST_STATUS = 'PENDING'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':team_id', $teamId);
            $checkStmt->bindParam(':user_id', $userId);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Ya has solicitado unirte a este equipo.'];
            }

            // Insertar nueva solicitud
            $query = "INSERT INTO TEAM_REQUESTS (TEAM_ID, USER_ID) 
                    VALUES (:team_id, :user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':team_id', $teamId);
            $stmt->bindParam(':user_id', $userId);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Solicitud de unión enviada correctamente.'];
            } else {
                return ['success' => false, 'message' => 'Error al enviar la solicitud.'];
            }

        } catch (PDOException $e) {
            error_log("Error al solicitar unirse al equipo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de servidor.'];
        }
    }


}

?>