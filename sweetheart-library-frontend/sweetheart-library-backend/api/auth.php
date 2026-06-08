<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];


if ($action === 'login' && $method === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($data['password'], $user['password'])) {
        echo json_encode([
            'success' => true,
            'token' => bin2hex(random_bytes(32)),
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
}


if ($action === 'register' && $method === 'POST') {
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) 
                           VALUES (?, ?, ?, 'user')");
    
    $success = $stmt->execute([
        $data['name'],
        $data['email'],
        $hashedPassword
    ]);

    if ($success) {
        echo json_encode([
            'success' => true, 
            'message' => 'Registration successful'
        ]);
    } else {
        
        $errorInfo = $stmt->errorInfo();
        $message = 'Registration failed';

        
        if (isset($errorInfo[1]) && $errorInfo[1] == 1062) {
            $message = 'This email is already registered. Please use another email.';
        }

        echo json_encode([
            'success' => false, 
            'message' => $message
        ]);
    }
}


if ($action === 'forgot_password' && $method === 'POST') {
    try {
        $email = $data['email'] ?? '';

        if (empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Email is required']);
            exit;
        }

        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        $resetLink = null;
        $message = 'Email not registered. Unable to generate a password reset link.';

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);

            
            $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)")
                ->execute([$email, $token, $expires]);

            
            $resetLink = "http://localhost:5173/reset-password?token=" . $token;

            $message = 'Reset link generated successfully.';
        }

        echo json_encode([
            'success' => true,
            'message' => $message,
            'reset_link' => $resetLink
        ]);

    } catch (Exception $e) {
        
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while processing your request.',
            'debug' => $e->getMessage()   
        ]);
    }
}


if ($action === 'reset_password' && $method === 'POST') {
    $token = $data['token'] ?? '';
    $newPassword = $data['password'] ?? '';

    if (empty($token) || empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    
    $stmt = $pdo->prepare("
        SELECT email FROM password_resets 
        WHERE token = ? 
        AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();

    if ($reset) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $reset['email']]);

        
        $pdo->prepare("DELETE FROM password_resets WHERE token = ?")->execute([$token]);

        echo json_encode(['success' => true, 'message' => 'Password reset successful']);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid or expired token. Please generate a new reset link.'
        ]);
    }
}