<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

include_once '../config/db.php';

$db = $pdo;

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // ==================== GET ALL FEEDBACK (For Admin) ====================
    $query = "SELECT id, name, email, type, message, rating, created_at 
              FROM feedback 
              ORDER BY created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($feedbacks);

} elseif ($method === 'POST') {
    // ==================== SUBMIT NEW FEEDBACK (For User) ====================
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->name) &&
        !empty($data->email) &&
        !empty($data->type) &&
        !empty($data->message)
    ) {
        $query = "INSERT INTO feedback 
                  (name, email, type, message, rating) 
                  VALUES 
                  (:name, :email, :type, :message, :rating)";

        $stmt = $db->prepare($query);

        $stmt->bindParam(":name", $data->name);
        $stmt->bindParam(":email", $data->email);
        $stmt->bindParam(":type", $data->type);
        $stmt->bindParam(":message", $data->message);
        $stmt->bindParam(":rating", $data->rating);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Thank you! Your feedback has been submitted successfully."
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Unable to submit feedback."
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Please fill in all required fields."
        ]);
    }
}
?>