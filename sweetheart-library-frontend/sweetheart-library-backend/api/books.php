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

// Borrow book - Reduce available_copies by 1
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['action']) && $data['action'] === 'borrow_book') {
        $book_id = intval($data['book_id']);

        $sql = "UPDATE books 
                SET available_copies = available_copies - 1 
                WHERE id = $book_id AND available_copies > 0";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => true, "message" => "Book borrowed successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to borrow book"]);
        }
        exit;
    }
}  
?>