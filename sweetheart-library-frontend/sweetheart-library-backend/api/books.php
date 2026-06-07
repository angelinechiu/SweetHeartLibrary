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
        $stmt = $pdo->query("SELECT * FROM books ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($method === 'POST') {
        if (!empty($input['id'])) {
            // UPDATE
            $stmt = $pdo->prepare("
                UPDATE books SET 
                    title = ?, 
                    author = ?, 
                    isbn = ?, 
                    category = ?, 
                    year = ?, 
                    available_copies = ?, 
                    total_copies = ?, 
                    is_featured = ?, 
                    is_popular = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $input['title'] ?? '',
                $input['author'] ?? '',
                $input['isbn'] ?? '',
                $input['category'] ?? '',
                $input['year'] ?? date('Y'),
                $input['available_copies'] ?? 0,
                $input['total_copies'] ?? 0,
                $input['is_featured'] ?? 0,
                $input['is_popular'] ?? 0,
                $input['id']
            ]);
            echo json_encode(['success' => true, 'message' => 'Book updated successfully']);
        } else {
            // CREATE
            $stmt = $pdo->prepare("
                INSERT INTO books 
                (title, author, isbn, category, year, available_copies, total_copies, is_featured, is_popular) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['title'] ?? '',
                $input['author'] ?? '',
                $input['isbn'] ?? '',
                $input['category'] ?? '',
                $input['year'] ?? date('Y'),
                $input['available_copies'] ?? 3,
                $input['total_copies'] ?? 3,
                $input['is_featured'] ?? 0,
                $input['is_popular'] ?? 0
            ]);
            echo json_encode(['success' => true, 'message' => 'Book created successfully', 'id' => $pdo->lastInsertId()]);
        }
        exit;
    }

    if ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Book deleted']);
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