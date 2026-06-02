<?php
require_once '../config/db.php';
$data = json_decode(file_get_contents("php://input"), true);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $user_id = $_GET['user_id'] ?? null;
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC");
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC");
    }
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($method === 'POST') {
    if (!empty($data['id']) && !empty($data['status'])) {
        // Update status only
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$data['status'], $data['id']]);
    } else {
        // Create new booking
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, booking_date, start_time, end_time, purpose, status) 
                               VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
            $data['user_id'], 
            $data['booking_date'], 
            $data['start_time'], 
            $data['end_time'], 
            $data['purpose']
        ]);
    }
    echo json_encode(['success' => true]);
}

if ($method === 'DELETE') {
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
}
?>