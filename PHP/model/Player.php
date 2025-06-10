<?php

class Player {
    private $conn;
    private $table_user = 'USERS';
    private $table_player = 'PLAYERS';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para registrar un jugador
    public function registerPlayer($playerData) {
        try {
            // Generar un ID único para el usuario
            $user_id = strtoupper(substr(uniqid(), -6));

            // Comenzar una transacción
            $this->conn->beginTransaction();

            // Inserción en la tabla USERS
            $query_user = "INSERT INTO {$this->table_user} (USER_ID, HANDLE, EMAIL, PASSWD) 
                           VALUES (:user_id, :handle, :email, :passwd)";
            $stmt_user = $this->conn->prepare($query_user);

            // Vincular parámetros para el usuario
            $stmt_user->bindParam(':user_id', $user_id);
            $stmt_user->bindParam(':handle', $playerData['handle']);
            $stmt_user->bindParam(':email', $playerData['email']);            
            $stmt_user->bindParam(':passwd', $playerData['password']);

            if (!$stmt_user->execute()) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Error al registrar el usuario'];
            }

            // Inserción en la tabla PLAYERS
            $query_player = "INSERT INTO {$this->table_player} (PLAYER_ID, USER_NAME, SURNAME, PRIMARY_POSITION, SECONDARY_POSITION)
                             VALUES (:player_id, :user_name, :surname, :primary_position, :secondary_position)";
            $stmt_player = $this->conn->prepare($query_player);

            // Vincular parámetros para el jugador
            $stmt_player->bindParam(':player_id', $user_id);
            $stmt_player->bindParam(':user_name', $playerData['user_name']);
            $stmt_player->bindParam(':surname', $playerData['surname']);
            $stmt_player->bindParam(':primary_position', $playerData['primary_position']);
            $stmt_player->bindParam(':secondary_position', $playerData['secondary_position']);

            if (!$stmt_player->execute()) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Error al registrar el jugador'];
            }

            // Confirmar la transacción
            $this->conn->commit();
            return ['success' => true, 'message' => 'Usuario y jugador registrados correctamente'];

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()];
        }
    }

    // Método para verificar si el correo electrónico ya está registrado
    public function getUserByEmail($email) {
        $query = "SELECT * FROM {$this->table_user} WHERE EMAIL = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
