<?php
// controllers/UserController.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include_once '../connection/database.php';
include_once '../model/User.php';

// Crear conexión a la base de datos
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Obtener acción desde query param (opcional)
$action = $_GET['action'] ?? null;

// Manejar solicitudes según el método HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        // Obtener datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents("php://input"), true);

        if ($action === 'requestJoinTeam') {
            // Validar datos
            $teamId = $data['team_id'] ?? null;
            $userId = $data['user_id'] ?? null;

            if (!$teamId || !$userId) {
                echo json_encode(['success' => false, 'message' => 'Missing team_id or player_id']);
                exit;
            }

            $response = $user->requestJoinTeam($teamId, $userId);
            echo json_encode($response);
            exit;
        }

        // Login por defecto
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
            exit;
        }

        $userData = $user->getUserByEmailAndPassword($email, $password);

        if (!$userData) {
            echo json_encode(['success' => false, 'message' => "Credenciales incorrectas. Inténtalo de nuevo."]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'user' => [
                'handle' => $userData['handle'],
                'name' => $userData['name'],
                'surname' => $userData['surname'],
                'picture' => $userData['picture'],
                'primary_position' => $userData['primary_position'],
                'secondary_position' => $userData['secondary_position'],
                'goals' => $userData['goals'],
                'assists' => $userData['assists'],
                'user_type' => $userData['user_type'],
                'email' => $email,
                'userId' => $userData['userId']
            ]
        ]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        // Validar campos obligatorios
        if (empty($data['user_id']) || empty($data['handle']) || empty($data['email']) || empty($data['user_name']) || empty($data['surname'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        // Convertir la imagen base64 a binario si viene
        $picture = null;
        if (!empty($data['picture'])) {
            $picture = base64_decode($data['picture']);
            if ($picture === false) {
                echo json_encode(['success' => false, 'message' => 'Invalid picture data']);
                exit;
            }
        }

        $data['birthday'] = $data['birthday'] ?? '';
        $data['primary_position'] = $data['primary_position'] ?? '';
        $data['secondary_position'] = $data['secondary_position'] ?? '';
        $data['goals'] = $data['goals'] ?? 0;
        $data['assists'] = $data['assists'] ?? 0;

        $update_result = $user->updateUserProfile($data['user_id'], [
            'handle' => $data['handle'],
            'email' => $data['email'],
            'user_name' => $data['user_name'],
            'surname' => $data['surname'],
            'primary_position' => $data['primary_position'],
            'secondary_position' => $data['secondary_position'],
            'birthday' => $data['birthday'],
            'goals' => $data['goals'],
            'assists' => $data['assists'],
            'picture' => $picture,
            'passwd' => $data['passwd'] ?? null
        ]);

        if ($update_result) {
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating profile']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        break;
}
