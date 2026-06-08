<?php
require_once '../config/db.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? null;
$data = json_decode(file_get_contents('php://input'), true);

header('Content-Type: application/json');


if ($method === 'POST' && $action === 'update_profile') {
    $user_id = $data['user_id'] ?? null;
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';

    if (!$user_id || !$name || !$email) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $success = $stmt->execute([$name, $email, $user_id]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}


if ($method === 'GET') {
    $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit();
}


if ($method === 'DELETE') {
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>