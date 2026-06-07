<?php
// ==================== CORS HEADERS ====================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// ==================== LOGIN ====================
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

// ==================== REGISTER ====================
if ($action === 'register' && $method === 'POST') {
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    // student_id removed
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
        // Better error handling
        $errorInfo = $stmt->errorInfo();
        $message = 'Registration failed';

        // Detect duplicate email error (MySQL error code 1062)
        if (isset($errorInfo[1]) && $errorInfo[1] == 1062) {
            $message = 'This email is already registered. Please use another email.';
        }

        echo json_encode([
            'success' => false, 
            'message' => $message
        ]);
    }
}

// ==================== FORGOT PASSWORD (Improved) ====================
if ($action === 'forgot_password' && $method === 'POST') {
    try {
        $email = $data['email'] ?? '';

        if (empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Email is required']);
            exit;
        }

        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        $resetLink = null;
        $message = 'Email not registered. Unable to generate a password reset link.';

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Delete old tokens for this email
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);

            // Insert new reset token
            $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)")
                ->execute([$email, $token, $expires]);

            // For development/testing only
            $resetLink = "http://localhost:5173/reset-password?token=" . $token;

            $message = 'Reset link generated successfully.';
        }

        echo json_encode([
            'success' => true,
            'message' => $message,
            'reset_link' => $resetLink
        ]);

    } catch (Exception $e) {
        // Catch any database or other errors
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while processing your request.',
            'debug' => $e->getMessage()   // Remove this line in production
        ]);
    }
}

// ==================== RESET PASSWORD (Improved) ====================
if ($action === 'reset_password' && $method === 'POST') {
    $token = $data['token'] ?? '';
    $newPassword = $data['password'] ?? '';

    if (empty($token) || empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    // More lenient check: token exists and not older than 24 hours
    $stmt = $pdo->prepare("
        SELECT email FROM password_resets 
        WHERE token = ? 
        AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
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
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid or expired token. Please generate a new reset link.'
        ]);
    }
}