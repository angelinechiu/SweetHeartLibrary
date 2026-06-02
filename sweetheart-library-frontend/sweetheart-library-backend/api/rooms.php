<?php
require_once '../config/db.php';
$data = json_decode(file_get_contents("php://input"), true);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM rooms ORDER BY id DESC");
    echo json_encode($stmt->fetchAll());
}

if ($method === 'POST') {
    if (!empty($data['id'])) {
        $stmt = $pdo->prepare("UPDATE rooms SET name=?, capacity=?, equipment=? WHERE id=?");
        $stmt->execute([$data['name'], $data['capacity'], $data['equipment'], $data['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO rooms (name, capacity, equipment) VALUES (?, ?, ?)");
        $stmt->execute([$data['name'], $data['capacity'], $data['equipment']]);
    }
    echo json_encode(['success' => true]);
}

if ($method === 'DELETE') {
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
}
?>