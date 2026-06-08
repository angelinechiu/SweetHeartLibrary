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
        $action = $_GET['action'] ?? 'get_all';

        if ($action === 'get_all') {
            
            $stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } 
        elseif ($action === 'get_visible') {
            
            $userId = $_GET['user_id'] ?? null;

            if ($userId) {
                $stmt = $pdo->prepare("
                    SELECT * FROM announcements 
                    WHERE user_id IS NULL 
                       OR user_id = ? 
                    ORDER BY created_at DESC
                ");
                $stmt->execute([$userId]);
            } else {
                
                $stmt = $pdo->query("
                    SELECT * FROM announcements 
                    WHERE user_id IS NULL 
                    ORDER BY created_at DESC
                ");
            }
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        exit;
    }

    
    if ($method === 'POST') {

        
        if (!empty($input['id']) || (isset($input['action']) && $input['action'] === 'update')) {
            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing id for update']);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE announcements SET 
                    title = ?, 
                    message = ?, 
                    type = ?, 
                    due_date = ?, 
                    is_published = ?,
                    user_id = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $input['title'] ?? '',
                $input['message'] ?? '',
                $input['type'] ?? 'Notice',
                $input['due_date'] ?? null,
                $input['is_published'] ?? 1,
                $input['user_id'] ?? null,           
                $input['id']
            ]);
            echo json_encode(['success' => true, 'message' => 'Announcement updated successfully']);
        } 
        else {
            
            $stmt = $pdo->prepare("
                INSERT INTO announcements (title, message, type, due_date, is_published, user_id) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['title'] ?? '',
                $input['message'] ?? '',
                $input['type'] ?? 'Notice',
                $input['due_date'] ?? null,
                $input['is_published'] ?? 1,
                $input['user_id'] ?? null           
            ]);
            echo json_encode([
                'success' => true, 
                'message' => 'Announcement created successfully', 
                'id' => $pdo->lastInsertId()
            ]);
        }
        exit;
    }

    
    if ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Announcement deleted successfully']);
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