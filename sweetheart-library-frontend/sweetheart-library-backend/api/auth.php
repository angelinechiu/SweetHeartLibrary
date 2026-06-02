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

// ==================== FORGOT PASSWORD ====================
if ($action === 'forgot_password') {
    $email = $data['email'] ?? '';

    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);

        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires]);

        // Generate reset link (for localhost)
        $resetLink = "http://localhost:5173/reset-password?token=" . $token;

        echo json_encode([
            'success' => true,
            'message' => 'Reset link generated',
            'reset_link' => $resetLink   // ← This is shown to user for testing
        ]);
    } else {
        echo json_encode(['success' => true, 'message' => 'If email exists, reset link sent.']);
    }
}

// ==================== RESET PASSWORD ====================
if ($action === 'reset_password') {
    $token = $data['token'] ?? '';
    $newPassword = $data['password'] ?? '';

    if (empty($token) || empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    // Find valid token
    $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();

    if ($reset) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update user password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $reset['email']]);

        // Delete used token
        $pdo->prepare("DELETE FROM password_resets WHERE token = ?")->execute([$token]);

        echo json_encode(['success' => true, 'message' => 'Password reset successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
    }
}
?>