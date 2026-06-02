<?php
require_once '../config/db.php';
$data = json_decode(file_get_contents("php://input"), true);

$action = $_GET['action'] ?? '';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($data['password'], $user['password'])) {
        echo json_encode([
            'success' => true,
            'token' => bin2hex(random_bytes(32)),
            'user' => $user
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
}

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, student_id, role) VALUES (?, ?, ?, ?, 'user')");
    $success = $stmt->execute([
        $data['name'],
        $data['email'],
        $hashedPassword,
        $data['student_id']
    ]);

    echo json_encode(['success' => $success]);
}
?>