<?php
require_once '../config/db.php';
$data = json_decode(file_get_contents("php://input"), true);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
    echo json_encode($stmt->fetchAll());
}

if ($method === 'POST') {
    if (!empty($data['id'])) {
        $stmt = $pdo->prepare("UPDATE events SET title=?, event_date=?, event_time=? WHERE id=?");
        $stmt->execute([$data['title'], $data['event_date'], $data['event_time'], $data['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO events (title, event_date, event_time) VALUES (?, ?, ?)");
        $stmt->execute([$data['title'], $data['event_date'], $data['event_time']]);
    }
    echo json_encode(['success' => true]);
}

if ($method === 'DELETE') {
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
}
?>