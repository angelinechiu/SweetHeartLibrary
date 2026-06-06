<?php
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM books ORDER BY id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($method === 'POST') {
    if (!empty($data['id'])) {
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
            $data['title'],
            $data['author'],
            $data['isbn'],
            $data['category'],
            $data['publication_year'],
            $data['copies'],
            $data['description'],
            $data['availability_status'],
            $data['id']
        ]);
    } else {
        // CREATE
        $stmt = $pdo->prepare("
            INSERT INTO books 
            (title, author, isbn, category, publication_year, copies, description, availability_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['title'],
            $data['author'],
            $data['isbn'],
            $data['category'],
            $data['publication_year'],
            $data['copies'],
            $data['description'],
            $data['availability_status'] ?? 'Available'
        ]);
    }
    echo json_encode(['success' => true]);
}

if ($method === 'DELETE') {
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
}

// ==================== BORROW BOOK (FIXED) ====================
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['action']) && $data['action'] === 'borrow_book') {
        $book_id = intval($data['book_id'] ?? 0);
        $user_id = intval($data['user_id'] ?? 0);

        if ($book_id <= 0) {
            echo json_encode(["success" => false, "message" => "Invalid book ID"]);
            exit;
        }

        try {
            // Check current available copies
            $checkStmt = $pdo->prepare("SELECT available_copies FROM books WHERE id = ?");
            $checkStmt->execute([$book_id]);
            $book = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$book) {
                echo json_encode(["success" => false, "message" => "Book not found"]);
                exit;
            }

            if ($book['available_copies'] <= 0) {
                echo json_encode([
                    "success" => false, 
                    "message" => "This book is currently out of stock."
                ]);
                exit;
            }

            // Deduct 1 copy
            $updateStmt = $pdo->prepare("
                UPDATE books 
                SET available_copies = available_copies - 1 
                WHERE id = ? AND available_copies > 0
            ");
            $updateStmt->execute([$book_id]);

            // Optional: Record in borrowed_books table
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
}
?>