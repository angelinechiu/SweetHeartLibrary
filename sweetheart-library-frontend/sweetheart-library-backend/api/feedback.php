<?php
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

header('Content-Type: application/json');

// GET - Admin can fetch all feedback
if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

// POST - Submit new feedback (from users)
if ($method === 'POST') {
    if (!empty($data['name']) && !empty($data['email']) && !empty($data['message'])) {
        
        $stmt = $pdo->prepare("
            INSERT INTO feedback (user_id, name, email, type, message, status) 
            VALUES (?, ?, ?, ?, ?, 'New')
        ");
        
        $userId = $data['user_id'] ?? null;
        
        $stmt->execute([
            $userId,
            $data['name'],
            $data['email'],
            $data['type'] ?? 'General',
            $data['message']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Feedback submitted successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    }
}

// PUT - Update status (Admin only)
if ($method === 'PUT') {
    if (!empty($data['id']) && !empty($data['status'])) {
        $stmt = $pdo->prepare("UPDATE feedback SET status = ? WHERE id = ?");
        $stmt->execute([$data['status'], $data['id']]);
        echo json_encode(['success' => true]);
    }
}

// DELETE - Delete feedback (Admin only)
if ($method === 'DELETE') {
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'] ?? null;
    
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM feedback WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
}
?>