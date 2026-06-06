<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

include_once '../config/db.php';

$db = $pdo;
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $db->prepare("SELECT * FROM feedback ORDER BY created_at DESC");
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Debug: Log what we received
    error_log("Feedback received: " . json_encode($data));

    if (
        empty($data['name']) ||
        empty($data['email']) ||
        empty($data['type']) ||
        empty($data['message'])
    ) {
        echo json_encode([
            "success" => false,
            "message" => "Please fill in all required fields."
        ]);
        exit;
    }

    try {
        $query = "INSERT INTO feedback 
                  (name, email, type, message, rating, user_id) 
                  VALUES 
                  (:name, :email, :type, :message, :rating, :user_id)";

        $stmt = $db->prepare($query);

        $success = $stmt->execute([
            ':name'    => $data['name'],
            ':email'   => $data['email'],
            ':type'    => $data['type'],
            ':message' => $data['message'],
            ':rating'  => floatval($data['rating'] ?? 5),
            ':user_id' => $data['user_id'] ?? null
        ]);

        if ($success) {
            echo json_encode([
                "success" => true,
                "message" => "Feedback submitted successfully!"
            ]);
        } else {
            // Get the actual database error
            $errorInfo = $stmt->errorInfo();
            echo json_encode([
                "success" => false,
                "message" => "Database error: " . ($errorInfo[2] ?? 'Unknown error')
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Exception: " . $e->getMessage()
        ]);
    }
}
?>