<?php
// models/Pitch.php

class Pitch {
    private $conn;
    private $table_name = "PITCHES";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE PITCH_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

    public function getBySportCentreAndCategory($sportCentreId, $category) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE SPORT_CENTRES_ID = ? AND CATEGORY = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $sportCentreId);
        $stmt->bindParam(2, $category);
        $stmt->execute();
        return $stmt;
    }


    public function getBySportCentre($sportCentreId) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE SPORT_CENTRES_ID = '$sportCentreId'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
