<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? null;
$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    if ($action === 'my_recent') {
        // Get real user_id from frontend
        $user_id = $_GET['user_id'] ?? 0;

        if ($user_id <= 0) {
            echo json_encode([]);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT id, room_name, start_time, end_time, status, location
            FROM room_bookings 
            WHERE user_id = ? 
            ORDER BY start_time DESC 
            LIMIT 3
        ");
        $stmt->execute([$user_id]);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format for frontend
        foreach ($bookings as &$b) {
            $b['date'] = date('M j, Y', strtotime($b['start_time']));
            $b['time'] = date('H:i', strtotime($b['start_time'])) . ' - ' . date('H:i', strtotime($b['end_time']));
        }

        echo json_encode($bookings);
        exit;
    }
}

if ($method === 'POST') {
    if ($action === 'cancel') {
        $booking_id = $data['booking_id'] ?? null;
        if (!$booking_id) {
            echo json_encode(['success' => false, 'message' => 'Booking ID required']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE room_bookings SET status = 'cancelled' WHERE id = ?");
        $success = $stmt->execute([$booking_id]);

        echo json_encode(['success' => $success]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>