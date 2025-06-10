<?php
// controllers/SportCentreController.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include_once '../connection/database.php';
include_once '../model/Pitch.php';

// Crear conexión a la base de datos
$database = new Database();
$db = $database->getConnection();
$pitch = new Pitch($db);

// Manejar solicitudes
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['sport_centre_id'])) {
            $id = $_GET['sport_centre_id'];
            $category = isset($_GET['category']) ? intval($_GET['category']) : null;

            error_log("ID recibido: " . $id);
            if ($category !== null) {
                $stmt = $pitch->getBySportCentreAndCategory($id, $category);
            } else {
                $stmt = $pitch->getBySportCentre($id);
            }

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'pitches' => $rows]);
            exit();
        }
    break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    break;
}
