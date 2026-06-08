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
        $user_id = $_GET['user_id'] ?? 0;
        if ($user_id <= 0) {
            echo json_encode([]);
            exit;
        }

        
        $stmt = $pdo->prepare("
            SELECT 
                bb.*,
                COALESCE(bb.book_title, b.title) AS book_title,
                COALESCE(bb.author, b.author) AS author
            FROM borrowed_books bb
            LEFT JOIN books b ON bb.book_id = b.id
            WHERE bb.user_id = ? 
              AND bb.status = 'Borrowed' 
            ORDER BY bb.due_date ASC 
            LIMIT 3
        ");
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($action === 'my_overdue') {
        $user_id = $_GET['user_id'] ?? 0;
        if ($user_id <= 0) {
            echo json_encode([]);
            exit;
        }

        
        $stmt = $pdo->prepare("
            SELECT 
                bb.*,
                COALESCE(bb.book_title, b.title) AS book_title,
                COALESCE(bb.author, b.author) AS author
            FROM borrowed_books bb
            LEFT JOIN books b ON bb.book_id = b.id
            WHERE bb.user_id = ? 
              AND bb.due_date < CURDATE() 
              AND bb.status IN ('Borrowed', 'Overdue') 
            ORDER BY bb.due_date ASC 
            LIMIT 3
        ");
        $stmt->execute([$user_id]);
        
        $overdueBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        
        if (!empty($overdueBooks)) {
            $ids = array_column($overdueBooks, 'id');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $updateStmt = $pdo->prepare("UPDATE borrowed_books SET status = 'Overdue' WHERE id IN ($placeholders)");
            $updateStmt->execute($ids);
        }
        
        echo json_encode($overdueBooks);
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