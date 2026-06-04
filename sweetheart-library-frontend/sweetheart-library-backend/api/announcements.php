<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

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

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("INSERT INTO announcements (title, message, type, due_date, is_published, created_by) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['title'],
        $data['message'],
        $data['type'] ?? 'Notice',
        $data['due_date'] ?? null,
        $data['is_published'] ?? 1,
        $data['created_by'] ?? null
    ]);
    
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
?>