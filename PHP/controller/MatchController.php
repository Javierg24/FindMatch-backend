<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include_once '../model/Match.php';
include_once '../connection/database.php';

$database = new Database();
$db = $database->getConnection();
$match = new Matches($db);

// Responder a preflight OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    switch ($_SERVER['REQUEST_METHOD']) {

        case 'GET':
            $type = $_GET['type'] ?? null;
            $userId = $_GET['user_id'] ?? null;
            $teamId = $_GET['team_id'] ?? null;

            if (!$userId || !$teamId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Faltan parámetros user_id o team_id']);
                exit();
            }

            if ($type === 'past') {
                $result = $match->getPastMatches($userId, $teamId);
            } elseif ($type === 'upcoming') {
                $result = $match->getUpcomingMatches($userId, $teamId);
            } elseif ($type === 'available') {
                $result = $match->getAvailableMatchesToJoin($userId, $teamId);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Parámetro "type" inválido o no proporcionado (usa "past", "upcoming" o "available")']);
                exit();
            }

            echo json_encode(['success' => true, 'matches' => $result]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Datos no válidos']);
                exit();
            }

            $action = $data['action'] ?? null;

            if (!$action) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Acción no proporcionada']);
                exit();
            }

            if ($action === 'join' || $action === 'create') {
                $userId = $data['user_id'] ?? null;
                $teamId = $data['team_id'] ?? null;

                if (!$userId || !$teamId) {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'message' => 'Faltan user_id o team_id']);
                    exit();
                }
            }

            if ($action === 'cancel') {
                $teamId = $data['team_id'] ?? null;
                $matchId = $data['match_id'] ?? null;

                if (!$teamId || !$matchId) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Faltan team_id o match_id para cancelar el partido']);
                    exit();
                }

                $cancelled = $match->cancelMatch($teamId, $matchId);

                echo json_encode([
                    'success' => $cancelled,
                    'message' => $cancelled ? 'Partido cancelado exitosamente.' : 'No se pudo cancelar el partido.'
                ]);
                break;
            }

            if ($action === 'join') {
                $matchId = $data['match_id'] ?? null;

                if (!$matchId) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Falta match_id']);
                    exit();
                }

                $joined = $match->joinMatchAsAwayTeam($matchId, $userId, $teamId);
                echo json_encode([
                    'success' => $joined,
                    'message' => $joined ? 'Equipo unido al partido exitosamente.' : 'No se pudo unir al partido (ya tiene equipo visitante o está muy cerca la hora).'
                ]);
                break;
            }

            if ($action === 'create') {
                $created = $match->createMatch($data, $userId, $teamId);
                echo json_encode([
                    'success' => $created,
                    'message' => $created ? 'Partido creado exitosamente.' : 'Error al crear el partido.'
                ]);
                break;
            }

            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción inválida o no proporcionada (usa "join", "create" o "cancel")']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}
