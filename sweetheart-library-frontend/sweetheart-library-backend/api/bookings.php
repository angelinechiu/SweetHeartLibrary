<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? null;
$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    $data = $_POST;
}

function jsonResponse($data) {
    echo json_encode($data);
    exit;
}

if ($method === 'GET') {
    if ($action === 'get') {
        $response = ['success' => true];
        $user_id = $_GET['user_id'] ?? null;

        if ($user_id) {
            $stmtBooks = $pdo->prepare("SELECT * FROM borrowed_books WHERE user_id = ? ORDER BY borrow_date DESC");
            $stmtBooks->execute([$user_id]);
            $response['borrowed_books'] = $stmtBooks->fetchAll(PDO::FETCH_ASSOC);

            $stmtRooms = $pdo->prepare("SELECT * FROM room_bookings WHERE user_id = ? ORDER BY start_time ASC");
            $stmtRooms->execute([$user_id]);
            $response['room_bookings'] = $stmtRooms->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmtBooks = $pdo->prepare("SELECT * FROM borrowed_books ORDER BY due_date ASC");
            $stmtBooks->execute();
            $response['borrowed_books'] = $stmtBooks->fetchAll(PDO::FETCH_ASSOC);

            $stmtRooms = $pdo->prepare("SELECT * FROM room_bookings WHERE status = 'Active' ORDER BY start_time ASC");
            $stmtRooms->execute();
            $response['room_bookings'] = $stmtRooms->fetchAll(PDO::FETCH_ASSOC);
        }

        $response['combined_bookings'] = [];
        foreach ($response['borrowed_books'] as $book) {
            $response['combined_bookings'][] = [
                'id' => $book['id'],
                'type' => 'book',
                'title' => $book['book_title'],
                'author' => $book['author'],
                'date' => $book['borrow_date'],
                'time' => 'Due ' . ($book['due_date'] ?? ''),
                'status' => $book['status'],
                'details' => $book,
            ];
        }

        foreach ($response['room_bookings'] as $room) {
            $startDate = date('Y-m-d', strtotime($room['start_time']));
            $response['combined_bookings'][] = [
                'id' => $room['id'],
                'type' => 'room',
                'room_name' => $room['room_name'],
                'date' => $startDate,
                'time' => date('H:i', strtotime($room['start_time'])) . ' - ' . date('H:i', strtotime($room['end_time'])),
                'status' => $room['status'],
                'details' => $room,
            ];
        }

        jsonResponse($response);
    }

    $user_id = $_GET['user_id'] ?? null;
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC");
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC");
    }
    jsonResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($method === 'POST') {
    if (!empty($action)) {
        $bookingId = $data['booking_id'] ?? null;
        if (empty($bookingId)) {
            jsonResponse(['success' => false, 'message' => 'Booking ID is required']);
        }

        if ($action === 'send_reminder') {
            $stmt = $pdo->prepare("UPDATE borrowed_books SET has_penalty = 1, status = 'Overdue' WHERE id = :id");
            $stmt->bindParam(':id', $bookingId);
            $result = $stmt->execute();
            jsonResponse(['success' => $result, 'message' => $result ? 'Reminder sent successfully' : 'Failed to send reminder']);
        }

        if ($action === 'mark_room_available') {
            $stmt = $pdo->prepare("UPDATE room_bookings SET status = 'Completed' WHERE id = :id");
            $stmt->bindParam(':id', $bookingId);
            $result = $stmt->execute();
            jsonResponse(['success' => $result, 'message' => $result ? 'Room marked as available' : 'Failed to update room status']);
        }

        if ($action === 'mark_returned') {
            $stmt = $pdo->prepare("UPDATE borrowed_books SET status = 'Returned' WHERE id = :id");
            $stmt->bindParam(':id', $bookingId);
            $result = $stmt->execute();
            jsonResponse(['success' => $result, 'message' => $result ? 'Book marked as returned' : 'Failed to update booking status']);
        }

        jsonResponse(['success' => false, 'message' => 'Unknown action']);
    }

    if (!empty($data['id']) && !empty($data['status'])) {
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$data['status'], $data['id']]);
        jsonResponse(['success' => true]);
    }

    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, booking_date, start_time, end_time, purpose, status) 
                               VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([
        $data['user_id'], 
        $data['booking_date'], 
        $data['start_time'], 
        $data['end_time'], 
        $data['purpose']
    ]);
    jsonResponse(['success' => true]);
}

if ($method === 'DELETE') {
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse(['success' => true]);
    }
}

jsonResponse(['success' => false, 'message' => 'Invalid request method']);
?>