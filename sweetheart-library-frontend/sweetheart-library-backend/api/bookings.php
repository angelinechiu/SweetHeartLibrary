<?php
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

$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    $data = $_POST;
}

$action = $_GET['action'] ?? $data['action'] ?? $_POST['action'] ?? null;

function respond($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// ====================== GET REQUESTS ======================
if ($method === 'GET') {

    if ($action === 'get') {
    $response = ['success' => true];
    $user_id = $_GET['user_id'] ?? null;

    if ($user_id) {

        $updateOverdue = $pdo->prepare("
        UPDATE borrowed_books 
        SET status = 'Overdue' 
        WHERE user_id = ? 
          AND due_date < CURDATE() 
          AND status IN ('Borrowed', 'Overdue')
    ");
    $updateOverdue->execute([$user_id]);
    
        // User-specific borrowed books (used by MyBookings + User Dashboard)
        $stmt = $pdo->prepare("
            SELECT 
                bb.*,
                COALESCE(bb.book_title, b.title) AS book_title,
                COALESCE(bb.author, b.author) AS author
            FROM borrowed_books bb
            LEFT JOIN books b ON bb.book_id = b.id
            WHERE bb.user_id = ?
            ORDER BY bb.borrow_date DESC
        ");
        $stmt->execute([$user_id]);
        $response['borrowed_books'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT * FROM room_bookings WHERE user_id = ? ORDER BY start_time ASC");
        $stmt->execute([$user_id]);
        $response['room_bookings'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Admin view - all borrowed books
        $stmt = $pdo->prepare("
            SELECT 
                bb.*,
                COALESCE(bb.book_title, b.title) AS book_title,
                COALESCE(bb.author, b.author) AS author
            FROM borrowed_books bb
            LEFT JOIN books b ON bb.book_id = b.id
            ORDER BY bb.due_date ASC
        ");
        $stmt->execute();
        $response['borrowed_books'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT * FROM room_bookings WHERE status = 'Active   ' ORDER BY start_time ASC");
        $stmt->execute();
        $response['room_bookings'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    respond($response);
}
    if ($action === 'check_daily_limit') {
        $user_id = $_GET['user_id'] ?? 0;
        $date = $_GET['date'] ?? date('Y-m-d');

        if ($user_id <= 0) {
            respond(['success' => false, 'message' => 'Invalid user ID'], 400);
        }

        try {
            $stmt = $pdo->prepare("
                SELECT COALESCE(SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)), 0) as total_hours
                FROM room_bookings 
                WHERE user_id = ? 
                  AND DATE(start_time) = ?
                  AND status != 'cancelled'
            ");
            $stmt->execute([$user_id, $date]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            respond([
                'success' => true,
                'total_hours' => (float)($result['total_hours'] ?? 0)
            ]);
        } catch (Exception $e) {
            respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

// ====================== POST REQUESTS ======================
if ($method === 'POST') {

    $bookingId = $data['booking_id'] ?? $data['borrowing_id'] ?? null;

    if (!empty($action)) {

        switch ($action) {

            // ==================== BORROW BOOK ====================
            case 'borrow_book':
                $book_id = $data['book_id'] ?? null;
                $user_id = $data['user_id'] ?? null;

                if (!$book_id || !$user_id) {
                    respond(['success' => false, 'message' => 'book_id and user_id are required'], 400);
                }

                try {
                    // Max 3 active books check
                    $countStmt = $pdo->prepare("
                        SELECT COUNT(*) as total 
                        FROM borrowings 
                        WHERE user_id = ? AND status IN ('Borrowed', 'Overdue')
                    ");
                    $countStmt->execute([$user_id]);
                    $count = $countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

                    if ($count >= 3) {
                        respond(['success' => false, 'message' => 'You have reached the maximum limit of 3 active borrowed books.'], 400);
                    }

                    // Already borrowed check
                    $check = $pdo->prepare("
                        SELECT id FROM borrowings 
                        WHERE user_id = ? AND book_id = ? AND status IN ('Borrowed', 'Overdue')
                    ");
                    $check->execute([$user_id, $book_id]);
                    if ($check->fetch()) {
                        respond(['success' => false, 'message' => 'You have already borrowed this book'], 400);
                    }

                    // Get book info
                    $bookStmt = $pdo->prepare("SELECT title FROM books WHERE id = ?");
                    $bookStmt->execute([$book_id]);
                    $book = $bookStmt->fetch(PDO::FETCH_ASSOC);

                    if (!$book) {
                        respond(['success' => false, 'message' => 'Book not found'], 404);
                    }

                    // Insert into borrowings
                    $stmt = $pdo->prepare("
                        INSERT INTO borrowings (user_id, book_id, borrowed_date, due_date, status) 
                        VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 'Borrowed')
                    ");
                    $success = $stmt->execute([$user_id, $book_id]);

                    // Decrease available copies
                    if ($success) {
                        $update = $pdo->prepare("
                            UPDATE books SET available_copies = GREATEST(available_copies - 1, 0) 
                            WHERE id = ?
                        ");
                        $update->execute([$book_id]);
                    }

                    respond([
                        'success' => $success,
                        'message' => $success ? 'Book borrowed successfully' : 'Failed to borrow book'
                    ]);

                } catch (Exception $e) {
                    respond(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;

            // ==================== MARK RETURNED ====================
            case 'mark_returned':
                if (!$bookingId) respond(['success' => false, 'message' => 'Booking ID required'], 400);
                $stmt = $pdo->prepare("
                    UPDATE borrowings 
                    SET status = 'Returned', returned_date = NOW() 
                    WHERE id = ?
                ");
                $success = $stmt->execute([$bookingId]);
                respond(['success' => $success, 'message' => $success ? 'Book marked as returned' : 'Failed']);
                break;

            // ==================== SEND REMINDER ====================
            case 'send_reminder':
                if (!$bookingId) respond(['success' => false, 'message' => 'Booking ID required'], 400);
                $stmt = $pdo->prepare("UPDATE borrowings SET status = 'Overdue' WHERE id = ?");
                $success = $stmt->execute([$bookingId]);
                respond(['success' => $success, 'message' => $success ? 'Reminder sent successfully' : 'Failed']);
                break;

            // ==================== RENEW BOOK ====================
            case 'renew_book':
                if (!$bookingId) respond(['success' => false, 'message' => 'Booking ID required'], 400);
                $stmt = $pdo->prepare("
                    UPDATE borrowings 
                    SET status = 'Borrowed', due_date = DATE_ADD(due_date, INTERVAL 7 DAY) 
                    WHERE id = ?
                ");
                $success = $stmt->execute([$bookingId]);
                respond(['success' => $success, 'message' => $success ? 'Book renewed successfully' : 'Failed to renew book']);
                break;

            // ==================== ROOM BOOKING ACTIONS ====================
            case 'create_room_booking':
                $user_id   = $data['user_id'] ?? null;
                $room_name = $data['room_name'] ?? $data['room_id'] ?? null;
                $start     = $data['start_time'] ?? null;
                $end       = $data['end_time'] ?? null;

                if (!$user_id || !$room_name || !$start || !$end) {
                    respond(['success' => false, 'message' => 'Missing required fields'], 400);
                }

                $stmt = $pdo->prepare("
                    INSERT INTO room_bookings (user_id, room_name, start_time, end_time, status) 
                    VALUES (?, ?, ?, ?, 'Active')
                ");
                $success = $stmt->execute([$user_id, $room_name, $start, $end]);

                respond([
                    'success' => $success, 
                    'message' => $success ? 'Room booking created successfully' : 'Failed to create booking'
                ]);
                break;

            case 'approve_room_booking':
                if (!$bookingId) respond(['success' => false, 'message' => 'Booking ID required'], 400);
                $stmt = $pdo->prepare("UPDATE room_bookings SET status = 'Active' WHERE id = ?");
                $success = $stmt->execute([$bookingId]);
                respond(['success' => $success]);
                break;

            case 'reject_room_booking':
                if (!$bookingId) respond(['success' => false, 'message' => 'Booking ID required'], 400);
                $stmt = $pdo->prepare("UPDATE room_bookings SET status = 'Rejected' WHERE id = ?");
                $success = $stmt->execute([$bookingId]);
                respond(['success' => $success]);
                break;

            case 'mark_room_available':
                if (!$bookingId) respond(['success' => false, 'message' => 'Booking ID required'], 400);
                $stmt = $pdo->prepare("UPDATE room_bookings SET status = 'Completed' WHERE id = ?");
                $success = $stmt->execute([$bookingId]);
                respond(['success' => $success, 'message' => $success ? 'Room marked as completed' : 'Failed']);
                break;

            case 'cancel_room':
                if (!$bookingId) respond(['success' => false, 'message' => 'Booking ID required'], 400);
                $stmt = $pdo->prepare("UPDATE room_bookings SET status = 'cancelled' WHERE id = ?");
                $success = $stmt->execute([$bookingId]);
                respond([
                    'success' => $success, 
                    'message' => $success ? 'Room booking cancelled successfully' : 'Failed to cancel room booking'
                ]);
                break;

            default:
                respond(['success' => false, 'message' => 'Unknown action: ' . $action], 400);
        }
    }

    respond(['success' => false, 'message' => 'No action specified'], 400);
}

// ====================== DELETE ======================
if ($method === 'DELETE') {
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("UPDATE room_bookings SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$id]);
        respond(['success' => true, 'message' => 'Booking cancelled']);
    }
    respond(['success' => false, 'message' => 'Invalid delete request'], 400);
}

respond(['success' => false, 'message' => 'Invalid request method'], 405);
?>