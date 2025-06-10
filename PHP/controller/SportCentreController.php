<?php
// controllers/SportCentreController.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../connection/database.php';
include_once '../model/SportCentre.php';

// Crear conexión a la base de datos
$database = new Database();
$db = $database->getConnection();
$sportCentre = new SportCentre($db);

// Manejar solicitudes
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Obtener un SportCentre por ID
            $stmt = $sportCentre->getById($_GET['id']);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                echo json_encode(['success' => true, 'sport_centre' => $row]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Centro deportivo no encontrado']);
            }
        } else {
            // Obtener todos los SportCentres
            $stmt = $sportCentre->getAll();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'sport_centres' => $rows]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        break;
}
