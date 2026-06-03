<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
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

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, student_id, role) 
                           VALUES (?, ?, ?, ?, 'user')");
    
    $success = $stmt->execute([
        $data['name'],
        $data['email'],
        $hashedPassword,
        $data['student_id']
    ]);

    echo json_encode(['success' => $success]);
}

// ==================== FORGOT PASSWORD ====================
if ($action === 'forgot_password' && $method === 'POST') {
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
        $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)")
            ->execute([$email, $token, $expires]);

        require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
        require_once __DIR__ . '/../PHPMailer/src/Exception.php';
        try {
            $phpmailer->isSMTP();
            $phpmailer->Host       = 'sandbox.smtp.mailtrap.io';
            $phpmailer->SMTPAuth   = true;
            $phpmailer->Port       = 2525;
            $phpmailer->Username   = '80eca2a39dc17b';
            $phpmailer->Password   = 'a7652890e9491e';
            $phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

            $phpmailer->setFrom('no-reply@sweetheartlibrary.com', 'Sweetheart Library');
            $phpmailer->addAddress($email);

            $resetLink = "http://localhost:5173/reset-password?token=" . $token;

            $phpmailer->isHTML(true);
            $phpmailer->Subject = 'Reset Your Sweetheart Library Password';
            $phpmailer->Body    = "
                <h2>Password Reset Request</h2>
                <p>Hello,</p>
                <p>We received a request to reset your password.</p>
                <p>Click the button below to reset your password:</p>
                <p><a href='$resetLink' style='background:#E8B4B8;color:#2C2C2C;padding:12px 25px;text-decoration:none;border-radius:50px;'>Reset Password</a></p>
                <p><strong>This link will expire in 1 hour.</strong></p>
            ";

            $phpmailer->send();

        } catch (Exception $e) {
            error_log("Mail Error: " . $phpmailer->ErrorInfo);
        }
    }

    echo json_encode(['success' => true]);
}

// ==================== RESET PASSWORD ====================
if ($action === 'reset_password' && $method === 'POST') {
    $token = $data['token'] ?? '';
    $newPassword = $data['password'] ?? '';

    if (empty($token) || empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();

    if ($reset) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $reset['email']]);

        $pdo->prepare("DELETE FROM password_resets WHERE token = ?")->execute([$token]);

        echo json_encode(['success' => true, 'message' => 'Password reset successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
    }
}
?>