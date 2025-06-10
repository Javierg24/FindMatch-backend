<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Incluir el modelo
include_once '../model/Player.php';
include_once '../connection/database.php';

$database = new Database();
$db = $database->getConnection();
$player = new Player($db);

// Manejar solicitudes
try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            // Leer los datos del jugador desde el cuerpo de la solicitud
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) {
                throw new Exception("Datos no válidos");
            }

            // Intentar registrar el jugador
            $result = $player->registerPlayer($data);

            // Retornar la respuesta
            echo json_encode($result);  // Devuelve la respuesta JSON
            break;
        
        default:
            echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

?>
