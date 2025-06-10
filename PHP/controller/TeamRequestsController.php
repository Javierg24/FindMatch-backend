<?php
// controllers/TeamRequestsController.php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    http_response_code(200);
    exit();
}


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include_once '../connection/database.php';
include_once '../model/TeamRequests.php';

$database = new Database();
$db = $database->getConnection();
$teamRequests = new TeamRequests($db);

// Permitir preflight OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Obtener solicitudes para un equipo (team_id como parámetro GET)
        if (isset($_GET['team_id'])) {
            $team_id = intval($_GET['team_id']);
            $solicitudes = $teamRequests->obtenerSolicitudes($team_id);

            if ($solicitudes !== false) {
                echo json_encode(['status' => 'success', 'data' => $solicitudes]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Error al obtener solicitudes']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Falta parámetro team_id']);
        }
        break;

    case 'POST':
        // Recibir JSON con acción: accept o reject
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['action'], $input['user_id'], $input['team_id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos en la petición']);
            exit();
        }

        $action = $input['action'];
        $user_id = $input['user_id'];
        $team_id = intval($input['team_id']);

        if ($action === 'accept') {
            $result = $teamRequests->aceptarSolicitud($user_id, $team_id);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Solicitud aceptada']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Error al aceptar solicitud']);
            }
        } elseif ($action === 'reject') {
            $result = $teamRequests->rechazarSolicitud($user_id, $team_id);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Solicitud rechazada']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Error al rechazar solicitud']);
            }
        } elseif ($action === 'removePlayer') {
            $result = $teamRequests->eliminarJugadorDelEquipo($user_id, $team_id);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Jugador eliminado del equipo']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar jugador']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Acción inválida']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Método HTTP no permitido']);
        break;
}
