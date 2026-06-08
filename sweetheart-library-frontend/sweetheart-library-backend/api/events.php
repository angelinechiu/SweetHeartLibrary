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
        $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($method === 'POST') {
        if (!empty($input['id'])) {
            
            $stmt = $pdo->prepare("
                UPDATE events SET 
                    title = ?, 
                    event_date = ?, 
                    event_time = ?, 
                    description = ? 
                WHERE id = ?
            ");
            $stmt->execute([
                $input['title'] ?? '',
                $input['event_date'] ?? null,
                $input['event_time'] ?? null,
                $input['description'] ?? '',
                $input['id']
            ]);
            echo json_encode(['success' => true, 'message' => 'Event updated successfully']);
        } else {
            
            $stmt = $pdo->prepare("
                INSERT INTO events (title, event_date, event_time, description) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['title'] ?? '',
                $input['event_date'] ?? null,
                $input['event_time'] ?? null,
                $input['description'] ?? ''
            ]);
            echo json_encode(['success' => true, 'message' => 'Event created successfully', 'id' => $pdo->lastInsertId()]);
        }
        exit;
    }

    if ($method === 'DELETE') {
        parse_str($_SERVER['QUERY_STRING'], $query);
        $id = $query['id'] ?? null;

        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
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