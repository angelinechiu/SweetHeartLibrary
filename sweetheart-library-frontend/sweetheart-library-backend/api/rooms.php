<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true) ?? [];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM rooms ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($method === 'POST') {
        if (!empty($input['id'])) {
            
            $stmt = $pdo->prepare("
                UPDATE rooms SET 
                    name = ?, 
                    capacity = ?, 
                    facilities = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $input['name'] ?? '',
                $input['capacity'] ?? 4,
                $input['facilities'] ?? '',
                $input['id']
            ]);
            echo json_encode(['success' => true, 'message' => 'Room updated successfully']);
        } else {
            
            $stmt = $pdo->prepare("
                INSERT INTO rooms (name, capacity, facilities) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $input['name'] ?? '',
                $input['capacity'] ?? 4,
                $input['facilities'] ?? ''
            ]);
            echo json_encode(['success' => true, 'message' => 'Room created successfully', 'id' => $pdo->lastInsertId()]);
        }
        exit;
    }

    if ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Room deleted']);
        }
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>