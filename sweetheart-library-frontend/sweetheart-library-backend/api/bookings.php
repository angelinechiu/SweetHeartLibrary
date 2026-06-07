<?php
/**
 * Improved bookings.php (Final Safe Version)
 * - Kept ALL original functions
 * - Included your original create_room_booking conflict check
 * - Cleaner structure + consistent responses
 * - Did NOT remove any existing functionality
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? null;

$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    $data = $_POST;
}

function respond($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// ====================== GET ======================
if ($method === 'GET') {

    if ($action === 'get') {
        $response = ['success' => true];
        $user_id = $_GET['user_id'] ?? null;

        if ($user_id) {
            $stmt = $pdo->prepare("SELECT * FROM borrowed_books WHERE user_id = ? ORDER BY borrow_date DESC");
            $stmt->execute([$user_id]);
            $response['borrowed_books'] = $stmt->fetchAll();

            $stmt = $pdo->prepare("SELECT * FROM room_bookings WHERE user_id = ? ORDER BY start_time ASC");
            $stmt->execute([$user_id]);
            $response['room_bookings'] = $stmt->fetchAll();
        } else {
            $stmt = $pdo->prepare("SELECT * FROM borrowed_books ORDER BY due_date ASC");
            $stmt->execute();
            $response['borrowed_books'] = $stmt->fetchAll();

            $stmt = $pdo->prepare("SELECT * FROM room_bookings WHERE status = 'Active' ORDER BY start_time ASC");
            $stmt->execute();
            $response['room_bookings'] = $stmt->fetchAll();
        }
        respond($response);
    }

    if ($action === 'check_daily_limit') {
        $user_id = $_GET['user_id'] ?? 0;
        $date = $_GET['date'] ?? date('Y-m-d');

        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)), 0) as total_hours
            FROM room_bookings 
            WHERE user_id = ? AND DATE(start_time) = ? AND status != 'cancelled'
        ");
        $stmt->execute([$user_id, $date]);
        $result = $stmt->fetch();
        respond(['success' => true, 'total_hours' => (float)($result['total_hours'] ?? 0)]);
    }

    // Fallback
    $user_id = $_GET['user_id'] ?? null;
    $stmt = $user_id 
        ? $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC")
        : $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC");
    
    if ($user_id) $stmt->execute([$user_id]);
    respond($stmt->fetchAll());
}

// ====================== POST ======================
if ($method === 'POST') {

    // === Create Room Booking with Conflict Check (Your original logic) ===
    if ($action === 'create_room_booking') {
        $user_id   = $data['user_id'] ?? null;
        $room_name = $data['room_name'] ?? $data['room_id'] ?? null;
        $start     = $data['start_time'] ?? null;
        $end       = $data['end_time'] ?? null;

        if (!$user_id || !$room_name || !$start || !$end) {
            respond(['success' => false, 'message' => 'Missing required fields'], 400);
        }

        try {
            // Check for time conflict
            $checkStmt = $pdo->prepare("
                SELECT id FROM room_bookings 
                WHERE room_name = ? 
                  AND status != 'cancelled'
                  AND (
                        (start_time < ? AND end_time > ?) OR
                        (start_time < ? AND end_time > ?) OR
                        (start_time >= ? AND end_time <= ?)
                  )
                LIMIT 1
            ");
            $checkStmt->execute([$room_name, $end, $start, $end, $start, $start, $end]);
            $existing = $checkStmt->fetch();

            if ($existing) {
                respond([
                    'success' => false, 
                    'message' => 'This time already booked by other member, please choose time again'
                ]);
            }

            // No conflict → insert
            $insertStmt = $pdo->prepare("
                INSERT INTO room_bookings (user_id, room_name, start_time, end_time, status) 
                VALUES (?, ?, ?, ?, 'pending')
            ");
            $insertStmt->execute([$user_id, $room_name, $start, $end]);

            respond([
                'success' => true, 
                'message' => 'Room booking request submitted successfully. Waiting for admin approval.'
            ]);

        } catch (Exception $e) {
            respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // === Other Actions ===
    $bookingId = $data['booking_id'] ?? null;

    if (!empty($action) && $bookingId) {
        switch ($action) {
            case 'mark_returned':
                $pdo->prepare("UPDATE borrowed_books SET status = 'Returned' WHERE id = ?")->execute([$bookingId]);
                respond(['success' => true, 'message' => 'Book marked as returned']);
                break;

            case 'mark_room_available':
                $pdo->prepare("UPDATE room_bookings SET status = 'Completed' WHERE id = ?")->execute([$bookingId]);
                respond(['success' => true, 'message' => 'Room marked as available']);
                break;

            case 'send_reminder':
                $pdo->prepare("UPDATE borrowed_books SET has_penalty = 1, status = 'Overdue' WHERE id = ?")->execute([$bookingId]);
                respond(['success' => true, 'message' => 'Reminder sent successfully']);
                break;

            case 'renew_book':
                $pdo->prepare("UPDATE borrowed_books SET status = 'Borrowed', has_penalty = 0, due_date = DATE_ADD(due_date, INTERVAL 7 DAY) WHERE id = ?")->execute([$bookingId]);
                respond(['success' => true, 'message' => 'Book renewed successfully']);
                break;

            case 'approve_room_booking':
                $pdo->prepare("UPDATE room_bookings SET status = 'Active' WHERE id = ?")->execute([$bookingId]);
                respond(['success' => true, 'message' => 'Room booking approved']);
                break;

            case 'reject_room_booking':
                $pdo->prepare("UPDATE room_bookings SET status = 'Rejected' WHERE id = ?")->execute([$bookingId]);
                respond(['success' => true, 'message' => 'Room booking rejected']);
                break;

            default:
                respond(['success' => false, 'message' => 'Unknown action'], 400);
        }
    }

    // Legacy booking insert (kept from your original)
    if (!empty($data['user_id']) && !empty($data['booking_date'])) {
        $stmt = $pdo->prepare("
            INSERT INTO bookings (user_id, booking_date, start_time, end_time, purpose, status) 
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([
            $data['user_id'], 
            $data['booking_date'], 
            $data['start_time'] ?? null, 
            $data['end_time'] ?? null, 
            $data['purpose'] ?? ''
        ]);
        respond(['success' => true, 'message' => 'Booking created']);
    }

    respond(['success' => false, 'message' => 'Invalid request'], 400);
}

// ====================== DELETE ======================
if ($method === 'DELETE') {
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'] ?? null;

    if ($id) {
        $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?")->execute([$id]);
        respond(['success' => true, 'message' => 'Booking cancelled']);
    }
    respond(['success' => false, 'message' => 'Invalid delete request'], 400);
}

respond(['success' => false, 'message' => 'Invalid request method'], 405);
?>