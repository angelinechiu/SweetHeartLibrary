<?php
// CORS Headers - Must be at the very top
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 3600");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// ==================== GET ====================
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'get_published';

    if ($action === 'get_published') {
        $stmt = $pdo->prepare("
            SELECT id, title, message, type, due_date, 
                   DATE_FORMAT(created_at, '%b %e, %Y') as published_at
            FROM announcements 
            WHERE is_published = 1 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } 
    elseif ($action === 'get_all') {
        $stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    exit;
}

// ==================== POST (Create or Update) ====================
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $_GET['action'] ?? 'create';

    if ($action === 'update' || isset($data['id'])) {
        // UPDATE existing announcement
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing id for update']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE announcements 
                               SET title = ?, message = ?, type = ?, due_date = ?, is_published = ? 
                               WHERE id = ?");
        $success = $stmt->execute([
            $data['title'],
            $data['message'],
            $data['type'] ?? 'Notice',
            $data['due_date'] ?? null,
            $data['is_published'] ?? 1,
            $data['id']
        ]);

        echo json_encode(['success' => $success]);
    } else {
        // CREATE new announcement
        $stmt = $pdo->prepare("INSERT INTO announcements (title, message, type, due_date, is_published, created_by) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([
            $data['title'],
            $data['message'],
            $data['type'] ?? 'Notice',
            $data['due_date'] ?? null,
            $data['is_published'] ?? 1,
            $data['created_by'] ?? null
        ]);

        echo json_encode(['success' => $success, 'id' => $pdo->lastInsertId()]);
    }
    exit;
}

// ==================== DELETE ====================
if ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing id']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
    $success = $stmt->execute([$id]);

    echo json_encode(['success' => $success]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
?>