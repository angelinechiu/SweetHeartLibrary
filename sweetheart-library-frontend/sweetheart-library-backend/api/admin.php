<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
    $activeBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn();
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalRooms = $pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn();

    echo json_encode([
        'totalBooks' => $totalBooks,
        'activeBookings' => $activeBookings,
        'totalUsers' => $totalUsers,
        'totalRooms' => $totalRooms
    ]);
}
?>