<?php
/**
 * Improved rooms.php (Safe version)
 * - Kept all original functions
 * - Added better validation and error handling
 * - Did NOT remove or change any existing logic
 */

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
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    if ($method === 'POST') {
        $name = trim($input['name'] ?? '');
        $capacity = (int)($input['capacity'] ?? 0);
        $equipment = trim($input['equipment'] ?? '');

        if (empty($name) || $capacity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Name and capacity are required']);
            exit;
        }

        if (!empty($input['id'])) {
            // UPDATE - Original logic kept
            $stmt = $pdo->prepare("UPDATE rooms SET name=?, capacity=?, equipment=? WHERE id=?");
            $stmt->execute([$name, $capacity, $equipment, $input['id']]);
            echo json_encode(['success' => true, 'message' => 'Room updated successfully']);
        } else {
            // CREATE - Original logic kept
            $stmt = $pdo->prepare("INSERT INTO rooms (name, capacity, equipment) VALUES (?, ?, ?)");
            $stmt->execute([$name, $capacity, $equipment]);
            echo json_encode(['success' => true, 'message' => 'Room added successfully', 'id' => $pdo->lastInsertId()]);
        }
        exit;
    }

    if ($method === 'DELETE') {
        parse_str($_SERVER['QUERY_STRING'], $query);
        $id = $query['id'] ?? null;

        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Room deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing id']);
        }
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()]);
}
?>