<?php

// Manejo de preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    http_response_code(200);
    exit();
}

// Cabeceras CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Incluir el modelo
include_once '../model/Team.php';
include_once '../connection/database.php';

$db = new Database();
$conn = $db->getConnection();
$model = new Team($conn);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener todos los equipos
    $allTeams = $model->getAllTeams();
    echo json_encode($allTeams);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"));

    if (isset($input->user_id) && isset($input->only_captain) && $input->only_captain === true) {
        $teams = $model->getTeamsWhereUserIsCaptain($input->user_id);
        echo json_encode($teams);
        return;
    }

    // Obtener equipos por usuario
    if (isset($input->user_id)) {
        $teams = $model->getTeamsByUserId($input->user_id);
        echo json_encode($teams);
        return;
    }

    if (isset($input->action) && $input->action === 'getTeamDetails' && isset($input->team_id)) {
        $teamDetails = $model->getTeamDetailsById($input->team_id);

        if ($teamDetails) {
            echo json_encode($teamDetails);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Equipo no encontrado"]);
        }
        return;
    }

    // Crear nuevo equipo con acción 'create'
    if (isset($input->action) && $input->action === 'create' && isset($input->data)) {
        $data = $input->data;

        if (isset($data->TEAM_NAME, $data->TEAM_ADDRESS, $data->TEAM_TYPE, $data->CAPTAIN)) {
            $creado = $model->createTeam($data);
            if ($creado) {
                http_response_code(201);
                echo json_encode(["message" => "Equipo creado correctamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al crear el equipo"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos obligatorios para crear el equipo"]);
        }
        return;
    }

    // Si no cumple ninguna condición
    http_response_code(400);
    echo json_encode(["error" => "Parámetros incorrectos o incompletos"]);

} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}
