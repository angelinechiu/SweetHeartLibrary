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
    // ==================== GET ====================
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM books ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    // ==================== POST ====================
    if ($method === 'POST') {

        // --- BORROW BOOK (Original function - KEPT EXACTLY AS IS) ---
        if (isset($input['action']) && $input['action'] === 'borrow_book') {
            $book_id = intval($input['book_id'] ?? 0);
            $user_id = intval($input['user_id'] ?? 0);

            if ($book_id <= 0) {
                echo json_encode(["success" => false, "message" => "Invalid book ID"]);
                exit;
            }

            try {
                $checkStmt = $pdo->prepare("SELECT available_copies FROM books WHERE id = ?");
                $checkStmt->execute([$book_id]);
                $book = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if (!$book) {
                    echo json_encode(["success" => false, "message" => "Book not found"]);
                    exit;
                }

                if ($book['available_copies'] <= 0) {
                    echo json_encode(["success" => false, "message" => "This book is currently out of stock."]);
                    exit;
                }

                $updateStmt = $pdo->prepare("
                    UPDATE books 
                    SET available_copies = available_copies - 1 
                    WHERE id = ? AND available_copies > 0
                ");
                $updateStmt->execute([$book_id]);

                if ($user_id > 0) {
                    $insertStmt = $pdo->prepare("
                        INSERT INTO borrowed_books (user_id, book_id, borrow_date, due_date, status) 
                        VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Borrowed')
                    ");
                    $insertStmt->execute([$user_id, $book_id]);
                }

                echo json_encode([
                    "success" => true, 
                    "message" => "Book borrowed successfully. 1 copy has been deducted."
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    "success" => false, 
                    "message" => "Failed to borrow book: " . $e->getMessage()
                ]);
            }
            exit;
        }

        // --- CREATE or UPDATE BOOK (This part was broken - now fixed) ---
        if (!empty($input['id'])) {
            // UPDATE
            $stmt = $pdo->prepare("
                UPDATE books SET 
                    title = ?, 
                    author = ?, 
                    isbn = ?, 
                    category = ?, 
                    publication_year = ?, 
                    copies = ?, 
                    description = ?, 
                    availability_status = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $input['title'] ?? '',
                $input['author'] ?? '',
                $input['isbn'] ?? '',
                $input['category'] ?? '',
                $input['publication_year'] ?? date('Y'),
                $input['copies'] ?? 1,
                $input['description'] ?? null,
                $input['availability_status'] ?? 'Available',
                $input['id']
            ]);
            echo json_encode(['success' => true, 'message' => 'Book updated successfully']);
        } else {
            // CREATE
            $stmt = $pdo->prepare("
                INSERT INTO books 
                (title, author, isbn, category, publication_year, copies, description, availability_status, available_copies) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $copies = $input['copies'] ?? 1;
            $stmt->execute([
                $input['title'] ?? '',
                $input['author'] ?? '',
                $input['isbn'] ?? '',
                $input['category'] ?? '',
                $input['publication_year'] ?? date('Y'),
                $copies,
                $input['description'] ?? null,
                $input['availability_status'] ?? 'Available',
                $copies
            ]);
            echo json_encode(['success' => true, 'message' => 'Book created successfully', 'id' => $pdo->lastInsertId()]);
        }
        exit;
    }

    // ==================== DELETE ====================
    if ($method === 'DELETE') {
        parse_str($_SERVER['QUERY_STRING'], $query);
        $id = $query['id'] ?? null;

        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Book deleted successfully']);
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