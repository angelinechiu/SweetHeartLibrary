<?php
// ==================== CORS ====================
$allowedOrigins = [
    'http://localhost:5173',                    // Local development
    'https://sweet-heart-library.vercel.app/',     // ← CHANGE THIS LATER to your Vercel URL
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ==================== DATABASE CONNECTION ====================
// Render provides DATABASE_URL environment variable
$databaseUrl = getenv('DATABASE_URL');

if ($databaseUrl) {
    // Production (Render)
    $dbParts = parse_url($databaseUrl);
    $host = $dbParts['host'];
    $port = $dbParts['port'] ?? 5432;
    $dbname = ltrim($dbParts['path'], '/');
    $username = $dbParts['user'];
    $password = $dbParts['pass'];
} else {
    // Local development fallback
    $host = 'localhost';
    $port = 5432;
    $dbname = 'SHL';
    $username = 'postgres';
    $password = 'your_local_password';
}

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}