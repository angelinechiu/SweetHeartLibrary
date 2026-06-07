<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); exit;
}

require_once '../config/db.php';

// TODO: Add admin check later (using token)

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM rooms ORDER BY id DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    if ($method === 'POST') {
        $name = trim($data['name'] ?? '');
        $capacity = (int)($data['capacity'] ?? 0);
        $equipment = trim($data['equipment'] ?? '');

        if (empty($name) || $capacity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Name and capacity are required']);
            exit;
        }

        if (!empty($data['id'])) {
            // Update
            $stmt = $pdo->prepare("UPDATE rooms SET name=?, capacity=?, equipment=? WHERE id=?");
            $stmt->execute([$name, $capacity, $equipment, $data['id']]);
            echo json_encode(['success' => true, 'message' => 'Room updated']);
        } else {
            // Create
            $stmt = $pdo->prepare("INSERT INTO rooms (name, capacity, equipment) VALUES (?, ?, ?)");
            $stmt->execute([$name, $capacity, $equipment]);
            echo json_encode(['success' => true, 'message' => 'Room added', 'id' => $pdo->lastInsertId()]);
        }
        exit;
    }

    if ($method === 'DELETE') {
        parse_str($_SERVER['QUERY_STRING'], $query);
        $id = $query['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Room deleted']);
        }
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>