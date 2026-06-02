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
        $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, description=? WHERE id=?");
        $stmt->execute([$data['title'], $data['author'], $data['description'], $data['id']]);
    } else {
        // CREATE
        $stmt = $pdo->prepare("INSERT INTO books (title, author, description) VALUES (?, ?, ?)");
        $stmt->execute([$data['title'], $data['author'], $data['description']]);
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
?>