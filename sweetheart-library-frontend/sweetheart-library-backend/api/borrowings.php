<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? null;
$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    if ($action === 'my_borrowed') {
        $user_id = $_GET['user_id'] ?? 1;
        $stmt = $pdo->prepare("SELECT * FROM borrowed_books WHERE user_id = ? AND status = 'Borrowed' ORDER BY due_date ASC");
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($action === 'my_overdue') {
        $user_id = $_GET['user_id'] ?? 1;
        $stmt = $pdo->prepare("SELECT * FROM borrowed_books WHERE user_id = ? AND status = 'Overdue' ORDER BY due_date ASC");
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
}

if ($method === 'POST') {
    if ($action === 'renew') {
        $borrowing_id = $data['borrowing_id'] ?? null;
        if (!$borrowing_id) {
            echo json_encode(['success' => false, 'message' => 'Borrowing ID required']);
            exit;
        }

        // Extend due date by 7 days and change status back to Borrowed
        $stmt = $pdo->prepare("
            UPDATE borrowed_books 
            SET due_date = DATE_ADD(due_date, INTERVAL 7 DAY), 
                status = 'Borrowed',
                has_penalty = 0
            WHERE id = ?
        ");
        $success = $stmt->execute([$borrowing_id]);

        echo json_encode(['success' => $success]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>